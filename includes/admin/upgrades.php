<?php
/**
 * Upgrades
 *
 * @package     GamiPress\Admin\Upgrades
 * @since       1.2.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_DIR . 'includes/admin/upgrades/1.1.0.php';
require_once GAMIPRESS_DIR . 'includes/admin/upgrades/1.2.7.php';
require_once GAMIPRESS_DIR . 'includes/admin/upgrades/1.2.8.php';
require_once GAMIPRESS_DIR . 'includes/admin/upgrades/1.3.0.php';

/**
 * GamiPress upgrades
 *
 * @since 1.1.0
 */
function gamipress_process_upgrades() {

    // Get stored version
    $stored_version = get_option( 'gamipress_version', '1.0.0' );

    if( $stored_version === GAMIPRESS_VER ) {
        return;
    }

    /**
     * Before process upgrades action
     */
    do_action( 'gamipress_before_process_upgrades', $stored_version );

    /**
     * Version upgrade filter
     */
    $stored_version = apply_filters( 'gamipress_process_upgrades', $stored_version );

    /**
     * After process upgrades action
     */
    do_action( 'gamipress_after_process_upgrades', $stored_version );

    // Updated stored version
    update_option( 'gamipress_version', $stored_version );

}
add_action( 'admin_init', 'gamipress_process_upgrades' );

/**
 * Helper function to check if GamiPress has been upgraded successfully
 *
 * @since 1.2.8
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

    if ( false === $completed_upgrades ) {
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
