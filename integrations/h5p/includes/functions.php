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
function gamipress_h5p_ajax_get_posts() {

    global $wpdb;

    if( isset( $_REQUEST['post_type'] ) && in_array( 'h5p_contents', $_REQUEST['post_type'] ) ) {

        $results = array();

        // Pull back the search string
        $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

        // Get the content title
        $contents = $wpdb->get_results( $wpdb->prepare(
            "SELECT c.id, c.title, l.title AS library
            FROM {$wpdb->prefix}h5p_contents AS c
            JOIN {$wpdb->prefix}h5p_libraries AS l ON l.id = c.library_id
            WHERE c.title LIKE %s",
            "%%{$search}%%"
        ) );

        foreach ( $contents as $content ) {

            // Results should meet same structure like posts
            $results[] = array(
                'ID' => $content->id,
                'post_title' => $content->title,
                'post_type' => $content->library,
            );

        }

        // Return our results
        wp_send_json_success( $results );
        die;

    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_h5p_ajax_get_posts', 5 );

// Get the content title
function gamipress_h5p_get_content_title( $content_id ) {

    if( absint( $content_id ) === 0 ) return '';

    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare(
        "SELECT c.title
                    FROM {$wpdb->prefix}h5p_contents c
                    WHERE c.id = %d",
        $content_id
    ) );

}

// Get the content type title
function gamipress_h5p_get_content_type_title( $content_type ) {

    if( empty( $content_type ) ) return '';

    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare(
        "SELECT l.title
                    FROM {$wpdb->prefix}h5p_libraries AS l
                    WHERE l.name = %s",
        $content_type
    ) );

}