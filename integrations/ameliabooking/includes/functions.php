<?php
/**
 * Functions
 *
 * @package GamiPress\AmeliaBooking\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_ameliabooking_ajax_get_posts() {

    global $wpdb;

    $results = array();

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? sanitize_text_field( $_REQUEST['q'] ) : '';
    $search = $wpdb->esc_like( $search );

    if( isset( $_REQUEST['post_type'] ) && in_array( 'ameliabooking_appointment', $_REQUEST['post_type'] ) ) {

        // Get the services
        $services = $wpdb->get_results( $wpdb->prepare(
            "SELECT a.id, a.name
            FROM {$wpdb->prefix}amelia_services AS a
            WHERE a.name LIKE %s",
            "%%{$search}%%"
        ) );

        foreach ( $services as $service ) {

            // Results should meet same structure like posts
            $results[] = array(
                'ID' => $service->id,
                'post_title' => $service->name,
            );

        }

        // Return our results
        wp_send_json_success( $results );
        die;

    } else if( isset( $_REQUEST['post_type'] ) && in_array( 'ameliabooking_event', $_REQUEST['post_type'] ) ) {

        // Get the events
        $events = $wpdb->get_results( $wpdb->prepare(
            "SELECT e.id, e.name, e.status, e.maxCapacity, e.bookingCloses
            FROM {$wpdb->prefix}amelia_events AS e
            WHERE e.name LIKE %s",
            "%%{$search}%%"
        ) );

        // Get current date to compare with end period
        $date = date( "Y-m-d H:i:s", current_time('timestamp') );

        foreach ( $events as $event ) {
            
            if ( $event->status !== 'rejected' ){

                $event_periodStart = gamipress_ameliabooking_get_date_close( $event->id );
                
                $event_capacity = gamipress_ameliabooking_check_capacity( $event->id );
                // To check if event is full
                if ($event_capacity < $event->maxCapacity) {
                    // To compare if event is opened
                    if ( $date < $event->bookingCloses || $date < $event_periodStart){

                        $results[] = array(
                            'ID'    => $event->id,
                            'post_title'  => $event->name,
                        );         

                    }
                }        
                
            }
            
        }

        // Return our results
        wp_send_json_success( $results );
        die;
    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_ameliabooking_ajax_get_posts', 5 );

// Get the service title
function gamipress_ameliabooking_get_service_title( $service_id ) {

    if( absint( $service_id ) === 0 ) return '';

    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare(
        "SELECT a.name FROM {$wpdb->prefix}amelia_services AS a WHERE a.id = %d",
        absint( $service_id )
    ) );

}

// Get the event title
function gamipress_ameliabooking_get_event_title( $event_id ) {

    if( absint( $event_id ) === 0 ) return '';

    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare(
        "SELECT e.name FROM {$wpdb->prefix}amelia_events AS e WHERE e.id = %d",
        absint( $event_id )
    ) );

}

/**
 * Get Amelia event date close. An event is closed when start if a date is not specified in Amelia settings
 *
 * @since 1.0.0
 *
 * @param int   $event_id   ID event
 * 
 * @return string
 * 
 */
function gamipress_ameliabooking_get_date_close( $event_id ) {

    global $wpdb;

    $event_periodStart = $wpdb->get_var( $wpdb->prepare(
        "SELECT periodStart FROM {$wpdb->prefix}amelia_events_periods WHERE eventId = %d",
        absint( $event_id )
    ) );

    return $event_periodStart;

}

/**
 * Check if event is full
 *
 * @since 1.0.0
 *
 * @param int   $event_id   ID event
 * 
 * @return int
 * 
 */
function gamipress_ameliabooking_check_capacity( $event_id ) {

    global $wpdb;

    $event_counter = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(id) FROM {$wpdb->prefix}amelia_customer_bookings_to_events_periods WHERE eventPeriodId = %d",
        absint( $event_id )
    ) );

    return absint( $event_counter );

}