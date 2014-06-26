WPHostel = {};

// when changing room in the room booking form, default to proper number of beds:
// 1 for dorm rooms and entire room price
// all beds for private rooms
function WPHostelChangeRoom(roomID, frm) {
	data = {'action': 'wphostel_ajax', 'type': 'change_room', 'room_id' : roomID}
	jQuery.post(wphostel_i18n.ajax_url, data, function(msg) {
		var parts = msg.split("|");
		frm.beds.value = parts[0];
		
		if(parts[1] == '0') jQuery(frm.beds).attr('readonly', 'readonly');		
		else jQuery(frm.beds).removeAttr('readonly');
	});
}