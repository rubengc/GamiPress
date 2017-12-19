<?php
/**
 * Activity Triggers, used for triggering achievement earning
 *
 * @package     GamiPress\Triggers
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * GamiPress activity triggers
 *
 * @since  1.0.0
 * @return array Array of all activity triggers
 */
function gamipress_get_activity_triggers() {

	GamiPress()->activity_triggers = apply_filters( 'gamipress_activity_triggers',
		array(
			// WordPress
			__( 'WordPress', 'gamipress' ) => array(
				'gamipress_login'             	    	=> __( 'Log in to website', 'gamipress' ),
				'gamipress_new_comment'  				=> __( 'Comment on a post', 'gamipress' ),
				'gamipress_specific_new_comment' 		=> __( 'Comment on a specific post', 'gamipress' ),
				'gamipress_publish_post'     			=> __( 'Publish a new post', 'gamipress' ),
				'gamipress_publish_page'     			=> __( 'Publish a new page', 'gamipress' ),
				'gamipress_delete_post'     			=> __( 'Delete a post', 'gamipress' ),
				'gamipress_delete_page'     			=> __( 'Delete a page', 'gamipress' ),
			),
			// Site Interactions
			__( 'Site Interactions', 'gamipress' ) => array(
				'gamipress_site_visit'  				=> __( 'Daily visit the website', 'gamipress' ),
				'gamipress_specific_post_visit'  		=> __( 'Daily visit a specific post', 'gamipress' ),
				'gamipress_user_post_visit'  			=> __( 'Get visits on any post', 'gamipress' ),
				'gamipress_user_specific_post_visit'	=> __( 'Get visits on a specific post', 'gamipress' ),
			),
			// GamiPress
			__( 'GamiPress', 'gamipress' ) => array(
				'specific-achievement' 					=> __( 'Unlock a specific achievement', 'gamipress' ),
				'any-achievement'      					=> __( 'Unlock any achievement of type', 'gamipress' ),
				'all-achievements'     					=> __( 'Unlock all Achievements of type', 'gamipress' ),
				'earn-points' 							=> __( 'Earn an amount of points', 'gamipress' ),
				'gamipress_expend_points' 				=> __( 'Expend an amount of points', 'gamipress' ),
				'earn-rank' 							=> __( 'Reach a rank', 'gamipress' ),
			),
		)
	);

	return GamiPress()->activity_triggers;

}

/**
 * GamiPress specific activity triggers
 *
 * @since  1.0.0
 *
 * @return array Array of all specific activity triggers
 */
function gamipress_get_specific_activity_triggers() {

	return apply_filters( 'gamipress_specific_activity_triggers', array(
		'gamipress_specific_new_comment' 		=> array( 'post', 'page' ),
		'gamipress_specific_post_visit'  		=> array( 'post', 'page' ),
		'gamipress_user_specific_post_visit'  	=> array( 'post', 'page' ),
	) );

}

/**
 * GamiPress specific activity triggers query args (used on requirements UI)
 *
 * @since  1.3.6
 *
 * @param array|string 	$query_args
 * @param string 		$activity_trigger
 *
 * @return array
 */
function gamipress_get_specific_activity_triggers_query_args( $query_args, $activity_trigger ) {

	return apply_filters( 'gamipress_specific_activity_triggers_query_args', $query_args, $activity_trigger );

}

/**
 * Helper function for returning an activity trigger label
 *
 * @since  1.0.0
 *
 * @param string $activity_trigger
 *
 * @return string
 */
function gamipress_get_activity_trigger_label( $activity_trigger ) {

	$activity_triggers = gamipress_get_activity_triggers();

	foreach( $activity_triggers as $group => $group_triggers ) {

		if( isset( $group_triggers[$activity_trigger] ) ) {

			return $group_triggers[$activity_trigger];
		}

	}

	return '';

}

/**
 * Helper function for returning a specific activity trigger label
 *
 * @since  1.0.0
 * @param string $activity_trigger
 * @return string
 */
