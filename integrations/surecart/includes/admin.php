<?php
/**
 * Admin
 *
 * @package GamiPress\SureCart\Admin
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * SureCart automatic updates
 *
 * @since  1.0.2
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_surecart_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress'] = __( 'SureCart integration', 'gamipress' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_surecart_automatic_updates' );