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
}
