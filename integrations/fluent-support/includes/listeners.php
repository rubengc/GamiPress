<?php
/**
 * Listeners
 *
 * @package GamiPress\Fluent_Support\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * New ticket from backend listener
 *
 * @since 1.0.0
 *
 * @param array $ticket     Data opened ticket
 */
function gamipress_fluent_support_open_ticket( $ticket ) {

    global $wpdb;

    $ticket_id = absint( $ticket['id'] );

    if ( $ticket['source'] != NULL ){
        $user = get_user_by( 'email', $ticket['customer']['email']);

        // Bail if user not found
        if( ! $user ) {
            return;
        }

        // Trigger new ticket (client)
        do_action( 'gamipress_fluent_support_client_open_ticket', $ticket_id, $user->ID );
    } else {
        $user = wp_get_current_user();

        $agent = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}fs_persons WHERE email='{$user->user_email}'" );

        // Bail if agent not found
        if( ! $agent ) {
            return;
        }

        // Bail if person entry is not an agent
        if ( $agent->person_type !== 'agent' ){
            return;
        }

        // Trigger new ticket (agent)
        do_action( 'gamipress_fluent_support_agent_open_ticket', $ticket_id, $user->ID );
    }

}
add_action( 'fluent_support/ticket_created', 'gamipress_fluent_support_open_ticket' );

/**
 * Agent ticket reply listener
 *
 * @since 1.0.0
 *
 * @param array $response   Response data
 * @param array $ticket     Ticket data
 */
function gamipress_fluent_support_agent_reply_ticket( $ticket, $response ) {

    global $wpdb;

    $closed_by = absint( $ticket['closed_by'] );
    $customer_id = absint( $ticket['customer_id'] );
    $ticket_id = absint( $ticket['id'] );
    $response_id = absint( $response['id'] );

    $agent = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}fs_persons WHERE id={$closed_by}");

    // Bail if agent not found
    if( ! $agent ) {
        return;
    }

    // Bail if ticket was closed by the customer
    if ( $customer_id === $closed_by ){
        return;
    }

    $user_id = absint( $agent->user_id );

    // If not user ID assigned, try to find the user by email
    if( $user_id === 0 ) {
        $user = get_user_by( 'email', $agent->email );

        // Bail if user not found
        if( ! $user ) {
            return;
        }

        $user_id = $user->ID;
    }

    // Trigger reply ticket (agent)
    do_action( 'gamipress_fluent_support_agent_reply_ticket', $response_id, $user_id, $ticket_id );

}
add_action( 'fluent_support/response_added_by_agent', 'gamipress_fluent_support_agent_reply_ticket', 10, 2 );

/**
 * Client ticket reply listener
 *
 * @since 1.0.0
 *
 * @param array $response   Replies data
 * @param array $ticket     Tickets data
 */
function gamipress_fluent_support_client_reply_ticket( $response, $ticket ) {

    $ticket_id = absint( $ticket['id'] );
    $response_id = absint( $response['id'] );
    $user = get_user_by( 'email', $ticket['customer']['email']);

    // Bail if user not found
    if( ! $user ) {
        return;
    }

    // Trigger reply ticket (agent)
    do_action( 'gamipress_fluent_support_client_reply_ticket', $response_id, $user->ID, $ticket_id );

}
add_action( 'fluent_support/response_added_by_customer', 'gamipress_fluent_support_client_reply_ticket', 10, 2 );

/**
 * Ticket closed listener
 *
 * @since 1.0.0
 *
 * @param array $ticket Ticket data
 */
function gamipress_fluent_support_close_ticket( $ticket ) {

    global $wpdb;

    $ticket_id = absint( $ticket['id'] );
    $closed_by = absint( $ticket['closed_by'] );
    $customer_id = absint( $ticket['customer_id'] );

    // Check if ticket was closed by the client
    if ( $customer_id === $closed_by ){

        $user = get_user_by( 'email', $ticket['customer']['email']);

        // Bail if user not found
        if( ! $user ) {
            return;
        }

        // Trigger close ticket (client)
        do_action( 'gamipress_fluent_support_client_close_ticket', $ticket_id, $user->ID );
    } else {

        $agent_id = absint( $ticket['agent_id'] );

        // Bail if agent not assigned
        if( $agent_id === 0 ) {
            return;
        }

        $agent = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}fs_persons WHERE id={$agent_id}");

        // Bail if agent not found
        if( ! $agent ) {
            return;
        }

        $user_id = absint( $agent->user_id );

        // If not user ID assigned, try to find the user by email
        if( $user_id === 0 ) {
            $user = get_user_by( 'email', $agent->email );

            // Bail if user not found
            if( ! $user ) {
                return;
            }

            $user_id = $user->ID;
        }

        // Trigger close ticket (agent)
        do_action( 'gamipress_fluent_support_agent_close_ticket', $ticket_id, $user_id );

    }

}
add_action( 'fluent_support/ticket_closed', 'gamipress_fluent_support_close_ticket', 10, 3 );
