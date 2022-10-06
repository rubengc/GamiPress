<?php
/**
 * Points-related Functions
 *
 * @package     GamiPress\Points_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return a user's points
 *
 * @since   1.0.0
 * @updated 1.6.9 Added $args argument
 *
 * @param  integer  $user_id        The given user's ID
 * @param  string   $points_type    The points type
 * @param  array    $args           Extra arguments
 *
 * @return integer  $user_points    The user's current points
 */
function gamipress_get_user_points( $user_id = 0, $points_type = '', $args = array() ) {

	// Use current user's ID if none specified
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
    }

    $args = wp_parse_args( $args, array(
        'date_query' => array(), // Limit user points balance on a specific date query, accepts before and after params
    ) );

    // Shorthand
    $date_query = $args['date_query'];

    // Retrieve the user points balance from logs based on date query given
    if( ( isset( $date_query['before'] ) && ! empty( $date_query['before'] ) )
        || ( isset( $date_query['after'] ) && ! empty( $date_query['after'] ) ) ) {

        return absint( gamipress_query_logs( array(
            'select' => 'GREATEST( IFNULL( SUM( lm0.meta_value ), 0 ), 0 )',
            'get_var' => true,
            'user_id' => $user_id,
            'where' => array(
                array(
                    'key'       => 'points',
                    'value'     => '0',
                    'compare'   => '!=',
                ),
                'points_type'   => $points_type
            ),
            'date_query' => $date_query
        ) ) );

    }

    // Default points
    $user_meta = '_gamipress_points';

    if( ! empty( $points_type ) ) {
        $user_meta = "_gamipress_{$points_type}_points";
    }

    // Fetch our user's points
    $raw_points = gamipress_get_user_meta( $user_id, $user_meta );

    // Transform points to a non-negative integer (sanely falls back to 0 if empty)
    $user_points = absint( $raw_points );

    /**
     * User points amount filter
     *
     * @param int       $user_points    The user's points (falls back to 0 if empty)
     * @param string    $raw_points     The original points (as fetched from the database)
     * @param int       $user_id        The ID of the user whose points were requested
     * @param string    $user_meta      The user meta field name that holds the user's points
     */
    return apply_filters( 'gamipress_get_user_points', $user_points, $raw_points, $user_id, $user_meta );

}

/**
 * Return a user's points awarded
 *
 * @since 	1.3.7
 * @updated 1.3.9.6 Added $since parameter
 *
 * @param  	integer 	$user_id      			The given user's ID
 * @param  	string 		$points_type   			The points type
 * @param 	integer   	$since
 *
 * @return 	integer 	$user_points_awarded  	The user's current points awarded
 */
function gamipress_get_user_points_awarded( $user_id = 0, $points_type = '', $since = 0 ) {

	// Use current user's ID if none specified
	if ( ! $user_id )
		$user_id = wp_get_current_user()->ID;

	// Default points
	$user_meta = '_gamipress_points_awarded';

	if( ! empty( $points_type ) ) {
		$user_meta = "_gamipress_{$points_type}_points_awarded";
	}

	$points_awarded = gamipress_get_user_meta( $user_id, $user_meta );

	// If user meta not exists or since parameter is defined, recalculate it
	if( empty( $points_awarded ) || $since > 0 ) {

		$points_awarded = 0;

		// Get the user earns and awards from log
		$user_points_logs = gamipress_get_user_logs( $user_id, array(
			'type' => array( 'points_earn', 'points_award' ),
			'points_type' => $points_type,
		), $since );

		ct_setup_table( 'gamipress_logs' );

		// Loop all logs to retrieve the amount of points awarded
		foreach( $user_points_logs as $user_points_log ) {
			$points_awarded += absint( ct_get_object_meta( $user_points_log->log_id, '_gamipress_points', true ) );
		}

		ct_reset_setup_table();

		// If since parameter has been set, return the points awarded since this date without update it
		if( $since > 0 ) {
			return $points_awarded;
		}

		// Finally update the user meta
		gamipress_update_user_meta( $user_id, $user_meta, $points_awarded );

	}

	// Return our user's points awarded as an integer (sanely falls back to 0 if empty)
	return absint( $points_awarded );
}

/**
 * Return a user's points deducted
 *
 * @since 	1.3.7
 * @updated 1.3.9.6 Added $since parameter
 *
 * @param  	integer 	$user_id      			The given user's ID
 * @param  	string 		$points_type   			The points type
 * @param 	integer   	$since
 *
 * @return 	integer 	$user_points_deducted  	The user's current points deducted
 */
