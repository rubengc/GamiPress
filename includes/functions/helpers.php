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