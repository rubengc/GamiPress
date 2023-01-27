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
 * @since  1.8.2 Function now informs if achievement has been awarded or not
 *
 * @param  int      $achievement_id     The given achievement ID to possibly award
 * @param  int      $user_id            The given user's ID
 * @param  string   $trigger            The trigger
 * @param  int      $site_id            The triggered site id
 * @param  array    $args               The triggered args
 *
 * @return bool|WP_Error                 True if achievement has been awarded, false or a WP_Error object otherwise
 */
function gamipress_maybe_award_achievement_to_user( $achievement_id = 0, $user_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

	// Set to current site id
	if ( ! $site_id ) {
		$site_id = get_current_blog_id();
    }

	// Grab current user ID if one isn't specified
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
    }

	$result = true;

	// Checks if the user has access to this achievement
	if ( ! gamipress_user_has_access_to_achievement( $user_id, $achievement_id, $trigger, $site_id, $args ) ) {
        $result = new WP_Error( 'gamipress_not_access_achievement', __( 'User hasn\'t access to this achievement.', 'gamipress' ) );
    }

    // Checks if the user has completed the achievement
    if ( $result === true && ! gamipress_check_achievement_completion_for_user( $achievement_id, $user_id, $trigger, $site_id, $args ) ) {
        $result = new WP_Error( 'gamipress_not_completed_achievement', __( 'User hasn\'t completed this achievement yet.', 'gamipress' ) );
    }

    // Only award the achievement if no errors confirmed
    if ( $result === true ) {
        gamipress_award_achievement_to_user( $achievement_id, $user_id, false, $trigger, $site_id, $args );
    }

    /**
     * After check if user should earn an achievement, and award it if so.
     *
     * @since  1.8.2
     *
     * @param  bool|WP_Error    $result             True if achievement has been awarded, false or a WP_Error object otherwise
     * @param  int              $user_id            The given user's ID
     * @param  int              $achievement_id     The given achievement ID to possibly award
     * @param  string           $trigger            The trigger
     * @param  int              $site_id            The triggered site id
     * @param  array            $args               The triggered args
     *
     * @return bool|WP_Error                         True if achievement has been awarded, false or a WP_Error object otherwise
     */
	return apply_filters( 'gamipress_maybe_award_achievement_to_user', $result, $user_id, $achievement_id, $trigger, $site_id, $args );

}

/* --------------------------------------------------
 * Access Checks
   -------------------------------------------------- */

/**
 * Check if user may access/earn achievement.
 *
 * @since  1.0.0
 *
 * @param  int      $achievement_id     The given achievement ID to possibly award
 * @param  int      $user_id            The given user's ID
 * @param  string   $trigger            The trigger
 * @param  int      $site_id            The triggered site id
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

	// Get the achievement post type to exclude some of them
	$post_type = gamipress_get_post_type( $achievement_id );

	if ( $return
        && ! in_array( $post_type, array( 'points-award', 'points-deduct' ) ) // If not is a points award or deduct
        && $parent_id = absint( gamipress_get_post_field( 'post_parent', $achievement_id ) )  // If achievement has a parent
    ) {

		// If we don't have access to the parent, we do not have access to this
		if ( ! gamipress_user_has_access_to_achievement( $user_id, $parent_id, $trigger, $site_id, $args ) ) {
			$return = false;
        }

		// If the parent requires sequential steps, confirm we've earned all previous steps
		if ( $return && gamipress_is_achievement_sequential( $parent_id ) ) {

			foreach ( gamipress_get_children_of_achievement( $parent_id ) as $sibling ) {
				// If this is the current step, we're good to go
				if ( $sibling->ID == $achievement_id ) {
				    break;
                }

                // Skip optional requirements
                if( ( bool ) gamipress_get_post_meta( $sibling->ID, '_gamipress_optional' ) ) {
                    continue;
                }

				// If we haven't earned any previous step, we can't earn this one
				if ( ! gamipress_has_user_earned_achievement( $sibling->ID, $user_id ) ) {
					$return = false;
					break;
				}
			}

		}
	}

    /**
     * Available filter for custom overrides
     *
     * @since  1.0.0
     *
     * @param  bool     $return             True if user has access, false otherwise
     * @param  int      $user_id            The given user's ID
     * @param  int      $achievement_id     The given achievement ID to possibly award
     * @param  string   $trigger            The trigger
     * @param  int      $site_id            The triggered site id
     * @param  array    $args               The triggered args
     *
     * @return bool                    	    True if user has access, false otherwise
     */
	return apply_filters( 'user_has_access_to_achievement', $return, $user_id, $achievement_id, $trigger, $site_id, $args );

}

/**
 * Checks if a user is allowed to work on a given requirement related to a specific ID
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

    // Bail if access is not already granted
    if( ! $return ) {
        return $return;
    }

	// If we're not working with a requirement, bail here
	if ( ! in_array( gamipress_get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) ) {
		return $return;
    }

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
 * Checks if a user is allowed to work on a given step
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

    // Bail if access is not already granted
    if( ! $return ) {
        return $return;
    }

	// If we're not working with a step, bail here
	if ( 'step' !== gamipress_get_post_type( $step_id ) ) {
		return $return;
    }

	// Prevent user from earning steps with no parents
	$achievement = gamipress_get_step_achievement( $step_id );

	if ( ! $achievement ) {
		return false;
    }

	// Prevent user from earning steps if achievement is not setup to be earned through steps
    if( 'triggers' !== gamipress_get_post_meta( $achievement->ID, '_gamipress_earned_by' ) ) {
        return false;
    }

    if( $return ) {

        $earned_times = gamipress_get_earnings_count( array(
            'user_id'   => absint( $user_id ),
            'post_id'   => absint( $step_id ),
            'since'     => absint( gamipress_achievement_last_user_activity( $achievement->ID, $user_id ) )
        ) );

        // Prevent user from repeatedly earning the same step
        if ( $earned_times > 0 ) {
            $return = false;
        }

    }

	// Send back our eligibility
	return $return;
}
add_filter( 'user_has_access_to_achievement', 'gamipress_user_has_access_to_step', 10, 3 );

/**
 * Checks if a user is allowed to work on a given points award
 *
 * @since  1.0.0
 *
 * @param  bool     $return   			The default return value
 * @param  int      $user_id  			The given user's ID
 * @param  int      $points_award_id  	The given points award's post ID
 *
 * @return bool              			True if user has access to step, false otherwise
 */
