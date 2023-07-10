<?php
/**
 * Triggers
 *
 * @package GamiPress\WP_Ulike\Triggers
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin triggers
 *
 * @since  1.0.0
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_wp_ulike_activity_triggers( $triggers ) {

    $triggers[__( 'WP Ulike', 'gamipress' )] = array(

        // Like
        'gamipress_wp_ulike_like'               => __( 'Like anything', 'gamipress' ),
        'gamipress_wp_ulike_post_like'          => __( 'Like a post', 'gamipress' ),
        'gamipress_wp_ulike_post_type_like'     => __( 'Like a post of a type', 'gamipress' ),
        'gamipress_wp_ulike_comment_like'       => __( 'Like a comment', 'gamipress' ),
        // Like (Author)
        'gamipress_wp_ulike_get_like'           => __( 'Get a like anywhere', 'gamipress' ),
        'gamipress_wp_ulike_get_post_like'      => __( 'Get a like on a post', 'gamipress' ),
        'gamipress_wp_ulike_get_post_type_like' => __( 'Get a like on a post of a type', 'gamipress' ),
        'gamipress_wp_ulike_get_comment_like'   => __( 'Get a like on a comment', 'gamipress' ),
        // Unlike
        'gamipress_wp_ulike_unlike'               => __( 'Unlike anything', 'gamipress' ),
        'gamipress_wp_ulike_post_unlike'          => __( 'Unlike a post', 'gamipress' ),
        'gamipress_wp_ulike_comment_unlike'       => __( 'Unlike a comment', 'gamipress' ),
        // Unlike (Author)
        'gamipress_wp_ulike_get_unlike'           => __( 'Get an unlike anywhere', 'gamipress' ),
        'gamipress_wp_ulike_get_post_unlike'      => __( 'Get an unlike on a post', 'gamipress' ),
        'gamipress_wp_ulike_get_comment_unlike'   => __( 'Get an unlike on a comment', 'gamipress' ),

    );

    // WP Ulike + BuddyPress
    if ( class_exists( 'BuddyPress' ) ) {
        $triggers[__( 'WP Ulike and BuddyPress', 'gamipress' )] = array(

            // Like
            'gamipress_wp_ulike_activity_like'      => __( 'Like an activity', 'gamipress' ),
            // Like (Author)
            'gamipress_wp_ulike_get_activity_like'  => __( 'Get a like on an activity', 'gamipress' ),
            // Unlike
            'gamipress_wp_ulike_activity_unlike'      => __( 'Unlike an activity', 'gamipress' ),
            // Unlike (Author)
            'gamipress_wp_ulike_get_activity_unlike'  => __( 'Get an unlike on an activity', 'gamipress' ),

        );
    }

    // WP Ulike + bbPress
    if ( class_exists( 'bbPress' ) ) {
        $triggers[__( 'WP Ulike and bbPress', 'gamipress' )] = array(

            // Like
            'gamipress_wp_ulike_topic_like'      => __( 'Like a topic', 'gamipress' ),
            // Like (Author)
            'gamipress_wp_ulike_get_topic_like'  => __( 'Get a like on a topic', 'gamipress' ),
            // Unlike
            'gamipress_wp_ulike_topic_unlike'      => __( 'Unlike a topic', 'gamipress' ),
            // Unlike (Author)
            'gamipress_wp_ulike_get_topic_unlike'  => __( 'Get an unlike on a topic', 'gamipress' ),

        );
    }

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_wp_ulike_activity_triggers' );

/**
 * Get user for a given trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $user_id user ID to override.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 * @return integer          User ID.
 */
