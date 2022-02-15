<?php
/**
 * Achievements Functions
 *
 * @package     GamiPress\Achievements_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Check if post is a registered GamiPress achievement.
 *
 * @since  1.0.0
 *
 * @param  object|int $post Post object or ID.
 * @return bool             True if post is an achievement, otherwise false.
 */
function gamipress_is_achievement( $post = null ) {

	// Assume we are working with an achievement object
	$return = true;

	// If post type is NOT a registered achievement type, it cannot be an achievement
	if ( ! in_array( gamipress_get_post_type( $post ), gamipress_get_achievement_types_slugs() ) ) {
		$return = false;
	}

	// If we pass both previous tests, this is a valid achievement (with filter to override)
	return apply_filters( 'gamipress_is_achievement', $return, $post );

}

/**
 * Get an array of achievements
 *
 * @since  1.0.0
 * @param  array $args An array of our relevant arguments
 * @return array       An array of the queried achievements
 */
function gamipress_get_achievements( $args = array() ) {

	// If not properly upgrade to required version fallback to compatibility function
	if( ! is_gamipress_upgraded_to( '1.5.1' ) ) {
		return gamipress_get_achievements_old( $args );
	}

	// Setup our defaults
	$defaults = array(
		'post_type'                => array_merge( gamipress_get_achievement_types_slugs(), gamipress_get_requirement_types_slugs() ),
		'numberposts'			   => -1,
		'suppress_filters'         => false,
		'achievement_relationship' => 'any',
	);

	$args = wp_parse_args( $args, $defaults );

    // Since 1.5.1, requirements has their parent stored in the post_parent field, so it isn't required at all
	if ( isset( $args['parent_of'] ) ) {

		$post_parent = absint( gamipress_get_post_field( 'post_parent', $args['parent_of'] ) );

        // Bail if achievement hasn't any parent
		if( $post_parent === 0 )
			return array();

        $args['post__in'] = array( $post_parent );

	}

	// Since 1.5.1, requirements has their parent stored in the post_parent field, so it isn't required at all
	if ( isset( $args['children_of'] ) ) {

        $args['post_parent']    = $args['children_of'];

        // When looking to get achievement steps, order is important to sequential steps
        $args['orderby']        = 'menu_order';
        $args['order']          = 'ASC';

	}

	// Get our achievement posts
	$achievements = get_posts( $args );

	return $achievements;

}

/**
 * Get an achievement's parent posts
 *
 * @since  1.0.0
 *
 * @param  integer     $achievement_id The given achievement's post ID
 *
 * @return object|bool                 The post object of the achievement's parent, or false if none
 */
function gamipress_get_parent_of_achievement( $achievement_id = 0 ) {

	// Grab the current post ID if no achievement_id was specified
	if ( ! $achievement_id ) {
		global $post;
		$achievement_id = $post->ID;
	}

	// Grab our achievement's parent
	$parents = gamipress_get_achievements( array( 'parent_of' => $achievement_id ) );

	// If it has a parent, return it, otherwise return false
	if ( ! empty( $parents ) )
		return $parents[0];
	else
		return false;

}

/**
 * Get an achievement's children posts
 *
 * @since  1.0.0
 *
 * @param  integer $achievement_id The given achievement's post ID
 *
 * @return array                   An array of our achievement's children (empty if none)
 */
function gamipress_get_children_of_achievement( $achievement_id = 0 ) {

	// Grab the current post ID if no achievement_id was specified
	if ( ! $achievement_id ) {
		global $post;
		$achievement_id = $post->ID;
	}

	// Grab and return our achievement's children
	return gamipress_get_achievements( array( 'children_of' => $achievement_id, 'achievement_relationship' => 'required' ) );

}

/**
 * Get step's achievement
 *
 * @since  1.5.1
 *
 * @param  integer     $step_id 	The given step's post ID
 *
 * @return object|bool              The post object of the achievement, or false if none
 */
function gamipress_get_step_achievement( $step_id = 0 ) {

	// If not properly upgrade to required version fallback to compatibility function
	if( ! is_gamipress_upgraded_to( '1.5.1' ) ) {

		$achievement = gamipress_get_parent_of_achievement( $step_id );

		if( $achievement ) {
			return $achievement;
		} else {
			return false;
		}

	}

	// Grab the current post ID if no step ID was specified
	if ( ! $step_id ) {
		global $post;
		$step_id = $post->ID;
	}

	// The step's achievement is the post parent
	$achievement_id = absint( gamipress_get_post_field( 'post_parent', $step_id ) );

	if( $achievement_id !== 0 ) {
		// If has parent, return his post object
		return gamipress_get_post( $achievement_id );
	} else {
		return false;
	}

}

/**
 * Get achievement steps
 *
 * @since 1.5.1
 *
 * @param integer   $achievement_id The given achievement requirement's post ID
 * @param string 	$post_status 	The steps status (publish by default)
 *
 * @return array                    An array of post objects with the achievement's steps
 */
