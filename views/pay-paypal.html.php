<p align="center"><?php _e('Booking ID:', 'wphostel')?> <?php echo $bid?></p>
<p align="center"><?php _e('Amount due:', 'wphostel')?> <?php echo WPHOSTEL_CURRENCY.' '. number_format($cost,2,".","");?></p>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<p align="center">
		<input type="hidden" name="cmd" value="_xclick">
		<input type="hidden" name="business" value="<?php echo get_option('wphostel_paypal');?>">
		<input type="hidden" name="item_name" value="<?php printf(__('Booking for %s Room / %d', 'wphostel'), $_room->prettify('rtype', $room->rtype), $bid)?>">
		<input type="hidden" name="item_number" value="<?php echo $bid?>">
		<input type="hidden" name="amount" value="<?php echo number_format($cost,2,".","")?>">
		<input type="hidden" name="return" value="<?php echo get_permalink($post->ID);?>">
		<input type="hidden" name="notify_url" value="<?php echo site_url('?wphostel=paypal&bid='.$bid);?>">
		<input type="hidden" name="no_shipping" value="1">
		<input type="hidden" name="no_note" value="1">
		<input type="hidden" name="currency_code" value="<?php echo WPHOSTEL_CURRENCY;?>">
		<input type="hidden" name="lc" value="US">
		<input type="hidden" name="bn" value="PP-BuyNowBF">
		<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-butcc.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</p>
	</form> 