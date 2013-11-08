<div class="wrap">
	<h1><?php _e('Manage Unavailable Dates', 'wphostel')?></h1>
	
	<p><?php _e('Use this page to set up dates when specific rooms can not be booked. This might be due to your scheduled maintenance, holiday, or any other date when your property will not be working, or just a specific room will not be available.', 'wphostel');?></p>
	
	<p><?php _e('Note that for simplicity bookings made will not be reflected here. Making a date available does not mean there are no already made reservations for it.', 'wphostel');?></p>
	
	<form method="post">
		<p><label><?php _e('Select date:', 'wphostel')?></label> <?php echo WPHostelQuickDDDate('date', $date, NULL, array("onchange='this.form.submit();'", "onchange='this.form.submit();'", "onchange='this.form.submit();'"), date("Y"), date("Y") + 5);?></p>
		
		<table class="widefat">
			<tr><th><?php _e('Room title', 'wphostel')?></th><th><?php _e('Room type', 'wphostel')?></th><th><?php _e('Make unavailable', 'wphostel')?></th>
			<?php foreach($rooms as $room):?>
				<tr><td><?php echo $room->title?></td><td><?php echo $_room->prettify('rtype', $room->rtype);?></td>
				<td align="center"><input type="checkbox" name="ids[]" value="<?php echo $room->id?>" <?php if(in_array($room->id, $unavailable_room_ids)) echo 'checked'?>></td></tr>
			<?php endforeach;?>
		</table>
		
		<p align="center"><input type="submit" name="set_dates" value="<?php _e('Save Unavailable Rooms', 'wphostel')?>"></p>		
	</form>
</div>