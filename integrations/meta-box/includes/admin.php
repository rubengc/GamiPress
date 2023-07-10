<?php
/**
 * Admin
 *
 * @package GamiPress\Meta_Box\Admin
 * @since 1.0.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Meta Box automatic updates
 *
 * @since  1.0.1
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_meta_box_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress'] = __( 'Meta Box integration', 'gamipress' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_meta_box_automatic_updates' );