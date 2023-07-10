<?php
/**
 * Listeners
 *
 * @package GamiPress\Upsell_Plugin\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Order completed listener
 *
 * @since 1.0.0
 *
 * @param Upsell\Entities\Order $order The order
 */
function gamipress_upsell_plugin_order_completed_listener( $order ) {

    // Bail if order is not marked as completed
    if ( $order->getStatus() !== 'completed' ) {
        return;
    }

    $items = $order->getItems();

    // Bail if no items purchased
    if ( ! is_array( $items ) ) {
        return;
    }

    $order_id = $order->getId();
    $order_total = $order->getTotal();
    $user_id = $order->customer()->attribute('user_id');

    // Trigger purchase
    do_action( 'gamipress_upsell_plugin_purchase', $order_id, $user_id );

    // Loop all items to trigger events on each one purchased
    foreach ( $items as $item ) {

        $product_id     = $item['id'];
        $quantity       = $item['quantity'];

        // Skip items not assigned to a product
        if( $product_id === 0 ) {
            continue;
        }

        // Trigger events same times as item quantity
        for ( $i = 0; $i < $quantity; $i++ ) {

            // Skip items not assigned to a product
            if( $product_id === 0 ) {
                continue;
            }

            $product = \Upsell\Entities\Product::find( $product_id );

            if( ! $product ) {
                continue;
            }

            // Trigger product purchase
            do_action( 'gamipress_upsell_plugin_product_purchase', $product_id, $user_id, $order_id, $quantity );

            // Trigger specific product purchase
            do_action( 'gamipress_upsell_plugin_specific_product_purchase', $product_id, $user_id, $order_id, $quantity );

            if( $product->isSubscriptionPayment() ) {
                // Trigger subscription purchase
                do_action( 'gamipress_upsell_plugin_subscription_purchase', $product_id, $user_id, $order_id, $quantity );

                // Trigger specific subscription purchase
                do_action( 'gamipress_upsell_plugin_specific_subscription_purchase', $product_id, $user_id, $order_id, $quantity );

                if ( ! empty( $order->getAttribute('subscription_id') ) ) {

                    // Trigger subscription renewal
                    do_action( 'gamipress_upsell_plugin_subscription_renewal', $product_id, $user_id, $order_id, $quantity );

                    // Trigger specific subscription renewal
                    do_action( 'gamipress_upsell_plugin_specific_subscription_renewal', $product_id, $user_id, $order_id, $quantity );

                }
            }


        } // End for of quantities

    } // End foreach of items

}
add_action( 'upsell_order_status_completed', 'gamipress_upsell_plugin_order_completed_listener', 10, 1 );

/**
 * Subscription cancelled listener
 *
 * @since 1.0.0
 *
 * @param Upsell\Entities\Subscription $subscription
 */
function gamipress_upsell_plugin_subscription_cancelled_listener( $subscription ) {

    // Bail if subscription hasn't been cancelled
    if( $subscription->getStatus() !== 'cancelled' ) {
        return;
    }

    $order_id = $subscription->getOrder()->getId();
    $order_total = $subscription->getOrder()->getTotal();
    $user_id = $subscription->customer()->attribute('user_id');
    $product_id = $subscription->getProduct()->getId();

    // Trigger subscription cancellation
    do_action( 'gamipress_upsell_plugin_subscription_cancelled', $product_id, $user_id, $order_id );

    // Trigger specific subscription cancellation
    do_action( 'gamipress_upsell_plugin_specific_subscription_cancelled', $product_id, $user_id, $order_id );

}
add_action( 'upsell_subscription_status_cancelled', 'gamipress_upsell_plugin_subscription_cancelled_listener', 10, 1 );

/**
 * Subscription refunded listener
 *
 * @since 1.0.0
 *
 * @param Upsell\Entities\Subscription $subscription
 */
function gamipress_upsell_plugin_subscription_refunded_listener( $subscription ) {

    // Bail if subscription hasn't been refunded
    if( $subscription->getStatus() !== 'refunded' ) {
        return;
    }

    $order_id = $subscription->getOrder()->getId();
    $order_total = $subscription->getOrder()->getTotal();
    $user_id = $subscription->customer()->attribute('user_id');
    $product_id = $subscription->getProduct()->getId();

    // Trigger subscription refunded
    do_action( 'gamipress_upsell_plugin_subscription_refunded', $product_id, $user_id, $order_id );

    // Trigger specific subscription refunded
    do_action( 'gamipress_upsell_plugin_specific_subscription_refunded', $product_id, $user_id, $order_id );

}
add_action( 'upsell_subscription_status_refunded', 'gamipress_upsell_plugin_subscription_refunded_listener', 10, 1 );

/**
 * Subscription expired listener
 *
 * @since 1.0.0
 *
 * @param Upsell\Entities\Subscription $subscription
 */
function gamipress_upsell_plugin_subscription_expired_listener( $subscription ) {

    // Bail if subscription hasn't been expired
    if( $subscription->getStatus() !== 'expired' ) {
        return;
    }

    $order_id = $subscription->getOrder()->getId();
    $order_total = $subscription->getOrder()->getTotal();
    $user_id = $subscription->customer()->attribute('user_id');
    $product_id = $subscription->getProduct()->getId();

    // Trigger subscription expired
    do_action( 'gamipress_upsell_plugin_subscription_expired', $product_id, $user_id, $order_id );

    // Trigger specific subscription expired
    do_action( 'gamipress_upsell_plugin_specific_subscription_expired', $product_id, $user_id, $order_id );

}
add_action( 'upsell_subscription_status_expired', 'gamipress_upsell_plugin_subscription_expired_listener', 10, 1 );