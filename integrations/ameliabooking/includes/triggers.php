<?php
/**
 * Triggers
 *
 * @package GamiPress\AmeliaBooking\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin activity triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_ameliabooking_activity_triggers( $triggers ) {

    // AmeliaBooking
    $triggers[__( 'Amelia', 'gamipress' )] = array(
        // Appointments
        'gamipress_ameliabooking_user_books_appointment'         => __( 'Book an appointment for any service', 'gamipress' ),
        'gamipress_ameliabooking_user_books_appointment_service' => __( 'Book an appointment for a specific service', 'gamipress' ),
        // Events
        'gamipress_ameliabooking_user_books_event'               => __( 'Book for any event', 'gamipress' ),
        'gamipress_ameliabooking_user_books_specific_event'      => __( 'Book for a specific event', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_ameliabooking_activity_triggers' );

/**
 * Register specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_ameliabooking_specific_activity_triggers( $specific_activity_triggers ) {
    
    $specific_activity_triggers['gamipress_ameliabooking_user_books_appointment_service'] = array( 'ameliabooking_appointment' );
    $specific_activity_triggers['gamipress_ameliabooking_user_books_specific_event'] = array( 'ameliabooking_event' );
    
    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_ameliabooking_specific_activity_triggers' );

/**
 * Register specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_ameliabooking_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_ameliabooking_user_books_appointment_service'] = __( 'Book an appointment for %s service', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_ameliabooking_user_books_specific_event'] = __( 'Book for %s event', 'gamipress' );
    
    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_ameliabooking_specific_activity_trigger_label' );

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
function gamipress_ameliabooking_specific_activity_trigger_post_title( $post_title, $specific_id, $trigger_type ) {

    switch( $trigger_type ) {
        case 'gamipress_ameliabooking_user_books_appointment_service':
            if( absint( $specific_id ) !== 0 ) {
                // Get the service title
                $service_title = gamipress_ameliabooking_get_service_title( $specific_id );

                $post_title = $service_title;
            }
            break;
        case 'gamipress_ameliabooking_user_books_specific_event':
            if( absint( $specific_id ) !== 0 ) {
                // Get the event title
                $event_title = gamipress_ameliabooking_get_event_title( $specific_id );

                $post_title = $event_title;
            }
            break;
    }

    return $post_title;

}
add_filter( 'gamipress_specific_activity_trigger_post_title', 'gamipress_ameliabooking_specific_activity_trigger_post_title', 10, 3 );

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
function gamipress_ameliabooking_specific_activity_trigger_permalink( $permalink, $specific_id, $trigger_type, $site_id ) {

    switch( $trigger_type ) {
        case 'gamipress_ameliabooking_user_books_appointment_service':
        case 'gamipress_ameliabooking_user_books_specific_event':
            $permalink = '';
            break;
    }

    return $permalink;

}
add_filter( 'gamipress_specific_activity_trigger_permalink', 'gamipress_ameliabooking_specific_activity_trigger_permalink', 10, 4 );

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
function gamipress_ameliabooking_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Appointments
        case 'gamipress_ameliabooking_user_books_appointment':
        case 'gamipress_ameliabooking_user_books_appointment_service':
        // Events
        case 'gamipress_ameliabooking_user_books_event':
        case 'gamipress_ameliabooking_user_books_specific_event':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_ameliabooking_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $specific_id Specific ID to override.
 * @param  string  $trigger     Trigger name.
 * @param  array   $args        Passed trigger args.
 *
 * @return integer              Specific ID.
 */
function gamipress_ameliabooking_specific_trigger_get_id( $specific_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_ameliabooking_user_books_appointment_service':
            $specific_id = $args[2];
            break;
        case 'gamipress_ameliabooking_user_books_specific_event':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;

}

add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_ameliabooking_specific_trigger_get_id', 10, 3 );

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
function gamipress_ameliabooking_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {

        // Appointments
        case 'gamipress_ameliabooking_user_books_appointment':
        case 'gamipress_ameliabooking_user_books_appointment_service':
            // Add the appointment and service IDs
            $log_meta['appointment_id'] = $args[0];
            $log_meta['service_id'] = $args[2];
            break;

        // Events
        case 'gamipress_ameliabooking_user_books_event':
        case 'gamipress_ameliabooking_user_books_specific_event':
            // Add the event ID
            $log_meta['event_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_ameliabooking_log_event_trigger_meta_data', 10, 5 );