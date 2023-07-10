<?php
/**
 * Scripts
 *
 * @package     GamiPress\Thrive_Quiz_Builder\Scripts
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
function gamipress_thrive_quiz_builder_admin_register_scripts() {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Scripts
    wp_register_script( 'gamipress-thrive-quiz-builder-admin-js', GAMIPRESS_THRIVE_QUIZ_BUILDER_URL . 'assets/js/gamipress-thrive-quiz-builder-admin' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_THRIVE_QUIZ_BUILDER_VER, true );

}
add_action( 'admin_init', 'gamipress_thrive_quiz_builder_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_thrive_quiz_builder_admin_enqueue_scripts( $hook ) {

    global $post_type;

    $allowed_post_types = array_merge( gamipress_get_achievement_types_slugs(), gamipress_get_rank_types_slugs() );

    // Requirements ui script
    if ( $post_type === 'points-type' || in_array( $post_type, $allowed_post_types ) ) {
        wp_enqueue_script( 'gamipress-thrive-quiz-builder-admin-js' );
    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_thrive_quiz_builder_admin_enqueue_scripts', 100 );