<?php
/*
 * Plugin Name: Connector GravityForms Mailerlite
 * Plugin URI: https://github.com/closemarketing/connector-gravityforms-mailerlite
 * Description: Connects GravityForms with MailerLite.
 * Author: closemarketing, davidperez
 * Author URI: https://www.closemarketing.es
 * Version: 1.2
 * 
 * Text Domain: connector-gravityforms-mailerlite
 * 
 * Domain Path: /languages
 * 
 * License: GNU General Public License version 3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

defined( 'ABSPATH' ) || exit;

// Loads translation.
load_plugin_textdomain( 'connector-gravityforms-mailerlite', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

define( 'GF_CGFM_VERSION', '1.2' );

/**
 * Detect plugin WooCommerce Mailerlite
 */
if ( ! is_plugin_active( 'woo-mailerlite/woo-mailerlite.php' ) ) {
	// Plugin is activated.
	require 'vendor/autoload.php';
}

// If Gravity Forms is loaded, bootstrap the Campaign Monitor Add-On.
add_action( 'gform_loaded', array( 'GF_CGFM_Bootstrap', 'load' ), 5 );

/**
 * Class GF_CGFM_Bootstrap
 *
 * Handles the loading of the Campaign Monitor Add-On and registers with the Add-On framework.
 */
class GF_CGFM_Bootstrap {

	/**
	 * If the Feed Add-On Framework exists, Campaign Monitor Add-On is loaded.
	 *
	 * @access public
	 * @static
	 */
	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}

		require_once 'class-gf-mailerlite.php';

		GFAddOn::register( 'GF_CGFM' );

	}

}

/**
 * Returns an instance of the GF_CGFM class
 *
 * @see    GF_CGFM::get_instance()
 *
 * @return object GF_CGFM
 */
function gf_mailerlite() {
	return GF_CGFM::get_instance();
}
