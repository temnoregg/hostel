<?php
// main model containing general config and UI functions
class WPHostel {
   static function install($update = false) {
   	global $wpdb;	
   	$wpdb -> show_errors();
   	
   	if(!$update) self::init();
	  
	   // rooms
   	if($wpdb->get_var("SHOW TABLES LIKE '".WPHOSTEL_ROOMS."'") != WPHOSTEL_ROOMS) {        
			$sql = "CREATE TABLE `" . WPHOSTEL_ROOMS . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `title` VARCHAR(100) NOT NULL DEFAULT 'room',
				  `rtype` VARCHAR(100) NOT NULL DEFAULT 'dorm',
				  `beds` TINYINT UNSIGNED NOT NULL DEFAULT 0,
				  `bathroom` VARCHAR(100) NOT NULL DEFAULT 'standard' /* ensuite, shared bathroom, etc goes here */,
				  `price` DECIMAL(8,2) NOT NULL DEFAULT '0.00',
				  `description` TEXT
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  }
	  	
	  	// bookings - will also contain unavailable dates which admin will store as bookings too			
		if($wpdb->get_var("SHOW TABLES LIKE '".WPHOSTEL_BOOKINGS."'") != WPHOSTEL_BOOKINGS) {        
				$sql = "CREATE TABLE `" . WPHOSTEL_BOOKINGS . "` (
					  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
					  `room_id` INT UNSIGNED NOT NULL DEFAULT 0,
					  `from_date` DATE NOT NULL DEFAULT '2000-01-01',
					  `to_date` DATE NOT NULL DEFAULT '2000-01-01',
					  `beds` TINYINT UNSIGNED NOT NULL DEFAULT 1,
					  `amount_paid` DECIMAL(8,2) NOT NULL DEFAULT '0.00',
					  `amount_due` DECIMAL(8,2) NOT NULL DEFAULT '0.00',
					  `is_static` TINYINT UNSIGNED NOT NULL DEFAULT 0 /* When 1 means admin just disabled these dates */,
					  `contact_name` VARCHAR(255) NOT NULL DEFAULT '',
					  `contact_email` VARCHAR(255) NOT NULL DEFAULT '',
					  `contact_phone` VARCHAR(255) NOT NULL DEFAULT '',
					  `contact_type` VARCHAR(255) NOT NULL DEFAULT '' /* how many people & male/female/couple/mixed */,
					  `timestamp` DATETIME /* When the reservation is made */,
					  `status` VARCHAR(100) NOT NULL DEFAULT 'active' /* pending, active or cancelled */					  
					) DEFAULT CHARSET=utf8;";
				
				$wpdb->query($sql);
		  }
		  
		  // payment records	  
	  	if($wpdb->get_var("SHOW TABLES LIKE '".WPHOSTEL_PAYMENTS."'") != WPHOSTEL_PAYMENTS) {        
			$sql = "CREATE TABLE `" . WPHOSTEL_PAYMENTS . "` (
				  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				  `booking_id` INT UNSIGNED NOT NULL DEFAULT 0,				  
				  `date` DATE NOT NULL DEFAULT '2001-01-01',
				  `amount` DECIMAL(8,2),
				  `status` VARCHAR(100) NOT NULL DEFAULT 'failed',
				  `paycode` VARCHAR(100) NOT NULL DEFAULT '',
				  `paytype` VARCHAR(100) NOT NULL DEFAULT 'paypal'
				) DEFAULT CHARSET=utf8;";
			
