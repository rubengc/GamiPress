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
 * Remove all user logs and earnings when is deleted from the database.
 *
 * @since 1.8.0
 *
 * @param int      $id       ID of the user to delete.
 * @param int|null $reassign ID of the user to reassign posts and links to (Default null, for no reassignment).
 */
function gamipress_on_delete_user( $id, $reassign ) {

    global $wpdb;

    $user_earnings 		= GamiPress()->db->user_earnings;
    $user_earnings_meta = GamiPress()->db->user_earnings_meta;
    $logs 		        = GamiPress()->db->logs;
    $logs_meta 	        = GamiPress()->db->logs_meta;

    // Delete all user's earnings
    $wpdb->query( "DELETE ue FROM {$user_earnings} AS ue WHERE ue.user_id = {$id}" );
    // Delete orphaned user earnings metas
    $wpdb->query( "DELETE uem FROM {$user_earnings_meta} uem LEFT JOIN {$user_earnings} ue ON ue.user_earning_id = uem.user_earning_id WHERE ue.user_earning_id IS NULL" );

    // Delete all user's logs
    $wpdb->query( "DELETE l FROM {$logs} AS l WHERE l.user_id = {$id}" );
    // Delete orphaned logs metas
    $wpdb->query( "DELETE lm FROM {$logs_meta} lm LEFT JOIN {$logs} l ON l.log_id = lm.log_id WHERE l.log_id IS NULL" );

}
add_action( 'delete_user', 'gamipress_on_delete_user', 10, 2 );

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
	if ( ! $args['user_id'] ) {
		$args['user_id'] = get_current_user_id();
    }

	// Bail if not user provided or current user is not logged in
	if( absint( $args['user_id'] ) === 0 ) {
	    return array();
    }

	// Setup CT object
	$ct_table = ct_setup_table( 'gamipress_user_earnings' );

    // Bail if table not yet created
    if( ! is_object( $ct_table ) ) {
        return array();
    }

	// Setup query args
	$query_args = array(
		'user_id' 			=> $args['user_id'],
		'items_per_page' 	=> $args['limit'],
	);

	if( $args['limit'] === -1 ) {
        $query_args['nopaging'] = true;
    }

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
		    if( ! gamipress_post_exists( $achievement->post_id ) ) {
                unset( $achievements[$key] );
            }

		    // Unset not published achievements
            if( gamipress_get_post_field( 'post_status', $achievement->post_id ) !== 'publish' ) {
                unset( $achievements[$key] );
            }

			// Unset hidden achievements on display context
			if( gamipress_is_achievement_hidden( $achievement->post_id ) ) {
				unset( $achievements[$key] );
            }

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
