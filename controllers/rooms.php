<?php
// manage hostel rooms controller
class WPHostelRooms {
	static function manage() {
		$_room = new WPHostelRoom();
		
		$action = empty($_GET['action'])?'list':$_GET['action'];
		switch($action) {
			case 'add':
				if(!empty($_POST['ok'])) {
					$_room -> add($_POST);
					$success = __('Room added.', 'wphostel');
					wphostel_redirect("admin.php?page=wphostel_rooms&action=list");
				}			
			
				require(WPHOSTEL_PATH."/views/room.php");
			break;
			
			case 'edit':
				if(!empty($_POST['ok'])) {
					$_room->edit($_POST, $_GET['id']);
					$success = __('Room details saved.', 'wphostel');
					wphostel_redirect("admin.php?page=wphostel_rooms&action=list");
				}
				
				$room = $_room->get($_GET['id']);
				
				require(WPHOSTEL_PATH."/views/room.php");
			break;
			
			case 'delete':
				$_room->delete($_GET['id']);
				$success = __("Room deleted.", 'wphostel');
				wphostel_redirect("admin.php?page=wphostel_rooms&action=list");
			break;			
			
			case 'list':
			default:
				$rooms = $_room->find();
				require(WPHOSTEL_PATH."/views/rooms.php");
			break;
		}
	}
	
	// ajax called function that returns the default number of beds for the booking form:
	// for dorm rooms and "per room" price return 1
	// for private rooms return max beds
	// outputs also 0 or 1 after the | to show whether the user can change or not the number of rooms
	static function default_beds() {
		global $wpdb;
		
		// select room
		$room = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WPHOSTEL_ROOMS." WHERE id=%d", $_POST['room_id']));
		
		if($room->rtype == 'dorm' or $room->price_type == 'per-room') {
			echo "1";
			if($room->rtype == 'dorm') echo "|1";
			else echo "|0";
		}
		else echo $room->beds.'|0';		
		
		exit;
	}
	
	// displays the availability table of all rooms by given dates
	static function availability_table($shortcode_id) {
		global $wpdb;
		
		$_room = new WPHostelRoom();
		$_booking = new WPHostelBooking();
		$dateformat = get_option('date_format');
		$booking_mode = get_option('wphostel_booking_mode');
		$min_stay = get_option('wphostel_min_stay');
				
		// the dropdown defaults to "from tomorrow to 1 day after"
		$default_dateto_diff = $min_stay ? strtotime("+ ".(intval($min_stay)+1)." days") : strtotime("+ 2 days");
		$datefrom = empty($_POST['wphostel_from']) ? date("Y-m-d", strtotime("tomorrow")) : $_POST['wphostel_from'];
		$dateto = empty($_POST['wphostel_to']) ? date("Y-m-d", $default_dateto_diff) : $_POST['wphostel_to'];
		
		// select all rooms
		$rooms = $wpdb->get_results("SELECT * FROM ".WPHOSTEL_ROOMS." ORDER BY price", ARRAY_A);
		
		// select all bookings in the given period
		$bookings = $_booking->select_in_period($datefrom, $dateto);
		
		$datefrom_time = strtotime($datefrom);
		$dateto_time = strtotime($dateto);		
		$numdays = ($dateto_time   -  $datefrom_time) / (24 * 3600);
		
		// match bookings to rooms so for each date we know if the room is booked or not
		foreach($rooms as $cnt=>$room) {
			$rooms[$cnt] = $_room->availability($room, $bookings, $datefrom, $dateto, $numdays, $datefrom_time, $dateto_time);			
		} // end foreach room
		
		include(WPHOSTEL_PATH."/views/partial/rooms-table.html.php");
	} // end availability table
}