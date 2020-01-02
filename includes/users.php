<?php
/**
 * Users Functions
 *
 * @package     GamiPress\Users_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register user's meta
 *
 * @since 1.8.0
 */
function gamipress_register_users_metas() {

    // Points types
    foreach( gamipress_get_points_types() as $points_type => $data ) {
        $meta_args = array(
            'type'              => 'integer',
            'description'       => sprintf( __( '%s balance', 'gamipress' ), $data['plural_name'] ),
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'absint',
            'auth_callback'     => '__return_true'
        );

        /**
         * Filters the points type meta arguments
         *
         * @since 1.8.0
         *
         * @param array     $meta_args      Meta arguments that will be passed to register_meta() function
         * @param string    $points_type    Points type's slug
         * @param array     $data           Points type's data
         */
        apply_filters( "gamipress_register_{$points_type}_meta_args", $meta_args, $points_type, $data );

        register_meta( 'user', "_gamipress_{$points_type}_points", $meta_args );
    }

    // Rank types
    foreach( gamipress_get_rank_types() as $rank_type => $data ) {
        $meta_args = array(
            'type'              => 'integer',
            'description'       => sprintf( __( 'Current %s', 'gamipress' ), $data['singular_name'] ),
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'absint',
            'auth_callback'     => '__return_true'
        );

        /**
         * Filters the rank type meta arguments
         *
         * @since 1.8.0
         *
         * @param array     $meta_args  Meta arguments that will be passed to register_meta() function
         * @param string    $rank_type  Rank type's slug
         * @param array     $data       Rank type's data
         */
        apply_filters( "gamipress_register_{$rank_type}_meta_args", $meta_args, $rank_type, $data );

        register_meta( 'user', "_gamipress_{$rank_type}_rank", $meta_args );
    }

}
add_action( 'init', 'gamipress_register_users_metas' );

/**
 * Get user's achievements
 *
 * @since   1.0.0
 * @updated 1.6.3 Return an empty array if not user provided or current user is not logged in
 *
 * @param  array $args An array of all our relevant arguments
 *
 * @return array       An array of all the achievement objects that matched our parameters, or empty if none
 */
function gamipress_get_user_achievements( $args = array() ) {

	// If not properly upgrade to required version fallback to compatibility function
	if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {
		return gamipress_get_user_achievements_old( $args );
	}

	// Setup our default args
	$defaults = array(
		'user_id'          => 0,     					// The given user's ID
		'site_id'          => get_current_blog_id(), 	// The given site's ID
		'achievement_id'   => false, 					// A specific achievement's post ID
		'achievement_type' => false, 					// A specific achievement type
		'since'            => 0,     					// A specific timestamp to use in place of $limit_in_days
		'limit'            => -1,    					// Limit of achievements to return
		'groupby'          => false,    				// Group by clause, setting it to 'post_id' or 'achievement_id' will prevent duplicated achievements
	);

	$args = wp_parse_args( $args, $defaults );

	// Use current user's ID if none specified
	if ( ! $args['user_id'] )
		$args['user_id'] = get_current_user_id();

	// Bail if not user provided or current user is not logged in
	if( absint( $args['user_id'] ) === 0 )
	    return array();

	// Setup CT object
	ct_setup_table( 'gamipress_user_earnings' );

	// Setup query args
	$query_args = array(
		'user_id' 			=> $args['user_id'],
		'nopaging' 			=> true,
		'items_per_page' 	=> $args['limit'],
	);

	if( $args['achievement_id'] !== false ) {
		$query_args['post_id'] = $args['achievement_id'];
	}

	if( $args['achievement_type'] !== false ) {
		$query_args['post_type'] = $args['achievement_type'];
	}

	if( $args['groupby'] !== false ) {
		$query_args['groupby'] = $args['groupby'];

		// achievement_id is allowed
		if( $args['groupby'] === 'achievement_id' ) {
			$query_args['groupby'] = 'post_id';
		}
	}

	if( $args['since'] !== 0 ) {
		$query_args['since'] = $args['since'];
	}

	$ct_query = new CT_Query( $query_args );

	$achievements = $ct_query->get_results();

	foreach ( $achievements as $key => $achievement ) {

		// Update object for backward compatibility for usages previously to 1.2.7
		$achievement->ID = $achievement->post_id;
		$achievement->date_earned = strtotime( $achievement->date );

		$achievements[$key] = $achievement;

        // If achievements earned will be displayed, then need to pass some filters
		if( isset( $args['display'] ) && $args['display'] ) {

		    // Unset not existent achievements
		    if( ! gamipress_post_exists( $achievement->post_id ) )
                unset( $achievements[$key] );

		    // Unset not published achievements
            if( gamipress_get_post_field( 'post_status', $achievement->post_id ) !== 'publish' )
                unset( $achievements[$key] );

			// Unset hidden achievements on display context
			if( gamipress_is_achievement_hidden( $achievement->post_id ) )
				unset( $achievements[$key] );
		}

	}

	ct_reset_setup_table();

	return $achievements;

}

