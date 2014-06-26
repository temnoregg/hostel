<?php
// procedural function to dispatch ajax requests
function wphostel_ajax() {
	global $wpdb, $user_ID;	
	
	$type = empty($_POST['type']) ? $_GET['type'] : $_POST['type'];	
		
	switch($type) {
		case 'change_room':
			WPHostelRooms :: default_beds();
		break;
		default:
			// nothing for now
		break;
	}
	exit;
}