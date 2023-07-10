<?php
/**
 * Listeners
 *
 * @package GamiPress\JetEngine\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Publish post listener
 *
 * @since 1.0.0
 *
 * @param string    $new_status The new post status
 * @param string    $old_status The old post status
 * @param WP_Post   $post       The post
 */
function gamipress_jetengine_publish_post_listener( $new_status, $old_status, $post ) {

    // Bail if post has been already published
    if( $old_status === 'publish' ) {
        return;
    }

    // Bail if post is not published
    if( $new_status !== 'publish' ) {
        return;
    }

    $user_id = get_current_user_id();

    // Login is required
    if ( $user_id === 0 ) return;

    // Check if it is a JetEngine post type
    if ( ! gamipress_jetengine_check_type( $post ) ) {
        return;
    }

    // Publish post of any type
    do_action( 'gamipress_jetengine_publish_post_any_type', $post->post_type, $user_id );

    // Publish post of specific type
    do_action( 'gamipress_jetengine_publish_post_specific_type', $post->post_type, $user_id );

}
add_action( 'transition_post_status', 'gamipress_jetengine_publish_post_listener', 10, 3 );

/**
 * Update post listener
 *
 * @since 1.0.0
 *
 * @param WP_Post    $post_after    Post object following the update.
 * @param WP_Post    $post_before   Post object before the update.
 * @param int        $post_ID       The post ID
 */
function gamipress_jetengine_update_post_listener( $post_ID, $post_after, $post_before ) {

    // Check if it is a JetEngine post type
    if ( ! gamipress_jetengine_check_type( $post_after ) ) {
        return;
    }
  
    // Check if it is an autosave or a revision.
    if ( wp_is_post_autosave( $post_ID ) || wp_is_post_revision( $post_ID ) ) {
        return;
    }

    // Bail if is a new post
    if( $post_before->post_status === 'auto-draft' ) {
        return;
    } 

    if ( isset( $_POST['original_post_status'] ) ) {
        if ( !empty( $_POST ) || $_POST['original_post_status'] === 'auto-draft' ) {
            return;
        }
    }    

    // Bail if post is removed
    if ( $post_after->post_status === 'trash' ) {
        return;
    }

    $user_id = get_current_user_id();

    // Login is required
    if ( $user_id === 0 ) return;

    // Get the updated post
    $post = get_post( absint( $post_ID ) );

    // Update post of any type
    do_action( 'gamipress_jetengine_update_post_any_type', $post->post_type, $user_id );

    // Update post of specific type
    do_action( 'gamipress_jetengine_update_post_specific_type', $post->post_type, $user_id );

}
add_action( 'post_updated', 'gamipress_jetengine_update_post_listener', 10, 3 );


/**
 * Delete post listener
 *
 * @since 1.0.0
 *
 * @param int        $post_id       The post ID
 */
function gamipress_jetengine_delete_post_listener( $post_id ) {

    $user_id = get_current_user_id();

    // Login is required
    if ( $user_id === 0 ) return;

    // Get the deleted post
    $post = get_post( absint( $post_id ) );

    // Check if it is a JetEngine post type
    if ( ! gamipress_jetengine_check_type( $post ) ) {
        return;
    }

    // Update post of any type
    do_action( 'gamipress_jetengine_delete_post_any_type', $post->post_type, $user_id );

    // Update post of specific type
    do_action( 'gamipress_jetengine_delete_post_specific_type', $post->post_type, $user_id );

}
add_action( 'trashed_post', 'gamipress_jetengine_delete_post_listener' );
add_action( 'before_delete_post', 'gamipress_jetengine_delete_post_listener' );


