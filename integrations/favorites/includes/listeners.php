<?php
/**
 * Listeners
 *
 * @package GamiPress\Favorites\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// New favorite
function gamipress_favorites_favorite( $post_id, $status, $site_id, $user_id ) {

    // Login is required
    if ( ! is_user_logged_in() )  {
        return;
    }

    // Decide the action based on the status
    $action = ( $status === 'active' ? 'favorite' : 'unfavorite' );

    // Favorite a post
    do_action( "gamipress_favorites_{$action}", $post_id, $user_id );

    // Favorite a specific post
    do_action( "gamipress_favorites_specific_{$action}", $post_id, $user_id );

    // Author triggers
    $post_author = absint( get_post_field( 'post_author', $post_id ) );

    // Get a favorite on a post
    do_action( "gamipress_favorites_user_{$action}", $post_id, $post_author, $user_id );

    // Get a favorite on a specific post
    do_action( "gamipress_favorites_user_specific_{$action}", $post_id, $post_author, $user_id );

}
add_action( 'favorites_after_favorite', 'gamipress_favorites_favorite', 10, 4 );

