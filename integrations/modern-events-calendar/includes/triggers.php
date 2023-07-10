<?php
/**
 * Triggers
 *
 * @package GamiPress\Modern_Events_Calendar\Triggers
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
function gamipress_modern_events_calendar_activity_triggers( $triggers ) {

    // Modern Events Calendar
    $triggers[__( 'Modern Events Calendar', 'gamipress' )] = array(
        'gamipress_modern_events_calendar_booking_completed'                => __( 'Complete booking for any event', 'gamipress' ),
        'gamipress_modern_events_calendar_booking_completed_specific_event' => __( 'Complete booking for a specific event', 'gamipress' ),
        'gamipress_modern_events_calendar_booking_pending'                  => __( 'Pending booking for any event', 'gamipress' ),
        'gamipress_modern_events_calendar_booking_pending_specific_event'   => __( 'Pending booking for a specific event', 'gamipress' ),
        'gamipress_modern_events_calendar_booking_cancelled'                => __( 'Cancel booking for any event', 'gamipress' ),
        'gamipress_modern_events_calendar_booking_cancelled_specific_event' => __( 'Cancel booking for a specific event', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_modern_events_calendar_activity_triggers' );

/**
 * Register specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_modern_events_calendar_specific_activity_triggers( $specific_activity_triggers ) {

    // Purchase
    $specific_activity_triggers['gamipress_modern_events_calendar_booking_completed_specific_event'] = array( 'mec-events' );
    $specific_activity_triggers['gamipress_modern_events_calendar_booking_pending_specific_event'] = array( 'mec-events' );
    $specific_activity_triggers['gamipress_modern_events_calendar_booking_cancelled_specific_event'] = array( 'mec-events' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_modern_events_calendar_specific_activity_triggers' );

/**
 * Register specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_modern_events_calendar_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Purchase
    $specific_activity_trigger_labels['gamipress_modern_events_calendar_booking_completed_specific_event'] = __( 'Complete booking for %s event', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_modern_events_calendar_booking_pending_specific_event'] = __( 'Pending booking for %s event', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_modern_events_calendar_booking_cancelled_specific_event'] = __( 'Cancel booking for %s event', 'gamipress' );
    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_modern_events_calendar_specific_activity_trigger_label' );

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
function gamipress_modern_events_calendar_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_modern_events_calendar_booking_completed':
        case 'gamipress_modern_events_calendar_booking_completed_specific_event':
        case 'gamipress_modern_events_calendar_booking_pending':
        case 'gamipress_modern_events_calendar_booking_pending_specific_event':
        case 'gamipress_modern_events_calendar_booking_cancelled':
        case 'gamipress_modern_events_calendar_booking_cancelled_specific_event':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_modern_events_calendar_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $specific_id Specific ID to override.
 * @param  string  $trigger     Trigger name.
 * @param  array   $args        Passed trigger args.
 *
 * @return integer              Specific ID.
 */
function gamipress_modern_events_calendar_specific_trigger_get_id( $specific_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_modern_events_calendar_booking_completed_specific_event':
        case 'gamipress_modern_events_calendar_booking_pending_specific_event':
        case 'gamipress_modern_events_calendar_booking_cancelled_specific_event':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;

}

add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_modern_events_calendar_specific_trigger_get_id', 10, 3 );

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
function gamipress_modern_events_calendar_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_modern_events_calendar_booking_completed':
        case 'gamipress_modern_events_calendar_booking_completed_specific_event':
        case 'gamipress_modern_events_calendar_booking_pending':
        case 'gamipress_modern_events_calendar_booking_pending_specific_event':
        case 'gamipress_modern_events_calendar_booking_cancelled':
        case 'gamipress_modern_events_calendar_booking_cancelled_specific_event':
            // Add the post ID
            $log_meta['post_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_modern_events_calendar_log_event_trigger_meta_data', 10, 5 );