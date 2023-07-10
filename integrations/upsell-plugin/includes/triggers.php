<?php
/**
 * Triggers
 *
 * @package GamiPress\Upsell_Plugin\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register activity triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_upsell_plugin_activity_triggers( $triggers ) {

    $triggers[__( 'Upsell Plugin', 'gamipress' )] = array(

        'gamipress_publish_upsell_product'                          => __( 'Publish a new product', 'gamipress' ), // Internal GamiPress listener

        // Purchase
        'gamipress_upsell_plugin_purchase'                          => __( 'Make a new purchase', 'gamipress' ),

        'gamipress_upsell_plugin_product_purchase'                  => __( 'Purchase a product', 'gamipress' ),
        'gamipress_upsell_plugin_specific_product_purchase'         => __( 'Purchase a specific product', 'gamipress' ),

        // Subscriptions
        'gamipress_upsell_plugin_subscription_purchase'             => __( 'Purchase a subscription product', 'gamipress' ),
        'gamipress_upsell_plugin_specific_subscription_purchase'    => __( 'Purchase a specific subscription product', 'gamipress' ),
        'gamipress_upsell_plugin_subscription_renewal'              => __( 'Renew a subscription product', 'gamipress' ),
        'gamipress_upsell_plugin_specific_subscription_renewal'     => __( 'Renew a specific subscription product', 'gamipress' ),
        'gamipress_upsell_plugin_subscription_cancelled'            => __( 'Cancel a subscription product', 'gamipress' ),
        'gamipress_upsell_plugin_specific_subscription_cancelled'   => __( 'Cancel a specific subscription product', 'gamipress' ),
        'gamipress_upsell_plugin_subscription_refunded'             => __( 'Refund a subscription product', 'gamipress' ),
        'gamipress_upsell_plugin_specific_subscription_refunded'    => __( 'Refund a specific subscription product', 'gamipress' ),
        'gamipress_upsell_plugin_subscription_expired'              => __( 'Subscription product expires', 'gamipress' ),
        'gamipress_upsell_plugin_specific_subscription_expired'     => __( 'Specific subscription product expires', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_upsell_plugin_activity_triggers' );

/**
 * Register specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_upsell_plugin_specific_activity_triggers( $specific_activity_triggers ) {

    // Purchase
    $specific_activity_triggers['gamipress_upsell_plugin_specific_product_purchase'] = array( 'upsell_product' );
    // Subscriptions
    $specific_activity_triggers['gamipress_upsell_plugin_specific_subscription_purchase'] = array( 'upsell_product' );
    $specific_activity_triggers['gamipress_upsell_plugin_specific_subscription_renewal'] = array( 'upsell_product' );
    $specific_activity_triggers['gamipress_upsell_plugin_specific_subscription_cancelled'] = array( 'upsell_product' );
    $specific_activity_triggers['gamipress_upsell_plugin_specific_subscription_refunded'] = array( 'upsell_product' );
    $specific_activity_triggers['gamipress_upsell_plugin_specific_subscription_expired'] = array( 'upsell_product' );


    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_upsell_plugin_specific_activity_triggers' );

/**
 * Register specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_upsell_plugin_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Purchase
    $specific_activity_trigger_labels['gamipress_upsell_plugin_specific_product_purchase'] = __( 'Purchase %s', 'gamipress' );
    // Subscriptions
    $specific_activity_trigger_labels['gamipress_upsell_plugin_specific_subscription_purchase'] = __( 'Purchase %s subscription', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_upsell_plugin_specific_subscription_renewal'] = __( 'Renew %s subscription', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_upsell_plugin_specific_subscription_cancelled'] = __( 'Cancel %s subscription', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_upsell_plugin_specific_subscription_refunded'] = __( 'Refund %s subscription', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_upsell_plugin_specific_subscription_expired'] = __( '%s subscription expires', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_upsell_plugin_specific_activity_trigger_label' );

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
function gamipress_upsell_plugin_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_publish_upsell_product': // Internal GamiPress listener
            // Purchase
        case 'gamipress_upsell_plugin_purchase':
        case 'gamipress_upsell_plugin_product_purchase':
        case 'gamipress_upsell_plugin_specific_product_purchase':
            // Subscriptions
        case 'gamipress_upsell_plugin_subscription_purchase':
        case 'gamipress_upsell_plugin_specific_subscription_purchase':
        case 'gamipress_upsell_plugin_subscription_renewal':
        case 'gamipress_upsell_plugin_specific_subscription_renewal':
        case 'gamipress_upsell_plugin_subscription_cancelled':
        case 'gamipress_upsell_plugin_specific_subscription_cancelled':
        case 'gamipress_upsell_plugin_subscription_refunded':
        case 'gamipress_upsell_plugin_specific_subscription_refunded':
        case 'gamipress_upsell_plugin_subscription_expired':
        case 'gamipress_upsell_plugin_specific_subscription_expired':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_upsell_plugin_trigger_get_user_id', 10, 3 );

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
function gamipress_upsell_plugin_specific_trigger_get_id( $specific_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Purchase
        case 'gamipress_upsell_plugin_specific_product_purchase':
        // Subscriptions
        case 'gamipress_upsell_plugin_specific_subscription_purchase':
        case 'gamipress_upsell_plugin_specific_subscription_renewal':
        case 'gamipress_upsell_plugin_specific_subscription_cancelled':
        case 'gamipress_upsell_plugin_specific_subscription_refunded':
        case 'gamipress_upsell_plugin_specific_subscription_expired':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;

}

add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_upsell_plugin_specific_trigger_get_id', 10, 3 );

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.2
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_upsell_plugin_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_publish_upsell_product': // Internal GamiPress listener
            // Add the product ID
            $log_meta['product_id'] = $args[0];
            $log_meta['post_id'] = $args[0]; // Post ID added to make column visible on logs list
            break;
        // Purchase
        case 'gamipress_upsell_plugin_product_purchase':
        case 'gamipress_upsell_plugin_specific_product_purchase':
            // Subscriptions
        case 'gamipress_upsell_plugin_subscription_purchase':
        case 'gamipress_upsell_plugin_specific_subscription_purchase':
        case 'gamipress_upsell_plugin_subscription_renewal':
        case 'gamipress_upsell_plugin_specific_subscription_renewal':
        case 'gamipress_upsell_plugin_subscription_cancelled':
        case 'gamipress_upsell_plugin_specific_subscription_cancelled':
        case 'gamipress_upsell_plugin_subscription_refunded':
        case 'gamipress_upsell_plugin_specific_subscription_refunded':
        case 'gamipress_upsell_plugin_subscription_expired':
        case 'gamipress_upsell_plugin_specific_subscription_expired':
            // Add the product and order IDs
            $log_meta['product_id'] = $args[0];
            $log_meta['post_id'] = $args[0]; // Post ID added to make column visible on logs list
            $log_meta['order_id'] = $args[2];
            break;
        case 'gamipress_upsell_plugin_purchase':
            // Add the order ID
            $log_meta['order_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_upsell_plugin_log_event_trigger_meta_data', 10, 5 );

/**
 * Extra filter to check duplicated activity
 *
 * @since 1.0.2
 *
 * @param bool 		$return
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return bool					True if user deserves trigger, else false
 */