/**
 * Updates the user's earned achievements
 *
 * @since  1.0.0
 *
 * @param  array $args 	An array containing all our relevant arguments
 *
 * @return bool 		The updated umeta ID on success, false on failure
 */
function gamipress_update_user_achievements( $args = array() ) {

	// If not properly upgrade to required version fallback to compatibility function
	if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {
		return gamipress_update_user_achievements_old( $args );
	}

	// Setup our default args
	$defaults = array(
		'user_id'          => 0,     // The given user's ID
		'site_id'          => get_current_blog_id(), // The given site's ID
		'new_achievements' => false, // An array of NEW achievements earned by the user
	);

	$args = wp_parse_args( $args, $defaults );

	// Use current user's ID if none specified
	if ( ! $args['user_id'] )
		$args['user_id'] = get_current_user_id();

	// Lets to append the new achievements array
	if ( is_array( $args['new_achievements'] ) && ! empty( $args['new_achievements'] ) ) {

		foreach( $args['new_achievements'] as $new_achievement ) {

			$user_earning_data = array(
				'title' => gamipress_get_post_field( 'post_title', $new_achievement->ID ),
				'post_id' => $new_achievement->ID,
				'post_type' => $new_achievement->post_type,
				'points' => absint( $new_achievement->points ),
				'points_type' => $new_achievement->points_type,
				'date' => date( 'Y-m-d H:i:s', $new_achievement->date_earned )
			);

			gamipress_insert_user_earning( absint( $args['user_id'] ), $user_earning_data );

		}

	}

	return true;

}

/**
 * Returns array of achievement types a user has earned across a multisite network
 *
 * @since  1.0.0
 * @param  integer $user_id  The user's ID
 * @return array             An array of post types
 */
function gamipress_get_network_achievement_types_for_user( $user_id ) {

    $blog_id = get_current_blog_id();

	// Assume we have no achievement types
	$all_achievement_types = array();

	// Loop through all active sites
	$sites = gamipress_get_network_site_ids();

	foreach( $sites as $site_blog_id ) {

		// If we're polling a different blog, switch to it
		if ( $blog_id != $site_blog_id ) {
			switch_to_blog( $site_blog_id );
		}

		// Merge earned achievements to our achievement type array
		$achievement_types = gamipress_get_user_earned_achievement_types( $user_id );

		if ( is_array( $achievement_types ) ) {
			$all_achievement_types = array_merge( $achievement_types, $all_achievement_types );
		}

        // If switched to blog, return back to que current blog
        if ( $blog_id != $site_blog_id && is_multisite() ) {
            restore_current_blog();
        }
	}

    // Restore the original blog so the sky doesn't fall
	if ( $blog_id != get_current_blog_id() && is_multisite() ) {
        restore_current_blog();
	}

	// Pare down achievement type list so we return no duplicates
	$achievement_types = array_unique( $all_achievement_types );

	// Return all found achievements
	return $achievement_types;

}
