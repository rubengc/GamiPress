<?php
/**
 * Admin
 *
 * @package GamiPress\WooCommerce_Shipstation\Admin
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Plugin automatic updates
 *
 * @since  1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_woocommerce_shipstation_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress'] = __( 'WooCommerce Shipstation integration', 'gamipress-woocommerce-shipstation-integration' );

    return $automatic_updates_plugins;

}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_woocommerce_shipstation_automatic_updates' );