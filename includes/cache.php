<?php
/**
 * GamiPress Cache Class
 *
 * Used to store commonly used query results
 *
 * @package     GamiPress\Cache
 * @since       1.4.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Helper function to get a cached element.
 *
 * @since 1.4.7
 *
 * @param string    $key
 * @param mixed     $default
 *
 * @return mixed
 */
function gamipress_get_cache( $key = '', $default = null ) {

    if( isset( GamiPress()->cache[$key] ) ) {
        return GamiPress()->cache[$key];
    }

    return $default;

}

/**
 * Helper function to set a cached element.
 *
 * @since 1.4.7
 *
 * @param string    $key
 * @param mixed     $value
 */
function gamipress_set_cache( $key = '', $value = '' ) {

    GamiPress()->cache[$key] = $value;

}