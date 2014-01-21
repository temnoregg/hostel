<?php
class WPHostelBooking {
	function add($vars) {
		global $wpdb;
		
		// prepare from/to date
		$fromdate = $vars['fromyear'].'-'.$vars['frommonth'].'-'.$vars['fromday'];
		$todate = $vars['toyear'].'-'.$vars['tomonth'].'-'.$vars['today'];
		
		$result = $wpdb->query($wpdb->prepare("INSERT INTO ".WPHOSTEL_BOOKINGS." SET
		 room_id=%d, from_date=%s, to_date=%s, amount_paid=%s, amount_due=%s,
		 is_static=%d, contact_name=%s, contact_email=%s, contact_phone=%s, 
		 contact_type=%s, timestamp=NOW(), status=%s, beds=%d", 
		 $vars['room_id'], $fromdate, $todate, $vars['amount_paid'], $vars['amount_due'], @$vars['is_static'],
		 $vars['contact_name'], $vars['contact_email'], $vars['contact_phone'], $vars['contact_type'], 
		 $vars['status'], $vars['beds'] ));
		 
	  	if($result === false) return false;
	  	return $wpdb->insert_id; 
	}

	function edit($vars, $id) {
		global $wpdb;
		
		// prepare from/to date
		$fromdate = $vars['fromyear'].'-'.$vars['frommonth'].'-'.$vars['fromday'];
		$todate = $vars['toyear'].'-'.$vars['tomonth'].'-'.$vars['today'];
		
		$result = $wpdb->query($wpdb->prepare("UPDATE ".WPHOSTEL_BOOKINGS." SET
		 room_id=%d, from_date=%s, to_date=%s, amount_paid=%s, amount_due=%s,
		 contact_name=%s, contact_email=%s, contact_phone=%s, 
		 contact_type=%s, status=%s, beds=%d WHERE id=%d", 
		 $vars['room_id'], $fromdate, $todate, $vars['amount_paid'], $vars['amount_due'],
		 $vars['contact_name'], $vars['contact_email'], $vars['contact_phone'], 
		 $vars['contact_type'], $vars['status'], $vars['beds'], $id ));
		 
	  	if($result === false) return false;
	  	return true;
	}
	
	// delete booking
	function delete($id) {
		global $wpdb;
		
		$result = $wpdb->query($wpdb->prepare("DELETE FROM ".WPHOSTEL_BOOKINGS." WHERE id=%d", $id));
		
		if($result === false) return false;
	  	return true;
	}
	
	// transfer amount due to amount paid
	function mark_paid($id) {
		global $wpdb;
		
		$result = $wpdb->query($wpdb->prepare("UPDATE ".WPHOSTEL_BOOKINGS." SET
			amount_paid=amount_paid + amount_due, amount_due=0
			WHERE id=%d", $id));
			
		if($result === false) return false;
	  	return true;	
	}
	
	// cancel booking - change status to cancelled
	function cancel($id) {
		global $wpdb;
		
		$wpdb->query($wpdb->prepare("UPDATE ".WPHOSTEL_BOOKINGS." SET
			status='cancelled' WHERE id=%d", $id));
			
		if($result === false) return false;
	  	return true;		
	}
	
	// sends user and admin emails when booking is made
	function email($booking_id) {
		global $wpdb;
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		
		$email_options = get_option('wphostel_email_options');
		
		if(!$email_options['do_email_admin'] and !$email_options['do_email_user']) return false;
		
		// select booking
		$booking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WPHOSTEL_BOOKINGS." WHERE id=%d", $booking_id));
		$from_date = date(get_option('date_format'), strtotime($booking->from_date));
		$to_date = date(get_option('date_format'), strtotime($booking->to_date));
		$timestamp = date(get_option('date_format').' '.get_option('time_format'), strtotime($booking->timestamp));
		
		if($email_options['do_email_admin']) {
			$subject = $email_options["email_admin_subject"];
			$message = $email_options["email_admin_message"];
			
			$message = str_replace('{{from-date}}', $from_date, $message);
			$message = str_replace('{{to-date}}', $to_date, $message);
			$message = str_replace('{{url}}', admin_url("admin.php?page=wphostel_bookings&do=edit&id=".$booking_id."&type=upcoming"), $message);
			$message = str_replace('{{contact-name}}', $booking->contact_name, $message);
			$message = str_replace('{{contact-email}}', $booking->contact_email, $message);
			$message = str_replace('{{contact-phone}}', $booking->contact_phone, $message);
			$message = str_replace('{{timestamp}}', $timestamp, $message);
			
 			$headers .= 'From: '. $email_options['admin_email'] . "\r\n";
 			// echo $subject.'-'.$message.'<br>';
			wp_mail( $email_options['admin_email'], $subject, $message, $headers );
		} // end do email admin
		
		if($email_options['do_email_user']) {	
			$_room = new WPHostelRoom();

			// select room
			$room = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WPHOSTEL_ROOMS." WHERE id=%d", $booking->room_id));			
			
			$subject = $email_options["email_user_subject"];
			$message = $email_options["email_user_message"];
			
			$message = str_replace('{{from-date}}', $from_date, $message);
			$message = str_replace('{{to-date}}', $to_date, $message);
			$message = str_replace('{{amount-paid}}', $booking->amount_paid, $message);			
			$message = str_replace('{{amount-due}}', $booking->amount_due, $message);
			$message = str_replace('{{room_type}}', $_room->prettify("rtype", $room->rtype), $message);
			$message = str_replace('{{timestamp}}', $timestamp, $message);			
			$message = str_replace('{{num-beds}}', $booking->beds, $message);
			
			$headers .= 'From: '. $email_options['admin_email'] . "\r\n";
			// echo $subject.'-'.$message.'<br>';
			wp_mail( $booking->contact_email, $subject, $message, $headers );
		} // end do email user
	} // end email
}