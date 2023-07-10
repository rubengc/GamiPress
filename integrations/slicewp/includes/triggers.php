<?php
/**
 * Triggers
 *
 * @package GamiPress\SliceWP\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register SliceWP specific triggers
 *
 * @since  1.0.0
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_slicewp_activity_triggers( $triggers ) {

    $triggers[__( 'SliceWP', 'gamipress' )] = array(
        'gamipress_slicewp_register_affiliate'    => __( 'Become an affiliate', 'gamipress' ),
        'gamipress_slicewp_new_commission'          => __( 'Earn a commission', 'gamipress' ),
        'gamipress_slicewp_commission_paid'         => __( 'Get a commission paid', 'gamipress' ),
        'gamipress_slicewp_commission_rejected'     => __( 'Get a commission rejected', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_slicewp_activity_triggers' );

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
function gamipress_slicewp_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_slicewp_register_affiliate':
        case 'gamipress_slicewp_new_commission':
        case 'gamipress_slicewp_commission_paid':
        case 'gamipress_slicewp_commission_rejected':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_slicewp_trigger_get_user_id', 10, 3 );

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.0
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_slicewp_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_slicewp_register_affiliate':
            // Add the affiliate ID
            $log_meta['affiliate_id'] = $args[0];
            break;
        case 'gamipress_slicewp_new_commission':
        case 'gamipress_slicewp_commission_paid':
        case 'gamipress_slicewp_commission_rejected':
            // Add the affiliate and new user IDs
            $log_meta['affiliate_id'] = $args[0];
            $log_meta['commission_id'] = $args[2];
            break;

    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_slicewp_log_event_trigger_meta_data', 10, 5 );