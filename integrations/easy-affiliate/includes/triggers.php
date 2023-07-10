<?php
/**
 * Triggers
 *
 * @package GamiPress\Easy_Affiliate\Triggers
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin triggers
 *
 * @since  1.0.0
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_easy_affiliate_activity_triggers( $triggers ) {

    $triggers[__( 'Easy Affiliate', 'gamipress' )] = array(

        'gamipress_easy_affiliate_become_affiliate' => __( 'Become as affiliate', 'gamipress' ),
        'gamipress_easy_affiliate_earn_referral'    => __( 'Earn a referral', 'gamipress' ),
        'gamipress_easy_affiliate_get_payment'      => __( 'Get a payment', 'gamipress' ),
        
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_easy_affiliate_activity_triggers' );

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
function gamipress_easy_affiliate_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_easy_affiliate_become_affiliate':
        case 'gamipress_easy_affiliate_earn_referral':
        case 'gamipress_easy_affiliate_get_payment':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_easy_affiliate_trigger_get_user_id', 10, 3 );
