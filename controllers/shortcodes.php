<?php
class WPHostelShortcodes {
	// displays and processes the booking form
	static function booking() {
		global $wpdb, $post;
		ob_start();
		$booking_mode = get_option('wphostel_booking_mode');
		if($booking_mode == 'none') return __('Online booking is not enabled.', 'wphostel');
		
		if(!empty($_POST['wphostel_book'])) {
			// insert booking details
			$_booking = new WPHostelBooking();
			$_room = new WPHostelRoom();
			
			$from_date = $_POST['fromyear'].'-'.$_POST['frommonth'].'-'.$_POST['fromday'];
			$to_date = $_POST['toyear'].'-'.$_POST['tomonth'].'-'.$_POST['today'];
			
			// make sure it's not a duplicate
			$bid = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".WPHOSTEL_BOOKINGS."
				WHERE room_id=%d AND from_date=%s AND to_date=%s AND contact_email=%s",
				$_POST['room_id'], $from_date, $to_date, $_POST['contact_email']));
				
			// select the room
			$room = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WPHOSTEL_ROOMS." WHERE id=%d", $_POST['room_id']));		
				
			// calculate cost
			$datefrom_time = strtotime($from_date);
			$dateto_time = strtotime($to_date);		
			$numdays = ($dateto_time   -  $datefrom_time) / (24 * 3600);	
			
			$cost = $numdays * $_POST['beds'] * $room->price;	
			$_POST['amount_paid'] =  0;
			$_POST['amount_due'] = $cost;			
			$_POST['status'] = 'pending';
										
			if(empty($bid)) {
				// make sure all dates are available
				$exists = $wpdb->get_var( $wpdb->prepare("SELECT id FROM ".WPHOSTEL_BOOKINGS." 
				WHERE room_id=%d AND ((from_date>=%s AND from_date<= %s) OR (to_date>=%s AND to_date<=%s)
				OR (from_date <= %s AND to_date >=%s))", 
				$_POST['room_id'], $from_date, $to_date, $from_date, $to_date, $from_date, $to_date));				
							
				if(!empty($exists)) return __('In your selection there are dates when the room is not available. Please select only dates available for booking','wphostel');
						
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
		else {		
			// when cooming from the list of rooms we have dates in GET
			$from_date = empty($_GET['from_date']) ? date("Y-m-d", strtotime("tomorrow")) : $_GET['from_date'];
			$to_date = empty($_GET['to_date']) ? date("Y-m-d", strtotime("+2 days")) : $_GET['to_date'];			
			
			// select all rooms		
			$rooms = $wpdb->get_results( "SELECT * FROM ".WPHOSTEL_ROOMS." ORDER BY title" );
			
			// display the booking form
			include(WPHOSTEL_PATH."/views/booking-form.html.php");
		}
		
		$content = ob_get_clean();
		return $content;
	} // end booking
	
	// list all rooms along with availability dropdown
	// will show cells for every date selected
	static function list_rooms() {
		global $wpdb, $post;
		$_room = new WPHostelRoom();
		$dateformat = get_option('date_format');
		$booking_mode = get_option('wphostel_booking_mode');
		
		// when we have clicked the booking button load the booking form
		if(!empty($_GET['in_booking_mode'])) {
			return self :: booking();
		} 
		
		// the dropdown defaults to "from tomorrow to 1 day after"
		$datefrom = empty($_POST['wphostel_fromday']) ? date("Y-m-d", strtotime("tomorrow")) : $_POST['wphostel_fromyear'].'-'.$_POST['wphostel_frommonth'].'-'.$_POST['wphostel_fromday'];
		$dateto = empty($_POST['wphostel_today']) ? date("Y-m-d", strtotime("+ 2 days")) : $_POST['wphostel_toyear'].'-'.$_POST['wphostel_tomonth'].'-'.$_POST['wphostel_today'];
		
		// select all rooms
		$rooms = $wpdb->get_results("SELECT * FROM ".WPHOSTEL_ROOMS." ORDER BY price", ARRAY_A);
		
		// select all bookings in the given period
		$bookings = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WPHOSTEL_BOOKINGS." WHERE (from_date >= %s AND from_date <= %s) 
			OR (to_date >=%s AND to_date <= %s) OR (from_date <= %s AND to_date >=%s) ", $datefrom, $dateto, $datefrom, $dateto, $datefrom, $dateto));
		
		// get the number of days between the two dates
		$datefrom_time = strtotime($datefrom);
		$dateto_time = strtotime($dateto);
		
		$numdays = ($dateto_time   -  $datefrom_time) / (24 * 3600);	
		
		// match bookings to rooms so for each date we know if the room is booked or not
		foreach($rooms as $cnt=>$room) {
			for($i=0; $i < $numdays; $i++) {
				// lets store number of available beds. When they reach 0 the whole room is not available
				$rooms[$cnt][$i]['available_beds'] = $room['beds'];
				// current day timestamp				
				$curday_time = $datefrom_time + $i*24*3600;
				foreach($bookings as $booking) {
					if($booking->room_id == $room['id']) {
						$booking_from_time = strtotime($booking->from_date);
						$booking_to_time = strtotime($booking->to_date);
						
						if($booking_from_time <= $curday_time and $booking_to_time>=$curday_time) {
							$rooms[$cnt][$i]['available_beds'] -= $booking->beds;
							if($rooms[$cnt][$i]['available_beds'] <= 0) break;
						}
					} // end if this booking is for this room
				} // end foreach booking
			} // end for i			
		} // end foreach room
		
		include(WPHOSTEL_PATH."/views/list-rooms.html.php");
		$content = ob_get_clean();
		return $content;
	} // end list_rooms();	
	
	// displays a Book! button
	static function book($atts) {
		global $post;
		
		$room_id = $atts[0];
		$text = empty($atts[1]) ? __('Book', 'wphostel') : $atts[1];
		
		return '<input type="button" onclick="window.location='."'".wphostel_book_url($post->ID, $room_id, date("Y-m-d"), date("Y-m-d", strtotime("tomorrow")))."'".'" value="'.$text.'">';
	}
}