function gamipress_get_user_points_deducted( $user_id = 0, $points_type = '', $since = 0 ) {

	// Use current user's ID if none specified
	if ( ! $user_id )
		$user_id = wp_get_current_user()->ID;

	// Default points
	$user_meta = '_gamipress_points_deducted';

	if( ! empty( $points_type ) ) {
		$user_meta = "_gamipress_{$points_type}_points_deducted";
	}

	$points_deducted = gamipress_get_user_meta( $user_id, $user_meta );

	// If user meta not exists or since parameter is defined, recalculate it
	if( empty( $points_deducted ) || $since > 0 ) {

		$points_deducted = 0;

		// Get the user earns and deducts from log
		$user_points_logs = gamipress_get_user_logs( $user_id, array(
			'type' => array( 'points_deduct', 'points_revoke' ),
			'points_type' => $points_type,
		), $since );

		ct_setup_table( 'gamipress_logs' );

		// Loop all logs to retrieve the amount of points deducted
		foreach( $user_points_logs as $user_points_log ) {
			$points_deducted += absint( ct_get_object_meta( $user_points_log->log_id, '_gamipress_points', true ) );
		}

		ct_reset_setup_table();

		// If since parameter has been set, return the points deducted since this date without update it
		if( $since > 0 ) {
			return $points_deducted;
		}

		// Finally update the user meta
		gamipress_update_user_meta( $user_id, $user_meta, $points_deducted );

	}

	// Return our user's points deducted as an integer (sanely falls back to 0 if empty)
	return absint( $points_deducted );
}

/**
 * Return a user's points expended
 *
 * @since 	1.3.7
 * @updated 1.3.9.6 Added $since parameter
 *
 * @param  	integer 	$user_id      			The given user's ID
 * @param  	string 		$points_type   			The points type
 * @param 	integer   	$since
 *
 * @return 	integer 	$user_points_expended  	The user's current points expended
 */
function gamipress_get_user_points_expended( $user_id = 0, $points_type = '', $since = 0 ) {

	// Use current user's ID if none specified
	if ( ! $user_id )
		$user_id = wp_get_current_user()->ID;

	// Default points
	$user_meta = '_gamipress_points_expended';

	if( ! empty( $points_type ) ) {
		$user_meta = "_gamipress_{$points_type}_points_expended";
	}

	$points_expended = gamipress_get_user_meta( $user_id, $user_meta );

	// If user meta not exists or since parameter is defined, recalculate it
	if( empty( $points_expended ) || $since > 0 ) {

		$points_expended = 0;

		// Get the user earns and expends from log
		$user_points_logs = gamipress_get_user_logs( $user_id, array(
			'type' => 'points_expend',
			'points_type' => $points_type,
		), $since );

		ct_setup_table( 'gamipress_logs' );

		// Loop all logs to retrieve the amount of points expended
		foreach( $user_points_logs as $user_points_log ) {
			$points_expended += absint( ct_get_object_meta( $user_points_log->log_id, '_gamipress_points', true ) );
		}

		ct_reset_setup_table();

		// If since parameter has been set, return the points expended since this date without update it
		if( $since > 0 ) {
			return $points_expended;
		}

		// Finally update the user meta
		gamipress_update_user_meta( $user_id, $user_meta, $points_expended );

	}

	// Return our user's points expended as an integer (sanely falls back to 0 if empty)
	return absint( $points_expended );

}

/**
 * Return site's points (sum of all user points)
 *
 * @since   1.5.9
 * @updated 1.6.9 Added $args argument
 *
 * @param  string   $points_type   The points type
 * @param  array    $args           Extra arguments
 *
 * @return int      $site_points  The site's current points
 */
function gamipress_get_site_points( $points_type = '', $args = array() ) {

    $args = wp_parse_args( $args, array(
        'date_query' => array(), // Limit user points balance on a specific date query, accepts before and after params
    ) );

    // Shorthand
    $date_query = $args['date_query'];

    // Retrieve the site points from logs based on date query given
    if( ( isset( $date_query['before'] ) && ! empty( $date_query['before'] ) )
        || ( isset( $date_query['after'] ) && ! empty( $date_query['after'] ) ) ) {

        return absint( gamipress_query_logs( array(
            'select' => 'GREATEST( IFNULL( SUM( lm0.meta_value ), 0 ), 0 )',
            'get_var' => true,
            'where' => array(
                array(
                    'key'       => 'points',
                    'value'     => '0',
                    'compare'   => '!=',
                ),
                'points_type'   => $points_type
            ),
            'date_query' => $date_query
        ) ) );

    }

    // Default points
    $user_meta = '_gamipress_points';

    if( ! empty( $points_type ) ) {
        $user_meta = "_gamipress_{$points_type}_points";
    }

    // Return site's points as an integer (sanely falls back to 0 if empty)
    return absint( gamipress_get_user_meta_sum( $user_meta ) );

}

/**
 * Return site's points awarded (sum of all user points awarded)
 *
 * @since 1.5.9
 *
 * @param  string   $points_type   The points type
 *
 * @return int      $site_points  The site's points awarded
 */
function gamipress_get_site_points_awarded( $points_type = '' ) {

    // Default points
    $user_meta = '_gamipress_points_awarded';

    if( ! empty( $points_type ) ) {
        $user_meta = "_gamipress_{$points_type}_points_awarded";
    }

    // Return site's points awarded as an integer (sanely falls back to 0 if empty)
    return absint( gamipress_get_user_meta_sum( $user_meta ) );

}

/**
 * Return site's points deducted (sum of all user points deducted)
 *
 * @since 1.5.9
 *
 * @param  string   $points_type   The points type
 *
 * @return int      $site_points  The site's points deducted
 */
function gamipress_get_site_points_deducted( $points_type = '' ) {

    // Default points
    $user_meta = '_gamipress_points_deducted';

    if( ! empty( $points_type ) ) {
        $user_meta = "_gamipress_{$points_type}_points_deducted";
    }

    // Return site's points deducted as an integer (sanely falls back to 0 if empty)
    return absint( gamipress_get_user_meta_sum( $user_meta ) );

}

