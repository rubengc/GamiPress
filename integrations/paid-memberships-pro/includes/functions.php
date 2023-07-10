<?php
/**
 * Functions
 *
 * @package GamiPress\Paid_Memberships_Pro\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_paid_memberships_pro_ajax_get_posts() {

    global $wpdb;

    if( isset( $_REQUEST['post_type'] ) && in_array( 'pmpro_membership', $_REQUEST['post_type'] ) ) {

        $results = array();

        // Pull back the search string
        $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

        // Get the memberships
        $memberships = $wpdb->get_results( $wpdb->prepare(
            "SELECT m.id, m.name
            FROM {$wpdb->pmpro_membership_levels} AS m
            WHERE m.name LIKE %s",
            "%%{$search}%%"
        ) );

        foreach ( $memberships as $membership ) {

            // Results should meet same structure like posts
            $results[] = array(
                'ID' => $membership->id,
                'post_title' => $membership->name,
            );

        }

        // Return our results
        wp_send_json_success( $results );
        die;

    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_paid_memberships_pro_ajax_get_posts', 5 );

// Get the membership title
function gamipress_paid_memberships_pro_get_membership_title( $membership_id ) {

    if( absint( $membership_id ) === 0 ) return '';

    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare(
        "SELECT m.name FROM {$wpdb->pmpro_membership_levels} AS m WHERE m.id = %d",
        $membership_id
    ) );

}