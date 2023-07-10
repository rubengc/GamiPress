<?php
/**
 * Triggers
 *
 * @package GamiPress\The_Events_Calendar\Triggers
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
function gamipress_the_events_calendar_activity_triggers( $triggers ) {

    $event_post_type = ( class_exists( 'Tribe__Events__Main' ) ? Tribe__Events__Main::POSTTYPE : 'tribe_events' );

    $triggers[__( 'The Events Calendar', 'gamipress' )] = array(

        // Publishing
        'gamipress_publish_' . $event_post_type           => __( 'Publish a new event', 'gamipress' ), // Internal GamiPress listener
        'gamipress_delete_' . $event_post_type            => __( 'Delete an event', 'gamipress' ),     // Internal GamiPress listener
    );

    if( class_exists( 'Tribe__Tickets__Main' ) ) {
        $triggers[__( 'Event Tickets', 'gamipress' )] = array(
            // RSVP
            'gamipress_the_events_calendar_rsvp_event'                      => __( 'Confirm RSVP for an event', 'gamipress' ),
            'gamipress_the_events_calendar_rsvp_specific_event'             => __( 'Confirm RSVP for a specific event', 'gamipress' ),

            // Purchase tickets
            'gamipress_the_events_calendar_purchase_ticket'                 => __( 'Purchase a ticket for an event', 'gamipress' ),
            'gamipress_the_events_calendar_purchase_ticket_specific_event'  => __( 'Purchase a ticket for a specific event', 'gamipress' ),

            // Checkin
            'gamipress_the_events_calendar_checkin_event'                      => __( 'Check-in at an event', 'gamipress' ),
            'gamipress_the_events_calendar_checkin_specific_event'             => __( 'Check-in at specific event', 'gamipress' ),
        );
    }

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_the_events_calendar_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_the_events_calendar_specific_activity_triggers( $specific_activity_triggers ) {

    $event_post_type = ( class_exists( 'Tribe__Events__Main' ) ? Tribe__Events__Main::POSTTYPE : 'tribe_events' );

    $specific_activity_triggers['gamipress_the_events_calendar_rsvp_specific_event'] = array( $event_post_type );
    $specific_activity_triggers['gamipress_the_events_calendar_purchase_ticket_specific_event'] = array( $event_post_type );
    $specific_activity_triggers['gamipress_the_events_calendar_checkin_specific_event'] = array( $event_post_type );

    return $specific_activity_triggers;

}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_the_events_calendar_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_the_events_calendar_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_the_events_calendar_rsvp_specific_event'] = __( 'Confirm RSVP for %s event', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_the_events_calendar_purchase_ticket_specific_event'] = __( 'Purchase a ticket for %s event', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_the_events_calendar_checkin_specific_event'] = __( 'Check-in at %s event', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_the_events_calendar_specific_activity_trigger_label' );

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
function gamipress_the_events_calendar_trigger_get_user_id( $user_id, $trigger, $args ) {

    $event_post_type = ( class_exists( 'Tribe__Events__Main' ) ? Tribe__Events__Main::POSTTYPE : 'tribe_events' );

    switch ( $trigger ) {
        case 'gamipress_publish_' . $event_post_type: // Internal GamiPress listener
        case 'gamipress_delete_' . $event_post_type: // Internal GamiPress listener
        case 'gamipress_the_events_calendar_rsvp_event':
        case 'gamipress_the_events_calendar_rsvp_specific_event':
        case 'gamipress_the_events_calendar_purchase_ticket':
        case 'gamipress_the_events_calendar_purchase_ticket_specific_event':
        case 'gamipress_the_events_calendar_checkin_event':
        case 'gamipress_the_events_calendar_checkin_specific_event':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_the_events_calendar_trigger_get_user_id', 10, 3 );

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
function gamipress_the_events_calendar_specific_trigger_get_id( $specific_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_the_events_calendar_rsvp_specific_event':
        case 'gamipress_the_events_calendar_purchase_ticket_specific_event':
        case 'gamipress_the_events_calendar_checkin_specific_event':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;

}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_the_events_calendar_specific_trigger_get_id', 10, 3 );

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
function gamipress_the_events_calendar_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_the_events_calendar_rsvp_event':
        case 'gamipress_the_events_calendar_rsvp_specific_event':
        case 'gamipress_the_events_calendar_purchase_ticket':
        case 'gamipress_the_events_calendar_purchase_ticket_specific_event':
            // Add the event ID
            $log_meta['event_id'] = $args[0];
            $log_meta['post_id'] = $args[0]; // Set post ID as the event ID to being displayed on logs
            $log_meta['ticket_id'] = $args[2];
            break;
        case 'gamipress_the_events_calendar_checkin_event':
        case 'gamipress_the_events_calendar_checkin_specific_event':
            $log_meta['event_id'] = $args[0];
            $log_meta['post_id'] = $args[0]; // Set post ID as the event ID to being displayed on logs
            $log_meta['attendee_id'] = $args[2];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_the_events_calendar_log_event_trigger_meta_data', 10, 5 );