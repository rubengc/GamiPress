<?php
/**
 * Scripts
 *
 * @package     GamiPress\BuddyPress\Scripts
 * @since       1.0.5
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_bp_admin_register_scripts() {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'gamipress-bp-admin-css', GAMIPRESS_BP_URL . 'assets/css/gamipress-buddypress-admin' . $suffix . '.css', array( ), GAMIPRESS_BP_VER, 'all' );

    // Scripts
    wp_register_script( 'gamipress-bp-admin-js', GAMIPRESS_BP_URL . 'assets/js/gamipress-buddypress-admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable' ), GAMIPRESS_BP_VER, true );
    wp_register_script( 'gamipress-bp-requirements-ui-js', GAMIPRESS_BP_URL . 'assets/js/gamipress-buddypress-requirements-ui' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_BP_VER, true );

}
add_action( 'admin_init', 'gamipress_bp_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_bp_admin_enqueue_scripts( $hook ) {

    global $post_type;

    $allowed_post_types = array_merge( gamipress_get_achievement_types_slugs(), gamipress_get_rank_types_slugs() );

    // Requirements ui script
    if ( $post_type === 'points-type' || in_array( $post_type, $allowed_post_types ) ) {
        wp_enqueue_script( 'gamipress-bp-requirements-ui-js' );
    }

    // Settings page
    if( $hook === 'gamipress_page_gamipress_settings' ) {

        //Stylesheets
        wp_enqueue_style( 'gamipress-bp-admin-css' );

        //Scripts
        wp_enqueue_script( 'gamipress-bp-admin-js' );

    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_bp_admin_enqueue_scripts', 100 );