<div class="wrap">
	<h1><?php _e('Hostel for WordPress', 'wphostel')?></h1>
	
	<p><?php _e('This is a plugin for managing hostels, BNB sites, and small hotel sites. You get an area where to manage your available rooms and prices, and the bookings made by visitors. Start with the main settings page to set up your booking mode, currency etc. Then once you enter your property and room details, you can use the following shortcodes:', 'wphostel')?></p>
	
	<ol>
		<li><input type="text" value="[wphostel-list]" readonly onclick="this.select();"> <?php _e('will display a table with your available rooms. A date selector on the top lets the user choose dates of their visit and then the rooms list is updated. If you have enabled booking in your Hostel settings page, the table will also show "Book" button when appropriate. The button will automaically load the booking form', 'wphostel')?></li>
		<li><input type="text" value="[wphostel-booking]" readonly onclick="this.select();"> <?php _e('displays a generic booking form with a drop-down selector for choosing room, and a date selector. If you use the [wphostel-list] shortcode you most probably do not need this one because the booking form is automatically generated.', 'wphostel');?></li>
	</ol>
	
	<p><?php printf(__('If you want to translate this plugin check out <a href="%s" target="_blank">this guide</a>. Our plugin textdomain is "wphostel" and you have to place your .po and .mo files in folder languages/', 'wphoistel'), 'http://blog.calendarscripts.info/how-to-translate-a-wordpress-plugin/');?></p>
</div>