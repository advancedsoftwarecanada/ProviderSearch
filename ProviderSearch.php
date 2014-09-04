<?php
/*
Plugin Name: ProviderSearch
Plugin URI: http://www.WebTechnologyMedia
Description: Dentist Plan Provider Search
Version: 1.0
Author: Andy Normore
Author URI: http://www.AndrewNormore.com
License: PRIVATE
*/


// -----------
// Includes
// -----------
include ('settings.php');
include ('includes/scripts.php');
include ('includes/database.php');
include ('includes/display.php');
include ('cron.php');
session_start();

wp_register_style( 'providersearch', '/wp-content/plugins/providersearch/includes/css/providersearch.css' );
wp_enqueue_style('providersearch');
wp_enqueue_script( 'jquery' );


$providersearch_settings_options = GET_option('providersearch_settings');

/* --------------- 
*  OBSERVER ID COOKIE
*  -------------- */
function ab_split_settings() {

	global $wpdb;
	global $AB_HOME;
	global $AB_SEARCH;
	global $AB_CHECKOUT;
	



}
add_action( 'init', 'ab_split_settings');

/* ----------------
*  ZIP CODE - FIND NEAR ZIP CODES
*  -------------- */
// get all the zipcodes within the specified radius - default 20
function zipcodeRadius($lat, $lon, $radius)
{
	global $wpdb;

    $radius = $radius ? $radius : 20;
    $sql = 'SELECT distinct(zip) FROM zip_codes WHERE (3958*3.1415926*sqrt((latitude-'.$lat.')*(latitude-'.$lat.') + cos(latitude/57.29578)*cos('.$lat.'/57.29578)*(longitude-'.$lon.')*(longitude-'.$lon.'))/180) <= '.$radius.';';
	$results = $wpdb->get_results($sql);

    $zipcodeList = array();
	foreach($results as $result)
    {
        array_push($zipcodeList, $result->zip);
    }
	
    return $zipcodeList;
}




/* -------------------------
*  3 MONTHS FREE
*  ---------------------- */
add_shortcode("providersearch_get_3_free", "providersearch_get_3_free_handler");
function providersearch_get_3_free_handler(){

	global $woocommerce;
	
	$woocommerce->cart->empty_cart();
	
	$woocommerce->cart->add_to_cart( 2397 );
	
	wp_redirect( '/3-free-months-of-service' ); exit;
	
}




/* -------------------------
*  JOIN CAREINGTON
*  ---------------------- */
add_shortcode("providersearch_join_careington", "providersearch_join_careington_handler");
function providersearch_join_careington_handler(){

	global $woocommerce;
		
	// 3 Months Free?
	// foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {		
		// // Keep 3 Months Free
		// if($cart_item['product_id'] == 2397 ){
			// $free3 = 1;
		// }		
	// }
	
	$woocommerce->cart->empty_cart();
	
	$woocommerce->cart->add_to_cart( 92 );
	$woocommerce->cart->add_to_cart( 2507 );
	$woocommerce->cart->add_to_cart( 2397 );
	
	echo do_shortcode("[observer_direct field='open_care' value='1']");
	echo do_shortcode("[observer_direct field='checkout' value='1']");
	
	wp_redirect( '/checkout' ); exit;
	
}




/* -------------------------
*  JOIN AETNA
*  ---------------------- */
add_shortcode("providersearch_join_aetna", "providersearch_join_aetna_handler");
function providersearch_join_aetna_handler(){

	global $woocommerce;
	// 3 Months Free?
	// foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {		
		// // Keep 3 Months Free
		// if($cart_item['product_id'] == 2397 ){
			// $free3 = 1;
		// }		
	// }
	
	$woocommerce->cart->empty_cart();
	
	$woocommerce->cart->add_to_cart( 88 );
	$woocommerce->cart->add_to_cart( 2507 );
	$woocommerce->cart->add_to_cart( 2397 );
	
	echo do_shortcode("[observer_direct field='open_ddn' value='1']");	
	echo do_shortcode("[observer_direct field='checkout' value='1']");
	
	wp_redirect( '/checkout' ); exit;

}



/* -------------------------
*  JOIN 247 My Doctor Live
*  ---------------------- */
add_shortcode("providersearch_join_drlive", "providersearch_join_drlive_handler");
function providersearch_join_drlive_handler(){

	global $woocommerce;
	
	// Check for DrLive membership already in cart
	$incart = 0;
	foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {		
		// found dr live
		if($cart_item['product_id'] == 822 ){
			$incart = 1;
		}		
	}
	
	if(!$incart){
		$woocommerce->cart->add_to_cart( 822 );
	}
	
	return 'added';

}





/* ----------------
*  FORMAT PHONE NUMBER
*  -------------- */
function formatPhoneNumber($phoneNumber) {
    $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);

    if(strlen($phoneNumber) > 10) {
        $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
        $areaCode = substr($phoneNumber, -10, 3);
        $nextThree = substr($phoneNumber, -7, 3);
        $lastFour = substr($phoneNumber, -4, 4);

        $phoneNumber = '+'.$countryCode.' ('.$areaCode.') '.$nextThree.'-'.$lastFour;
    }
    else if(strlen($phoneNumber) == 10) {
        $areaCode = substr($phoneNumber, 0, 3);
        $nextThree = substr($phoneNumber, 3, 3);
        $lastFour = substr($phoneNumber, 6, 4);

        $phoneNumber = '('.$areaCode.') '.$nextThree.'-'.$lastFour;
    }
    else if(strlen($phoneNumber) == 7) {
        $nextThree = substr($phoneNumber, 0, 3);
        $lastFour = substr($phoneNumber, 3, 4);

        $phoneNumber = $nextThree.'-'.$lastFour;
    }

    return $phoneNumber;
}





/* ----------------
*  providersearch_clear_header SHORTCODE
*  -------------- */
add_shortcode("providersearch_clear_header", "providersearch_clear_header_handler");
function providersearch_clear_header_handler(){

	$html .= '
		<style>

		.small_banner {
			height: 20px !important;
			padding: 0 !important;
			text-indent: -9999px !important;
		}
		  
		</style>';
	
	return $html;
}





/* ----------------
*  3 MONTHS FREE AJAX -> WOOCOMMERCE 
*  -------------- */
add_shortcode("searchprovider_3_free", "searchprovider_3_free_handler");
function searchprovider_3_free_handler(){

	global $woocommerce;

	$woocommerce->cart->add_to_cart(2397);


}















// ++++++++++++++++++++++++++++++++++++++++++++++
//
//
//	VERSION 3 
//
//
// ++++++++++++++++++++++++++++++++++++++++++++++


