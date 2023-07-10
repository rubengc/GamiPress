<?php
/**
 * Listeners
 *
 * @package GamiPress\Easy_Affiliate\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Become affiliate listener
 *
 * @since 1.0.0
 *
 * @param array $args       Args from Easy Affiliate event
 *
 * @return mixed
 */
function gamipress_easy_affiliate_become_affiliate_listener( $args ) {

    $user_id = get_current_user_id();

    // Bail if no user
    if ( absint( $user_id ) === 0 ) {
        return;
    }

    $user_affiliate = get_user_meta( $user_id, 'wafp_is_affiliate', true );

    // Bail if user is affiliated
    if ( isset ( $user_affiliate ) && $user_affiliate === '1' ) {
        return;
    }

    // Trigger become affiliate
    do_action( 'gamipress_easy_affiliate_become_affiliate', $event_id, $user_id );

}
add_filter( 'esaf_event_affiliate-added', 'gamipress_easy_affiliate_become_affiliate_listener' );


/**
 * Earn referral listener
 *
 * @since 1.0.0
 *
 * @param array $args       Args from Easy Affiliate event
 *
 * @return mixed
 */
function gamipress_easy_affiliate_earn_referral_listener( $args ) {

    $user_id = get_current_user_id();

    // Bail if no user
    if ( absint( $user_id ) === 0 ) {
        return;
    }

    $user_affiliate = get_user_meta( $user_id, 'wafp_is_affiliate', true );

    // Bail if user is not affiliated
    if ( !isset ( $user_affiliate ) && $user_affiliate !== '1' ) {
        return;
    }

    // Trigger become affiliate
    do_action( 'gamipress_easy_affiliate_earn_referral', $event_id, $user_id );

}

add_filter( 'esaf_event_transaction-recorded', 'gamipress_easy_affiliate_earn_referral_listener' );

/**
 * Get payment listener
 *
 * @since 1.0.0
 *
 * @param array $args       Args from Easy Affiliate event
 *
 * @return mixed
 */
function gamipress_easy_affiliate_get_payment_listener( $args ) {

    $user_id = get_current_user_id();

    // Bail if no user
    if ( absint( $user_id ) === 0 ) {
        return;
    }

    $user_affiliate = get_user_meta( $user_id, 'wafp_is_affiliate', true );

    // Bail if user is not affiliated
    if ( !isset ( $user_affiliate ) && $user_affiliate !== '1' ) {
        return;
    }

    // Trigger become affiliate
    do_action( 'gamipress_easy_affiliate_get_payment', $event_id, $user_id );

}

add_filter( 'esaf_event_payment-added', 'gamipress_easy_affiliate_get_payment_listener' );
