<?php
/**
 * Listeners
 *
 * @package GamiPress\AnsPress\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function gamipress_jetpack_site_subscription( $result = '' ) {

    // Check result
    if( $result !== 'success' ) return;

    // Check current logged in user
    $user_id = get_current_user_id();

    if( $user_id === 0 ) return;

    // Award user for subscribe to the site
    do_action( 'gamipress_jetpack_site_subscription', get_current_blog_id(), $user_id );

}
add_action( 'jetpack_subscriptions_form_submission', 'gamipress_jetpack_site_subscription' );

function gamipress_jetpack_comment_subscription( $result, $post_ids ) {

    // Check result
    if( is_wp_error( $result ) ) return;

    // Check current logged in user
    $user_id = get_current_user_id();

    if( $user_id === 0 ) return;

    // Award each post
    foreach ( $post_ids as $post_id ) {

        // Award user for comment subscription
        do_action( 'gamipress_jetpack_comment_subscription', $post_id, $user_id );

    }

}
add_action( 'jetpack_subscriptions_comment_form_submission', 'gamipress_jetpack_comment_subscription', 10, 2 );