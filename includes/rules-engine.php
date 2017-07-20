<?php
/**
 * Rules Engine: The brains behind this whole operation
 *
 * @package     GamiPress\Rules_Engine
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Check if user should earn an achievement, and award it if so.
 *
 * @since  1.0.0
 * @param  integer $achievement_id The given achievement ID to possibly award
 * @param  integer $user_id        The given user's ID
 * @param  string $this_trigger    The trigger
 * @param  integer $site_id        The triggered site id
 * @param  array $args             The triggered args
 * @return mixed                   False if user has no access, void otherwise
 */
function gamipress_maybe_award_achievement_to_user( $achievement_id = 0, $user_id = 0, $this_trigger = '', $site_id = 0, $args = array() ) {

	// Set to current site id
	if ( ! $site_id ) {
		$site_id = get_current_blog_id();
    }

	// Grab current user ID if one isn't specified
	if ( ! $user_id ) {
		$user_id = wp_get_current_user()->ID;
    }

	// If the user does not have access to this achievement, bail here
	if ( ! gamipress_user_has_access_to_achievement( $user_id, $achievement_id, $this_trigger, $site_id, $args ) ) {
		return false;
    }

	// If the user has completed the achievement, award it
	if ( gamipress_check_achievement_completion_for_user( $achievement_id, $user_id, $this_trigger, $site_id, $args ) ) {
		gamipress_award_achievement_to_user( $achievement_id, $user_id, false, $this_trigger, $site_id, $args );
    }
}

/**
 * Check if user has completed an achievement
 *
 * @since  1.0.0
 * @param  integer $achievement_id The given achievement ID to verify
 * @param  integer $user_id        The given user's ID
 * @param  string  $this_trigger   The trigger
 * @param  integer $site_id        The triggered site id
 * @param  array   $args           The triggered args
 * @return bool                    True if user has completed achievement, false otherwise
 */
function gamipress_check_achievement_completion_for_user( $achievement_id = 0, $user_id = 0, $this_trigger = '', $site_id = 0, $args = array() ) {

	// Assume the user has completed the achievement
	$return = true;

	// Set to current site id
	if ( ! $site_id )
		$site_id = get_current_blog_id();

	// If the user has not already earned the achievement...
	if ( ! gamipress_get_user_achievements( array( 'user_id' => absint( $user_id ), 'achievement_id' => absint( $achievement_id ), 'since' => 1 + gamipress_achievement_last_user_activity( $achievement_id, $user_id ) ) ) ) {

		// Grab our required achievements for this achievement
		$required_achievements = gamipress_get_required_achievements_for_achievement( $achievement_id );

		// If we have requirements, loop through each and make sure they've been completed
		if ( is_array( $required_achievements ) && ! empty( $required_achievements ) ) {
			foreach ( $required_achievements as $requirement ) {
				// Has the user already earned the requirement?
				if ( ! gamipress_get_user_achievements( array( 'user_id' => $user_id, 'achievement_id' => $requirement->ID, 'since' => gamipress_achievement_last_user_activity( $achievement_id, $user_id ) ) ) ) {
					$return = false;
					break;
				}
			}
		}
	}

	// Available filter to support custom earning rules
	return apply_filters( 'user_deserves_achievement', $return, $user_id, $achievement_id, $this_trigger, $site_id, $args );

}

/**
 * Check if user meets the points requirement for a given achievement
 *
 * @since  1.0.0
 * @param  bool    $return         The current status of whether or not the user deserves this achievement
 * @param  integer $user_id        The given user's ID
 * @param  integer $achievement_id The given achievement's post ID
 * @return bool                    Our possibly updated earning status
 */
function gamipress_user_meets_points_requirement( $return = false, $user_id = 0, $achievement_id = 0 ) {

	// First, see if the achievement requires a minimum amount of points
	if ( 'points' == get_post_meta( $achievement_id, '_gamipress_earned_by', true ) ) {

		// Grab our user's points and see if they at least as many as required
		$points_required        = absint( get_post_meta( $achievement_id, '_gamipress_points_required', true ) );
		$points_type_required   = absint( get_post_meta( $achievement_id, '_gamipress_points_type_required', true ) );
        $user_points            = gamipress_get_users_points( $user_id, $points_type_required );
		$last_activity          = gamipress_achievement_last_user_activity( $achievement_id );

		if ( $user_points >= $points_required )
			$return = true;
		else
			$return = false;

		// If the user just earned the badge, though, don't let them earn it again
		// This prevents an infinite loop if the badge has no maximum earnings limit
		$minimum_time = time() - 2;
		if ( $last_activity >= $minimum_time ) {
		    $return = false;
		}
	}

	// Return our eligibility status
	return $return;
}
add_filter( 'user_deserves_achievement', 'gamipress_user_meets_points_requirement', 10, 3 );

