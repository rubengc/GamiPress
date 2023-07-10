<?php
/**
 * Scripts
 *
 * @package     GamiPress\Divi\Scripts
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
function gamipress_divi_admin_register_scripts() {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Scripts
    wp_register_script( 'gamipress-divi-admin-js', GAMIPRESS_DIVI_URL . 'assets/js/gamipress-divi-admin' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_DIVI_VER, true );

}
add_action( 'admin_init', 'gamipress_divi_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_divi_admin_enqueue_scripts( $hook ) {

    global $post_type;

    // Requirements ui script
    if ( $post_type === 'points-type' || in_array( $post_type, gamipress_get_achievement_types_slugs() ) || in_array( $post_type, gamipress_get_rank_types_slugs() ) ) {
        wp_enqueue_script( 'gamipress-divi-admin-js' );
    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_divi_admin_enqueue_scripts', 100 );