<?php
/**
 * Triggers
 *
 * @package GamiPress\wpDiscuz\Triggers
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
function gamipress_wpdiscuz_activity_triggers( $triggers ) {

    $triggers[__( 'wpDiscuz', 'gamipress' )] = array(

        // Vote Up
        'gamipress_wpdiscuz_vote_up'              => __( 'Vote up a comment', 'gamipress' ),
        'gamipress_wpdiscuz_get_vote_up'          => __( 'Get a vote up on a comment', 'gamipress' ),
        // Vote Down
        'gamipress_wpdiscuz_vote_down'            => __( 'Vote down a comment', 'gamipress' ),
        'gamipress_wpdiscuz_get_vote_down'        => __( 'Get a vote down on a comment', 'gamipress' ),

    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_wpdiscuz_activity_triggers' );

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
function gamipress_wpdiscuz_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Vote Up
        case 'gamipress_wpdiscuz_vote_up':
        case 'gamipress_wpdiscuz_get_vote_up':
        // Vote Down
        case 'gamipress_wpdiscuz_vote_down':
        case 'gamipress_wpdiscuz_get_vote_down':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_wpdiscuz_trigger_get_user_id', 10, 3 );

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
function gamipress_wpdiscuz_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Vote Up
        case 'gamipress_wpdiscuz_vote_up':
        // Vote Down
        case 'gamipress_wpdiscuz_vote_down':
            $log_meta['id'] = $args[0]; // Comment ID
            $log_meta['comment_author_id'] = $args[2]; // Comment author ID
            $log_meta['post_id'] = $args[3]; // Post ID
            $log_meta['vote'] = $args[4]; // Vote type (1 up, -1 down)
            break;
        // Get a vote up
        case 'gamipress_wpdiscuz_get_vote_up':
        // Get a vote down
        case 'gamipress_wpdiscuz_get_vote_down':
            $log_meta['id'] = $args[0]; // Comment ID
            $log_meta['voter_id'] = $args[2]; // Voter ID
            $log_meta['post_id'] = $args[3]; // Post ID
            $log_meta['vote'] = $args[4]; // Vote type (1 up, -1 down)
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_wpdiscuz_log_event_trigger_meta_data', 10, 5 );