<?php

// ---------
// SCRIPTS
// ---------

// Load providersearch CSS
function providersearch_load_style() {
	wp_enqueue_style('providersearch-styles', plugin_dir_url(__FILE__).'css/providersearch.css');	
	// wp_register_script( 'tablesearch', plugins_url( '/js/tablesearch.js', __FILE__ ) );  
}
add_action('wp_enqueue_scripts', 'providersearch_load_style');

