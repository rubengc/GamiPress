<?php
/**
 * Admin
 *
 * @package GamiPress\LifterLMS\Admin
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_LIFTERLMS_DIR . 'includes/admin/recount-activity.php';

/**
 * LifterLMS automatic updates
 *
 * @since  1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_lifterlms_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress'] = __( 'LifterLMS integration', 'gamipress' );

    return $automatic_updates_plugins;

}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_lifterlms_automatic_updates' );