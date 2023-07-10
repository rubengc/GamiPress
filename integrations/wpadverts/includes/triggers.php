<?php
/**
 * Triggers
 *
 * @package GamiPress\WPAdverts\Triggers
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
function gamipress_wpadverts_activity_triggers( $triggers ) {

    $triggers[__( 'WPAdverts', 'gamipress' )] = array(

        // New advert
        'gamipress_wpadverts_new_advert'        => __( 'Publish an advert', 'gamipress' ),
        'gamipress_wpadverts_new_free_advert'   => __( 'Publish a free advert', 'gamipress' ),
        'gamipress_wpadverts_new_paid_advert'   => __( 'Publish a paid advert', 'gamipress' ),
        // Send message
        'gamipress_wpadverts_send_message'              => __( 'Send a message to an advert author', 'gamipress' ),
        'gamipress_wpadverts_specific_send_message'     => __( 'Send a message to a specific advert author', 'gamipress' ),
        'gamipress_wpadverts_receive_message'           => __( 'Receive a message from an advert', 'gamipress' ),
        'gamipress_wpadverts_specific_receive_message'  => __( 'Receive a message from a specific advert', 'gamipress' ),
        // Payment
        'gamipress_wpadverts_pay_advert'            => __( 'Pay an advert', 'gamipress' ),
        'gamipress_wpadverts_pay_specific_advert'   => __( 'Pay a specific advert', 'gamipress' ),
        'gamipress_wpadverts_renew_advert'          => __( 'Renew an advert', 'gamipress' ),
        'gamipress_wpadverts_renew_specific_advert' => __( 'Renew a specific advert', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_wpadverts_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_wpadverts_specific_activity_triggers( $specific_activity_triggers ) {

    // Send message
    $specific_activity_triggers['gamipress_wpadverts_specific_send_message'] = array( 'advert' );
    $specific_activity_triggers['gamipress_wpadverts_specific_receive_message'] = array( 'advert' );
    // Payment
    $specific_activity_triggers['gamipress_wpadverts_pay_specific_advert'] = array( 'advert' );
    $specific_activity_triggers['gamipress_wpadverts_renew_specific_advert'] = array( 'advert' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_wpadverts_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_wpadverts_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Send message
    $specific_activity_trigger_labels['gamipress_wpadverts_specific_send_message'] = __( 'Send a message to %s author', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wpadverts_specific_receive_message'] =  __( 'Receive a message from %s advert', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_wpadverts_specific_activity_trigger_label' );

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
function gamipress_wpadverts_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // New advert
        case 'gamipress_wpadverts_new_advert':
        case 'gamipress_wpadverts_new_free_advert':
        case 'gamipress_wpadverts_new_paid_advert':
        // Send message
        case 'gamipress_wpadverts_send_message':
        case 'gamipress_wpadverts_specific_send_message':
        case 'gamipress_wpadverts_receive_message':
        case 'gamipress_wpadverts_specific_receive_message':
        // Payment
        case 'gamipress_wpadverts_pay_advert':
        case 'gamipress_wpadverts_pay_specific_advert':
        case 'gamipress_wpadverts_renew_advert':
        case 'gamipress_wpadverts_renew_specific_advert':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_wpadverts_trigger_get_user_id', 10, 3);


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
function gamipress_wpadverts_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        // Send message
        case 'gamipress_wpadverts_specific_send_message':
        case 'gamipress_wpadverts_specific_receive_message':
        // Payment
        case 'gamipress_wpadverts_pay_specific_advert':
        case 'gamipress_wpadverts_renew_specific_advert':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_wpadverts_specific_trigger_get_id', 10, 3 );

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
function gamipress_wpadverts_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // New advert
        case 'gamipress_wpadverts_new_advert':
        case 'gamipress_wpadverts_new_free_advert':
        case 'gamipress_wpadverts_new_paid_advert':
            // Send message
        case 'gamipress_wpadverts_send_message':
        case 'gamipress_wpadverts_specific_send_message':
        case 'gamipress_wpadverts_receive_message':
        case 'gamipress_wpadverts_specific_receive_message':
            // Add the post ID
            $log_meta['post_id'] = $args[0];
            break;
        // Payment
        case 'gamipress_wpadverts_pay_advert':
        case 'gamipress_wpadverts_pay_specific_advert':
        case 'gamipress_wpadverts_renew_advert':
        case 'gamipress_wpadverts_renew_specific_advert':
            // Add thepsot and payment IDs
            $log_meta['post_id'] = $args[0];
            $log_meta['payment_id'] = $args[2];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_wpadverts_log_event_trigger_meta_data', 10, 5 );