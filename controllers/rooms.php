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
}