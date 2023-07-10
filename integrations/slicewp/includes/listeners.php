<?php
/**
 * Listeners
 *
 * @package GamiPress\SliceWP\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register affiliate
 *
 * @since  1.0.0
 *
 * @param int       $affiliate_id
 * @param array     $affiliate_data
 */
function gamipress_slicewp_register_affiliate( $affiliate_id, $affiliate_data ) {

    if( $affiliate_data['status'] != 'active' ) {
        return;
    }

    // Get the affiliate user ID
    $affiliate = slicewp_get_affiliate( $affiliate_id );
    $user_id =  $affiliate->get( 'user_id' );

    // Trigger become an affiliate event
    do_action( 'gamipress_slicewp_register_affiliate', $affiliate_id, $user_id );
}
add_action( 'slicewp_insert_affiliate', 'gamipress_slicewp_register_affiliate', 10, 2 );
add_action( 'slicewp_update_affiliate', 'gamipress_slicewp_register_affiliate', 10, 2 );

/**
 * Earn a commission
 *
 * @since  1.0.0
 *
 *  @param int   $commission_id      The commission ID
 * @param array $commission_data    The commission data
 */
function gamipress_slicewp_new_commission( $commission_id, $commission_data ) {

    // Get the affiliate user ID
    $affiliate = slicewp_get_affiliate( $commission_data['affiliate_id'] );
    $user_id =  $affiliate->get( 'user_id' );

    // Trigger earn a commission event to award to the affiliate for a new commission
    do_action( 'gamipress_slicewp_new_commission', $commission_data['affiliate_id'], $user_id, $commission_id );

}
add_action( 'slicewp_insert_commission', 'gamipress_slicewp_new_commission', 10, 2 );

/**
 * Fires immediately after a commission's status has been successfully updated.
 *
 * @since 1.0.0
 *
 * @param int   $commission_id          The commission ID
 * @param array $commission_data        The commission data
 * @param array $commission_old_data    The commission old data
 */
function gamipress_slicewp_commission_status_change( $commission_id, $commission_data, $commission_old_data ) {

    $affiliate_id = ( ! empty( $commission_data['affiliate_id'] ) ? $commission_data['affiliate_id'] : $commission_old_data['affiliate_id'] );

    if( empty( $affiliate_id ) ) {
        return;
    }

    // Get the affiliate user ID
    $affiliate = slicewp_get_affiliate( $affiliate_id );
    $user_id =  $affiliate->get( 'user_id' );

    $new_status = $commission_data['status'];
    $old_status = $commission_old_data['status'];

    if( $new_status === 'paid' && $old_status !== 'paid' ) {
        // Trigger get a commission paid event to award to the affiliate who was paid
        do_action( 'gamipress_slicewp_commission_paid', $affiliate_id, $user_id, $commission_id );
    }

    if( $new_status === 'rejected' && $old_status !== 'rejected' ) {
        // Trigger get a commission rejected event to award to the affiliate who was paid
        do_action( 'gamipress_slicewp_commission_rejected', $affiliate_id, $user_id, $commission_id );
    }

}
add_action( 'slicewp_update_commission', 'gamipress_slicewp_commission_status_change', 10, 3 );