/**
 * Award an achievement to a user
 *
 * @since  1.0.0
 * @param  integer $achievement_id The given achievement ID to award
 * @param  integer $user_id        The given user's ID
 * @param  integer $admin_id       The given admin's ID
 * @param  string $this_trigger    The trigger
 * @param  integer $site_id        The triggered site id
 * @param  array $args             The triggered args
 * @return mixed                   False on not achievement, void otherwise
 */
function gamipress_award_achievement_to_user( $achievement_id = 0, $user_id = 0, $admin_id = 0, $this_trigger = '', $site_id = 0, $args = array() ) {

	global $wp_filter, $wp_version;

	// Sanity Check: ensure we're working with an achievement post
	if ( ! gamipress_is_achievement( $achievement_id ) )
		return false;

	// Use the current user ID if none specified
	if ( $user_id == 0 )
		$user_id = wp_get_current_user()->ID;

	// Get the current site ID none specified
	if ( ! $site_id )
		$site_id = get_current_blog_id();

	// Setup our achievement object
	$achievement_object = gamipress_build_achievement_object( $achievement_id );

	// Update user's earned achievements
	gamipress_update_user_achievements( array( 'user_id' => $user_id, 'new_achievements' => array( $achievement_object ) ) );

	// Log the earning of the award
	gamipress_log_user_achievement_award( $user_id, $achievement_id, $admin_id );

	// Available hook for unlocking any achievement of this achievement type
	do_action( 'gamipress_unlock_' . $achievement_object->post_type, $user_id, $achievement_id, $this_trigger, $site_id, $args );

	// Patch for WordPress to support recursive actions, specifically for gamipress_award_achievement
	// Because global iteration is fun, assuming we can get this fixed for WordPress 3.9
	$is_recursed_filter = ( 'gamipress_award_achievement' == current_filter() );
	$current_key = null;

	// Get current position
	if ( $is_recursed_filter ) {
		$current_key = key( $wp_filter[ 'gamipress_award_achievement' ] );
	}

	// Available hook to do other things with each awarded achievement
	do_action( 'gamipress_award_achievement', $user_id, $achievement_id, $this_trigger, $site_id, $args );

	if ( $is_recursed_filter ) {
		reset( $wp_filter[ 'gamipress_award_achievement' ] );

		while ( key( $wp_filter[ 'gamipress_award_achievement' ] ) !== $current_key ) {
			next( $wp_filter[ 'gamipress_award_achievement' ] );
		}
	}

}

/**
 * Revoke an achievement from a user
 *
 * @since  1.0.0
 * @param  integer $achievement_id The given achievement's post ID
 * @param  integer $user_id        The given user's ID
 * @return void
 */
function gamipress_revoke_achievement_from_user( $achievement_id = 0, $user_id = 0 ) {

	// Use the current user's ID if none specified
	if ( ! $user_id )
		$user_id = wp_get_current_user()->ID;

	// Grab the user's earned achievements
	$earned_achievements = gamipress_get_user_achievements( array( 'user_id' => $user_id ) );

	// Loop through each achievement and drop the achievement we're revoking
	foreach ( $earned_achievements as $key => $achievement ) {
		if ( $achievement->ID == $achievement_id ) {

			// Drop the achievement from our earnings
			unset( $earned_achievements[$key] );

			// Re-key our array
			$earned_achievements = array_values( $earned_achievements );

			// Update user's earned achievements
			gamipress_update_user_achievements( array( 'user_id' => $user_id, 'all_achievements' => $earned_achievements ) );

			// Available hook for taking further action when an achievement is revoked
			do_action( 'gamipress_revoke_achievement', $user_id, $achievement_id );

			// Stop after dropping one, because we don't want to delete ALL instances
			break;
		}
	}

}

