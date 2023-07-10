<?php
/**
 * Triggers
 *
 * @package GamiPress\Fluent_Support\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin activity triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_fluent_support_activity_triggers( $triggers ) {

    // Fluent Support
    // Agent
    $triggers[__( 'Fluent Support - Events for agents', 'gamipress' )] = array(
        'gamipress_fluent_support_agent_open_ticket'           => __( 'Open a new ticket', 'gamipress' ),
        'gamipress_fluent_support_agent_reply_ticket'          => __( 'Reply to ticket', 'gamipress' ),
        'gamipress_fluent_support_agent_close_ticket'          => __( 'Close a ticket', 'gamipress' ),
    );

    // Client
    $triggers[__( 'Fluent Support - Events for clients', 'gamipress' )] = array(
        'gamipress_fluent_support_client_open_ticket'          => __( 'Open a new ticket', 'gamipress' ),
        'gamipress_fluent_support_client_reply_ticket'         => __( 'Reply to ticket', 'gamipress' ),
        'gamipress_fluent_support_client_close_ticket'         => __( 'Close a ticket', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_fluent_support_activity_triggers' );

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
function gamipress_fluent_support_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Agent
        case 'gamipress_fluent_support_agent_open_ticket':
        case 'gamipress_fluent_support_agent_reply_ticket':
        case 'gamipress_fluent_support_agent_close_ticket':
        // Client
        case 'gamipress_fluent_support_client_open_ticket':
        case 'gamipress_fluent_support_client_reply_ticket':
        case 'gamipress_fluent_support_client_close_ticket':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_fluent_support_trigger_get_user_id', 10, 3 );

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.2
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_fluent_support_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Agent
        case 'gamipress_fluent_support_agent_open_ticket':
        case 'gamipress_fluent_support_agent_reply_ticket':
        case 'gamipress_fluent_support_agent_close_ticket':
        // Client
        case 'gamipress_fluent_support_client_open_ticket':
        case 'gamipress_fluent_support_client_reply_ticket':
        case 'gamipress_fluent_support_client_close_ticket':
            // Add the post ID
            $log_meta['post_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_fluent_support_log_event_trigger_meta_data', 10, 5 );

/**
 * Extra filter to check duplicated activity
 *
 * @since 1.0.2
 *
 * @param bool 		$return
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return bool					True if user deserves trigger, else false
 */
function gamipress_fluent_support_trigger_duplicity_check( $return, $user_id, $trigger, $site_id, $args  ) {

    // If user doesn't deserves trigger, then bail to prevent grant access
    if( ! $return )
        return $return;

    $log_meta = array(
        'type' => 'event_trigger',
        'trigger_type' => $trigger,
    );

    switch ( $trigger ) {
        // Agent
        case 'gamipress_fluent_support_agent_open_ticket':
        case 'gamipress_fluent_support_agent_reply_ticket':
        case 'gamipress_fluent_support_agent_close_ticket':
        // Client
        case 'gamipress_fluent_support_client_open_ticket':
        case 'gamipress_fluent_support_client_reply_ticket':
        case 'gamipress_fluent_support_client_close_ticket':
            // Prevent duplicated tickets/replies
            $log_meta['post_id'] = gamipress_get_event_arg( $args, 'post_id', 0 );
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
    }

    return $return;

}
add_filter( 'gamipress_user_deserves_trigger', 'gamipress_fluent_support_trigger_duplicity_check', 10, 5 );