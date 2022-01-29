<?php
/**
 * Plugin Name: Order Reviews for WooCommerce
 * Plugin URI: https://jompha.com
 * Description: A review and feedback capture plugin for WooCommerce. Powered by Jompha.
 * Version: 1.0.0
 * Author: Jompha
 * Author URI: https://jompha.com/
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 4.0
 * Tested up to: 5.8
 * WC requires at least: 4.0
 * WC tested up to: 5.5.2
 * 
 * Text Domain: order-reviews-for-woocommerce
 * Domain Path: /languages
 * 
 * @package ORFW
 * @author Jompha
 */

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) )
    exit();

require_once __DIR__ . '/ORFW.php';
\ORFW::getInstance();