function gamipress_user_has_access_to_points_award( $return = false, $user_id = 0, $points_award_id = 0 ) {

    // Bail if access is not already granted
    if( ! $return ) {
        return $return;
    }

	// If we're not working with a points award, bail here
	if ( 'points-award' !== gamipress_get_post_type( $points_award_id ) ) {
		return $return;
    }

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

    if ( $return ) {

        $earned_times = gamipress_get_earnings_count( array(
            'user_id'   => absint( $user_id ),
            'post_id'   => absint( $points_award_id ),
            'since'     => absint( gamipress_achievement_last_user_activity( $points_award_id, $user_id ) )
        ) );

        // Prevent user to exceed maximum earnings the same points award
        if ( $earned_times >= $maximum_earnings ) {
            $return = false;
        }

    }

	// Send back our eligibility
	return $return;

}
add_filter( 'user_has_access_to_achievement', 'gamipress_user_has_access_to_points_award', 10, 3 );

/**
 * Checks if a user is allowed to work on a given points deduct
 *
 * @since  1.3.7
 *
 * @param  bool     $return   			The default return value
 * @param  int      $user_id  			The given user's ID
 * @param  int      $points_deduct_id  	The given points deduct's post ID
 *
 * @return bool              			True if user has access to step, false otherwise
 */
function gamipress_user_has_access_to_points_deduct( $return = false, $user_id = 0, $points_deduct_id = 0 ) {

    // Bail if access is not already granted
    if( ! $return ) {
        return $return;
    }

	// If we're not working with a step, bail here
	if ( 'points-deduct' !== gamipress_get_post_type( $points_deduct_id ) ) {
		return $return;
    }

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

    if ( $return ) {

        $earned_times = gamipress_get_earnings_count( array(
            'user_id'   => absint( $user_id ),
            'post_id'   => absint( $points_deduct_id ),
            'since'     => absint( gamipress_achievement_last_user_activity( $points_deduct_id, $user_id ) )
        ) );

        // Prevent user to exceed maximum earnings the same points deduct
        if ( $earned_times >= $maximum_earnings ) {
            $return = false;
        }

    }

	// Send back our eligibility
	return $return;

}
add_filter( 'user_has_access_to_achievement', 'gamipress_user_has_access_to_points_deduct', 10, 3 );

/**
 * Checks if a user is allowed to work on a given rank requirement
 *
 * @since  1.0.0
 *
 * @param  bool     $return   The default return value
 * @param  int      $user_id  The given user's ID
 * @param  int      $rank_requirement_id  The given rank requirement's post ID
 *
 * @return bool              True if user has access to step, false otherwise
 */
function gamipress_user_has_access_to_rank_requirement( $return = false, $user_id = 0, $rank_requirement_id = 0 ) {

    // Bail if access is not already granted
    if( ! $return ) {
        return $return;
    }

	// If we're not working with a rank requirement, bail here
	if ( 'rank-requirement' !== gamipress_get_post_type( $rank_requirement_id ) ) {
		return $return;
    }

	// If is a rank requirement, we need to check if rank requirement is for next rank and not other
	$rank = gamipress_get_rank_requirement_rank( $rank_requirement_id );

	// Bail if not rank assigned to this rank requirement
	if( ! $rank ) {
		return false;
    }

	$next_user_rank_id = gamipress_get_next_user_rank_id( $user_id, $rank->post_type );

	if( $return && $next_user_rank_id === 0 ) {
		$return = false;
    }

	if ( $return && absint( $rank->ID ) !== $next_user_rank_id ) {
		$return = false;
    }

    if ( $return ) {

        $earned_times = gamipress_get_earnings_count( array(
            'user_id'   => absint( $user_id ),
            'post_id'   => absint( $rank_requirement_id ),
            'since'     => absint( gamipress_achievement_last_user_activity( $rank->ID, $user_id ) )
        ) );

        // Prevent user from repeatedly earning the same requirement
        if ( $earned_times > 0 ) {
            $return = false;
        }

    }

	// Send back our eligibility
	return $return;

}
add_filter( 'user_has_access_to_achievement', 'gamipress_user_has_access_to_rank_requirement', 10, 3 );

/**
 * Check if user meets the post type requirement for a given achievement
 *
 * @since  1.9.9
 *
 * @param  bool     $return             True if user has access, false otherwise
 * @param  int      $user_id            The given user's ID
 * @param  int      $achievement_id     The given achievement ID to possibly award
 * @param  string   $trigger            The trigger
 * @param  int      $site_id            The triggered site id
 * @param  array    $args               The triggered args
 *
 * @return bool                         True if user has access to step, false otherwise
 */
function gamipress_user_meets_post_type_requirement( $return = false, $user_id = 0, $achievement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // Bail if access is not already granted
    if( ! $return ) {
        return $return;
    }

    $post_type = gamipress_get_post_type( $achievement_id );

    // Bail if not is a requirement
    if( ! in_array( $post_type, gamipress_get_requirement_types_slugs() ) ) {
        return $return;
    }

    if( empty( $trigger ) ) {
        $trigger = gamipress_get_post_meta( $achievement_id, '_gamipress_trigger_type' );
    }

    if( in_array( $trigger, array(
        'gamipress_new_comment_post_type',
        'gamipress_user_post_comment_post_type',
        'gamipress_spam_comment_post_type',
        'gamipress_publish_post_type',
        'gamipress_delete_post_type',
        'gamipress_post_type_visit',
        'gamipress_user_post_type_visit',
    ) ) ) {

        $index = 2;

        if( in_array( $trigger, array(
            'gamipress_new_comment_post_type',
            'gamipress_user_post_comment_post_type',
            'gamipress_spam_comment_post_type',
        ) ) ) {
            $index = 3;
        }

        $post_type = gamipress_get_event_arg( $args, 'post_type', $index );
        $post_type_required = gamipress_get_post_meta( $achievement_id, '_gamipress_post_type_required' );

        // Deserve if post type matches
        $return = (bool) ( $post_type === $post_type_required );
    }

    // Send back our eligibility
    return $return;

}
add_filter( 'user_has_access_to_achievement', 'gamipress_user_meets_post_type_requirement', 10, 6 );

/**
 * Check if user meets the role requirement for a given achievement
 *
 * @since  1.8.9
 *
 * @param  bool     $return             True if user has access, false otherwise
 * @param  int      $user_id            The given user's ID
 * @param  int      $achievement_id     The given achievement ID to possibly award
 * @param  string   $trigger            The trigger
 * @param  int      $site_id            The triggered site id
 * @param  array    $args               The triggered args
 *
 * @return bool                         True if user has access to step, false otherwise
 */