/**
 * Return site's points expended (sum of all user points expended)
 *
 * @since 1.5.9
 *
 * @param  string   $points_type   The points type
 *
 * @return int      $site_points  The site's points expended
 */
function gamipress_get_site_points_expended( $points_type = '' ) {

    // Default points
    $user_meta = '_gamipress_points_expended';

    if( ! empty( $points_type ) ) {
        $user_meta = "_gamipress_{$points_type}_points_expended";
    }

    // Return site's points expended as an integer (sanely falls back to 0 if empty)
    return absint( gamipress_get_user_meta_sum( $user_meta ) );

}

/**
 * Award points to a user
 *
 * @since 1.3.6
 *
 * @param integer 			$user_id 		The given user's ID
 * @param integer 			$points 		The points the user is being awarded
 * @param string|WP_Post 	$points_type 	The points type
 * @param array 			$args			Array of extra arguments
 *
 * @return integer                 The user's updated points total
 */
function gamipress_award_points_to_user( $user_id = 0, $points = 0, $points_type = '', $args = array() ) {

	// Initialize args
	$args = wp_parse_args( $args, array(
		'admin_id' => 0,
		'achievement_id' => null,
		'reason' => '',
		'log_type' => '',
	) );

	// If points are negative, turn them to positive
	if( $points < 0  && $args['admin_id'] === 0 ) {
		$points *= -1;
	}

	// Use current user's ID if none specified
	if ( ! $user_id )
		$user_id = get_current_user_id();

	// If the points type is a WP_Post, then get the slug
	if( is_object( $points_type ) )
		$points_type = $points_type->post_name;

	// Available action for triggering other processes
	do_action( 'gamipress_award_points_to_user', $user_id, $points, $points_type, $args );

	return gamipress_update_user_points( $user_id, $points, $args['admin_id'], $args['achievement_id'], $points_type, $args['reason'], $args['log_type'] );

}

/**
 * Deduct points to a user
 *
 * @since 1.3.6
 *
 * @param integer 			$user_id 		The given user's ID
 * @param integer 			$points 		The points the user is being awarded
 * @param string|WP_Post 	$points_type 	The points type
 * @param array 			$args			Array of extra arguments
 *
 * @return integer                 The user's updated points total
 */
function gamipress_deduct_points_to_user( $user_id = 0, $points = 0, $points_type = '', $args = array() ) {

	// Initialize args
	$args = wp_parse_args( $args, array(
		'admin_id' => 0,
		'achievement_id' => null,
		'reason' => '',
		'log_type' => '',
	) );

	// If points are positive, turn them to negative
	if( $points > 0 && $args['admin_id'] === 0 ) {
		$points *= -1;
	}

	// Use current user's ID if none specified
	if ( ! $user_id )
		$user_id = get_current_user_id();

	// If the points type is a WP_Post, then get the slug
	if( is_object( $points_type ) )
		$points_type = $points_type->post_name;

	// Available action for triggering other processes
	do_action( 'gamipress_deduct_points_to_user', $user_id, $points, $points_type, $args );

	return gamipress_update_user_points( $user_id, $points, $args['admin_id'], $args['achievement_id'], $points_type, $args['reason'], $args['log_type'] );

}

/**
 * Posts a log entry when a user earns points
 *
 * @since  1.0.0
 * @updated 1.3.6 	Added $reason parameter
 * @updated 1.3.7 	Added $og_type parameter
 * @updated 1.3.9.4 Now stores _gamipress_{$points_type}_points for user total and _gamipress_{$points_type}_new_points for last awarded or deducted points
 *
 * @param  integer 			$user_id        	The given user's ID
 * @param  integer 			$new_points     	The new points the user is being awarded/deducted
 * @param  integer 			$admin_id       	If being awarded by an admin, the admin's user ID
 * @param  integer 			$achievement_id 	The achievement that generated the points movement, if applicable
 * @param  string|WP_Post  	$points_type    	The points type
 * @param  string  			$reason    			Custom reason to override default log pattern
 * @param  string  			$log_type    		Log type
 *
 * @return integer                 				The user's updated points total
 */
function gamipress_update_user_points( $user_id = 0, $new_points = 0, $admin_id = 0, $achievement_id = null, $points_type = '', $reason = '', $log_type = '' ) {

	// Use current user's ID if none specified
	if ( ! $user_id )
		$user_id = get_current_user_id();

	// If the points type is a WP_Post, then get the slug
	if( is_object( $points_type ) )
		$points_type = $points_type->post_name;

	// Grab the user's current points
	$current_points = gamipress_get_user_points( $user_id, $points_type );

	// If we're getting an admin ID, $new_points is actually the final total, so subtract the current points
	if ( $admin_id ) {
		$new_points = $new_points - $current_points;
	}

    // Default points meta
    $points_meta = '_gamipress_points';
    $new_points_meta = '_gamipress_new_points';

	// Points meta by type
    if( ! empty( $points_type ) ) {
		$points_meta = "_gamipress_{$points_type}_points";
		$new_points_meta = "_gamipress_{$points_type}_new_points";
    }

	// Update our user's total
	$total_points = max( $current_points + $new_points, 0 );

	// Update user's points total
	gamipress_update_user_meta( $user_id, $points_meta, $total_points );

	// Update a meta as flag to meet how many points has been awarded or deducted
	gamipress_update_user_meta( $user_id, $new_points_meta, $new_points );

	// Available action for triggering other processes
	do_action( 'gamipress_update_user_points', $user_id, $new_points, $total_points, $admin_id, $achievement_id, $points_type, $reason, $log_type );

	// Maybe award some points-based achievements
	foreach ( gamipress_get_points_based_achievements( $points_type ) as $achievement ) {
		gamipress_maybe_award_achievement_to_user( $achievement->ID, $user_id );
	}

	return $total_points;
}

