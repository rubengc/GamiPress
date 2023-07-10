<?php
/**
 * Listeners
 *
 * @package GamiPress\The_Events_Calendar\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * After saving a ticket listener
 *
 * @since 1.0.0
 *
 * @param TECTicketsCommerceStatusStatus_Interface      $new_status     New post status
 * @param TECTicketsCommerceStatusStatus_Interface|null $old_status     Old post status
 * @param WP_Post   $post   Post object
 */
function gamipress_the_events_calendar_after_save_ticket_listener( $new_status, $old_status, $post ) {

    $user_id = get_current_user_id();
    $order_data = tec_tc_get_order( $post );

    // Bail if order is not completed
    if ( $order_data->post_status !== 'tec-tc-completed' ) {
        return;
    }

    

    foreach( $order_data->items as $ticket_id => $value ) {

        $event_id = $value['event_id'];

        for ( $i = absint( $value['quantity'] ); $i > 0; $i -= 1 ){

            if( $class === 'Tribe__Tickets__RSVP' && $attendee['order_status'] === 'yes' ) {
                // RSVP

                // RSVP an event
                do_action( 'gamipress_the_events_calendar_rsvp_event', $event_id, $user_id, $ticket_id );

                // RSVP a specific event
                do_action( 'gamipress_the_events_calendar_rsvp_specific_event', $event_id, $user_id, $ticket_id );
            } else {
                // Ticket was bought

                // Purchase a ticket for an event
                do_action( 'gamipress_the_events_calendar_purchase_ticket', $event_id, $user_id, $ticket_id );

                // Purchase a ticket for a specific event
                do_action( 'gamipress_the_events_calendar_purchase_ticket_specific_event', $event_id, $user_id, $ticket_id );
            }

        }

    }

}
add_action( 'tec_tickets_commerce_order_status_flag_complete', 'gamipress_the_events_calendar_after_save_ticket_listener', 10, 3 );

/**
 * Action fired when an RSVP has had attendee tickets generated for it
 *
 * @param int    $product_id   RSVP ticket post ID
 * @param string $order_id     ID (hash) of the RSVP order
 * @param int    $qty          Quantity ordered
 */
function gamipress_the_events_calendar_tickets_generated_listener( $product_id, $order_id, $qty ) {

    $attendees = tribe_tickets_get_attendees( $order_id, 'rsvp_order' );

    if ( empty( $attendees ) ) {
        return;
    }

    foreach ( $attendees as $attendee ) {

        $user_id  = absint( $attendee['user_id'] );
        $event_id = absint( $attendee['event_id'] );
        $ticket_id = absint( $attendee['ticket_id'] );

        if( $attendee['order_status'] === 'yes' ) {

            // RSVP an event
            do_action( 'gamipress_the_events_calendar_rsvp_event', $event_id, $user_id, $ticket_id );

            // RSVP a specific event
            do_action( 'gamipress_the_events_calendar_rsvp_specific_event', $event_id, $user_id, $ticket_id );

        }

    }

}
add_action( 'event_tickets_rsvp_tickets_generated_for_product', 'gamipress_the_events_calendar_tickets_generated_listener', 10, 3);
add_action( 'event_tickets_woocommerce_tickets_generated_for_product', 'gamipress_the_events_calendar_tickets_generated_listener', 10, 3);
add_action( 'event_tickets_tpp_tickets_generated_for_product', 'gamipress_the_events_calendar_tickets_generated_listener', 10, 3);

/**
 * Checkin listener
 *
 * @since 1.0.0
 *
 * @param int       $attendee_id    Attendee post ID
 * @param bool|null $qr             true if from QR checkin process
 */
function gamipress_the_events_calendar_checkin_listener( $attendee_id, $qr ) {

    if ( ! $attendee_id ) {
        return;
    }

    $attendees = tribe_tickets_get_attendees( $attendee_id, 'rsvp_order' );

    if ( empty( $attendees ) ) {
        return;
    }

    foreach ( $attendees as $attendee ) {

        $user_id  = absint( $attendee['user_id'] );
        $event_id = absint( $attendee['event_id'] );

        // Check-in at an event
        do_action( 'gamipress_the_events_calendar_checkin_event', $event_id, $user_id, $attendee_id, $qr );

        // Check-in at a specific event
        do_action( 'gamipress_the_events_calendar_checkin_specific_event', $event_id, $user_id, $attendee_id, $qr );

    }

}
add_action( 'event_tickets_checkin', 'gamipress_the_events_calendar_checkin_listener', 10, 2 );
add_action( 'rsvp_checkin', 'gamipress_the_events_calendar_checkin_listener', 10, 2 );
add_action( 'eddtickets_checkin', 'gamipress_the_events_calendar_checkin_listener', 10, 2 );
add_action( 'wootickets_checkin', 'gamipress_the_events_calendar_checkin_listener', 10, 2 );