/**
 * Award additional achievements to user
 *
 * @since  1.0.0
 * @param  integer $user_id        The given user's ID
 * @param  integer $achievement_id The given achievement's post ID
 * @return void
 */
function gamipress_maybe_award_additional_achievements_to_user( $user_id = 0, $achievement_id = 0 ) {

	// Get achievements that can be earned from completing this achievement
	$dependent_achievements = gamipress_get_dependent_achievements( $achievement_id );

    // See if a user has unlocked all achievements of a given type
    gamipress_maybe_trigger_unlock_all( $user_id, $achievement_id );

	// Loop through each dependent achievement and see if it can be awarded
	foreach ( $dependent_achievements as $achievement ) {
		gamipress_maybe_award_achievement_to_user( $achievement->ID, $user_id );
	}

}
add_action( 'gamipress_award_achievement', 'gamipress_maybe_award_additional_achievements_to_user', 10, 2 );

/**
 * Check if a user has unlocked all achievements of a given type
 *
 * Triggers hook gamipress_unlock_all_{$post_type}
 *
 * @since  1.0.0
 * @param  integer $user_id        The given user's ID
 * @param  integer $achievement_id The given achievement's post ID
 * @return void
 */
function gamipress_maybe_trigger_unlock_all( $user_id = 0, $achievement_id = 0 ) {

	// Grab our user's (presumably updated) earned achievements
	$earned_achievements = gamipress_get_user_achievements( array( 'user_id' => $user_id ) );

	// Get the post type of the earned achievement
	$post_type = get_post_type( $achievement_id );

	// Hook for unlocking all achievements of this achievement type
	if ( $all_achievements_of_type = gamipress_get_achievements( array( 'post_type' => $post_type ) ) ) {

		// Assume we can award the user for unlocking all achievements of this type
		$all_per_type = true;

		// Loop through each of our achievements of this type
		foreach ( $all_achievements_of_type as $achievement ) {

			// Assume the user hasn't earned this achievement
			$found_achievement = false;

			// Loop through each eacrned achivement and see if we've earned it
			foreach ( $earned_achievements as $earned_achievement ) {
				if ( $earned_achievement->ID == $achievement->ID ) {
					$found_achievement = true;
					break;
				}
			}

			// If we haven't earned this single achievement, we haven't earned them all
			if ( ! $found_achievement ) {
				$all_per_type = false;
				break;
			}
		}

		// If we've earned all achievements of this type, trigger our hook
		if ( $all_per_type ) {
			do_action( 'gamipress_unlock_all_' . $post_type, $user_id, $achievement_id );
		}
	}
}

/**
 * Check if user may access/earn achievement.
 *
 * @since  1.0.0
 * @param  integer $user_id        The given user's ID
 * @param  integer $achievement_id The given achievement's post ID
 * @param  string $this_trigger    The trigger
 * @param  integer $site_id        The triggered site id
 * @param  array $args        The triggered args
 * @return bool                    True if user has access, false otherwise
 */
function gamipress_user_has_access_to_achievement( $user_id = 0, $achievement_id = 0, $this_trigger = '', $site_id = 0, $args = array() ) {

	// Set to current site id
	if ( ! $site_id ) {
		$site_id = get_current_blog_id();
	}

	// Assume we have access
	$return = true;

	// If the achievement is not published, we do not have access
	if ( 'publish' != get_post_status( $achievement_id ) ) {
		$return = false;
	}

	// If we've exceeded the max earnings, we do not have acces
	if ( $return && gamipress_achievement_user_exceeded_max_earnings( $user_id, $achievement_id ) ) {
		$return = false;
	}

	// If we have access, and the achievement has a parent...
	if ( $return && $parent_achievement = gamipress_get_parent_of_achievement( $achievement_id ) ) {

		// If we don't have access to the parent, we do not have access to this
		if ( ! gamipress_user_has_access_to_achievement( $user_id, $parent_achievement->ID, $this_trigger, $site_id, $args ) ) {
			$return = false;
		}

		// If the parent requires sequential steps, confirm we've earned all previous steps
		if ( $return && gamipress_is_achievement_sequential( $parent_achievement->ID ) ) {
			foreach ( gamipress_get_children_of_achievement( $parent_achievement->ID ) as $sibling ) {
				// If this is the current step, we're good to go
				if ( $sibling->ID == $achievement_id ) {
					break;
				}
				// If we haven't earned any previous step, we can't earn this one
				if ( ! gamipress_get_user_achievements( array( 'user_id' => absint( $user_id ), 'achievement_id' => absint( $sibling->ID ) ) ) ) {
					$return = false;
					break;
				}
			}
		}
	}

	// Available filter for custom overrides
	return apply_filters( 'user_has_access_to_achievement', $return, $user_id, $achievement_id, $this_trigger, $site_id, $args );

}