function gamipress_get_achievement_steps( $achievement_id = 0, $post_status = 'publish' ) {

	// If not properly upgrade to required version fallback to compatibility function
	if( ! is_gamipress_upgraded_to( '1.5.1' ) ) {
		// gamipress_get_assigned_requirements() has backward compatibility and merge new and old results
		return gamipress_get_assigned_requirements( $achievement_id, 'step', $post_status );
	}

    // Grab the current post ID if no achievement_id was specified
    if ( ! $achievement_id ) {
        global $post;
        $achievement_id = $post->ID;
    }

    $cache = gamipress_get_cache( 'achievement_steps', array(), false );

    // If result already cached, return it
    if( isset( $cache[$achievement_id] ) && isset( $cache[$achievement_id][$post_status] ) ) {
        return $cache[$achievement_id][$post_status];
    }

    $steps = get_posts( array(
        'post_type'         => 'step',
        'post_parent'     	=> $achievement_id,
        'post_status'       => $post_status,
        'orderby'			=> 'menu_order',
        'order'				=> 'ASC',
        'posts_per_page'    => -1,
        'suppress_filters'  => false,
    ));

    // Cache function result
    if( ! isset( $cache[$achievement_id] ) ){
        $cache[$achievement_id] = array();
    }

    $cache[$achievement_id][$post_status] = $steps;

    gamipress_set_cache( 'achievement_steps', $cache );

    // Return steps array
    return $steps;

}

/**
 * Check if the achievement's child achievements must be earned sequentially
 *
 * @since  1.0.0
 *
 * @param  integer $achievement_id The given achievement's post ID
 *
 * @return bool                    True if steps are sequential, false otherwise
 */
function gamipress_is_achievement_sequential( $achievement_id = 0 ) {

	// Grab the current post ID if no achievement_id was specified
	if ( ! $achievement_id ) {
		global $post;
		$achievement_id = $post->ID;
	}

	// If our achievement requires sequential steps, return true, otherwise false
	if ( gamipress_get_post_meta( $achievement_id, '_gamipress_sequential' ) )
		return true;
	else
		return false;
}

/**
 * Check if user has already earned an achievement the maximum number of times
 *
 * @since  1.0.0
 *
 * @param  integer $user_id        The given user's ID
 * @param  integer $achievement_id The given achievement's post ID
 *
 * @return bool                    True if we've exceed the max possible earnings, false if we're still eligable
 */
function gamipress_achievement_user_exceeded_max_earnings( $user_id = 0, $achievement_id = 0 ) {

    // Sanitize vars
    $user_id = absint( $user_id );
    $achievement_id = absint( $achievement_id );

    // Global max earnings
	$global_max_earnings = gamipress_get_post_meta( $achievement_id, '_gamipress_global_maximum_earnings' );

	// -1, 0 or empty means unlimited earnings
    if( $global_max_earnings === '-1' || $global_max_earnings === '0' || empty( $global_max_earnings ) ) {
        $global_max_earnings = 0;
    }

    $global_max_earnings = absint( $global_max_earnings );

    // Only check global max earnings if isn't setup as unlimited
    if( $global_max_earnings > 0 ) {

        $earned_times = gamipress_get_earnings_count( array( 'post_id' => $achievement_id ) );

        // Bail if achievement has exceeded its global max earnings
        if( $earned_times >= $global_max_earnings ) {
            return true;
        }

    }

    // Per user max earnings
	$max_earnings = gamipress_get_post_meta( $achievement_id, '_gamipress_maximum_earnings' );

	// Unlimited maximum earnings per user check
    if( $max_earnings === '-1' || $max_earnings === '0' || empty( $max_earnings ) ) {
		return false;
    }

	// If the achievement has an earning limit per user, and we've earned it before...
	if ( $max_earnings && $user_has_achievement = gamipress_get_earnings_count( array( 'user_id' => absint( $user_id ), 'post_id' => absint( $achievement_id ) ) ) ) {

	    // If we've earned it as many (or more) times than allowed, then we have exceeded maximum earnings, thus true
		if ( $user_has_achievement >= $max_earnings ) {
			return true;
        }

	}

	// The post has no limit, or we're under it
	return false;
}

/**
 * Helper function for building an object for our achievement
 *
 * @since  1.0.0
 * @param  integer $achievement_id The given achievement's post ID
 * @param  string  $context        The context in which we're creating this object
 *
 * @return false|object            Our object containing only the relevant bits of information we want
 */
function gamipress_build_achievement_object( $achievement_id = 0, $context = 'earned' ) {

	// Grab the new achievement's $post data, and bail if it doesn't exist
	$achievement = gamipress_get_post( $achievement_id );

	if ( ! $achievement ) {
		return false;
    }

	// Setup a new object for the achievement
	$achievement_object                 = new stdClass;
	$achievement_object->ID             = $achievement_id;
	$achievement_object->post_type      = $achievement->post_type;
	$achievement_object->points         = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_points' ) );
	$achievement_object->points_type    = gamipress_get_post_meta( $achievement_id, '_gamipress_points_type' );

	// Store the current timestamp differently based on context
	if ( 'earned' == $context ) {
		$achievement_object->date_earned = current_time( 'timestamp' );
	} elseif ( 'started' == $context ) {
		$achievement_object->date_started = $achievement_object->last_activity_date = current_time( 'timestamp' );
	}

	// Return our achievement object, available filter so we can extend it elsewhere
	return apply_filters( 'gamipress_achievement_object', $achievement_object, $achievement_id, $context );

}

/**
 * Get an array of post IDs for achievements that are marked as "hidden"
 *
 * @since  1.0.0
 *
 * @param  string|array $achievement_type 	Limit the array to a specific type of achievement
 *
 * @return array                    		An array of hidden achievement post IDs
 */