/**
 * Return a user's latest points awarded or deducted
 *
 * @since 1.3.9.4
 *
 * @param  integer 	$user_id      	The given user's ID
 * @param  string 	$points_type   	The points type
 *
 * @return integer $user_points  The user's last updated points
 */
function gamipress_get_last_updated_user_points( $user_id = 0, $points_type = '' ) {

	// Use current user's ID if none specified
	if ( ! $user_id )
		$user_id = wp_get_current_user()->ID;

	// Default points
	$user_meta = '_gamipress_new_points';

	if( ! empty( $points_type ) ) {
		$user_meta = "_gamipress_{$points_type}_new_points";
	}

	// Return our user's latest points as an integer (sanely falls back to 0 if empty)
	return absint( gamipress_get_user_meta( $user_id, $user_meta ) );
}

/**
 * Update user points awarded
 *
 * @since 1.3.7
 *
 * @param integer 			$user_id 		The given user's ID
 * @param integer 			$points 		The points the user is being awarded
 * @param string|WP_Post 	$points_type 	The points type
 * @param array 			$args			Array of extra arguments
 *
 * @return integer                 			The user's updated points awarded
 */
function gamipress_update_user_points_awarded( $user_id = 0, $points = 0, $points_type = '', $args = array() ) {

	// If points are negative, turn them to positive
	if( $points < 0 )
		$points *= -1;

	$current_points = gamipress_get_user_points_awarded( $user_id, $points_type );

	// Default points
	$user_meta = '_gamipress_points_awarded';

	if( ! empty( $points_type ) )
		$user_meta = "_gamipress_{$points_type}_points_awarded";

	// Update our user's total
	$total_points = $current_points + $points;
	gamipress_update_user_meta( $user_id, $user_meta, $total_points );

	return $total_points;

}

/**
 * Used on rules engine, this function returns the points amount that user has earned subtracting the used on the different checks
 *
 * @since   1.7.6.3
 * @updated 1.8.1   Force function to correctly pass the date query to gamipress_get_user_points() function
 *
 * @param  int 	    $user_id      	    The given user's ID
 * @param  string 	$points_type   	    The points type
 * @param  int 	    $requirement_id   	The requirement ID that is already in the loop
 *
 * @return integer $user_points  The user's points on the current rules engine loop
 */
function gamipress_get_user_points_awarded_in_loop( $user_id = 0, $points_type = '', $requirement_id = 0 ) {

    $multiple_awarded_key   = "gamipress_multiple_{$points_type}_awarded";
    $last_points_key        = "gamipress_{$points_type}_last_points";

    // Initialize the last points earned array
    if( ! isset( $GLOBALS[$last_points_key] ) )
        $GLOBALS[$last_points_key] = array();

    $requirement_key = $requirement_id;

    // Special condition for ranks that all rank requirements follow the earn points event
    if( gamipress_get_post_type( $requirement_id ) === 'rank-requirement' ) {

        // Set as requirement key the rank type instead
        $rank_id = gamipress_get_post_field( 'post_parent', $requirement_id );
        $requirement_key = gamipress_get_post_type( $rank_id );

    }

    if( ! isset( $GLOBALS[$last_points_key][$requirement_key] ) ) {
        // Initialize the last points earned for this requirement

        // Get the last activity of the current in use achievement
        $last_activity = absint( gamipress_achievement_last_user_activity( $requirement_id, $user_id ) );

        // If user hasn't earned this yet, then get activity count from publish date
        if( $last_activity === 0 ) {
            $last_activity = strtotime( gamipress_get_post_date( $requirement_id ) );
        }

        $args = array(
            'date_query' => array(
                'after' => date( 'Y-m-d H:i:s', $last_activity )
            )
        );

        // Get user points earned since last time has earning the achievement
        $GLOBALS[$last_points_key][$requirement_key] = gamipress_get_user_points( $user_id, $points_type, $args );
    }

    // Initialize the awarded points array
    if( ! isset( $GLOBALS[$multiple_awarded_key] ) ) {
        $GLOBALS[$multiple_awarded_key] = array();
    }

    // Initialize the awarded points count for this requirement
    if( ! isset( $GLOBALS[$multiple_awarded_key][$requirement_key] ) ) {
        $GLOBALS[$multiple_awarded_key][$requirement_key] = 0;
    }

    return ( $GLOBALS[$last_points_key][$requirement_key] - $GLOBALS[$multiple_awarded_key][$requirement_key] );

}

