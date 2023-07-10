<?php
/**
 * Ajax Functions
 *
 * @package GamiPress\WooCommerce\Ajax_Functions
 * @since 1.1.3
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Return product variations dropdown for requirements UI
function gamipress_wc_ajax_get_product_variations() {

    $product_id = $_POST['post_id'];
    $site_id = $_POST['site_id'];
    $selected = $_POST['selected'];

    if( gamipress_is_network_wide_active() && $site_id !== get_current_blog_id() ) {
        switch_to_blog( $site_id );
        $product = wc_get_product( $product_id );
        restore_current_blog();
    } else {
        $product = wc_get_product( $product_id );
    }

    // Bail if product doesn't exists
    if( ! $product ) {
        die();
    }

    $output = gamipress_wc_get_product_variations_dropdown( $product_id, $selected, $site_id );

    // Bail if product has no variations
    if( empty( $output ) ) {
        echo '<span style="color: #a00;">This product has no variations</span>';
        die();
    }

    echo $output;
    die();

}
add_action( 'wp_ajax_gamipress_wc_get_product_variations', 'gamipress_wc_ajax_get_product_variations' );