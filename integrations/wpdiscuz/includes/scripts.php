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
function gamipress_wpdiscuz_admin_register_scripts() {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-wpdiscuz-admin-css', GAMIPRESS_WPDISCUZ_URL . 'assets/css/gamipress-wpdiscuz-admin' . $suffix . '.css', array( ), GAMIPRESS_WPDISCUZ_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-wpdiscuz-admin-js', GAMIPRESS_WPDISCUZ_URL . 'assets/js/gamipress-wpdiscuz-admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable' ), GAMIPRESS_WPDISCUZ_VER, true );

}
add_action( 'admin_init', 'gamipress_wpdiscuz_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_wpdiscuz_admin_enqueue_scripts( $hook ) {

    // Settings page
    if( $hook === 'gamipress_page_gamipress_settings' ) {

        //Stylesheets
        wp_enqueue_style( 'gamipress-wpdiscuz-admin-css' );

        //Scripts
        wp_enqueue_script( 'gamipress-wpdiscuz-admin-js' );

    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_wpdiscuz_admin_enqueue_scripts', 100 );