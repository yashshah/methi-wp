<?php
/*
Plugin Name: Methi Search
Version: 0.1
Plugin URI: https://github.com/siddharthlatest/methi-wp
Description: Methi is an awesome realtime, mobileview optimized search for your wordpress blog, powered by appbase.io.
Author: Siddharth Kothari
Author URI: https://twitter.com/siddharthlatest
*/

if (!defined('ABSPATH')) {
	die('script kiddies, f*ck off');
}
require_once 'methi-index.php';

define('METHI_VERSION', '0.1');

add_action('init', 'add_wpdata_to_appbase');
add_action('publish_post', 'add_wpdata_to_appbase');
?>
