<?php
/**
 * Listeners
 *
 * @package GamiPress\Modern_Events_Calendar\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Booking listener
 *
 * @since 1.0.0
 *
 * @param int $book_id ID of the booking
 */
function gamipress_modern_events_calendar_booking_listener( $book_id ) {

    $event_id = absint( get_post_meta( $book_id, 'mec_event_id', true ) );
    $attendees = get_post_meta( $book_id, 'mec_attendees', true );

    $status = 'completed';

    if( current_filter() === 'mec_booking_pended' ) {
        $status = 'pending';
    } else if( current_filter() === 'mec_booking_canceled' ) {
        $status = 'cancelled';
    }

    foreach( $attendees as $attendee ) {

        $user = get_user_by( 'email', $attendee['email'] );

        // Skip if user not registered
        if( ! $user ) {
            continue;
        }

        // Trigger booking is completed
        do_action( "gamipress_modern_events_calendar_booking_{$status}", $event_id, $user->ID );

        // Trigger booking is completed specific event
        do_action( "gamipress_modern_events_calendar_booking_{$status}_specific_event", $event_id, $user->ID );

    }

}
add_action( 'mec_booking_completed', 'gamipress_modern_events_calendar_booking_listener' );
add_action( 'mec_booking_pended', 'gamipress_modern_events_calendar_booking_listener' );
add_action( 'mec_booking_canceled', 'gamipress_modern_events_calendar_booking_listener' );