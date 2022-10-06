<?php
/**
 * Helpers Functions
 *
 * @package     GamiPress\Helpers_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.7.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Utility to check whether function is disabled.
 *
 * @since 1.3.7
 *
 * @param string $function  Name of the function.
 * @return bool             Whether or not function is disabled.
 */
function gamipress_is_function_disabled( $function ) {
    $disabled = explode( ',',  ini_get( 'disable_functions' ) );

    return in_array( $function, $disabled );
}

/**
 * Helper function to recursively check if something is in a multidimensional array
 *
 * @since 1.4.1
 *
 * @param mixed $needle
 * @param array $haystack
 * @param bool  $strict
 *
 * @return bool
 */
function gamipress_in_array( $needle, $haystack, $strict = false ) {

    foreach( $haystack as $item ) {
        if (
            ( $strict ? $item === $needle : $item == $needle )
            || ( is_array( $item ) && gamipress_in_array( $needle, $item, $strict ) )
        ) {
            return true;
        }
    }

    return false;
}

/**
 * Helper function to check if a string starts by needle string given
 *
 * @since 1.7.0
 *
 * @param string $haystack
 * @param string $needle
 *
 * @return bool
 */
function gamipress_starts_with( $haystack, $needle ) {
    return strncmp( $haystack, $needle, strlen( $needle ) ) === 0;
}

/**
 * Helper function to check if a string ends by needle string given
 *
 * @since 1.7.0
 *
 * @param string $haystack
 * @param string $needle
 *
 * @return bool
 */
function gamipress_ends_with( $haystack, $needle ) {
    return $needle === '' || substr_compare( $haystack, $needle, -strlen( $needle ) ) === 0;
}

/**
 * Helper function to get all editable roles included if get_editable_roles() doesn't exists
 *
 * @since 1.8.8
 *
 * @return array[]|mixed|void
 */
function gamipress_get_editable_roles() {

    if( function_exists('get_editable_roles' ) ) {
        $roles = get_editable_roles();
    } else {
        $roles = wp_roles()->roles;

        $roles = apply_filters( 'editable_roles', $roles );
    }

    return $roles;

}

/**
 * Utility function to get the number condition options
 *
 * @since 2.4.2
 *
 * @return array
 */
function gamipress_number_condition_options() {

    return array(
        'equal'             => __( 'equal to', 'gamipress'),
        'not_equal'         => __( 'not equal to', 'gamipress'),
        'less_than'         => __( 'less than', 'gamipress' ),
        'greater_than'      => __( 'greater than', 'gamipress' ),
        'less_or_equal'     => __( 'less or equal to', 'gamipress' ),
        'greater_or_equal'  => __( 'greater or equal to', 'gamipress' ),
    );

}

/**
 * Utility function to check the condition option parameter
 *
 * @since 2.0.0
 *
 * @param int|float $to_match   Number to match
 * @param int|float $to_compare Number to compare
 * @param string    $condition  The condition to compare numbers
 *
 * @return bool
 */
function gamipress_number_condition_matches( $to_match, $to_compare, $condition ) {

    if( empty( $condition ) ) {
        $condition = 'equal';
    }

    $matches = false;

    switch( $condition ) {
        case 'equal':
        case '=':
        case '==':
        case '===':
            $matches = ( $to_match == $to_compare );
            break;
        case 'not_equal':
        case '!=':
        case '!==':
            $matches = ( $to_match != $to_compare );
            break;
        case 'less_than':
        case '<':
            $matches = ( $to_match < $to_compare );
            break;
        case 'greater_than':
        case '>':
            $matches = ( $to_match > $to_compare );
            break;
        case 'less_or_equal':
        case '<=':
            $matches = ( $to_match <= $to_compare );
            break;
        case 'greater_or_equal':
        case '>=':
            $matches = ( $to_match >= $to_compare );
            break;
    }

    return $matches;

}

/**
 * Utility function to get the condition options
 * Note: For numbers, use the gamipress_number_condition_options()
 *
 * @since 2.4.2
 *
 * @return array
 */
function gamipress_condition_options() {

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
 * @since 2.4.2
 *
 * @param mixed     $a          Element to match
 * @param mixed     $b          Element to compare
 * @param string    $condition  The condition to compare elements
 *
 * @return bool
 */
function gamipress_condition_matches( $a, $b, $condition ) {

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
    if( ! gamipress_is_string_condition( $condition ) ) {
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
 * @since 2.4.2
 *
 * @param string    $condition  The condition to check
 *
 * @return bool
 */
function gamipress_is_string_condition( $condition ) {

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