<?php
/**
 * Functions
 *
 * @package GamiPress\WP_User_Manager\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_wp_user_manager_ajax_get_posts() {

    if( isset( $_REQUEST['post_type'] ) && in_array( 'wp_user_manager_form', $_REQUEST['post_type'] ) ) {
        global $wpdb;

        // Pull back the search string
        $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT f.id AS ID, f.name AS post_title
		FROM   {$wpdb->prefix}wpum_registration_forms AS f
		WHERE  f.name LIKE %s",
            "%%{$search}%%"
        ) );

        // Return our results
        wp_send_json_success( $results );
        die;
    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_wp_user_manager_ajax_get_posts', 5 );