/**
 * Used on rules engine, update the points amount used on the different checks
 *
 * @since 1.7.6.3
 *
 * @param  int 	    $new_points         New points to apply to the already checked amoount
 * @param  string 	$points_type   	    The points type
 * @param  int 	    $requirement_id   	The requirement ID that is already in the loop
 *
 * @return integer $user_points  The user's points on the current rules engine loop
 */
function gamipress_update_user_points_awarded_in_loop( $new_points = 0, $points_type = '', $requirement_id = 0 ) {

    $multiple_awarded_key = "gamipress_multiple_{$points_type}_awarded";

    // Initialize the awarded points array
    if( ! isset( $GLOBALS[$multiple_awarded_key] ) )
        $GLOBALS[$multiple_awarded_key] = array();

    $requirement_key = $requirement_id;

    // Special condition for ranks that all rank requiements follow the earn points event
    if( gamipress_get_post_type( $requirement_id ) === 'rank-requirement' ) {

        // Set as requirement key the rank type instead
        $rank_id = gamipress_get_post_field( 'post_parent', $requirement_id );
        $requirement_key = gamipress_get_post_type( $rank_id );

    }

    // Initialize the awarded points count for this requirement
    if( ! isset( $GLOBALS[$multiple_awarded_key][$requirement_key] ) )
        $GLOBALS[$multiple_awarded_key][$requirement_key] = 0;

    $GLOBALS[$multiple_awarded_key][$requirement_key] += $new_points;

    return $GLOBALS[$multiple_awarded_key][$requirement_key];

}

/**
 * Update user points deducted
 *
 * @since 1.3.7
 *
 * @param integer 			$user_id 		The given user's ID
 * @param integer 			$points 		The points the user is being deducted
 * @param string|WP_Post 	$points_type 	The points type
 * @param array 			$args			Array of extra arguments
 *
 * @return integer                 			The user's updated points deducted
 */
function gamipress_update_user_points_deducted( $user_id = 0, $points = 0, $points_type = '', $args = array() ) {

	// If points are negative, turn them to positive
	if( $points < 0 ) {
		$points *= -1;
	}

	$current_points = gamipress_get_user_points_deducted( $user_id, $points_type );

	// Default points
	$user_meta = '_gamipress_points_deducted';

	if( ! empty( $points_type ) ) {
		$user_meta = "_gamipress_{$points_type}_points_deducted";
	}

	// Update our user's total
	$total_points = $current_points + $points;
	gamipress_update_user_meta( $user_id, $user_meta, $total_points );

	return $total_points;

}

/**
 * Update user points expended
 *
 * @since 1.3.7
 *
 * @param integer 			$user_id 		The given user's ID
 * @param integer 			$points 		The points the user is being expended
 * @param string|WP_Post 	$points_type 	The points type
 * @param array 			$args			Array of extra arguments
 *
 * @return integer                 			The user's updated points expended
 */
function gamipress_update_user_points_expended( $user_id = 0, $points = 0, $points_type = '', $args = array() ) {

	// If points are negative, turn them to positive
	if( $points < 0 ) {
		$points *= -1;
	}

	$current_points = gamipress_get_user_points_expended( $user_id, $points_type );

	// Default points
	$user_meta = '_gamipress_points_expended';

	if( ! empty( $points_type ) ) {
		$user_meta = "_gamipress_{$points_type}_points_expended";
	}

	// Update our user's total
	$total_points = $current_points + $points;
	gamipress_update_user_meta( $user_id, $user_meta, $total_points );

	return $total_points;

}

/**
 * Log a user's updated points
 *
 * @since 	1.0.0
 * @updated 1.3.6 Support for deduct/revoke points
 *
 * @param integer $user_id        The user ID
 * @param integer $new_points     Points added/deducted to the user's total
 * @param integer $total_points   The user's updated total points
 * @param integer $admin_id       An admin ID (if admin-awarded)
 * @param integer $achievement_id The associated achievement ID
 * @param string  $points_type    The points type
 * @param string  $reason         Custom reason to override default log pattern
 * @param string  $log_type       Custom log type
 */
