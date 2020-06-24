<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link        
 * @since      1.0.0
 *
 * @package    Woo_Products_Sync
 * @subpackage Woo_Products_Sync/admin/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <h1>Sync products with hawthornegc.com</h1>

    <p class="submit">
        <a href="#" class="button button-primary sync-products">Sync Products</a> <span class="spinner"></span>

        <div class="sync-started hidden">
            <p>Syncing products with hawthornegc.com...</p>
        </div>

        <div class="sync-info hidden">
            <p>Synced successfully!</p>
<!--            <p>Errors: <span>5</span></p>-->
        </div>

        <div class="upload-started hidden">
            <p>Uploading products to Woocommerce...</p>
        </div>

        <div class="progress hidden">
            <p>
                Progress: <span class="done">0</span>/<span class="count"></span>
            </p>
        </div>

        <div class="upload-info hidden">
            <b>Uploaded successfully!</b>
        </div>

        <div class="error hidden"></div>
    </p>

</div>