<?php
/**
 * Listeners
 *
 * @package GamiPress\Digimember\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Purchase listener
 *
 * @since 1.0.0
 *
 * @param int       $user_id
 * @param int       $product_id
 * @param int       $order_id
 * @param string    $reason (order_paid|order_cancelled|payment_missing)
 */
function gamipress_digimember_purchase_listener( $user_id, $product_id, $order_id, $reason ) {

    $product_type = gamipress_digimember_get_product_type( $product_id );

    if( $reason === 'order_paid' ) {

        // Trigger purchase any product
        do_action( 'gamipress_digimember_purchase_product', $order_id, $user_id, $product_id, $product_type, $reason );

        // Trigger purchase specific product
        do_action( 'gamipress_digimember_purchase_specific_product', $order_id, $user_id, $product_id, $product_type, $reason );

        // Trigger purchase any {type} product
        do_action( "gamipress_digimember_purchase_{$product_type}_product", $order_id, $user_id, $product_id, $product_type, $reason );

        // Trigger purchase specific {type} product
        do_action( "gamipress_digimember_purchase_specific_{$product_type}_product", $order_id, $user_id, $product_id, $product_type, $reason );

    } else if( $reason === 'order_cancelled' ) {

        // Trigger cancel any product
        do_action( 'gamipress_digimember_cancel_product', $order_id, $user_id, $product_id, $product_type, $reason );

        // Trigger cancel specific product
        do_action( 'gamipress_digimember_cancel_specific_product', $order_id, $user_id, $product_id, $product_type, $reason );

        // Trigger cancel any {type} product
        do_action( "gamipress_digimember_cancel_{$product_type}_product", $order_id, $user_id, $product_id, $product_type, $reason );

        // Trigger cancel specific {type} product
        do_action( "gamipress_digimember_cancel_specific_{$product_type}_product", $order_id, $user_id, $product_id, $product_type, $reason );

    }

}
add_action( 'digimember_purchase', 'gamipress_digimember_purchase_listener', 10, 4 );