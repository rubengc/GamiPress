<?php
/**
 * Listeners
 *
 * @package GamiPress\Give\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * New donation
 *
 * @since  1.0.0
 *
 * @param int       $payment_id     The payment ID.
 * @param string    $new_status     The payment new status.
 * @param string    $old_status     The payment old status.
 */
function gamipress_give_new_donation( $payment_id, $new_status, $old_status ) {

    // Make sure that payments are only completed once.
    if ( $old_status === 'publish' || $old_status === 'complete' ) {
        return;
    }

    // Make sure the payment completion is only processed when new status is complete.
    if ( $new_status !== 'publish' && $new_status !== 'complete' ) {
        return;
    }

    // Setup the payment data
    $payment        = new Give_Payment( $payment_id );
    $user_id        = $payment->user_id;
    $donor_id       = $payment->customer_id;
    $amount         = $payment->total;
    $form_id        = $payment->form_id;

    // Trigger new donation event to award to the user that donates
    do_action( 'gamipress_give_new_donation', $payment_id, $user_id, $form_id, $amount );

    // Trigger new donation event to award to the user that donates a minimum amount
    do_action( 'gamipress_give_new_donation_min_amount', $payment_id, $user_id, $form_id, $amount );

    if( absint( $form_id ) !== 0 ) {
        // Trigger new donation through a specific form event to award to the user that donates
        do_action( 'gamipress_give_new_donation_specific_form', $payment_id, $user_id, $form_id, $amount );
    }
}
add_action( 'give_update_payment_status', 'gamipress_give_new_donation', 10, 3 );
