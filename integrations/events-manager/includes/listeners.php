<?php
/**
 * Listeners
 *
 * @package GamiPress\Events_Manager\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Booking status listener
 *
 * @since 1.0.0
 *
 * @param bool $result
 * @param EM_Booking $booking
 *
 * @return mixed
 */
function gamipress_events_manager_booking_status_listener( $result, $booking ) {

    $event_id = $booking->event_id;
    $user_id = $booking->person_id;

    $status = $booking->booking_status;
    $prev_status = $booking->previous_status;
    $bookings_approval = get_option('dbem_bookings_approval');
    $rejected_statuses = array( 0, 2, 3 );

    if ( $status == 1 || ( ! $bookings_approval && $status < 2) ) {
        // If the new status is 'approved', then trigger events

        // Trigger gamipress_events_manager_new_booking event
        do_action( 'gamipress_events_manager_new_booking', $event_id, $user_id, $booking );

        // Trigger gamipress_events_manager_new_specific_booking event
        do_action( 'gamipress_events_manager_new_specific_booking', $event_id, $user_id, $booking );

    } else if ( ( $prev_status == 1 || ( ! $bookings_approval && $prev_status < 2 ) ) && in_array( $status, $rejected_statuses ) ) {
        // Else if status got changed from previously 'approved', then trigger cancel attendance event

        // Trigger gamipress_events_manager_cancel_booking event
        do_action( 'gamipress_events_manager_cancel_booking', $event_id, $user_id, $booking );

        // Trigger gamipress_events_manager_cancel_specific_booking event
        do_action( 'gamipress_events_manager_cancel_specific_booking', $event_id, $user_id, $booking );
    }

    return $result;

}
add_filter( 'em_booking_set_status', 'gamipress_events_manager_booking_status_listener', 100, 2 );
//add_filter( 'em_booking_save', 'gamipress_events_manager_booking_status_listener', 100, 2 );

/**
 * Booking status listener
 *
 * @since 1.0.0
 *
 * @param bool $result
 * @param EM_Booking $booking
 *
 * @return mixed
 */
function gamipress_events_manager_new_booking_listener( $result, $booking ) {

    $event_id = $booking->event_id;
    $user_id = $booking->person_id;

    $status = $booking->booking_status;
    $prev_status = $booking->previous_status;
    $bookings_approval = get_option('dbem_bookings_approval');
    $rejected_statuses = array( 0, 2, 3 );

    if ( $status == 1 && ( ! $bookings_approval && $status < 2) && $prev_status !== $status) {
        // If the new status is 'approved', then trigger events

        // Trigger gamipress_events_manager_new_booking event
        do_action( 'gamipress_events_manager_new_booking', $event_id, $user_id, $booking );

        // Trigger gamipress_events_manager_new_specific_booking event
        do_action( 'gamipress_events_manager_new_specific_booking', $event_id, $user_id, $booking );

    } 

    return $result;

}

add_filter( 'em_booking_save', 'gamipress_events_manager_new_booking_listener', 100, 2 );
