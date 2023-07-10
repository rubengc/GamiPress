<?php
/**
 * Triggers
 *
 * @package GamiPress\Advanced_Ads\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Advanced Ads specific triggers
 *
 * @since  1.0.0
 *
 * @param array $triggers
 *
 * @return mixed
 */
function gamipress_advanced_ads_activity_triggers( $triggers ) {

    $triggers[__( 'Advanced Ads', 'gamipress' )] = array(
        'gamipress_advanced_ads_ad_published'       => __( 'Publish an ad', 'gamipress' ),
        'gamipress_advanced_ads_ad_unpublished'     => __( 'Get an ad unpublished', 'gamipress' ),
        'gamipress_advanced_ads_ad_expired'         => __( 'Get an ad expired', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_advanced_ads_activity_triggers' );

/**
 * Get user for a given trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $user_id user ID to override.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          User ID.
 */
function gamipress_advanced_ads_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_advanced_ads_ad_published':
        case 'gamipress_advanced_ads_ad_unpublished':
        case 'gamipress_advanced_ads_ad_expired':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}

add_filter( 'gamipress_trigger_get_user_id', 'gamipress_advanced_ads_trigger_get_user_id', 10, 3);

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.0
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_advanced_ads_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_advanced_ads_ad_published':
        case 'gamipress_advanced_ads_ad_unpublished':
        case 'gamipress_advanced_ads_ad_expired':
            // Add the post ID
            $log_meta['post_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_advanced_ads_log_event_trigger_meta_data', 10, 5 );