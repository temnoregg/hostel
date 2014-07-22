<form method="post">
	<p><?php _e('From date:', 'wphostel')?> <input type="text" name="wphostel_from" value="<?php echo $datefrom?>" class="wphostelDatePicker"></p>
	<p><?php _e('To date:', 'wphostel')?> <input type="text" name="wphostel_to" value="<?php echo $dateto?>" class="wphostelDatePicker"></p>
	<p><input type="button" value="<?php _e('Show availability', 'wphostel')?>" onclick="validateHostelForm(this.form);"></p>
</form>

<div id="wphostelRoomsTable<?php echo $shortcode_id?>">
	<?php echo WPHostelRooms :: availability_table($shortcode_id);?>
</div>

<script type="text/javascript">
function validateHostelForm(frm) {
	startParts = frm.wphostel_from.value.split('-');
	var startDate = new Date(startParts[0], startParts[1], startParts[2]);
	endParts = frm.wphostel_to.value.split('-');
	var endDate = new Date(endParts[0], endParts[1], endParts[2]);
	
	daydiff = (endDate - startDate) / (1000*60*60*24);
	
	if(daydiff > 5) {
		 alert("<?php _e('Please select up to 5 days interval.', 'wphostel')?>");
		 return false;
	}

	data = {'action': 'wphostel_ajax', 'type': 'list_rooms', 'wphostel_from' : frm.wphostel_from.value, 
		'wphostel_to' : frm.wphostel_to.value, 'shortcode_id' : '<?php echo $shortcode_id?>'};
	jQuery.post(wphostel_i18n.ajax_url, data, function(msg){
			jQuery('#wphostelRoomsTable<?php echo $shortcode_id?>').html(msg);
		});
	
}
jQuery(document).ready(function() {
    jQuery('.wphostelDatePicker').datepicker({
        dateFormat : 'yy-mm-dd'
    });
});	
</script>