function gamipress_get_hidden_achievement_ids( $achievement_type = '' ) {

	if( ! is_array( $achievement_type ) ) {
		$achievement_type = array( $achievement_type );
	}

	$cache = gamipress_get_cache( 'hidden_achievements_ids', array(), false );
	$hidden_ids = array();
	$all_cached = true;

	// loop al given types looking if all has been cached
	foreach( $achievement_type as $type ) {

		if( isset( $cache[$type] ) && is_array( $cache[$type] ) ) {
			$hidden_ids = array_merge( $hidden_ids, $cache[$type] );
		} else {
			$all_cached = false;
		}

	}

	// Return hidden achievements cached
	if( $all_cached ) {
		return $hidden_ids;
	}

	// Reset the hidden ids var
	$hidden_ids = array();

	// Grab our hidden achievements
	$hidden_achievements = get_posts( array(
		'post_type'         => $achievement_type,
		'post_status'       => 'publish',
		'posts_per_page'    => -1,
		'meta_key'          => '_gamipress_hidden',
		'meta_value'        => 'hidden',
		'suppress_filters'  => false,
	) );

	foreach ( $hidden_achievements as $achievement ) {

		$hidden_ids[] = $achievement->ID;

		// Initialize hidden achievement type cache
		if( ! isset( $cache[$achievement->post_type] ) ) {
			$cache[$achievement->post_type] = array();
		}

		$cache[$achievement->post_type][] = $achievement->ID;

	}

	// Cache hidden achievements
	gamipress_set_cache( 'hidden_achievements_ids', $cache );

	// Return our results
	return $hidden_ids;
}

/**
 * Check if achievement is marked as "hidden"
 *
 * @since 1.4.7
 *
 * @param int $achievement_id The achievement ID
 *
 * @return bool
 */

function gamipress_is_achievement_hidden( $achievement_id ) {

    $hidden = gamipress_get_post_meta( $achievement_id, '_gamipress_hidden' );

    return ( $hidden === 'hidden' );

}

/**
 * Get an array of post IDs for a user's earned achievements
 *
 * @since  1.0.0
 * @param  integer $user_id          The given user's ID
 * @param  string  $achievement_type Limit the array to a specific type of achievement
 * @return array                     Our user's array of earned achievement post IDs
 */
function gamipress_get_user_earned_achievement_ids( $user_id = 0, $achievement_type = '' ) {

	// Assume we have no earned achievements
	$earned_ids = array();

	// Grab our earned achievements
	$earned_achievements = gamipress_get_user_achievements( array(
		'user_id'           => $user_id,
		'achievement_type'  => $achievement_type,
		'display'           => true
	) );

	foreach ( $earned_achievements as $achievement ) {
		$earned_ids[] = $achievement->ID;
    }

	return $earned_ids;

}

/**
 * Get an array of unique achievement types a user has earned
 *
 * @since  1.0.0
 *
 * @param  int  $user_id The ID of the user earning the achievement
 * @return array 		 The array of achievements the user has earned
 */
function gamipress_get_user_earned_achievement_types( $user_id = 0 ){

	$achievements = gamipress_get_user_achievements( array( 'user_id' => $user_id ) );

	if( ! $achievements ) {
		return array();
	}

	$achievement_types = wp_list_pluck( $achievements, 'post_type' );

	return array_unique( $achievement_types );
}

/**
 * Returns achievements that may be earned when the given achievement is earned.
 *
 * @since  1.0.0
 *
 * @param  integer $achievement_id The given achievement's post ID
 *
 * @return array                   An array of achievements that are dependent on the given achievement
 */
function gamipress_get_dependent_achievements( $achievement_id = 0 ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.5.1' ) ) {
        return gamipress_get_dependent_achievements_old( $achievement_id );
    }

	global $wpdb;

	// Grab the current achievement ID if none specified
	if ( ! $achievement_id ) {
		global $post;
		$achievement_id = $post->ID;
	}

	$posts    	= GamiPress()->db->posts;
	$postmeta 	= GamiPress()->db->postmeta;

	// Grab posts that can be earned by unlocking the given achievement
    $achievements = $wpdb->get_results( $wpdb->prepare(
        "SELECT *
		 FROM {$posts} as posts,
		      {$postmeta} as meta
		 WHERE posts.ID = meta.post_id
		  AND meta.meta_key = '_gamipress_achievement_post'
		  AND meta.meta_value = %d",
        $achievement_id
    ) );

	// In addition, the post parent can be earned by unlocking the given achievement
	$post_parent = absint( gamipress_get_post_field( 'post_parent', $achievement_id ) );

	if( $post_parent !== 0 ) {
        $achievements[] = gamipress_get_post( $post_parent );
	}

	// Note: Dependent achievements has been removed since it causes duplicated awards
    // Also, on unlock an achievement of a specific type an event is triggered to handle its awards

	// Available filter to modify an achievement's dependents
	return apply_filters( 'gamipress_dependent_achievements', $achievements, $achievement_id );

}

/**
 * Returns achievements that must be earned to earn given achievement.
 *
 * @since   1.0.0
 * @updated 1.5.1 Added $post_status parameter
 *
 * @param  integer  $achievement_id The given achievement's post ID
 * @param  string   $post_status    The required achievements status (publish by default)
 *
 * @return array                   An array of achievements that are dependent on the given achievement
 */
