<h1><?php _e('Manage Rooms', 'wphostel')?></h1>

<div class="wrap">
	<p><a href="admin.php?page=wphostel_rooms&action=add"><?php _e('Click here to add room', 'wphostel')?></a></p>
	
	<?php if(!sizeof($rooms)):?>
		<p><?php _e('You have not added any rooms yet.', 'wphostel')?></p>		
	<?php echo "</div>"; 
	return false;
	endif?>
	
	<table class="widefat">
		<tr><th><?php _e('Room title', 'wphostel')?></th><th><?php _e('Room type', 'wphostel')?></th><th><?php _e('Num beds', 'wphostel')?></th>
		<th><?php _e('Bathroom', 'wphostel')?></th><th><?php _e('Price', 'wphostel')?></th><th><?php _e('Book shortcode', 'wphostel')?></th><th><?php _e('Action', 'wphostel')?></th></tr>
		
		<?php foreach($rooms as $room):
		 $class = ('alternate' == @$class) ? '' : 'alternate';?>
			<tr class="<?php echo $class?>"><td><?php echo stripslashes($room->title)?></td> <td><?php echo $_room->prettify('rtype', $room->rtype);?></td>
			<td><?php echo $room->beds?></td><td><?php echo $_room->prettify('bathroom', $room->bathroom)?></td> <td><?php echo WPHOSTEL_CURRENCY.' '.$room->price?><br>
			<?php echo $_room->prettify('price_type', $room->price_type);?></td>
			<td><input type="text" value="[wphostel-book <?php echo $room->id?>]" size="15" onclick="this.select();" readonly></td>
			<td><a href="admin.php?page=wphostel_rooms&action=edit&id=<?php echo $room->id?>"><?php _e('Edit', 'wphostel')?></a> | <a href="#" onclick="wphostelDeleteRoom(<?php echo $room->id?>);return false;"><?php _e('Delete', 'wphostel')?></a></td></tr>
		<?php endforeach;?>
	</table>
	
	<p><?php _e('When you place the booking shortcode a "Book" button will be automatically generated. Use it on a page where you have manually described your room with pictures etc. You can pass custom button text as second argument to the shortcode - like this:', 'wphostel')?> [wphostel-book 1 "Reserve room!"].</p>
</div>

<script type="text/javascript" >
function wphostelDeleteRoom(id) {
	if(confirm("<?php _e('Are you sure?', 'wphostel')?>")) {
		window.location='admin.php?page=wphostel_rooms&action=delete&id='+id;
	}
}
</script>