<?php
/*
Plugin Name: Methi Search
Version: 0.1
Plugin URI: https://github.com/yashshah/methi-wp
Description: Methi is an awesome realtime, mobileview optimized search for your wordpress blog, powered by appbase.io.
Author: Yash Shah
Author URI: https://twitter.com/yashshah
*/

// For debugging purposes
//error_reporting(E_ALL);
//ini_set("display_errors", 1); 
//define('WP-DEBUG', true);

if (!defined('ABSPATH')) {
	die('script kiddies, f*ck off');
}

global $pluginUrl,$pluginDir;

$pluginDir=dirname(__FILE__)."/";
$pluginUrl=WP_CONTENT_URL."/plugins/".  basename($pluginDir)."/";

include_once 'methi-class-search.php';

define('METHI_VERSION', '0.1');

?>
