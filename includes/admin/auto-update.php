<?php
/**
 * Auto Update
 *
 * @package     GamiPress\Admin\Auto_Update
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
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

    $automatic_updates = (bool) gamipress_get_option( 'automatic_updates', false );

    // Return if not automatic updates enabled
    if( $automatic_updates !== true ) {
        return $update;
    }

    $automatic_updates_plugins = gamipress_get_option( 'automatic_updates_plugins', false );

    // if not is an array, initialize
    if( ! is_array( $automatic_updates_plugins  ) ) {
        $automatic_updates_plugins = array();
    }

    // Automatic updates plugins is an array of plugin_slug => plugin_title, so we need the keys
    $automatic_updates_plugins = array_keys( $automatic_updates_plugins );

    // Add gamipress to the plugins slugs
    $automatic_updates_plugins[] = 'gamipress';

    // Just return true if automatic updates was checked and plugin is on the automatic updates plugins array
    if( in_array( $item['slug'], $automatic_updates_plugins ) ) {
        return $update;
    }

    return $update;

}
add_filter( 'auto_update_plugin', 'gamipress_auto_update_plugin', 10, 2 );