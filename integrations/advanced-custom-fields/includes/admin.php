<?php
/**
 * Admin
 *
 * @package GamiPress\Advanced_Custom_Fields\Admin
 * @since 1.0.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Advanced Custom Fields automatic updates
 *
 * @since  1.0.1
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_acf_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress'] = __( 'Advanced Custom Fields integration', 'gamipress' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_acf_automatic_updates' );