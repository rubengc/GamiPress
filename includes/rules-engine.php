<?php
/**
 * Rules Engine: The brains behind this whole operation
 *
 * @package     GamiPress\Rules_Engine
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Check if user should earn an achievement, and award it if so.
 *
 * @since  1.0.0
 *
 * @param  integer  $achievement_id     The given achievement ID to possibly award
 * @param  integer  $user_id            The given user's ID
 * @param  string   $trigger            The trigger
 * @param  integer  $site_id            The triggered site id
 * @param  array    $args               The triggered args
 */
function gamipress_maybe_award_achievement_to_user( $achievement_id = 0, $user_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

	// Set to current site id
	if ( ! $site_id ) {
		$site_id = get_current_blog_id();
    }

	// Grab current user ID if one isn't specified
	if ( ! $user_id ) {
		$user_id = wp_get_current_user()->ID;
    }

	// If the user does not have access to this achievement, bail here
	if ( ! gamipress_user_has_access_to_achievement( $user_id, $achievement_id, $trigger, $site_id, $args ) ) {
		return;
    }

	// If the user has completed the achievement, award it
	if ( gamipress_check_achievement_completion_for_user( $achievement_id, $user_id, $trigger, $site_id, $args ) ) {
		gamipress_award_achievement_to_user( $achievement_id, $user_id, false, $trigger, $site_id, $args );
    }

}

/* --------------------------------------------------
 * Access Checks
   -------------------------------------------------- */

/**
 * Check if user may access/earn achievement.
 *
 * @since  1.0.0
 *
 * @param  integer  $achievement_id     The given achievement ID to possibly award
 * @param  integer  $user_id            The given user's ID
 * @param  string   $trigger            The trigger
 * @param  integer  $site_id            The triggered site id
 * @param  array    $args               The triggered args
 *
 * @return bool                    	    True if user has access, false otherwise
 */