function gamipress_log_user_points( $user_id, $new_points, $total_points, $admin_id, $achievement_id, $points_type = '', $reason = '', $log_type = '' ) {

    $log_meta = array(
        'achievement_id' => $achievement_id,
        'points' => $new_points,
        'points_type' => $points_type,
        'total_points' => number_format( $total_points ),
    );

    $access = 'public';

	if( ! empty( $log_type ) ) {
		$type = $log_type;

		if ( $admin_id ) {
			$access = 'private';
			$log_meta['admin_id'] = $admin_id;
		}
	} else {

		// Alter our log pattern if this was an admin action
		if ( $admin_id ) {

			$access = 'private';
			$log_meta['admin_id'] = $admin_id;

			if( $new_points > 0 ) {
				// Points awarded
				$type = 'points_award';
				$log_meta['pattern'] = gamipress_get_option( 'points_awarded_log_pattern', __( '{admin} awarded {user} {points} {points_type} for a new total of {total_points} {points_type}', 'gamipress' ) );
			} else {
				// Points revoked
				$type = 'points_revoke';
				$log_meta['pattern'] = gamipress_get_option( 'points_revoked_log_pattern', __( '{admin} revoked {user} {points} {points_type} for a new total of {total_points} {points_type}', 'gamipress' ) );
			}

		} else {

			if( $new_points > 0 ) {
				// Points earned
				$type = 'points_earn';
				$log_meta['pattern'] = gamipress_get_option( 'points_earned_log_pattern', __( '{user} earned {points} {points_type} for a new total of {total_points} {points_type}', 'gamipress' ) );
			} else {
				// Points deducted
				$type = 'points_deduct';
				$log_meta['pattern'] = gamipress_get_option( 'points_deducted_log_pattern', __( '{user} deducted {points} {points_type} for a new total of {total_points} {points_type}', 'gamipress' ) );
			}

		}
    }

	if( ! empty( $reason ) ) {
		$log_meta['pattern'] = $reason;
	}

	// Update user counts
	if( $type === 'points_award' || $type === 'points_earn' ) {
		gamipress_update_user_points_awarded( $user_id, $new_points, $points_type );
	}

	if( $type === 'points_revoke' || $type === 'points_deduct' ) {
		gamipress_update_user_points_deducted( $user_id, $new_points, $points_type );
	}

	if( $type === 'points_expend' ) {
		gamipress_update_user_points_expended( $user_id, $new_points, $points_type );
	}

	// Decide trigger type value based on log type
	switch( $type ) {
		case 'points_earn':
			$trigger = 'gamipress_earn_points';
			break;
		case 'points_deduct':
			$trigger = 'gamipress_deduct_points';
			break;
		case 'points_award':
			$trigger = 'gamipress_award_points';
			break;
		case 'points_revoke':
			$trigger = 'gamipress_revoke_points';
			break;
		default:
			$trigger = __( '(no trigger)', 'gamipress' );
			break;
	}

	// Create the log entry
	gamipress_insert_log( $type, $user_id, $access, $trigger, $log_meta );

}
add_action( 'gamipress_update_user_points', 'gamipress_log_user_points', 10, 8 );

/**
 * Get Points Type Points Awards
 *
 * @since  	1.0.0
 * @updated 1.4.6 Added $post_status parameter
 *
 * @param integer|string 	$points_type 	The points type's post ID or the points type slug
 * @param string 			$post_status 	The points awards status (publish by default)
 *
 * @return array|bool                  		Array of WP_Post of the points awards, or false if none
 */
function gamipress_get_points_type_points_awards( $points_type = 0, $post_status = 'publish' ) {

	// Try to find the points type by slug
	if( ! is_numeric( $points_type ) && ! empty( $points_type ) ) {
		$points_types = gamipress_get_points_types();

		if( isset( $points_types[$points_type] ) ) {
			$points_type = $points_types[$points_type]['ID'];
		}
	}

	// Grab the current post ID if no points_type_id was specified
	if ( ! $points_type ) {
		global $post;
		$points_type = $post->ID;
	}

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.5.1' ) ) {
        // gamipress_get_assigned_requirements() has backward compatibility and merge new and old results
        return gamipress_get_assigned_requirements( $points_type, 'points-award', $post_status );
    }

    $cache = gamipress_get_cache( 'points_type_points_awards', array(), false );

    // If result already cached, return it
    if( isset( $cache[$points_type] ) && isset( $cache[$points_type][$post_status] ) ) {
        return $cache[$points_type][$post_status];
    }

	$points_awards = get_posts( array(
		'post_type'         => 'points-award',
		'post_parent'     	=> $points_type,
		'post_status'       => $post_status,
		'orderby'			=> 'menu_order',
		'order'				=> 'ASC',
		'posts_per_page'    => -1,
		'suppress_filters'  => false,
	) );

    // Cache function result
    if( ! isset( $cache[$points_type] ) ){
        $cache[$points_type] = array();
    }

    $cache[$points_type][$post_status] = $points_awards;

    gamipress_set_cache( 'points_type_points_awards', $cache );

	// If it has a points type, return it, otherwise return false
	if ( ! empty( $points_awards ) )
		return $points_awards;
	else
		return false;

}

/**
 * Get Points Award Points Type
 *
 * @since  1.0.0
 *
 * @param  integer     $points_award_id The given points award's post ID
 * @return object|bool                 The post object of the points type, or false if none
 */
function gamipress_get_points_award_points_type( $points_award_id = 0 ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.5.1' ) ) {
        return gamipress_get_points_award_points_type_old( $points_award_id );
    }

	// Grab the current post ID if no points_award_id was specified
	if ( ! $points_award_id ) {
		global $post;
		$points_award_id = $post->ID;
	}

	// The points award's points type is the post parent
	$points_type_id = absint( gamipress_get_post_field( 'post_parent', $points_award_id ) );

	if( $points_type_id !== 0 ) {
		// If has parent, return his post object
		return gamipress_get_post( $points_type_id );
	} else {
		return false;
	}

}

/**
 * Get Points Type Points Deductions
 *
 * @since  	1.3.7
 * @updated 1.4.6 Added $post_status parameter
 *
 * @param integer|string 	$points_type 	The points type's post ID or the points type slug
 * @param string 			$post_status 	The points deducts status (publish by default)
 *
 *
 * @return array|bool                  		Array of WP_Post of the points deducts, or false if none
 */
