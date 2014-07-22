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

// load the booking form when called in the room calendar shortcode
function WPHostelLoadBooking(frm, divID) {	
	var form_data = jQuery(frm).serialize();
	jQuery.post(wphostel_i18n.ajax_url, form_data, function(msg) {
		if(msg.indexOf('BOOKERROR') != -1) {
			parts = msg.split('BOOKERROR-->');
			alert(parts[1]);
			return false;
		}
		jQuery('#'+divID).html(msg);
	}); 
}

function WPHostelValidateBooking(frm) {
	// beds
	if(frm.beds.value == '' || isNaN(frm.beds.value) || frm.beds.value < 1) {
		alert(wphostel_i18n.beds_required);
		frm.beds.focus();
		return false;
	}
	
	// from date
	if(frm.from_date.value == '') {
		alert(wphostel_i18n.from_date_required);
		frm.from_date.focus();
		return false;
	}
	
	// to date
	if(frm.to_date.value == '') {
		alert(wphostel_i18n.to_date_required);
		frm.to_date.focus();
		return false;
	}
	
	// to date must be > from date
	var curDate = new Date();
	var fromParts = frm.from_date.value.split('-');
	var fromDate = new Date(fromParts[0], fromParts[1]-1, fromParts[2]);
	var toParts = frm.to_date.value.split('-');
	var toDate = new Date(toParts[0], toParts[1]-1, toParts[2]);
	
	if(curDate > fromDate) {
		alert(wphostel_i18n.from_date_atleast_today);
		frm.from_date.focus();
		return false;
	}

	if(fromDate >= toDate) {
		alert(wphostel_i18n.from_date_before_to);
		frm.to_date.focus();
		return false;
	}	
	
	if(frm.contact_name.value == '') {
		alert(wphostel_i18n.enter_name);
		frm.contact_name.focus();
		return false;
	}
	
	var emailStr = frm.contact_email.value; 
	if(emailStr == '' || emailStr.indexOf('@') < 1 || emailStr.indexOf('.') < 1) {
		alert(wphostel_i18n.enter_email);
		frm.contact_email.focus();
		return false;
	}
	
	var divID = '#WPHostelBooking' + frm.shortcode_id.value;
	
	// all fine, submit by ajax
	var form_data = jQuery(frm).serialize();
	jQuery.post(wphostel_i18n.ajax_url, form_data, function(msg) {
		if(msg.indexOf('BOOKERROR') != -1) {
			parts = msg.split('BOOKERROR-->');
			alert(parts[1]);
			return false;
		}		
		jQuery(divID).html(msg);
	}); 
}