function gamipress_get_required_achievements_for_achievement( $achievement_id = 0, $post_status = 'publish' ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.5.1' ) ) {
        return gamipress_get_required_achievements_for_achievement_old( $achievement_id );
    }

	global $wpdb;

	// Grab the current achievement ID if none specified
	if ( ! $achievement_id ) {
		global $post;
		$achievement_id = $post->ID;
	}

	// Don't retrieve requirements if achievement is not earned by steps
	if ( gamipress_get_post_meta( $achievement_id, '_gamipress_earned_by' ) !== 'triggers' )
		return false;

	$posts = GamiPress()->db->posts;

	// Setup the post status var
    if( ! is_array( $post_status ) ) {
		$post_status = explode( ',', $post_status );
    }

    if( in_array( 'any', $post_status ) ) {
        $post_status = array( 'publish', 'draft', 'pending', 'private' );
    }

	// Grab our requirements for this achievement
	$requirements = $wpdb->get_results( $wpdb->prepare(
		"SELECT *
		 FROM $posts as posts
		 WHERE posts.post_parent = %d
		  AND posts.post_status IN ( '" . implode( "', '", $post_status ) . "' )
		 ORDER BY CAST( posts.menu_order as SIGNED ) ASC",
		$achievement_id
	) );

	return $requirements;

}

/**
 * Returns achievements that may be earned when the given achievement is earned.
 *
 * @since   1.0.0
 * @updated 1.3.1 	added steps with required points
 * @updated 1.3.2 	improved query
 * @updated 1.3.9.4 added $points_type argument
 *
 * @return array An array of achievements that are dependent on the given achievement
 */
function gamipress_get_points_based_achievements( $points_type = '' ) {

	global $wpdb;

	if( empty( $points_type ) ) {
		return array();
	}

	$achievements = gamipress_get_transient( "gamipress_{$points_type}_based_achievements" );

	if ( empty( $achievements ) ) {

		$posts    	= GamiPress()->db->posts;
		$postmeta 	= GamiPress()->db->postmeta;

		// Grab posts that can be earned by unlocking the given achievement
		$achievements = $wpdb->get_results( $wpdb->prepare(
			"SELECT *
			FROM   {$posts} as p
			LEFT JOIN {$postmeta} AS m1 ON ( p.ID = m1.post_id AND m1.meta_key = %s )
			LEFT JOIN {$postmeta} AS m2 ON ( p.ID = m2.post_id AND m2.meta_key = %s )
			LEFT JOIN {$postmeta} AS m3 ON ( p.ID = m3.post_id AND m3.meta_key = %s )
			WHERE m1.meta_value = %s
				AND ( m2.meta_value = %s OR m2.meta_value = %s OR m3.meta_value = %s )
			GROUP BY p.ID",
			'_gamipress_points_type_required',	// (m1.meta_key) Of this post type
			'_gamipress_trigger_type',			// (m2.meta_key) Requirements based on earn points
			'_gamipress_earned_by',				// (m3.meta_key) Achievements earned by points
			$points_type,						// (m1.meta_value) Of this post type
			'earn-points',						// (m2.meta_value) Requirements based on earn points
			'points-balance',					// (m2.meta_value) Requirements based on points balance
			'points'							// (m3.meta_value) Achievements earned by points
		) );

		// Store these posts to a transient for 1 day
		gamipress_set_transient( "gamipress_{$points_type}_based_achievements", $achievements, 60*60*24 );
	}

	return (array) maybe_unserialize( $achievements );
}

/**
 * Destroy the points-based achievements transient if we edit a points-based achievement
 *
 * @since 1.0.0
 * @param integer $post_id The given post's ID
 */
function gamipress_bust_points_based_achievements_cache( $post_id ) {

	$post = get_post( $post_id );

	if ( gamipress_is_achievement( $post )
		&& ( 'points' === gamipress_get_post_meta( $post_id, '_gamipress_earned_by' )
            || ( isset( $_POST['_gamipress_earned_by'] ) && 'points' === $_POST['_gamipress_earned_by'] ) ) ) {

		$points_type = gamipress_get_post_meta( $post_id, '_gamipress_points_type_required' );

		// If the post is one of our achievement types and the achievement is awarded by minimum points, delete the transient
		gamipress_delete_transient( "gamipress_{$points_type}_based_achievements" );

	} else if( in_array( gamipress_get_post_type( $post_id ), gamipress_get_requirement_types_slugs() )
		&& in_array( gamipress_get_post_meta( $post_id, '_gamipress_trigger_type' ), array( 'earn-points', 'points-balance' ) ) ) {

		$points_type = gamipress_get_post_meta( $post_id, '_gamipress_points_type_required' );

		// If the post is one of our requirement types and the trigger type is a points based one, delete the transient
		gamipress_delete_transient( "gamipress_{$points_type}_based_achievements" );

	}

}
add_action( 'save_post', 'gamipress_bust_points_based_achievements_cache' );
add_action( 'trash_post', 'gamipress_bust_points_based_achievements_cache' );

/**
 * Returns achievements that may be earned when the given achievement is earned.
 *
 * @since   1.3.1
 * @updated 1.3.2 improved query
 *
 * @return array An array of achievements that are dependent on the given achievement
 */