function gamipress_user_meets_role_requirement( $return = false, $user_id = 0, $achievement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // Bail if access is not already granted
    if( ! $return ) {
        return $return;
    }

    $post_type = gamipress_get_post_type( $achievement_id );

    // Bail if not is a requirement
    if( ! in_array( $post_type, gamipress_get_requirement_types_slugs() ) ) {
        return $return;
    }

    if( empty( $trigger ) ) {
        $trigger = gamipress_get_post_meta( $achievement_id, '_gamipress_trigger_type' );
    }

    if( in_array( $trigger, array(
        'gamipress_add_specific_role',
        'gamipress_set_specific_role',
        'gamipress_remove_specific_role'
    ) ) ) {
        $role = gamipress_get_event_arg( $args, 'role', 1 );
        $role_required = gamipress_get_post_meta( $achievement_id, '_gamipress_user_role_required' );

        // Deserve if role matches
        $return = (bool) ( $role === $role_required );
    }

    // Send back our eligibility
    return $return;

}
add_filter( 'user_has_access_to_achievement', 'gamipress_user_meets_role_requirement', 10, 6 );

/**
 * Check if user meets the meta requirement for a given achievement
 *
 * @since  1.0.0
 *
 * @param bool $return          The default return value
 * @param int $user_id          The given user's ID
 * @param int $requirement_id   The given requirement's post ID
 * @param string $trigger       The trigger triggered
 * @param int $site_id          The site id
 * @param array $args           Arguments of this trigger
 *
 * @return bool True if user has access to the requirement, false otherwise
 */
function gamipress_user_meets_meta_requirement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) )
        return $return;

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

    // Rules engine needs to check if meta key matches required ones
    if( $return && ( $trigger === 'gamipress_update_post_meta_any_value'
        || $trigger === 'gamipress_update_user_meta_any_value' ) ) {

        $meta_key = $args[2]; 
        $required_meta_key = gamipress_get_post_meta( $requirement_id, '_gamipress_meta_key_required', true );

        $return = (bool) ( $meta_key === $required_meta_key );
        
    }

    // Rules engine needs to check if meta key and values matches required ones
    if( $return && ( $trigger === 'gamipress_update_post_meta_specific_value'
        || $trigger === 'gamipress_update_user_meta_specific_value' ) ) {

        $meta_key = $args[2];
        $meta_value = $args[3];
        $required_meta_key = gamipress_get_post_meta( $requirement_id, '_gamipress_meta_key_required', true );
        $required_meta_value = gamipress_get_post_meta( $requirement_id, '_gamipress_meta_value_required', true );
        
        if ( is_numeric( $meta_value ) ){
            
            $meta_value = strval( $meta_value );
        }
        
        $return = (bool) ( $meta_key === $required_meta_key );

        if ( $return ) {
            // Check if field value matches the required one (with support for arrays)
            if( is_array( $meta_value ) )
                $return = (bool) ( in_array( $required_meta_value, $meta_value ) );
            else
                $return = (bool) ( $meta_value === $required_meta_value );
        }
 
    }

    // Send back our eligibility
    return $return;

}
add_filter( 'user_has_access_to_achievement', 'gamipress_user_meets_meta_requirement', 10, 6 );

/* --------------------------------------------------
 * Completion Checks
   -------------------------------------------------- */

/**
 * Check if user has completed an achievement
 *
 * @since  1.0.0
 *
 * @param  int      $achievement_id The given achievement ID to verify
 * @param  int      $user_id        The given user's ID
 * @param  string   $trigger_type   The trigger
 * @param  int      $site_id        The triggered site id
 * @param  array    $args           The triggered args
 *
 * @return bool                    True if user has completed achievement, false otherwise
 */
function gamipress_check_achievement_completion_for_user( $achievement_id = 0, $user_id = 0, $trigger_type = '', $site_id = 0, $args = array() ) {

	// Assume the user has completed the achievement
	$return = true;

	// Set to current site id
	if ( ! $site_id ) {
		$site_id = get_current_blog_id();
    }

    // Points types can't be earned
	if( gamipress_get_post_type( $achievement_id ) === 'points-type' ) {
	    return false;
    }

	// If the user has not already earned the achievement...
	if ( gamipress_get_earnings_count( array(
		'user_id'   => absint( $user_id ),
		'post_id'   => absint( $achievement_id ),
		'since'     => gamipress_achievement_last_user_activity( $achievement_id, $user_id ) + 1
	) ) === 0 ) {

		// Grab published required achievements for this achievement
		$required_achievements = gamipress_get_required_achievements_for_achievement( $achievement_id, 'publish' );

		// If we have requirements, loop through each and make sure they've been completed
		if ( is_array( $required_achievements ) && ! empty( $required_achievements ) ) {

			foreach ( $required_achievements as $requirement ) {

                // Skip optional requirements
                if( ( bool ) gamipress_get_post_meta( $requirement->ID, '_gamipress_optional' ) ) {
                    continue;
                }

				$requirement_earned = gamipress_get_earnings_count( array(
					'user_id'   => $user_id,
					'post_id'   => $requirement->ID,
					'since'     => gamipress_achievement_last_user_activity( $achievement_id, $user_id ) - 1
				) );

				// Has the user already earned the requirements?
				if ( $requirement_earned === 0 ) {
					$return = false;
					break;
				}
			}

		}
	}

    /**
     * Available filter to support custom earning rules
     *
     * @since  1.0.0
     *
     * @param  bool     $return         True if user has completed achievement, false otherwise
     * @param  int      $user_id        The given user's ID
     * @param  int      $achievement_id The given achievement ID to verify
     * @param  string   $trigger_type   The trigger
     * @param  int      $site_id        The triggered site id
     * @param  array    $args           The triggered args
     *
     * @return bool                    True if user has completed achievement, false otherwise
     */
	return apply_filters( 'user_deserves_achievement', $return, $user_id, $achievement_id, $trigger_type, $site_id, $args );

}

/**
 * Check if user meets the points requirement for a given achievement
 *
 * @since   1.0.0
 * @updated 1.6.4 Added checks for 'points-balance' event
 *
 * @param  bool 	$return 		The current status of whether or not the user deserves this achievement
 * @param  int	    $user_id 		The given user's ID
 * @param  int 	    $achievement_id The given achievement's post ID
 * @param  string   $trigger_type   The trigger
 * @param  int      $site_id        The triggered site id
 * @param  array    $args           The triggered args
 *
 * @return bool Our possibly updated earning status
 */