function gamipress_user_has_access_to_achievement( $user_id = 0, $achievement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

	// Set to current site id
	if ( ! $site_id ) {
		$site_id = get_current_blog_id();
	}

	// Assume we have access
	$return = true;

	// If the achievement is not published, we do not have access
	if ( gamipress_get_post_status( $achievement_id ) !== 'publish' ) {
		$return = false;
	}

	// If we've exceeded the max earnings, we do not have access
	if ( $return && gamipress_achievement_user_exceeded_max_earnings( $user_id, $achievement_id ) ) {
		$return = false;
	}

	// If we have access, and the achievement has a parent...
	if ( $return && $parent_achievement = gamipress_get_parent_of_achievement( $achievement_id ) ) {

		// If we don't have access to the parent, we do not have access to this
		if ( ! gamipress_user_has_access_to_achievement( $user_id, $parent_achievement->ID, $trigger, $site_id, $args ) ) {
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
	return apply_filters( 'user_has_access_to_achievement', $return, $user_id, $achievement_id, $trigger, $site_id, $args );

}

/**
 * Checks if an user is allowed to work on a given requirement related to a specific ID
 *
 * @since  1.0.8
 *
 * @param bool      $return             The default return value
 * @param int       $user_id            The given user's ID
 * @param int       $requirement_id     The given requirement's post ID
 * @param string    $trigger            The trigger triggered
 * @param int       $site_id            The site id
 * @param array     $args               Arguments of this trigger
 *
 * @return bool                         True if user has access to the requirement, false otherwise
 */
function gamipress_user_has_access_to_specific_requirement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

	// If we're not working with a requirement, bail here
	if ( ! in_array( gamipress_get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) )
		return $return;

	// If is specific trigger rules engine needs the attached id
	if( $return && in_array( $trigger, array_keys( gamipress_get_specific_activity_triggers() ) ) ) {

		$specific_id = gamipress_specific_trigger_get_id( $trigger, $args );
		$required_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_achievement_post' ) );

		// True if there is a specific id, a attached id and both are equal
		$return = (bool) (
			$specific_id !== 0
			&& $required_id !== 0
			&& $specific_id === $required_id
		);

	}

	// Send back our eligibility
	return $return;
}
add_filter( 'user_has_access_to_achievement', 'gamipress_user_has_access_to_specific_requirement', 10, 6 );

/**
 * Checks if an user is allowed to work on a given step
 *
 * @since  1.0.0
 *
 * @param  bool     $return     The default return value
 * @param  int      $user_id    The given user's ID
 * @param  int      $step_id    The given step's post ID
 *
 * @return bool                 True if user has access to step, false otherwise
 */
function gamipress_user_has_access_to_step( $return = false, $user_id = 0, $step_id = 0 ) {

	// If we're not working with a step, bail here
	if ( 'step' !== gamipress_get_post_type( $step_id ) )
		return $return;

	// Prevent user from earning steps with no parents
	$achievement = gamipress_get_step_achievement( $step_id );

	if ( ! $achievement ) {
		return false;
	}

	// Prevent user from earning steps if achievement is not setup to be earned through steps
    if( 'triggers' !== gamipress_get_post_meta( $achievement->ID, '_gamipress_earned_by' ) ) {
        return false;
    }

	// Prevent user from repeatedly earning the same step
	if ( $return && gamipress_get_user_achievements( array(
			'user_id'        => absint( $user_id ),
			'achievement_id' => absint( $step_id ),
			'since'          => absint( gamipress_achievement_last_user_activity( $achievement->ID, $user_id ) )
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
 *
 * @param  bool    $return   			The default return value
 * @param  integer $user_id  			The given user's ID
 * @param  integer $points_award_id  	The given points award's post ID
 *
 * @return bool              			True if user has access to step, false otherwise
 */
function gamipress_user_has_access_to_points_award( $return = false, $user_id = 0, $points_award_id = 0 ) {

	// If we're not working with a points award, bail here
	if ( 'points-award' !== gamipress_get_post_type( $points_award_id ) )
		return $return;

	// Prevent user from earning points awards with no points type
	$points_type = gamipress_get_points_award_points_type( $points_award_id );

	if ( ! $points_type ) {
		return false;
	}

	$maximum_earnings = absint( gamipress_get_post_meta( $points_award_id, '_gamipress_maximum_earnings' ) );

	// No maximum earnings set
	if( $maximum_earnings === 0 ) {
		return $return;
	}

	$earned_times = count( gamipress_get_user_achievements( array(
		'user_id'        => absint( $user_id ),
		'achievement_id' => absint( $points_award_id ),
		'since'          => absint( gamipress_achievement_last_user_activity( $points_award_id, $user_id ) )
	) ) );

	// Prevent user to exceed maximum earnings the same points award
	if ( $return && $earned_times >= $maximum_earnings )
		$return = false;

	// Send back our eligibility
	return $return;

}
add_filter( 'user_has_access_to_achievement', 'gamipress_user_has_access_to_points_award', 10, 3 );

/**
 * Checks if an user is allowed to work on a given points deduct
 *
 * @since  1.3.7
 *
 * @param  bool    $return   			The default return value
 * @param  integer $user_id  			The given user's ID
 * @param  integer $points_deduct_id  	The given points deduct's post ID
 *
 * @return bool              			True if user has access to step, false otherwise
 */
function gamipress_user_has_access_to_points_deduct( $return = false, $user_id = 0, $points_deduct_id = 0 ) {

	// If we're not working with a step, bail here
	if ( 'points-deduct' !== gamipress_get_post_type( $points_deduct_id ) )
		return $return;

	// Prevent user from earning points deducts with no points type
	$points_type = gamipress_get_points_deduct_points_type( $points_deduct_id );

	if ( ! $points_type ) {
		return false;
	}

	$maximum_earnings = absint( gamipress_get_post_meta( $points_deduct_id, '_gamipress_maximum_earnings' ) );

	// No maximum earnings set
	if( $maximum_earnings === 0 ) {
		return $return;
	}

	$earned_times = count( gamipress_get_user_achievements( array(
		'user_id'        => absint( $user_id ),
		'achievement_id' => absint( $points_deduct_id ),
		'since'          => absint( gamipress_achievement_last_user_activity( $points_deduct_id, $user_id ) )
	) ) );

	// Prevent user to exceed maximum earnings the same points deduct
	if ( $return && $earned_times >= $maximum_earnings )
		$return = false;

	// Send back our eligibility
	return $return;

}
add_filter( 'user_has_access_to_achievement', 'gamipress_user_has_access_to_points_deduct', 10, 3 );

/**
 * Checks if an user is allowed to work on a given rank requirement
 *
 * @since  1.0.0
 *
 * @param  bool    $return   The default return value
 * @param  integer $user_id  The given user's ID
 * @param  integer $rank_requirement_id  The given rank requirement's post ID
 *
 * @return bool              True if user has access to step, false otherwise
 */
function gamipress_user_has_access_to_rank_requirement( $return = false, $user_id = 0, $rank_requirement_id = 0 ) {

	// If we're not working with a rank requirement, bail here
	if ( 'rank-requirement' !== gamipress_get_post_type( $rank_requirement_id ) )
		return $return;

	// If is a rank requirement, we need to check if rank requirement is for next rank and not other
	$requirement_rank = gamipress_get_rank_requirement_rank( $rank_requirement_id );

	// Bail if not rank assigned to this rank requirement
	if( ! $requirement_rank ) {
		return false;
	}

	$next_user_rank_id = gamipress_get_next_user_rank_id( $user_id, $requirement_rank->post_type );

	if( $return && $next_user_rank_id === 0 ) {
		$return = false;
	}

	if ( $return && $requirement_rank->ID !== $next_user_rank_id ) {
		$return = false;
	}

	// Prevent user from repeatedly earning the same requirement
	if ( $return && gamipress_get_user_achievements( array(
			'user_id'        => absint( $user_id ),
			'achievement_id' => absint( $rank_requirement_id ),
			'since'          => absint( gamipress_achievement_last_user_activity( $requirement_rank->ID, $user_id ) )
		) )
	) {
		$return = false;
	}

	// Send back our eligibility
	return $return;
}
add_filter( 'user_has_access_to_achievement', 'gamipress_user_has_access_to_rank_requirement', 10, 3 );

/* --------------------------------------------------
 * Completion Checks
   -------------------------------------------------- */

/**
 * Check if user has completed an achievement
 *
 * @since  1.0.0
 *
 * @param  integer $achievement_id The given achievement ID to verify
 * @param  integer $user_id        The given user's ID
 * @param  string  $this_trigger   The trigger
 * @param  integer $site_id        The triggered site id
 * @param  array   $args           The triggered args
 *
 * @return bool                    True if user has completed achievement, false otherwise
 */
function gamipress_check_achievement_completion_for_user( $achievement_id = 0, $user_id = 0, $this_trigger = '', $site_id = 0, $args = array() ) {

	// Assume the user has completed the achievement
	$return = true;

	// Set to current site id
	if ( ! $site_id )
		$site_id = get_current_blog_id();

	// If the user has not already earned the achievement...
	if ( ! gamipress_get_user_achievements( array(
		'user_id' => absint( $user_id ),
		'achievement_id' => absint( $achievement_id ),
		'since' => 1 + gamipress_achievement_last_user_activity( $achievement_id, $user_id )
	) ) ) {

		// Grab published required achievements for this achievement
		$required_achievements = gamipress_get_required_achievements_for_achievement( $achievement_id, 'publish' );

		// If we have requirements, loop through each and make sure they've been completed
		if ( is_array( $required_achievements ) && ! empty( $required_achievements ) ) {

			foreach ( $required_achievements as $requirement ) {

				$requirement_earned = gamipress_get_user_achievements( array(
					'user_id' => $user_id,
					'achievement_id' => $requirement->ID,
					'since' => gamipress_achievement_last_user_activity( $achievement_id, $user_id ) - 1
				) );

				// Has the user already earned the requirement?
				if ( empty( $requirement_earned ) ) {
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
 * @since   1.0.0
 * @updated 1.6.4 Added checks for 'points-balance' event
 *
 * @param  bool 	$return 		The current status of whether or not the user deserves this achievement
 * @param  integer	$user_id 		The given user's ID
 * @param  integer 	$achievement_id The given achievement's post ID
 *
 * @return bool Our possibly updated earning status
 */
function gamipress_user_meets_points_requirement( $return = false, $user_id = 0, $achievement_id = 0 ) {

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

	// First, see if the achievement requires a minimum amount of points
	if (
		'points' === gamipress_get_post_meta( $achievement_id, '_gamipress_earned_by' ) 			// Check for achievements earned by points
		|| 'earn-points' === gamipress_get_post_meta( $achievement_id, '_gamipress_trigger_type' ) 	// Check for requirements with earn points activity

	) {

		// Grab our user's points and see if they at least as many as required
		$points_required        = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_points_required' ) );
		$points_type_required   = gamipress_get_post_meta( $achievement_id, '_gamipress_points_type_required' );
        $last_activity          = absint( gamipress_achievement_last_user_activity( $achievement_id, $user_id ) );

		// Get user points earned since last time has earning the achievement
		$awarded_points    		= gamipress_get_user_points_awarded( $user_id, $points_type_required, $last_activity - 2 );

		if( $awarded_points >= $points_required ) {

			// If the user just earned the achievement, though, don't let them earn it again
			// This prevents an infinite loop if the achievement has no maximum earnings limit
			$minimum_time  	= current_time( 'timestamp' ) - 1;

			if ( $last_activity > $minimum_time ) {
				$return = false;
			}

		} else {
            $return = false;
        }

	} else if( 'points-balance' === gamipress_get_post_meta( $achievement_id, '_gamipress_trigger_type' ) ) {

        // Grab our user's points and see if they at least as many as required
        $points_required        = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_points_required' ) );
        $points_type_required   = gamipress_get_post_meta( $achievement_id, '_gamipress_points_type_required' );

        // Get user points balance
        $points_balance    		= gamipress_get_user_points( $user_id, $points_type_required );

        if( $points_balance >= $points_required ) {

            // If the user just earned the achievement, though, don't let them earn it again
            // This prevents an infinite loop if the achievement has no maximum earnings limit
            $minimum_time  	= current_time( 'timestamp' ) - 1;
            $last_activity  = absint( gamipress_achievement_last_user_activity( $achievement_id, $user_id ) );

            if ( $last_activity > $minimum_time ) {
                $return = false;
            }

        } else {
            $return = false;
        }

	} else if( 'gamipress_expend_points' === gamipress_get_post_meta( $achievement_id, '_gamipress_trigger_type' ) ) {

		// Grab our user's points expended and see if they at least as many as required
		$points_required        = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_points_required' ) );
		$points_type_required   = gamipress_get_post_meta( $achievement_id, '_gamipress_points_type_required' );

		// Get user points expended since last time has earning the achievement
		$expended_points 		= gamipress_get_user_points_expended( $user_id, $points_type_required, gamipress_achievement_last_user_activity( $achievement_id, $user_id ) - 1 );

		if ( $expended_points < $points_required )
			$return = false;

	}

	// Return our eligibility status
	return $return;

}
add_filter( 'user_deserves_achievement', 'gamipress_user_meets_points_requirement', 10, 3 );


/**
 * Check if user meets the rank requirement for a given achievement
 *
 * @since  1.3.1
 *
 * @param  bool    $return         The current status of whether or not the user deserves this achievement
 * @param  integer $user_id        The given user's ID
 * @param  integer $achievement_id The given achievement's post ID
 *
 * @return bool                    Our possibly updated earning status
 */
function gamipress_user_meets_rank_requirement( $return = false, $user_id = 0, $achievement_id = 0 ) {

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

	// First, see if the achievement requires a minimum rank
	if (
		'rank' === gamipress_get_post_meta( $achievement_id, '_gamipress_earned_by' ) 				// Check for achievements earned by rank
		|| 'earn-rank' === gamipress_get_post_meta( $achievement_id, '_gamipress_trigger_type' ) 	// Check for requirements with earn rank activity
	) {
		// Grab our user's rank and compared it with the required one
		$rank_required   = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_rank_required' ) );
		$user_rank_id    = gamipress_get_user_rank_id( $user_id, gamipress_get_post_type( $rank_required ) );

		if ( $user_rank_id === $rank_required )
			$return = true;
		else
			$return = false;

		if( $return ) {

			// If the user just earned the achievement, though, don't let them earn it again
			// This prevents an infinite loop if the achievement has no maximum earnings limit
			$last_activity 	= gamipress_achievement_last_user_activity( $achievement_id, $user_id );
			$minimum_time 	= current_time( 'timestamp' ) - 1;

			if ( $last_activity > $minimum_time ) {
				$return = false;
			}

		}
	}

	// Return our eligibility status
	return $return;

}
add_filter( 'user_deserves_achievement', 'gamipress_user_meets_rank_requirement', 10, 3 );

/**
 * Validate whether or not the user has completed all requirements for an achievement
 *
 * @since  1.0.0
 *
 * @param  bool $return      		True if user deserves achievement, false otherwise
 * @param  integer $user_id  		The given user's ID
 * @param  integer $achievement_id  The achievement post ID
 *
 * @return bool              		True if user deserves step, false otherwise
 */
function gamipress_user_deserves_limit_requirements( $return = false, $user_id = 0, $achievement_id = 0 ) {

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

	$trigger_type = gamipress_get_post_meta( $achievement_id, '_gamipress_trigger_type' );

	// Check if activity trigger is excluded from this check
	if( in_array( $trigger_type, gamipress_get_activity_triggers_excluded_from_activity_limit() ) ) {
		return $return;
	}

	// Only override the $return data if we're working on a requirement
	if ( in_array( gamipress_get_post_type( $achievement_id ), gamipress_get_requirement_types_slugs() ) ) {

		// Check if is limited over time
		$since = gamipress_get_achievement_limit_timestamp( $achievement_id );

		if( $since > 0 ) {

			// Activity count limit over time
			$activity_count_limit = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_limit' ) );

			// Activity count limited to a timestamp
			$activity_count = absint( gamipress_get_achievement_activity_count( $user_id, $achievement_id, $since ) );

			// Force bail if user exceeds the limit over time
			if( $activity_count > $activity_count_limit ) {
				return false;
			}

		}

		// Get the required number of check-ins
		$required_activity_count = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_count' ) );

		// Grab the relevant activity count
		$activity_count = absint( gamipress_get_achievement_activity_count( $user_id, $achievement_id ) );

		// If we meet or exceed the required number of check-ins, then deserve the achievement
		if ( $activity_count >= $required_activity_count ) {
			$return = true;
		} else {
			$return = false;
		}
	}

	return $return;
}
add_filter( 'user_deserves_achievement', 'gamipress_user_deserves_limit_requirements', 10, 3 );

/**
 * Count the user's relevant actions for a given achievement
 *
 * @since  1.0.0
 *
 * @param  integer $user_id 		The given user's ID
 * @param  integer $achievement_id 	The given achievement's ID
 * @param  integer $since 			Timestamp since retrieve the count
 *
 * @return integer          		The total activity count
 */
function gamipress_get_achievement_activity_count( $user_id = 0, $achievement_id = 0, $since = 0 ) {

	// Setup site id
	$site_id = get_current_blog_id();

	// Assume the user has no relevant activities
	$activities = 0;

	$post_type = gamipress_get_post_type( $achievement_id );

	if ( in_array( $post_type, gamipress_get_requirement_types_slugs() ) ) {

		// Grab the requirements
		$requirements = gamipress_get_requirement_object( $achievement_id );

		// Determine which type of trigger we're using and return the corresponding activities
		switch( $requirements['trigger_type'] ) {
			case 'specific-achievement':

				if( $post_type === 'step' && $since === 0 ) {

					// Get our parent achievement
					$achievement = gamipress_get_step_achievement( $achievement_id );

					// If the user has any interaction with this achievement, only get activity since that date
					if ( $achievement && $date = gamipress_achievement_last_user_activity( $achievement->ID, $user_id ) ) {
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

		// Just continue if trigger is set
		if( isset( $trigger ) ) {

			if( $since === 0 ) {

				if( gamipress_get_post_type( $achievement_id ) === 'rank-requirement' ) {
					// If since is not defined and is a rank requirement, we need to get the last time user earned the latest rank of type
					$rank = gamipress_get_rank_requirement_rank( $achievement_id );

					$since = gamipress_get_rank_earned_time( $user_id, $rank->post_type );
				} else {
					// If since is not defined, then get activity count since achievement publish date
					$since = strtotime( gamipress_get_post_date( $achievement_id ) );
				}

			}

            $activities = gamipress_get_user_trigger_count( $user_id, $trigger, $since, $site_id, $requirements );

		}

	}

	// Available filter for overriding user activity
	return absint( apply_filters( "gamipress_{$post_type}_activity", $activities, $user_id, $achievement_id ) );
}

/**
 * Get the start date where an achievement is limiting
 *
 * @param int $achievement_id	The achievement ID
 *
 * @return int 					Timestamp from the limit starts
 */
function gamipress_get_achievement_limit_timestamp( $achievement_id = 0 ) {

	$limit_type = gamipress_get_post_meta( $achievement_id, '_gamipress_limit_type' );

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

	return apply_filters( 'gamipress_get_achievement_limit_timestamp', $from, $achievement_id );

}

/* --------------------------------------------------
 * Award Checks
   -------------------------------------------------- */

/**
 * Award an achievement to the user
 *
 * @since  1.0.0
 *
 * @param  integer 	$achievement_id The given achievement ID to award
 * @param  integer 	$user_id        The given user's ID
 * @param  integer 	$admin_id       The given admin's ID
 * @param  string 	$trigger    	The trigger
 * @param  integer 	$site_id        The triggered site id
 * @param  array 	$args           The triggered args
 *
 * @return mixed                   False on not achievement, void otherwise
 */
function gamipress_award_achievement_to_user( $achievement_id = 0, $user_id = 0, $admin_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

	global $wp_filter;

	// Sanity Check: ensure we're working with an achievement or requirement post
	if ( ! ( gamipress_is_achievement( $achievement_id ) || gamipress_is_requirement( $achievement_id ) ) )
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
	gamipress_log_user_achievement_award( $user_id, $achievement_id, $admin_id, $trigger );

	// Available hook for unlocking any achievement of this achievement type
	do_action( 'gamipress_unlock_' . $achievement_object->post_type, $user_id, $achievement_id, $trigger, $site_id, $args );

	// Patch for WordPress to support recursive actions, specifically for gamipress_award_achievement
	// Because global iteration is fun, assuming we can get this fixed for WordPress 3.9
	$is_recursed_filter = ( current_filter() === 'gamipress_award_achievement');
	$current_key = null;

	// Get current position
	if ( $is_recursed_filter ) {
		$current_key = key( $wp_filter[ 'gamipress_award_achievement' ] );
	}

	// Available hook to do other things with each awarded achievement
	do_action( 'gamipress_award_achievement', $user_id, $achievement_id, $trigger, $site_id, $args );

	if ( $is_recursed_filter ) {
		reset( $wp_filter[ 'gamipress_award_achievement' ] );

		while ( key( $wp_filter[ 'gamipress_award_achievement' ] ) !== $current_key ) {
			next( $wp_filter[ 'gamipress_award_achievement' ] );
		}
	}

}

/**
 * Award additional achievements to user
 *
 * @since   1.0.0
 * @update  1.6.9 Correctly add the trigger type to the maybe award function call
 *
 * @param  integer $user_id        The given user's ID
 * @param  integer $achievement_id The given achievement's post ID
 *
 * @return void
 */
function gamipress_maybe_award_additional_achievements_to_user( $user_id = 0, $achievement_id = 0 ) {

    // Get the achievement post type
    $post_type = gamipress_get_post_type( $achievement_id );
    $achievement_types = gamipress_get_achievement_types_slugs();
    $rank_types = gamipress_get_achievement_types_slugs();


	// Get achievements that can be earned from completing this achievement
	$dependent_achievements = gamipress_get_dependent_achievements( $achievement_id );

    // See if the user has unlocked all achievements of a given type
    gamipress_maybe_trigger_unlock_all( $user_id, $achievement_id );

	// Loop through each dependent achievement and see if it can be awarded
	foreach ( $dependent_achievements as $achievement ) {

	    $trigger = '';

	    if( in_array( $post_type, $achievement_types ) ) {
            $trigger = 'specific-achievement';
        } else if( in_array( $post_type, $rank_types ) ) {
            $trigger = 'earn-rank';
        }

		gamipress_maybe_award_achievement_to_user( $achievement->ID, $user_id, $trigger );
	}

}
add_action( 'gamipress_award_achievement', 'gamipress_maybe_award_additional_achievements_to_user', 10, 2 );

/**
 * Check if the user has unlocked all achievements of a given type
 *
 * Triggers hook gamipress_unlock_all_{$post_type}
 *
 * @since  1.0.0
 *
 * @param  integer $user_id        The given user's ID
 * @param  integer $achievement_id The given achievement's post ID
 *
 * @return void
 */
function gamipress_maybe_trigger_unlock_all( $user_id = 0, $achievement_id = 0 ) {

    // Get the post type of the earned achievement
    $post_type = gamipress_get_post_type( $achievement_id );

	// Grab our user's (presumably updated) earned achievements
	$earned_achievements = gamipress_get_user_achievements( array( 'user_id' => $user_id, 'achievement_type' => $post_type ) );

	// Hook for unlocking all achievements of this achievement type
	if ( $all_achievements_of_type = gamipress_get_achievements( array( 'post_type' => $post_type ) ) ) {

		// Assume we can award the user for unlocking all achievements of this type
		$all_per_type = true;

		// Loop through each of our achievements of this type
		foreach ( $all_achievements_of_type as $achievement ) {

			// Assume the user hasn't earned this achievement
			$found_achievement = false;

			// Loop through each earned achievement and see if we've earned it
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
 * Award new points to the user based on logged activites and earned achievements
 *
 * @since  1.0.0
 *
 * @param  integer $user_id        The given user's ID
 * @param  integer $achievement_id The given achievement's post ID
 */
function gamipress_maybe_award_points( $user_id = 0, $achievement_id = 0 ) {

	// Grab our points from the provided post
	$points = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_points' ) );
	$points_type = gamipress_get_post_meta( $achievement_id, '_gamipress_points_type' );
	$points_types = gamipress_get_points_types();

	if ( ! empty( $points ) && isset( $points_types[$points_type] ) ) {

		$post_type = gamipress_get_post_type( $achievement_id );

		if( $post_type === 'points-deduct' ) {
			gamipress_deduct_points_to_user( $user_id, $points, $points_type, array( 'achievement_id' => $achievement_id ) );
		} else {
			gamipress_award_points_to_user( $user_id, $points, $points_type, array( 'achievement_id' => $achievement_id ) );
		}

	}

}
add_action( 'gamipress_award_achievement', 'gamipress_maybe_award_points', 10, 2 );

/**
 * Award same achievement to the user based on how many points has earn
 *
 * @since  1.0.0
 *
 * @param  integer $user_id        The given user's ID
 * @param  integer $achievement_id The given achievement's post ID
 */
function gamipress_maybe_award_multiple_points( $user_id = 0, $achievement_id = 0 ) {

    // First, see if the requirement requires a minimum amount of points (just requirements has this meta)
    if ( 'earn-points' === gamipress_get_post_meta( $achievement_id, '_gamipress_trigger_type' ) ) {

        // Grab our user's points and see if they at least as many as required
        $points_required        = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_points_required' ) );
        $points_type_required   = gamipress_get_post_meta( $achievement_id, '_gamipress_points_type_required' );

        // Check if we are in a loop of multiple points to award
        if( ! ( isset( $GLOBALS["gamipress_doing_multiple_{$points_type_required}_award"] ) && $GLOBALS["gamipress_doing_multiple_{$points_type_required}_award"] === true ) ) {

            $last_achievement_activity = absint( gamipress_achievement_last_user_activity( $achievement_id, $user_id ) );

            // Get user points earned since last time has earning the achievement
            $user_last_points = gamipress_get_user_points_awarded( $user_id, $points_type_required, $last_achievement_activity - 2 );

            if( $user_last_points >= $points_required && $points_required > 0 ) {

                // Starting multiple points award
                $GLOBALS["gamipress_doing_multiple_{$points_type_required}_award"] = true;

                // Set times to award
                $times_to_award = intval( $user_last_points / $points_required ) - 1; // -1 is to prevent award the current one

                // Check the maximum times this requirement could be earned
                $maximum_earnings = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_maximum_earnings' ) );

                // If maximum earnings is different to 0, we need to set how many times to award it
                if( $maximum_earnings !== 0 ) {

                    $earned_times = count( gamipress_get_user_achievements( array(
                        'user_id'        => absint( $user_id ),
                        'achievement_id' => absint( $achievement_id ),
                        'since'          => $last_achievement_activity
                    ) ) );

                    // If times to award and earned times exceed the maximum earnings
                    if( ( $times_to_award + $earned_times ) >= $maximum_earnings  ) {
                        $times_to_award = ( $maximum_earnings - $earned_times ) - 1; // -1 is to prevent award the current one
                    }

                }

                // Award same achievement many times (rules engine will check limited times to earn it)
                for( $i=0; $i < $times_to_award; $i++ ) {
                    gamipress_award_achievement_to_user( $achievement_id, $user_id );
                }

                // Ending multiple points award
                $GLOBALS["gamipress_doing_multiple_{$points_type_required}_award"] = false;

            }

        }

    }

}
add_action( 'gamipress_award_achievement', 'gamipress_maybe_award_multiple_points', 15, 2 );

/**
 * Award new rank to the user based on logged activites and earned achievements
 *
 * @since  1.3.1
 *
 * @param  integer $user_id        The given user's ID
 * @param  integer $achievement_id The given achievement's post ID
 */
function gamipress_maybe_award_rank( $user_id = 0, $achievement_id = 0 ) {

	if( gamipress_get_post_type( $achievement_id ) !== 'rank-requirement' )
		return;

	// Get the requirement rank
	$rank = gamipress_get_rank_requirement_rank( $achievement_id );

	if( ! $rank )
		return;

	$old_rank = gamipress_get_user_rank( $user_id );

	// Return if current rank is this one
	if( $old_rank && $old_rank->ID === $rank->ID )
		return;

	// Get all requirements of this rank
	$requirements = gamipress_get_rank_requirements( $rank->ID );

	$completed = true;

	foreach( $requirements as $requirement ) {
		// Check if rank requirement has been earned
		if( ! gamipress_get_user_achievements( array(
			'user_id' => $user_id,
			'achievement_id' => $requirement->ID,
			'since' => strtotime( $rank->post_date )
		) ) ) {
			$completed = false;
			break;
		}
	}

	// If all rank requirements has been completed, award the rank
	if ( $completed ) {
		gamipress_update_user_rank( $user_id, $rank->ID, false, $achievement_id );
	}

}
add_action( 'gamipress_award_achievement', 'gamipress_maybe_award_rank', 10, 2 );

/* --------------------------------------------------
 * Revoke Checks
   -------------------------------------------------- */

/**
 * Revoke an achievement to the user
 *
 * @since  	1.4.3
 *
 * @param int 	$achievement_id The given achievement's post ID
 * @param int 	$user_id        The given user's ID
 * @param int 	$earning_id     The user's earning ID
 *
 * @return void
 */
function gamipress_revoke_achievement_to_user( $achievement_id = 0, $user_id = 0, $earning_id = 0 ) {

	// Use the current user's ID if none specified
	if ( ! $user_id )
		$user_id = wp_get_current_user()->ID;

	// Setup CT object
	$ct_table = ct_setup_table( 'gamipress_user_earnings' );

	if( $earning_id === 0 ) {

		$query = new CT_Query( array(
			'user_id' => $user_id,
			'post_id' => $achievement_id,
			'items_per_page' => 1
		) );

		$results = $query->get_results();

		if( count( $results ) > 0 ) {
			$earning_id = $results[0]->user_earning_id;
		}

	}

	// Available action for triggering other processes
	do_action( 'gamipress_revoke_achievement_to_user', $user_id, $achievement_id, $earning_id );

	if( $earning_id ) {
		$ct_table->db->delete( $earning_id );
	}

}