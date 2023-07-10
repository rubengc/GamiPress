<?php
/**
 * Triggers
 *
 * @package GamiPress\Favorites\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Favorites specific triggers
 *
 * @since  1.0.0
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_favorites_activity_triggers( $triggers ) {

    $triggers[__( 'Favorites', 'gamipress' )] = array(
        // Favorite
        'gamipress_favorites_favorite' => __( 'Favorite a post', 'gamipress' ),
        'gamipress_favorites_specific_favorite' => __( 'Favorite a specific post', 'gamipress' ),
        'gamipress_favorites_user_favorite' => __( 'Get a favorite on a post', 'gamipress' ),
        'gamipress_favorites_user_specific_favorite' => __( 'Get a favorite on a specific post', 'gamipress' ),
        // Unfavorite
        'gamipress_favorites_unfavorite' => __( 'Unfavorite a post', 'gamipress' ),
        'gamipress_favorites_specific_unfavorite' => __( 'Unfavorite a specific post', 'gamipress' ),
        'gamipress_favorites_user_unfavorite' => __( 'Get a unfavorite on a post', 'gamipress' ),
        'gamipress_favorites_user_specific_unfavorite' => __( 'Get a unfavorite on a specific post', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_favorites_activity_triggers' );

/**
 * Register Favorites specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_favorites_specific_activity_triggers( $specific_activity_triggers ) {

    $post_types = array();

    // Get favorites post type display options
    $types = get_option('simplefavorites_display');

    if ( ! empty( $types['posttypes'] ) && $types !== "" ) {

        foreach ( $types['posttypes'] as $key => $type ) {

            // If favorites display is active, then add the post type
            if ( isset( $type['display'] ) && $type['display'] == 'true' ) {
                $post_types[] = $key;
            }

        }

    }

    $specific_activity_triggers['gamipress_favorites_specific_favorite'] = $post_types;
    $specific_activity_triggers['gamipress_favorites_user_specific_favorite'] = $post_types;
    $specific_activity_triggers['gamipress_favorites_specific_unfavorite'] = $post_types;
    $specific_activity_triggers['gamipress_favorites_user_specific_unfavorite'] = $post_types;

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_favorites_specific_activity_triggers' );

/**
 * Register Favorites specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_favorites_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_favorites_specific_favorite'] = __( 'Favorite %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_favorites_user_specific_favorite'] = __( 'Get %s favorited', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_favorites_specific_unfavorite'] = __( 'Unfavorite %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_favorites_user_specific_unfavorite'] = __( 'Get %s unfavorited', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_favorites_specific_activity_trigger_label' );

/**
 * Get user for a given trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $user_id user ID to override.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 * @return integer          User ID.
 */
function gamipress_favorites_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Favorite
        case 'gamipress_favorites_favorite':
        case 'gamipress_favorites_specific_favorite':
        case 'gamipress_favorites_user_favorite':
        case 'gamipress_favorites_user_specific_favorite':
        // Unfavorite
        case 'gamipress_favorites_unfavorite':
        case 'gamipress_favorites_specific_unfavorite':
        case 'gamipress_favorites_user_unfavorite':
        case 'gamipress_favorites_user_specific_unfavorite':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_favorites_trigger_get_user_id', 10, 3);


/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer  $specific_id Specific ID.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          Specific ID.
 */
function gamipress_favorites_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_favorites_specific_favorite':
        case 'gamipress_favorites_user_specific_favorite':
        // Unfavorite
        case 'gamipress_favorites_specific_unfavorite':
        case 'gamipress_favorites_user_specific_unfavorite':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_favorites_specific_trigger_get_id', 10, 3 );

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
function gamipress_favorites_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Favorite
        case 'gamipress_favorites_favorite':
        case 'gamipress_favorites_specific_favorite':
        case 'gamipress_favorites_user_favorite':
        case 'gamipress_favorites_user_specific_favorite':
        // Unfavorite
        case 'gamipress_favorites_unfavorite':
        case 'gamipress_favorites_specific_unfavorite':
        case 'gamipress_favorites_user_unfavorite':
        case 'gamipress_favorites_user_specific_unfavorite':
            // Add the favorited post ID
            $log_meta['post_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_favorites_log_event_trigger_meta_data', 10, 5 );