function gamipress_get_specific_activity_trigger_label( $activity_trigger ) {

	$specific_activity_trigger_labels = apply_filters( 'gamipress_specific_activity_trigger_label', array(
		'gamipress_specific_new_comment' 		=> __( 'Comment on %s', 'gamipress' ),
		'gamipress_specific_post_visit'  		=> __( 'Visit %s', 'gamipress' ),
		'gamipress_user_specific_post_visit'  	=> __( '%s gets visited', 'gamipress' ),
	) );

	if( isset( $specific_activity_trigger_labels[$activity_trigger] ) ) {
		return $specific_activity_trigger_labels[$activity_trigger];
	}

	return '';

}

/**
 * Load up our activity triggers so we can add actions to them
 *
 * @since 1.0.0
 * @return void
 */
function gamipress_load_activity_triggers() {

	// Grab our activity triggers
	$activity_triggers = gamipress_get_activity_triggers();

	// Loop through each achievement type and add triggers for unlocking them
	foreach ( gamipress_get_achievement_types_slugs() as $achievement_type ) {

		// Grab the post type object, and bail if it's not actually an object
		$post_type_object = get_post_type_object( $achievement_type );
		if ( ! is_object( $post_type_object ) )
			continue;

		// Add trigger for unlocking ANY and ALL posts for each achievement type
		$activity_triggers[__( 'GamiPress', 'gamipress' )]['gamipress_unlock_' . $achievement_type] = sprintf( __( 'Unlocked a %s', 'gamipress' ), $post_type_object->labels->singular_name );
		$activity_triggers[__( 'GamiPress', 'gamipress' )]['gamipress_unlock_all_' . $achievement_type] = sprintf( __( 'Unlocked all %s', 'gamipress' ), $post_type_object->labels->name );

	}

	// Loop through each trigger and add our trigger event to the hook
	foreach ( $activity_triggers as $group => $group_triggers ) {
		foreach( $group_triggers as $trigger => $label ) {
			add_action( $trigger, 'gamipress_trigger_event', 10, 20 );
		}
	}

}
add_action( 'init', 'gamipress_load_activity_triggers' );

/**
 * Handle each of our activity triggers
 *
 * @since 1.0.0
 * @return mixed
 */
function gamipress_trigger_event() {

	// Setup all our globals
	global $blog_id, $wpdb;

	$site_id = $blog_id;

	$args = func_get_args();

	// Grab our current trigger
	$trigger = current_filter();

	// if only log events with listeners is enabled, the check if has listeners
	if( (bool) gamipress_get_option( 'only_log_events_with_listeners', false ) ) {
		// If not achievements listening it, then return
		if( ! gamipress_trigger_has_listeners( $trigger, $site_id, $args ) ) {
			return $args[0];
		}
	}

	// Grab the user ID
	$user_id = gamipress_trigger_get_user_id( $trigger, $args );
	$user_data = get_user_by( 'id', $user_id );

	// Sanity check, if we don't have a user object, bail here
	if ( ! is_object( $user_data ) ) {
		return $args[0];
	}

	// If the user doesn't satisfy the trigger requirements, bail here
	if ( ! apply_filters( 'gamipress_user_deserves_trigger', true, $user_id, $trigger, $site_id, $args ) ) {
		return $args[0];
	}

	// Update hook count for this user
	$new_count = gamipress_update_user_trigger_count( $user_id, $trigger, $site_id, $args );

	// Log meta data
	$log_meta = array(
		'pattern' => gamipress_get_option( 'trigger_log_pattern', __( '{user} triggered {trigger_type} (x{count})', 'gamipress' ) ),
		'count' => $new_count,
		'trigger_type' => $trigger,
	);

	// If is specific trigger then try to get the attached id
	if( in_array( $trigger, array_keys( gamipress_get_specific_activity_triggers() ) ) ) {
		$specific_id = gamipress_specific_trigger_get_id( $trigger, $args );

		// If there is a specific id, then add it to the log meta data
		if( $specific_id !== 0 ) {
			$log_meta['achievement_post'] = $specific_id;
		}
	}

	// Available filter to insert custom meta data
	$log_meta = apply_filters( 'gamipress_log_event_trigger_meta_data', $log_meta, $user_id, $trigger, $site_id, $args );

	// Mark the count in the log entry
	gamipress_insert_log( 'event_trigger', $user_id, 'private', $log_meta );

	// Now determine if any achievements are earned based on this trigger event
	$triggered_achievements = $wpdb->get_results( $wpdb->prepare(
		"SELECT post_id
		FROM   $wpdb->postmeta
		WHERE  meta_key = '_gamipress_trigger_type'
		       AND meta_value = %s",
		$trigger
	) );

	foreach ( $triggered_achievements as $achievement ) {
		gamipress_maybe_award_achievement_to_user( $achievement->post_id, $user_id, $trigger, $site_id, $args );
	}

	return $args[ 0 ];

}

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.1.8
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_log_event_trigger_extended_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

	switch ( $trigger ) {
		case 'gamipress_publish_post':
		case 'gamipress_publish_page':
		case 'gamipress_delete_post':
		case 'gamipress_delete_page':
		case 'gamipress_specific_post_visit':
			// Add the published/deleted/visited post ID
			$log_meta['post_id'] = $args[0];
			break;
		case 'gamipress_user_post_visit':
		case 'gamipress_user_specific_post_visit':
			// Add the visited post ID
			$log_meta['post_id'] = $args[0];
			$log_meta['visitor_id'] = $args[2];
			break;
		case 'gamipress_new_comment':
		case 'gamipress_specific_new_comment':
			// Add the comment ID and post commented ID
			$log_meta['comment_id'] = $args[0];
			$log_meta['comment_post_id'] = $args[2];
			break;
		case 'gamipress_expend_points':
			// Add the post ID, the amount of points and the points type
			$log_meta['post_id'] = $args[0];
			$log_meta['points'] = $args[2];
			$log_meta['points_type'] = $args[3];
			break;
		case 'gamipress_login':
		case 'gamipress_site_visit':
		case 'gamipress_unlock_' === substr( $trigger, 0, 15 ):
		default :
			// Nothing to store
			break;
	}

	return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_log_event_trigger_extended_meta_data', 10, 5 );

