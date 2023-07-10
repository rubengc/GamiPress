<?php
/**
 * Admin
 *
 * @package     GamiPress\WP_Simple_Pay\Admin
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Plugin automatic updates
 *
 * @since 1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_wp_simple_pay_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress'] = __( 'WPAdverts integration', 'gamipress' );

    return $automatic_updates_plugins;

}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_wp_simple_pay_automatic_updates' );