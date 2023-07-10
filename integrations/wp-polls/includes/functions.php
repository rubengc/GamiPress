<?php
/**
 * Functions
 *
 * @package GamiPress\WP_Polls\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_wp_polls_ajax_get_posts() {

    global $wpdb;

    if( isset( $_REQUEST['post_type'] ) && in_array( 'wp_polls', $_REQUEST['post_type'] ) ) {

        $results = array();

        // Pull back the search string
        $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

        $polls = $wpdb->get_results( $wpdb->prepare(
            "SELECT pollq_id, pollq_question
            FROM {$wpdb->pollsq}
            WHERE pollq_question LIKE %s",
            "%%{$search}%%"
        ) );

        foreach ( $polls as $poll ) {

            // Results should meet same structure like posts
            $results[] = array(
                'ID' => $poll->pollq_id,
                'post_title' => $poll->pollq_question,
            );

        }

        // Return our results
        wp_send_json_success( $results );
        die;
    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_wp_polls_ajax_get_posts', 5 );

// Helper function to get the poll title (Question)
function gamipress_wp_polls_get_poll_title( $poll_id ) {

    global $wpdb;
    $sql = "SELECT pollq_question FROM {$wpdb->pollsq} WHERE pollq_id = %d ";

    return $wpdb->get_var( $wpdb->prepare( $sql, $poll_id ) );

}