<?php
/**
 * Triggers
 *
 * @package GamiPress\WP_Simple_Pay\Triggers
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin specific triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_wp_simple_pay_activity_triggers( $triggers ) {

    $triggers['WP Simple Pay'] = array(
        'gamipress_wp_simple_pay_purchase'        => __( 'Complete a purchase through any form', 'gamipress' ),
        'gamipress_wp_simple_pay_specific_purchase'        => __( 'Complete a purchase through a specific form', 'gamipress' ),
    );

    if( SIMPLE_PAY_PLUGIN_NAME === 'WP Simple Pay Pro' ) {
        $triggers['WP Simple Pay']['gamipress_wp_simple_pay_renew_subscription'] = __( 'Renew a subscription', 'gamipress' );
    }

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_wp_simple_pay_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_wp_simple_pay_specific_activity_triggers( $specific_activity_triggers ) {

    $specific_activity_triggers['gamipress_wp_simple_pay_specific_purchase'] = array( 'simple-pay' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_wp_simple_pay_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_wp_simple_pay_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_wp_simple_pay_specific_purchase'] = __( 'Complete a purchase through %s form', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_wp_simple_pay_specific_activity_trigger_label' );

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
function gamipress_wp_simple_pay_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_wp_simple_pay_purchase':
        case 'gamipress_wp_simple_pay_specific_purchase':
        case 'gamipress_wp_simple_pay_renew_subscription':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_wp_simple_pay_trigger_get_user_id', 10, 3);


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
function gamipress_wp_simple_pay_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_wp_simple_pay_specific_purchase':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_wp_simple_pay_specific_trigger_get_id', 10, 3 );

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
function gamipress_wp_simple_pay_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_wp_simple_pay_purchase':
        case 'gamipress_wp_simple_pay_specific_purchase':
        case 'gamipress_wp_simple_pay_renew_subscription':
            // Add the post ID
            $log_meta['post_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_wp_simple_pay_log_event_trigger_meta_data', 10, 5 );