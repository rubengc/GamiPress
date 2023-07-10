<?php
/**
 * Functions
 *
 * @package GamiPress\Meta_Box\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get conditions
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_meta_box_get_value_conditions() {

    return array(
        // String
        'equal'             => __( 'is equal to', 'gamipress' ),
        'not_equal'         => __( 'is not equal to', 'gamipress' ),
        'contains'          => __( 'contains', 'gamipress' ),
        'not_contains'      => __( 'does not contains', 'gamipress' ),
        'start_with'        => __( 'starts with', 'gamipress' ),
        'not_start_with'    => __( 'does not starts with', 'gamipress' ),
        'ends_with'         => __( 'ends with', 'gamipress' ),
        'not_ends_with'     => __( 'does not ends with', 'gamipress' ),
        // Number
        'less_than'         => __( 'is less than', 'gamipress' ),
        'greater_than'      => __( 'is greater than', 'gamipress' ),
        'less_or_equal'     => __( 'is less or equal to', 'gamipress' ),
        'greater_or_equal'  => __( 'is greater or equal to', 'gamipress' ),
    );

}

/**
 * Utility function to get the condition option parameter
 *
 * @since 1.4.5
 *
 * @param mixed     $a          Element to match
 * @param mixed     $b          Element to compare
 * @param string    $condition  The condition to compare elements
 *
 * @return bool
 */
function gamipress_meta_box_condition_matches( $a, $b, $condition ) {

    if( empty( $condition ) ) {
        $condition = 'equal';
    }

    $matches = false;

    // Ensure that the element to compare is a string
    if( is_array( $b ) ) {
        $b = implode( ',', $b );
    }

    $a = strval( $a );
    $b = strval( $b );

    // If not is a string condition and elements to compare are numerics, turn them to float
    if( ! gamipress_meta_box_is_string_condition( $condition ) ) {
        if( is_numeric( $a ) ) {
            $a = (float) $a;
        }

        if( is_numeric( $b ) ) {
            $b = (float) $b;
        }
    }

    switch( $condition ) {
        case 'equal':
        case '=':
        case '==':
        case '===':
            $matches = ( $a == $b );
            break;
        case 'not_equal':
        case '!=':
        case '!==':
            $matches = ( $a != $b );
            break;
        case 'less_than':
        case '<':
            $matches = ( $a < $b );
            break;
        case 'greater_than':
        case '>':
            $matches = ( $a > $b );
            break;
        case 'less_or_equal':
        case '<=':
            $matches = ( $a <= $b );
            break;
        case 'greater_or_equal':
        case '>=':
            $matches = ( $a >= $b );
            break;
        case 'contains':
            $matches = ( strpos( $a, strval( $b ) ) !== false );
            break;
        case 'not_contains':
            $matches = ( strpos( $a, strval( $b ) ) === false );
            break;
        case 'start_with':
            $matches = ( gamipress_starts_with( $a, $b ) );
            break;
        case 'not_start_with':
            $matches = ( ! gamipress_starts_with( $a, $b ) );
            break;
        case 'ends_with':
            $matches = ( gamipress_ends_with( $a, $b ) );
            break;
        case 'not_ends_with':
            $matches = ( ! gamipress_ends_with( $a, $b ) );
            break;
    }

    return $matches;

}

/**
 * Utility function to meet if condition is related to string
 *
 * @since 1.7.6
 *
 * @param string    $condition  The condition to check
 *
 * @return bool
 */
function gamipress_meta_box_is_string_condition( $condition ) {

    $return = false;

    switch( $condition ) {
        case 'contains':
        case 'not_contains':
        case 'start_with':
        case 'not_start_with':
        case 'ends_with':
        case 'not_ends_with':
            $return = true;
            break;
    }

    return $return;

}