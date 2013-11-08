<div class="wrap">
	<h1><?php _e('Manage Bookings / Reservations', 'wphostel')?></h1>
	
	<p><?php _e('Showing', 'wphostel')?> <select onchange="window.location='admin.php?page=wphostel_bookings&type='+this.value;">
		<option value="upcoming" <?php if($type == 'upcoming') echo 'selected'?>><?php _e('Upcoming', 'wphostel')?></option>
		<option value="past" <?php if($type == 'past') echo 'selected'?>><?php _e('Past', 'wphostel')?></option>		
	</select> <?php _e('bookings', 'wphostel')?></p>
	<p><a href="admin.php?page=wphostel_bookings&do=add&type=<?php echo $type?>&offset=<?php echo $offset?>"><?php _e('Click here to manually add a new booking', 'wphostel')?></a></p>
	
	<?php if(!sizeof($bookings)):?>
		<p><?php _e('There are no bookings to show at the moment.', 'wphostel')?></p>
	<?php return false; 
	endif;?>
	<table class="widefat">
		<tr><th><?php _e('Room/beds', 'wphostel')?></th><th><?php _e('Contact name', 'wphostel')?></th><th><?php _e('Contact email', 'wphostel')?></th><th><?php _e('Booking dates', 'wphostel')?></th>
		<th><?php _e('Amount paid/due', 'wphostel')?></th><th><?php _e('Status', 'wphostel')?></th><th><?php _e('Action', 'wphostel')?></th></tr>
		<?php foreach($bookings as $booking):?>
			<tr><td><?php printf(__('%d beds in %s', 'wphostel'), $booking->beds, $booking->room);?></td>
			<td><?php echo $booking->contact_name?></td><td><?php echo $booking->contact_email?></td>
			<td><?php echo date(get_option('date_format'), strtotime($booking->from_date)).' - '.date(get_option('date_format'), strtotime($booking->to_date))?></td>
			<td><?php echo WPHOSTEL_CURRENCY." ".$booking->amount_paid." / ".WPHOSTEL_CURRENCY.' '.$booking->amount_due;?></td>
			<td><?php switch($booking->status):
			case 'active': _e('Active', 'wphostel'); break;
			case 'pending': _e('Pending', 'wphostel'); break;
			case 'cancelled': _e('Cancelled', 'wphostel'); break;
			endswitch;?></td>
			<td><input type="button" value="<?php _e('Edit', 'wphostel')?>" onclick="window.location='admin.php?page=wphostel_bookings&do=edit&id=<?php echo $booking->id?>&type=<?php echo $type?>&offset=<?php echo $offset?>';">
			<input type="button" value="<?php _e('Mark as paid', 'wphostel');?>" onclick="wpHostelMarkPaid(<?php echo $booking->id?>);"></td></tr>
		<?php endforeach;?>
	</table>
	
	<p align="center"><?php if($offset > 0):?>
		<a href="admin.php?page=wphostel_bookings&type=<?php echo $type?>&offset=<?php echo $offset - $page_limit?>"><?php _e('[previous page]', 'wphostel')?></a>
	<?php endif;?> 
	<?php if($count > ($page_limit + $offset*$page_limit)):?>
		<a href="admin.php?page=wphostel_bookings&type=<?php echo $type?>&offset=<?php echo $offset + $page_limit?>"><?php _e('[next page]', 'wphostel')?></a>
	<?php endif;?>	
	</p>
</div>

<script type="text/javascript">
function wpHostelMarkPaid(id) {
	if(confirm("<?php _e('Are you sure?', 'wphostel')?>")) {
		window.location = 'admin.php?page=wphostel_bookings&type=<?php echo $type?>&offset=<?php echo $offset;?>&mark_paid=1&id='+id;
	}
}
</script>