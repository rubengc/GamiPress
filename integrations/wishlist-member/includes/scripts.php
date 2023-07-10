<?php
/**
 * Scripts
 *
 * @package     GamiPress\WishList_Member\Scripts
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
function gamipress_wishlist_member_admin_register_scripts() {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Scripts
    wp_register_script( 'gamipress-wishlist-member-admin-js', GAMIPRESS_WISHLIST_MEMBER_URL . 'assets/js/gamipress-wishlist-member-admin' . $suffix . '.js', array( 'jquery' ), GAMIPRESS_WISHLIST_MEMBER_VER, true );

}
add_action( 'admin_init', 'gamipress_wishlist_member_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function gamipress_wishlist_member_admin_enqueue_scripts( $hook ) {

    global $post_type;

    $allowed_post_types = array_merge( gamipress_get_achievement_types_slugs(), gamipress_get_rank_types_slugs() );

    // Requirements ui script
    if ( $post_type === 'points-type' || in_array( $post_type, $allowed_post_types ) ) {
        wp_enqueue_script( 'gamipress-wishlist-member-admin-js' );
    }

}
add_action( 'admin_enqueue_scripts', 'gamipress_wishlist_member_admin_enqueue_scripts', 100 );