function gamipress_get_rank_based_achievements() {

	global $wpdb;

	$achievements = gamipress_get_transient( 'gamipress_rank_based_achievements' );

	if ( empty( $achievements ) ) {

		$posts    	= GamiPress()->db->posts;
		$postmeta 	= GamiPress()->db->postmeta;

		// Grab posts that can be earned by unlocking the given achievement
		$achievements = $wpdb->get_results( $wpdb->prepare(
			"SELECT *
			FROM   {$posts} as posts
			LEFT JOIN {$postmeta} AS m1 ON ( posts.ID = m1.post_id AND m1.meta_key = %s )
			LEFT JOIN {$postmeta} AS m2 ON ( posts.ID = m2.post_id AND m2.meta_key = %s )
			WHERE m1.meta_value = %s
				OR m2.meta_value = %s",
			'_gamipress_trigger_type',	// (m1.meta_key) Requirements based on earn rank
			'_gamipress_earned_by',		// (m2.meta_key) Achievements earned by rank
			'earn-rank',				// (m1.meta_value) Requirements based on earn rank
			'rank'						// (m2.meta_value) Achievements earned by rank
		) );

		// Store these posts to a transient for 1 days
		gamipress_set_transient( 'gamipress_rank_based_achievements', $achievements, 60*60*24 );
	}

	return (array) maybe_unserialize( $achievements );

}

/**
 * Destroy the rank-based achievements transient if we edit a rank-based achievement
 *
 * @deprecated Removed the transient usage since 1.3.2
 *
 * @since 1.3.1
 *
 * @param integer $post_id The given post's ID
 */
function gamipress_bust_rank_based_achievements_cache( $post_id ) {

	$post = get_post( $post_id );

	if (
		gamipress_is_achievement( $post )
		&& (
			'rank' === gamipress_get_post_meta( $post_id, '_gamipress_earned_by' )
			|| ( isset( $_POST['_gamipress_earned_by'] ) && 'rank' === $_POST['_gamipress_earned_by'] )
		)
	) {

		// If the post is one of our achievement types, and the achievement is awarded by a rank, delete the transient
		gamipress_delete_transient( 'gamipress_rank_based_achievements' );

	} else if(
		in_array( gamipress_get_post_type( $post_id ), gamipress_get_requirement_types_slugs() )
		&& 'earn-rank' == gamipress_get_post_meta( $post_id, '_gamipress_trigger_type' )
	) {

		// If the post is one of our requirement types and the trigger type is a rank based one, delete the transient
		gamipress_delete_transient( 'gamipress_rank_based_achievements' );

	}

}
add_action( 'save_post', 'gamipress_bust_rank_based_achievements_cache' );
add_action( 'trash_post', 'gamipress_bust_rank_based_achievements_cache' );

/**
 * Helper function to retrieve an achievement post thumbnail
 *
 * Falls back to achievement type's thumbnail.
 *
 * @since  1.0.0
 *
 * @param  integer $post_id    The achievement's post ID
 * @param  string  $image_size The name of a registered custom image size
 * @param  string  $class      A custom class to use for the image tag
 *
 * @return string              Our formatted image tag
 */
function gamipress_get_achievement_post_thumbnail( $post_id = 0, $image_size = 'gamipress-achievement', $class = 'gamipress-achievement-thumbnail' ) {

	// Get our achievement thumbnail
	$image = get_the_post_thumbnail( $post_id, $image_size, array( 'class' => $class ) );

	// If we don't have an image...
	if ( ! $image ) {

		// Grab our achievement type's post thumbnail
		$achievement = get_page_by_path( gamipress_get_post_type( $post_id ), OBJECT, 'achievement-type' );
		$image = is_object( $achievement ) ? get_the_post_thumbnail( $achievement->ID, $image_size, array( 'class' => $class ) ) : false;

		// If we still have no image
		if ( ! $image ) {

			// If we already have an array for image size
			if ( is_array( $image_size ) ) {
				// Write our sizes to an associative array
				$image_sizes['width'] = $image_size[0];
				$image_sizes['height'] = $image_size[1];

			// Otherwise, attempt to grab the width/height from our specified image size
			} else {
				global $_wp_additional_image_sizes;
				if ( isset( $_wp_additional_image_sizes[$image_size] ) )
					$image_sizes = $_wp_additional_image_sizes[$image_size];
			}

			// If we can't get the defined width/height, set our own
			if ( empty( $image_sizes ) ) {
				$image_sizes = array(
					'width'  => 100,
					'height' => 100
				);
			}

			// Available filter: 'gamipress_default_achievement_post_thumbnail'
			$default_thumbnail = apply_filters( 'gamipress_default_achievement_post_thumbnail', '', $achievement, $image_sizes );

			if( ! empty( $default_thumbnail ) ) {
				$image = '<img src="' . $default_thumbnail . '" width="' . $image_sizes['width'] . '" height="' . $image_sizes['height'] . '" class="' . $class . '">';
			}

		}
	}

	// Finally, return our image tag
	return $image;
}

/**
 * Get an array of all users who have earned a given achievement
 *
 * @since   1.0.0
 * @updated 1.6.7 Added $args parameter and removed check to meet if user has earned the achievement, also removed 1.2.8 compatibility
 *
 * @param  int      $achievement_id The given achievement's post ID
 * @param  array    $args           Array of arguments that modifies the query result
 *
 * @return array                    Array of user objects
 */