function gamipress_user_meets_points_requirement( $return = false, $user_id = 0, $achievement_id = 0, $trigger_type = '', $site_id = 0, $args = array() ) {

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return ) {
        return $return;
    }

    if( empty( $trigger_type ) ) {
        $trigger_type = gamipress_get_post_meta( $achievement_id, '_gamipress_trigger_type' );
    }

	// First, see if the achievement requires a minimum amount of points
	if (
		'points' === gamipress_get_post_meta( $achievement_id, '_gamipress_earned_by' ) 			// Check for achievements earned by points
		|| 'earn-points' === $trigger_type 	                                                        // Check for requirements with earn points activity
	) {

		// Grab our user's points and see if they at least as many as required
		$points_required        = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_points_required' ) );
		$points_type_required   = gamipress_get_post_meta( $achievement_id, '_gamipress_points_type_required' );
        $last_activity          = absint( gamipress_achievement_last_user_activity( $achievement_id, $user_id ) );

		// Get user points earned since last time has earning the achievement
		$awarded_points    		= gamipress_get_user_points_awarded_in_loop( $user_id, $points_type_required, $achievement_id );

		if( $awarded_points >= $points_required ) {

			// If the user just earned the achievement, though, don't let them earn it again
			// This prevents an infinite loop if the achievement has no maximum earnings limit
			$minimum_time  	= current_time( 'timestamp' ) - 1;

			if ( $last_activity > $minimum_time ) {
				$return = false;
            }

            // Increase the points awarded of this loop since the achievement will be awarded
            gamipress_update_user_points_awarded_in_loop( $points_required, $points_type_required, $achievement_id );

		} else {
            $return = false;
        }

	} else if( 'points-balance' === $trigger_type ) {

        // Grab our user's points and see if they at least as many as required
        $points_condition       = gamipress_get_post_meta( $achievement_id, '_gamipress_points_condition' );
        $points_required        = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_points_required' ) );
        $points_type_required   = gamipress_get_post_meta( $achievement_id, '_gamipress_points_type_required' );

        // Get user points balance
        $points_balance    		= absint( gamipress_get_user_points( $user_id, $points_type_required ) );

        if( empty( $points_condition ) ) {
            $points_condition = 'greater_or_equal';
        }

        if( gamipress_number_condition_matches( $points_balance, $points_required, $points_condition ) ) {

            // If the user just earned the achievement, though, don't let them earn it again
            // This prevents an infinite loop if the achievement has no maximum earnings limit
            $minimum_time  	= current_time( 'timestamp' ) - 1;
            $last_activity  = absint( gamipress_achievement_last_user_activity( $achievement_id, $user_id ) );

            if ( $last_activity > $minimum_time )
                $return = false;

        } else {
            $return = false;
        }

	} else if( 'gamipress_expend_points' === $trigger_type ) {

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
add_filter( 'user_deserves_achievement', 'gamipress_user_meets_points_requirement', 10, 6 );

/**
 * Check if user meets the rank requirement for a given achievement
 *
 * @since  1.3.1
 *
 * @param  bool     $return         The current status of whether or not the user deserves this achievement
 * @param  int      $user_id        The given user's ID
 * @param  int      $achievement_id The given achievement's post ID
 * @param  string   $trigger_type   The trigger
 * @param  int      $site_id        The triggered site id
 * @param  array    $args           The triggered args
 *
 * @return bool                    Our possibly updated earning status
 */
function gamipress_user_meets_rank_requirement( $return = false, $user_id = 0, $achievement_id = 0, $trigger_type = '', $site_id = 0, $args = array() ) {

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return ) {
        return $return;
    }

    if( empty( $trigger_type ) ) {
        $trigger_type = gamipress_get_post_meta( $achievement_id, '_gamipress_trigger_type' );
    }

	// First, see if the achievement requires a minimum rank
	if (
		'rank' === gamipress_get_post_meta( $achievement_id, '_gamipress_earned_by' ) 				// Check for achievements earned by rank
		|| 'earn-rank' === $trigger_type 	                                                        // Check for requirements with earn rank activity
		|| 'revoke-rank' === $trigger_type 	                                                        // Check for requirements with revoke rank activity
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

			if ( $last_activity > $minimum_time )
				$return = false;

		}
	}

	// Return our eligibility status
	return $return;

}
add_filter( 'user_deserves_achievement', 'gamipress_user_meets_rank_requirement', 10, 6 );

/**
 * Validate whether or not the user has completed all requirements for an achievement
 *
 * @since  1.0.0
 *
 * @param  bool     $return         True if user deserves achievement, false otherwise
 * @param  int      $user_id  		The given user's ID
 * @param  int      $achievement_id The achievement post ID
 * @param  string   $trigger_type   The trigger
 * @param  int      $site_id        The triggered site id
 * @param  array    $args           The triggered args
 *
 * @return bool              		True if user deserves limit requirements, false otherwise
 */
function gamipress_user_deserves_limit_requirements( $return = false, $user_id = 0, $achievement_id = 0, $trigger_type = '', $site_id = 0, $args = array() ) {

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return ) {
        return $return;
    }

    // If we're not working with a requirement, bail here
    if ( ! in_array( gamipress_get_post_type( $achievement_id ), gamipress_get_requirement_types_slugs() ) ) {
        return $return;
    }

    if( empty( $trigger_type ) ) {
        $trigger_type = gamipress_get_post_meta( $achievement_id, '_gamipress_trigger_type' );
    }

	// Check if activity trigger is excluded from this check
	if( in_array( $trigger_type, gamipress_get_activity_triggers_excluded_from_activity_limit() ) ) {
		return $return;
    }

    // Check if is limited over time
    $since = gamipress_get_achievement_limit_timestamp( $achievement_id );

    if( $since > 0 ) {

        // Activity count limited to a timestamp
        $activity_count = absint( gamipress_get_achievement_activity_count( $user_id, $achievement_id, $since ) );

        if ( $activity_count !== 0 ) {

            // Activity count limit over time
            $limit = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_limit' ) );

            // Force bail if user exceeds the limit over time
            if( $activity_count > $limit ) {
                return false;
            }

        }

    }

    // Grab the relevant activity count
    $activity_count = absint( gamipress_get_achievement_activity_count( $user_id, $achievement_id ) );

    if ( $activity_count !== 0 ) {

        // Get the required number of check-ins
        $count = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_count' ) );

        // If exceed the required number of check-ins, then deserve the achievement
        if ( $activity_count < $count ) {
            return false;
        }

    }

	return $return;
}
add_filter( 'user_deserves_achievement', 'gamipress_user_deserves_limit_requirements', 10, 6 );

