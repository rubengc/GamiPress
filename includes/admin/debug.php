<?php
/**
 * Debug
 *
 * @package     GamiPress\Admin\Debug
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.6
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Helper function to check if debug mode is enabled
 *
 * @since       1.0.6
 *
 * @return bool
 */
function gamipress_is_debug_mode() {

    return (bool) gamipress_get_option( 'debug_mode', false );
}