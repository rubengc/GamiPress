<?php
/**
 * Triggers
 *
 * @package GamiPress\Give\Triggers
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
function gamipress_give_activity_triggers( $triggers ) {

    $triggers[__( 'Give', 'gamipress' )] = array(
        'gamipress_give_new_donation'               => __( 'Make a donation', 'gamipress' ),
        'gamipress_give_new_donation_min_amount'    => __( 'Make a donation of a minimum amount', 'gamipress' ),
        'gamipress_give_new_donation_specific_form' => __( 'Make a donation through a specific form', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_give_activity_triggers' );

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
function gamipress_give_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $minimum_amount = ( isset( $requirement['give_amount'] ) ) ? floatval( $requirement['give_amount'] ) : 0;

    switch( $requirement['trigger_type'] ) {

        // Minimum amount event
        case 'gamipress_give_new_donation_min_amount':

            $formatted_amount = give_currency_filter( give_format_amount( $minimum_amount ), array( 'decode_currency' => true ) );

            return sprintf( __( 'Make a donation of %s or higher', 'gamipress' ), $formatted_amount );
            break;

    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_give_activity_trigger_label', 10, 3 );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_give_specific_activity_triggers( $specific_activity_triggers ) {

    $specific_activity_triggers['gamipress_give_new_donation_specific_form'] = array( 'give_forms' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_give_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_give_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_give_new_donation_specific_form'] = __( 'Make a donation through the form %s', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_give_specific_activity_trigger_label' );

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
function gamipress_give_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_give_new_donation':
        case 'gamipress_give_new_donation_min_amount':
        case 'gamipress_give_new_donation_specific_form':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_give_trigger_get_user_id', 10, 3 );

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
function gamipress_give_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_give_new_donation_specific_form':
            $specific_id = $args[2];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_give_specific_trigger_get_id', 10, 3 );

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
function gamipress_give_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_give_new_donation':
        case 'gamipress_give_new_donation_min_amount':
        case 'gamipress_give_new_donation_specific_form':
            // Add the payment ID, form ID and amount donated
            $log_meta['payment_id'] = $args[0];
            $log_meta['form_id'] = $args[2];
            $log_meta['amount'] = $args[3];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_give_log_event_trigger_meta_data', 10, 5 );