<?php
/**
 * Plugin Name:       Product Subtitles for WooCommerce
 * Plugin URI:        https://spiderwares.com/producttagline/
 * Description:       Enhance your WooCommerce store with the Product Subtitle for WooCommerce plugin. Add customizable, dynamic subtitles to your products to highlight key features, promotions, and unique selling points
 * Version:           1.2.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            SpiderWares
 * Author URI:        https://spiderwares.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Requires Plugins   woocommerce
 * Text Domain:       product-subtitle-for-woocommerce
 *
 * @package Product Subtitle for WooCommerce
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'PSWC_FILE' ) ) :
	define( 'PSWC_FILE', __FILE__ ); // Define the plugin file path.
endif;

if ( ! defined( 'PSWC_BASENAME' ) ) :
	define( 'PSWC_BASENAME', plugin_basename( PSWC_FILE ) ); // Define the plugin basename.
endif;

if ( ! defined( 'PSWC_VERSION' ) ) :
	define( 'PSWC_VERSION', '1.2.0' ); // Define the plugin version.
endif;

if ( ! defined( 'PSWC_PATH' ) ) :
	define( 'PSWC_PATH', plugin_dir_path( __FILE__ ) ); // Define the plugin directory path.
endif;

if ( ! defined( 'PSWC_URL' ) ) :
	define( 'PSWC_URL', plugin_dir_url( __FILE__ ) ); // Define the plugin directory URL.
endif;

if ( ! defined( 'PSWC_UPGRADE_URL' ) ) :
	define( 'PSWC_UPGRADE_URL', 'https://www.spiderwares.com/' ); // Define the upgrade URL.
endif;

if ( ! class_exists( 'Product_Subtitle', false ) ) :
	include_once PSWC_PATH . '/includes/class-product-subtitle.php';
endif;

if ( ! function_exists( 'PSWC' ) ) :
	/**
	 * Function to initalize .
	 */
	function PSWC() {
		return Product_Subtitle::instance();
	}

	$GLOBALS['wpbookit'] = PSWC();
endif;
