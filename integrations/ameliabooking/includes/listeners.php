<?php
/**
 * Listeners
 *
 * @package GamiPress\AmeliaBooking\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Book appointment listener
 *
 * @since 1.0.0
 *
 * @param array $args   Appointment data
 */
function gamipress_ameliabooking_book_appointment( $args ) {

    $user_id = get_current_user_id();

    // Bail if the booking is not for an appointment
    if ( $args['type'] !== 'appointment'){
        return;
    }

    $appointment_id = absint( $args['appointment']['id'] );
    $service_id = absint( $args['appointment']['serviceId'] );

    // Book any service
    do_action( 'gamipress_ameliabooking_user_books_appointment', $appointment_id, $user_id, $service_id );

    // Book specific service
    do_action( 'gamipress_ameliabooking_user_books_appointment_service', $appointment_id, $user_id, $service_id );

}
add_action( 'AmeliaBookingAddedBeforeNotify', 'gamipress_ameliabooking_book_appointment' );

/**
 * Book event listener
 *
 * @since 1.0.0
 *
 * @param array $args   Event data
 */
function gamipress_ameliabooking_book_event( $args ) {

    $user_id = get_current_user_id();

    // Bail if the booking is not for an appointment
    if ( $args['type'] !== 'event'){
        return;
    }

    $event_id = absint( $args['event']['id'] );

    // Book any service
    do_action( 'gamipress_ameliabooking_user_books_event', $event_id, $user_id );

    // Book specific service
    do_action( 'gamipress_ameliabooking_user_books_specific_event', $event_id, $user_id );

}
add_action( 'AmeliaBookingAddedBeforeNotify', 'gamipress_ameliabooking_book_event' );
