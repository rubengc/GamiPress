<?php
/**
 * Listeners
 *
 * @package GamiPress\Paid_Memberships_Pro\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Purchase membership listener
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param MemberOrder $morder
 */
function gamipress_pmpro_purchase_membership( $user_id, $morder ) {

    $user                = $morder->getUser();
    $membership          = $morder->getMembershipLevel();
    $user_id             = $user->ID;
    $membership_id       = $membership->id;

    // Trigger purchase any membership
    do_action( 'gamipress_pmpro_purchase_membership', $membership_id, $user_id );

    // Trigger purchase specific membership
    do_action( 'gamipress_pmpro_purchase_specific_membership', $membership_id, $user_id );

}
add_action( 'pmpro_after_checkout', 'gamipress_pmpro_purchase_membership', 10, 2 );

/**
 * Renew membership listener
 *
 * @since 1.0.0
 *
 * @param MemberOrder $morder
 */
function gamipress_pmpro_renew_membership( $morder ) {

    if( $morder->status !== 'success' ) {
        return;
    }

    // Check if this order was triggered already
    $triggered = get_pmpro_membership_order_meta( $morder->id, '_gamipress_renew_membership_triggered', true );

    if ( ! empty( $triggered ) ) {
        return;
    }

    $user                = $morder->getUser();
    $membership          = $morder->getMembershipLevel();
    $user_id             = $user->ID;
    $membership_id       = $membership->id;

    // Get all active membershipships for this user
	$old_levels = pmpro_getMembershipLevelsForUser( $user_id );

    foreach ( $old_levels as $level ) {
        
        if ($level->ID !== $membership_id) {
            return;
        } 
    }

    // Bail if not is a renewal
    if( ! $morder->is_renewal() ) {
        return;
    }

    // Trigger renew any membership
    do_action( 'gamipress_pmpro_renew_membership', $membership_id, $user_id );

    // Trigger renew specific membership
    do_action( 'gamipress_pmpro_renew_specific_membership', $membership_id, $user_id );

    // Register that we've already triggered on this order
    update_pmpro_membership_order_meta( $morder->id, '_gamipress_renew_membership_triggered', true );

}
add_action( 'pmpro_added_order', 'gamipress_pmpro_renew_membership' );

/**
 * Cancel membership listener
 *
 * @since 1.0.0
 *
 * @param MemberOrder $morder
 */
function gamipress_pmpro_cancel_membership( $morder ) {

    $user                = $morder->getUser();
    $membership          = $morder->getMembershipLevel();
    $user_id             = $user->ID;
    $membership_id       = $membership->id;

    // Trigger cancel any membership
    do_action( 'gamipress_pmpro_cancel_membership', $membership_id, $user_id );

    // Trigger cancel specific membership
    do_action( 'gamipress_pmpro_cancel_specific_membership', $membership_id, $user_id );

}
add_action( 'pmpro_subscription_cancelled', 'gamipress_pmpro_cancel_membership' );

/**
 * Membership expires listener
 *
 * @since 1.0.0
 *
 * @param MemberOrder $morder
 */
function gamipress_pmpro_membership_expired( $morder ) {

    $user                = $morder->getUser();
    $membership          = $morder->getMembershipLevel();
    $user_id             = $user->ID;
    $membership_id       = $membership->id;

    // Trigger any membership expired
    do_action( 'gamipress_pmpro_membership_expired', $membership_id, $user_id );

    // Trigger specific membership expired
    do_action( 'gamipress_pmpro_specific_membership_expired', $membership_id, $user_id );

}
add_action( 'pmpro_subscription_expired', 'gamipress_pmpro_membership_expired' );