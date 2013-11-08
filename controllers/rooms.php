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
}