<?php
/**
 * Auto Update
 *
 * @package     GamiPress\Admin\Auto_Update
 * @since       1.1.2
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Filters the auto update plugin routine to allow GamiPress to be automatically updated.
 *
 * @since 1.1.2
 *
 * @param bool $update  Flag to update the plugin or not.
 * @param array $item   Update data about a specific plugin.
 *
 * @return bool $update The new update state.
 */
function gamipress_auto_update_plugin( $update, $item ) {

    $item = (array) $item;

    // If do not have everything we need, return
    if ( ! isset( $item['new_version'] ) || ! isset( $item['slug'] ) ) {
        return $update;
    }

    // If not GamiPress, return
    if( $item['slug'] !== 'gamipress' ) {
        return $update;
    }

    $automatic_updates = (bool) gamipress_get_option( 'automatic_updates', false );

    // Just return true if automatic updates was checked
    if( $automatic_updates === true ) {
        return true;
    }

    return $update;

}
add_filter( 'auto_update_plugin', 'gamipress_auto_update_plugin', 10, 2 );