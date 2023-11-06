<?php
/**
 * Listeners
 *
 * @package GamiPress\WooCommerce_Shipstation\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Trigger listener
 *
 * @since 1.0.0
 *
 * @param WC_Order  $order  Order object
 * @param array     $args   Additional data
 */
function gamipress_woocommerce_shipstation_order_shipped( $order, $args ) {

    // Bail if not order
    if ( ! $order ) {
        return;
    }

    $user_id = $order->get_user_id();

    // Bail if not user
    if ( $user_id === 0 ) {
        return;
    }
    
    $order_id = $order->get_id();

    // Trigger order shipped
    do_action( 'gamipress_wc_shipstation_order_shipped', $order_id, $user_id );

    $items = $order->get_items();

    if ( is_array( $items ) ) {

        // On purchase, trigger events on each product purchased
        foreach ( $items as $item ) {
            
            $product_id     = 0;

            if( class_exists( 'WC_Order_Item' ) && $item instanceof WC_Order_Item ) {

                // WooCommerce >= 3.0.0
                $product_id     = $item->get_product_id();
                $variation_id   = $item->get_variation_id();
                $quantity       = $item->get_quantity();

            } else if( is_array( $item ) && isset( $item['product_id'] ) ) {

                // WooCommerce < 3.0.0
                $product_id     = $item['product_id'];
                $variation_id   = ( isset( $item['variation_id'] ) ? $item['variation_id'] : 0 );
                $quantity       = isset( $item['qty'] ) ? absint( $item['qty'] ) : 1;

            }

            if( $product_id !== 0 ) {
                    
                // Trigger order shipped for specific product
                do_action( 'gamipress_wc_shipstation_order_product_shipped', $order_id, $user_id, $product_id );

            }

        } // end foreach

    } // end if $items is an array

}
add_action( 'woocommerce_shipstation_shipnotify', 'gamipress_woocommerce_shipstation_order_shipped', 10, 2 );