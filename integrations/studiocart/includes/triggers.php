<?php
/**
 * Triggers
 *
 * @package GamiPress\Studiocart\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin specific triggers
 *
 * @since 1.0.0
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_studiocart_activity_triggers( $triggers ) {

    $triggers[__( 'Studiocart', 'gamipress' )] = array(

        'gamipress_studiocart_any_product_purchase'                     => __( 'Purchase any product', 'gamipress' ),
        'gamipress_studiocart_specific_product_purchase'                => __( 'Purchase a specific product', 'gamipress' ),
        'gamipress_studiocart_complete_any_product_order'               => __( 'Complete an order for any product', 'gamipress' ),
        'gamipress_studiocart_complete_specific_product_order'          => __( 'Complete an order for a specific product', 'gamipress' ),
        'gamipress_studiocart_refund_any_product_order'                 => __( 'Refund an order for any product', 'gamipress' ),
        'gamipress_studiocart_refund_specific_product_order'            => __( 'Refund an order for a specific product', 'gamipress' ),
        'gamipress_studiocart_cancel_any_product_subscription'          => __( 'Cancel a subscription for any product', 'gamipress' ),
        'gamipress_studiocart_cancel_specific_product_subscription'     => __( 'Cancel a subscription for a specific product', 'gamipress' ),
        
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_studiocart_activity_triggers' );

/**
 * Register Studiocart specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_studiocart_specific_activity_triggers( $specific_activity_triggers ) {

    $specific_activity_triggers['gamipress_studiocart_specific_product_purchase'] = array( 'sc_product' );
    $specific_activity_triggers['gamipress_studiocart_complete_specific_product_order'] = array( 'sc_product' );
    $specific_activity_triggers['gamipress_studiocart_refund_specific_product_order'] = array( 'sc_product' );
    $specific_activity_triggers['gamipress_studiocart_cancel_specific_product_subscription'] = array( 'sc_product' );
    
    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_studiocart_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_studiocart_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_studiocart_specific_product_purchase'] = __( 'Purchase %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_studiocart_complete_specific_product_order'] = __( 'Complete an order for %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_studiocart_refund_specific_product_order'] = __( 'Refund an order for %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_studiocart_cancel_specific_product_subscription'] = __( 'Cancel subscription for %s', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_studiocart_specific_activity_trigger_label' );

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
function gamipress_studiocart_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {

        case 'gamipress_studiocart_any_product_purchase':
        case 'gamipress_studiocart_specific_product_purchase':
        case 'gamipress_studiocart_complete_any_product_order':
        case 'gamipress_studiocart_complete_specific_product_order':
        case 'gamipress_studiocart_refund_any_product_order':
        case 'gamipress_studiocart_refund_specific_product_order':
        case 'gamipress_studiocart_cancel_any_product_subscription':
        case 'gamipress_studiocart_cancel_specific_product_subscription':
            $user_id = $args[2];
            break;

    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_studiocart_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer  $specific_id Specific ID.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          Specific ID.
 */
function gamipress_studiocart_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {

        case 'gamipress_studiocart_specific_product_purchase':
        case 'gamipress_studiocart_complete_specific_product_order':
        case 'gamipress_studiocart_refund_specific_product_order':
        case 'gamipress_studiocart_cancel_specific_product_subscription':
            $specific_id = $args[0];
            break;

    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_studiocart_specific_trigger_get_id', 10, 3 );

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
function gamipress_studiocart_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {

        case 'gamipress_studiocart_any_product_purchase':
        case 'gamipress_studiocart_specific_product_purchase':
        case 'gamipress_studiocart_complete_any_product_order':
        case 'gamipress_studiocart_complete_specific_product_order':
        case 'gamipress_studiocart_refund_any_product_order':
        case 'gamipress_studiocart_refund_specific_product_order':
        case 'gamipress_studiocart_cancel_any_product_subscription':
        case 'gamipress_studiocart_cancel_specific_product_subscription':
            $log_meta['product_id'] = $args[0];
            $log_meta['order_id'] = $args[1];
            break;

    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_studiocart_log_event_trigger_meta_data', 10, 5 );