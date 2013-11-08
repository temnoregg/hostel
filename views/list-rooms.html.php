<form method="post">
	<p><?php _e('From date:', 'wphostel')?> <?php echo WPHostelQuickDDDate('wphostel_from', $datefrom, NULL, NULL, date("Y"), date("Y") + 5);?></p>
	<p><?php _e('To date:', 'wphostel')?> <?php echo WPHostelQuickDDDate('wphostel_to', $dateto, NULL, NULL, date("Y"), date("Y") + 5);?></p>
	<p><input type="submit" value="<?php _e('Show rooms', 'wphostel')?>"></p>
</form>

<table>
	<tr><th><?php _e('Room type', 'wphostel')?></th><th><?php _e('Bathroom', 'wphostel')?></th>
	<?php for($i=0; $i < $numdays; $i++):
		$curday_time = $datefrom_time + $i*24*3600;?>
		<th><?php echo date($dateformat, $curday_time);?></th>
	<?php endfor;?>	
	<th><?php _e('Price per person')?></th><?php if($booking_mode != 'none'):?><th><?php _e('Book', 'wphostel')?></th><?php endif;?></tr>
	
	<?php foreach($rooms as $room):
		$can_book = true; ?>
		<tr><td><?php echo $_room->prettify('rtype', $room['rtype'])?></td><td><?php echo $_room->prettify('bathroom', $room['bathroom'])?></td>
		<?php for($i=0; $i < $numdays; $i++):
			if(!$room[$i]['available_beds']) $can_book = false;?>
			<td><?php echo $room[$i]['available_beds'] ? sprintf(__('%d beds', 'wphostel'), $room[$i]['available_beds'])	 : "X"?></td>
		<?php endfor;?>	
		<td><?php echo WPHOSTEL_CURRENCY.' '.$room['price']?></td>
		<?php if($booking_mode != 'none'):?><td align="center"><?php if($can_book):?>
			<input type="button" value="<?php _e('Book', 'wphostel');?>" onclick="window.location='<?php echo wphostel_book_url($post->ID, $room['id'], $datefrom, $dateto)?>'">
		<?php else: _e('Not available', 'wphostel');
		endif;?></td><?php endif;?></tr>
	<?php endforeach;?>
</table>