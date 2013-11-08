<?php
class WPHostelPayment {
	// handle Paypal IPN request
	static function parse_request($wp) {
		
		// only process requests with "namaste=paypal"
	   if (array_key_exists('wphostel', $wp->query_vars) 
	            && $wp->query_vars['wphostel'] == 'paypal') {
	        self::paypal_ipn($wp);
	   }	
	}
	
	// process paypal IPN
	static function paypal_ipn($wp) {
		global $wpdb;
		echo "<!-- WPHOSTEL paypal IPN -->";
		
	   $paypal_email = get_option("wphostel_paypal");
		
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';
		foreach ($_POST as $key => $value) { 
		  $value = urlencode(stripslashes($value)); 
		  $req .= "&$key=$value";
		}		
		
		// post back to PayPal system to validate
		$header="";
		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .="Host: www.paypal.com\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
		
		
		if($fp) {			
			fputs ($fp, $header . $req);
		   while (!feof($fp)) {
		      $res = fgets ($fp, 1024);
		     
		      if (strstr ($res, "200 OK")) {
		      	// check the payment_status is Completed
			      // check that txn_id has not been previously processed
			      // check that receiver_email is your Primary PayPal email
			      // process payment
				   $payment_completed = false;
				   $txn_id_okay = false;
				   $receiver_okay = false;
				   $payment_currency_okay = false;
				   $payment_amount_okay = false;
				   
				   if($_POST['payment_status']=="Completed") {
				   	$payment_completed = true;
				   } 
				   else self::log_and_exit("Payment status: $_POST[payment_status]");
				   
				   // check txn_id
				   $txn_exists = $wpdb->get_var($wpdb->prepare("SELECT paycode FROM ".WPHOSTEL_PAYMENTS."
					   WHERE paytype='paypal' AND paycode=%s", $_POST['txn_id']));
					if(empty($txn_id)) $txn_id_okay = true; 
					else self::log_and_exit("TXN ID exists: $txn_id");  
					
					// check receiver email					
					if($_POST['business']==$paypal_email) {
						$receiver_okay = true;
					}
					else self::log_and_exit("Business email is wrong: $_POST[business]");
					
					// check payment currency
					if($_POST['mc_currency']==get_option("wphostel_currency")) {
						$payment_currency_okay = true;
					}
					else self::log_and_exit("Currency is $_POST[mc_currency]"); 
					
					// check amount
					$booking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WPHOSTEL_BOOKINGS." WHERE id=%d", $_GET['bid']));
					$fee = $booking->amount_due;
					if($_POST['mc_gross']>=$booking->amount_due) {
						$payment_amount_okay = true;
					}
					else self::log_and_exit("Wrong amount: $_POST[mc_gross] when price is $fee"); 
					
					// everything OK, insert payment and enroll
					if($payment_completed and $txn_id_okay and $receiver_okay and $payment_currency_okay 
							and $payment_amount_okay) {					
												
						$wpdb->query($wpdb->prepare("INSERT INTO ".WPHOSTEL_PAYMENTS." SET 
							booking_id=%d, date=CURDATE(), amount=%s, status='completed', paycode=%s, paytype='paypal'", 
							$_GET['bid'], $fee, $_POST['txn_id']));
							
						// activate booking and move amount due in amount paid
						$wpdb->query($wpdb->prepare("UPDATE ".WPHOSTEL_BOOKINGS." SET status='active', amount_paid = amount_due, amount_due = 0
							WHERE id=%d", $_GET['bid']));						
						
						$_booking = new WPHostelBooking();
						// send email if you have to
						$_booking->email($_GET['bid']);
						exit;
					}
		     	}
		     	else self::log_and_exit("Paypal result is not 200 OK: $res");
		   }  
		   fclose($fp);  
		} 
		else self::log_and_exit("Can't connect to Paypal");
		
		exit;
	}
	
	// log paypal errors
	static function log_and_exit($msg) {
		// log
		$errorlog=get_option("wphostel_errorlog");
		$errorlog = $msg."\n".$errorlog;
		update_option("wphostel_errorlog",$errorlog);
		
		// throw exception as there's no need to contninue
		exit;
	}
}