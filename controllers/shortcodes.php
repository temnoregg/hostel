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
				// if this is a private room, we cannot book less beds than the room has
				if($room->rtype == 'private' and $_POST['beds'] != $room->beds and $room->price_type != 'per-room') {
					return sprintf(__('This is a private room. You have to book all the %d beds', 'wphostel'), $room->beds);
				}				
				
				// select all bookings in the given period
				$bookings = $_booking->select_in_period($from_date, $to_date);
								
				// make sure all dates are available
				$check_room = $_room->availability($check_room, $bookings, $from_date, $to_date, $numdays, $datefrom_time, $dateto_time);
				foreach($check_room['days']	as $day) {
					if(!$day['available_beds']) return __('In your selection there are dates when the room is not available. Please select only dates available for booking','wphostel');
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
		else {		
			// when coming from the list of rooms we have dates in GET
			$from_date = empty($_GET['from_date']) ? date("Y-m-d", strtotime("tomorrow")) : $_GET['from_date'];
			$to_date = empty($_GET['to_date']) ? date("Y-m-d", strtotime("+2 days")) : $_GET['to_date'];			
			
			// select all rooms		
			$rooms = $wpdb->get_results( "SELECT * FROM ".WPHOSTEL_ROOMS." ORDER BY title" );
			
			// display the booking form
			wp_enqueue_script('jquery-ui-datepicker');
		  wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
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
		$_booking = new WPHostelBooking();
		$dateformat = get_option('date_format');
		$booking_mode = get_option('wphostel_booking_mode');
		
		// when we have clicked the booking button load the booking form
		if(!empty($_GET['in_booking_mode'])) {
			return self :: booking();
		} 
		
		// the dropdown defaults to "from tomorrow to 1 day after"
		$datefrom = empty($_POST['wphostel_from']) ? date("Y-m-d", strtotime("tomorrow")) : $_POST['wphostel_from'];
		$dateto = empty($_POST['wphostel_to']) ? date("Y-m-d", strtotime("+ 2 days")) : $_POST['wphostel_to'];
		
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
		
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
		
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