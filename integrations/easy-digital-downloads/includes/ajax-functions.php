<?php
/**
 * Ajax Functions
 *
 * @package GamiPress\Easy_Digital_Downloads\Ajax_Functions
 * @since 1.1.2
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Return download variations dropdown for requirements UI
function gamipress_edd_ajax_get_download_variations() {

    $download_id = $_POST['post_id'];
    $site_id = $_POST['site_id'];
    $selected = $_POST['selected'];

    if( gamipress_is_network_wide_active() && $site_id !== get_current_blog_id() ) {
        switch_to_blog( $site_id );
        $download = edd_get_download( $download_id );
        restore_current_blog();
    } else {
        $download = edd_get_download( $download_id );
    }

    // Bail if download doesn't exists
    if( ! $download ) {
        die();
    }

    $output = gamipress_edd_get_download_variations_dropdown( $download_id, $selected, $site_id );

    // Bail if download has no variations
    if( empty( $output ) ) {
        echo '<span style="color: #a00;">This download has no variations</span>';
        die();
    }

    echo $output;
    die();

}
add_action( 'wp_ajax_gamipress_edd_get_download_variations', 'gamipress_edd_ajax_get_download_variations' );