/**
 * Checks if an user is allowed to work on a given step
 *
 * @since  1.0.0
 * @param  bool    $return   The default return value
 * @param  integer $user_id  The given user's ID
 * @param  integer $step_id  The given step's post ID
 * @return bool              True if user has access to step, false otherwise
 */
function gamipress_user_has_access_to_step( $return = false, $user_id = 0, $step_id = 0 ) {

	// If we're not working with a step, bail here
	if ( 'step' !== get_post_type( $step_id ) )
		return $return;

	// Prevent user from earning steps with no parents
	$parent_achievement = gamipress_get_parent_of_achievement( $step_id );
	if ( ! $parent_achievement ) {
		$return = false;
	}

	// Prevent user from repeatedly earning the same step
	if ( $return && $parent_achievement && gamipress_get_user_achievements( array(
			'user_id'        => absint( $user_id ),
			'achievement_id' => absint( $step_id ),
			'since'          => absint( gamipress_achievement_last_user_activity( $parent_achievement->ID, $user_id ) )
		) )
	)
		$return = false;

	// Send back our eligibility
	return $return;
}
add_filter( 'user_has_access_to_achievement', 'gamipress_user_has_access_to_step', 10, 3 );

/**
 * Checks if an user is allowed to work on a given points award
 *
 * @since  1.0.0
 * @param  bool    $return   The default return value
 * @param  integer $user_id  The given user's ID
 * @param  integer $step_id  The given step's post ID
 * @return bool              True if user has access to step, false otherwise
 */
function gamipress_user_has_access_to_points_award( $return = false, $user_id = 0, $points_award_id = 0 ) {

    // If we're not working with a step, bail here
    if ( 'points-award' !== get_post_type( $points_award_id ) )
        return $return;

    // Prevent user from earning points awards with no points type
	$points_type = gamipress_get_points_award_points_type( $points_award_id );
    if ( ! $points_type ) {
        $return = false;
    }

    // Prevent user from repeatedly earning the same points award
    if ( $return && $points_type && gamipress_get_user_achievements( array(
            'user_id'        => absint( $user_id ),
            'achievement_id' => absint( $points_award_id ),
            'since'          => absint( gamipress_achievement_last_user_activity( $points_type->ID, $user_id ) )
        ) )
    )
        $return = false;

    // Send back our eligibility
    return $return;
}
add_filter( 'user_has_access_to_achievement', 'gamipress_user_has_access_to_points_award', 10, 3 );

/**
 * Validate whether or not a user has completed all requirements for an achievement
 *
 * @since  1.0.0
 * @param  bool $return      		True if user deserves achievement, false otherwise
 * @param  integer $user_id  		The given user's ID
 * @param  integer $achievement_id  The achievement post ID
 * @return bool              		True if user deserves step, false otherwise
 */
function gamipress_user_deserves_limit_requirements( $return = false, $user_id = 0, $achievement_id = 0 ) {

	// Only override the $return data if we're working on a step or points award
	if ( 'step' == get_post_type( $achievement_id ) || 'points-award' == get_post_type( $achievement_id ) ) {

		// Check if is limited over time
		$since = gamipress_get_achievement_limit_timestamp( $achievement_id );

		if( $since > 0 ) {
			// Activity count limit over time
			$activity_count_limit = absint( get_post_meta( $achievement_id, '_gamipress_limit', true ) );

			// Activity count limited to a timestamp
			$activity_count = absint( gamipress_get_achievement_activity_count( $user_id, $achievement_id, $since ) );

			// Force bail if user exceeds the limit over time
			if( $activity_count >= $activity_count_limit ) {
				return false;
			}
		}

		// Get the required number of checkins
		$minimum_activity_count = absint( get_post_meta( $achievement_id, '_gamipress_count', true ) );

		// Grab the relevant activity count
		$activity_count = absint( gamipress_get_achievement_activity_count( $user_id, $achievement_id ) );

		// If we meet or exceed the required number of checkins, then deserve the achievement
		if ( $activity_count >= $minimum_activity_count ) {
			$return = true;
		} else {
			$return = false;
		}
	}

	return $return;
}
add_filter( 'user_deserves_achievement', 'gamipress_user_deserves_limit_requirements', 10, 3 );

