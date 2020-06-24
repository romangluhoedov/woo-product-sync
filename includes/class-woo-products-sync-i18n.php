<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link        
 * @since      1.0.0
 *
 * @package    Woo_Products_Sync
 * @subpackage Woo_Products_Sync/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Woo_Products_Sync
 * @subpackage Woo_Products_Sync/includes
 * @author     nongkuschoolubol <notify@nongkuschoolubol.com>
 */
class Woo_Products_Sync_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woo-products-sync',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
