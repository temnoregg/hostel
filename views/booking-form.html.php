<?php if(!empty($_GET['booking_mode'])):?>
	<p><a href="<?php echo get_permalink($post->ID)?>"><?php _e('Back to the listing of rooms', 'wphostel')?></a></p>
<?php endif;?>

<div class="wrap wphostel-box">
		<form class='wphostel-form' method="post" onsubmit="return WPHostelValidateBooking(this);">
			<div><label><?php _e('Select room:', 'wphostel')?></label> <select name="room_id" onchange="WPHostelChangeRoom(this.value, this.form);">
				<?php foreach($rooms as $room):?>
					<option value="<?php echo $room->id?>" <?php if(!empty($_GET['room_id']) and $_GET['room_id'] == $room->id) echo 'selected'?>><?php echo $room->title;?></option>
				<?php endforeach;?>
			</select></div>
			
			<div><label><?php _e('No. beds to book:', 'wphostel')?></label> <input type="text" name="beds" value="<?php echo empty($booking->beds) ? 1 : $booking->beds?>" size="4"></div>
			<div><label><?php _e('From date:', 'wphostel')?></label> <input type="text" size="10" name="from_date" value="<?php echo $from_date?>" class="wphostelDatePicker"></div>
			<div><label><?php _e('To date:', 'wphostel')?></label> <input type="text" size="10" name="to_date" value="<?php echo $to_date?>" class="wphostelDatePicker"></div>
			
			<div><label><?php _e('Contact name:', 'wphostel')?></label> <input type="text" name="contact_name" value="<?php echo empty($booking->contact_name) ? '' : $booking->contact_name?>"></div>
			<div><label><?php _e('Contact email:', 'wphostel')?></label> <input type="text" name="contact_email" value="<?php echo empty($booking->contact_email) ? '' : $booking->contact_email?>"></div>
			<div><label><?php _e('Contact phone:', 'wphostel')?></label> <input type="text" name="contact_phone" value="<?php echo empty($booking->contact_phone) ? '' : $booking->contact_phone?>"></div>
			<div><label><?php _e('You are:', 'wphostel')?></label> <select name="contact_type">
			<option value="male"><?php _e('Male(s)', 'wphostel')?></option>
			<option value="female"><?php _e('Female(s)', 'wphostel')?></option>
			<option value="couple"><?php _e('Couple', 'wphostel')?></option>
			<option value="mixed"><?php _e('Mixed', 'wphostel')?></option>
			</select></div>
					
			<div align="center">
				<input type="submit" value="<?php _e('Make Reservation', 'wphostel')?>">
			</div>
			<input type="hidden" name="wphostel_book" value="1">
		</form>
	</div>
	
<script type="text/javascript" >
function WPHostelValidateBooking(frm) {
	if(frm.contact_name.value == '') {
		alert("<?php _e('Please enter name!', 'wphostel');?>");
		frm.contact_name.focus();
		return false;
	}
	
	if(frm.contact_email.value == '') {
		alert("<?php _e('Please enter email address!', 'wphostel');?>");
		frm.contact_email.focus();
		return false;
	}
	
	return true;
}

jQuery(document).ready(function() {
    jQuery('.wphostelDatePicker').datepicker({
        dateFormat : 'yy-mm-dd'
    });
});	
</script>