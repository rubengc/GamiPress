<?php
/**
 * Achievement Activity Functions
 *
 * @package     GamiPress\Activity_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the UNIX timestamp for the last activity on an achievement for a given user
 *
 * @since   1.0.0
 * @updated 1.6.1 Added special support to rank requirements getting last activity from previous earned rank
 *
 * @param  integer $achievement_id  The given achievements post ID
 * @param  integer $user_id  		The given user's ID
 * @return integer           		The UNIX timestamp for the last reported achievement activity
 */
function gamipress_achievement_last_user_activity( $achievement_id = 0, $user_id = 0 ) {

	// Assume the user has no history with this achievement
	$since = 0;

	// Attempt to grab the last activity date from active achievement meta
	if ( $achievement = gamipress_user_get_active_achievement( $user_id, $achievement_id ) ) {

		$since = $achievement->date_started - 1;

	// Attempt to grab the achievement earned date
	} else if ( $achievements = gamipress_get_user_achievements( array( 'user_id' => $user_id, 'achievement_id' => $achievement_id, 'limit' => 1 ) ) ) {

		$achievement = $achievements[0];

		// Return the achievement date earned
		if ( is_object( $achievement ) )
			$since = $achievement->date_earned + 1;

    // If hasn't earned it and is a rank requirement, then grab the rank earned date
	} else if( gamipress_get_post_type( $achievement_id ) === 'rank-requirement' ) {

	    $rank = gamipress_get_rank_requirement_rank( $achievement_id );

	    if( $rank ) {

	        // Just get rank earned time if rank is not the lowest priority one (aka default rank)
	        if( ! gamipress_is_lowest_priority_rank( gamipress_get_user_rank_id( $user_id, $rank->post_type ) ) ) {

	            // Set since from previous earned time rank
                $since = gamipress_get_rank_earned_time( $user_id, $rank->post_type ) - 1;
            }
        }


    }

	// Finally, return our time
	return $since;

}

/**
 * Get a user's active achievements
 *
 * @since  1.0.0
 *
 * @param  integer $user_id User ID
 * @return array            An array of the user's active achievements
 */
function gamipress_user_get_active_achievements( $user_id ) {

	// Get the user's active achievements from meta
	$achievements = gamipress_get_user_meta( $user_id, '_gamipress_active_achievements' );

	// If there are no achievements
	if ( empty( $achievements ) )
		return array();

	// Otherwise, we DO have achievements and should return them cast as an array
	return (array) $achievements;
}

/**
 * Update a user's active achievements
 *
 * @since  1.0.0
 *
 * @param  integer $user_id      User ID
 * @param  array   $achievements An array of achievements to pass to meta
 * @param  boolean $update       True to update to exsiting active achievements, false to replace entire array (Default: false)
 * @return array                 The updated achievements array
 */
function gamipress_user_update_active_achievements( $user_id = 0, $achievements = array(), $update = false ) {

	// If we're not replacing, append the passed array to our existing array
	if ( true == $update ) {
		$existing_achievements = gamipress_user_get_active_achievements( $user_id );
		$achievements = (array) $achievements + (array) $existing_achievements;
	}

	// Update the user's active achievements meta
	gamipress_update_user_meta( $user_id, '_gamipress_active_achievements', $achievements );

	// Return our updated achievements array
	return (array) $achievements;
}

/**
 * Get a user's active achievement details
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
 * @since  1.0.0
 *
 * @param  integer $user_id        User ID
 * @param  integer $achievement_id Achievement post ID
 * @return object                  The active Achievement object
 */
function gamipress_user_add_active_achievement( $user_id = 0, $achievement_id = 0 ) {

	// If achievement is a step, bail here
	if ( 'step' == gamipress_get_post_type( $achievement_id ) )
		return false;

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
	if ( 'step' === gamipress_get_post_type( $achievement_id ) )
		return false;

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
add_action( 'gamipress_award_achievement', 'gamipress_user_update_active_achievement_on_earnings', 10, 2 );