/**
 * Count the user's relevant actions for a given achievement
 *
 * @since  1.0.0
 *
 * @param  int $user_id 		The given user's ID
 * @param  int $achievement_id 	The given achievement's ID
 * @param  int $since 			Timestamp since retrieve the count
 *
 * @return int          		The total activity count
 */
function gamipress_get_achievement_activity_count( $user_id = 0, $achievement_id = 0, $since = 0 ) {

	// Setup site id
	$site_id = get_current_blog_id();

	// Assume the user has no relevant activities
	$activity_count = 0;

	$post_type = gamipress_get_post_type( $achievement_id );

	if ( in_array( $post_type, gamipress_get_requirement_types_slugs() ) ) {

		// Grab the requirement object
		$requirement = gamipress_get_requirement_object( $achievement_id );

		// Determine which type of trigger we're using and return the corresponding activities
		switch( $requirement['trigger_type'] ) {
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
                $activity_count = gamipress_get_earnings_count( array(
					'user_id'   => absint( $user_id ),
					'post_id'   => absint( $requirement['achievement_post'] ),
					'since'     => $since
				) );
				break;
			case 'any-achievement':
				$trigger = 'gamipress_unlock_' . $requirement['achievement_type'];
				break;
			case 'all-achievements':
				$trigger = 'gamipress_unlock_all_' . $requirement['achievement_type'];
				break;
            case 'revoke-any-achievement':
                $trigger = 'gamipress_revoke_' . $requirement['achievement_type'];
                break;
			default:
				$trigger = $requirement['trigger_type'];
				break;
		}

		// Just continue if trigger is set
		if( isset( $trigger ) ) {

            // If since is not defined check it from new ways
			if( $since === 0 ) {

				if( gamipress_get_post_type( $achievement_id ) === 'rank-requirement' ) {
					// If is a rank requirement, we need to get the last time user earned the latest rank of type
					$rank = gamipress_get_rank_requirement_rank( $achievement_id );

					$since = gamipress_get_rank_earned_time( $user_id, $rank->post_type );
				} else {

                    // Get activity count from last time earned
                    $since = gamipress_achievement_last_user_activity( $achievement_id, $user_id );

                    // If user hasn't earned this yet, then get activity count from publish date
                    if( $since === 0 ) {
                        $parent_id = absint( gamipress_get_post_field( 'post_parent', $achievement_id ) );

                        // Try to get the date from the parent (achievement, rank or points type), if not possible, get it from the requirement
                        if( $parent_id !== 0 ) {
                            $since = strtotime( gamipress_get_post_date( $parent_id ) );
                        } else {
                            $since = strtotime( gamipress_get_post_date( $achievement_id ) );
                        }
                    }

                    if( defined( 'GAMIPRESS_DOING_ACTIVITY_RECOUNT' ) && GAMIPRESS_DOING_ACTIVITY_RECOUNT ) {
                        // Reduce it in 1 if check comes from the recount activity tool
                        $since -= 1;
                    }
				}

			}

            $activity_count = gamipress_get_user_trigger_count( $user_id, $trigger, $since, $site_id, $requirement );

		}

	}

    /**
     * Available filter for overriding user activity count
     *
     * @since  1.0.0
     *
     * @param  int $activity_count  The achievement's activity count
     * @param  int $user_id 		The given user's ID
     * @param  int $achievement_id 	The given achievement's ID
     * @param  int $since 			Timestamp since retrieve the count
     *
     * @return int          		The total activity count
     */
	return absint( apply_filters( "gamipress_{$post_type}_activity_count", $activity_count, $user_id, $achievement_id, $since ) );
}

/**
 * Count the user's relevant actions for a given achievement applying the requirement limits
 *
 * @since  2.0.2
 *
 * @param  int $user_id 		The given user's ID
 * @param  int $achievement_id 	The given achievement's ID
 * @param  int $since 			Timestamp since retrieve the count
 *
 * @return int          		The total activity count
 */
