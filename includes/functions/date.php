<?php
/**
 * Date Functions
 *
 * @package     GamiPress\Date_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.7.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Helper function to format a given date or timestamp
 *
 * @since 1.6.9
 *
 * @param string    $format Date format
 * @param mixed     $date
 *
 * @return string|false
 */
function gamipress_date( $format, $date = '' ) {

    $date_formatted = false;

    if( strtotime( $date ) ) {
        // Ensure date given is correct
        $date_formatted = date( $format, strtotime( $date ) );
    } else if( absint( $date ) > 0 ) {
        // Support for timestamp value
        $date_formatted = date( $format, absint( $date ) );
    }

    return $date_formatted;

}

/**
 * Get a specific period date range
 *
 * @see gamipress_get_time_periods()
 *
 * @since 1.6.9
 *
 * @param string $period
 *
 * @return array
 */
function gamipress_get_period_range( $period = '' ) {

    // Setup date range var
    $date_range = array(
        'start' => '',
        'end'   => '',
    );

    if( $period !== '' ) {

        switch( $period ) {
            case 'today':
                $date_range = array(
                    'start' => date( 'Y-m-d 00:00:00', current_time( 'timestamp' ) ),
                    'end' => '',
                );
                break;
            case 'yesterday':
                $date_range = array(
                    'start' => date( 'Y-m-d 00:00:00', strtotime( '-1 day', current_time( 'timestamp' ) ) ),
                    'end' => date( 'Y-m-d 23:59:59', strtotime( '-1 day', current_time( 'timestamp' ) ) ),
                );
                break;
            case 'current-week':
            case 'this-week':
                $date_range = gamipress_get_date_range( 'week' );
                break;
            case 'past-week':
                $previous_week = strtotime( '-1 week', current_time( 'timestamp' ) );
                $date_range = gamipress_get_date_range( 'week', $previous_week );
                break;
            case 'current-month':
            case 'this-month':
                $date_range = gamipress_get_date_range( 'month' );
                break;
            case 'past-month':
                $previous_month = strtotime( '-1 month', current_time( 'timestamp' ) );
                $date_range = gamipress_get_date_range( 'month', $previous_month );
                break;
            case 'current-year':
            case 'this-year':
                $date_range = gamipress_get_date_range( 'year' );
                break;
            case 'past-year':
                $previous_year = strtotime( '-1 year', current_time( 'timestamp' ) );
                $date_range = gamipress_get_date_range( 'year', $previous_year );
                break;
            default:
                // For custom ranges use 'gamipress_get_period_range' filter
                break;
        }
    }

    /**
     * Filter the period date range
     *
     * @since 1.6.9
     *
     * @param array     $date_range An array with period date range
     * @param string    $period     Given period, see gamipress_get_time_periods()
     */
    return $date_range = apply_filters( 'gamipress_get_period_range', $date_range, $period );
}

/**
 * Helper function to get a range date based on a given date
 *
 * @since 1.6.9
 *
 * @param string            $range (week|month|year)
 * @param integer|string    $date
 *
 * @return array
 */
function gamipress_get_date_range( $range = '', $date = 0 ) {

    if( gettype( $date ) === 'string' ) {
        $date = strtotime( $date );
    }

    if( ! $date ) {
        $date = current_time( 'timestamp' );
    }

    $start_date = 0;
    $end_date = 0;

    switch( $range ) {
        case 'week':

            // Weekly range
            $start_date    = strtotime( 'monday this week', $date );
            $end_date      = strtotime( 'midnight', strtotime( 'sunday this week', $date ) );

            break;
        case 'month':

            // Monthly range
            $start_date    = strtotime( date( 'Y-m-01', $date ) );
            $end_date      = strtotime( 'midnight', strtotime( 'last day of this month', $date ) );

            break;
        case 'year':

            // Yearly range
            $start_date    = strtotime( date( 'Y-01-01', $date ) );
            $end_date      = strtotime( date( 'Y-12-31', $date ) );

            break;
    }

    return array(
        'start'    => date( 'Y-m-d 00:00:00', $start_date ),
        'end'      => date( 'Y-m-d 23:59:59', $end_date )
    );

}