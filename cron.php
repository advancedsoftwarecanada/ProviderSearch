<?php

/* ----------------
*  EXPORT NEW ORDERS
*  -------------- */
add_shortcode("cron_neworders", "cron_neworders_handler");
function cron_neworders_handler(){

	global $wpdb, $theorder, $woocommerce;
	$debug = $_GET['debug'];	
	

	// =============
	// RUN TIME
	// =============
	if($_GET['key'] == 'cron'){
	
		if($debug) $html .= 'starting<br/>';
	
		// =====================
		// CAREINGTON MEMBER INFORMATION
		// =====================			
		$orders_post = $wpdb->get_results("SELECT * FROM  `wp_posts` WHERE  `post_type` = 'shop_order' ORDER BY  `post_date` ASC");
		foreach($orders_post as $order_post){
		
			unset($txt);
			$txt = array();
			
			// -----
			// Fetch the products 
			// -----
			$order = new WC_Order( $order_post->ID );
			$items = $order->get_items();
			
			foreach ( $items as $item ) {				
				$product = 'NOPROD';
				
				if($item['product_id'] == 88){ $product = 'ddnaetn'; $groupcode = "MDRL2ATNA"; } // Aetna
				if($item['product_id'] == 92){ $product = 'ddncare'; $groupcode = "MDRL2POS";}   // Careington
			}			
			
			// ------
			// Member ID Generation
			// ------
			$member_id = $order_post->ID.$product;
			while(strlen($member_id) < 12){
				$member_id .= 0;
			}
			
			
			// ------
			// FILE BUILD - TXT
			// ------
			$txt['title']              = cleandata( "", 03);
			$txt['first_name']         = cleandata( get_post_meta( $order_post->ID, '_billing_first_name', true), 15);
			$txt['middle_initial']     = cleandata( "", 01);
			$txt['last_name']          = cleandata( get_post_meta( $order_post->ID, '_billing_last_name', true), 20);
			$txt['post_name']          = cleandata( "", 04);
			$txt['unique_id']     	   = cleandata( $member_id, 12);
			$txt['squence_number']     = cleandata( "", 2);
			
			$txt['member_ssn']         = cleandata( "", 9);
			
			$txt['address_line_1']     = cleandata( get_post_meta( $order_post->ID, '_billing_address_1', true), 33);
			$txt['address_line_2']     = cleandata( get_post_meta( $order_post->ID, '_billing_address_2', true), 33);
			$txt['city']               = cleandata( get_post_meta( $order_post->ID, '_billing_city', true), 21);
			$txt['state']              = cleandata( get_post_meta( $order_post->ID, '_billing_state', true), 02);
			$txt['zip']                = cleandata( get_post_meta( $order_post->ID, '_billing_postcode', true), 05);
			$txt['plus_4']             = cleandata( "", 04);
			
			$txt['home_phone']         = cleandata( get_post_meta( $order_post->ID, '_billing_phone', true), 10, 1); // 1 = phone
			
			$txt['work_phone']         = cleandata( "", 10);
			$txt['coverage']           = cleandata( "", 02);
			
			$txt['group_code']         = cleandata( $groupcode, 12); // MDRL2POS or MDRL2ATNA 
			$txt['terminitation_date'] = cleandata( "        ", 08);
			$txt['effective_date']     = cleandata( get_post_meta( $order_post->ID, '_paid_date', true), 08, 2); // 2 = date
			// $txt['date_of_birth ']     = cleandata( "", 08);
			// $txt['relation']           = cleandata( "", 01);
			
			// $txt['student_status']     = cleandata( "", 01);
			
			// $txt['plan']               = cleandata( "", 04);
			// $txt['gender']             = cleandata( "", 01);	
			
			$txt['fill'] 			  = "               ";
			
			foreach($txt as $value){
				$txt_output .= $value;
			}
			$txt_output .= "\r\n";
			

			
			
			// ------
			// FILE BUILD - CSV
			// ------
			$csv['first_name']         = get_post_meta( $order_post->ID, '_billing_first_name', true);
			$csv['last_name']          = get_post_meta( $order_post->ID, '_billing_last_name', true);
			$csv['squence_number']     = $member_id;
			
			$csv['address_line_1']     = get_post_meta( $order_post->ID, '_billing_address_1', true);
			$csv['address_line_2']     = get_post_meta( $order_post->ID, '_billing_address_2', true);
			$csv['city']               = get_post_meta( $order_post->ID, '_billing_city', true);
			$csv['state']              = get_post_meta( $order_post->ID, '_billing_state', true);
			
			$csv['home_phone']         = get_post_meta( $order_post->ID, '_billing_phone', true); 
			
			
			$csv['group_code']         = $groupcode; // MDRL2POS or MDRL2ATNA 
			$csv['effective_date']     = get_post_meta( $order_post->ID, '_paid_date', true);
			
			foreach($csv as $value){
				$csv_output .= $value .',';
			}
			$csv_output .= "\r\n";
			
			
			
		}

		
		// // DEPENDENT INFORMATION
		// if ($dependent){
		
			// $title            = '';
			// $first_name       = '';
			// $middle_initial   = '';
			// $last_name        = '';
			// $post_name        = '';
			// $member_unique_id = '';
			
			// $sequence_number  = '';
			
			// $dependent_ssn    = '';
			// $address_line_1   = '';
			// $address_line_2   = '';
			// $city             = '';
			// $state            = '';
			// $zip              = '';
			// $plus_4           = '';
			// $home_phone       = '';
			// $work_phone       = '';
			// $filler           = '';
			// $group_code       = '';
			// $termination_date = '';
			// $effective_date   = '';
			// $date_of_birth    = '';
			// $relation         = '';
			
			// $student_status   = '';
			// $plan             = '';
			// $gender           = '';
		
		// }
		
		
		
		

		// ====================
		// BUILD FILES
		// ====================
		
		// Careington Fixes
		// Need to remove last TWO lines of extra text
		$content = explode( "\r\n", $txt_output);
		$max = count($content); // how many lines
		
		var_dump($max);
		
		$i = 0;
		foreach($content as $line){
			if($i < $max - 3){
				$output_fixed .= $line . "\r\n"; // This line fixes the extra data problem on $output, and is now $output_fixed
			}
			$i++;
		}
		
		
		// SAVE TXT FOR CAREINGTON
		$filename = 'api/activate/dentaldiscountnetwork'.date('mdy').'_full.txt';
		file_put_contents($filename, $output_fixed); // Save File

		
		// SAVE CSV FOR PROCOM
		$filename = 'api/activate/dentaldiscountnetwork'.date('mdy').'_full.csv';
		file_put_contents($filename, $csv_output); // Save File
		
		
		
		// ====================
		// BUILD PROCOM FILE
		// ====================
		// $ftp_server=""; 
		// $ftp_user_name=""; 
		// $ftp_user_pass=""; 
		// $file = "";//tobe uploaded 
		// $remote_file = ""; 

		// // set up basic connection 
		// $conn_id = ftp_connect($ftp_server); 

		// // login with username and password 
		// $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

		// // upload a file 
		// if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) { 
		// echo "successfully uploaded $file\n"; 
		// exit; 
		// } else { 
		// echo "There was a problem while uploading $file\n"; 
		// exit; 
		// } 
		// // close the connection 
		// ftp_close($conn_id); 
		
		
		
		
		
		// ====================
		// CAREINGTON EMAIL NOTIFICATION	
		// ====================
		// assumes $to, $subject, $message have already been defined earlier...
		$to      = 'andrewnormore@gmail.com, andy@webtechnologymedia.ca'; //elig@careington.com
		$subject = 'DDN DAILY REPORT: Activations Sent to Careington';
		$message = 'This is an automatic test report';

		$headers[] = 'From: DentalDiscountNetwork.com <care@DentalDiscountNetwork.com>';

		add_filter( 'wp_mail_content_type', 'set_html_content_type' );
		wp_mail( $to, $subject, '', $headers, $filename);
		remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
		

		
		
		
		
		// =========
		// FILE SAVE -> PROCOM
		// =========
		// FTP username: doctor@proconserv.com
		// FTP server: ftp.proconserv.com
		// FTP & explicit FTPS port: 21
		
		
		
		
		
		
		
		// ====
		// DONE
		// ====
		if($debug) { 
			return $html . '<pre>'. $output_fixed .'</pre>';
		} else {
			return $html;
		}	
		
		
	
	} else {
		return 'Invalid Access';
	}
	
}



function cleandata($input, $length, $special_type=0){

	
	$debug = $_GET['debug'];
	if($debug) { $replace = "_"; } else { $replace = " ";}
	
	// Phone Number
	if($special_type==1){
		$input = str_replace( "-", "", $input);
		$input = str_replace( " ", "", $input);
	}
	
	// WooCommerce Date
	if($special_type==2){
		
		// Check for 0, woocommerce has a date bug
		if($input == 0){
			$input = date('mdY'); // if not date, use today
		} else {
			$input = strtotime($input); // Convets to timestamp
			$input = date('mdY', $input); // Converst to MMDDYYY as per Careingtons doc
		}
	}	
	
	$input = str_pad( $input, $length, $replace);
	
	return $input;

}