/**
 * Count a user's relevant actions for a given achievement
 *
 * @since  1.0.0
 * @param  integer $user_id 		The given user's ID
 * @param  integer $achievement_id 	The given achievement's ID
 * @param  integer $since 			Timestamp since retrieve the count
 * @return integer          		The total activity count
 */
function gamipress_get_achievement_activity_count( $user_id = 0, $achievement_id = 0, $since = 0 ) {
	// Assume the user has no relevant activities
	$activities = 0;

	$post_type = get_post_type( $achievement_id );

	if ( $post_type === 'step' || $post_type === 'points-award') {

		// Grab the requirements
		if( $post_type === 'step' ) {
			$requirements = gamipress_get_step_requirements( $achievement_id );
		} else {
			$requirements = gamipress_get_points_award_requirements( $achievement_id );
		}

		// Determine which type of trigger we're using and return the corresponding activities
		switch( $requirements['trigger_type'] ) {
			case 'specific-achievement' :
				if( $post_type === 'step' && $since === 0 ) {
					// Get our parent achievement
					$parent_achievement = gamipress_get_parent_of_achievement( $achievement_id );

					// If the user has any interaction with this achievement, only get activity since that date
					if ( $parent_achievement && $date = gamipress_achievement_last_user_activity( $parent_achievement->ID, $user_id ) ) {
						$since = $date;
					}
				}

				// Get our achievement activity
				$achievements = gamipress_get_user_achievements( array(
					'user_id'        => absint( $user_id ),
					'achievement_id' => absint( $requirements['achievement_post'] ),
					'since'          => $since
				) );

				$activities = count( $achievements );
				break;
			case 'any-achievement' :
				$trigger = 'gamipress_unlock_' . $requirements['achievement_type'];
				break;
			case 'all-achievements' :
				$trigger = 'gamipress_unlock_all_' . $requirements['achievement_type'];
				break;
			default :
				$trigger = $requirements['trigger_type'];
				break;
		}

		if( isset( $trigger ) ) {
			if( $since !== 0 ) {
				$activities = gamipress_get_user_trigger_count_from_logs( $user_id, $trigger, $since );
			} else {
				$activities = gamipress_get_user_trigger_count( $user_id, $trigger );
			}
		}

	}

	// Available filter for overriding user activity
	return absint( apply_filters( "gamipress_{$post_type}_activity", $activities, $user_id, $achievement_id ) );
}

/**
 * Get the start date where an achievement is limiting
 *
 * @param int $achievement_id	The achievement ID
 * @return int 					Timestamp from the limit starts
 */
function gamipress_get_achievement_limit_timestamp( $achievement_id = 0 ) {
	$limit_type = get_post_meta( $achievement_id, '_gamipress_limit_type', true );

	if( ! $limit_type || $limit_type === 'unlimited' ) {
		// No limit
        return 0;
	}

	$now = current_time( 'timestamp' );
	$from = current_time( 'timestamp' );

	switch( $limit_type ) {
        case 'daily':
			$from = mktime( 0, 0, 0, date( 'n', $now ), date( 'j', $now ), date( 'Y', $now ) );
            break;
        case 'weekly':
			$from = mktime( 0, 0, 0, date( "n", $now ), date( "j", $now ) - date( "N", $now ) + 1 );
            break;
        case 'monthly':
			$from =  mktime( 0, 0, 0, date( "n", $now ), 1, date( 'Y', $now ) );
            break;
        case 'yearly':
			$from =  mktime( 0, 0, 0, 1, 1, date( 'Y', $now ) );
            break;
	}

    return $from;
}