function gamipress_upsell_plugin_trigger_duplicity_check( $return, $user_id, $trigger, $site_id, $args  ) {

    // If user doesn't deserves trigger, then bail to prevent grant access
    if( ! $return )
        return $return;

    $log_meta = array(
        'type' => 'event_trigger',
        'trigger_type' => $trigger,
    );

    switch ( $trigger ) {
        case 'gamipress_publish_upsell_product': // Internal GamiPress listener
            // User can not create same product more times, so check it
            $log_meta['product_id'] = $args[0];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        // Purchase
        case 'gamipress_upsell_plugin_product_purchase':
        case 'gamipress_upsell_plugin_specific_product_purchase':
            // Subscriptions
        case 'gamipress_upsell_plugin_subscription_purchase':
        case 'gamipress_upsell_plugin_specific_subscription_purchase':
        case 'gamipress_upsell_plugin_subscription_renewal':
        case 'gamipress_upsell_plugin_specific_subscription_renewal':
        case 'gamipress_upsell_plugin_subscription_cancelled':
        case 'gamipress_upsell_plugin_specific_subscription_cancelled':
        case 'gamipress_upsell_plugin_subscription_refunded':
        case 'gamipress_upsell_plugin_specific_subscription_refunded':
        case 'gamipress_upsell_plugin_subscription_expired':
        case 'gamipress_upsell_plugin_specific_subscription_expired':
            // Refund
        case 'gamipress_upsell_plugin_product_refund':
        case 'gamipress_upsell_plugin_specific_product_refund':
        case 'gamipress_upsell_plugin_user_product_refund':

            $quantity = isset( $args[3] ) ? $args[3] : 1;

            // Don't perform duplicity check if quantity is higher than 1
            if( $quantity > 1 ) {
                break;
            }

            // User can not place same product and order IDs more times, so check it
            $log_meta['product_id'] = $args[0];
            $log_meta['order_id'] = $args[2];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        case 'gamipress_upsell_plugin_purchase':
            // User can not place or refund same order ID more times, so check it
            $log_meta['order_id'] = $args[0];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
    }

    return $return;

}
add_filter( 'gamipress_user_deserves_trigger', 'gamipress_upsell_plugin_trigger_duplicity_check', 10, 5 );