/* ----------------
*  Provider Table
*  -------------- */
add_shortcode("providersearch_providertable", "providersearch_providertable_handler");
function providersearch_providertable_handler(){

	global $wpdb;
	global $AB_HOME, $AB_SEARCH, $AB_CHECKOUT;

	$GET = preg_replace( "/[^a-zA-Z0-9\s\p{P}]/", '', $_GET );
	
	// SESSION ZIP CODE
	if(isset($_SESSION['zip'])){ 	
		if ($_SESSION['zip'] != $GET['zip']){
			$_SESSION['zip'] = $GET['zip'];
		}
	}else{
		$_SESSION['zip'] = $GET['zip'];
	}
	// echo $_SESSION['zip']; //DEBUG	
	
	
	
	// ============
	// MAIL CHIMP
	// ============
	include ('includes/mailchimp/MCAPI.class.php');
	// if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i", $_GET['email'])) {
		// return "Email address is invalid"; 
	// }
	
	// grab an API Key from http://admin.mailchimp.com/account/api/
	$api = new MCAPI('ab79e7e883a8d06ea602e014be58f364-us3');
	
	// grab your List's Unique Id by going to http://admin.mailchimp.com/lists/
	// Click the "settings" link for the list - the Unique Id is at the bottom of that page. 
	$list_id = "4b02ce6276";

	// Full Call:
	// $id, $email_address, $merge_vars=NULL, $email_type='html', $double_optin=true, $update_existing=false, $replace_interests=true, $send_welcome=false
	
	$api->listSubscribe($list_id, $GET['email'], '', 'html', 'false');
	
	
	
	// ============
	// QUICK REPORT
	// ============
	$wpdb->query("UPDATE `X_observer` SET  `zip_email` =  '".mysql_real_escape_string($GET['email'])."' WHERE `observer_id` = '".mysql_real_escape_string($_COOKIE['observer_id'])."';");
	
	
	// =====================
	// SEARCH FOR ZIP CODES
	// =====================
	$searchzip = $GET['zip'];	
	$distance = $GET['distance'];
	$plantype = $GET['group1'];
	
	//////////////////////////////
	// Find Lat and Long of Zip
	//////////////////////////////
	$found_results = 0;
	$QUERY1 = $wpdb->get_results( "SELECT * FROM x_zip_codes WHERE zip = '".$searchzip."'" );
	foreach($QUERY1 as $data){
		$lat1 = $data->latitude;
		$lon1 = $data->longitude;
		$state = $data->state;
		
		$found_results = 1;
	}
	
	// NO RESULTS
	// ----------
	if($found_results == 0){
		$NO_RESULT = 1;
		error_log("ZIP CODE ERROR - NO RESULT FOUND");
		$wpdb->query("UPDATE `X_observer` SET  `zip_invalid` =  '1' WHERE `observer_id` = '".mysql_real_escape_string($_COOKIE['observer_id'])."';");
	}
	
	
	//////////////////////////////
	// GEOBLOCK STATES
	//////////////////////////////
	if($state=='FL' || $state=='VT' || $state=='MT' || $state=='UT' || $state=='WV' || $state=='WA' || $NO_RESULT == 1 ){
	
	$wpdb->query("UPDATE `X_observer` SET  `zip_invalid` =  '1' WHERE `observer_id` = '".mysql_real_escape_string($_COOKIE['observer_id'])."';");
	
	$html .= '
	
		<!-- Start of header -->
		<header>
			<div class="holder clearfix">
				<div class="logo"><a href="/"><img src="'.get_template_directory_uri().'/img/logo2.png" alt="logo" /></a></div>
				<div class="slogan"><h2>Dental Plans you can really<br />sink your teeth into</h2></div>
				
								
				<!-- Customer Service -->
				<div class="scustomerservice">
				
					<div class="toprightmenu clearfix">
						<ul class="clearfix">
							
							<?php if($AB_HOME == 2){
							?>
								<li><a href="/compare">Compare</a></li>
							<?php } ?>
							<li><a href="/dentist-search">Find Dentists</a></li>
							<li><a href="/faq">FAQ</a></li>
							<li class="last"><a href="/contact">Contact us</a></li>
						</ul>
						<br />
						<div style="text-align:right; font-size:12px; color:#FFF; font-family:arial; padding-right:12px;"><span style="color:#eb1010; font-size:18px; font-weight:bold;">Questions?</span><span style="font-size:14px;">1-888-908-5242</span></div>
					</div><!-- End of toprightmenu -->
				
				</div>
				<div style="clear:both"></div>
				<!-- Customer Service -->
				
				
				<div class="clearfix"></div>
				<ul class="steps clearfix">
					<li class="step1">
						<h3>1</h3>
						<p>Search Dental Discounts</p>
					</li>
					<li class="step2 active">
						<h3>2</h3>
						<p class="twolines">Choose a Dental<br />Discount Plan</p>
					</li>
					<li class="step3">
						<h3>3</h3>
						<p>Enrollment Information</p>
					</li>
				</ul><!-- End of steps -->
				<div class="toptext">Sorry, we do not provide service to that State or Zipcode, '.$_GET['zip'].'</strong></div>
			</div><!-- End of holder -->
		</header>
		<!-- End of header -->
		
	
		<!-- Start of main-search-results -->
		<div id="main-search-results">
			<div class="holder clearfix">
			
				<h2><center>(We do not provide service to FL, VT, MT, UT, WV or WA)</center></h2>
				<p>&nbsp;</p>
				
				<center>
					<div class="findadentist" style="width:300px">
						<h2>Find a Plan</h2>
						<form id="searchform" method="get" action="/search-plans">
							<input type="hidden" name="distance" value="1" />
							<div class="zip">
								<span>Your ZIP:</span> <input id="zipsearch" type="text" name="zip"/>
							</div>
							<div class="zip">
								<span>Your Email:</span> <input id="email" type="text" name="email"/>
							</div>
							<div class="radiobtn">
								<div class="radio1"><input type="radio" name="group1" value="Individual"><span>Individual</span></div>
								<div class="radio2"><input type="radio" name="group1" value="Family" checked ><span>Family</span></div>
							</div>
							<div class="submit">
								<button id="searchprovider" ></button>
							</div>
						</form>
					</div>
				</center>
						
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				
			</div><!-- End of holder -->
		</div>
		
		<!-- End of main-search-results -->';
		return $html;
	}
	
	
	
	$distance = 1;
	while ( count($providers) <= 20 ) {
		$i++;
		
		//////////////////////////////
		// LIMIT - No more than 50 zip codes, or it gets real slow and dies
		//////////////////////////////
		if( count($zipcodes) > 100 ){
		$html = '
			<!-- Start of header -->
			<header>
				<div class="holder clearfix">
					<div class="logo"><a href="/"><img src="'.get_template_directory_uri().'/img/logo2.png" alt="logo" /></a></div>
					<div class="slogan"><h2>Dental Plans you can really<br />sink your teeth into</h2></div>
					
					<!-- Customer Service -->
					<div class="scustomerservice">
					
						<div class="toprightmenu clearfix">
							<ul class="clearfix">
								
								<?php if($AB_HOME == 2){
								?>
									<li><a href="/compare">Compare</a></li>
								<?php } ?>
								<li><a href="/dentist-search">Find Dentists</a></li>
								<li><a href="/faq">FAQ</a></li>
								<li class="last"><a href="/contact">Contact us</a></li>
							</ul>
							<br />
							<div style="text-align:right; font-size:12px; color:#FFF; font-family:arial; padding-right:12px;"><span style="color:#eb1010; font-size:18px; font-weight:bold;">Questions?</span><span style="font-size:14px;">1-888-908-5242</span></div>
						</div><!-- End of toprightmenu -->
					
					</div>
					<div style="clear:both"></div>
					<!-- Customer Service -->
				
				
					<div class="clearfix"></div>
					<ul class="steps clearfix">
						<li class="step1">
							<h3>1</h3>
							<p>Search Dental Discounts</p>
						</li>
						<li class="step2 active">
							<h3>2</h3>
							<p class="twolines">Choose a Dental<br />Discount Plan</p>
						</li>
						<li class="step3">
							<h3>3</h3>
							<p>Enrollment Information</p>
						</li>
					</ul><!-- End of steps -->
					<div class="toptext">Sorry, we do not provide service to that State or Zipcode, '.$_GET['zip'].'</strong></div>
				</div><!-- End of holder -->
			</header>
			<!-- End of header -->
		
		
			<center><h1>Sorry, We searched over 50 zip codes near you with no results!</h1><p>Please check the zip code and try again!</p><p>(We do not provide service to VT, MT, UT, WV or WA)</p></center>';
			// $html .= do_shortcode('[searchprovider_plans]');
			return $html;
		}
		
		
		//////////////////////////////
		// Find Zip Codes by radius
		//////////////////////////////
		$zipcodes = zipcodeRadius($lat1, $lon1, $distance);
		// var_dump(count($providers));
		// var_dump($i .'='.count($zipcodes) .'='.count($providers));

		$distance += 5;
		
		
		//////////////////////////////////////////////////
		// Find Providers in ZipCodes For GOOGLE MAP
		//////////////////////////////////////////////////
		$sql_providers = 'SELECT * FROM X_providers_full WHERE ';
		foreach($zipcodes as $zipcode){
			$sql_providers .= "zip = '".$zipcode."' OR " ;
		}
		
		$sql_providers = substr($sql_providers, 0, -4); // Strip the last OR 
		$sql_providers .= ' ORDER BY address1';
		// var_dump($sql_providers);
		// $sql_providers .= ' LIMIT 50';
		$providers = $wpdb->get_results($sql_providers);
		
		$dentists_found = count($providers);
		
		$_SESSION['dentists_found'] = $dentists_found;
		
		$_SESSION['zip'] = $GET['zip'];
	}


    // ===========================
	//
	// SEARCH RESULT
	//
	// ===========================
		
		// ===================
		// AB SPLIT TEST THEME
		// ===================
		///////////////////////////////////////////////////////////////////////////////////
		if($AB_SEARCH == 1){
		///////////////////////////////////////////////////////////////////////////////////
		
			$html .= '
		
			<!-- Start of header -->
			<header>
				<div class="holder clearfix">
					<div class="logo"><a href="/"><img src="'.get_template_directory_uri().'/img/logo2.png" alt="logo" /></a></div>
					<div class="slogan"><h2>Dental Plans you can really<br />sink your teeth into</h2></div>
							
					<!-- Customer Service -->
					<div class="scustomerservice">
					
						<div class="toprightmenu clearfix">
							<ul class="clearfix">
								
								<?php if($AB_HOME == 2){
								?>
									<li><a href="/compare">Compare</a></li>
								<?php } ?>
								<li><a href="/dentist-search">Find Dentists</a></li>
								<li><a href="/faq">FAQ</a></li>
								<li class="last"><a href="/contact">Contact us</a></li>
							</ul>
							<br />
							<div style="text-align:right; font-size:12px; color:#FFF; font-family:arial; padding-right:12px;"><span style="color:#eb1010; font-size:18px; font-weight:bold;">Questions?</span><span style="font-size:14px;">1-888-908-5242</span></div>
						</div><!-- End of toprightmenu -->
					
					</div>
					<div style="clear:both"></div>
					<!-- Customer Service -->			
				
				
				
					<div class="clearfix"></div>
					<ul class="steps clearfix">
						<li class="step1">
							<h3>1</h3>
							<p>Search Dental Discounts</p>
						</li>
						<li class="step2 active">
							<h3>2</h3>
							<p class="twolines">Choose a Dental<br />Discount Plan</p>
						</li>
						<li class="step3">
							<h3>3</h3>
							<p>Enrollment Information</p>
						</li>
					</ul><!-- End of steps -->
					<div id="main-home">
						<div class="holder clearfix">
							<div class="tabletitle">Here is the most <em>comprehensive savings</em> plan in '.$GET['zip'].'! <span style="font-size:12px; color:#F00;cursor:pointer;" data-toggle="modal" data-target="#careington_providers">(View Dentists)</span> </div>
						</div>
					</div>
					<div id="SearchFlashText" style="color:F00; font-size:24px; color:#F00; text-align:center; padding-bottom:20px;"><strong>SPECIAL OFFER</strong>: Sign up today and get three months for <u>FREE</u>!</div>
				</div><!-- End of holder -->
			</header>
			<!-- End of header -->';
			
			$html .= '
			
			<div id="main-home">
				<div class="holder clearfix">
				
						<div class="row">
							<div class="col-md-6">
							
								<div id="searchresult2">
								
									<center>										
										<img src="'.get_template_directory_uri().'/img/as-seen-on-gma.png" alt="Enroll Now" style="margin-bottom:20px;"/>
										
										
										<video  id="americaplayer" class="video-js vjs-default-skin" style="margin-bottom:20px; width:100%; height:auto;"
										  controls preload="auto" width="460" height="260"
										  poster="'.get_template_directory_uri().'/media/discount_club.jpg"
										 >
										 <source src="'.get_template_directory_uri().'/media/goodmorningamerica.mp4" type="video/mp4" />
										 <source src="'.get_template_directory_uri().'/media/goodmorningamerica.webm" type="video/webm" />
										 <source src="'.get_template_directory_uri().'/media/goodmorningamerica.ogv" type="video/ogg" />
										</video>
										
										
									</center>
								
									<ul class="plan-items clearfix">
										<li class="item ">
											<div class="title" style="width:100%; text-align:center;">Careington '.$plantype .' Plan: $9.83 a month</div>
											<div class="desc"><center>Save 20% to 70%  on many dental procedures.</br>&nbsp;</center></div>
											<ul class="clearfix">
												<li><strong>Add up to five additional members for free!</strong></li>
												<li><strong>180,000+ dentists nationwide</strong></li>
												<li>No paper work hassles</li>
												<li>48 Hour activation</li>
												<li>30 Day money back guarantee</li>
												<li>3 Months FREE on signup!</li>	
											</ul>
											<center>
												<a href="/search-plans/join-careington">
													<img src="'.get_template_directory_uri().'/img/btn-enroll-now.png" alt="Enroll Now"/>
												</a>
											</center>
										</li>
									</ul>
									
									<center>
										<div class="btn btn-info" style="margin:15px;" data-toggle="modal" data-target="#careington_providers">View Dentists in '.$GET['zip'].' </div>
									</center>
									

								</div>
								
							</div>
							<div class="col-md-6 customwell">
								
								<!-- Savings Begin -->
								<div class="tables" >
									<div class="col">
										<table style="width:100%">
											<tr class="head">
												<th class="first"><img src="'.get_template_directory_uri().'/img/careington.jpg" alt="#" /></th>
												<th class="second"><span>Average Cost</span></th>
												<th class="third"><span>Average cost with<br />Careington</span></th>
												<th class="fourth"><img src="'.get_template_directory_uri().'/img/yousave.png" alt="you save" /></th>
											</tr>
											<tr>
												<td class="first"><span>Adult Teeth Cleaning</span></td>
												<td class="second"><span>$102</span></td>
												<td class="third"><span>$31</span></td>
												<td class="fourth"><span>70%</span></td>
											</tr>
											<tr>
												<td class="first"><span>Childrens Teeth Cleaning</span></td>
												<td class="second"><span>$75</span></td>
												<td class="third"><span>$23</span></td>
												<td class="fourth"><span>69%</span></td>
											</tr>
											<tr>
												<td class="first"><span>Routine 6 Month Check-Up</span></td>
												<td class="second"><span>$57</span></td>
												<td class="third"><span>$15</span></td>
												<td class="fourth"><span>74%</span></td>
											</tr>
											<tr>
												<td class="first"><span>In Depth Check-Up</span></td>
												<td class="second"><span>$99</span></td>
												<td class="third"><span>$19</span></td>
												<td class="fourth"><span>81%</span></td>
											</tr>
											<tr>
												<td class="first"><span>Full Mouth X-Rays</span></td>
												<td class="second"><span>$144</span></td>
												<td class="third"><span>$43</span></td>
												<td class="fourth"><span>70%</span></td>
											</tr>
											<tr>
												<td class="first"><span>Panoramic Film</span></td>
												<td class="second"><span>$122</span></td>
												<td class="third"><span>$43</span></td>
												<td class="fourth"><span>65%</span></td>
											</tr>
											<tr>
												<td class="first"><span>Protective Sealant / Tooth</span></td>
												<td class="second"><span>$61</span></td>
												<td class="third"><span>$22</span></td>
												<td class="fourth"><span>64%</span></td>
											</tr>
											<tr>
												<td class="first"><span>1 Surface White Filling</span></td>
												<td class="second"><span>$175</span></td>
												<td class="third"><span>$55</span></td>
												<td class="fourth"><span>69%</span></td>
											</tr>
											<tr>
												<td class="first"><span>Single Crown - Porcelain on High Noble Metal</span></td>
												<td class="second"><span>$1,227</span></td>
												<td class="third"><span>$511</span></td>
												<td class="fourth"><span>58%</span></td>
											</tr>
											<tr>
												<td class="first"><span>Single Crown - Porcelain on Noble Metal</span></td>
												<td class="second"><span>$1,162</span></td>
												<td class="third"><span>$483</span></td>
												<td class="fourth"><span>58%</span></td>
											</tr>								
											<tr>
												<td class="first"><span>Core Build-Up With Pins</span></td>
												<td class="second"><span>$296</span></td>
												<td class="third"><span>$196</span></td>
												<td class="fourth"><span>66%</span></td>
											</tr>
											<tr>
												<td class="first"><span>Root Canal Treatment - Front Tooth</span></td>
												<td class="second"><span>$826</span></td>
												<td class="third"><span>$294</span></td>
												<td class="fourth"><span>64%</span></td>
											</tr>
											<tr>
												<td class="first"><span>Root Canal Treatment - Bicuspid</span></td>
												<td class="second"><span>$967</span></td>
												<td class="third"><span>$619</span></td>
												<td class="fourth"><span>64%</span></td>
											</tr>
											<tr>
												<td class="first"><span>Root Canal Treatment - Molar</span></td>
												<td class="second"><span>$1,170</span></td>
												<td class="third"><span>$438</span></td>
												<td class="fourth"><span>63%</span></td>
											</tr>
											<tr>
												<td class="first"><span>Perio Scaling and Root Planing (Per Quadrant)</span></td>
												<td class="second"><span>$271</span></td>
												<td class="third"><span>$102</span></td>
												<td class="fourth"><span>62%</span></td>
											</tr>
											<tr>
												<td class="first"><span>Full Upper Denture</span></td>
												<td class="second"><span>$1,855</span></td>
												<td class="third"><span>$643</span></td>
												<td class="fourth"><span>65%</span></td>
											</tr>
											<tr>
												<td class="first"><span>Single Tooth Removal - Simple Extraction</span></td>
												<td class="second"><span>$197</span></td>
												<td class="third"><span>$55</span></td>
												<td class="fourth"><span>72%</span></td>
											</tr>
										</table>
										<p>Members may take advantage of savings offered by an industry leader in dental care. Careington International Corporation is one of the most recognized professional dental networks in the nation and boasts a provider network of over 148,000 dental access points.</p>
									</div>
								</div>
								<!-- Savings End -->
								
							</div>
						</div>

				</div>
			</div>
			
			<div id="main-home">
				<div class="holder clearfix">
					<div class="tabletitle">Enroll now to get 3 months of service for <em>FREE!</em></div>
				</div>
			</div>	
		
			<!-- CAREINGTON TABLE PROVIDER-->
			<div class="modal fade" id="careington_providers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog" >
				<div class="modal-content">
				  <div class="modal-body">
				  
							<center>
								<img src="'.get_template_directory_uri().'/img/careington.jpg" alt="#" />
								
								<br />
								<a href="/search-plans/join-careington/" type="button" class="btn btn-success btn-lg" >
									Enroll with Careington
								</a>
								
								<br /><br />
								<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
							</center>
							
							<div id="main-home">
								<div class="holder clearfix">
									<div class="tabletitle">Use your Careington membership at <em>ANY</em> of these dentists in '.$GET['zip'].' and <em>Nationwide</em></div>
								</div>
							</div>					
							
							
							<br style="clear:both" />';
									
										// ===================== //
										// PROVIDER TABLE        //
										// ===================== //
										
										$i = 0; // used to hide tables after 5
										
										foreach($providers as $provider){
										
											if($provider->dental_agent == 'careington'){
											
												$i ++;
												
												$dentist_name = strtolower($provider->last_name." ".$provider->first_name);
											
												if($i % 2 == 0){
													$minisearch_evenodd = "minisearch_even";
												}else{
													$minisearch_evenodd = "minisearch_odd";
												}
											
												$html .= '
												
												<div class="row '.$minisearch_evenodd.'">
													<div class="col-md-3 minisearch_row">Dr. '.$dentist_name.'</div>
													<div class="col-md-3 minisearch_row">'.strtolower($provider->center_name).'</div>
													<div class="col-md-3 minisearch_row">'.strtolower($provider->address1).'<br />'.$provider->address2.'</div>
													<div class="col-md-3 minisearch_row">'.formatPhoneNumber($provider->phone).'</div>
												</div>										
												
												';
											
											}
											
										}
										$html .='
					
					<br />
					<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
					<a href="/search-plans/join-careington/" type="button" class="btn btn-success" style="float:right" >Enroll with Careington</a>
					<br style="clear:both" />
					
				  </div>
				</div>
			  </div>
			</div>
					
			<script type="text/javascript">
			
				
				
				jQuery( document ).ready(function() {							
						
						// ===============
						// HOME FLASH TEXT
						// ===============
						
						// Timer
						// --------
						jQuery( document ).ready(function() {
							setInterval(searchFlashText, 2000);
						});		
						
						window.message = 0;
						
						function searchFlashText(){
						
							var messages = [ "<strong>SPECIAL OFFER</strong>: Receive three months <u>FREE</u>!",
											 "Add FIVE additional plan members at no cost",
											 "No Paper work",
											 "Instant Savings",
											 "48 hour activation",
											 "30 Day Money Back Guarantee",
											 "Most Dentists Nationwide"
										 ];
						
							jQuery("#SearchFlashText").fadeOut("fast", function(){
								jQuery("#SearchFlashText").html(messages[window.message]);
							});
							jQuery("#SearchFlashText").fadeIn("fast");
							
							window.message += 1;
							if(window.message >= 7){
								window.message = 0;
							}

						}
						// =============== //
						
					
				});
				
			</script>';
		
		}
		
		
		///////////////////////////////////////////////////////////////////////////////////
		if($AB_SEARCH == 2){
		///////////////////////////////////////////////////////////////////////////////////
		
		$html .= '
	
		<!-- Start of header -->
		<header>
			<div class="holder clearfix">
				<div class="logo"><a href="/"><img src="'.get_template_directory_uri().'/img/logo2.png" alt="logo" /></a></div>
				<div class="slogan"><h2>Dental Plans you can really<br />sink your teeth into</h2></div>
				
				<!-- Customer Service -->
				<div class="scustomerservice">
				
					<div class="toprightmenu clearfix">
						<ul class="clearfix">
							<li><a href="/compare">Compare</a></li>
							<li><a href="/dentist-search">Find Dentists</a></li>
							<li><a href="/faq">FAQ</a></li>
							<li class="last"><a href="/contact">Contact us</a></li>
						</ul>
						<br />
						<div style="text-align:right; font-size:12px; color:#FFF; font-family:arial; padding-right:12px;"><span style="color:#eb1010; font-size:18px; font-weight:bold;">Questions?</span><span style="font-size:14px;">1-888-908-5242</span></div>
					</div><!-- End of toprightmenu -->
				
				</div>
				<div style="clear:both"></div>
				<!-- Customer Service -->
				
				
				<div class="clearfix"></div>
				<ul class="steps clearfix">
					<li class="step1">
						<h3>1</h3>
						<p>Search Dental Discounts</p>
					</li>
					<li class="step2 active">
						<h3>2</h3>
						<p class="twolines">Choose a Dental<br />Discount Plan</p>
					</li>
					<li class="step3">
						<h3>3</h3>
						<p>Enrollment Information</p>
					</li>
				</ul><!-- End of steps -->
				
				
				<!-- <div id="main-home">
					<div class="holder clearfix">
						<div class="tabletitle" style="padding:10px;">We found 2 plans in your area</div>
					</div>
				</div> -->
				
				
				
			</div><!-- End of holder -->
		</header>
		<!-- End of header -->
		
		
		
		
		<!-- AETNA / CAREINGTON -->
		<div id="main-search-results">
			<div class="holder clearfix">
			
			
		<!-- BOOTSTRAP PLANS -->
		<div class="container-fluid"  style="max-width:980px;">
		
		
			<div class="row">
				<div class="col-lg-7">
				
					<div class="row">
						<div class="col-lg-12">
							<h3><center>We found 2 plans in '.$GET['zip'].'</center></h3>
						</div>
					</div>
					
					<div class="row" style="padding:10px;">
						<div class="col-lg-12">
							<img width=50 src="'.get_template_directory_uri().'/img/aetna.jpg" alt="#" /> <span style="font-size:24px; color:#0094c0">Incredible deal at $7.50 a month</span> <span class="btn btn-xs btn-success" data-toggle="modal" data-target="#aetna_details">Show Me</span>
						</div>
					</div>
					
					<div class="row" style="padding:10px;">
						<div class="col-lg-12">
							<img width=50 src="'.get_template_directory_uri().'/img/careington.jpg" alt="#" /> <span style="font-size:24px; color:#0094c0"><strong>Super Saver</strong> rate at $9.83 a month</span> <span class="btn btn-xs btn-success" data-toggle="modal" data-target="#careington_details">Show Me</span>
						</div>
					</div>
					
					
					
					
				</div>
				<div class="col-lg-5">
				
					<div class="row" style="padding:10px;">
						<div class="col-lg-12">
							<center>
								<span style="font-size:24px; color:#0094c0">We found 63 Dentists</span>
								<br />
								<span class="btn btn-success scroll_to_dentist">Show Me Them</span>
							</center>
						</div>
					</div>
					
					
					<div class="row">
						<div class="col-lg-12">
							<center>
								
								<span style="font-size:24px; color:#0094c0">Search by Dentist last name</span>
								
							</center>
						</div>
					</div>
					
					
					<div class="row" >
						<div class="col-lg-offset-3 col-lg-6">
							<center>
								
								<input type="text" class="form-control" id="dentist_search_top_name" />		
								<span class="btn btn-success dentist_search_top">Search</span>
								
							</center>
						</div>
					</div>
					
					
				</div>
			</div>
			
		</div>
		
		
		
		
		<br />
		
		
		
		
		<!-- HOW IT WORKS -->
		<div class="container customwell" style="max-width:980px; padding:20px;">
		
		
				<div class="row">
				
					
					<div class="col-md-6">
					
						<img src="'.get_template_directory_uri().'/img/as-seen-on-gma.png" alt="#"/>
					
						<video  id="americaplayer" class="video-js vjs-default-skin" style="margin-bottom:20px; width:100%; height:auto;"
						  controls preload="auto" width="460" height="260"
						  poster="'.get_template_directory_uri().'/media/discount_club.jpg"
						 >
						 <source src="'.get_template_directory_uri().'/media/goodmorningamerica.mp4" type="video/mp4" />
						 <source src="'.get_template_directory_uri().'/media/goodmorningamerica.webm" type="video/webm" />
						 <source src="'.get_template_directory_uri().'/media/goodmorningamerica.ogv" type="video/ogg" />
						</video>
					
					</div>
				
					<div class="col-md-6">
					
						<div class="row">
							<div class="col-md-12">
								<center><h1 style="color:#0094c0;">How does my Membership work?</h1></center>
							</div>
						</div>
				
					
					
						<div class="row">
						
							<div class="col-md-5">
								<img src="'.get_template_directory_uri().'/img/how_1.png" alt="#"/>
							</div>
							
							<div class="col-md-7">
								<h4 style="color:#75a84c; font-weight:bold;">1) Choose A Dental Plan That\'s Right For You</h4>
								<p>Each plan is a little different. Find a plan that fits your needs!</p>
							</div>
							
						</div>
						
						
						
						<div class="row">
						
							<div class="col-md-5">
								<img src="'.get_template_directory_uri().'/img/how_2.png" alt="#"/>
							</div>
							
							<div class="col-md-7">
								<h4 style="color:#75a84c; font-weight:bold;">2) Gain Access to Dentists at discounted rates</h4>
								<p>Visit any dentist in your plan\'s nationwide dental network and save on every visit.</p>
							</div>
							
						</div>
						
						
						
						<div class="row">
						
							<div class="col-md-5">
								<img src="'.get_template_directory_uri().'/img/how_3.png" alt="#"/>
							</div>
							
							<div class="col-md-7">
								<h4 style="color:#75a84c; font-weight:bold;">3) Save Money with Fixed Prices</h4>
								<p>No matter what your dentist would normally charge for a procedure, you pay the discounted rate.</p>
							</div>
							
						</div>
						
						
						
						<div class="row">
						
							<div class="col-md-5">
								<img src="'.get_template_directory_uri().'/img/how_4.png" alt="#"/>
							</div>
							
							<div class="col-md-7">
								<h4 style="color:#75a84c;  font-weight:bold;">4) Everyone Wins!</h4>
								<p>Dentists get more patients and keep their dental practice running.
								<br />You save lots of money and take care of your teeth.
								<br />No one has to deal with paperwork or postpone needed care.</p>
							</div>
							
						</div>
						
					</div>
					
					
					
				</div>
				
		</div>
		<!-- End HOW IT WORKS -->
		
		
		
		
		
		<div id="main-home">
			<div class="holder clearfix">
				<div class="tabletitle" style="padding:10px;"><img src="'.get_template_directory_uri().'/img/specialoffer.png" alt="#" /> Enroll now to get 3 months of service for <em>FREE!</em></div>
			</div>
		</div>	
		
		
		
		
		
		<!-- BOOTSTRAP PLANS -->
		<div class="container-fluid"  style="max-width:980px;">
		
			<div class="row">
			
				<div class="col-lg-5 customwell bootstrap_plan">
				
						<div class="logo"><img src="'.get_template_directory_uri().'/img/aetna.jpg" alt="#" /></div>
						<div class="title">'.$plantype .' Plan: $7.50 a month</div>
						<div class="desc"><center>Serviced By Aetna. In most instances***, you can save 15% to 70% on many dental procedures.</center></div>
						<ul class="clearfix">
							<li>Add one additional member for free!</li>
							<li>130,000+ dentists nation wide</li>
							<li>No paper work hassles</li>
							<li>48 Hour activation</li>
							<li>30 Day money back guarantee</li>
							<li>3 Months FREE on signup!</li>							
						</ul>
						<a href="#" class="selectplan" data-toggle="modal" data-target="#aetna_details">Select this plan</a>
						<center style="margin:15px; margin-bottom:5px;">
							<div class="btn btn-info scroll_to_dentist">View Dentists in '.$GET['zip'].' </div> <!-- data-toggle="modal" data-target="#aetna_providers" -->
							<a href="/search-plans/join-aetna/">
								<div class="btn btn-success">Enroll Now!</div>
							</a>
						</center>
						
				</div>
				
				
				<div class="col-lg-5  customwell bootstrap_plan">
				
					<div class="logo"><img src="'.get_template_directory_uri().'/img/careington.jpg" alt="#" /></div>
						<div class="title">'.$plantype .' Plan: $9.83 a month</div>
						<div class="desc"><center>Save 20% to 70%  on many dental procedures.</br>&nbsp;</center></div>
						<ul class="clearfix">
							<li>Add up to <strong>five</strong> additional members for free!</li>
							<li>180,000+ dentists nation wide</li>
							<li>No paper work hassles</li>
							<li>48 Hour activation</li>
							<li>30 Day money back guarantee</li>
							<li>3 Months FREE on signup!</li>	
						</ul>
						<a href="#" class="selectplan" data-toggle="modal" data-target="#careington_details">Select this plan</a>
						<center style="margin:15px; margin-bottom:5px;">
							<div class="btn btn-info scroll_to_dentist" >View Dentists in '.$GET['zip'].' </div>	<!--  data-toggle="modal" data-target="#careington_providers" -->
							<a href="/search-plans/join-careington">
								<div class="btn btn-success">Enroll Now!</div>
							</a>
						</center>
					
				</div>
			
			</div>
		
		</div>
		<!-- BOOTSTRAP PLANS -->
		
		
		
		
		
		
		


		<!-- AETNA DENTIST POPUPS -->
		<div class="modal fade" id="aetna_providers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog" >
			<div class="modal-content">
			  <div class="modal-body">
			  
						<center>
							<img src="'.get_template_directory_uri().'/img/aetna.jpg" alt="#" />
							
							<br />
							<a href="/search-plans/join-aetna/" type="button" class="btn btn-success btn-lg" >
								Enroll with Aetna
							</a>
							
							<br /><br />
							<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
						</center>
						
						<div id="main-home">
							<div class="holder clearfix">
								<div class="tabletitle">Use your Aetna membership at <em>ANY</em> of these dentists in '.$GET['zip'].' and <em>Nationwide</em></div>
							</div>
						</div>					
						
						
						<br style="clear:both" />';
								
									// ===================== //
									// PROVIDER TABLE        //
									// ===================== //
									$i = 0;
									foreach($providers as $provider){
																			
										if($provider->dental_agent == 'aetna'){
										
											$i ++;
										
											$dentist_name = strtolower($provider->first_name." ".$provider->last_name);
										
											if($i % 2 == 0){
												$minisearch_evenodd = "minisearch_even";
											}else{
												$minisearch_evenodd = "minisearch_odd";
											}
										
											$html .= '
											
											<div class="row '.$minisearch_evenodd.'">
												<div class="col-md-3 minisearch_row">Dr. '.$dentist_name.'</div>
												<div class="col-md-3 minisearch_row">'.strtolower($provider->center_name).'</div>
												<div class="col-md-3 minisearch_row">'.strtolower($provider->address1).'<br />'.$provider->address2.'</div>
												<div class="col-md-3 minisearch_row">'.formatPhoneNumber($provider->phone).'</div>
											</div>										
											
											';
										
										}
										
									}
									$html .='
				
				<br />
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				<a href="/search-plans/join-aetna/" type="button" class="btn btn-success" style="float:right" >Enroll with Aetna</a>
				<br style="clear:both" />
				
			  </div>
			</div>
		  </div>
		</div>

		
			
			
			
		<!-- CAREINGTON TABLE PROVIDER-->
		<div class="modal fade" id="careington_providers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog" >
			<div class="modal-content">
			  <div class="modal-body">
			  
						<center>
							<img src="'.get_template_directory_uri().'/img/careington.jpg" alt="#" />
							
							<br />
							<a href="/search-plans/join-careington/" type="button" class="btn btn-success btn-lg" >
								Enroll with Careington
							</a>
							
							<br /><br />
							<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
						</center>
						
						<div id="main-home">
							<div class="holder clearfix">
								<div class="tabletitle">Use your Careington membership at <em>ANY</em> of these dentists in '.$GET['zip'].' and <em>Nationwide</em></div>
							</div>
						</div>					
						
						
						<br style="clear:both" />';
								
									// ===================== //
									// PROVIDER TABLE        //
									// ===================== //
									$i = 0;
									foreach($providers as $provider){
									
										if($provider->dental_agent == 'careington'){
										
											$i++;
									
											$dentist_name = strtolower($provider->first_name." ".$provider->last_name);
										
											if($i % 2 == 0){
												$minisearch_evenodd = "minisearch_even";
											}else{
												$minisearch_evenodd = "minisearch_odd";
											}
										
											$html .= '
											
											<div class="row '.$minisearch_evenodd.'">
												<div class="col-md-3 minisearch_row">Dr. '.$dentist_name.'</div>
												<div class="col-md-3 minisearch_row">'.strtolower($provider->center_name).'</div>
												<div class="col-md-3 minisearch_row">'.strtolower($provider->address1).'<br />'.$provider->address2.'</div>
												<div class="col-md-3 minisearch_row">'.formatPhoneNumber($provider->phone).'</div>
											</div>										
											
											';
										
										}
										
									}
									$html .='
				
				<br />
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				<a href="/search-plans/join-careington/" type="button" class="btn btn-success" style="float:right" >Enroll with Careington</a>
				<br style="clear:both" />
				
			  </div>
			</div>
		  </div>
		</div>
			
		
		
		
		
		
		
		
	
		<!-- FIND YOUR DENTIST -->
		';
		


		
		$dentists = $wpdb->get_results("SELECT * FROM X_providers_full WHERE zip ='".$GET['zip']."' ORDER BY last_name ");
	

		$html .= '
		<div id="main-home" class="scrolled_to_dentist">
			<div class="holder clearfix">
				<div class="tabletitle" style="padding:10px; margin-bottom:20px;">Looking for your Dentist?</div>
			</div>
		</div>
		
		
		<div class="container" style="margin-bottom:20px;">
			<div class="row">
			
				<div class="col-lg-4 col-md-4 col-sm-4 col-lg-offset-4 col-md-offset-4 col-sm-offset-4">
					<div class="form-group">
						<center>Type your Dentists Last Name</center>
						<input id="dentist_last_name" type="email" class="form-control" placeholder="">
					</div>				
				</div>
				
			</div>
			
			<div class="row">
				<div class="col-lg-4 col-md-4 col-sm-4 col-lg-offset-4 col-md-offset-4 col-sm-offset-4">
					<input id="dentist_search" type="button" class="btn btn-success btn-block" value="Find My Dentist" />
				</div>
			</div>
		</div>
		
		';
		
		
		
		
			$html .= '
			
					<div id="dentist_search_area">
					
					<div id="main-home" class="scrolled_to_dentist">
						<div class="holder clearfix">
							<div class="tabletitle" style="padding:10px; margin-bottom:20px;">Showing Dentists found in zip: '. $GET['zip'] .'</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-1 minisearch_row" style="font-size:16px; font-weight:bold;" >Plan</div>
						<div class="col-md-1 minisearch_row" style="font-size:16px; font-weight:bold;" >Zip</div>
						<div class="col-md-2 minisearch_row" style="font-size:16px; font-weight:bold;" >Dentists Name</div>
						<div class="col-md-2 minisearch_row" style="font-size:16px; font-weight:bold;" >Centre Name</div>
						<div class="col-md-2 minisearch_row" style="font-size:16px; font-weight:bold;" >Address</div>
						<div class="col-md-2 minisearch_row" style="font-size:16px; font-weight:bold;" >Phone Number</div>
						<div class="col-md-2 minisearch_row" style="font-size:16px; font-weight:bold;" ></div>
					</div>		
					
					';
			
					// ===================== //
					// PROVIDER TABLE        //
					// ===================== //
					
					$i = 0; // used to hide tables after 5
					
					foreach($dentists as $provider){
					
							if($provider->dental_agent == "aetna") {
								$provider_image = '<img width=40 height=40 src="'.get_template_directory_uri().'/img/aetna.jpg" alt="logo" />';
								$modal = '<div class="btn btn-primary" data-toggle="modal" data-target="#aetna_details"><center>View Plan Savings</center></div>';
							}
							if($provider->dental_agent == "careington") {
								$provider_image = '<img width=40 height=40 src="'.get_template_directory_uri().'/img/careington.jpg" alt="logo" />';
								$modal = '<div class="btn btn-primary" data-toggle="modal" data-target="#careington_details"><center>View Plan Savings</center></div>';
							}
						
							$i ++;
							
							$dentist_name = strtolower($provider->first_name." ".$provider->last_name);
						
							if($i % 2 == 0){
								$minisearch_evenodd = "minisearch_even";
							}else{
								$minisearch_evenodd = "minisearch_odd";
							}
							
							$show_more = 0;
							if($i > 10){
								$hidden_result = " style='display:none' ";
								$show_more = 1;
							}
						
							$html .= '
							
							<div class="row '.$minisearch_evenodd.'" '.$hidden_result.'>
								<div class="col-md-1 minisearch_row">'.$provider_image.'</div>
								<div class="col-md-1 minisearch_row">'.$provider->zip.'</div>
								<div class="col-md-2 minisearch_row">Dr. '.$dentist_name.'</div>
								<div class="col-md-2 minisearch_row">'.strtolower($provider->center_name).'</div>
								<div class="col-md-2 minisearch_row">'.strtolower($provider->address1).'<br />'.$provider->address2.'</div>
								<div class="col-md-2 minisearch_row">'.formatPhoneNumber($provider->phone).'</div>
								<div class="col-md-2 minisearch_row">'.$modal.'</div>
							</div>										
							
							';
						
						
									
						
					}
		
			if($show_more == 1){
				$html .= "<center><div id='show_all_dentists' class='btn btn-large btn-success'>Show All Results</div></center>";
			}
		
		
			$html .= '</div>
			<!-- End FIND YOUR DENTIST-->
				
				
				
				
				
			
				
				<script type="text/javascript">
				
					
					
					jQuery( document ).ready(function() {		

							// DENTIST TOP SEARCH
							// -----------------
							jQuery(".dentist_search_top").click(function() {
								
								jQuery("#dentist_last_name").val( jQuery("#dentist_search_top_name").val() ); 
							
								jQuery("html, body").animate({
									scrollTop: $(".scrolled_to_dentist").offset().top
								}, 2000);
								
								jQuery("#dentist_search").click();
								
							});
							
							
							
							// SCROLL TO DENTIST
							// -----------------
							jQuery(".scroll_to_dentist").click(function() {
								jQuery("html, body").animate({
									scrollTop: $(".scrolled_to_dentist").offset().top
								}, 2000);
							});
							
							
							// Show All Dentists
							// -----------------
							jQuery("#show_all_dentists").click(function() {
								$("#dentist_search_area > row ").each(function() {
									jQuery("#dentist_search_area > row").css("display");
								});
							});
							
							
							
							
							
							// DENTIST SEARCH
							// --------------
							jQuery("#dentist_search").click(function() {
								var spaces = /\s/g.test(jQuery("#dentist_last_name").val());
								var comma = /\,/g.test(jQuery("#dentist_last_name").val());
								
								if(spaces == false && comma == false && jQuery("#dentist_last_name").val() != ""){
									valid = "VALID";
									
									
									jQuery("#dentist_search_area").html("<center><h1>Searching</h1><p>Please Wait ... </p></center>");
									
									
									// Ajax Request
									jQuery.ajax({
									  type: "POST",
									  url: "/search-plans/dentist-search-api/", 
									  data: "lastname="+jQuery("#dentist_last_name").val()+"&zip="+'.$GET['zip'].',
									  success: function(data) {
										jQuery("#dentist_search_area").html(data);
									  },
									  dataType: "html"
									});
									
									
								} else {
									valid = "INVALID";
									alert("Please enter ONLY your Dentists LAST NAME");
								}
								
							});
					
							
							// ===============
							// HOME FLASH TEXT
							// ===============
							
							// Timer
							// --------
							jQuery( document ).ready(function() {
								setInterval(searchFlashText, 2000);
							});		
							
							window.message = 0;
							
							function searchFlashText(){
							
								var messages = [ "<strong>SPECIAL OFFER</strong>: Receive three months <u>FREE</u>!", 
												 "Add ONE additional plan members at no cost with Aetna Dental Access",
												 "Add FIVE additional plan members at no cost with Careington",
												 "<strong>View Plan Savings</strong> for sample list of savings!",
												 "Instant Savings",
												 "48 hour activation",
												 "30 Day Money Back Guarantee",
												 "Start Saving with our Nationwide Plans!"
											 ];
							
								jQuery("#SearchFlashText").fadeOut("fast", function(){
									jQuery("#SearchFlashText").html(messages[window.message]);
								});
								
								jQuery("#SearchFlashText").fadeIn("fast");
								
								window.message += 1;
								if(window.message >= 8){
									window.message = 0;
								}

							}
							// =============== //
							
						
					});
					
				</script>
				
				
			</div><!-- End of holder -->
		</div>
		<!-- End of main-search-results -->';
		
		}
		
		
		///////////////////////////////////////////////////////////////////////////////////
		// SPLIT TEST 3 
		// DISABLED 
		// Shows Provider search results
		///////////////////////////////////////////////////////////////////////////////////
		
		if($AB_SEARCH == 3){
		
		$html .= '
	
		<!-- Start of header -->
		<header>
			<div class="holder clearfix">
				<div class="logo"><a href="/"><img src="'.get_template_directory_uri().'/img/logo2.png" alt="logo" /></a></div>
				<div class="slogan"><h2>Dental Plans you can really<br />sink your teeth into</h2></div>
				<div class="clearfix"></div>
				<ul class="steps clearfix">
					<li class="step1">
						<h3>1</h3>
						<p>Search Dental Discounts</p>
					</li>
					<li class="step2 active">
						<h3>2</h3>
						<p class="twolines">Choose a Dental<br />Discount Plan</p>
					</li>
					<li class="step3">
						<h3>3</h3>
						<p>Enrollment Information</p>
					</li>
				</ul><!-- End of steps -->
				<div class="toptext">We\'ve found 2 '.$_GET['group1'].' Plans and <br />'.$dentists_found.' Dentists in: <strong>'.$_GET['zip'].'</strong></div>
			</div><!-- End of holder -->
		</header>
		<!-- End of header -->';
		
		$html .= '
	
		<!-- Start of main-search-results -->
		<div id="main-search-results">
			<div class="holder clearfix">
			
				<ul class="plan-items clearfix">
					<li class="item item1">
						<div class="logo"><img src="'.get_template_directory_uri().'/img/aetna.jpg" alt="#" /></div>
						<div class="title">'.$plantype .' Plan: $7.50 a month</div>
						<div class="desc">Serviced By Aetna. In most instances***, you can save 15% to 70% on everything from:</div>
						<ul class="clearfix">
							<li>Root Canals</li>
							<li>General Dentistry</li>
							<li>Crowns</li>
							<li>Cleanings</li>
							<li>Orthodontia</li>
						</ul>
						<a href="#" class="selectplan" data-toggle="modal" data-target="#aetna_details">Select this plan</a>
					</li>					
					<li class="item item2">
						<div class="logo"><img src="'.get_template_directory_uri().'/img/careington.jpg" alt="#" /></div>
						<div class="title">'.$plantype .' Plan: $7.50 a month</div>
						<div class="desc">Save 20% to 70% on most procedures including:</div>
						<ul class="clearfix">
							<li>Dentures</li>
							<li>Routine Oral Exams</li>
							<li>Root Canals</li>
							<li>Unlimited Cleanings</li>
							<li>Crowns</li>
						</ul>
						<a href="#" class="selectplan" data-toggle="modal" data-target="#careington_details">Select this plan</a>
					</li>
				</ul><!-- End of plan-items -->
				
				<div class="searchbox clearfix">
					<h3>Search Dentists and Plans in Your Area:</h3>
					<input id="search" type="text" placeholder="Search by Dentist Name" />
				</div><!-- End of searchbox -->
	
				<style>
					.provider_hidden {display:none;}
				</style>
	
				<div class="resultsbox">
					<table id="providersTable">
					
						<tr class="head">
							<th class="first">Provider</th>
							<th class="second">Dentists Name</th>
							<th class="third">Center Name</th>
							<th class="third">Address</th>
							<th class="fourth">Phone</th>
						</tr>
					
						';
					
						// ===================== //
						// PROVIDER TABLE        //
						// ===================== //
						
						$i = 0; // used to hide tables after 5
						$max = 20;
						if($i > $max){
							$hidden_html_class = " class='provider_hidden' ";
						}
						
						
						foreach($providers as $provider){
						
							$hidden_html_class = '';
						
							$i ++;
							if($i > $max){
								$hidden_html_class = " class='provider_hidden' ";
							}
							
							
							if($provider->dental_agent == 'careington'){
								$provider_image = get_template_directory_uri().'/img/careington.jpg';
								$join_plan = 'join-careington';
								$provider_title = "<strong>Care</strong>ington";
								$provider_modal = "#modal-careingtondetails";
								$modal = "careington_details";
								
								$dentist_name = strtolower($provider->first_name." ".$provider->last_name);
							}else{
								$provider_image = get_template_directory_uri().'/img/aetna.jpg';
								$join_plan = 'join-aetna';
								$provider_title = "Aetna Dental Access Plan";
								$provider_modal = "#modal-aetnadetails";
								$modal = "aetna_details";
								
								$dentist_name = strtolower($provider->last_name." ".$provider->first_name);
							}
						
						
							$html .= '
							<tr '.$hidden_html_class.'>
								<td class="first">
									<div class="icon"><img width="50" src="'. $provider_image .'" alt="#" /></div>
									<a class="more" href="#" data-toggle="modal" data-target="#'.$modal.'">More<br />details</a>
								</td>
								<td class="second"><span>Dr. '.$dentist_name.'</span></td>
								<td class="second"><span>'.strtolower($provider->center_name).'</span></td>
								<td class="third"><span>'.strtolower($provider->address1).'<br />'.$provider->address2.'</span></td>
								<td class="fourth"><span>'.formatPhoneNumber($provider->phone).'</span></td>
								
							</tr> ';
							
						}
						$html .='
						</table>
					</div>
					
				<div class="searchbox clearfix" style="float:left">
					<h3>Search Dentists and Plans in Your Area:</h3>
					<input id="search2" type="text" placeholder="Search by Dentist Name" />
				</div><!-- End of searchbox -->
				
				<script type="text/javascript">
				
					function showall() {
						jQuery("#providersTable tr").removeClass("provider_hidden");
						jQuery(".moreresults").hide();						
					}
					
					
					jQuery( document ).ready(function() {
					
							jQuery("#search").keyup(function() {
								jQuery(".moreresults").hide();	
							
								var value = this.value;
								value = value.toLowerCase();

								jQuery("#providersTable").find("tr").each(function(index) {
									if (index === 0) return;
									var id = jQuery(this).find("td").first().next().text().toLowerCase().trim();
									jQuery(this).toggle(id.indexOf(value) !== -1);
								});
							});
							
							jQuery("#search2").keyup(function() {
								jQuery(".moreresults").hide();	
							
								var value = this.value;
								value = value.toLowerCase();

								jQuery("#providersTable").find("tr").each(function(index) {
									if (index === 0) return;
									var id = jQuery(this).find("td").first().next().text().toLowerCase().trim();
									jQuery(this).toggle(id.indexOf(value) !== -1);
								});
							});

						
					});
					
				</script>
				
				<div class="moreresults" style="cursor:pointer !important;" onClick="showall();"><div class="more" >Show More Results</div></div>
				
			</div><!-- End of holder -->
		</div>
		<!-- End of main-search-results -->';
		
		}
		
				
	return $html;

}


