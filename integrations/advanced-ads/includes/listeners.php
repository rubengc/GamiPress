<?php
/**
 * Listeners
 *
 * @package GamiPress\Advanced_Ads\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ad published listener
 *
 * @since 1.0.0
 *
 * @param Advanced_Ads_Ad $ad
 */
function gamipress_advanced_ads_ad_published( $ad ) {

    $post = get_post( $ad->id );

    // Bail if post does not exists
    if( ! $post ) {
        return;
    }

    $user_id = absint( $post->post_author );

    // Bail if post does not has an author assigned
    if( absint( $user_id === 0 ) ) {
        return;
    }

    do_action( 'gamipress_advanced_ads_ad_published', $ad->id, $user_id );

}
add_action( 'advanced-ads-ad-status-published', 'gamipress_advanced_ads_ad_published', 10 );

/**
 * Ad unpublished listener
 *
 * @since 1.0.0
 *
 * @param Advanced_Ads_Ad $ad
 */
function gamipress_advanced_ads_ad_unpublished( $ad ) {

    $post = get_post( $ad->id );

    // Bail if post does not exists
    if( ! $post ) {
        return;
    }

    $user_id = absint( $post->post_author );

    // Bail if post does not has an author assigned
    if( absint( $user_id === 0 ) ) {
        return;
    }

    do_action( 'gamipress_advanced_ads_ad_unpublished', $ad->id, $user_id );

}
add_action( 'advanced-ads-ad-status-unpublished', 'gamipress_advanced_ads_ad_unpublished', 10 );

/**
 * Ad expired listener
 *
 * @since 1.0.0
 *
 * @param int $ad_id
 * @param Advanced_Ads_Ad $ad
 */
function gamipress_advanced_ads_ad_expired( $ad_id, $ad ) {

    $post = get_post( $ad->id );

    // Bail if post does not exists
    if( ! $post ) {
        return;
    }

    $user_id = absint( $post->post_author );

    // Bail if post does not has an author assigned
    if( absint( $user_id === 0 ) ) {
        return;
    }

    do_action( 'gamipress_advanced_ads_ad_expired', $ad->id, $user_id );

}
add_action( 'advanced-ads-ad-expired', 'gamipress_advanced_ads_ad_expired', 10, 2 );