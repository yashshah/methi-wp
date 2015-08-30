<?php
/*
Plugin Name: Methi Search
Version: 0.1
Plugin URI: https://github.com/yashshah/methi-wp
Description: Methi is an awesome realtime, mobileview optimized search for your wordpress blog, powered by appbase.io.
Author: Siddharth Kothari
Author URI: https://twitter.com/yashshah
*/

// For debugging purposes
//error_reporting(E_ALL);
//ini_set("display_errors", 1); 
//define('WP-DEBUG', true);

if (!defined('ABSPATH')) {
	die('script kiddies, f*ck off');
}
require_once 'methi-index.php';

define('METHI_VERSION', '0.1');

add_action('init', 'add_wpdata_to_appbase');
add_action('publish_post', 'add_wpdata_to_appbase');
?>