/**
 * Extra filter to check duplicated activity
 *
 * @since 1.1.8
 *
 * @param bool 		$return
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return bool					True if user deserves trigger, else false
 */
function gamipress_trigger_duplicity_check( $return, $user_id, $trigger, $site_id, $args  ) {

	$log_meta = array(
		'type' => 'event_trigger',
		'trigger_type' => $trigger,
	);

	switch ( $trigger ) {
		case 'gamipress_publish_post':
		case 'gamipress_publish_page':
		case 'gamipress_delete_post':
		case 'gamipress_delete_page':
			// User can not publish/delete same post more times, so check it
			$log_meta['post_id'] = $args[0];
			$return = (bool) ( gamipress_get_user_log_count( $user_id, $log_meta ) === 0 );
			break;
		case 'gamipress_user_post_visit':
		case 'gamipress_user_specific_post_visit':
			// Prevent award author for receive repeated visits
			$log_meta['post_id'] = $args[0];
			$log_meta['visitor_id'] = $args[2];

			// Guests are allowed to trigger the visit unlimited times
			if( $log_meta['visitor_id'] !== 0 ) {
				$return = (bool) ( gamipress_get_user_log_count( $user_id, $log_meta ) === 0 );
			}
			break;
		case 'gamipress_new_comment':
		case 'gamipress_specific_new_comment':
			// User can not publish same comment more times, so check it
			$log_meta['comment_id'] = $args[0];
			$return = (bool) ( gamipress_get_user_log_count( $user_id, $log_meta ) === 0 );
			break;
	}

	return apply_filters( 'gamipress_trigger_duplicity_check', $return, $user_id, $trigger, $site_id, $args );

}
add_filter( 'gamipress_user_deserves_trigger', 'gamipress_trigger_duplicity_check', 10, 5 );

/**
 * Get user for a given trigger action.
 *
 * @since  1.0.0
 *
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          User ID.
 */
function gamipress_trigger_get_user_id( $trigger = '', $args = array() ) {

	switch ( $trigger ) {
		case 'gamipress_login':
		case 'gamipress_site_visit':
		case 'gamipress_unlock_' == substr( $trigger, 0, 15 ):
			$user_id = $args[0];
			break;
		case 'gamipress_publish_post':
		case 'gamipress_publish_page':
		case 'gamipress_delete_post':
		case 'gamipress_delete_page':
		case 'gamipress_new_comment':
		case 'gamipress_specific_new_comment':
		case 'gamipress_specific_post_visit':
		case 'gamipress_user_post_visit':
		case 'gamipress_user_specific_post_visit':
		case 'gamipress_expend_points':
			$user_id = $args[1];
			break;
		default :
			$user_id = get_current_user_id();
			break;
	}

	return apply_filters( 'gamipress_trigger_get_user_id', $user_id, $trigger, $args );
}

