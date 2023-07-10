<?php
/**
 * Listeners
 *
 * @package GamiPress\Awesome_Support\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * New ticket from backend listener
 *
 * @since 1.0.0
 *
 * @param int   $ticket_id Ticket ID
 */
function gamipress_awesome_support_open_ticket_admin( $ticket_id ) {

    $client_id = get_post_field( 'post_author', $ticket_id );
    $agent_id = intval( get_post_meta( $ticket_id, '_wpas_assignee', true ) );

    // Trigger new ticket (agent)
    do_action( 'gamipress_awesome_support_agent_open_ticket', $ticket_id, $agent_id );

    // Trigger new ticket (client)
    do_action( 'gamipress_awesome_support_client_open_ticket', $ticket_id, $client_id );

}
add_action( 'wpas_post_new_ticket_admin', 'gamipress_awesome_support_open_ticket_admin' );

/**
 * New ticket listener
 *
 * @since 1.0.0
 *
 * @param int   $ticket_id  Ticket ID
 * @param array $data       Data to be inserted as the ticket
 */
function gamipress_awesome_support_open_ticket( $ticket_id, $data ) {

    // If the ID is set it means that is updating a post and NOT creating it.
    if ( isset( $data['ID'] ) ) {
        return;
    }

    $client_id = get_post_field( 'post_author', $ticket_id );
    $agent_id = intval( get_post_meta( $ticket_id, '_wpas_assignee', true ) );

    // Trigger new ticket (agent)
    do_action( 'gamipress_awesome_support_agent_open_ticket', $ticket_id, $agent_id );

    // Trigger new ticket (client)
    do_action( 'gamipress_awesome_support_client_open_ticket', $ticket_id, $client_id );

}
add_action( 'wpas_open_ticket_after', 'gamipress_awesome_support_open_ticket', 10, 2 );

/**
 * Ticket reply listener
 *
 * @since 1.0.0
 *
 * @param int   $reply_id   Reply ID
 * @param array $data       Data to be inserted as the reply
 */
function gamipress_awesome_support_ticket_reply( $reply_id, $data ) {

    // If the ID is set it means that is updating a post and NOT creating it.
    if ( isset( $data['ID'] ) ) {
        return;
    }

    $user_id = absint( $data['post_author'] );

    $action = user_can( $user_id, 'edit_ticket' ) ? 'agent_reply_ticket' : 'client_reply_ticket';

    // Trigger reply ticket (agent or client)
    do_action( "gamipress_awesome_support_{$action}", $reply_id, $user_id );

}
add_action( 'wpas_add_reply_complete', 'gamipress_awesome_support_ticket_reply', 10, 2 );

/**
 * Ticket closed listener
 *
 * @since 1.0.0
 *
 * @param integer $ticket_id ID of the ticket we just closed
 * @param boolean $update    True on success, false on fialure
 * @param integer $user_id   ID of the user who did the action
 */
function gamipress_awesome_support_close_ticket( $ticket_id, $update, $user_id ) {

    $client_id = get_post_field( 'post_author', $ticket_id );
    $agent_id = intval( get_post_meta( $ticket_id, '_wpas_assignee', true ) );

    // Trigger close ticket (agent)
    do_action( 'gamipress_awesome_support_agent_close_ticket', $ticket_id, $agent_id );

    // Trigger close ticket (client)
    do_action( 'gamipress_awesome_support_client_close_ticket', $ticket_id, $client_id );

}
add_action( 'wpas_after_close_ticket', 'gamipress_awesome_support_close_ticket', 10, 3 );
