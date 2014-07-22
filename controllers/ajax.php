<?php
// procedural function to dispatch ajax requests
function wphostel_ajax() {
	global $wpdb, $user_ID;	
	
	$type = empty($_POST['type']) ? $_GET['type'] : $_POST['type'];	
		
	switch($type) {
		case 'change_room':
			WPHostelRooms :: default_beds();
		break;
		case 'book':
			// book a room
			$booking_mode = get_option('wphostel_booking_mode');
			if($booking_mode == 'none') return __('Online booking is not enabled.', 'wphostel');
			echo WPHostelBookings :: book();
		break;
		case 'load_booking_form':
			// because the booking form expects them in $_GET but we send in ajax as $_POST
			// we have to transfer the vars
			$_GET['room_id'] = $_POST['room_id'];
			$_GET['in_booking_mode'] = 1;
			$_GET['from_date'] = $_POST['from_date'];
			$_GET['to_date'] = $_POST['to_date'];
			echo WPHostelShortcodes :: booking("roomID".$_POST['room_id']);
		break;
		case 'list_rooms':			
			WPHostelRooms :: availability_table($_POST['shortcode_id']);
		break;
	}
	exit;
}