<?php
/**
 * Triggers
 *
 * @package GamiPress\SureCart\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register SureCart activity triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_surecart_activity_triggers( $triggers ) {

    $triggers[__( 'SureCart', 'gamipress' )] = array(

        'gamipress_surecart_new_purchase'               => __( 'Make a new purchase', 'gamipress' ),
        'gamipress_surecart_new_product_purchase'       => __( 'Purchase a product', 'gamipress' ),
        'gamipress_surecart_specific_product_purchase'  => __( 'Purchase a specific product', 'gamipress' ),
        
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_surecart_activity_triggers' );

/**
 * Build custom activity trigger label
 *
 * @since  1.0.0
 *
 * @param string    $title
 * @param integer   $requirement_id
 * @param array     $requirement
 *
 * @return string
 */
function gamipress_surecart_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $product_id = ( isset( $requirement['surecart_product'] ) ) ? $requirement['surecart_product'] : '';

    switch( $requirement['trigger_type'] ) {
        case 'gamipress_surecart_specific_product_purchase':
            
            // Get the products
            $products = SureCart\Models\Product::get();

            foreach ( $products as $product ) {
                if ( $product->id === $product_id ) {
                    $product_name = $product->name;
                    break;
                }
            }

            return sprintf( __( 'Purchase %s', 'gamipress' ), $product_name );
            break;
        
    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_surecart_activity_trigger_label', 10, 3 );


/**
 * Register SureCart specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_surecart_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Purchase
    $specific_activity_trigger_labels['gamipress_surecart_specific_product_purchase'] = __( 'Purchase %s', 'gamipress' );
    
    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_surecart_specific_activity_trigger_label' );


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
function gamipress_surecart_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        
        // Purchase
        case 'gamipress_surecart_new_purchase':
        case 'gamipress_surecart_new_product_purchase':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_surecart_trigger_get_user_id', 10, 3 );

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
function gamipress_surecart_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Purchase
        case 'gamipress_surecart_new_product_purchase':
        case 'gamipress_surecart_specific_product_purchase':
            // Add the product and order IDs
            $log_meta['product_id'] = $args[0];
            $log_meta['order_id'] = $args[2];
            break;
        

    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_surecart_log_event_trigger_meta_data', 10, 5 );
