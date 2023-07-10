<?php
/**
 * Admin
 *
 * @package GamiPress\WS_Form\Admin
 * @since 1.0.0
 */
// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * WS Form automatic updates
 *
 * @since  1.0.1
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_ws_form_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress'] = __( 'WS Form integration', 'gamipress' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_ws_form_automatic_updates' );