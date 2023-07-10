<?php
/**
 * Listeners
 *
 * @package GamiPress\Invite_Anyone\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Send invite listener
 *
 * @since 1.0.0
 *
 * @param int       $user_id
 * @param string    $email
 * @param array     $group
 */
function gamipress_invite_anyone_send_invite_listener( $user_id, $email, $group )  {

    // Trigger send an invitation event
    do_action( 'gamipress_invite_anyone_send_invite', $user_id, $email, $group );

}
add_action( 'sent_email_invite', 'gamipress_invite_anyone_send_invite_listener', 10, 3 );

/**
 * Accept invite listener
 *
 * @since 1.0.0
 *
 * @param int   $user_id    Invited user ID
 * @param array $inviters   Array of user IDs that invited the user
 */
function gamipress_invite_anyone_accept_invite_listener( $user_id, $inviters ) {

    if ( empty( $inviters ) ) return;

    // Invite Anyone will pass on an array of user IDs of those who have invited this user which we need to loop though
    foreach ( (array) $inviters as $inviter_id ) {

        // Check if signup requires activation key
        if ( apply_filters( 'bp_core_signup_send_activation_key', true ) ) {

            // Get pending invites list
            $pending = get_transient( 'gamipress_invite_anyone_pending_invites' );

            if ( $pending === false ) $pending = array();

            // Add to pending list invites if not there already
            if ( ! isset( $pending[$user_id] ) ) {
                $pending[$user_id] = $inviter_id;

                delete_transient( 'gamipress_invite_anyone_pending_invites' );
                set_transient( 'gamipress_invite_anyone_pending_invites', $pending, 7 * DAY_IN_SECONDS );
            }

        } else {

            // Trigger accept an invitation event (award invited user)
            do_action( 'gamipress_invite_anyone_accept_invite', $user_id, $inviter_id );

            // Trigger get an invitation accepted event (award user that sent the invitation)
            do_action( 'gamipress_invite_anyone_accepted_invite', $user_id, $inviter_id );

        }

    }

}
add_action( 'accepted_email_invite', 'gamipress_invite_anyone_accept_invite_listener', 10, 2 );

function gamipress_invite_anyone_activated_user_listener( $user_id ) {

    // Get pending invites list
    $pending = get_transient( 'gamipress_invite_anyone_pending_invites' );

    // Bail if not pending invites
    if ( $pending === false || ! isset( $pending[$user_id] ) ) return;

    $inviter_id = $pending[$user_id];

    // Trigger accept an invitation event (award invited user)
    do_action( 'gamipress_invite_anyone_accept_invite', $user_id, $inviter_id );

    // Trigger get an invitation accepted event (award user that sent the invitation)
    do_action( 'gamipress_invite_anyone_accepted_invite', $user_id, $inviter_id );;

    // Remove from list
    unset( $pending[$user_id] );

    // Update pending list
    delete_transient( 'gamipress_invite_anyone_pending_invites' );
    set_transient( 'gamipress_invite_anyone_pending_invites', $pending, 7 * DAY_IN_SECONDS );

}
add_action( 'bp_core_activated_user', 'gamipress_invite_anyone_activated_user_listener' );
