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
     * Hook to add recountable activity triggers (login or daily visits are not recountable because they are not getting stored in the database)
     *
     * @since 1.1.8
     */
    $recountable_activity_triggers = apply_filters( 'gamipress_recountable_activity_triggers',
        array(
            '' => __( 'Choose the activity to recount', 'gamipress' ),
            // WordPress
            __( 'WordPress', 'gamipress' ) => array(
                'comments'            => __( 'Recount comments', 'gamipress' ),
                'published_content'      => __( 'Recount content publishing', 'gamipress' ),
            ),
        )
    );

    $meta_boxes['recount-activity'] = array(
        'title' => __( 'Recount Activity', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_recount_activity_tool_fields', array(
            'recount_activity_desc' => array(
                'content' => __( 'This tool will try to sync old activity with your already configured GamiPress install. GamiPress logs will be updated with all the activity stored in the database and the already configured points awards and achievements will be awarded too.', 'gamipress' )
                    . '<br>' . __( '<strong>Note:</strong> Some activity may not be possible to recount (like user log in or daily visits) because there are not registries stored in the database.', 'gamipress' ),
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
 * @since 1.1.8
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

    $response = array(
        'success' => true,
        'message' =>  __( 'Activity recount process has been done successfully.', 'gamipress' )
    );

    $activity = $_POST['activity'];

    /**
     * Hook to process activity recount
     *
     * @since 1.1.8
     *
     * @param array $response Response to return. array( success => true|false, message => string )
     */
    $response = apply_filters( "gamipress_activity_recount_$activity", $response );

    if( ! is_array( $response ) ) {
        wp_send_json_error( 'Activity recount process has failed!', 'gamipress' );
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
 * @since 1.1.8
 *
 * @param array $response
 *
 * @return array $response
 */
function gamipress_activity_recount_comments( $response ) {

    global $wpdb;

    // Get all stored users
    $users = $wpdb->get_results( "SELECT {$wpdb->users}.ID FROM {$wpdb->users}" );

    foreach( $users as $user ) {
        // Get all user approved comments
        $comments = $wpdb->get_results( $wpdb->prepare(
            "
            SELECT *
            FROM $wpdb->comments AS c
            WHERE c.user_id = %s
		       AND c.comment_approved = '1'
            ",
            $user->ID
        ) );

        foreach( $comments as $comment ) {
            // Trigger comment actions
            do_action( 'gamipress_specific_new_comment', (int) $comment->ID, (int) $comment->user_id, $comment->comment_post_ID, $comment );
            do_action( 'gamipress_new_comment', (int) $comment->ID, (int) $comment->user_id, $comment->comment_post_ID, $comment );
        }
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_comments', 'gamipress_activity_recount_comments' );

/**
 * Recount published content
 *
 * @since 1.1.8
 *
 * @param array $response
 *
 * @return array $response
 */
function gamipress_activity_recount_published_content( $response ) {

    global $wpdb;

    // Get all stored users
    $users = $wpdb->get_results( "SELECT {$wpdb->users}.ID FROM {$wpdb->users}" );

    foreach( $users as $user ) {
        // Get all user published posts
        $posts = $wpdb->get_results( $wpdb->prepare(
            "
            SELECT *
            FROM $wpdb->posts AS p
            WHERE p.post_author = %s
		       AND p.post_status = 'publish'
            ",
                $user->ID
        ) );

        foreach( $posts as $post ) {
            // Trigger content publishing action for each post
            do_action( "gamipress_publish_{$post->post_type}", $post->ID, $post->post_author, $post );
        }
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_published_content', 'gamipress_activity_recount_published_content' );