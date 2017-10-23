<?php
/**
 * Activity Listeners
 *
 * @package     GamiPress\Listeners
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Listener for content publishing
 *
 * Triggers: gamipress_login
 *
 * @since  1.1.0
 *
 * @param string  $user_login Username.
 * @param WP_User $user       WP_User object of the logged-in user.
 *
 * @return void
 */
function gamipress_login_listener( $user_login, $user ) {
    do_action( 'gamipress_login', $user->ID, $user );
}
add_action( 'wp_login', 'gamipress_login_listener', 10, 2 );

/**
 * Listener for content publishing
 *
 * Triggers: gamipress_new_{$post_type}
 *
 * @since  1.0.0
 *
 * @param  string   $new_status The new post status
 * @param  string   $old_status The old post status
 * @param  WP_Post  $post       The post
 *
 * @return void
 */
function gamipress_transition_post_status_listener( $new_status, $old_status, $post ) {
    // Statuses to check the old status
    $old_statuses = apply_filters( 'gamipress_publish_listener_old_status', array( 'new', 'auto-draft', 'draft', 'private', 'pending', 'future' ), $post->ID );

    // Statuses to check the new status
    $new_statuses = apply_filters( 'gamipress_publish_listener_new_status', array( 'publish', 'private' ), $post->ID );

    // Check if post status transition come to publish
    if ( in_array( $old_status, $old_statuses ) && in_array( $new_status, $new_statuses ) ) {
        // Trigger content publishing actions
        do_action( "gamipress_publish_{$post->post_type}", $post->ID, $post->post_author, $post );
    }
}
add_action( 'transition_post_status', 'gamipress_transition_post_status_listener', 10, 3 );

/**
 * Listener for comment publishing
 *
 * Triggers: gamipress_new_comment, gamipress_specific_new_comment
 *
 * @since  1.0.0
 *
 * @param  integer $comment_ID The comment ID
 * @param  array|object $comment The comment array
 *
 * @return void
 */
function gamipress_approved_comment_listener( $comment_ID, $comment ) {
    // Enforce array for both hooks (wp_insert_comment uses object, comment_{status}_comment uses array)
    if ( is_object( $comment ) ) {
        $comment = get_object_vars( $comment );
    }

    // Check if comment is approved
    if ( 1 != (int) $comment[ 'comment_approved' ] ) {
        return;
    }

    // Trigger comment actions
    do_action( 'gamipress_specific_new_comment', (int) $comment_ID, (int) $comment[ 'user_id' ], $comment[ 'comment_post_ID' ], $comment );
    do_action( 'gamipress_new_comment', (int) $comment_ID, (int) $comment[ 'user_id' ], $comment[ 'comment_post_ID' ], $comment );

}
add_action( 'comment_approved_comment', 'gamipress_approved_comment_listener', 10, 2 );
add_action( 'wp_insert_comment', 'gamipress_approved_comment_listener', 10, 2 );

/**
 * Listener for daily visits
 *
 * Triggers: gamipress_site_visit, gamipress_specific_post_visit
 *
 * @since  1.0.0
 *
 * @return void
 */
function gamipress_site_visit_listener() {
    // Bail if is an ajax request
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return;
    }

    // Bail if is admin area
    if( is_admin() ) {
        return;
    }

    // Bail if not logged in
    if( ! is_user_logged_in() ) {
        return;
    }

    // Current User ID
    $user_id = get_current_user_id();
    $now = current_time( 'timestamp' );

    // Website daily visit
    $count = gamipress_get_user_trigger_count( $user_id, 'gamipress_site_visit', $now );

    // Trigger daily visit action if not triggered today
    if( $count === 0 ) {
        do_action( 'gamipress_site_visit', $user_id );
    }

    // Post daily visit
    global $post;

    if( $post ) {

        $count = gamipress_get_user_trigger_count( $user_id, 'gamipress_specific_post_visit', $now, 0, array( $post->ID, $user_id, $post ) );

        // Trigger specific daily visit action if not triggered today
        if( $count === 0 ) {
            do_action( 'gamipress_specific_post_visit', $post->ID, $user_id, $post );
        }
    }
}
add_action( 'wp_head', 'gamipress_site_visit_listener' );