<?php
/**
 * Admin
 *
 * @package GamiPress\MemberPress\Admin
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * MemberPress automatic updates
 *
 * @since  1.0.2
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_memberpress_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress'] = __( 'MemberPress integration', 'gamipress' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_memberpress_automatic_updates' );