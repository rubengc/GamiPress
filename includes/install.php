<?php
/**
 * Install
 *
 * @package     GamiPress\Install
 * @since       1.1.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function gamipress_install() {

    // Setup default GamiPress options
    $gamipress_settings = ( $exists = get_option( 'gamipress_settings' ) ) ? $exists : array();

    if ( empty( $gamipress_settings ) ) {

        $gamipress_settings['minimum_role'] = 'manage_options';
        $gamipress_settings['achievement_image_size'] = array( 'width' => 100, 'height' => 100 );

        update_option( 'gamipress_settings', $gamipress_settings );
    }

    // Register GamiPress post types and flush rewrite rules
    gamipress_flush_rewrite_rules();

    gamipress_process_upgrades();

}

function gamipress_process_upgrades() {

    // Get stored version
    $stored_version = get_option( 'gamipress_version', '1.0.0' );

    do_action( 'gamipress_before_process_upgrades', $stored_version );

    if ( version_compare( $stored_version, '1.1.0', '<' ) ) {
        gamipress_110_upgrades();
    }

    do_action( 'gamipress_after_process_upgrades', $stored_version );

    // Updated stored version
    update_option( 'gamipress_version', GAMIPRESS_VER );

}

function gamipress_110_upgrades() {
    global $wpdb;

    // Update wp_login trigger to gamipress_login
    $wpdb->update(
        $wpdb->postmeta,
        array(
            'meta_value' => 'gamipress_login'
        ),
        array(
            'meta_key' => '_gamipress_trigger_type',
            'meta_value' => 'wp_login',
        )
    );

}