/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.8
 *
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          Specific ID.
 */
function gamipress_specific_trigger_get_id( $trigger = '', $args = array() ) {

	switch ( $trigger ) {
		case 'gamipress_specific_new_comment':
			$specific_id = $args[2];
			break;
		case 'gamipress_specific_post_visit':
		case 'gamipress_user_specific_post_visit':
		default :
			$specific_id = $args[0];
			break;
	}

	return absint( apply_filters( 'gamipress_specific_trigger_get_id', $specific_id, $trigger, $args ) );
}

/**
 * Check if trigger has listeners
 *
 * @since 1.0.8
 *
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return bool
 */
function gamipress_trigger_has_listeners( $trigger, $site_id, $args ) {

	global $wpdb;

	$listeners_count = $wpdb->get_var( $wpdb->prepare(
		"
		SELECT COUNT(*)
		FROM   $wpdb->posts AS p
		LEFT JOIN $wpdb->postmeta AS pm
		ON ( p.ID = pm.post_id )
		WHERE p.post_status = %s
			AND p.post_type IN ( '" . implode( "', '", gamipress_get_requirement_types_slugs() ) . "' )
			AND ( pm.meta_key = %s AND pm.meta_value = %s )
		",
		'publish',
		'_gamipress_trigger_type', $trigger
	) );

	$has_listeners = ( absint( $listeners_count ) > 0 );

	return apply_filters( 'gamipress_trigger_has_listeners', $has_listeners, $trigger, $site_id, $args );
}

/**
 * Wrapper function for returning a user's array of sprung triggers
 *
 * @since  1.0.0
 * @param  integer $user_id The given user's ID
 * @param  integer $site_id The desired Site ID to check
 * @return array            An array of the triggers a user has triggered
 */
function gamipress_get_user_triggers( $user_id = 0, $site_id = 0 ) {

	// Grab all of the user's triggers
	$user_triggers = ( $array_exists = get_user_meta( $user_id, '_gamipress_triggered_triggers', true ) ) ? $array_exists : array( $site_id => array() );

	// Use current site ID if site ID is not set, AND not explicitly set to false
	if ( ! $site_id && false !== $site_id ) {
		$site_id = get_current_blog_id();
	}

	// Return only the triggers that are relevant to the provided $site_id
	if ( $site_id && isset( $user_triggers[ $site_id ] ) ) {
		return $user_triggers[ $site_id ];

	// Otherwise, return the full array of all triggers across all sites
	} else {
		return $user_triggers;
	}
}

/**
 * Get the count for the number of times is logged a user has triggered a particular trigger
 *
 * @since  1.0.0
 *
 * @param  integer $user_id The given user's ID
 * @param  string  $trigger The given trigger we're checking
 * @param  integer $since 	The since timestamp where retrieve the logs
 * @param  integer $site_id The desired Site ID to check
 * @param  array $args      The triggered args or requirement object
 *
 * @return integer          The total number of times a user has triggered the trigger
 */
