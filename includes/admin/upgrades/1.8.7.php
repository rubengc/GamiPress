<?php
/**
 * 1.8.7 Upgrades
 *
 * @package     GamiPress\Admin\Upgrades\1.8.7
 * @since       1.8.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return 1.8.7 as last required upgrade
 *
 * @return string
 */
function gamipress_187_is_last_required_upgrade() {

    return '1.8.7';

}
add_filter( 'gamipress_get_last_required_upgrade', 'gamipress_187_is_last_required_upgrade', 187 );

/**
 * Process 1.8.7 upgrades
 *
 * @param string $stored_version
 *
 * @return string
 */
function gamipress_187_upgrades( $stored_version ) {

    // Already upgrade
    if ( version_compare( $stored_version, '1.8.7', '>=' ) ) {
        return $stored_version;
    }

    // Ensure that GamiPress tables have been created
    if( gamipress_database_table_exists( GamiPress()->db->logs ) ) {
        // Process 1.8.7 upgrade
        gamipress_process_187_upgrade();

        // There is nothing to update, so upgrade
        $stored_version = '1.8.7';
    }

    return $stored_version;

}
add_filter( 'gamipress_process_upgrades', 'gamipress_187_upgrades', 187 );

/**
 * Process 1.8.7 upgrades
 */
function gamipress_process_187_upgrade() {

    global $wpdb;

    ignore_user_abort( true );
    set_time_limit( 0 );

    // Bail if GamiPress tables haven't been created yet
    if( ! gamipress_database_table_exists( GamiPress()->db->logs ) ) {
        return;
    }

    // Setup tables to update
    $tables = array(
        GamiPress()->db->logs,
        GamiPress()->db->logs_meta,
        GamiPress()->db->user_earnings,
        GamiPress()->db->user_earnings_meta,
    );

    foreach( $tables as $table ) {
        // Alter table to use InnoDB
        $wpdb->query( "ALTER TABLE {$table} ENGINE = InnoDB;" );
    }

    // Updated stored version
    update_option( 'gamipress_version', '1.8.7' );

}