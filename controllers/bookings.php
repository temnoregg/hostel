<?php
class WPHostelBookings {
	static function manage() {
		global $wpdb;
		$_booking = new WPHostelBooking();
		
		switch(@$_GET['do']) {
			case 'add':
				if(!empty($_POST['ok'])) {
					$_POST['from_date'] = $_POST['fromyear'].'-'.$_POST['frommonth'].'-'.$_POST['fromday'];
					$_POST['to_date'] = $_POST['toyear'].'-'.$_POST['tomonth'].'-'.$_POST['today'];
					$_POST['status'] = 'active';
					$_booking -> add($_POST);
					wphostel_redirect("admin.php?page=wphostel_bookings&type=".$_GET['type']);
				}
							
				// select rooms for the dropdown
				$rooms = $wpdb->get_results("SELECT * FROM ".WPHOSTEL_ROOMS." ORDER BY title");
				require(WPHOSTEL_PATH."/views/booking.html.php");
			break;
			
			case 'edit':
				if(!empty($_POST['del'])) {
					$_booking->delete($_GET['id']);
					wphostel_redirect("admin.php?page=wphostel_bookings&type=$_GET[type]&offset=$_GET[offset]");				
				}				
			
				if(!empty($_POST['ok'])) {
					$_POST['from_date'] = $_POST['fromyear'].'-'.$_POST['frommonth'].'-'.$_POST['fromday'];
					$_POST['to_date'] = $_POST['toyear'].'-'.$_POST['tomonth'].'-'.$_POST['today'];
					$_booking -> edit($_POST, $_GET['id']);
					wphostel_redirect("admin.php?page=wphostel_bookings&type=$_GET[type]&offset=$_GET[offset]");
				}			
			
				// select booking
				$booking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WPHOSTEL_BOOKINGS." WHERE id=%d", $_GET['id']));
				
				// select rooms for the dropdown
				$rooms = $wpdb->get_results("SELECT * FROM ".WPHOSTEL_ROOMS." ORDER BY title");
				require(WPHOSTEL_PATH."/views/booking.html.php");
			break;
			
