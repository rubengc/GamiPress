<?php
/**
 * Scripts
 *
 * @package     GamiPress\wpForo\Scripts
 * @since       1.0.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register admin scripts
 *
 * @since       1.0.1
 * @return      void
 */
function gamipress_wpforo_admin_register_scripts() {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-wpforo-admin-css', GAMIPRESS_WPFORO_URL . 'assets/css/gamipress-wpforo-admin' . $suffix . '.css', array( ), GAMIPRESS_WPFORO_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-wpforo-admin-js', GAMIPRESS_WPFORO_URL . 'assets/js/gamipress-wpforo-admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable' ), GAMIPRESS_WPFORO_VER, true );

}
add_action( 'admin_init', 'gamipress_wpforo_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.1
 * @return      void
 */
function gamipress_wpforo_admin_enqueue_scripts( $hook ) {

    // Settings page
    if( $hook === 'gamipress_page_gamipress_settings' ) {

        //Stylesheets
        wp_enqueue_style( 'gamipress-wpforo-admin-css' );


    }

    global $post_type;

    // Requirements ui script
    if ( $post_type === 'points-type' || in_array( $post_type, gamipress_get_achievement_types_slugs() ) || in_array( $post_type, gamipress_get_rank_types_slugs() ) ) {
        wp_enqueue_script( 'gamipress-wpforo-admin-js' );
    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_wpforo_admin_enqueue_scripts', 100 );