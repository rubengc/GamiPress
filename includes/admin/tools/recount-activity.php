<?php
/**
 * Recount Activity Tool
 *
 * @package     GamiPress\Admin\Tools\Recount_Activity
 * @since       1.1.8
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Recount Activity Tool meta boxes
 *
 * @since  1.1.8
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_recount_activity_tool_meta_boxes( $meta_boxes ) {

    /**
     * Hook to add recountable activity triggers (login or daily visits are not recountable because they aren't stored in database)
     *
     * @since 1.1.8
     */
    $recountable_activity_triggers = apply_filters( 'gamipress_recountable_activity_triggers',
        array(
            '' => __( 'Choose the activity to recount', 'gamipress' ),
            // WordPress
            __( 'WordPress', 'gamipress' ) => array(
                'comments'              => __( 'Recount comments', 'gamipress' ),
                'published_content'     => __( 'Recount content publishing', 'gamipress' ),
            ),
        )
    );

    $meta_boxes['recount-activity'] = array(
        'title' => __( 'Recount Activity', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_recount_activity_tool_fields', array(
            'recount_activity_desc' => array(
                'content' => __( 'This tool will try to sync old activity with your already configured GamiPress install. GamiPress logs will be updated with all the activity stored in the database and the already configured points awards and deducts and achievements will be awarded or deducted too.', 'gamipress' )
                    . '<br>' . __( '<strong>Note:</strong> Some activity may not be possible to recount (like user log in or daily visits) because there are not registries stored in the database.', 'gamipress' )
                    . '<br>' . __( '<strong>Important:</strong> If emails to notify users about new earnings are enabled is possible that users will receive a lot of emails so is recommendable to deactivate them temporally.', 'gamipress' ),
                'type' => 'html',
            ),
            'activity_to_recount' => array(
                'desc' => __( 'Choose the activity to recount.', 'gamipress' ),
                'type' => 'advanced_select',
                'options' => $recountable_activity_triggers,
            ),
            'recount_activity' => array(
                'label' => __( 'Recount Activity', 'gamipress' ),
                'type' => 'button',
                'button' => 'primary'
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_tools_general_meta_boxes', 'gamipress_recount_activity_tool_meta_boxes' );

/**
 * AJAX handler for the recount activity tool
 *
 * @since   1.1.8
 * @updated 1.4.2 Added the run again utility
 */
function gamipress_ajax_recount_activity_tool() {

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    // Check parameters received
    if( ! isset( $_POST['activity'] ) || empty( $_POST['activity'] ) ) {
        wp_send_json_error( __( 'You need to choose an activity to recount.', 'gamipress' ) );
    }

    ignore_user_abort( true );

    if ( ! gamipress_is_function_disabled( 'set_time_limit' ) ) {
        set_time_limit( 0 );
    }

    $response = array(
        'success' => true,
        'run_again' => false,
        'message' =>  __( 'Activity recount process has been done successfully.', 'gamipress' )
    );

    $activity = $_POST['activity'];
    $loop = ( ! isset( $_POST['loop'] ) ? 0 : absint( $_POST['loop'] ) );

    /**
     * Hook to process activity recount
     *
     * @since   1.1.8
     * @updated 1.4.2 Added $loop parameter
     *
     * @param array $response   Response to return. Response format: array(
     *                              success => true|false,
     *                              run_again => true|false,
     *                              message => string
     *                          )
     * @param int   $loop       Parameter to meet in which loop is the tool (just increased if run_again is set to true).
     *                          Important: First loop will be 0.
     */
    $response = apply_filters( "gamipress_activity_recount_$activity", $response, $loop );

    if( ! is_array( $response ) ) {
        wp_send_json_error( 'Activity recount process has failed!', 'gamipress' );
    }

    if( $response['run_again'] ) {
        // Return the full response
        wp_send_json_success( $response );
    }

    if( $response['success'] === true ) {
        // Return a success message
        wp_send_json_success( $response['message'] );
    } else {
        // Return an error message
        wp_send_json_error( $response['message'] );
    }
}
add_action( 'wp_ajax_gamipress_recount_activity_tool', 'gamipress_ajax_recount_activity_tool' );

/**
 * Recount comments activity
 *
 * @since   1.1.8
 * @updated 1.4.2 Added $loop parameter
 *
 * @param array $response
 * @param int   $loop
 *
 * @return array $response
 */
function gamipress_activity_recount_comments( $response, $loop ) {

    global $wpdb;

    // Set a limit of 100 comments
    $limit = 100;
    $offset = $limit * $loop;

    // Get all approved comments count
    $comments_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = '1'" ) );

    // On first loop send an informational text
    if( $loop === 0 && $comments_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d comments found, recounting...', 'gamipress' ), $comments_count );

        // Return early to inform
        return $response;
    }

    // Get all approved comments
    $comments = $wpdb->get_results( "SELECT * FROM {$wpdb->comments} WHERE comment_approved = '1' LIMIT {$offset}, {$limit}" );

    foreach( $comments as $comment ) {

        // Trigger comment actions to user
        do_action( 'gamipress_specific_new_comment', (int) $comment->ID, (int) $comment->user_id, $comment->comment_post_ID, $comment );
        do_action( 'gamipress_new_comment', (int) $comment->ID, (int) $comment->user_id, $comment->comment_post_ID, $comment );

        if( $comment->comment_post_ID !== 0 ) {

            $post_author = absint( get_post_field( 'post_author', $comment->comment_post_ID ) );

            // Trigger comment actions to author
            do_action( 'gamipress_user_specific_post_comment', (int) $comment->ID, $post_author, $comment->comment_post_ID, $comment );
            do_action( 'gamipress_user_post_comment', (int) $comment->ID, $post_author, $comment->comment_post_ID, $comment );
        }

        // GamiPress has a 1 second delay to prevent unlimited earnings
        sleep( 1 );
    }

    $recounted_comments = $limit * ( $loop + 1 );

    // Check remaining comments
    if( $recounted_comments < $comments_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining comments to finish recount', 'gamipress' ), ( $comments_count - $recounted_comments ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_comments', 'gamipress_activity_recount_comments', 10, 2 );

/**
 * Recount published content
 *
 * @since   1.1.8
 * @updated 1.4.2 Added $loop parameter
 *
 * @param array $response
 * @param int   $loop
 *
 * @return array $response
 */
function gamipress_activity_recount_published_content( $response, $loop ) {

    global $wpdb;

    // Set a limit of 100 posts
    $limit = 100;
    $offset = $limit * $loop;

    // Get all public post types which means they are visitable
    $public_post_types = get_post_types( array( 'public' => true ) );

    // Remove attachment from public post types
    if( isset( $public_post_types['attachment'] ) ) {
        unset( $public_post_types['attachment'] );
    }

    // Get all published posts
    $posts_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type IN ( '" . implode( "', '", $public_post_types ) . "' )" ) );

    // On first loop send an informational text
    if( $loop === 0 && $posts_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d posts found, recounting...', 'gamipress' ), $posts_count );

        // Return early to inform
        return $response;
    }

    // Get all published posts
    $posts = $wpdb->get_results( "SELECT * FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type IN ( '" . implode( "', '", $public_post_types ) . "' ) LIMIT {$offset}, {$limit}" );

    foreach( $posts as $post ) {
        // Trigger content publishing action for each post
        do_action( "gamipress_publish_{$post->post_type}", $post->ID, $post->post_author, $post );

        // GamiPress has a 1 second delay to prevent unlimited earnings
        sleep( 1 );
    }

    $recounted_posts = $limit * ( $loop + 1 );

    // Check remaining posts
    if( $recounted_posts < $posts_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining posts to finish recount', 'gamipress' ), ( $posts_count - $recounted_posts ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_published_content', 'gamipress_activity_recount_published_content', 10, 2 );