<?php
class WPHostelRoom {
	function add($vars) {
		global $wpdb;
		
		$result = $wpdb->query($wpdb->prepare("INSERT INTO ".WPHOSTEL_ROOMS." SET
			title=%s, rtype=%s, beds=%d, bathroom=%s, price=%s, description=%s", 
			$vars['title'], $vars['rtype'], $vars['beds'], $vars['bathroom'], $vars['price'], $vars['description']));
			
		if($result===false) return false;
		return true;	
	}
	
	function edit($vars, $id) {
		global $wpdb;
		
		$result = $wpdb->query($wpdb->prepare("UPDATE ".WPHOSTEL_ROOMS." SET
			title=%s, rtype=%s, beds=%d, bathroom=%s, price=%s, description=%s WHERE id=%d", 
			$vars['title'], $vars['rtype'], $vars['beds'], $vars['bathroom'], $vars['price'], $vars['description'], $id));
			
		if($result===false) return false;
		return true;	
	}
	
	function delete($id) {
		global $wpdb;
		
		$result = $wpdb->query($wpdb->prepare("DELETE FROM ".WPHOSTEL_ROOMS." WHERE id=%d", $id));
		
		if($result) {
			// delete also bookings
			$wpdb->query($wpdb->prepare("DELETE FROM ".WPHOSTEL_BOOKINGS." WHERE room_id=%d", $id));
		}
		
		if(!$result) return false;
		return true;
	}
	
	// list all rooms, paginated. 
	// allow filters
	function find($filters = null) {
		global $wpdb;
		
		$ob = "id";
		$dir = "DESC";
		$offset = empty($_GET['offset']) ? 0 : $_GET['offset'];
		$limit = 20;
		
		$rooms = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WPHOSTEL_ROOMS." ORDER BY %s %s LIMIT %d, %d",
			$ob, $dir, $offset, $limit));
			
		return $rooms;	
	}
	
	// return specific room details
	function get($id) {
		global $wpdb;
		
		$room = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WPHOSTEL_ROOMS." WHERE id=%d", $id));
		
		return $room;	
	}
	
	// prettify/translate some of the room's properties to be human friendly
	function prettify($property, $value) {
		switch($property) {
			case 'rtype':
				switch($value) {
					case 'private': return __('Private', 'wphostel'); break;
					case 'dorm': return __('Dorm', 'wphostel'); break;
				}	
			break;
			
			case 'bathroom':
				switch($value) {
					case 'ensuite': return __('Ensuite', 'wphostel'); break;
					case 'shared': return __('Shared', 'wphostel'); break;
				}
			break;
		}
	}
}