<?php
/**
 * Admin
 *
 * @package GamiPress\LearnPress\Admin
 * @since 1.0.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * LearnPress automatic updates
 *
 * @since  1.0.1
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_lp_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress'] = __( 'LearnPress integration', 'gamipress' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_lp_automatic_updates' );