function gamipress_get_achievement_earners( $achievement_id = 0, $args = array() ) {

	global $wpdb;

	// Setup vars
	$user_earnings = GamiPress()->db->user_earnings;
	$from = "{$user_earnings} AS u ";
	$where = "u.post_id = {$achievement_id} ";
    $group_by = "u.user_id";
    $order_by = '';
	$limit = '';

    // Setup default args
    $defaults = array(
        'limit'     => -1,
        'orderby'   => 'u.date',
        'order'     => 'DESC',
    );

    // On multisite, get earners only of the current site
    if( gamipress_is_network_wide_active() ) {
        $from .= "LEFT JOIN {$wpdb->usermeta} AS umcap ON ( umcap.user_id = u.user_id ) ";
        $where .= "AND umcap.meta_key = '" . $wpdb->get_blog_prefix( gamipress_get_original_site_id() ) . "capabilities' ";
    }

    /**
     * Filters the earners args
     *
     * @since 1.6.7
     *
     * @param array     $args           Array of given arguments
     * @param int       $achievement_id The given achievement's post ID
     *
     * @return array
     */
    $args = apply_filters( 'gamipress_get_achievement_earners_args', $args, $achievement_id );

    $args = wp_parse_args( $args, $defaults );

    // Setup limit
    if( (int)( $args['limit'] ) > 0 ) {
        $limit = '0, ' . $args['limit'];
    }

    // Setup order by
    if( ! empty( $args['orderby'] ) ) {
        $order_by = $args['orderby'] . ' ' .  $args['order'];
    }

	$earners = $wpdb->get_col(
	    "SELECT u.user_id
		FROM {$from}
		WHERE {$where}
		GROUP BY {$group_by} "
        . ( ! empty( $order_by ) ? "ORDER BY {$order_by} " : '' )
        . ( ! empty( $limit ) ? "LIMIT {$limit} " : '' )
    );

	$earned_users = array();

	foreach( $earners as $earner_id ) {
	    $earned_users[] = new WP_User( $earner_id );
	}

	return $earned_users;

}

/**
 * Build an unordered list of users who have earned a given achievement
 *
 * @since   1.0.0
 * @updated 1.6.7 Added $args parameter
 *
 * @param  int      $achievement_id The given achievement's post ID
 * @param  array    $args           Array of arguments that modifies the earners list result
 *
 * @return string                   Concatenated markup
 */
function gamipress_get_achievement_earners_list( $achievement_id = 0, $args = array() ) {

    /**
     * Filters the earners list args
     *
     * @since 1.6.7
     *
     * @param array     $args           Array of given arguments
     * @param int       $achievement_id The given achievement's post ID
     *
     * @return array
     */
    $args = apply_filters( 'gamipress_get_achievement_earners_list_args', $args, $achievement_id );

	// Grab our users
	$earners = gamipress_get_achievement_earners( $achievement_id, $args );
	$output = '';

	// Only generate output if we have earners
	if ( ! empty( $earners ) )  {

        /**
         * Filters the achievement earners heading text
         *
         * @since 1.8.6
         *
         * @param string    $heading_text   The heading text
         * @param int       $achievement_id The given achievement's post ID
         * @param array     $args           Array of given arguments
         *
         * @return string
         */
        $heading_text = apply_filters( 'gamipress_achievement_earners_heading', __( 'People who have earned this:', 'gamipress' ), $achievement_id, $args );

		// Loop through each user and build our output
		$output .= '<h4>' . $heading_text . '</h4>';

		$output .= '<ul class="gamipress-achievement-earners-list achievement-' . $achievement_id . '-earners-list">';

		foreach ( $earners as $user ) {

            /**
             * Filters the achievement earner url
             *
             * @since 1.8.6
             *
             * @param string    $user_url       The user URl, by default the get_author_posts_url()
             * @param int       $user_id        The rendered user ID
             * @param int       $achievement_id The given achievement's post ID
             * @param array     $args           Array of given arguments
             *
             * @return string
             */
            $user_url = apply_filters( 'gamipress_achievement_earner_user_url', get_author_posts_url( $user->ID ), $user->ID, $achievement_id, $args );

            /**
             * Filters the achievement earner display
             *
             * @since 1.8.6
             *
             * @param string    $user_display   The user display, by default the user display name
             * @param int       $user_id        The rendered user ID
             * @param int       $achievement_id The given achievement's post ID
             * @param array     $args           Array of given arguments
             *
             * @return string
             */
            $user_display = apply_filters( 'gamipress_achievement_earner_user_display', $user->display_name, $user->ID, $achievement_id, $args );

			$user_content = '<li>'
                    . '<a href="' . $user_url . '">'
                        . get_avatar( $user->ID )
                        . '<span class="earner-display-name">' . $user_display . '</span>'
                    . '</a>'
                . '</li>';

            /**
             * Filters the earners list user output
             *
             * @since   1.0.0
             * @updated 1.6.7 Added $achievement_id and $args arguments
             *
             * @param string    $user_content   User output
             * @param int       $user_id        The rendered user ID
             * @param int       $achievement_id The given achievement's post ID
             * @param array     $args           Array of given arguments
             *
             * @return string
             */
			$output .= apply_filters( 'gamipress_get_achievement_earners_list_user', $user_content, $user->ID, $achievement_id, $args );

		}

		$output .= '</ul>';
	}

    /**
     * Filters the achievement earners list output
     *
     * @since 1.0.0
     *
     * @param string    $output         Achievement earners list output
     * @param int       $achievement_id The given achievement's post ID
     * @param array     $args           Array of given arguments
     * @param array     $earners        Array of achievement earners
     *
     * @return string
     */
	return apply_filters( 'gamipress_get_achievement_earners_list', $output, $achievement_id, $args, $earners );
}

