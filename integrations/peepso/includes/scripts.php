<?php
/**
 * Scripts
 *
 * @package     GamiPress\PeepSo\Scripts
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_peepso_admin_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-peepso-admin-css', GAMIPRESS_PEEPSO_URL . 'assets/css/gamipress-peepso-admin' . $suffix . '.css', array( ), GAMIPRESS_PEEPSO_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-peepso-admin-js', GAMIPRESS_PEEPSO_URL . 'assets/js/gamipress-peepso-admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable' ), GAMIPRESS_PEEPSO_VER, true );

}
add_action( 'admin_init', 'gamipress_peepso_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_peepso_admin_enqueue_scripts( $hook ) {

    // Settings page
    if( $hook === 'gamipress_page_gamipress_settings' ) {

        //Stylesheets
        wp_enqueue_style( 'gamipress-peepso-admin-css' );

        //Scripts
        wp_enqueue_script( 'gamipress-peepso-admin-js' );

    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_peepso_admin_enqueue_scripts', 100 );