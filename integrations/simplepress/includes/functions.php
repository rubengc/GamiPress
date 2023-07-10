<?php
/**
 * Functions
 *
 * @package GamiPress\SimplePress\Functions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_simplepress_ajax_get_posts() {

    global $wpdb;

    if( isset( $_REQUEST['post_type'] ) ) {

        // Get the user input
        $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

        if( in_array( 'simplepress_forum', $_REQUEST['post_type'] ) ) {
            // Forums

            $table = SPFORUMS;

            // Try to find the forums
            $forums = $wpdb->get_results(
                "SELECT * FROM {$table}
                " . ( ! empty( $search ) ? "WHERE ( forum_name LIKE '%{$search}%' OR  forum_name LIKE '{$search}%' )" : '' )
            );

            // Build the results array
            $results = array();

            foreach ( $forums as $forum ) {

                // Results should meet same structure like posts
                $results[] = array(
                    'ID' => $forum->forum_id,
                    'post_title' => $forum->forum_name,
                );

            }

            // Return our results
            wp_send_json_success( $results );
            die;
        } else if( in_array( 'simplepress_topic', $_REQUEST['post_type'] ) ) {
            // Topics

            $table = SPTOPICS;

            // Try to find the topics
            $topics = $wpdb->get_results(
                "SELECT * FROM {$table}
                 " . ( ! empty( $search ) ? "WHERE ( topic_name LIKE '%{$search}%' OR  topic_name LIKE '{$search}%' )" : '' )
            );

            // Build the results array
            $results = array();

            foreach ( $topics as $topic ) {

                // Results should meet same structure like posts
                $results[] = array(
                    'ID' => $topic->topic_id,
                    'post_title' => $topic->topic_name,
                    'post_type' => gamipress_simplepress_get_forum_title( $topic->forum_id ),
                );

            }

            // Return our results
            wp_send_json_success( $results );
            die;

        }

    }


}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_simplepress_ajax_get_posts', 5 );

// Helper function to get the forum title
function gamipress_simplepress_get_forum_title( $forum_id ) {

    global $wpdb;

    $table = SPFORUMS;

    return $wpdb->get_var( $wpdb->prepare( "SELECT forum_name FROM {$table} WHERE forum_id = %d;", $forum_id ) );

}

// Helper function to get the topic title
function gamipress_simplepress_get_topic_title( $topic_id ) {

    global $wpdb;

    $table = SPTOPICS;

    return $wpdb->get_var( $wpdb->prepare( "SELECT topic_name FROM {$table} WHERE topic_id = %d;", $topic_id ) );

}