function gamipress_get_points_type_points_deducts( $points_type = 0, $post_status = 'publish' ) {

	// Try to find the points type by slug
	if( ! is_numeric( $points_type ) && ! empty( $points_type ) ) {
		$points_types = gamipress_get_points_types();

		if( isset( $points_types[$points_type] ) ) {
			$points_type = $points_types[$points_type]['ID'];
		}
	}

	// Grab the current post ID if no points_type_id was specified
	if ( ! $points_type ) {
		global $post;
		$points_type = $post->ID;
	}

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.5.1' ) ) {
        // gamipress_get_assigned_requirements() has backward compatibility and merge new and old results
        return gamipress_get_assigned_requirements( $points_type, 'points-deduct', $post_status );
    }

    $cache = gamipress_get_cache( 'points_type_points_deducts', array(), false );

    // If result already cached, return it
    if( isset( $cache[$points_type] ) && isset( $cache[$points_type][$post_status] ) ) {
        return $cache[$points_type][$post_status];
    }

	$points_deducts = get_posts( array(
		'post_type'         => 'points-deduct',
		'post_parent'     	=> $points_type,
		'post_status'       => $post_status,
		'orderby'			=> 'menu_order',
		'order'				=> 'ASC',
		'posts_per_page'    => -1,
		'suppress_filters'  => false,
	) );

    // Cache function result
    if( ! isset( $cache[$points_type] ) ){
        $cache[$points_type] = array();
    }

    $cache[$points_type][$post_status] = $points_deducts;

    gamipress_set_cache( 'points_type_points_deducts', $cache );

	// If it has a points type, return it, otherwise return false
	if ( ! empty( $points_deducts ) )
		return $points_deducts;
	else
		return false;

}

/**
 * Get Points Deduction Points Type
 *
 * @since  1.3.7
 *
 * @param  integer     $points_deduct_id The given points deduct's post ID
 * @return object|bool                 The post object of the points type, or false if none
 */
function gamipress_get_points_deduct_points_type( $points_deduct_id = 0 ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.5.1' ) ) {
        return gamipress_get_points_deduct_points_type_old( $points_deduct_id );
    }

    // Grab the current post ID if no points_award_id was specified
    if ( ! $points_deduct_id ) {
        global $post;
        $points_deduct_id = $post->ID;
    }

    // The points award's points type is the post parent
    $points_type_id = absint( gamipress_get_post_field( 'post_parent', $points_deduct_id ) );

    if( $points_type_id !== 0 ) {
        // If has parent, return his post object
        return get_post( $points_type_id );
    } else {
        return false;
    }

}

/**
 * Update all dependent data if points type name has changed.
 *
 * @since  1.0.0
 *
 * @param  array $data      Post data.
 * @param  array $post_args Post args.
 * @return array            Updated post data.
 */
function gamipress_maybe_update_points_type( $data = array(), $post_args = array() ) {

	// Bail if not is main site, on network wide installs points are just available on main site
	if( gamipress_is_network_wide_active() && ! is_main_site() ) {
		return $data;
	}

	// Bail if not is a points type
	if( $post_args['post_type'] !== 'points-type') {
		return $data;
	}

	// If user set an empty slug, then generate it
	if( empty( $post_args['post_name'] ) ) {
		$post_args['post_name'] = wp_unique_post_slug(
			sanitize_title( $post_args['post_title'] ),
			$post_args['ID'],
			$post_args['post_status'],
			$post_args['post_type'],
			$post_args['post_parent']
		);
	}

	// Sanitize post_name
	$post_args['post_name'] = gamipress_sanitize_slug( $post_args['post_name'] );

	if ( gamipress_points_type_changed( $post_args ) ) {

		$original_type = get_post( $post_args['ID'] )->post_name;
		$new_type = $post_args['post_name'];

		$data['post_name'] = gamipress_update_points_types( $original_type, $new_type );

		add_filter( 'redirect_post_location', 'gamipress_points_type_rename_redirect', 99 );

	}

	return $data;
}
add_filter( 'wp_insert_post_data' , 'gamipress_maybe_update_points_type' , 99, 2 );

/**
 * Check if a points type name has changed.
 *
 * @since  1.0.0
 *
 * @param  array $post_args Post args.
 * @return bool             True if name has changed, otherwise false.
 */
function gamipress_points_type_changed( $post_args = array() ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	}

	$original_post = ( !empty( $post_args['ID'] ) && isset( $post_args['ID'] ) ) ? get_post( $post_args['ID'] ) : null;
	$status = false;
	if ( is_object( $original_post ) ) {
		if (
			'points-type' === $post_args['post_type']
			&& $original_post->post_status !== 'auto-draft'
			&& ! empty( $original_post->post_name )
			&& $original_post->post_name !== $post_args['post_name']
		) {
			$status = true;
		}
	}

	return $status;
}

/**
 * Replace all instances of one points type with another.
 *
 * @since  1.0.0
 *
 * @param  string $original_type Original points type.
 * @param  string $new_type      New points type.
 * @return string                New points type.
 */
function gamipress_update_points_types( $original_type = '', $new_type = '' ) {

	// Sanity check to prevent alterating core posts
	if ( empty( $original_type ) || in_array( $original_type, array( 'post', 'page', 'attachment', 'revision', 'nav_menu_item' ) ) ) {
		return $new_type;
	}

    gamipress_update_post_meta_points_type( $original_type, $new_type );
	gamipress_update_user_meta_points_types( $original_type, $new_type );
    gamipress_update_logs_metas_points_types( $original_type, $new_type );
    gamipress_update_user_earnings_points_types( $original_type, $new_type );
	gamipress_flush_rewrite_rules();

	return $new_type;
}

