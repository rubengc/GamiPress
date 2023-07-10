<?php
/**
 * Listeners
 *
 * @package GamiPress\AffiliateWP\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register affiliate
 *
 * @since  1.0.0
 *
 * @param integer $affiliate  Affiliate ID
 * @param  string $status     The new affiliate status. Optional.
 * @param  string $old_status The old affiliate status.
 */
function gamipress_affwp_register_affiliate( $affiliate, $status, $old_status ) {

    if ( $status !== 'active' ) {
        return;
    }

    $user_id = affwp_get_affiliate_user_id( $affiliate );

    do_action( 'gamipress_affwp_register_affiliate', $affiliate, $user_id );

}

add_action( 'affwp_set_affiliate_status', 'gamipress_affwp_register_affiliate', 10, 3 );

/**
 * Earn a referral
 *
 * @since  1.0.7
 *
 * @param integer   $referral_id
 */
function gamipress_affwp_new_referral( $referral_id ) {

    $referral = affwp_get_referral( $referral_id );

    // Get user id from affiliate id
    $user_id = affwp_get_affiliate_user_id( $referral->affiliate_id );

    // Trigger earn a referral event to award to the affiliate for a new referral
    do_action( 'gamipress_affwp_new_referral', $referral->affiliate_id, $user_id, $referral_id );

}
add_action( 'affwp_insert_referral', 'gamipress_affwp_new_referral' );

/**
 * Referral visit
 *
 * @since  1.0.0
 *
 * @param integer   $insert_id
 * @param array     $data
 */
function gamipress_affwp_referral_visit( $insert_id, $data ) {

    $affiliate_id = absint( $data['affiliate_id'] );

    // Get user id from affiliate id
    $user_id = affwp_get_affiliate_user_id( $affiliate_id );

    // Trigger referral visit event to award to the affiliate that gets a visit
    do_action( 'gamipress_affwp_referral_visit', $affiliate_id, $user_id );

}
add_action( 'affwp_post_insert_visit', 'gamipress_affwp_referral_visit', 10, 2 );

/**
 * Referral sign up
 *
 * @since  1.0.5
 *
 * @param integer $user_id
 */
function gamipress_affwp_referral_signup( $user_id ) {

    $affiliate_id = affiliate_wp()->tracking->get_affiliate_id();

    // If new registered user has come from an affiliate, then trigger it
    if( $affiliate_id ) {

        // Get user id from affiliate id
        $affiliate_user_id = affwp_get_affiliate_user_id( $affiliate_id );

        // Trigger referral sign up event to award the affiliate that gets the new user
        do_action( 'gamipress_affwp_referral_signup', $affiliate_id, $affiliate_user_id, $user_id );

        // Trigger become an user through an affiliate event to award the referral that becomes as a new user
        do_action( 'gamipress_affwp_register_referral', $affiliate_id, $user_id, $affiliate_user_id );

    }

}
add_action( 'user_register', 'gamipress_affwp_referral_signup' );

/**
 * Fires immediately after a referral's status has been successfully updated.
 *
 * @since 1.0.9
 *
 * @param int    $referral_id Referral ID.
 * @param string $new_status  New referral status.
 * @param string $old_status  Old referral status.
 */
function gamipress_affwp_referral_status_change( $referral_id, $new_status, $old_status ) {

    $referral = affwp_get_referral( $referral_id );
    $user_id = affwp_get_affiliate_user_id( $referral->affiliate_id );

    if( $new_status === 'paid' && $old_status !== 'paid' ) {
        // Trigger get a referral paid event to award to the affiliate who was paid
        do_action( 'gamipress_affwp_referral_paid', $referral->affiliate_id, $user_id, $referral_id );
    }

    if( $new_status === 'rejected' && $old_status !== 'rejected' ) {
        // Trigger get a referral rejected event to award to the affiliate who was paid
        do_action( 'gamipress_affwp_referral_rejected', $referral->affiliate_id, $user_id, $referral_id );
    }

}
add_action( 'affwp_set_referral_status', 'gamipress_affwp_referral_status_change', 10, 3 );