/**
 * Flush rewrite rules whenever an achievement type is published.
 *
 * @since 1.0.0
 *
 * @param string $new_status New status.
 * @param string $old_status Old status.
 * @param object $post       Post object.
 */
function gamipress_flush_rewrite_on_published_achievement( $new_status, $old_status, $post ) {

	if ( 'achievement-type' === $post->post_type && 'publish' === $new_status && 'publish' !== $old_status ) {
		gamipress_flush_rewrite_rules();
	}

}
add_action( 'transition_post_status', 'gamipress_flush_rewrite_on_published_achievement', 10, 3 );

/**
 * Update all dependent data if achievement type name has changed.
 *
 * @since  1.0.0
 *
 * @param  array $data      Post data.
 * @param  array $post_args Post args.
 *
 * @return array            Updated post data.
 */
function gamipress_maybe_update_achievement_type( $data = array(), $post_args = array() ) {

	// Bail if not is main site, on network wide installs achievements are just available on main site
	if( gamipress_is_network_wide_active() && ! is_main_site() ) {
		return $data;
	}

	// Bail if not is a achievement type
	if( $post_args['post_type'] !== 'achievement-type') {
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

    if ( gamipress_achievement_type_changed( $post_args ) ) {

        $original_type = get_post( $post_args['ID'] )->post_name;
        $new_type = $post_args['post_name'];

        $data['post_name'] = gamipress_update_achievement_types( $original_type, $new_type );

		/**
		 * Action triggered when an achievement type has been changed
		 *
		 * @since  1.5.1
		 *
		 * @param  array 	$data      		Post data.
		 * @param  array 	$post_args 		Post args.
		 * @param  string 	$original_type 	The original type slug.
		 * @param  string 	$new_type 		The new type slug.
		 */
		do_action( 'gamipress_achievement_type_changed', $data, $post_args, $original_type, $new_type );

		// Add a filter for redirect user to renamed achievement
        add_filter( 'redirect_post_location', 'gamipress_achievement_type_rename_redirect', 99 );

    }

	return $data;
}
add_filter( 'wp_insert_post_data' , 'gamipress_maybe_update_achievement_type' , 99, 2 );

/**
 * Check if an achievement type name has changed.
 *
 * @since  1.0.0
 *
 * @param  array $post_args Post args.
 * @return bool             True if name has changed, otherwise false.
 */
function gamipress_achievement_type_changed( $post_args = array() ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	}

	$original_post = ( !empty( $post_args['ID'] ) && isset( $post_args['ID'] ) ) ? get_post( $post_args['ID'] ) : null;
	$status = false;

	if ( is_object( $original_post ) ) {
		if (
			$post_args['post_type'] === 'achievement-type'
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
 * Replace all instances of one achievement type with another.
 *
 * @since  1.0.0
 *
 * @param  string $original_type Original achievement type.
 * @param  string $new_type      New achievement type.
 * @return string                New achievement type.
 */
function gamipress_update_achievement_types( $original_type = '', $new_type = '' ) {

	// Sanity check to prevent altering core posts
	if ( empty( $original_type ) || in_array( $original_type, array( 'post', 'page', 'attachment', 'revision', 'nav_menu_item' ) ) ) {
		return $new_type;
	}

	gamipress_update_achievements_achievement_types( $original_type, $new_type );
	gamipress_update_earned_meta_achievement_types( $original_type, $new_type );

	/**
	 * Action triggered when an achievement type gets updated (called before flush rewrite rules)
	 *
	 * @since  1.5.1
	 *
	 * @param  string 	$original_type 	The original type slug.
	 * @param  string 	$new_type 		The new type slug.
	 */
	do_action( 'gamipress_update_achievement_type', $original_type, $new_type );

	gamipress_flush_rewrite_rules();

	return $new_type;

}

/**
 * Change all achievements of one type to a new type.
 *
 * @since 1.0.0
 *
 * @param string $original_type Original achievement type.
 * @param string $new_type      New achievement type.
 */
function gamipress_update_achievements_achievement_types( $original_type = '', $new_type = '' ) {

	$items = get_posts( array(
		'posts_per_page'    => -1,
		'post_status'       => 'any',
		'post_type'         => $original_type,
		'fields'            => 'id',
		'suppress_filters'  => false,
	) );

	foreach ( $items as $item ) {
		set_post_type( $item->ID, $new_type );
	}

}

/**
 * Change all earned meta from one achievement type to another.
 *
 * @since 1.0.0
 *
 * @param string $original_type Original achievement type.
 * @param string $new_type      New achievement type.
 */
function gamipress_update_earned_meta_achievement_types( $original_type = '', $new_type = '' ) {

	// If not properly upgrade to required version fallback to compatibility function
	if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {
		gamipress_update_earned_meta_achievement_types_old( $original_type, $new_type );
		return;
	}

	// Setup CT object
	$ct_table = ct_setup_table( 'gamipress_user_earnings' );

	$ct_table->db->update(
		array( 'post_type' => $new_type ),
		array( 'post_type' => $original_type )
	);

	ct_reset_setup_table();

}

/**
 * Get unserialized user achievement metas.
 *
 * @since  1.0.0
 *
 * @param  string $meta_key      Meta key.
 * @param  string $original_type Achievement type.
 *
 * @return array                 User achievement metas.
 */
function gamipress_get_unserialized_achievement_metas( $meta_key = '', $original_type = '' ) {

	$metas = gamipress_get_achievement_metas( $meta_key, $original_type );

	if ( ! empty( $metas ) ) {
		foreach ( $metas as $key => $meta ) {
			$metas[ $key ]->meta_value = maybe_unserialize( $meta->meta_value );
		}
	}

	return $metas;

}

/**
 * Get serialized user achievement metas.
 *
 * @since  1.0.0
 *
 * @param  string $meta_key      Meta key.
 * @param  string $original_type Achievement type.
 * @return array                 User achievement metas.
 */
function gamipress_get_achievement_metas( $meta_key = '', $original_type = '' ) {

	global $wpdb;

	return $wpdb->get_results( $wpdb->prepare(
		"SELECT *
		FROM   {$wpdb->usermeta}
		WHERE  meta_key = %s
		       AND meta_value LIKE '%%%s%%'",
		$meta_key,
		$original_type
	) );

}

/**
 * Change user achievement meta from one achievement type to another.
 *
 * @since 1.0.0
 *
 * @param array  $achievements  Array of achievements.
 * @param string $original_type Original achievement type.
 * @param string $new_type      New achievement type.
 *
 * @return array $achievements
 */
function gamipress_update_meta_achievement_types( $achievements = array(), $original_type = '', $new_type = '' ) {

	if ( is_array( $achievements ) && ! empty( $achievements ) ) {

		foreach ( $achievements as $key => $achievement ) {
			if ( $achievement->post_type === $original_type ) {
				$achievements[ $key ]->post_type = $new_type;
			}
		}

	}

	return $achievements;
}

/**
 * Redirect to include custom rename message.
 *
 * @since  1.0.0
 *
 * @param  string $location Original URI.
 * @return string           Updated URI.
 */
function gamipress_achievement_type_rename_redirect( $location = '' ) {

	remove_filter( 'redirect_post_location', __FUNCTION__, 99 );

	return add_query_arg( 'message', 99, $location );

}

/**
 * Filter the "post updated" messages to include support for achievement types.
 *
 * @since 1.0.0
 *
 * @param array $messages Array of messages to display.
 *
 * @return array $messages Compiled list of messages.
 */
function gamipress_achievement_type_update_messages( $messages ) {

	$messages['achievement-type'] = array_fill( 1, 10, __( 'Achievement Type saved successfully.', 'gamipress' ) );
	$messages['achievement-type']['99'] = sprintf( __('Achievement Type renamed successfully. <p>All achievements of this type, and all active and earned user achievements, have been updated <strong>automatically</strong>.</p> All shortcodes, %s, and URIs that reference the old achievement type slug must be updated <strong>manually</strong>.', 'gamipress'), '<a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">' . __( 'widgets', 'gamipress' ) . '</a>' );

	return $messages;

}
add_filter( 'post_updated_messages', 'gamipress_achievement_type_update_messages' );

/**
 * Log a user's achievements earns/awards
 *
 * @since   1.0.0
 * @updated 1.4.7 Added $trigger parameter
 *
 * @param int       $user_id        The user ID
 * @param int       $achievement_id The associated achievement ID
 * @param int       $admin_id       An admin ID (if admin-awarded)
 * @param string    $trigger        The trigger that fires this function
 */
function gamipress_log_user_achievement_award( $user_id, $achievement_id, $admin_id = 0, $trigger = '' ) {

    $post_type = gamipress_get_post_type( $achievement_id );

	$log_meta = array(
		'achievement_id' => $achievement_id,
	);

	$access = 'public';

	// Alter our log pattern if this was an admin action
	if ( $admin_id ) {
		$type = 'achievement_award';
		$access = 'private';

		$log_meta['pattern'] =  gamipress_get_option( 'achievement_awarded_log_pattern', __( '{admin} awarded {user} with the the {achievement} {achievement_type}', 'gamipress' ) );
		$log_meta['admin_id'] = $admin_id;

        if( empty( $trigger ) ) {
            $trigger = 'gamipress_award_achievement';
        }
	} else {
		$type = 'achievement_earn';

        if( in_array( $post_type, gamipress_get_requirement_types_slugs() ) ) {
            $log_meta['pattern'] = gamipress_get_option( 'requirement_complete_log_pattern', __( '{user} completed the {achievement_type} {achievement}', 'gamipress' ) );
        } else {
            $log_meta['pattern'] = gamipress_get_option( 'achievement_earned_log_pattern', __( '{user} unlocked the {achievement} {achievement_type}', 'gamipress' ) );
        }

        if( empty( $trigger ) ) {
            $trigger = 'gamipress_unlock_achievement';
        }
	}

	// Create the log entry
    gamipress_insert_log( $type, $user_id, $access, $trigger, $log_meta );

}
