<?php
/**
 * Triggers
 *
 * @package GamiPress\WooCommerce_Shipstation\Triggers
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
function gamipress_woocommerce_shipstation_activity_triggers( $triggers ) {

    $triggers[__( 'WooCommerce Shipstation', 'gamipress-woocommerce-shipstation-integration' )] = array(

        'gamipress_wc_shipstation_order_shipped'                => __( 'Get an order shipped', 'gamipress' ),
        'gamipress_wc_shipstation_order_product_shipped'        => __( 'Get a product shipped', 'gamipress' ),
        
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_woocommerce_shipstation_activity_triggers' );

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
function gamipress_woocommerce_shipstation_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_wc_shipstation_order_shipped':
        case 'gamipress_wc_shipstation_order_product_shipped':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_woocommerce_shipstation_trigger_get_user_id', 10, 3 );

/**
 * Register WooCommerce specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_woocommerce_specific_activity_triggers( $specific_activity_triggers ) {

    $specific_activity_triggers['gamipress_wc_shipstation_order_product_shipped'] = array( 'product' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_woocommerce_specific_activity_triggers' );

/**
 * Register WooCommerce specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_woocommerce_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_wc_shipstation_order_product_shipped'] = __( 'Get %s product shipped', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_woocommerce_specific_activity_trigger_label' );

/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.1
 *
 * @param  integer $specific_id Specific ID to override.
 * @param  string  $trigger     Trigger name.
 * @param  array   $args        Passed trigger args.
 *
 * @return integer              Specific ID.
 */
function gamipress_woocommerce_specific_trigger_get_id( $specific_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_wc_shipstation_order_product_shipped':
            $specific_id = $args[2];
            break;
    }

    return $specific_id;

}

add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_woocommerce_specific_trigger_get_id', 10, 3 );

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
function gamipress_woocommerce_shipstation_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {

        case 'gamipress_wc_shipstation_order_shipped':
            $log_meta['order_id'] = $args[0];
            break;
        case 'gamipress_wc_shipstation_order_product_shipped':
            $log_meta['order_id'] = $args[0];
            $log_meta['product_id'] = $args[2];
            break;

    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_woocommerce_shipstation_log_event_trigger_meta_data', 10, 5 );
