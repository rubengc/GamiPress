<?php
/**
 * Listeners
 *
 * @package GamiPress\WP_PostRatings\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * New rate listener
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param int $post_id
 * @param int $rating_value
 */
function gamipress_wp_postratings_rate( $user_id, $post_id, $rating_value ) {

    // Login is required
    if ( ! is_user_logged_in() ) return;

    // Get the post author
    $post_author = absint( get_post_field( 'post_author', $post_id ) );

    // Rate a post
    do_action( 'gamipress_wp_postratings_rate', $post_id, $user_id, $rating_value );

    // Rate a specific post
    do_action( 'gamipress_wp_postratings_specific_rate', $post_id, $user_id, $rating_value );

    // Get a rate on a post
    do_action( 'gamipress_wp_postratings_user_rate', $post_id, $post_author, $user_id, $rating_value );

    // Get a rate on a specific post
    do_action( 'gamipress_wp_postratings_user_specific_rate', $post_id, $post_author, $user_id, $rating_value );

    // --------------------------------------------------------------
    // Specific rate
    // --------------------------------------------------------------

    // Rate a post with a specific rating
    do_action( 'gamipress_wp_postratings_rate_specific', $post_id, $user_id, $rating_value );

    // Rate a specific post with a specific rating
    do_action( 'gamipress_wp_postratings_specific_rate_specific', $post_id, $user_id, $rating_value );

    // Get a specific rate on a post
    do_action( 'gamipress_wp_postratings_user_rate_specific', $post_id, $post_author, $user_id, $rating_value );

    // Get a specific rate on a specific post
    do_action( 'gamipress_wp_postratings_user_specific_rate_specific', $post_id, $post_author, $user_id, $rating_value );

    // --------------------------------------------------------------
    // Minimum rate
    // --------------------------------------------------------------

    // Rate a post with a minimum rating
    do_action( 'gamipress_wp_postratings_minimum_rate', $post_id, $user_id, $rating_value );

    // Rate a specific post with a minimum rating
    do_action( 'gamipress_wp_postratings_specific_minimum_rate', $post_id, $user_id, $rating_value );

    // Get a minimum rate on a post
    do_action( 'gamipress_wp_postratings_user_minimum_rate', $post_id, $post_author, $user_id, $rating_value );

    // Get a minimum rate on a specific post
    do_action( 'gamipress_wp_postratings_user_specific_minimum_rate', $post_id, $post_author, $user_id, $rating_value );

}
add_action( 'rate_post', 'gamipress_wp_postratings_rate', 10, 3 );

