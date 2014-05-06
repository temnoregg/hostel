<?php
/*
Plugin Name: Hostel
Plugin URI: http://backpackercompare.com/wp-hostel/
Description: Hostel / BnB management plugin 
Author: Kiboko Labs
Version: 0.7.3
Author URI: http://calendarscripts.info/
License: GPLv2 or later
*/

define( 'WPHOSTEL_PATH', dirname( __FILE__ ) );
define( 'WPHOSTEL_RELATIVE_PATH', dirname( plugin_basename( __FILE__ )));
define( 'WPHOSTEL_URL', plugin_dir_url( __FILE__ ));

// require controllers and models
require(WPHOSTEL_PATH."/helpers/htmlhelper.php");
require(WPHOSTEL_PATH."/models/hostel.php");
require(WPHOSTEL_PATH."/models/room.php");
require(WPHOSTEL_PATH."/controllers/rooms.php");
require(WPHOSTEL_PATH."/models/booking.php");
require(WPHOSTEL_PATH."/models/payment.php");
require(WPHOSTEL_PATH."/controllers/bookings.php");
require(WPHOSTEL_PATH."/controllers/shortcodes.php");
require(WPHOSTEL_PATH."/controllers/help.php");

add_action('init', array("WPHostel", "init"));

register_activation_hook(__FILE__, array("WPHostel", "install"));
add_action('admin_menu', array("WPHostel", "menu"));
add_action('admin_enqueue_scripts', array("WPHostel", "scripts"));

// show the things on the front-end
add_action( 'wp_enqueue_scripts', array("WPHostel", "scripts"));

// widgets
add_action( 'widgets_init', array("WPHostel", "register_widgets") );

// other actions
add_action('wp_ajax_hostel_ajax', 'wphostel_ajax');
add_action('wp_ajax_nopriv_hostel_ajax', 'wphostel_ajax');