/**
 * Replace all posts metas with old points type with the new one.
 *
 * @since  1.2.7
 *
 * @param  string 	            $original_type  Original points type.
 * @param  string 	            $new_type       New points type.
 *
 * @return array|object|null                    Post metas updated.
 */
function gamipress_update_post_meta_points_type( $original_type = '', $new_type = '' ) {

	global $wpdb;

	$postmeta = GamiPress()->db->postmeta;

	return $wpdb->get_results( $wpdb->prepare(
		"UPDATE {$postmeta}
		SET meta_value = %s
		WHERE meta_key = %s
		AND meta_value = %s",
		$new_type,
		"_gamipress_points_type",
        $original_type
	) );

}

/**
 * Replace all user metas with old points type with the new one.
 *
 * @since  1.0.0
 *
 * @param  string 	            $original_type  Original points type.
 * @param  string 	            $new_type       New points type.
 *
 * @return array|object|null                    User metas updated.
 */
function gamipress_update_user_meta_points_types( $original_type = '', $new_type = '' ) {

    global $wpdb;

    return $wpdb->get_results( $wpdb->prepare(
        "UPDATE {$wpdb->usermeta}
		SET meta_key = %s
		WHERE meta_key = %s",
        "_gamipress_{$new_type}_points",
        "_gamipress_{$original_type}_points"
    ) );

}

/**
 * Replace all logs metas with old points type with the new one.
 *
 * @since  1.7.5
 *
 * @param  string 	            $original_type  Original points type.
 * @param  string 	            $new_type       New points type.
 *
 * @return array|object|null                    Logs metas updated.
 */
function gamipress_update_logs_metas_points_types( $original_type = '', $new_type = '' ) {

    global $wpdb;

    $logs_meta = GamiPress()->db->logs_meta;

    $result = $wpdb->get_results( $wpdb->prepare(
        "UPDATE {$logs_meta}
		SET meta_value = %s
		WHERE meta_key = %s
		AND meta_value = %s",
        $new_type,
        "_gamipress_points_type",
        $original_type
    ) );

    return $result;

}

/**
 * Replace all user earnings with old points type with the new one.
 *
 * @since  1.7.5
 *
 * @param  string 	            $original_type  Original points type.
 * @param  string 	            $new_type       New points type.
 *
 * @return array|object|null                    User earnings updated.
 */
function gamipress_update_user_earnings_points_types( $original_type = '', $new_type = '' ) {

    global $wpdb;

    $user_earnings = GamiPress()->db->user_earnings;

    $result = $wpdb->get_results( $wpdb->prepare(
        "UPDATE {$user_earnings}
		SET points_type = %s
		WHERE points_type = %s",
        $new_type,
        $original_type
    ) );

    return $result;

}

/**
 * Redirect to include custom rename message.
 *
 * @since  1.0.0
 *
 * @param  string $location Original URI.
 * @return string           Updated URI.
 */
function gamipress_points_type_rename_redirect( $location = '' ) {

	remove_filter( 'redirect_post_location', __FUNCTION__, 99 );

	return add_query_arg( 'message', 99, $location );

}

/**
 * Filter the "post updated" messages to include support for points types.
 *
 * @since 1.0.0
 *
 * @param array $messages Array of messages to display.
 *
 * @return array $messages Compiled list of messages.
 */
function gamipress_points_type_update_messages( $messages ) {

	$messages['points-type'] = array_fill( 1, 10, __( 'Points Type saved successfully.', 'gamipress' ) );
	$messages['points-type']['99'] = sprintf( __('Points Type renamed successfully. <p>All user points of this type have been updated <strong>automatically</strong>.</p> All shortcodes, %s, and URIs that reference the old points type slug must be updated <strong>manually</strong>.', 'gamipress'), '<a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">' . __( 'widgets', 'gamipress' ) . '</a>' );

	return $messages;

}
add_filter( 'post_updated_messages', 'gamipress_points_type_update_messages' );

/**
 * Helper function to retrieve a points post thumbnail
 *
 * @since  1.3.7
 *
 * @param  integer|string 	$points_type 	The points type's post ID or slug
 * @param  string  			$image_size 	The name of a registered custom image size
 * @param  string  			$class      	A custom class to use for the image tag
 *
 * @return string              Our formatted image tag
 */
function gamipress_get_points_type_thumbnail( $points_type = '', $image_size = 'gamipress-points', $class = 'gamipress-points-thumbnail' ) {

	if( gettype( $points_type ) === 'integer' ) {
		$post_id = $points_type;
	} else if( absint( $points_type ) !== 0 ) {
        $post_id = $points_type;
	} else {
		$points_types = gamipress_get_points_types();

		if( ! isset( $points_types[$points_type] ) ) {
			return '';
		}

		$post_id = $points_types[$points_type]['ID'];
	}

	// Return our image tag with custom size
	return get_the_post_thumbnail( $post_id, $image_size, array( 'class' => $class ) );

}
