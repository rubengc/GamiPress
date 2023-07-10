<?php
/**
 * Functions
 *
 * @package GamiPress\FluentCRM\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_fluentcrm_ajax_get_posts() {

    global $wpdb;

    $results = array();

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    if( isset( $_REQUEST['post_type'] ) && in_array( 'fluentcrm_tags', $_REQUEST['post_type'] ) ) {

        $tags = $wpdb->get_results( $wpdb->prepare(
            "SELECT t.id, t.title
            FROM   {$wpdb->prefix}fc_tags AS t
            WHERE  t.title LIKE %s",
            "%%{$search}%%"
        ) );

        foreach ( $tags as $tag ) {

            // Results should meet same structure like posts
            $results[] = array(
                'ID' => $tag->id,
                'post_title' => $tag->title,
            );

        }

        // Return our results
        wp_send_json_success( $results );
        die;

    } else if( isset( $_REQUEST['post_type'] ) && in_array( 'fluentcrm_lists', $_REQUEST['post_type'] ) ) {

        $lists = $wpdb->get_results( $wpdb->prepare(
            "SELECT t.id, t.title
            FROM   {$wpdb->prefix}fc_lists AS t
            WHERE  t.title LIKE %s",
            "%%{$search}%%"
        ) );

        foreach ( $lists as $list ) {

            // Results should meet same structure like posts
            $results[] = array(
                'ID' => $list->id,
                'post_title' => $list->title,
            );

        }

        // Return our results
        wp_send_json_success( $results );
        die;

    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_fluentcrm_ajax_get_posts', 5 );

// Helper function to get the subscriber user ID
function gamipress_fluentcrm_get_subscriber_user_id( $subscriber ) {

    $user_id = 0;

    if( absint( $subscriber->user_id ) !== 0 ) {
        // Get the user ID
        $user_id = $subscriber->user_id;
    } else if( ! empty( $subscriber->email ) ) {
        // Search by email
        $user = get_user_by_email( $subscriber->email );

        $user_id = $user->ID;
    }

    return $user_id;

}

// Get the tag title
function gamipress_fluentcrm_get_tag_title( $tag_id ) {

    if( absint( $tag_id ) === 0 ) {
        return '';
    }

    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare(
        "SELECT title FROM {$wpdb->prefix}fc_tags WHERE id = %s",
        $tag_id
    ) );

}

// Get the list title
function gamipress_fluentcrm_get_list_title( $list_id ) {

    if( absint( $list_id ) === 0 ) {
        return '';
    }

    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare(
        "SELECT title FROM {$wpdb->prefix}fc_lists WHERE id = %s",
        $list_id
    ) );

}