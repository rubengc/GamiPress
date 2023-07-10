<?php
/**
 * Triggers
 *
 * @package GamiPress\Digimember\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin specific triggers
 *
 * @since  1.0.0
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_digimember_activity_triggers( $triggers ) {

    $triggers[__( 'Digimember', 'gamipress' )] = array(

        // Purchase product
        'gamipress_digimember_purchase_product'                                => __( 'Purchase any product', 'gamipress' ),
        'gamipress_digimember_purchase_specific_product'                       => __( 'Purchase a specific product', 'gamipress' ),
        'gamipress_digimember_purchase_membership_product'                     => __( 'Purchase any membership product', 'gamipress' ),
        'gamipress_digimember_purchase_specific_membership_product'            => __( 'Purchase a specific membership product', 'gamipress' ),
        'gamipress_digimember_purchase_download_product'                       => __( 'Purchase any download product', 'gamipress' ),
        'gamipress_digimember_purchase_specific_download_product'              => __( 'Purchase a specific download product', 'gamipress' ),
        // Cancel product
        'gamipress_digimember_cancel_product'                                   => __( 'Cancel any product', 'gamipress' ),
        'gamipress_digimember_cancel_specific_product'                          => __( 'Cancel a specific product', 'gamipress' ),
        'gamipress_digimember_cancel_membership_product'                        => __( 'Cancel any membership product', 'gamipress' ),
        'gamipress_digimember_cancel_specific_membership_product'               => __( 'Cancel a specific membership product', 'gamipress' ),
        'gamipress_digimember_cancel_download_product'                          => __( 'Cancel any download product', 'gamipress' ),
        'gamipress_digimember_cancel_specific_download_product'                 => __( 'Cancel a specific download product', 'gamipress' ),

    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_digimember_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_digimember_specific_activity_triggers( $specific_activity_triggers ) {

    // Purchase product
    $specific_activity_triggers['gamipress_digimember_purchase_specific_product'] = array( 'digimember_product' );
    $specific_activity_triggers['gamipress_digimember_purchase_specific_membership_product'] = array( 'digimember_membership_product' );
    $specific_activity_triggers['gamipress_digimember_purchase_specific_download_product'] = array( 'digimember_download_product' );
    // Cancel product
    $specific_activity_triggers['gamipress_digimember_cancel_specific_product'] = array( 'digimember_product' );
    $specific_activity_triggers['gamipress_digimember_cancel_specific_membership_product'] = array( 'digimember_membership_product' );
    $specific_activity_triggers['gamipress_digimember_cancel_specific_download_product'] = array( 'digimember_download_product' );

    return $specific_activity_triggers;

}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_digimember_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_digimember_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Purchase product
    $specific_activity_trigger_labels['gamipress_digimember_purchase_specific_product'] = __( 'Purchase %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_digimember_purchase_specific_membership_product'] = __( 'Purchase %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_digimember_purchase_specific_download_product'] = __( 'Purchase %s', 'gamipress' );
    // Cancel product
    $specific_activity_trigger_labels['gamipress_digimember_cancel_specific_product'] = __( 'Cancel %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_digimember_cancel_specific_membership_product'] = __( 'Cancel %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_digimember_cancel_specific_download_product'] = __( 'Cancel %s', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_digimember_specific_activity_trigger_label' );

/**
 * Get plugin specific activity trigger post title
 *
 * @since  1.0.0
 *
 * @param  string   $post_title
 * @param  integer  $specific_id
 * @param  string   $trigger_type
 *
 * @return string
 */
function gamipress_digimember_specific_activity_trigger_post_title( $post_title, $specific_id, $trigger_type ) {

    switch( $trigger_type ) {
        // Purchase product
        case 'gamipress_digimember_purchase_specific_product':
        case 'gamipress_digimember_purchase_specific_membership_product':
        case 'gamipress_digimember_purchase_specific_download_product':
        // Cancel product
        case 'gamipress_digimember_cancel_specific_product':
        case 'gamipress_digimember_cancel_specific_membership_product':
        case 'gamipress_digimember_cancel_specific_download_product':
            if( absint( $specific_id ) !== 0 ) {

                // Get the product title
                $post_title = gamipress_digimember_get_product_title( $specific_id );
            }
            break;
    }

    return $post_title;

}
add_filter( 'gamipress_specific_activity_trigger_post_title', 'gamipress_digimember_specific_activity_trigger_post_title', 10, 3 );

