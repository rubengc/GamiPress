<?php
/**
 * GamiPress 1.8.7 compatibility functions
 *
 * @package     GamiPress\1.8.7
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.8.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Change all active meta from one achievement type to another.
 *
 * @deprecated Since active achievements meta is not used anymore
 *
 * @since 1.0.0
 *
 * @param string $original_type Original achievement type.
 * @param string $new_type      New achievement type.
 */
function gamipress_update_active_meta_achievement_types( $original_type = '', $new_type = '' ) {

    $metas = gamipress_get_unserialized_achievement_metas( '_gamipress_active_achievements', $original_type );

    if ( ! empty( $metas ) ) {
        foreach ( $metas as $meta ) {
            $meta->meta_value = gamipress_update_meta_achievement_types( $meta->meta_value, $original_type, $new_type );

            gamipress_update_user_meta( $meta->user_id, $meta->meta_key, $meta->meta_value );
        }
    }

}

/**
 * Get a user's active achievements
 *
 * @deprecated Unused anymore
 *
 * @since  1.0.0
 *
 * @param  integer $user_id User ID
 * @return array            An array of the user's active achievements
 */
function gamipress_user_get_active_achievements( $user_id ) {

    // Get the user's active achievements
    $active_achievements = gamipress_get_cache( 'active_achievements', array(), false );

    // If there are no achievements
    if( ! isset( $active_achievements[$user_id] ) ) {
        return array();
    }

    // Otherwise, we DO have achievements and should return them cast as an array
    return $active_achievements[$user_id];
}

/**
 * Update a user's active achievements
 *
 * @deprecated Unused anymore
 *
 * @since  1.0.0
 *
 * @param  integer $user_id      User ID
 * @param  array   $achievements An array of achievements to pass to meta
 * @param  boolean $update       True to update to exsiting active achievements, false to replace entire array (Default: false)
 * @return array                 The updated achievements array
 */
function gamipress_user_update_active_achievements( $user_id = 0, $achievements = array(), $update = false ) {

    // Get the user's active achievements
    $active_achievements = gamipress_get_cache( 'active_achievements', array(), false );

    // If there are no achievements
    if( ! isset( $active_achievements[$user_id] ) ) {
        $active_achievements[$user_id] = array();
    }

    // If we're not replacing, append the passed array to our existing array
    if ( true == $update ) {
        $existing_achievements = gamipress_user_get_active_achievements( $user_id );
        $active_achievements[$user_id] = (array) $achievements + (array) $existing_achievements;
    } else {
        $active_achievements[$user_id] = (array) $achievements;
    }

    // Update the user's active achievements
    gamipress_set_cache( 'active_achievements', $active_achievements );

    // Return our updated achievements array
    return $achievements;
}

/**
 * Get a user's active achievement details
 *
 * @deprecated Unused anymore
 *
 * @since  1.0.0
 *
 * @param  integer $user_id        User ID
 * @param  integer $achievement_id Achievement post ID
 * @return mixed                   An achievement object if it exists, false if not
 */
function gamipress_user_get_active_achievement( $user_id = 0, $achievement_id = 0 ) {

    // Get the user's active achievements
    $achievements = gamipress_user_get_active_achievements( $user_id );

    // Return the achievement if it exists, or false if not
    return ( isset( $achievements[$achievement_id] ) ) ? $achievements[$achievement_id] : false;
}

/**
 * Add an achievement to a user's active achievements
 *
 * @deprecated Unused anymore
 *
 * @since  1.0.0
 *
 * @param  integer $user_id        User ID
 * @param  integer $achievement_id Achievement post ID
 * @return object                  The active Achievement object
 */
function gamipress_user_add_active_achievement( $user_id = 0, $achievement_id = 0 ) {

    // If achievement is a step, bail here
    if ( 'step' == gamipress_get_post_type( $achievement_id ) ) {
        return false;
    }

    // Get the user's active achievements
    $achievements = gamipress_user_get_active_achievements( $user_id );

    // If it doesn't exist, add the achievement to the array
    if ( ! isset( $achievements[$achievement_id] ) ) {
        $achievements[$achievement_id] = gamipress_build_achievement_object( $achievement_id, 'started' );
        gamipress_user_update_active_achievements( $user_id, $achievements );
    }

    // Send back the added achievement object
    return $achievements[$achievement_id];
}

/**
 * Update the stored data for an active achievement
 *
 * @deprecated Unused anymore
 *
 * @since  1.0.0
 *
 * @param  integer $user_id        User ID
 * @param  integer $achievement_id Achievement post ID
 * @param  object  $achievement    Achievement object to insert into user meta
 *
 * @return object                  The final updated achievement object
 */
function gamipress_user_update_active_achievement( $user_id = 0, $achievement_id = 0, $achievement = null ) {

    // If achievement is a step, bail here
    if ( 'step' === gamipress_get_post_type( $achievement_id ) ) {
        return false;
    }

    // If we weren't passed an object, get the latest version from meta
    if ( ! is_object( $achievement ) )
        $achievement = gamipress_user_get_active_achievement( $user_id, $achievement_id );

    // If we still don't have an object, build one
    if ( ! is_object( $achievement ) )
        $achievement = gamipress_build_achievement_object( $achievement_id, 'started' );

    // Update our last activity date
    $achievement->last_activity_date = current_time( 'timestamp' );

    // Available filter for manipulating the achievement object
    $achievement = apply_filters( 'gamipress_user_update_active_achievement', $achievement, $user_id, $achievement_id );

    // Update the user's active achievements
    gamipress_user_update_active_achievements( $user_id, array( $achievement_id => $achievement ), true );

    // Return the updated achievement object
    return $achievement;
}

/**
 * Remove an achievement from a user's list of active achievements
 *
 * @deprecated Unused anymore
 *
 * @since  1.0.0
 *
 * @param  integer $user_id        User ID
 * @param  integer $achievement_id Achievement post ID
 * @return array                   The user's active achievements
 */
function gamipress_user_delete_active_achievement( $user_id = 0, $achievement_id = 0 ) {

    // Get the user's active achievements
    $achievements = gamipress_user_get_active_achievements( $user_id );

    // If the achievement exists, unset it
    if ( isset( $achievements[$achievement_id] ) )
        unset( $achievements[$achievement_id] );

    // Update the user's active achievements
    return gamipress_user_update_active_achievements( $user_id, $achievements );
}

/**
 * Update the user's active achievement meta with each earned achievement
 *
 * @deprecated Unused anymore
 *
 * @since  1.0.0
 *
 * @param  integer $user_id         The given user's ID
 * @param  integer $achievement_id  The given achievement's post ID
 * @return object                   The final achievement object
 */
function gamipress_user_update_active_achievement_on_earnings( $user_id, $achievement_id ) {

    // If achievement is a step, update its parent activity
    if ( 'step' === gamipress_get_post_type( $achievement_id ) ) {

        $parent_achievement = gamipress_get_step_achievement( $achievement_id );

        if ( $parent_achievement ) {
            gamipress_user_update_active_achievement( $user_id, $parent_achievement->ID );
        }

        // Otherwise, drop the earned achievement form the user's active achievement array
    } else {
        gamipress_user_delete_active_achievement( $user_id, $achievement_id );
    }

}
//add_action( 'gamipress_award_achievement', 'gamipress_user_update_active_achievement_on_earnings', 10, 2 );