function gamipress_get_user_trigger_count( $user_id, $trigger, $since = 0, $site_id = 0, $args = array() ) {

	// If not properly upgrade to required version fallback to compatibility function
	if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {
		return gamipress_get_user_trigger_count_old(  $user_id, $trigger, $since, $site_id, $args );
	}

	global $wpdb;

	$ct_table = ct_setup_table( 'gamipress_logs' );

	// Set to current site id
	if ( ! $site_id )
		$site_id = get_current_blog_id();

	$date = '';

	if( $since !== 0 ) {
		$now = date( 'Y-m-d' );
		$since = date( 'Y-m-d', $since );

		$date = "BETWEEN '$since' AND '$now'";

		if( $since === $now ) {
			$date = ">= '$now'";
		}
	}

	// If is specific trigger then try to get the attached id
	if( in_array( $trigger, array_keys( gamipress_get_specific_activity_triggers() ) ) ) {

		$specific_id = 0;

		// if isset this key it means $args is a requirement object
		if( isset( $args['achievement_post'] ) ) {
			$specific_id = absint( $args['achievement_post'] );
		} else if( ! empty( $args ) ) {
			$specific_id = gamipress_specific_trigger_get_id( $trigger, $args );
		}

		// If there is a specific id, then try to find the count
		if( $specific_id !== 0 ) {
			$user_triggers = $wpdb->get_var( $wpdb->prepare(
				"
				SELECT COUNT(*)
				FROM   {$ct_table->db->table_name} AS l
				INNER JOIN {$ct_table->meta->db->table_name} AS lm1
				ON ( l.log_id = lm1.log_id )
				INNER JOIN {$ct_table->meta->db->table_name} AS lm2
				ON ( l.log_id = lm2.log_id )
				WHERE l.user_id = %d
					AND l.type = %s
					AND CAST( l.date AS DATE ) {$date}
					AND (
						( lm1.meta_key = %s AND lm1.meta_value = %s )
						AND ( lm2.meta_key = %s AND lm2.meta_value = %s )
					)
				",
				absint( $user_id ),
				'event_trigger',
				'_gamipress_trigger_type', $trigger,
				'_gamipress_achievement_post', $specific_id
			) );
		} else {
			return 0;
		}
	} else {
		// Single trigger count
		$user_triggers = $wpdb->get_var( $wpdb->prepare(
			"
			SELECT COUNT(*)
			FROM   {$ct_table->db->table_name} AS l
			INNER JOIN {$ct_table->meta->db->table_name} AS lm
			ON ( l.log_id = lm.log_id )
			WHERE l.user_id = %d
				AND l.type = %s
				AND CAST( l.date AS DATE ) {$date}
				AND (
					lm.meta_key = %s AND lm.meta_value = %s
				)
			",
			absint( $user_id ),
			'event_trigger',
			'_gamipress_trigger_type', $trigger
		) );
	}

	// If we have any triggers, return the current count for the given trigger
	return absint( $user_triggers );

}

/**
 * Update the user's trigger count for a given trigger by 1
 *
 * @since  1.0.0
 * @param  integer $user_id The given user's ID
 * @param  string  $trigger The trigger we're updating
 * @param  integer $site_id The desired Site ID to update
 * @param  array $args        The triggered args
 * @return integer          The updated trigger count
 */
function gamipress_update_user_trigger_count( $user_id, $trigger, $site_id = 0, $args = array() ) {

	// Set to current site id
	if ( ! $site_id )
		$site_id = get_current_blog_id();

	// Grab the current count and increase it by 1
	$trigger_count = absint( gamipress_get_user_trigger_count( $user_id, $trigger, $site_id, $args ) );
	$trigger_count += (int) apply_filters( 'gamipress_update_user_trigger_count', 1, $user_id, $trigger, $site_id, $args );

	// Update the triggers arary with the new count
	$user_triggers = gamipress_get_user_triggers( $user_id, false );
	$user_triggers[$site_id][$trigger] = $trigger_count;
	update_user_meta( $user_id, '_gamipress_triggered_triggers', $user_triggers );

	// Send back our trigger count for other purposes
	return $trigger_count;

}

/**
 * Reset a user's trigger count for a given trigger to 0 or reset ALL triggers
 *
 * @since  1.0.0
 * @param  integer $user_id The given user's ID
 * @param  string  $trigger The trigger we're updating (or "all" to dump all triggers)
 * @param  integer $site_id The desired Site ID to update (or "all" to dump across all sites)
 * @return integer          The updated trigger count
 */
function gamipress_reset_user_trigger_count( $user_id, $trigger, $site_id = 0 ) {

	// Set to current site id
	if ( ! $site_id )
		$site_id = get_current_blog_id();

	// Grab the user's current triggers
	$user_triggers = gamipress_get_user_triggers( $user_id, false );

	// If we're deleteing all triggers...
	if ( 'all' == $trigger ) {
		// For all sites
		if ( 'all' == $site_id )
			$user_triggers = array();
		// For a specific site
		else
			$user_triggers[$site_id] = array();
	// Otherwise, reset the specific trigger back to zero
	} else {
		$user_triggers[$site_id][$trigger] = 0;
	}

	// Finally, update our user meta
	update_user_meta( $user_id, '_gamipress_triggered_triggers', $user_triggers );

}