/**
 * Get plugin specific activity trigger permalink
 *
 * @since  1.0.0
 *
 * @param  string   $permalink
 * @param  integer  $specific_id
 * @param  string   $trigger_type
 * @param  integer  $site_id
 *
 * @return string
 */
function gamipress_digimember_specific_activity_trigger_permalink( $permalink, $specific_id, $trigger_type, $site_id ) {

    switch( $trigger_type ) {
        // Purchase product
        case 'gamipress_digimember_purchase_specific_product':
        case 'gamipress_digimember_purchase_specific_membership_product':
        case 'gamipress_digimember_purchase_specific_download_product':
        // Cancel product
        case 'gamipress_digimember_cancel_specific_product':
        case 'gamipress_digimember_cancel_specific_membership_product':
        case 'gamipress_digimember_cancel_specific_download_product':
            $permalink = '';
            break;
    }

    return $permalink;

}
add_filter( 'gamipress_specific_activity_trigger_permalink', 'gamipress_digimember_specific_activity_trigger_permalink', 10, 4 );

/**
 * Get user for a digimembern trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $user_id user ID to override.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 * @return integer          User ID.
 */
function gamipress_digimember_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Purchase product
        case 'gamipress_digimember_purchase_product':
        case 'gamipress_digimember_purchase_specific_product':
        case 'gamipress_digimember_purchase_membership_product':
        case 'gamipress_digimember_purchase_specific_membership_product':
        case 'gamipress_digimember_purchase_download_product':
        case 'gamipress_digimember_purchase_specific_download_product':
        // Cancel product
        case 'gamipress_digimember_cancel_product':
        case 'gamipress_digimember_cancel_specific_product':
        case 'gamipress_digimember_cancel_membership_product':
        case 'gamipress_digimember_cancel_specific_membership_product':
        case 'gamipress_digimember_cancel_download_product':
        case 'gamipress_digimember_cancel_specific_download_product':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_digimember_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a digimembern specific trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer  $specific_id Specific ID.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          Specific ID.
 */
function gamipress_digimember_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_digimember_purchase_specific_product':
        case 'gamipress_digimember_purchase_specific_membership_product':
        case 'gamipress_digimember_purchase_specific_download_product':
        case 'gamipress_digimember_cancel_specific_product':
        case 'gamipress_digimember_cancel_specific_membership_product':
        case 'gamipress_digimember_cancel_specific_download_product':
            $specific_id = $args[2];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_digimember_specific_trigger_get_id', 10, 3 );

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
function gamipress_digimember_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Purchase product
        case 'gamipress_digimember_purchase_product':
        case 'gamipress_digimember_purchase_specific_product':
        case 'gamipress_digimember_purchase_membership_product':
        case 'gamipress_digimember_purchase_specific_membership_product':
        case 'gamipress_digimember_purchase_download_product':
        case 'gamipress_digimember_purchase_specific_download_product':
        // Cancel product
        case 'gamipress_digimember_cancel_product':
        case 'gamipress_digimember_cancel_specific_product':
        case 'gamipress_digimember_cancel_membership_product':
        case 'gamipress_digimember_cancel_specific_membership_product':
        case 'gamipress_digimember_cancel_download_product':
        case 'gamipress_digimember_cancel_specific_download_product':
            // Add the order ID, product ID, product type and the reason
            $log_meta['order_id'] = $args[0];
            $log_meta['product_id'] = $args[2];
            $log_meta['product_type'] = $args[3];
            $log_meta['reason'] = $args[5];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_digimember_log_event_trigger_meta_data', 10, 5 );