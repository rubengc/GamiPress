<?php
/**
 * Triggers
 *
 * @package GamiPress\Jetpack\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin activity triggers
 *
 * @since   1.0.0
 *
 * @param   array $triggers
 * @return  mixed
 */
function gamipress_jetpack_activity_triggers( $triggers ) {

    $triggers[__( 'Jetpack', 'gamipress' )] = array(
        'gamipress_jetpack_site_subscription'       => __( 'Site subscription', 'gamipress' ),
        'gamipress_jetpack_comment_subscription'    => __( 'Comment subscription', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_jetpack_activity_triggers' );

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
function gamipress_jetpack_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_jetpack_site_subscription':
        case 'gamipress_jetpack_comment_subscription':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_jetpack_trigger_get_user_id', 10, 3 );

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.0
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_jetpack_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_jetpack_site_subscription':
            // Add the blog ID
            $log_meta['blog_id'] = $args[0];
            break;
        case 'gamipress_jetpack_comment_subscription':
            // Add the post ID
            $log_meta['post_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_jetpack_log_event_trigger_meta_data', 10, 5 );