<?php
/**
 * Scripts
 *
 * @package     GamiPress\bbPress\Scripts
 * @since       1.0.4
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_bbp_admin_register_scripts() {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-bbp-admin-css', GAMIPRESS_BBP_URL . 'assets/css/gamipress-bbpress-admin' . $suffix . '.css', array( ), GAMIPRESS_BBP_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-bbp-admin-js', GAMIPRESS_BBP_URL . 'assets/js/gamipress-bbpress-admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable' ), GAMIPRESS_BBP_VER, true );

}
add_action( 'admin_init', 'gamipress_bbp_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_bbp_admin_enqueue_scripts( $hook ) {

    // Settings page
    if( $hook === 'gamipress_page_gamipress_settings' ) {

        //Stylesheets
        wp_enqueue_style( 'gamipress-bbp-admin-css' );

        //Scripts
        wp_enqueue_script( 'gamipress-bbp-admin-js' );

    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_bbp_admin_enqueue_scripts', 100 );