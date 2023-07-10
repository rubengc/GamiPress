<?php
/**
 * Triggers
 *
 * @package GamiPress\AffiliateWP\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register AffiliateWP specific triggers
 *
 * @since  1.0.0
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_affwp_activity_triggers( $triggers ) {

    $triggers[__( 'AffiliateWP', 'gamipress' )] = array(
        'gamipress_affwp_register_affiliate'    => __( 'Become an affiliate', 'gamipress' ),
        'gamipress_affwp_new_referral'          => __( 'Earn a referral', 'gamipress' ),
        'gamipress_affwp_referral_visit'        => __( 'Referral visit', 'gamipress' ),
        'gamipress_affwp_register_referral'     => __( 'Become an user through an affiliate', 'gamipress' ),
        'gamipress_affwp_referral_signup'       => __( 'Referral sign up', 'gamipress' ),
        'gamipress_affwp_referral_paid'         => __( 'Get a referral paid', 'gamipress' ),
        'gamipress_affwp_referral_rejected'     => __( 'Get a referral rejected', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_affwp_activity_triggers' );

/**
 * Get user for a given trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $user_id user ID to override.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 * @return integer          User ID.
 */
function gamipress_affwp_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_affwp_register_affiliate':
        case 'gamipress_affwp_new_referral':
        case 'gamipress_affwp_referral_visit':
        case 'gamipress_affwp_register_referral':
        case 'gamipress_affwp_referral_signup':
        case 'gamipress_affwp_referral_paid':
        case 'gamipress_affwp_referral_rejected':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_affwp_trigger_get_user_id', 10, 3 );

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.1
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_affwp_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_affwp_register_affiliate':
        case 'gamipress_affwp_referral_visit':
        case 'gamipress_affwp_register_referral':
            // Add the affiliate ID
            $log_meta['affiliate_id'] = $args[0];
            break;
        case 'gamipress_affwp_referral_signup':
        case 'gamipress_affwp_new_referral':
        case 'gamipress_affwp_referral_paid':
        case 'gamipress_affwp_referral_rejected':
            // Add the affiliate and new user IDs
            $log_meta['affiliate_id'] = $args[0];
            $log_meta['referral_id'] = $args[2];
            break;

    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_affwp_log_event_trigger_meta_data', 10, 5 );