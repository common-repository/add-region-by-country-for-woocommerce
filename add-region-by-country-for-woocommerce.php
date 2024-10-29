<?php
/*
Plugin Name: Add Region by Country for Woocommerce
Plugin URI: https://www.c-metric.com/
Description: We can able to add multiple custom Region by Country for Woocommerce
Version: 1.0.4
Author: C-Metric
Author URI: https://www.c-metric.com/
Text Domain: cmetric-arbyw
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! defined( 'WC_CMETRIC_ARBYW_DB_VERSION' ) ) {
    define( 'WC_CMETRIC_ARBYW_DB_VERSION', '1.0.4' );
}
if( ! defined( 'WC_CMETRIC_ARBYW_PLUGIN_FILE' )){
	define( 'WC_CMETRIC_ARBYW_PLUGIN_FILE', plugin_basename( __FILE__ ) );
}
if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}
// include all required files here
require_once('cmetric_arbyw_class.php');

/**
 * Get it Started
*/
$GLOBALS['WC_Cmetric_Arbyw'] = new WC_Cmetric_Arbyw();	
?>