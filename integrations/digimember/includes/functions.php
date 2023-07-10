<?php
/**
 * Functions
 *
 * @package GamiPress\H5P\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_digimember_ajax_get_posts() {

    global $wpdb;

    if( isset( $_REQUEST['post_type'] ) &&
        ( in_array( 'digimember_product', $_REQUEST['post_type'] )
        || in_array( 'digimember_membership_product', $_REQUEST['post_type'] )
        || in_array( 'digimember_download_product', $_REQUEST['post_type'] ) ) ) {

        $results = array();

        // Pull back the search string
        $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

        if( in_array( 'digimember_product', $_REQUEST['post_type'] ) ) {

            // Get products
            $products = $wpdb->get_results( $wpdb->prepare(
                "SELECT p.id, p.name, p.type
                FROM {$wpdb->prefix}digimember_product AS p
                WHERE p.name LIKE %s",
                "%%{$search}%%"
            ) );

        } else if( in_array( 'digimember_membership_product', $_REQUEST['post_type'] ) ) {

            // Get membership products
            $products = $wpdb->get_results( $wpdb->prepare(
                "SELECT p.id, p.name, p.type
                FROM {$wpdb->prefix}digimember_product AS p
                WHERE p.name LIKE %s
                AND p.type = 'membership'",
                "%%{$search}%%"
            ) );

        } else if( in_array( 'digimember_download_product', $_REQUEST['post_type'] ) ) {

            // Get download products
            $products = $wpdb->get_results( $wpdb->prepare(
                "SELECT p.id, p.name, p.type
                FROM {$wpdb->prefix}digimember_product AS p
                WHERE p.name LIKE %s
                AND p.type = 'download'",
                "%%{$search}%%"
            ) );

        }

        foreach ( $products as $product ) {

            // Results should meet same structure like posts
            $results[] = array(
                'ID' => $product->id,
                'post_title' => $product->name,
                'post_type' => ucfirst( $product->type ),
            );

        }

        // Return our results
        wp_send_json_success( $results );
        die;

    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_digimember_ajax_get_posts', 5 );

// Get the product title
function gamipress_digimember_get_product_title( $product_id ) {

    if( absint( $product_id ) === 0 ) return '';

    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare(
        "SELECT p.name FROM {$wpdb->prefix}digimember_product AS p WHERE p.id = %d",
        $product_id
    ) );

}

/**
 * Get the product type
 *
 * @since 1.0.0
 *
 * @param int $product_id
 *
 * @return string|null
 */
function gamipress_digimember_get_product_type( $product_id ) {

    // Empty title if no ID provided
    if( absint( $product_id ) === 0 ) {
        return '';
    }

    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare(
        "SELECT p.type FROM {$wpdb->prefix}digimember_product AS p WHERE p.id = %d",
        $product_id
    ) );

}