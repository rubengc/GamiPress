<?php
/**
 * Listeners
 *
 * @package GamiPress\WP_Simple_Pay\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Purchase listener (Lite)
 *
 * @since 1.0.0
 */
function gamipress_wp_simple_pay_purchase_listener_lite() {

    if( SIMPLE_PAY_PLUGIN_NAME === 'WP Simple Pay Pro' ) {
        return;
    }

    $data = \SimplePay\Core\Payments\Payment_Confirmation\get_confirmation_data();

    // Bail if not customer data
    if ( ! isset( $data['customer'] ) ) {
        return;
    }

    // Bail if no form assigned
    if( ! isset( $data['form'] ) ) {
        return;
    }

    $user = get_user_by_email( $data['customer']->email );

    // Bail if user can't be found
    if( ! $user ) {
        return;
    }

    $user_id = $user->ID;
    $post_id = $data['form']->id;

    // Trigger complete a purchase through a form
    do_action( 'gamipress_wp_simple_pay_purchase', $post_id, $user_id );

    // Trigger complete a purchase through a specific form
    do_action( 'gamipress_wp_simple_pay_specific_purchase', $post_id, $user_id );

}
add_action( 'init', 'gamipress_wp_simple_pay_purchase_listener_lite' );

/**
 * Purchase listener
 *
 * @since 1.0.0
 *
 * @param \Stripe\Event                              $event Stripe Event.
 * @param \Stripe\Subscription|\Stripe\PaymentIntent $object Stripe Subscription or PaymentIntent
 */
function gamipress_wp_simple_pay_purchase_listener( $event, $object ) {

    $user = get_user_by_email( $object->customer->email );

    // Bail if user can't be found
    if( ! $user ) {
        return;
    }

    $user_id = $user->ID;

    $payment = $event->data->object;

    if ( isset( $payment->metadata->simpay_form_id ) ) {
        $post_id = $payment->metadata->simpay_form_id;
    } else {
        $post_id = end( $payment->lines->data )->metadata->simpay_form_id;
    }

    // Trigger complete a purchase through a form
    do_action( 'gamipress_wp_simple_pay_purchase', $post_id, $user_id );

    // Trigger complete a purchase through a specific form
    do_action( 'gamipress_wp_simple_pay_specific_purchase', $post_id, $user_id );

}
add_action( 'simpay_webhook_subscription_created', 'gamipress_wp_simple_pay_purchase_listener', 10, 2 );
add_action( 'simpay_webhook_payment_intent_succeeded', 'gamipress_wp_simple_pay_purchase_listener', 10, 2 );

/**
 * Renew subscription listener
 *
 * @since 1.0.0
 *
 * @param \Stripe\Event        $event Stripe Event object.
 * @param \Stripe\Invoice      $invoice Stripe Invoice object.
 * @param \Stripe\Subscription $subscription Stripe Subscription object.
 */
function gamipress_wp_simple_pay_renew_subscription_listener( $event, $invoice, $subscription ) {

    $user = get_user_by_email( $invoice->customer_email );

    // Bail if user can't be found
    if( ! $user ) {
        return;
    }

    $user_id = $user->ID;

    $payment = $event->data->object;

    if ( isset( $payment->metadata->simpay_form_id ) ) {
        $post_id = $payment->metadata->simpay_form_id;
    } else {
        $post_id = end( $payment->lines->data )->metadata->simpay_form_id;
    }

    // Trigger renew a subscription
    do_action( 'gamipress_wp_simple_pay_renew_subscription', $post_id, $user_id );


}
add_action( 'simpay_webhook_invoice_payment_succeeded', 'gamipress_wp_simple_pay_renew_subscription_listener', 10, 3 );
