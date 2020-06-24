<?php

/**
 * Fired during plugin activation
 *
 * @link        
 * @since      1.0.0
 *
 * @package    Woo_Products_Sync
 * @subpackage Woo_Products_Sync/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woo_Products_Sync
 * @subpackage Woo_Products_Sync/includes
 * @author     nongkuschoolubol <notify@nongkuschoolubol.com>
 */
class Woo_Products_Sync_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $create_products_table = "CREATE TABLE IF NOT EXISTS ".\nongkuschoolubol\SunlightsuplyApiDB::TABLE_NAME." (
        id int(11) NOT NULL AUTO_INCREMENT,
        wp_post_id int(11) DEFAULT 0,
        product_id int(11) UNIQUE DEFAULT 0,
        product_name varchar(255) DEFAULT '0',
        category_id int(11) DEFAULT 0,
        category_name varchar(255) DEFAULT '0',
        category_web_id varchar(255) DEFAULT '0',
        description text,
        image_thumbnail varchar(255) DEFAULT '0',
        image_medium varchar(255) DEFAULT '0',
        price float DEFAULT 0,
        quantity int(11) DEFAULT 0,
        product_weight float DEFAULT 0,
        product_length float DEFAULT 0,
        product_width float DEFAULT 0,
        product_height float DEFAULT 0,
        family_id int(11) DEFAULT 0,
        family_name varchar(255) DEFAULT '0',
        family_web_id varchar(255) DEFAULT '0',
        case_quantity int(11) DEFAULT 0,
        api_data text,
        PRIMARY KEY  (id)
        ) $charset_collate;";

        $wpdb->query( $create_products_table );

        $create_families_table = "CREATE TABLE IF NOT EXISTS ".\nongkuschoolubol\SunlightsuplyApiDB::TABLE_FAMILIES." (
        id int(11) NOT NULL AUTO_INCREMENT,
        wp_post_id int(11) DEFAULT 0,
        family_id int(11) UNIQUE DEFAULT 0,
        family_name varchar(255) DEFAULT '0',
        family_web_id varchar(255) DEFAULT '0',
        family_description text,
        category_id int(11) DEFAULT 0,
        category_name varchar(255) DEFAULT '0',
        category_web_id varchar(255) DEFAULT '0',
        image_medium varchar(255) DEFAULT '0',
        image_thumbnail varchar(255) DEFAULT '0',
        api_data text,
        PRIMARY KEY  (id)
        ) $charset_collate;";

        $wpdb->query( $create_families_table );
	}

}
