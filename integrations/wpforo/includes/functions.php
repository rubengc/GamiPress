<?php
/**
 * Functions
 *
 * @package GamiPress\wpForo\Functions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_wpforo_ajax_get_posts() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );
    global $wpdb;

    if( isset( $_REQUEST['post_type'] ) ) {
        
        // Get the user input
        $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

        if( in_array( 'wpforo_forum', $_REQUEST['post_type'] ) ) {
            // Forums

            $table = WPF()->tables->forums;
            $boards = WPF()->tables->boards;

            // Get the boards
            $results_boards = $wpdb->get_results( 
                "SELECT boardid FROM {$boards}"
            );

            // Try to find the forums
            $forums = $wpdb->get_results(
                "SELECT * FROM {$table}
                " . ( ! empty( $search ) ? "WHERE ( title LIKE '%{$search}%' OR  title LIKE '{$search}%' )" : '' )
            );

            // Build the results array
            $results = array();

            foreach ( $forums as $forum ) {

                // Results should meet same structure like posts
                $results[] = array(
                    'ID' => $forum->forumid,
                    'post_title' => $forum->title,
                );

            }

            if ( count( $results_boards ) > 1 ) {
                foreach ( $results_boards as $board ){
                    if ( $board->boardid !== '0' ){
                        $table = $wpdb->prefix . 'wpforo_' . $board->boardid . '_forums';
                        // Get the forums
                        $results_forums = $wpdb->get_results(
                            "SELECT * FROM {$table}
                            " . ( ! empty( $search ) ? "WHERE ( title LIKE '%{$search}%' OR  title LIKE '{$search}%' )" : '' )
                        );
                        
                        foreach ($results_forums as $forum ) {
                            $forum_id = $board->boardid . '-' . $forum->forumid;
                            $results[] = array(
                                'ID' => $forum_id,
                                'post_title' => $forum->title,
                            );
                        }
                    }
                }
            } 

            // Return our results
            wp_send_json_success( $results );
            die;
        } else if( in_array( 'wpforo_topic', $_REQUEST['post_type'] ) ) {
            // Topics

            $table = WPF()->tables->topics;
            $boards = WPF()->tables->boards;

            // Get the boards
            $results_boards = $wpdb->get_results( 
                "SELECT boardid FROM {$boards}"
            );

            // Try to find the topics
            $topics = $wpdb->get_results(
                "SELECT * FROM {$table}
                 " . ( ! empty( $search ) ? "WHERE ( title LIKE '%{$search}%' OR  title LIKE '{$search}%' )" : '' )
            );

            // Build the results array
            $results = array();

            foreach ( $topics as $topic ) {

                // Results should meet same structure like posts
                $results[] = array(
                    'ID' => $topic->topicid,
                    'post_title' => $topic->title,
                    'post_type' => gamipress_wpforo_get_forum_title( $topic->forumid ),
                );

            }

            if ( count( $results_boards ) > 1 ) {
                foreach ( $results_boards as $board ){
                    if ( $board->boardid !== '0' ){
                        $table = $wpdb->prefix . 'wpforo_' . $board->boardid . '_topics';
                        // Get the topics
                        $results_topics = $wpdb->get_results(
                            "SELECT * FROM {$table}
                             " . ( ! empty( $search ) ? "WHERE ( title LIKE '%{$search}%' OR  title LIKE '{$search}%' )" : '' )
                        );
                        
                        foreach ($results_topics as $topic ) {
                            $forum_id = $board->boardid . '-' . $topic->forumid;
                            $results[] = array(
                                'ID' => $board->boardid . '-' . $topic->topicid,
                                'post_title' => $topic->title,
                                'post_type' => gamipress_wpforo_get_forum_title( $forum_id ),
                            );
                        }
                    }
                }
            }

            $response = array(
                'results' => $results,
                'more_results' => false,
            );

            // Return our results
            wp_send_json_success( $response );
            die;

        }

    }


}
add_action( 'wp_ajax_gamipress_wpforo_get_posts', 'gamipress_wpforo_ajax_get_posts', 5 );


function gamipress_wpforo_forum_options(){

}

// Helper function to get the forum title
function gamipress_wpforo_get_forum_title( $forum_id ) {

    global $wpdb;

    if ( strpos( $forum_id, '-' ) ) {
        $board = explode('-', $forum_id)[0];
        $forum_id = explode('-', $forum_id)[1];
        $table = $table = $wpdb->prefix . 'wpforo_' . $board . '_forums';

    } else {
        $table = WPF()->tables->forums;
    }
    
    return $wpdb->get_var( $wpdb->prepare(
        "SELECT f.title FROM {$table} AS f WHERE f.forumid = %d",
        $forum_id
    ) );

}

// Helper function to get the topic title
function gamipress_wpforo_get_topic_title( $topic_id ) {

    global $wpdb;

    if ( strpos( $topic_id, '-' ) ) {
        $board = explode('-', $topic_id)[0];
        $topic_id = explode('-', $topic_id)[1];
        $table = $table = $wpdb->prefix . 'wpforo_' . $board . '_topics';

    } else {
        $table = WPF()->tables->topics;
    }

    return $wpdb->get_var( $wpdb->prepare(
        "SELECT f.title FROM {$table} AS f WHERE f.topicid = %d",
        $topic_id
    ) );

}