function gamipress_wp_ulike_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Like
        case 'gamipress_wp_ulike_like':
        case 'gamipress_wp_ulike_post_like':
        case 'gamipress_wp_ulike_post_type_like':
        case 'gamipress_wp_ulike_comment_like':
        // Like (Author)
        case 'gamipress_wp_ulike_get_like':
        case 'gamipress_wp_ulike_get_post_like':
        case 'gamipress_wp_ulike_get_post_type_like':
        case 'gamipress_wp_ulike_get_comment_like':
        // Unlike
        case 'gamipress_wp_ulike_unlike':
        case 'gamipress_wp_ulike_post_unlike':
        case 'gamipress_wp_ulike_comment_unlike':
        // Unlike (Author)
        case 'gamipress_wp_ulike_get_unlike':
        case 'gamipress_wp_ulike_get_post_unlike':
        case 'gamipress_wp_ulike_get_comment_unlike':
        // BuddyPress
        case 'gamipress_wp_ulike_activity_like':
        case 'gamipress_wp_ulike_get_activity_like':
        case 'gamipress_wp_ulike_activity_unlike':
        case 'gamipress_wp_ulike_get_activity_unlike':
        // bbPress
        case 'gamipress_wp_ulike_topic_like':
        case 'gamipress_wp_ulike_get_topic_like':
        case 'gamipress_wp_ulike_topic_unlike':
        case 'gamipress_wp_ulike_get_topic_unlike':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_wp_ulike_trigger_get_user_id', 10, 3 );

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.1
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_wp_ulike_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Like
        case 'gamipress_wp_ulike_like':
        case 'gamipress_wp_ulike_post_like':
        case 'gamipress_wp_ulike_post_type_like':
        case 'gamipress_wp_ulike_comment_like':
        // Unlike
        case 'gamipress_wp_ulike_unlike':
        case 'gamipress_wp_ulike_post_unlike':
        case 'gamipress_wp_ulike_comment_unlike':
        // BuddyPress
        case 'gamipress_wp_ulike_activity_like':
        case 'gamipress_wp_ulike_activity_unlike':
        // bbPress
        case 'gamipress_wp_ulike_topic_like':
        case 'gamipress_wp_ulike_topic_unlike':
            // Add object ID
            $log_meta['id'] = $args[0];
            break;

        // Like (Author)
        case 'gamipress_wp_ulike_get_like':
        case 'gamipress_wp_ulike_get_post_like':
        case 'gamipress_wp_ulike_get_post_type_like':
        case 'gamipress_wp_ulike_get_comment_like':
        // Unlike (Author)
        case 'gamipress_wp_ulike_get_unlike':
        case 'gamipress_wp_ulike_get_post_unlike':
        case 'gamipress_wp_ulike_get_comment_unlike':
        // BuddyPress
        case 'gamipress_wp_ulike_get_activity_like':
        case 'gamipress_wp_ulike_get_activity_unlike':
        // bbPress
        case 'gamipress_wp_ulike_get_topic_like':
        case 'gamipress_wp_ulike_get_topic_unlike':
            // Add object ID and the user that perform the like/unlike (on author events)
            $log_meta['id'] = $args[0];
            $log_meta['liker_id'] = $args[2];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_wp_ulike_log_event_trigger_meta_data', 10, 5 );

/**
 * Build custom activity trigger label
 *
 * @param string    $title
 * @param integer   $requirement_id
 * @param array     $requirement
 *
 * @return string
 */
function gamipress_wp_ulike_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $post_types = get_post_types( array( 'public' => true ), 'objects' );
    $post_type = ( isset( $requirement['wp_ulike_post_type'] ) ) ? $requirement['wp_ulike_post_type'] : '';

    switch( $requirement['trigger_type'] ) {
        // Like post type
        case 'gamipress_wp_ulike_post_type_like':
            if( $post_type === '' ) {
                return __( 'Like a post of a type', 'gamipress' );
            } else {
                return sprintf( __( 'Like a post of a %s type', 'gamipress' ), $post_type );

            }
            break;
        // Get like post type
        case 'gamipress_wp_ulike_get_post_type_like':
            if( $post_type === '' ) {
                return __( 'Get a like on a post of a type', 'gamipress' );
            } else {
                return sprintf( __( 'Get a like on a post of %s type', 'gamipress' ), $post_type );

            }
            break;

    }

    return $title;

}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_wp_ulike_activity_trigger_label', 10, 3 );