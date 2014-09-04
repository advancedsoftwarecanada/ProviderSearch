<?php


// ----------------
// OPTIONS PAGE
// ----------------
function providersearch_options_page() {

	global $providersearch_options;

		ob_start(); ?>
		<div class="wrap">
			<h1>Provider Search Manager</h1>
			
			<form method='GET' action='options.php'>
			
				<?php settings_fields('providersearch_settings_group'); ?>
								
				<p>Use this button to update Aetna and Careington provider data.</p>
				<p><strong>Make sure to upload new provider data via SFTP.</strong></p>
			
				<p class='submit'>
					<input type='submit' class='button-primary' value='<?php _e('Update Database'); ?>' />
				</p>
			
			</form>
			
		</div>
	<?php
	
	if($_GET['settings-updated'] == 'true'){
		BeginUpdate();
	}
	
}

// ----------------
// OPTIONS LINK
// ----------------
function providersearch_add_options_link() {
	// http://codex.wordpress.org/Adding_Administration_Menus
	// add_options_page('providersearch Manager Options', 'Provider Search', 'manage_options', 'providersearch-options', 'providersearch_options_page'); // Old Way
	add_menu_page('providersearch Manager Options', 'Provider Search', 'manage_options', 'providersearch-options', 'providersearch_options_page');
}
add_action('admin_menu', 'providersearch_add_options_link');

// ----------------
// providersearch SETTINGS
// ----------------
function providersearch_register_settings() {
	register_setting('providersearch_settings_group','providersearch_settings');
}
add_action('admin_init','providersearch_register_settings');







function BeginUpdate(){

	global $wpdb;

	$table_name = "X_providers"; 

	//---------------------
	// CLEAR DATABASE
	//---------------------
	// $wpdb->query("DROP TABLE IF EXISTS $table_name");	
	
	// $sql = "CREATE TABLE $table_name (
	  // id int(50) NOT NULL AUTO_INCREMENT,
	  // provider_id int(20),
	  // last_name VARCHAR(55),
	  // first_name VARCHAR(55),
	  // center_name VARCHAR(55),
	  // address1 VARCHAR(55),
	  // address2 VARCHAR(55),
	  // city VARCHAR(55),
	  // state VARCHAR(55),
	  // zip VARCHAR(5),
	  // phone VARCHAR(55),
	  // languages VARCHAR(55),
	  // dental_agent VARCHAR(55),
	  // UNIQUE KEY id (id)
	// ) CHARACTER SET utf8;";

	// require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	// dbDelta( $sql );
	
	
	//---------------------
	// IMPORT CAREINGTON
	//---------------------
	// POS_import($table_name);	
	// $html .= "<div style='width:auto; background:#0F0; color:#000; border:2px solid #00F;'>CAREINGTON COMPLETE</div>";
	
	//---------------------
	// IMPORT AETNA
	//---------------------
	AETNA_import($table_name);
	$html .= "<div style='width:auto; background:#0F0; color:#000; border:2px solid #00F;'>AETNA COMPLETE</div>";
	
	echo $html;
}



function POS_import($table_name){
	
	global $wpdb;

	$filename = dirname(__FILE__).'/data/careington_2.txt';
	$lines = file($filename, FILE_IGNORE_NEW_LINES);

	foreach($lines as $line){

		// echo $line.'<br /><br /><br />';

		//-----------
		// Data Map
		//-----------
		$record = array(
			PrvID 		 => substr($line,0,9),	
			LName 		 => substr($line,9,25),	
			FName 		 => substr($line,34,15),
			PostName 	 => substr($line,50,4),
			Center 		 => substr($line,53,35),
			Addr1 		 => substr($line,88,35),
			Addr2 		 => substr($line,123,35),
			City 		 => substr($line,158,25),
			State 		 => substr($line,183,2),
			Zip 		 => substr($line,185,9),
			Phone 		 => substr($line,194,10),
			Language	 => substr($line,258,40)
		);
		
		$record = array_filter(array_map('trim', $record));

		// echo '<pre>';
		// var_dump($record);
		// echo '</pre>';
		

		$sql = "INSERT INTO $table_name (
		  provider_id,
		  last_name ,
		  first_name,
		  center_name,
		  address1,
		  address2,
		  city,
		  state,
		  zip,
		  phone,
		  languages,
		  dental_agent
		  ) 
		  VALUES
		  (
		   '".$record['PrvID']."',
		   '".$record['FName']."',
		   '".$record['LName']."',
		   '".$record['Center']."',
		   '".$record['Addr1']."',
		   '".$record['Addr2']."',
		   '".$record['City']."',
		   '".$record['State']."',
		   '".$record['Zip']."',
		   '".$record['Phone']."',
		   '".$record['Language']."',
		   'careington'
		  )
		  ";
		$wpdb->query($sql);	
		
	}	
}


//=====================
// AETNA DATA IMPORT
//=====================
// This file is separated by tabs
// Using Explode() to filter data
// No named fields or column headers!
function AETNA_import($table_name){
	
	global $wpdb;

	$filename = dirname(__FILE__).'/data/aetna.txt';
	$lines = file($filename, FILE_IGNORE_NEW_LINES);

	foreach($lines as $line){


		//-----------
		// Data Map
		//-----------
		
		$record = explode("\t", $line);  

		// echo '<pre>';
		// var_dump($record);
		// echo '</pre>';
		
		$sql = "INSERT INTO $table_name (
		  provider_id,
		  last_name ,
		  first_name,
		  center_name,
		  address1,
		  address2,
		  city,
		  state,
		  zip,
		  phone,
		  languages,
		  dental_agent
		  ) 
		  VALUES
		  (
		   '".$record[1]."',
		   '".$record[3]."',
		   '".$record[4]."',
		   '".$record[11]."',
		   '".$record[6]."',
		   '".$record[7]."',
		   '".$record[8]."',
		   '".$record[9]."',
		   '".$record[10]."',
		   '".$record[12]."',
		   'N/A',
		   'aetna'
		  )
		  ";
		$wpdb->query($sql);	
		
	}	
}




