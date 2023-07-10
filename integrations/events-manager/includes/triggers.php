<?php
/**
 * Triggers
 *
 * @package GamiPress\Events_Manager\Triggers
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
function gamipress_events_manager_activity_triggers( $triggers ) {

    if( ! defined('EM_POST_TYPE_EVENT') ) define( 'EM_POST_TYPE_EVENT', 'event' );

    $triggers[__( 'Events Manager', 'gamipress' )] = array(

        // Publishing
        'gamipress_publish_' . EM_POST_TYPE_EVENT           => __( 'Publish a new event', 'gamipress' ), // Internal GamiPress listener
        'gamipress_delete_' . EM_POST_TYPE_EVENT            => __( 'Delete an event', 'gamipress' ),     // Internal GamiPress listener

        // Attend
        'gamipress_events_manager_new_booking'              => __( 'Attend an event', 'gamipress' ),
        'gamipress_events_manager_new_specific_booking'     => __( 'Attend a specific event', 'gamipress' ),

        // Cancel attend
        'gamipress_events_manager_cancel_booking'           => __( 'Cancel attendance to an event', 'gamipress' ),
        'gamipress_events_manager_cancel_specific_booking'  => __( 'Cancel attendance to a specific event', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_events_manager_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_events_manager_specific_activity_triggers( $specific_activity_triggers ) {

    if( ! defined('EM_POST_TYPE_EVENT') ) define( 'EM_POST_TYPE_EVENT', 'event' );

    $specific_activity_triggers['gamipress_events_manager_new_specific_booking'] = array( EM_POST_TYPE_EVENT );
    $specific_activity_triggers['gamipress_events_manager_cancel_specific_booking'] = array( EM_POST_TYPE_EVENT );

    return $specific_activity_triggers;

}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_events_manager_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_events_manager_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_events_manager_new_specific_booking'] = __( 'Attend to %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_events_manager_cancel_specific_booking'] = __( 'Cancel attendance to %s', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_events_manager_specific_activity_trigger_label' );

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
function gamipress_events_manager_trigger_get_user_id( $user_id, $trigger, $args ) {

    if( ! defined('EM_POST_TYPE_EVENT') ) define( 'EM_POST_TYPE_EVENT', 'event' );

    switch ( $trigger ) {
        case 'gamipress_publish_' . EM_POST_TYPE_EVENT: // Internal GamiPress listener
        case 'gamipress_delete_' . EM_POST_TYPE_EVENT: // Internal GamiPress listener
        case 'gamipress_events_manager_new_booking':
        case 'gamipress_events_manager_new_specific_booking':
        case 'gamipress_events_manager_cancel_booking':
        case 'gamipress_events_manager_cancel_specific_booking':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_events_manager_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.1
 *
 * @param  integer $specific_id Specific ID to override.
 * @param  string  $trigger     Trigger name.
 * @param  array   $args        Passed trigger args.
 *
 * @return integer              Specific ID.
 */
function gamipress_events_manager_specific_trigger_get_id( $specific_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_events_manager_new_specific_booking':
        case 'gamipress_events_manager_cancel_specific_booking':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;

}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_events_manager_specific_trigger_get_id', 10, 3 );

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
function gamipress_events_manager_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_events_manager_new_booking':
        case 'gamipress_events_manager_new_specific_booking':
        case 'gamipress_events_manager_cancel_booking':
        case 'gamipress_events_manager_cancel_specific_booking':
            // Add the event ID
            $log_meta['event_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_events_manager_log_event_trigger_meta_data', 10, 5 );