			$wpdb->query($sql);
	  }  	 
		  
		// if there's no currency, default it to USD
		$currency = get_option('wphostel_currency');
		if(empty($currency)) update_option('wphostel_currency', 'USD');
		
		// add new fields
		wphostel_add_db_fields(array(
			array("name" => 'price_type', "type" => "VARCHAR(100) NOT NULL DEFAULT 'per-bed'"),
		 ),
		 WPHOSTEL_ROOMS);  	  
   }
   
   // main menu
   static function menu() {
   	add_menu_page(__('Hostel', 'wphostel'), __('Hostel', 'wphostel'), "manage_options", "wphostel_options", 
   		array(__CLASS__, "options"));
   	add_submenu_page('wphostel_options', __('Settings', 'wphostel'), __('Settings', 'wphostel'), "manage_options", "wphostel_options", 
   		array(__CLASS__, "options"));
		add_submenu_page('wphostel_options', __("Manage Rooms", 'wphostel'), __("Manage Rooms", 'wphostel'), 'manage_options', 'wphostel_rooms', array('WPHostelRooms', "manage"));
		add_submenu_page('wphostel_options', __("Manage Bookings", 'wphostel'), __("Manage Bookings", 'wphostel'), 'manage_options', 'wphostel_bookings', array('WPHostelBookings', "manage")); 
		add_submenu_page('wphostel_options', __("Unavailable Dates", 'wphostel'), __("Unavailable Dates", 'wphostel'), 'manage_options', 'wphostel_unavailable', array('WPHostelBookings', "unavailable")); 
   	add_submenu_page('wphostel_options', __("Help", 'wphostel'), __("Help", 'wphostel'), 'manage_options', 'wphostel_help', array('WPHostelHelp', "index")); 	
		
	}
	
	// CSS and JS
	static function scripts() {
		// CSS
		wp_register_style( 'wphostel-css', WPHOSTEL_URL.'css/main.css?v=1');
	  wp_enqueue_style( 'wphostel-css' );
   
   	wp_enqueue_script('jquery');
	   
	   // Namaste's own Javascript
		wp_register_script(
				'wphostel-common',
				WPHOSTEL_URL.'js/common.js',
				false,
				'0.1.0',
				false
		);
		wp_enqueue_script("wphostel-common");
		
		$translation_array = array('ajax_url' => admin_url('admin-ajax.php'),
			'enter_name' =>  __('Please enter name!', 'wphostel'),
			'enter_email' => __('Please enter email address!', 'wphostel'),
			'beds_required' => __('Please enter number of beds, numbers only', 'wphostel'),
			'from_date_required' => __('Please enter arrival date', 'wphostel'),
			'to_date_required' => __('Please enter date of leaving', 'wphostel'),
			'from_date_atleast_today' => __('Date of arrival cannot be in the past', 'wphostel'),
			'from_date_before_to' => __('Date of arrival cannot be after date of leave', 'wphostel'),);	
		wp_localize_script( 'wphostel-common', 'wphostel_i18n', $translation_array );	
		
		// jQuery Validator
		wp_enqueue_script(
				'jquery-validator',
				'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js',
				false,
				'0.1.0',
				false
		);
	}
	
	// initialization
	static function init() {
		global $wpdb;
		load_plugin_textdomain( 'wphostel', false, WPHOSTEL_RELATIVE_PATH."/languages/" );
		if (!session_id()) @session_start();
		
		// define table names 
		define( 'WPHOSTEL_ROOMS', $wpdb->prefix. "wphostel_rooms");
		define( 'WPHOSTEL_BOOKINGS', $wpdb->prefix. "wphostel_bookings");
		define( 'WPHOSTEL_PAYMENTS', $wpdb->prefix. "wphostel_payments");
	
		define( 'WPHOSTEL_VERSION', get_option('wphostel_version'));
		
		// if there's no currency, default it to USD
		$currency = get_option('wphostel_currency');
		if(empty($currency)) update_option('wphostel_currency', 'USD');
		define( 'WPHOSTEL_CURRENCY', get_option('wphostel_currency'));
		
		// shortcodes
		add_shortcode('wphostel-booking', array("WPHostelShortcodes", "booking"));
		add_shortcode('wphostel-list', array("WPHostelShortcodes", "list_rooms"));
		add_shortcode('wphostel-book', array("WPHostelShortcodes", "book"));
		
		// Paypal IPN
		add_filter('query_vars', array(__CLASS__, "query_vars"));
		add_action('parse_request', array("WPHostelPayment", "parse_request"));
		
		// cleanup unconfirmed bookings
		if(get_option('wphostel_booking_mode') == 'paypal') {
			$cleanup_hours = get_option('wphostel_cleanup_hours');
			if(!empty($cleanup_hours) and is_numeric($cleanup_hours)) {
				 $wpdb->query("DELETE FROM ".WPHOSTEL_BOOKINGS." WHERE
				 	timestamp < NOW() - INTERVAL $cleanup_hours HOUR	
				 	AND amount_paid = 0 AND status != 'active'");
			}
		}
		
		$old_version = get_option('wphostel_version');
		if(empty($old_version) or $old_version < 0.72) self :: install(true);
		update_option('wphostel_version', '0.72');
	}
	
	// handle Hostel vars in the request
	static function query_vars($vars) {
		$new_vars = array('wphostel');
		$vars = array_merge($new_vars, $vars);
	   return $vars;
	} 	
		
	// parse Namaste vars in the request
	static function template_redirect() {		
		global $wp, $wp_query, $wpdb;
		$redirect = false;		
		 
	  if($redirect) {
	   	if(@file_exists(TEMPLATEPATH."/".$template)) include TEMPLATEPATH."/namaste/".$template;		
			else include(WPHOSTEL_PATH."/views/templates/".$template);
			exit;
	  }	   
	}	
			
	// manage general options
	static function options() {
		if(!empty($_POST['ok'])) {
			if(empty($_POST['currency'])) $_POST['currency'] = $_POST['custom_currency'];
			update_option('wphostel_currency', $_POST['currency']);
			update_option('wphostel_booking_mode', $_POST['booking_mode']);
			update_option('wphostel_email_options', array("do_email_admin"=>@$_POST['do_email_admin'], 
				"admin_email"=>$_POST['admin_email'], "do_email_user"=>@$_POST['do_email_user'], 
				"email_admin_subject"=>$_POST['email_admin_subject'], "email_admin_message"=>$_POST['email_admin_message'],
				"email_user_subject"=>$_POST['email_user_subject'], "email_user_message"=>$_POST['email_user_message']));
			update_option('wphostel_paypal', $_POST['paypal']);
			update_option('wphostel_cleanup_hours', $_POST['cleanup_hours']);
			// update_option('wphostel_booking_url', $_POST['booking_url']);		
			update_option('wphostel_min_stay', $_POST['min_stay']);
		}		
		
		$currency = get_option('wphostel_currency');
		$currencies=array('USD'=>'$', "EUR"=>"&euro;", "GBP"=>"&pound;", "JPY"=>"&yen;", "AUD"=>"AUD",
		   "CAD"=>"CAD", "CHF"=>"CHF", "CZK"=>"CZK", "DKK"=>"DKK", "HKD"=>"HKD", "HUF"=>"HUF",
		   "ILS"=>"ILS", "MXN"=>"MXN", "NOK"=>"NOK", "NZD"=>"NZD", "PLN"=>"PLN", "SEK"=>"SEK",
		   "SGD"=>"SGD");
		$currency_keys = array_keys($currencies);  
		   
		$booking_mode = get_option('wphostel_booking_mode');   
		$email_options = get_option('wphostel_email_options');
		$paypal = get_option('wphostel_paypal');
		$cleanup_hours = get_option('wphostel_cleanup_hours');
		$min_stay = get_option('wphostel_min_stay');
		   	
		require(WPHOSTEL_PATH."/views/options.php");
	}	
	
	static function help() {
		require(WPHOSTEL_PATH."/views/help.php");
	}	
	
	static function register_widgets() {
		// register_widget('WPHostelWidget');
	}
}