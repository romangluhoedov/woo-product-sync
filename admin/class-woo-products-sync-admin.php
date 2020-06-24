<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link        
 * @since      1.0.0
 *
 * @package    Woo_Products_Sync
 * @subpackage Woo_Products_Sync/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Products_Sync
 * @subpackage Woo_Products_Sync/admin
 * @author     nongkuschoolubol <notify@nongkuschoolubol.com>
 */
class Woo_Products_Sync_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


    /**
     * Class works with API and DB
     *
     * @var \nongkuschoolubol\SunlightsuplyApiDB
     */
    public $api;


    protected $custom_fields = [
        'each' => [
            [
                'id' => '_variable_title', 'label' => 'Title'
            ],
            [
                'id' => '_upc', 'label' => 'Upc'
            ],
            [
                'id' => '_variable_volume', 'label' => 'Volume (cf)'
            ],
            [
                'id' => '_variable_dim_weight', 'label' => 'Dim Weight (in3/lb)'
            ],
        ],
        'case' => [
            [
                'id' => '_c_upc', 'label' => 'Upc'
            ],
            [
                'id' => '_c_quantity', 'label' => 'Quantity'
            ],
            [
                'id' => '_c_variable_volume', 'label' => 'Volume (cf)'
            ],
            [
                'id' => '_c_variable_dim_weight', 'label' => 'Dim Weight (in3/lb)'
            ],
            [
                'id' => '_c_variable_weight', 'label' => 'Weight (lb)'
            ],
            [
                'id' => '_c_variable_length', 'label' => 'Length (in)'
            ],
            [
                'id' => '_c_variable_width', 'label' => 'Width (in)'
            ],
            [
                'id' => '_c_variable_height', 'label' => 'Height (in)'
            ],
        ],
        'pallet' => [
            [
                'id' => '_pal_upc', 'label' => 'Upc'
            ],
            [
                'id' => '_pal_quantity', 'label' => 'Quantity'
            ],
            [
                'id' => '_pal_variable_volume', 'label' => 'Volume (cf)'
            ],
            [
                'id' => '_pal_variable_dim_weight', 'label' => 'Dim Weight (in3/lb)'
            ],
            [
                'id' => '_pal_variable_weight', 'label' => 'Weight (lb)'
            ],
            [
                'id' => '_pal_variable_length', 'label' => 'Length (in)'
            ],
            [
                'id' => '_pal_variable_width', 'label' => 'Width (in)'
            ],
            [
                'id' => '_pal_variable_height', 'label' => 'Height (in)'
            ],
        ],
        'other' => [
            [
                'id' => '_dealer_price', 'label' => 'Dealer price ($)'
            ]
        ]
    ];

    protected $sale_categories = [
        [
            'id' => 'staff-picks', 'label' => 'Staff Picks'
        ],
        [
            'id' => 'best-sellers', 'label' => 'Best Sellers'
        ],
        [
            'id' => 'new-arrivals', 'label' => 'New Arrivals'
        ]
    ];


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->api = new \nongkuschoolubol\SunlightsuplyApiDB( 'KkArzcSNRRxKPPMQiKGrzMdymNmjQD3T7SqSkaZzeJXWbdb3HK', '5EAzELdtYTzNaGDMZ5pS' );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Products_Sync_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Products_Sync_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-products-sync-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Products_Sync_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Products_Sync_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-products-sync-admin.js', array( 'jquery' ), $this->version, false );

	}

    public function add_options_page() {
        add_menu_page( 'Products Sync', 'Products Sync', 'activate_plugins', 'products-sync', array($this, 'show_main_page'), 'dashicons-cloud');
    }

    public function show_main_page() {
        include('partials/woo-products-sync-admin-display.php');
    }


    /*
     * Add Custom Fields to variable products
     */
    public function lags_woo_add_custom_variation_fields( $loop, $variation_data, $variation ) {

        // Sale products custom fields

        echo '<div class="options_group form-row form-row-full">';
        foreach ($this->sale_categories as $cat) {
            woocommerce_wp_checkbox(
                array(
                    'id'            => '_checkbox_'.$cat['id'].'[' . $variation->ID . ']',
                    'label'         => $cat['label'],
                    'value'         => get_post_meta( $variation->ID, '_checkbox_'.$cat['id'], true ),
                )
            );
        }
        echo '</div>';

        // Other fields

        $custom_fields = $this->custom_fields;

        foreach ($custom_fields['other'] as $key => $field) {

            $class = ($key % 2 == 0) ? 'first' : 'last';

            echo '<div class="options_group form-row form-row-'.$class.'">';
            woocommerce_wp_text_input(
                array(
                    'id'          => $field['id'].'[' . $variation->ID . ']',
                    'label'       => __( $field['label'], 'woocommerce' ),
                    'desc_tip'    => true,
                    'value' => get_post_meta( $variation->ID, $field['id'], true )
                )
            );
            echo '</div>';
        }

        foreach ($custom_fields['each'] as $key => $field) {

            $class = ($key % 2 == 0) ? 'first' : 'last';

            echo '<div class="options_group form-row form-row-'.$class.'">';
            woocommerce_wp_text_input(
                array(
                    'id'          => $field['id'].'[' . $variation->ID . ']',
                    'label'       => __( $field['label'], 'woocommerce' ),
                    'desc_tip'    => true,
                    'value' => get_post_meta( $variation->ID, $field['id'], true )
                )
            );
            echo '</div>';
        }

        echo '<div class="options_group form-row form-row-full">
                <h1>Case</h1>
            </div>';

        foreach ($custom_fields['case'] as $key => $field) {

            $class = ($key % 2 == 0) ? 'first' : 'last';

            echo '<div class="options_group form-row form-row-'.$class.'">';
            woocommerce_wp_text_input(
                array(
                    'id'          => $field['id'].'[' . $variation->ID . ']',
                    'label'       => __( $field['label'], 'woocommerce' ),
                    'desc_tip'    => true,
                    'value' => get_post_meta( $variation->ID, $field['id'], true )
                )
            );
            echo '</div>';
        }

        echo '<div class="options_group form-row form-row-full">
                <h1>Pallet</h1>
            </div>';

        foreach ($custom_fields['pallet'] as $key => $field) {

            $class = ($key % 2 == 0) ? 'first' : 'last';

            echo '<div class="options_group form-row form-row-'.$class.'">';
            woocommerce_wp_text_input(
                array(
                    'id'          => $field['id'].'[' . $variation->ID . ']',
                    'label'       => __( $field['label'], 'woocommerce' ),
                    'desc_tip'    => true,
                    'value' => get_post_meta( $variation->ID, $field['id'], true )
                )
            );
            echo '</div>';
        }

    }


    /*
     * Save variable product fields
     */
    public function lags_woo_add_custom_variation_fields_save( $post_id ){

        foreach ($this->sale_categories as $cat) {

            $val = (isset($_POST['_checkbox_'.$cat['id']][ $post_id ])) ? 'yes' : 'no';

            update_post_meta( $post_id, '_checkbox_'.$cat['id'], $val );
        }

        $custom_fields = $this->custom_fields;

        $spec_types = ['each', 'case', 'pallet', 'other'];

        foreach ($spec_types as $type) {
            foreach ($custom_fields[$type] as $field) {
                update_post_meta( $post_id, $field['id'], esc_attr( $_POST[$field['id']][ $post_id ] ) );
            }
        }

    }


    public function get_local_categories()
    {
        $cats_args = array(
            'taxonomy'     => 'product_cat',
            'type'         => 'product',
            'child_of'     => 0,
            'parent'       => '',
            'orderby'      => 'name',
            'order'        => 'ASC',
            'hide_empty'   => 0,
            'hierarchical' => 0,
            'pad_counts'   => false,
        );

        $cats = get_categories($cats_args);

        $cats_arr = [];
        foreach ($cats as $cat) {
            $cats_arr[$cat->slug] = $cat->term_id;
        }

        return $cats_arr;
    }

    public function sync_products()
    {

        $this->api->uploadAll();

        if ($this->api->hasErrors()) {
            $results['status'] = $this->api->getErrors();
            exit(json_encode($results));
        }

        // Deleting products that wasn't returned from API

        $this->delete_old_products('variations');
        $this->delete_old_products('families');

        $categories = $this->api->getCategories();

        // Deleting product categories that wasn't returned from API
        $this->delete_old_categories($categories);

        $nav_columns_titles = ['Products Column 1', 'Products Column 2', 'Products Column 3'];

        $nav_columns_ids = [];

        foreach ($nav_columns_titles as $column_title) {
            $nav_column = get_term_by('name', $column_title, 'nav_menu');
            $column_id = $nav_column->term_id;
            $nav_columns_ids[] = $column_id;

            $menu = wp_get_nav_menu_object($column_title);

            $menu_items = wp_get_nav_menu_items($menu);

            foreach ($menu_items as $item) {
                // Deleting old menu categories
                wp_delete_post($item->ID);
            }

        }

        // Sorting alphabetically
        $category_name = array();
        foreach ($categories as $key => $row)
        {
            $category_name[$key] = $row['category_name'];
        }
        array_multisort($category_name, SORT_ASC, $categories);

        $count_cats = count($categories);
        $cats_in_column = ceil($count_cats / 3);

        $chunks = array_chunk($categories, $cats_in_column);

        foreach ($nav_columns_ids as $key => $id) {
            foreach ($chunks[$key] as $cat)
                wp_update_nav_menu_item($id, 0, array(
                'menu-item-title' => $cat['category_name'],
                'menu-item-url' => home_url('/product-category/'.$cat['category_web_id']),
                'menu-item-status' => 'publish'));

        }

        foreach ($categories as $category) {

            $get_term = get_term_by('name', $category['category_name'], 'product_cat');

            if (!$get_term) {
                $insert_cat = wp_insert_term($category['category_name'], 'product_cat',
                    array('slug' => $category['category_web_id']));

                if (is_wp_error($insert_cat)) {
                    echo $category['category_name'].' - '.$insert_cat->get_error_message().PHP_EOL;
                }
            }
        }

        // Set query progress to zero
        file_put_contents( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/query_progress.txt', 0 );

        $results['status'] = true;

        exit(json_encode($results));
	}

    public function count_wc_products()
    {
        $results['products_count'] = $this->api->getFamiliesNum();

        if (is_null($results['products_count'])) {
            $results['error'] = true;
            exit(json_encode($results));
        }

        exit(json_encode($results));
	}


    public function add_wc_products()
    {

        global $wpdb;

        $offset = $_POST['offset'];
        $products_count = $_POST['products_count'];
        $default_limit = 1;
        $limit = (($products_count - $offset) < $default_limit) ? ($products_count - $offset) : $default_limit;

        $cats_arr = $this->get_local_categories();

        $families = $this->api->getFamilies($limit, $offset);

        $i = 0;

        if (!empty($families)) {
            foreach ($families as $family) {

                $id = wc_get_product_id_by_sku($family['family_id']);

                $id = ($id != 0) ? $id : '';

                $objFamily = new WC_Product_Variable($id);

                $objFamily->set_name($family['family_name']);
                $objFamily->set_status("publish");  // can be publish,draft or any wordpress product status
                $objFamily->set_catalog_visibility('visible'); // add the product visibility status
                $objFamily->set_description($family['family_description']);
                $objFamily->set_sku($family['family_id']); //can be blank in case you don't have sku, but You can't add duplicate sku's
                $objFamily->set_manage_stock(false); // true or false
                $objFamily->set_backorders('no');
                $objFamily->set_reviews_allowed(true);
                $objFamily->set_sold_individually(false);
                $objFamily->set_category_ids(array($cats_arr[$family['category_web_id']])); // array of category ids, You can get category id from WooCommerce Product Category Section of Wordpress Admin
                $objFamily->set_tax_class('Item');

                $image_title = end(explode('/',$family['image_thumbnail']));

                $media_ID = $this->is_image_exists($image_title);

                if (!isset($media_ID)) {
                    $media_ID = $this->uploadMedia($family['image_thumbnail']);
                }
                $objFamily->set_image_id($media_ID);

                $family_id = $objFamily->save();

                $wpdb->update(
                    $wpdb->prefix.'ss_api_families',
                    array('wp_post_id' => (int)$family_id),
                    array('family_id' => (int)$family['family_id'])
                );

                $variations_titles = [];
                $variations_brands = [];

                if (empty($family['items'])) continue;
                foreach ($family['items'] as $variation) {
                    $variations_titles[] = $variation['product_name'];

                    $api_data = unserialize($variation['api_data']);
                    $variations_brands[] = $api_data['BrandName'];

                }

                // Adding attributes
                $attributes = array(
                    array("name"=>"Title","options"=>$variations_titles,"position"=>2,"visible"=>1,"variation"=>1),
                    array("name"=>"Brand","options"=>$variations_brands,"position"=>3,"visible"=>1,"variation"=>0)
                );

                $productAttributes=array();
                foreach($attributes as $attribute){
                    $attr = wc_sanitize_taxonomy_name(stripslashes($attribute["name"])); // remove any unwanted chars and return the valid string for taxonomy name
                    $attr = 'pa_'.$attr; // woocommerce prepend pa_ to each attribute name
                    if($attribute["options"]){
                        foreach($attribute["options"] as $option){
                            wp_set_object_terms($family_id,$option,$attr,true); // save the possible option value for the attribute which will be used for variation later
                        }
                    }
                    $productAttributes[sanitize_title($attr)] = array(
                        'name' => sanitize_title($attr),
                        'value' => $attribute["options"],
                        'position' => $attribute["position"],
                        'is_visible' => $attribute["visible"],
                        'is_variation' => $attribute["variation"],
                        'is_taxonomy' => '1'
                    );
                }
                update_post_meta($family_id,'_product_attributes',$productAttributes); // save the meta entry for product attributes

                if(!empty($family['items'])) {

                    foreach($family['items'] as $variation){

                        $api_data = unserialize($variation['api_data']);

                        $variation_id = wc_get_product_id_by_sku($variation["product_id"]);

                        $variation_id = ($variation_id != 0) ? $variation_id : '';

                        $objVariation = new WC_Product_Variation($variation_id);

                        $variation_price = $variation["price"] * 1.2;
                        $variation_price = round($variation_price, 2, PHP_ROUND_HALF_UP);
                        $objVariation->set_price($variation_price);
                        $objVariation->set_regular_price($variation_price);
                        $objVariation->set_parent_id($family_id);
                        $objVariation->set_description($variation['description']);
                        if(isset($variation["product_id"]) && $variation["product_id"]){
                            $objVariation->set_sku($variation["product_id"]);
                        }
                        $objVariation->set_manage_stock(true);
                        if ($api_data['NoUps'] == 1) {
                            $objVariation->set_shipping_class_id(8350); // Hardcoded production id for "LTL Freight" shipping class
                        }
                        $objVariation->set_stock_quantity($variation["quantity"]);
                        $stock_status = ($variation["quantity"] > 0) ? 'instock' : 'outofstock';
                        $objVariation->set_stock_status($stock_status); // in stock or out of stock value
                        $var_attributes = array();
                        $title_attr_key = "pa_".wc_sanitize_taxonomy_name(stripslashes("Title")); // name of variant attribute should be same as the name used for creating product attributes
                        $title_attr_val =  wc_sanitize_taxonomy_name(stripslashes($variation["product_name"]));
                        $var_attributes[$title_attr_key] = $title_attr_val;
                        $objVariation->set_attributes($var_attributes);

                        $gallery = [];
                        $count = 1;
                        do {
                            $media_data = $this->get_image_data('large', $variation["product_id"], $count);
                            if ($media_data['id'] == false) {
                                $media_data = $this->get_image_data('medium', $variation["product_id"], $count);
                            }

                            if ($media_data['id'] !== false) {
                                if (is_null($media_data['id'])) {
                                    $media_data['id'] = $this->uploadMedia($media_data['url']);
                                }

                                if ($count == 1) {
                                    $objVariation->set_image_id($media_data['id']);
                                } else {
                                    $gallery[] = $media_data['id'];
                                }

                            }

                            $count++;

                        } while ($media_data['id'] !== false);

                        $var_id = $objVariation->save();

                        if (!empty($gallery)) {
                            update_post_meta( $var_id, 'woo_variation_gallery_images', $gallery );
                        }

                        $wpdb->update(
                            $wpdb->prefix.'ss_api_products',
                            ['wp_post_id' => (int)$var_id],
                            ['product_id' => (int)$variation["product_id"]]
                        );

                        $data_to_insert = [
                          'each' => [
                              'EachUpc' => '_upc',
                              'EachVolume' => '_variable_volume',
                              'EachDimWeight' => '_variable_dim_weight',
                              'EachWeight' => '_weight',
                              'EachLength' => '_length',
                              'EachWidth' => '_width',
                              'EachHeight' => '_height',
                          ],
                          'case' => [
                              'CaseUpc' => '_c_upc',
                              'CaseQuantity' => '_c_quantity',
                              'CaseVolume' => '_c_variable_volume',
                              'CaseDimWeight' => '_c_variable_dim_weight',
                              'CaseWeight' => '_c_variable_weight',
                              'CaseLength' => '_c_variable_length',
                              'CaseWidth' => '_c_variable_width',
                              'CaseHeight' => '_c_variable_height',
                          ],
                          'pallet' => [
                              'PalletUpc' => '_pal_upc',
                              'PalletQuantity' => '_pal_quantity',
                              'PalletVolume' => '_pal_variable_volume',
                              'PalletDimWeight' => '_pal_variable_dim_weight',
                              'PalletWeight' => '_pal_variable_weight',
                              'PalletLength' => '_pal_variable_length',
                              'PalletWidth' => '_pal_variable_width',
                              'PalletHeight' => '_pal_variable_height',
                          ]
                        ];

                        foreach ($data_to_insert as $data_array) {
                            foreach ($data_array as $key => $field) {
                                update_post_meta( $var_id, $field, esc_attr( $api_data[$key] ) );
                            }
                        }

                        update_post_meta( $var_id, '_variable_title', esc_attr( $variation['product_name'] ) );

                        update_post_meta( $var_id, '_dealer_price', esc_attr( round($variation['price'], 2, PHP_ROUND_HALF_UP) ) );

                        $sold_in_quantities = (empty($api_data['SoldInQuantitiesOf']) || $api_data['SoldInQuantitiesOf'] == 0)
                            ? 1 : $api_data['SoldInQuantitiesOf'];

                        update_post_meta( $var_id, '_variable_sold_in_quantities_of', esc_attr( $sold_in_quantities ) );

                        if ($api_data['NoUps'] == 1) {
                            update_post_meta($var_id, '_ltl_freight_variation', 50);
                        }

                        // Adding sale products fields

                        $attrs = [
                            [ 'meta_key' => '_checkbox_staff-picks', 'api_field' => 'Signature'],
                            [ 'meta_key' => '_checkbox_best-sellers', 'api_field' => 'IsSale'],
                            [ 'meta_key' => '_checkbox_new-arrivals', 'api_field' => 'IsNew' ]
                        ];

                        foreach ($attrs as $attr) {
                            $val = ($api_data[$attr['api_field']]) ? 'yes' : 'no';
                            update_post_meta( $var_id, $attr['meta_key'], esc_attr( $val ) );
                        }

                    }

                }

                //TODO Change this
                if (is_int($family_id)) $i++;

            }

            file_put_contents( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/query_progress.txt', $offset+$i );


            $get_progress = file_get_contents( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/query_progress.txt');

            $results['query_progress'] = $get_progress;

        }

        $results['success'] = ($products_count == $offset + $i ) ? true : false;

        exit(json_encode($results));
    }

    public function get_item_num($num)
    {
        if ($num < 10) {
            $num = '0'.$num;
        }
        return $num;
    }

    public function is_image_exists($image_title)
    {
        global $wpdb;

        $media_ID = $wpdb->get_var( "SELECT post_id FROM $wpdb->postmeta 
                       WHERE meta_key = '_wp_attached_file' AND meta_value LIKE '%/".$image_title."'" );

        return $media_ID;
    }

    public function get_image_data($size, $id, $inc)
    {
        $image_url = 'https://sunlightsupply.s3.amazonaws.com/imageMedia/part/'.$size.'/'.$id.'-'. $this->get_item_num($inc).'.jpg';

        if ($this->check_remote_file($image_url)) {
            $image_title = end(explode('/', $image_url));

            $media_id = $this->is_image_exists($image_title);

            return [
                'id' => $media_id,
                'url' => $image_url
            ];
        }
        else {
            return [
                'id' => false
            ];
        }

    }

    public function delete_old_categories($api_categories)
    {

        $api_categories_titles = [];

        if (!empty($api_categories)) {

            foreach ($api_categories as $category) {
                $api_categories_titles[] = $category['category_name'];
            }

            $wc_categories_arr = get_terms( 'product_cat', array('hide_empty' => false) );

            foreach ($wc_categories_arr as $cat) {
                if (!in_array($cat->name, $api_categories_titles)) {
                    wp_delete_category($cat->term_id);
                }
            }

        }
    }

    public function delete_old_products($type = 'families')
    {
        global $wpdb;
        $prefix = $wpdb->prefix;

        // Deleting posts and postmeta
        if ($type == 'families') {
            $api_products = $this->api->getFamilies('', '');
        }
        elseif ($type == 'variations') {
            $api_products = $this->api->getProducts('', '');
        }

        $api_products_sku = [];

        switch ($type) {
            case 'families':
                $id = 'family_id';
                $post_type = 'product';
                break;

            case 'variations':
                $id = 'product_id';
                $post_type = 'product_variation';
                break;
        }

        if (!empty($api_products) && isset($id) && isset($post_type)) {

            foreach ($api_products as $product) {
                $api_products_sku[] = $product[$id];
            }

            $wc_sku_arr = $wpdb->get_results("SELECT pm.meta_value, pm.post_id
                FROM " . $prefix . "postmeta as pm
                INNER JOIN wpdg_posts AS p
                ON p.ID=pm.post_id
                WHERE pm.meta_key = '_sku' AND p.post_type = '" . $post_type . "'");

            foreach ($wc_sku_arr as $p) {
                if (!in_array($p->meta_value, $api_products_sku)) {
                    wp_delete_post($p->post_id, true);
                }
            }

        }

        //TODO delete attributes

    }

    public function uploadMedia($image_url){
        media_sideload_image($image_url,0);
        $attachments = get_posts(array(
            'post_type' => 'attachment',
            'post_status' => null,
            'post_parent' => 0,
            'orderby' => 'post_date',
            'order' => 'DESC'
        ));
        return $attachments[0]->ID;
    }

    public function get_query_progress()
    {
        $result = file_get_contents(plugin_dir_url( __FILE__ ) . '../includes/query_progress.txt');
        exit(json_encode($result));
    }

    public function check_remote_file($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        // don't download content
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        curl_close($ch);
        if($result !== FALSE)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}
