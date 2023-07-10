<?php
/**
 * Scripts
 *
 * @package     GamiPress\Caldera_Forms\Scripts
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
function gamipress_cf_admin_register_scripts() {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Scripts
    wp_register_script( 'gamipress-caldera-forms-admin-js', GAMIPRESS_CF_URL . 'assets/js/gamipress-caldera-forms-admin' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_CF_VER, true );

}
add_action( 'admin_init', 'gamipress_cf_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_cf_admin_enqueue_scripts( $hook ) {

    global $post_type;

    // Requirements ui script
    if ( $post_type === 'points-type' || in_array( $post_type, gamipress_get_achievement_types_slugs() ) || in_array( $post_type, gamipress_get_rank_types_slugs() ) ) {
        wp_enqueue_script( 'gamipress-caldera-forms-admin-js' );
    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_cf_admin_enqueue_scripts', 100 );