/* ----------------
*  HOME PAGE HEADER
*  -------------- */
add_shortcode("providersearch_full_header", "providersearch_full_header_handler");
function providersearch_full_header_handler(){

		global $AB_HOME, $AB_SEARCH, $AB_CHECKOUT;

		///////////////////////////////////////////////////////////////////////////////////
		if($AB_HOME == 1){
		///////////////////////////////////////////////////////////////////////////////////
		$html .= '

				<div class="toprightmenu clearfix">
					<ul class="clearfix">
						<li><a href="/dentist-search">Find Dentists</a></li>
						<li><a href="/faq">FAQ</a></li>
						<li class="last"><a href="/contact">Contact us</a></li>
					</ul>
					<br />
					<div style="text-align:right; font-size:12px; color:#FFF; font-family:arial; padding-right:12px;"><span style="color:#eb1010; font-size:18px; font-weight:bold;">Questions?</span><span style="font-size:14px;">1-888-908-5242</span></div>
				</div><!-- End of toprightmenu -->
				
				<div class="threecols clearfix">
					<div class="col col1">
						<div class="logo"><a href="/"><img src="'.get_template_directory_uri().'/img/logo.png" alt="#" /></a></div>
						<div class="slogan">Dental Plans you can really sink your teeth into</div>
					</div>
					<div class="col col2">
						<div class="findadentist">
							<h2>Find a Plan</h2>
							<form id="searchform" method="get" action="/search-plans">
								<input type="hidden" name="distance" value="1" />
								<div class="zip">
									<span>Your ZIP:</span> <input id="zipsearch" type="text" name="zip"/>
								</div>
								<div class="zip">
									<span>Your Email:</span> <input id="email" type="text" name="email"/>
								</div>
								<div class="radiobtn">
									<div class="radio1"><input type="radio" name="group1" value="Individual"><span>Individual</span></div>
									<div class="radio2"><input type="radio" name="group1" value="Family" checked ><span>Family</span></div>
								</div>
								<div class="submit">
									<button id="searchprovider"></button>
								</div>
							</form>
						</div>
					</div>
					<div class="col col3" >
						<div id="HomeFlashText"><span style="color:#F00">SPECIAL OFFER</span> Sign up today and receive 3 months FREE!</div>						
					</div>
				</div><!-- End of threecols -->
				
				<div class="twocols clearfix">
					<div class="col col1">
						<ul class="clearfix">
							<li><a href="#"><img src="'.get_template_directory_uri().'/img/bbb.png" alt="#" /></a></li>
							<li><a href="#"><img src="'.get_template_directory_uri().'/img/verisign.png" alt="#" /></a></li>
							<li><a href="#"><img src="'.get_template_directory_uri().'/img/pinkribbon.png" alt="#" /></a></li>
							<li><a href="#"><img width=47 src="'.get_template_directory_uri().'/img/careington.jpg" alt="#" /></a></li>
						</ul>
					</div>
					<div class="col col2">
						<img class="arrow1" src="'.get_template_directory_uri().'/img/arrow1.png" alt="#" />
						<h2>Plans as Low as $9.83 Per Month</h2>
						<h3>Hassle Free 30 day money back refund policy</h3>
					</div>
				</div><!-- End of twocols -->
			';}

		
		///////////////////////////////////////////////////////////////////////////////////
		if($AB_HOME == 2){
		///////////////////////////////////////////////////////////////////////////////////
		$html .= '

				<div class="toprightmenu clearfix">
					<ul class="clearfix">
						<li><a href="/compare">Compare</a></li>
						<li><a href="/dentist-search">Find Dentists</a></li>
						<li><a href="/faq">FAQ</a></li>
						<li class="last"><a href="/contact">Contact us</a></li>
					</ul>
					<br />
					<div style="text-align:right; font-size:12px; color:#FFF; font-family:arial; padding-right:12px;"><span style="color:#eb1010; font-size:18px; font-weight:bold;">Questions?</span><span style="font-size:14px;">1-888-908-5242</span></div>
				</div><!-- End of toprightmenu -->
				
				<div class="threecols clearfix">
					<div class="col col1">
						<div class="logo"><a href="/"><img src="'.get_template_directory_uri().'/img/logo.png" alt="#" /></a></div>
						<div class="slogan">Dental Plans you can really sink your teeth into</div>
					</div>
					<div class="col col2">
						<div class="findadentist">
							<h2>Find a Plan</h2>
							<form id="searchform" method="get" action="/search-plans">
								<input type="hidden" name="distance" value="1" />
								<div class="zip">
									<span>Your ZIP:</span> <input id="zipsearch" type="text" name="zip"/>
								</div>
								<div class="zip">
									<span>Your Email:</span> <input id="email" type="text" name="email"/>
								</div>
								<div class="radiobtn">
									<div class="radio1"><input type="radio" name="group1" value="Individual"><span>Individual</span></div>
									<div class="radio2"><input type="radio" name="group1" value="Family" checked ><span>Family</span></div>
								</div>
								<div class="submit">
									<button id="searchprovider"></button>
								</div>
							</form>
						</div>
					</div>
					<div class="col col3" >
						<div id="HomeFlashText"><span style="color:#F00">SPECIAL OFFER</span> Sign up today and receive 3 months FREE!</div>						
					</div>
				</div><!-- End of threecols -->
				
				<div class="twocols clearfix">
					<div class="col col1">
						<ul class="clearfix">
							<li><a href="#"><img src="'.get_template_directory_uri().'/img/bbb.png" alt="#" /></a></li>
							<li><a href="#"><img src="'.get_template_directory_uri().'/img/verisign.png" alt="#" /></a></li>
							<li><a href="#"><img src="'.get_template_directory_uri().'/img/pinkribbon.png" alt="#" /></a></li>
							<li><a href="#"><img src="'.get_template_directory_uri().'/img/product_gif.gif" alt="#" /></a></li>
						</ul>
					</div>
					<div class="col col2">
						<img class="arrow1" src="'.get_template_directory_uri().'/img/arrow1.png" alt="#" />
						<h2>Plans as Low as $7.49 Per Month</h2>
						<h3>Hassle Free 30 day money back refund policy</h3>
					</div>
				</div><!-- End of twocols -->
			';}

return $html;
}		






/* ----------------
*  HOME PAGE HEADER
*  -------------- */
add_shortcode("providersearch_checkout_thankyou", "providersearch_checkout_thankyou_handler");
function providersearch_checkout_thankyou_handler(){

	$member_id = $_GET['mid'];

	$html .= '
	
			<!-- JQUERY CHECKOUT SYSTEM -->
			<div class="panel panel-success">
				
				<div class="well">
					<center>
						<h1 style="font-size:60px;">Thank You!</h1>
						<h2>Your order has been received.</h2>
					</center>
						<ul>
							<li>We have just sent your Welcome Kit Email, please be sure to check your spam folder!</li>
							<li>Check your Email now for your receipt</li>
							<li>Your Membership will be activated within 24-48 hours</li>
							<li>We will mail your <strong>Membership Cards</strong> on the next business day</li>
							<li>Shipping usually takes 5-10 business days</li>
							<li>Please call us at any time to update your account information</li>
							<li>Thank you for shopping with Dental Discount Network!</li>
						</ul>					
					
					<center>
						<a href="http://transcend.webtechnologymedia.ca/userpdf/'.$member_id.'-WelcomeKit.pdf" target="_blank">
							<img height="60" src="'.get_template_directory_uri().'/img/pdfdownload.png" alt="" /><span class="btn btn-lg btn-success">Please Download your Welcome Kit now!</span>
						</a>
						<br />
						<span style="font-size:12px;">(And remember to check your spam folder!)</span>
					</center>
					
				</div>
				
			</div>
			<!-- JQUERY CHECKOUT SYSTEM -->
			
			
			<!-- ------------------- -->
			<!-- Rejoiner Conversion -->
			<!-- ------------------- -->
			
			
			<script type="text/javascript">
			var _rejoiner = _rejoiner || [];
			_rejoiner.push(["setAccount", "52f92fb3281cb75e15afd802"]);
			_rejoiner.push(["setDomain", ".dentaldiscountnetwork.com"]);
			_rejoiner.push(["sendConversion"]);
			(function() {
				var s = document.createElement("script");
				s.type = "text/javascript";
				s.async = true;
				s.src = "https://s3.amazonaws.com/rejoiner/js/v3/t.js";
				var x = document.getElementsByTagName("script")[0];
				x.parentNode.insertBefore(s, x);
			})();
			</script>
			<script type="text/javascript">
			_rejoiner.push(["setCartData", {"customer_order_number": "'.$member_id.'"}]);
			</script>
			
			
			<!-- ----------------------- -->
			<!-- End Rejoiner Conversion -->
			<!-- ----------------------- -->
	
			
	';

	return $html;

}



/* ----------------
*  Dentist Search
*  -------------- */
add_shortcode("providersearch_dentist_search", "providersearch_dentist_search_handler");
function providersearch_dentist_search_handler(){
	
	global $wpdb;	
	$GET = preg_replace( "/[^a-zA-Z0-9\s\p{P}]/", '', $_GET );
	
	// Search For Dentist
	// ------------------
	
		$html .= '
		
			<!-- Start of header -->
			<header>
				<div class="holder clearfix">
					<div class="toptext">Please Enter your Dentists Last Name</div>
				</div><!-- End of holder -->
			</header>
			<!-- End of header -->
			
			<form id="searchForm" class="form-horizontal well" role="form" action="/dentist-search/" method="GET">
			  
				<div class="container">
					<div class="row">
					
						<div class="col-md-4"></div>
						
						<div class="col-md-4">
							<center>
								<div class="form-group">
									<input id="lastname" class="form-control" type="text" name="lastname" placeholder="Last Name" value="'.$GET['lastname'].'" />
								</div>
								<div class="form-group">
									<input id="zip" class="form-control" type="text" name="zipcode" placeholder="Zip Code" value="'.$GET['zip'].'" />
								</div>
								<div class="form-group">
									<div id="submitBtn" class="btn btn-large btn-success" >Search</div>
								</div>
							</center>
						</div>
						
						<div class="col-md-4"></div>
						
					</div>
				</div>
				  
			</form>
			
			<script>
				jQuery("#submitBtn").click( function() {
				
					if( jQuery("#lastname").val() == "") {
						alert("Please enter dentists last name");
						invalid = 1;
					} else {
						jQuery("#searchForm").submit();
					}
				});
			</script>
			
		
		';
	
	
	// Search For Dentist
	// ------------------
	if(isset($GET['lastname'])){
	
		if($GET['zipcode'] != ""){
			$zip_sql = "AND zip = '".$GET['zipcode']."' ";
		}
		
		$dentists = $wpdb->get_results("SELECT * FROM X_providers_full WHERE last_name LIKE '".$GET['lastname']."' ".$zip_sql." ORDER BY zip LIMIT 50");
		if(count($dentists) == 0){

			$html .= '
					<div id="main-home">
						<div class="holder clearfix">
							<div class="tabletitle">Sorry, we didn\'t find '.$GET['lastname'].'... Try Again!</div>
						</div>
					</div>					
					';
		
		} else {
		
			$html .= '
					<div id="main-home">
						<div class="holder clearfix">
							<div class="tabletitle">Dentists Found:</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-1 minisearch_row" style="font-size:16px; font-weight:bold;" >Plan</div>
						<div class="col-md-1 minisearch_row" style="font-size:16px; font-weight:bold;" >Zip</div>
						<div class="col-md-2 minisearch_row" style="font-size:16px; font-weight:bold;" >Dentists Name</div>
						<div class="col-md-2 minisearch_row" style="font-size:16px; font-weight:bold;" >Centre Name</div>
						<div class="col-md-2 minisearch_row" style="font-size:16px; font-weight:bold;" >Address</div>
						<div class="col-md-2 minisearch_row" style="font-size:16px; font-weight:bold;" >Phone Number</div>
						<div class="col-md-2 minisearch_row" style="font-size:16px; font-weight:bold;" ></div>
					</div>		
					
					';
			
					// ===================== //
					// PROVIDER TABLE        //
					// ===================== //
					
					$i = 0; // used to hide tables after 5
					
					foreach($dentists as $provider){
					
							if($provider->dental_agent == "aetna") {
								$provider_image = '<img width=40 height=40 src="'.get_template_directory_uri().'/img/aetna.jpg" alt="logo" />';
								$modal = '<div class="btn btn-primary" data-toggle="modal" data-target="#aetna_details"><center>View Plan Savings</center></div>';
							}
							if($provider->dental_agent == "careington") {
								$provider_image = '<img width=40 height=40 src="'.get_template_directory_uri().'/img/careington.jpg" alt="logo" />';
								$modal = '<div class="btn btn-primary" data-toggle="modal" data-target="#careington_details"><center>View Plan Savings</center></div>';
							}
						
							$i ++;
							
							$dentist_name = strtolower($provider->first_name." ".$provider->last_name);
						
							if($i % 2 == 0){
								$minisearch_evenodd = "minisearch_even";
							}else{
								$minisearch_evenodd = "minisearch_odd";
							}
						
							$html .= '
							
							<div class="row '.$minisearch_evenodd.'">
								<div class="col-md-1 minisearch_row">'.$provider_image.'</div>
								<div class="col-md-1 minisearch_row">'.$provider->zip.'</div>
								<div class="col-md-2 minisearch_row">Dr. '.$dentist_name.'</div>
								<div class="col-md-2 minisearch_row">'.strtolower($provider->center_name).'</div>
								<div class="col-md-2 minisearch_row">'.strtolower($provider->address1).'<br />'.$provider->address2.'</div>
								<div class="col-md-2 minisearch_row">'.formatPhoneNumber($provider->phone).'</div>
								<div class="col-md-2 minisearch_row">'.$modal.'</div>
							</div>										
							
							';
						
						
									
				
			}
		
		}
	
	}
	

	return $html;

}



/* ----------------
*  Dentist Search API
*  -------------- */
add_shortcode("providersearch_dentist_search_api", "providersearch_dentist_search_api_handler");
function providersearch_dentist_search_api_handler(){
	
	global $wpdb;
		
	
		$dentists = $wpdb->get_results("SELECT * FROM X_providers_full WHERE last_name LIKE '".$_POST['lastname']."' AND zip = '".$_POST['zip']."' LIMIT 10 ");
		if(count($dentists) == 0){
			
			
			// LOOK IN SURROUNDING ZIP CODES FOR DENTST
			// EXAMPLE: ACCROSS THE ROAD? DOWN THE STREET? 1 ZIP CODE OVER
			// ------------------------------------------------------------
			$QUERY1 = $wpdb->get_results( "SELECT * FROM x_zip_codes WHERE zip = '".$_POST['zip']."'" );
			foreach($QUERY1 as $data){
				$lat1 = $data->latitude;
				$lon1 = $data->longitude;
				$state = $data->state;
				
				$found_results = 1;
			}
			
			$zipcodes = zipcodeRadius($lat1, $lon1, $distance);
			
			$sql_providers = 'SELECT * FROM X_providers_full WHERE ';
			foreach($zipcodes as $zipcode){
				$sql_providers .= "zip = '".$zipcode."' AND last_name LIKE '%".$_POST['lastname']."%' OR " ;
			}
			
			$sql_providers = substr($sql_providers, 0, -4); // Strip the last OR 
			$sql_providers .= ' ORDER BY address1 LIMIT 40';
			// var_dump($sql_providers);
			// $sql_providers .= ' LIMIT 50';
			
			
			$dentists = $wpdb->get_results($sql_providers);
			
			
			// DENTISTS WERE FOUND
			// -------------------
			if(count($dentists) > 0 ) {
			
			}
			
			// NONE FOUND, SHOW DEFAULT DENTISTS
			// -------------------
			if(count($dentists) == 0 ) {
			$html .= '<div id="main-home">
						<div class="holder clearfix">
							<div class="tabletitle">We didn\'t find Dr. '.$_POST['lastname'].'... <br />Showing ALL dentists in your zip code</div>
						</div>
					</div>';
					
					$dentists = $wpdb->get_results("SELECT * FROM X_providers_full WHERE zip = '".$_POST['zip']."' ORDER BY last_name");
			}
			
			$html .= '

				<div class="row">
					<div class="col-md-1 minisearch_row" style="font-size:16px; font-weight:bold;" >Plan</div>
					<div class="col-md-1 minisearch_row" style="font-size:16px; font-weight:bold;" >Zip</div>
					<div class="col-md-2 minisearch_row" style="font-size:16px; font-weight:bold;" >Dentists Name</div>
					<div class="col-md-2 minisearch_row" style="font-size:16px; font-weight:bold;" >Centre Name</div>
					<div class="col-md-2 minisearch_row" style="font-size:16px; font-weight:bold;" >Address</div>
					<div class="col-md-2 minisearch_row" style="font-size:16px; font-weight:bold;" >Phone Number</div>
					<div class="col-md-2 minisearch_row" style="font-size:16px; font-weight:bold;" ></div>
				</div>
				
				';
		
				// ===================== //
				// PROVIDER TABLE        //
				// ===================== //
				
				$i = 0; // used to hide tables after 5
				
				foreach($dentists as $provider){
				
					if($provider->dental_agent == "aetna") {
						$provider_image = '<img width=40 height=40 src="'.get_template_directory_uri().'/img/aetna.jpg" alt="logo" />';
						$modal = '<div class="btn btn-primary" data-toggle="modal" data-target="#aetna_details"><center>View Plan Savings</center></div>';
					}
					if($provider->dental_agent == "careington") {
						$provider_image = '<img width=40 height=40 src="'.get_template_directory_uri().'/img/careington.jpg" alt="logo" />';
						$modal = '<div class="btn btn-primary" data-toggle="modal" data-target="#careington_details"><center>View Plan Savings</center></div>';
					}
				
					$i ++;
					
					$dentist_name = strtolower($provider->first_name." ".$provider->last_name);
				
					if($i % 2 == 0){
						$minisearch_evenodd = "minisearch_even";
					}else{
						$minisearch_evenodd = "minisearch_odd";
					}
				
					$html .= '
					
					<div class="row '.$minisearch_evenodd.'">
						<div class="col-md-1 minisearch_row">'.$provider_image.'</div>
						<div class="col-md-1 minisearch_row">'.$provider->zip.'</div>
						<div class="col-md-2 minisearch_row">Dr. '.$dentist_name.'</div>
						<div class="col-md-2 minisearch_row">'.strtolower($provider->center_name).'</div>
						<div class="col-md-2 minisearch_row">'.strtolower($provider->address1).'<br />'.$provider->address2.'</div>
						<div class="col-md-2 minisearch_row">'.formatPhoneNumber($provider->phone).'</div>
						<div class="col-md-2 minisearch_row">'.$modal.'</div>
					</div>										
					
					';
				}
			
		
		} else {
		
			$html .= '

					<div class="row">
						<div class="col-md-1 minisearch_row" style="font-size:22px; font-weight:bold;" >Plan</div>
						<div class="col-md-1 minisearch_row" style="font-size:22px; font-weight:bold;" >Zip Code</div>
						<div class="col-md-2 minisearch_row" style="font-size:22px; font-weight:bold;" >Dentists Name</div>
						<div class="col-md-2 minisearch_row" style="font-size:22px; font-weight:bold;" >Centre Name</div>
						<div class="col-md-2 minisearch_row" style="font-size:22px; font-weight:bold;" >Address</div>
						<div class="col-md-2 minisearch_row" style="font-size:22px; font-weight:bold;" >Phone Number</div>
						<div class="col-md-2 minisearch_row" style="font-size:22px; font-weight:bold;" ></div>
					</div>
					
					';
			
					// ===================== //
					// PROVIDER TABLE        //
					// ===================== //
					
					$i = 0; // used to hide tables after 5
					
					foreach($dentists as $provider){
					
						if($provider->dental_agent == "aetna") {
							$provider_image = '<img width=40 height=40 src="'.get_template_directory_uri().'/img/aetna.jpg" alt="logo" />';
							$modal = '<div class="btn btn-primary" data-toggle="modal" data-target="#aetna_details"><center>View Plan Savings</center></div>';
						}
						if($provider->dental_agent == "careington") {
							$provider_image = '<img width=40 height=40 src="'.get_template_directory_uri().'/img/careington.jpg" alt="logo" />';
							$modal = '<div class="btn btn-primary" data-toggle="modal" data-target="#careington_details"><center>View Plan Savings</center></div>';
						}
					
						$i ++;
						
						$dentist_name = strtolower($provider->first_name." ".$provider->last_name);
					
						if($i % 2 == 0){
							$minisearch_evenodd = "minisearch_even";
						}else{
							$minisearch_evenodd = "minisearch_odd";
						}
					
					
						$html .= '
						
						<div class="row '.$minisearch_evenodd.'" '.$hidden_result.'>
							<div class="col-md-1 minisearch_row">'.$provider_image.'</div>
							<div class="col-md-1 minisearch_row">'.$provider->zip.'</div>
							<div class="col-md-2 minisearch_row">Dr. '.$dentist_name.'</div>
							<div class="col-md-2 minisearch_row">'.strtolower($provider->center_name).'</div>
							<div class="col-md-2 minisearch_row">'.strtolower($provider->address1).'<br />'.$provider->address2.'</div>
							<div class="col-md-2 minisearch_row">'.formatPhoneNumber($provider->phone).'</div>
							<div class="col-md-2 minisearch_row">'.$modal.'</div>
						</div>										
						
						';
						
				
					}
		
		}
	
	return $html;
	
	
}























?>