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
 * Listener for user log in
 *
 * Triggers: gamipress_login
 *
 * @since   1.1.0
 * @updated 1.4.3 Changed event triggering by calling directly to gamipress_trigger_event()
 *
 * @param string  $user_login Username.
 * @param WP_User $user       WP_User object of the logged-in user.
 */
function gamipress_login_listener( $user_login, $user ) {

    // Sometimes this event is triggered before gamipress_load_activity_triggers()
    // so to avoid issues, method has changed to trigger the event directly
    do_action( 'gamipress_login', $user->ID, $user );

    gamipress_trigger_event( array(
        'event' => 'gamipress_login',
        'user_id' => $user->ID,
    ) );

}
add_action( 'wp_login', 'gamipress_login_listener', 10, 2 );

/**
 * Listener for content publishing
 *
 * Triggers: gamipress_publish_{$post_type}
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
 * Listener for content deletion
 *
 * Triggers: gamipress_delete_{$post_type}
 *
 * @since  1.3.7
 *
 * @param  integer  $post_id The deleted post ID
 *
 * @return void
 */
function gamipress_delete_post_listener( $post_id ) {

    $post = get_post( $post_id );

    // Trigger content deletion actions
    do_action( "gamipress_delete_{$post->post_type}", $post->ID, $post->post_author, $post );

}
add_action( 'trashed_post', 'gamipress_delete_post_listener' );
add_action( 'before_delete_post', 'gamipress_delete_post_listener' );

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

    $post_id = absint( $comment[ 'comment_post_ID' ] );

    // Trigger comment actions
    do_action( 'gamipress_specific_new_comment', (int) $comment_ID, (int) $comment[ 'user_id' ], $post_id, $comment );
    do_action( 'gamipress_new_comment', (int) $comment_ID, (int) $comment[ 'user_id' ], $post_id, $comment );

    if( $post_id !== 0 ) {

        $post_author = absint( get_post_field( 'post_author', $post_id ) );

        // Trigger comment actions to author
        do_action( 'gamipress_user_specific_post_comment', (int) $comment_ID, $post_author, $post_id, $comment );
        do_action( 'gamipress_user_post_comment', (int) $comment_ID, $post_author, $post_id, $comment );
    }

}
add_action( 'comment_approved_', 'gamipress_approved_comment_listener', 10, 2 );
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
    $now = strtotime( date( 'Y-m-d', current_time( 'timestamp' ) ) );

    // Website daily visit
    $count = gamipress_get_user_trigger_count( $user_id, 'gamipress_site_visit', $now );

    // Trigger daily visit action if not triggered today
    if( $count === 0 ) {
        do_action( 'gamipress_site_visit', $user_id );
    }

    global $post;

    if( $post ) {

        // Post daily visit
        $count = gamipress_get_user_trigger_count( $user_id, 'gamipress_specific_post_visit', $now, 0, array( $post->ID, $user_id, $post ) );

        // Trigger daily post visit action if not triggered today
        if( $count === 0 ) {

            // Trigger any post visit
            do_action( 'gamipress_post_visit', $post->ID, $user_id, $post );

            // Trigger specific post visit
            do_action( 'gamipress_specific_post_visit', $post->ID, $user_id, $post );

        }

    }
}
add_action( 'wp_head', 'gamipress_site_visit_listener' );

/**
 * Listener for user post visits
 *
 * Triggers: gamipress_user_post_visit, gamipress_user_specific_post_visit
 *
 * @since  1.2.9
 *
 * @return void
 */
function gamipress_user_post_visit_listener() {

    // Bail if is an ajax request
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return;
    }

    // Bail if is admin area
    if( is_admin() ) {
        return;
    }

    // Current User ID
    $user_id = get_current_user_id();

    global $post;

    if( $post ) {

        $post_author = absint( $post->post_author );

        // Trigger user post visit action to the author if visitor not is the author
        if( $post_author && $post_author !== $user_id ) {
            do_action( 'gamipress_user_post_visit', $post->ID, $post_author, $user_id, $post );
            do_action( 'gamipress_user_specific_post_visit', $post->ID, $post_author, $user_id, $post );
        }

    }

}
add_action( 'wp_head', 'gamipress_user_post_visit_listener' );

/**
 * Listener for expend points
 *
 * Triggers: gamipress_expend_points
 *
 * @since  1.3.7
 *
 * @param integer $post_id 	        The item unlocked ID (achievement or rank)
 * @param integer $user_id 			The user ID
 * @param integer $points 			The amount of points expended
 * @param string  $points_type 		The points type of the amount of points expended
 *
 * @return void
 */
function gamipress_expend_points_listener( $post_id, $user_id, $points, $points_type ) {

    // Trigger user expend points action
    do_action( 'gamipress_expend_points', $post_id, $user_id, $points, $points_type );

}
add_action( 'gamipress_achievement_unlocked_with_points', 'gamipress_expend_points_listener', 10, 4 );
add_action( 'gamipress_rank_unlocked_with_points', 'gamipress_expend_points_listener', 10, 4 );