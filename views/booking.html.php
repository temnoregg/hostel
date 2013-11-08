<div class="wrap">
	<h1><?php _e('Add/Edit Reservation', 'wphostel')?></h1>
	
	<div class="wrap wphostel-box postbox">
		<form class='wphostel-form' method="post">
			<div><label><?php _e('Select room:', 'wphostel')?></label> <select name="room_id">
				<?php foreach($rooms as $room):?>
					<option value="<?php echo $room->id?>" <?php if(!empty($booking->room_id) and $booking->room_id == $room->id) echo 'selected'?>><?php echo $room->title;?></option>
				<?php endforeach;?>
			</select></div>
			<div><label><?php _e('No. beds to book:', 'wphostel')?></label> <input type="text" name="beds" value="<?php echo empty($booking->beds) ? 1 : $booking->beds?>" size="4"></div>
			<div><label><?php _e('From date:', 'wphostel')?></label> <?php echo WPHostelQuickDDDate('from', @$booking->from_date, NULL, NULL, date("Y"), date("Y") + 10);?></div>
			<div><label><?php _e('To date:', 'wphostel')?></label> <?php echo WPHostelQuickDDDate('to', @$booking->to_date, NULL, NULL, date("Y"), date("Y") + 10);?></div>
			<div><label><?php _e('Amount paid:', 'wphostel')?></label> <?php echo WPHOSTEL_CURRENCY?> <input type="text" name="amount_paid" value="<?php echo empty($booking->amount_paid) ? '' : $booking->amount_paid?>" size="6"></div>
			<div><label><?php _e('Amount due:', 'wphostel')?></label> <?php echo WPHOSTEL_CURRENCY?> <input type="text" name="amount_due" value="<?php echo empty($booking->amount_due) ? '' : $booking->amount_due?>" size="6"></div>
			<div><label><?php _e('Contact name:', 'wphostel')?></label> <input type="text" name="contact_name" value="<?php echo empty($booking->contact_name) ? '' : $booking->contact_name?>"></div>
			<div><label><?php _e('Contact email:', 'wphostel')?></label> <input type="text" name="contact_email" value="<?php echo empty($booking->contact_email) ? '' : $booking->contact_email?>"></div>
			<div><label><?php _e('Contact phone:', 'wphostel')?></label> <input type="text" name="contact_phone" value="<?php echo empty($booking->contact_phone) ? '' : $booking->contact_phone?>"></div>
			<div><label><?php _e('Visitors type:', 'wphostel')?></label> <select name="contact_type">
			<option value="male" <?php if(!empty($booking->id) and $booking->contact_type=='male') echo "selected"?>><?php _e('Male(s)', 'wphostel')?></option>
			<option value="female" <?php if(!empty($booking->id) and $booking->contact_type=='female') echo "selected"?>><?php _e('Female(s)', 'wphostel')?></option>
			<option value="couple" <?php if(!empty($booking->id) and $booking->contact_type=='couple') echo "selected"?>><?php _e('Couple', 'wphostel')?></option>
			<option value="mixed" <?php if(!empty($booking->id) and $booking->contact_type=='mixed') echo "selected"?>><?php _e('Mixed', 'wphostel')?></option>
			</select></div>
			<?php if(!empty($booking->id)):?>
				<div><label><?php _e('Booking status:', 'wphostel')?></label> <select name="status">
				<option value="active" <?php if($booking->status == 'active') echo 'selected'?>><?php _e('Active', 'wphostel')?></option>
				<option value="pending" <?php if($booking->status == 'pending') echo 'selected'?>><?php _e('Pending (not confirmed)', 'wphostel')?></option>
				<option value="cancelled" <?php if($booking->status == 'cancelled') echo 'selected'?>><?php _e('Cancelled', 'wphostel')?></option>
				</select></div>
			<?php endif;?>
			
			<div align="center">
				<input type="submit" value="<?php _e('Save Reservation', 'wphostel')?>">			
				<?php if(!empty($booking->id)):?>
					<input type="button" value="<?php _e('Delete booking', 'wphostel');?>" onclick="wpHostelConfirmDelete(this.form);">
				<?php endif;?>
				<input type="button" value="<?php _e('Go Back', 'wphostel')?>" onclick="window.location='admin.php?page=wphostel_bookings&type=<?php echo $_GET['type']?>&offset=<?php echo $_GET['offset']?>';">
			</div>
			<input type="hidden" name="ok" value="1">
			<input type="hidden" name="del" value="0">
		</form>
	</div>
</div>

<script type="text/javascript" >
function wpHostelConfirmDelete(frm) {
	if(confirm("<?php _e('Are you sure?','wphostel');?>")) {
			frm.del.value=1;
			frm.submit();
	}
}
</script>