function gamipress_get_achievement_activity_count_limited( $user_id = 0, $achievement_id = 0, $since = 0 ) {

    // Assume the user has no relevant activities
    $activity_count = 0;

    $post_type = gamipress_get_post_type( $achievement_id );

    if ( in_array( $post_type, gamipress_get_requirement_types_slugs() ) ) {

        $limit_type = gamipress_get_post_meta( $achievement_id, '_gamipress_limit_type' );

        if( empty( $limit_type ) ) {
            $limit_type = 'unlimited';
        }

        if( $limit_type === 'unlimited' ) {
            // Times activity has triggered
            $activity_count = absint( gamipress_get_achievement_activity_count( $user_id, $achievement_id, $since ) );
        } else {

            // Grab the requirement object
            $requirement = gamipress_get_requirement_object( $achievement_id );

            $where = array(
                'type' => 'event_trigger',
            );

            // Determine which type of trigger we're using and return the corresponding activities
            switch( $requirement['trigger_type'] ) {
                case 'specific-achievement':
                    $trigger = 'gamipress_unlock_' . $requirement['achievement_type'];
                    $where['post_id'] = absint( $requirement['achievement_post'] );
                    break;
                case 'any-achievement':
                    $trigger = 'gamipress_unlock_' . $requirement['achievement_type'];
                    break;
                case 'all-achievements':
                    $trigger = 'gamipress_unlock_all_' . $requirement['achievement_type'];
                    break;
                case 'revoke-specific-achievement':
                    $trigger = 'gamipress_revoke_' . $requirement['achievement_type'];
                    $where['post_id'] = absint( $requirement['achievement_post'] );
                    break;
                case 'revoke-any-achievement':
                    $trigger = 'gamipress_revoke_' . $requirement['achievement_type'];
                    break;
                default:
                    $trigger = $requirement['trigger_type'];
                    break;
            }

            $where['trigger_type'] = $trigger;
            $group_by = '';

            // Grab the requirement object
            $requirement = gamipress_get_requirement_object( $achievement_id );
            $site_id = get_current_blog_id();

            /**
             * Filter required to get the same where conditions as in the gamipress_get_user_trigger_count() function
             *
             * @see gamipress_get_user_trigger_count()
             */
            $where = apply_filters( 'gamipress_get_user_trigger_count_log_meta', $where, $user_id, $trigger, $since, $site_id, $requirement );

            /**
             * Filter to override the where data to filter the logs count applying the requirement limits
             *
             * @since   2.0.4
             *
             * @param  array    $log_meta       The meta data to filter the logs count
             * @param  int      $user_id        The given user's ID
             * @param  string   $trigger        The given trigger we're checking
             * @param  int      $since 	        The since timestamp where retrieve the logs
             * @param  int      $site_id        The desired Site ID to check
             * @param  array    $args           The triggered args or requirement object
             *
             * @return array                    The where data to filter the logs count
             */
            $where = apply_filters( 'gamipress_get_achievement_activity_count_limited_where', $where, $user_id, $trigger, $since, $site_id, $requirement );

            // If since is not defined check it from new ways
            if( $since === 0 ) {

                if( $post_type === 'rank-requirement' ) {
                    // If is a rank requirement, we need to get the last time user earned the latest rank of type
                    $rank = gamipress_get_rank_requirement_rank( $achievement_id );

                    $since = gamipress_get_rank_earned_time( $user_id, $rank->post_type );
                } else {

                    // Get activity count from last time earned
                    $since = gamipress_achievement_last_user_activity( $achievement_id, $user_id );

                    // If user hasn't earned this yet, then get activity count from publish date
                    if( $since === 0 ) {
                        $since = strtotime( gamipress_get_post_date( $achievement_id ) ) - 1;
                    }

                }

            }

            // Setup the group by clause based on the requirement limit type
            switch( $limit_type ) {
                case 'minutely':
                    $group_by = 'YEAR(l.date), MONTH(l.date), DAY(l.date), HOUR(l.date), MINUTE(l.date)';
                    break;
                case 'hourly':
                    $group_by = 'YEAR(l.date), MONTH(l.date), DAY(l.date), HOUR(l.date)';
                    break;
                case 'daily':
                    $group_by = 'YEAR(l.date), MONTH(l.date), DAY(l.date)';
                    break;
                case 'weekly':
                    $group_by = "YEAR(l.date), DATE_FORMAT( l.date, '%u' )";
                    break;
                case 'monthly':
                    $group_by = 'YEAR(l.date), MONTH(l.date)';
                    break;
                case 'yearly':
                    $group_by = 'YEAR(l.date)';
                    break;
            }

            // Get the user activity counts grouped by date
            $results = gamipress_query_logs( array(
                'select'    => 'LEAST(COUNT(*), ' . $requirement['limit'] . ') AS count', // Least() returns the lowest value (like PHP min() function)
                'user_id'   => $user_id,
                'where'     => $where,
                'since'     => $since,
                'group_by'  => $group_by,
            ) );

            if( is_array( $results ) ) {
                foreach( $results as $result ) {
                    $activity_count += absint( $result->count );
                }
            }

        }

    }

    /**
     * Available filter for overriding user activity count applying the requirement limits
     *
     * @since  1.0.0
     *
     * @param  int $activity_count  The achievement's activity count
     * @param  int $user_id 		The given user's ID
     * @param  int $achievement_id 	The given achievement's ID
     * @param  int $since 			Timestamp since retrieve the count
     *
     * @return int          		The total activity count
     */
    return absint( apply_filters( "gamipress_{$post_type}_activity_count_limited", $activity_count, $user_id, $achievement_id, $since ) );

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

    // No limit
	if( ! $limit_type || $limit_type === 'unlimited' ) {
		return 0;
    }

	$now    = current_time( 'timestamp' );
	$from   = current_time( 'timestamp' );

	$hour   = date( 'H', $now );
	$minute = date( 'i', $now );
	$second = date( 's', $now );
	$day    = date( 'n', $now );
	$month  = date( 'j', $now );
	$year   = date( 'Y', $now );

	switch( $limit_type ) {
        case 'minutely':
            $from = mktime( $hour, $minute - 1, $second, $day, $month, $year );
            break;
        case 'hourly':
            $from = mktime( $hour - 1, $minute, $second, $day, $month, $year );
            break;
		case 'daily':
			$from = mktime( 0, 0, 0, $day, $month, $year );
			break;
		case 'weekly':
			$from = mktime( 0, 0, 0, $day, $month - date( 'N', $now ) + 1 );
			break;
		case 'monthly':
			$from =  mktime( 0, 0, 0, $day, 1, $year );
			break;
		case 'yearly':
			$from =  mktime( 0, 0, 0, 1, 1, $year );
			break;
	}

    /**
     * Available filter to override the start date where an achievement is limiting
     *
     * @since 1.0.0
     *
     * @param int       $from	        The start date where an achievement is limiting
     * @param int       $achievement_id	The achievement ID
     * @param string    $limit_type	    The achievement limit type
     *
     * @return int 					    Timestamp from the limit starts
     */
	return apply_filters( 'gamipress_get_achievement_limit_timestamp', $from, $achievement_id, $limit_type );

}

/* --------------------------------------------------
 * Award Checks
   -------------------------------------------------- */

/**
 * Award an achievement to the user
 *
 * @since  1.0.0
 *
 * @param  int 	    $achievement_id The given achievement ID to award
 * @param  int 	    $user_id        The given user's ID
 * @param  int 	    $admin_id       The given admin's ID
 * @param  string 	$trigger    	The trigger
 * @param  int 	    $site_id        The triggered site id
 * @param  array 	$args           The triggered args
 *
 * @return mixed                   False on not achievement, void otherwise
 */
