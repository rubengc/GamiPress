<?php
/**
 * Scripts
 *
 * @package     GamiPress\Link\Scripts
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
function gamipress_vimeo_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Scripts
    wp_register_script( 'gamipress-vimeo-api-js', 'https://player.vimeo.com/api/player.js', array( 'jquery' ), GAMIPRESS_VIMEO_VER, true );
    wp_register_script( 'gamipress-vimeo-js', GAMIPRESS_VIMEO_URL . 'assets/js/gamipress-vimeo' . $suffix . '.js', array( 'jquery', 'gamipress-vimeo-api-js' ), GAMIPRESS_VIMEO_VER, true );

}
add_action( 'init', 'gamipress_vimeo_register_scripts' );

/**
 * Enqueue frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_vimeo_enqueue_scripts( $hook = null ) {

    /**
     * Allowed delay in seconds to allow set the video as watched
     * This delay is added as extra watched seconds to avoid delay issues caused, for example, by the browser or javascript slowdowns
     * A delay of 1 sec will make event get triggered if users see 9 secs of a 10 secs video
     *
     * @since 1.0.3
     *
     * @param int $allowed_delay    Allowed delay in seconds, by default 1
     *
     * @return int                  Allowed delay in seconds
     */
    $allowed_delay = apply_filters( 'gamipress_vimeo_allowed_delay', 1 );

    // Scripts
    wp_localize_script( 'gamipress-vimeo-js', 'gamipress_vimeo', array(
        'ajaxurl' => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
        'user_id'       => get_current_user_id(),
        'post_id'       => get_the_ID(),
        'debug_mode'    => gamipress_is_debug_mode(),
        'allowed_delay' => $allowed_delay
    ) );

    wp_enqueue_script( 'gamipress-vimeo-api-js' );
    wp_enqueue_script( 'gamipress-vimeo-js' );

}
add_action( 'wp_enqueue_scripts', 'gamipress_vimeo_enqueue_scripts', 100 );

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_vimeo_admin_register_scripts() {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Scripts
    wp_register_script( 'gamipress-vimeo-requirements-ui-js', GAMIPRESS_VIMEO_URL . 'assets/js/gamipress-vimeo-requirements-ui' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_VIMEO_VER, true );

}
add_action( 'admin_init', 'gamipress_vimeo_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_vimeo_admin_enqueue_scripts( $hook ) {

    global $post_type;

    // Scripts

    $allowed_post_types = array_merge( gamipress_get_achievement_types_slugs(), gamipress_get_rank_types_slugs() );

    // Requirements ui script
    if ( $post_type === 'points-type' || in_array( $post_type, $allowed_post_types ) ) {
        wp_enqueue_script( 'gamipress-vimeo-requirements-ui-js' );
    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_vimeo_admin_enqueue_scripts', 100 );