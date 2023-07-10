<?php
/**
 * Scripts
 *
 * @package     GamiPress\Ultimate_Member\Scripts
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_ultimate_member_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-ultimate-member-css', GAMIPRESS_ULTIMATE_MEMBER_URL . 'assets/css/gamipress-ultimate-member' . $suffix . '.css', array( ), GAMIPRESS_ULTIMATE_MEMBER_VER, 'all' );

}
add_action( 'init', 'gamipress_ultimate_member_register_scripts' );

/**
 * Enqueue frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_ultimate_member_enqueue_scripts( $hook = null ) {

    // Stylesheets
    wp_enqueue_style( 'gamipress-ultimate-member-css' );

}
add_action( 'wp_enqueue_scripts', 'gamipress_ultimate_member_enqueue_scripts' );

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_ultimate_member_admin_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-ultimate-member-admin-css', GAMIPRESS_ULTIMATE_MEMBER_URL . 'assets/css/gamipress-ultimate-member-admin' . $suffix . '.css', array( ), GAMIPRESS_ULTIMATE_MEMBER_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-ultimate-member-admin-js', GAMIPRESS_ULTIMATE_MEMBER_URL . 'assets/js/gamipress-ultimate-member-admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable' ), GAMIPRESS_ULTIMATE_MEMBER_VER, true );

}
add_action( 'admin_init', 'gamipress_ultimate_member_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_ultimate_member_admin_enqueue_scripts( $hook ) {

    // Settings page
    if( $hook === 'gamipress_page_gamipress_settings' ) {

        //Stylesheets
        wp_enqueue_style( 'gamipress-ultimate-member-admin-css' );

        //Scripts
        wp_enqueue_script( 'gamipress-ultimate-member-admin-js' );

    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_ultimate_member_admin_enqueue_scripts', 100 );