			// view/print booking details. Will allow also to confirm/cancel
			case 'view':
				// select booking and room details
				$booking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WPHOSTEL_BOOKINGS." WHERE id=%d", $_GET['id']));
				$room = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WPHOSTEL_ROOMS." WHERE id=%d", $booking['room_id']));	
			
				require(WPHOSTEL_PATH."/views/view-booking.html.php");
			break;			
			
			// list bookings
			default:
				// which bookings to show - upcoming or past?
				$type = empty($_GET['type']) ? 'upcoming' : $_GET['type'];
				$offset = empty($_GET['offset']) ? 0 : $_GET['offset'];
				
				// mark booking as fully paid	
				if(!empty($_GET['mark_paid'])) {
					$_booking->mark_paid($_GET['id']);
					wphostel_redirect("admin.php?page=wphostel_bookings&type=".$type."&offset=".$offset);
				}
				
				// define $where_sql and orderby depending on the $type				
				if($type == 'upcoming') {
					$where_sql = "AND from_date >=  CURDATE()";
					$orderby = "ORDER BY from_date";
					
				}
				else {
					$where_sql = "AND from_date < CURDATE() ";
					$orderby = "ORDER BY from_date DESC";
				}
				
				// define limit (as it's paginated)				
				$page_limit = 20;
				$limit_sql = $wpdb->prepare("LIMIT %d, %d", $offset, $page_limit);
				
				$bookings = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS tB.*, tR.title as room 
					FROM ".WPHOSTEL_BOOKINGS." tB JOIN ".WPHOSTEL_ROOMS." tR ON tR.id = tB.room_id
					WHERE is_static=0 $where_sql $orderby $limit_sql");
				$count = $wpdb->get_var("SELECT FOUND_ROWS()");	
				
				require(WPHOSTEL_PATH."/views/bookings.html.php");  
			break;
		}
	}
	
	// manage unavailable dates
	// they are entered as "static" booking. 
	// these bookings always have 1 DB record for each single date
	static function unavailable() {
		global $wpdb;
		$_booking = new WPHostelBooking();
		$_room = new WPHostelRoom();
		
		$date = empty($_POST['dateyear']) ? date("Y-m-d") : $_POST['dateyear'].'-'.$_POST['datemonth'].'-'.$_POST['dateday'];
		$to_date = date("Y-m-d", strtotime($date) + 24*3600);
		
		// select all available rooms
		$rooms = $wpdb->get_results( "SELECT * FROM ".WPHOSTEL_ROOMS." ORDER BY title" );
		
		$unavailable_room_ids = (!empty($_POST['ids']) and is_array($_POST['ids'])) ? $_POST['ids'] : array(0);		
		if(!empty($_POST['set_dates'])) {
			foreach($rooms as $room) {
				if(in_array($room->id, $unavailable_room_ids)) {
					// make sure there is a static booking for the room on this date
					$exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".WPHOSTEL_BOOKINGS." 
						WHERE room_id=%d AND from_date=%s AND to_date=%s AND is_static=1", $room->id, $date, $to_date));
					if(!$exists) {
						$wpdb->query($wpdb->prepare("INSERT INTO ".WPHOSTEL_BOOKINGS." SET
							room_id=%d, from_date=%s, to_date=%s, is_static=1", $room->id, $date, $to_date));
					}	
				}
				else {
					// delete any static bookings for this room on this date
					$wpdb->query($wpdb->prepare("DELETE FROM ".WPHOSTEL_BOOKINGS." 
						WHERE is_static=1 AND from_date=%s AND to_date=%s AND room_id=%d", $date, $to_date, $room->id));
				}
			}
		}
			
		// now select all static bookings on the given date and feel new $unavailable_room_ids array
		$static_bookings = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WPHOSTEL_BOOKINGS." 
			WHERE is_static=1 AND from_date=%s AND to_date=%s", $date, $to_date));
		$unavailable_room_ids = array();
		foreach($static_bookings as $booking) $unavailable_room_ids[] = $booking->room_id;
		
		require(WPHOSTEL_PATH."/views/unavailable-dates.html.php");
	}
	
	// do the booking
	static function book() {
		global $wpdb, $post;
		
		// insert booking details
			$_booking = new WPHostelBooking();
			$_room = new WPHostelRoom();
			
			$from_date = $_POST['from_date'];
			$to_date = $_POST['to_date'];
			
			// make sure it's not a duplicate
			$bid = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".WPHOSTEL_BOOKINGS."
				WHERE room_id=%d AND from_date=%s AND to_date=%s AND contact_email=%s",
				$_POST['room_id'], $from_date, $to_date, $_POST['contact_email']));
				
			// select the room
			$room = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WPHOSTEL_ROOMS." WHERE id=%d", $_POST['room_id']));	
			$check_room = (array)$room;	
			
			if($room->price_type == 'per-room') $_POST['beds'] = 1;
				
			// calculate cost
			$datefrom_time = strtotime($from_date);
			$dateto_time = strtotime($to_date);		
			$numdays = ($dateto_time   -  $datefrom_time) / (24 * 3600);	
			
			$cost = $numdays * $_POST['beds'] * $room->price;	
			$_POST['amount_paid'] =  0;
			$_POST['amount_due'] = $cost;			
			$_POST['status'] = 'pending';
										
			if(empty($bid)) {
				// minimum stay required?
				$min_stay = get_option('wphostel_min_stay');
				if(!empty($min_stay) and $min_stay > $numdays) {
					return '<!--BOOKERROR-->'.sprintf(__('Minimum stay of %d days is required.', 'wphostel'), $min_stay);
				}						
				
				// if this is a private room, we cannot book less beds than the room has
				if($room->rtype == 'private' and $_POST['beds'] != $room->beds and $room->price_type != 'per-room') {
					return '<!--BOOKERROR-->'.sprintf(__('This is a private room. You have to book all the %d beds', 'wphostel'), $room->beds);
				}				
				
				// select all bookings in the given period
				$bookings = $_booking->select_in_period($from_date, $to_date);
								
				// make sure all dates are available
				$check_room = $_room->availability($check_room, $bookings, $from_date, $to_date, $numdays, $datefrom_time, $dateto_time);
				foreach($check_room['days']	as $day) {
					if(!$day['available_beds'] or $day['available_beds'] < $_POST['beds']) return '<!--BOOKERROR-->'. __('In your selection there are dates when the room is not available or there are not enough free beds. Please check your selection.','wphostel');
				}		
						
				$bid = $_booking->add($_POST);
			}
			
			// if paypal display payment button otherwise display success message
			if(get_option('wphostel_booking_mode') == 'paypal') {
				include(WPHOSTEL_PATH."/views/pay-paypal.html.php");
			}
			else {
				echo "<p>".__('Thank you for your reservation request. We will get back to you when it is confirmed', 'wphostel')."</p>";
				
				// send email if you have to
				$_booking->email($bid);
			}
	}
}