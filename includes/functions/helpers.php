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
 * Utility function to get the condition option parameter
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