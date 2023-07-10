<?php
/**
 * Listeners
 *
 * @package GamiPress\SureCart\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Purchase listener
function gamipress_surecart_new_purchase( $purchase ) {

    $user_id = get_current_user_id();

    // Bail if no user
    if ( $user_id === 0 ) {
        return;
    }

    //$product_id = strval($purchase->product);
    $product_id = $purchase->product;
    $quantity = $purchase->quantity;
    $order_id = $purchase->initial_order;

    // Trigger make new purchase
    do_action( 'gamipress_surecart_new_purchase', $order_id, $user_id );

    // Trigger events same times as item quantity
    for ( $i = 0; $i < $quantity; $i++ ) {

        // Trigger new product purchase
        do_action( 'gamipress_surecart_new_product_purchase', $product_id, $user_id, $order_id, $quantity );

        // Trigger specific product purchase
        do_action( 'gamipress_surecart_specific_product_purchase', $product_id, $user_id, $order_id, $quantity );

    }
    
 

}
add_action( 'surecart/purchase_created', 'gamipress_surecart_new_purchase' );
