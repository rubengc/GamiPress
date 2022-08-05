<?php
/**
 * Upgrades
 *
 * @package     GamiPress\Admin\Upgrades
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.2.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_DIR . 'includes/admin/upgrades/1.1.0.php';
require_once GAMIPRESS_DIR . 'includes/admin/upgrades/1.2.7.php';
require_once GAMIPRESS_DIR . 'includes/admin/upgrades/1.2.8.php';
require_once GAMIPRESS_DIR . 'includes/admin/upgrades/1.3.0.php';
require_once GAMIPRESS_DIR . 'includes/admin/upgrades/1.3.1.php';
require_once GAMIPRESS_DIR . 'includes/admin/upgrades/1.3.7.php';
require_once GAMIPRESS_DIR . 'includes/admin/upgrades/1.4.3.php';
require_once GAMIPRESS_DIR . 'includes/admin/upgrades/1.4.7.php';
require_once GAMIPRESS_DIR . 'includes/admin/upgrades/1.5.1.php';
require_once GAMIPRESS_DIR . 'includes/admin/upgrades/1.8.7.php';
require_once GAMIPRESS_DIR . 'includes/admin/upgrades/2.3.7.php';

/**
 * GamiPress upgrades
 *
 * @since 1.1.0
 */
function gamipress_process_upgrades() {

    if ( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    // Get stored version
    $stored_version = get_option( 'gamipress_version', '1.0.0' );

    if( $stored_version === GAMIPRESS_VER ) {
        return;
    }

    /**
     * Before process upgrades action
     *
     * @since 1.1.0
     *
     * @param string $stored_version Latest upgrade version
     */
    do_action( 'gamipress_before_process_upgrades', $stored_version );

    /**
     * Version upgrade filter
     *
     * @since 1.1.0
     *
     * @param string $stored_version Latest upgrade version
     *
     * @return string
     */
    $stored_version = apply_filters( 'gamipress_process_upgrades', $stored_version );

    /**
     * After process upgrades action
     *
     * @since 1.1.0
     *
     * @param string $stored_version Latest upgrade version
     */
    do_action( 'gamipress_after_process_upgrades', $stored_version );

    // Updated stored version
    update_option( 'gamipress_version', $stored_version );

}
add_action( 'admin_init', 'gamipress_process_upgrades' );

/**
 * Get the latest GamiPress version that requires an upgrade
 *
 * @since 1.5.1
 *
 * @return string   Last version that required an upgrade
 */
function gamipress_get_last_required_upgrade() {

    $version = '1.1.0';

    /**
     * Get the last required upgrade (useful to meet if version stored and current required is the same)
     *
     * @since 1.5.1
     *
     * @param string $stored_version Latest upgrade version
     *
     * @return string
     */
    return apply_filters( 'gamipress_get_last_required_upgrade', $version );

}

/**
 * Helper function to check if GamiPress has been upgraded successfully
 *
 * @since 1.2.8
 *
 * @param string $desired_version
 *
 * @return bool
 */
function is_gamipress_upgraded_to( $desired_version = '1.0.0' ) {

    // Get stored version
    $stored_version = get_option( 'gamipress_version', '1.0.0' );

    return (bool) version_compare( $stored_version, $desired_version, '>=' );

}

/**
 * Get's the array of completed upgrade actions
 *
 * @since  1.2.8
 *
 * @return array The array of completed upgrades
 */
function gamipress_get_completed_upgrades() {

    $completed_upgrades = get_option( 'gamipress_completed_upgrades' );

    if ( ! is_array( $completed_upgrades ) ) {
        $completed_upgrades = array();
    }

    return $completed_upgrades;

}

/**
 * Check if the upgrade routine has been run for a specific action
 *
 * @since  1.2.8
 *
 * @param  string $upgrade_action The upgrade action to check completion for
 *
 * @return bool                   If the action has been added to the completed actions array
 */
function is_gamipress_upgrade_completed( $upgrade_action = '' ) {

    if ( empty( $upgrade_action ) ) {
        return false;
    }

    $completed_upgrades = gamipress_get_completed_upgrades();

    return in_array( $upgrade_action, $completed_upgrades );

}

/**
 * Adds an upgrade action to the completed upgrades array
 *
 * @since  1.2.8
 *
 * @param  string $upgrade_action The action to add to the completed upgrades array
 *
 * @return bool                   If the function was successfully added
 */
function gamipress_set_upgrade_complete( $upgrade_action = '' ) {

    if ( empty( $upgrade_action ) ) {
        return false;
    }

    $completed_upgrades   = gamipress_get_completed_upgrades();
    $completed_upgrades[] = $upgrade_action;

    // Remove any blanks, and only show uniques
    $completed_upgrades = array_unique( array_values( $completed_upgrades ) );

    return update_option( 'gamipress_completed_upgrades', $completed_upgrades );
}

/**
 * Utility function to check if a database table exists
 *
 * @since   1.3.5
 * @updated 1.4.0 Added support for network wide database
 * @updated 1.4.7 Cached return
 *
 * @param  string $table_name The desired table name
 *
 * @return bool               Whatever if table exists or not
 */
function gamipress_database_table_exists( $table_name ) {

    global $wpdb;

    $cache = gamipress_get_cache( 'installed_tables', array(), false );

    // If result already cached, return it
    if( isset( $cache[$table_name] ) ) {
        return $cache[$table_name];
    }

    $table_exist = $wpdb->get_var( $wpdb->prepare(
        "SHOW TABLES LIKE %s",
        $wpdb->esc_like( $table_name )
    ) );

    if( empty( $table_exist ) ) {
        $table_exist = $wpdb->get_var( $wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $wpdb->esc_like( $wpdb->prefix . $table_name )
        ) );
    }

    if( empty( $table_exist ) ) {
        $table_exist = $wpdb->get_var( $wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $wpdb->esc_like( $wpdb->base_prefix . $table_name )
        ) );
    }

    // Cache function result
    $cache[$table_name] = ( ! empty( $table_exist ) );

    gamipress_set_cache( 'installed_tables', $cache );

    return ! empty( $table_exist );

}

/**
 * Utility function to check if a database table has a specific field
 *
 * @since 1.4.7
 *
 * @param  string $table_name   The desired table name
 * @param  string $column_name  The desired column name
 *
 * @return bool                 Whatever if table exists and has this column or not
 */
function gamipress_database_table_has_column( $table_name, $column_name ) {

    global $wpdb;

    $cache = gamipress_get_cache( 'installed_table_columns', array(), false );

    // If result already cached, return it
    if( isset( $cache[$table_name] ) && isset( $cache[$table_name][$column_name] ) ) {
        return $cache[$table_name][$column_name];
    }

    if( ! gamipress_database_table_exists( $table_name ) ) {
        return false;
    }

    $column_exists = $wpdb->get_var( $wpdb->prepare(
        "SHOW COLUMNS FROM {$table_name} LIKE %s",
        $wpdb->esc_like( $column_name )
    ) );

    // Check if already cached any column from this table, if not, initialize it
    if( ! isset( $cache[$table_name] ) ) {
        $cache[$table_name] = array();
    }

    // Cache function result
    $cache[$table_name][$column_name] = ( ! empty( $column_exists ) );

    gamipress_set_cache( 'installed_table_columns', $cache );

    return ! empty( $column_exists );

}