function gamipress_award_achievement_to_user( $achievement_id = 0, $user_id = 0, $admin_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

	global $wp_filter;

	// Sanity Check: ensure we're working with an achievement or requirement post
	if ( ! ( gamipress_is_achievement( $achievement_id ) || gamipress_is_requirement( $achievement_id ) ) ) {
		return false;
    }

	// Use the current user ID if none specified
	if ( $user_id == 0 ) {
		$user_id = wp_get_current_user()->ID;
    }

	// Get the current site ID none specified
	if ( ! $site_id ) {
		$site_id = get_current_blog_id();
    }

	// Setup our achievement
	$achievement = gamipress_build_achievement_object( $achievement_id );

	// Update user's earned achievements
	gamipress_update_user_achievements( array( 'user_id' => $user_id, 'new_achievements' => array( $achievement ) ) );

	// Log the earning of the award
	gamipress_log_user_achievement_award( $user_id, $achievement_id, $admin_id, $trigger );

	// Available hook for unlocking any achievement of this achievement type
	do_action( 'gamipress_unlock_' . $achievement->post_type, $user_id, $achievement_id, $trigger, $site_id, $args );

	// Patch for WordPress to support recursive actions, specifically for gamipress_award_achievement
	// Because global iteration is fun, assuming we can get this fixed for WordPress 3.9
	$is_recursive_filter = ( current_filter() === 'gamipress_award_achievement');
	$current_key = null;

	// Get current position
	if ( $is_recursive_filter ) {
		$current_key = key( $wp_filter[ 'gamipress_award_achievement' ] );
    }

    /**
     * Available hook to do other things with each awarded achievement
     *
     * @since  1.0.0
     *
     * @param  int 	    $user_id        The given user's ID
     * @param  int 	    $achievement_id The given achievement ID to award
     * @param  string 	$trigger    	The trigger
     * @param  int 	    $site_id        The triggered site id
     * @param  array 	$args           The triggered args
     */
	do_action( 'gamipress_award_achievement', $user_id, $achievement_id, $trigger, $site_id, $args );

	if ( $is_recursive_filter ) {
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
 * @param  int $user_id        The given user's ID
 * @param  int $achievement_id The given achievement's post ID
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

        $trigger_type = gamipress_get_post_meta( $achievement->ID, '_gamipress_trigger_type' );

        // Skip achievements for revoking
        if( in_array( $trigger_type, array( 'revoke-specific-achievement', 'revoke-rank' ) ) ) {
            continue;
        }

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
 * @param  int $user_id        The given user's ID
 * @param  int $achievement_id The given achievement's post ID
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
 * @param  int $user_id        The given user's ID
 * @param  int $achievement_id The given achievement's post ID
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
 * @param  int $user_id        The given user's ID
 * @param  int $achievement_id The given achievement's post ID
 */
function gamipress_maybe_award_multiple_points( $user_id = 0, $achievement_id = 0 ) {

    $post_type = gamipress_get_post_type( $achievement_id );

    // First, see if the requirement requires a minimum amount of points (just requirements has this meta)
    // Note: Rank requirements are excluded from this
    if ( 'earn-points' === gamipress_get_post_meta( $achievement_id, '_gamipress_trigger_type' )
         && ! in_array( $post_type, array( 'rank-requirement' ) ) ) {

        // Grab our user's points and see if they at least as many as required
        $points_required        = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_points_required' ) );
        $points_type_required   = gamipress_get_post_meta( $achievement_id, '_gamipress_points_type_required' );
        $multiple_award_key     = "gamipress_doing_multiple_{$points_type_required}_award";

        // Check if we are in a loop of multiple points to award
        if( ! ( isset( $GLOBALS[$multiple_award_key] ) && $GLOBALS[$multiple_award_key] === true ) ) {

            $last_achievement_activity = absint( gamipress_achievement_last_user_activity( $achievement_id, $user_id ) );

            // Get user points earned since last time has earning the achievement
            $user_last_points = gamipress_get_user_points_awarded_in_loop( $user_id, $points_type_required, $achievement_id );

            if( $user_last_points >= $points_required && $points_required > 0 ) {

                // Starting multiple points award
                $GLOBALS[$multiple_award_key] = true;

                // Set times to award
                $times_to_award = intval( $user_last_points / $points_required );

                // Check the maximum times this requirement could be earned
                $maximum_earnings = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_maximum_earnings' ) );

                // If maximum earnings is different to 0, we need to set how many times to award it
                if( $maximum_earnings !== 0 ) {

                    $earned_times = gamipress_get_earnings_count( array(
                        'user_id'   => absint( $user_id ),
                        'post_id'   => absint( $achievement_id ),
                        'since'     => $last_achievement_activity
                    ) );

                    // If times to award and earned times exceed the maximum earnings
                    if( ( $times_to_award + $earned_times ) >= $maximum_earnings )
                        $times_to_award = ( $maximum_earnings - $earned_times );

                }

                // Award same achievement many times (rules engine will check limited times to earn it)
                for( $i=0; $i < $times_to_award; $i++ ) {
                    gamipress_award_achievement_to_user( $achievement_id, $user_id );

                    // On every loop increase the points awarded
                    gamipress_update_user_points_awarded_in_loop( $points_required, $points_type_required, $achievement_id );
                }

                // Ending multiple points award
                $GLOBALS[$multiple_award_key] = false;

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
 * @param  int $user_id        The given user's ID
 * @param  int $achievement_id The given achievement's post ID
 */
function gamipress_maybe_award_rank( $user_id = 0, $achievement_id = 0 ) {

	if( gamipress_get_post_type( $achievement_id ) !== 'rank-requirement' ) {
		return;
    }

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

        // Skip optional requirements
        if( ( bool ) gamipress_get_post_meta( $requirement->ID, '_gamipress_optional' ) ) {
            continue;
        }

		// Check if rank requirement has been earned
		if( gamipress_get_earnings_count( array(
			'user_id'   => $user_id,
			'post_id'   => $requirement->ID,
			'since'     => strtotime( $rank->post_date )
		) ) === 0 ) {
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
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
    }

	// Setup CT object
	$ct_table = ct_setup_table( 'gamipress_user_earnings' );

    // If earning ID not provide, find it
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

    $earning = false;

    if( $earning_id ) {
        $earning = ct_get_object( $earning_id );
    }

    // If achievement ID provided, check if requirement should get revoked too
    if( $achievement_id !== 0 ) {

        // Setup vars
        $post_type = gamipress_get_post_type( $achievement_id );
        $points_types = gamipress_get_points_types();
        $is_achievement = in_array( $post_type, gamipress_get_achievement_types_slugs() );
        $is_rank = in_array( $post_type, gamipress_get_rank_types_slugs() );

        // If is achievement or rank, revoke also all its requirements
        if ( $is_achievement || $is_rank ) {

            /**
             * Available filter to determine if should revoke requirements when parent element is revoked too
             *
             * @since  	1.8.7
             *
             * @param bool 	$revoke         Revoke confirmation, by default true
             * @param int 	$user_id        The given user ID
             * @param int 	$achievement_id The post ID to revoke
             * @param int 	$earning_id     The user earning ID
             */
            $revoke_requirements = apply_filters( 'gamipress_revoke_requirements_on_revoke_parent', true, $achievement_id, $user_id, $earning_id );

            if( $revoke_requirements ) {

                $requirements = array();

                if ( $is_achievement ) {
                    // Get the achievement steps
                    $requirements = gamipress_get_achievement_steps( $achievement_id );
                } else if ( $is_rank ) {
                    // Get the rank requirements
                    $requirements = gamipress_get_rank_requirements( $achievement_id );
                }

                foreach( $requirements as $requirement ) {
                    gamipress_revoke_achievement_to_user( $requirement->ID, $user_id );
                }

            }

        }

        // If is revoking the current user rank, update user rank with the previous one
        if( $is_rank ) {

            $user_rank_id = gamipress_get_user_rank_id( $user_id, $post_type );

            if( $achievement_id === $user_rank_id ) {

                $prev_rank_id = gamipress_get_prev_rank_id( $user_rank_id );

                $prev_rank_query = new CT_Query( array(
                    'user_id' => $user_id,
                    'post_id' => $prev_rank_id,
                    'items_per_page' => 1
                ) );

                $prev_rank_earnings = $prev_rank_query->get_results();

                // If previous rank is registered in user earnings, remove the action to register it in user earnings
                if( count( $prev_rank_earnings ) > 0 ) {
                    remove_action( 'gamipress_update_user_rank', 'gamipress_register_user_rank_earning', 10 );
                }

                // Update the user rank with the previous one
                gamipress_update_user_rank( $user_id, $prev_rank_id );

                // Restore the removed action
                if( count( $prev_rank_earnings ) > 0 ) {
                    add_action( 'gamipress_update_user_rank', 'gamipress_register_user_rank_earning', 10, 5 );
                }

            }

        }

        if( $earning ) {
            // Get the points and points type from the user earning entry
            $points = absint( $earning->points );
            $points_type = $earning->points_type;
        } else {
            // Get the points and points type from the post provided
            $points = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_points' ) );
            $points_type = gamipress_get_post_meta( $achievement_id, '_gamipress_points_type' );
        }

        if( $points !== 0 ) {

            // Turn the "points-type" post type to award or deduct type depending on the amount of points assigned
            if( $post_type === 'points-type' ) {
                if( $points > 0 ) {
                    $post_type = 'points-award';
                } else {
                    $post_type = 'points-deduct';
                }
            }

            if ( in_array( $post_type, gamipress_get_achievement_types_slugs() ) || $post_type === 'points-award' ) {

                /**
                 * Available filter to determine if should deduct points when an element is revoked
                 *
                 * @since  	1.8.9
                 *
                 * @param bool 	$revoke         Deduct confirmation, by default true
                 * @param int 	$user_id        The given user ID
                 * @param int 	$achievement_id The post ID to revoke (commonly an achievement ID or points award ID)
                 * @param int 	$earning_id     The user earning ID
                 */
                $deduct_points = apply_filters( 'gamipress_deduct_points_on_revoke', true, $achievement_id, $user_id, $earning_id );

                if( $deduct_points ) {

                    if ( ! empty( $points ) && isset( $points_types[$points_type] ) ) {
                        // Revoke points awarded from this achievement or points award
                        gamipress_deduct_points_to_user( $user_id, $points, $points_type, array( 'achievement_id' => $achievement_id ) );
                    }

                }

            }

            if ( $post_type === 'points-deduct' ) {

                /**
                 * Available filter to determine if should award points when an element is revoked
                 *
                 * @since  	1.8.9
                 *
                 * @param bool 	$revoke         Award confirmation, by default true
                 * @param int 	$user_id        The given user ID
                 * @param int 	$achievement_id The post ID to revoke (commonly a points deduct ID)
                 * @param int 	$earning_id     The user earning ID
                 */
                $award_points = apply_filters( 'gamipress_award_points_on_revoke', true, $achievement_id, $user_id, $earning_id );

                if( $award_points ) {

                    if ( ! empty( $points ) && isset( $points_types[$points_type] ) ) {
                        // Restore points deducted from these points deduct
                        gamipress_award_points_to_user( $user_id, $points, $points_type, array( 'achievement_id' => $achievement_id ) );
                    }

                }

            }

        }

        /**
         * Available action per type for triggering other processes
         *
         * @since 2.3.1
         *
         * @param int 	$user_id        The given user's ID
         * @param int 	$achievement_id The given achievement's post ID
         * @param int 	$earning_id     The user's earning ID
         */
        do_action( 'gamipress_revoke_' . $post_type, $user_id, $achievement_id, $earning_id );

    }

    /**
     * Available action for triggering other processes
     *
     * @since 1.4.3
     *
     * @param int 	$user_id        The given user's ID
     * @param int 	$achievement_id The given achievement's post ID
     * @param int 	$earning_id     The user's earning ID
     */
	do_action( 'gamipress_revoke_achievement_to_user', $user_id, $achievement_id, $earning_id );

	if( $earning_id ) {
		$ct_table->db->delete( $earning_id );
	}

    ct_reset_setup_table();

}

/**
 * Award additional achievements to user
 *
 * @since 2.3.1
 *
 * @param  int $user_id        The given user's ID
 * @param  int $achievement_id The given achievement's post ID
 *
 * @return void
 */
function gamipress_maybe_award_additional_achievements_to_user_on_revoke( $user_id = 0, $achievement_id = 0 ) {

    // Get the achievement post type
    $post_type = gamipress_get_post_type( $achievement_id );
    $achievement_types = gamipress_get_achievement_types_slugs();
    $rank_types = gamipress_get_achievement_types_slugs();

    // Get achievements that can be earned from completing this achievement
    $dependent_achievements = gamipress_get_dependent_achievements( $achievement_id );

    // Loop through each dependent achievement and see if it can be awarded
    foreach ( $dependent_achievements as $achievement ) {
        $trigger = '';

        $trigger_type = gamipress_get_post_meta( $achievement->ID, '_gamipress_trigger_type' );

        // Skip achievements for awarding
        if( in_array( $trigger_type, array( 'specific-achievement', 'earn-rank' ) ) ) {
            continue;
        }

        if( in_array( $post_type, $achievement_types ) ) {
            $trigger = 'revoke-specific-achievement';
        } else if( in_array( $post_type, $rank_types ) ) {
            $trigger = 'revoke-rank';
        }

        gamipress_maybe_award_achievement_to_user( $achievement->ID, $user_id, $trigger );
    }

}
add_action( 'gamipress_revoke_achievement_to_user', 'gamipress_maybe_award_additional_achievements_to_user_on_revoke', 10, 2 );