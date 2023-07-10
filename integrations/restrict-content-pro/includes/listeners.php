<?php
/**
 * Listeners
 *
 * @package GamiPress\Restrict_Content_Pro\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Purchase membership listener
 *
 * @since 1.0.0
 *
 * @param int            $membership_id ID of the membership.
 * @param RCP_Membership $membership    Membership object.
 */
function gamipress_rcp_purchase_membership( $membership_id, $membership ) {

    $user_id = $membership->get_user_id();
    $membership_id = $membership->get_object_id();

    if( ! $membership->is_paid() ) {
        // Trigger get any free membership
        do_action( 'gamipress_rcp_free_membership', $membership_id, $user_id );

        // Trigger get specific free membership
        do_action( 'gamipress_rcp_free_specific_membership', $membership_id, $user_id );
    } else {
        // Trigger purchase any membership
        do_action( 'gamipress_rcp_purchase_membership', $membership_id, $user_id );

        // Trigger purchase specific membership
        do_action( 'gamipress_rcp_purchase_specific_membership', $membership_id, $user_id );
    }


}
add_action( 'rcp_membership_post_activate', 'gamipress_rcp_purchase_membership', 10, 2 );

/**
 * Purchase membership listener
 *
 * @since 1.0.0
 *
 * @param string $old_status
 * @param int    $membership_id
 */
function gamipress_rcp_cancel_membership( $old_status, $membership_id ) {

    $membership = rcp_get_membership( $membership_id );

    $user_id = $membership->get_user_id();
    $membership_id = $membership->get_object_id();

    // Trigger cancel any membership
    do_action( 'gamipress_rcp_cancel_membership', $membership_id, $user_id );

    // Trigger cancel specific membership
    do_action( 'gamipress_rcp_cancel_specific_membership', $membership_id, $user_id );

}
add_action( 'rcp_transition_membership_status_cancelled', 'gamipress_rcp_cancel_membership', 10, 2 );

/**
 * Purchase membership listener
 *
 * @since 1.0.0
 *
 * @param string $old_status
 * @param int    $membership_id
 */
function gamipress_rcp_membership_expired( $old_status, $membership_id ) {

    $membership = rcp_get_membership( $membership_id );

    $user_id = $membership->get_user_id();
    $membership_id = $membership->get_object_id();

    // Trigger any membership expired
    do_action( 'gamipress_rcp_membership_expired', $membership_id, $user_id );

    // Trigger specific membership expired
    do_action( 'gamipress_rcp_specific_membership_expired', $membership_id, $user_id );

}
add_action( 'rcp_transition_membership_status_expired', 'gamipress_rcp_membership_expired', 10, 2 );