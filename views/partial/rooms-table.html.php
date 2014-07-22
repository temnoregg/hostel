<table>
		<tr><th><?php _e('Room type', 'wphostel')?></th><th><?php _e('Bathroom', 'wphostel')?></th>
		<?php for($i=0; $i < $numdays; $i++):
			$curday_time = $datefrom_time + $i*24*3600;?>
			<th><?php echo date($dateformat, $curday_time);?></th>
		<?php endfor;?>	
		<th><?php _e('Price per person', 'wphostel')?></th><?php if($booking_mode != 'none'):?><th><?php _e('Book', 'wphostel')?></th><?php endif;?></tr>
		
		<?php foreach($rooms as $room):
			$can_book = true; ?>
			<tr><td><?php echo $_room->prettify('rtype', $room['rtype'])?></td><td><?php echo $_room->prettify('bathroom', $room['bathroom'])?></td>
			<?php for($i=0; $i < $numdays; $i++):
				if(!$room['days'][$i]['available_beds']) $can_book = false;?>
				<td><?php echo $room['days'][$i]['available_beds'] ? sprintf(__('%d beds', 'wphostel'), $room['days'][$i]['available_beds'])	 : "X"?></td>
			<?php endfor;?>	
			<td><?php echo WPHOSTEL_CURRENCY.' '.$room['price'].' <br>('.$_room->prettify('price_type', $room['price_type']).')';?></td>
			<?php if($booking_mode != 'none'):?><td align="center"><?php if($can_book):?>
				<form method="post">
				<input type="hidden" name="from_date" value="<?php echo $datefrom?>">
				<input type="hidden" name="to_date" value="<?php echo $dateto?>">
				<input type="hidden" name="room_id" value="<?php echo $room['id']?>">
				<input type="hidden" name="currently_setting" value="from">		
				<input type="hidden" name="action" value="wphostel_ajax">
				<input type="hidden" name="type" value="load_booking_form">
				<input type="hidden" name="in_booking_mode" value="1">
				<input type="button" value="<?php _e('Book', 'wphostel');?>" onclick="WPHostelLoadBooking(this.form, 'wphostelRoomsTable<?php echo $shortcode_id?>');">
				</form>
			<?php else: _e('Not available', 'wphostel');
			endif;?></td><?php endif;?></tr>
		<?php endforeach;?>
</table>