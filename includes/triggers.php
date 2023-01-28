<?php
/**
 * Activity Triggers, used for triggering achievement earning
 *
 * @package     GamiPress\Triggers
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * GamiPress activity triggers
 *
 * @since  1.0.0
 *
 * @return array Array of all activity triggers
 */
function gamipress_get_activity_triggers() {

	GamiPress()->activity_triggers = apply_filters( 'gamipress_activity_triggers',
		array(
			// WordPress
			__( 'WordPress', 'gamipress' ) => array(
				'gamipress_register'             	    => __( 'Register to website', 'gamipress' ),
				'gamipress_login'             	    	=> __( 'Log in to website', 'gamipress' ),
				'gamipress_new_comment'  				=> __( 'Comment on a post', 'gamipress' ),
				'gamipress_specific_new_comment' 		=> __( 'Comment on a specific post', 'gamipress' ),
                'gamipress_new_comment_post_type'       => __( 'Comment on a post of a type', 'gamipress' ),
				'gamipress_user_post_comment'  			=> __( 'Get a comment on a post', 'gamipress' ),
				'gamipress_user_specific_post_comment' 	=> __( 'Get a comment on a specific post', 'gamipress' ),
                'gamipress_user_post_comment_post_type' => __( 'Get a comment on a post of a type', 'gamipress' ),
                'gamipress_spam_comment'  				=> __( 'Get a comment marked as spam', 'gamipress' ),
                'gamipress_specific_spam_comment' 		=> __( 'Get a comment of a specific post marked as spam', 'gamipress' ),
                'gamipress_spam_comment_post_type'  	=> __( 'Get a comment on a post of a type marked as spam', 'gamipress' ),
				'gamipress_publish_post'     			=> __( 'Publish a new post', 'gamipress' ),
				'gamipress_delete_post'     			=> __( 'Delete a post', 'gamipress' ),
				'gamipress_publish_page'     			=> __( 'Publish a new page', 'gamipress' ),
				'gamipress_delete_page'     			=> __( 'Delete a page', 'gamipress' ),
                'gamipress_publish_post_type'     	    => __( 'Publish a new post of a type', 'gamipress' ),
                'gamipress_delete_post_type'     	    => __( 'Delete a post of a type', 'gamipress' ),
				'gamipress_add_role'     			    => __( 'Get added to any role', 'gamipress' ),
				'gamipress_add_specific_role'     	    => __( 'Get added to specific role', 'gamipress' ),
				'gamipress_set_role'     			    => __( 'Get assigned to any role', 'gamipress' ),
				'gamipress_set_specific_role'     		=> __( 'Get assigned to specific role', 'gamipress' ),
				'gamipress_remove_role'     			=> __( 'Get removed from any role', 'gamipress' ),
				'gamipress_remove_specific_role'     	=> __( 'Get removed from a specific role', 'gamipress' ),
                'gamipress_update_user_meta_any_value'      => __( 'Get user meta updated with any value', 'gamipress' ),
                'gamipress_update_user_meta_specific_value' => __( 'Get user meta updated with a value', 'gamipress' ),
                'gamipress_update_post_meta_any_value'      => __( 'Post meta updated with any value', 'gamipress' ),
                'gamipress_update_post_meta_specific_value' => __( 'Post meta updated with a value', 'gamipress' ),
			),
			// Site Interactions
			__( 'Site Interactions', 'gamipress' ) => array(
				'gamipress_site_visit'  				=> __( 'Daily visit the website', 'gamipress' ),
				'gamipress_post_visit'  				=> __( 'Daily visit any post', 'gamipress' ),
				'gamipress_specific_post_visit'  		=> __( 'Daily visit a specific post', 'gamipress' ),
                'gamipress_post_type_visit'  		    => __( 'Daily visit a post of a type', 'gamipress' ),
				'gamipress_user_post_visit'  			=> __( 'Get visits on any post', 'gamipress' ),
				'gamipress_user_specific_post_visit'	=> __( 'Get visits on a specific post', 'gamipress' ),
                'gamipress_user_post_type_visit'  	    => __( 'Get visits on a post of a type', 'gamipress' ),
			),
			// GamiPress
			'GamiPress' => array(
				'specific-achievement' 					=> __( 'Unlock a specific achievement', 'gamipress' ),
				'any-achievement'      					=> __( 'Unlock any achievement of type', 'gamipress' ),
				'all-achievements'     					=> __( 'Unlock all Achievements of type', 'gamipress' ),
                'revoke-specific-achievement' 			=> __( 'Get a specific achievement revoked', 'gamipress' ),
                'revoke-any-achievement'      			=> __( 'Get any achievement of type revoked', 'gamipress' ),
                'earn-points' 							=> __( 'Earn an amount of points', 'gamipress' ),
				'points-balance' 					    => __( 'Reach a points balance', 'gamipress' ),
				'gamipress_expend_points' 				=> __( 'Expend an amount of points', 'gamipress' ),
				'earn-rank' 							=> __( 'Reach a rank', 'gamipress' ),
				'revoke-rank' 							=> __( 'Get a rank revoked', 'gamipress' ),
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

	// Get all public post types which means they are visitable
	$public_post_types = get_post_types( array( 'public' => true ) );

	// Remove attachment from public post types
	if( isset( $public_post_types['attachment'] ) ) {
		unset( $public_post_types['attachment'] );
	}

	// Remove keys
	$public_post_types = array_values( $public_post_types );

	// Get all post types with comments support
	$comments_post_types = get_post_types_by_support( 'comments' );

	// Remove attachment from post types with comments support
	if( $index = array_search( 'attachment', $comments_post_types ) ) {
		unset( $comments_post_types[$index] );
	}

	// Remove keys
	$comments_post_types = array_values( $comments_post_types );

	return apply_filters( 'gamipress_specific_activity_triggers', array(
		'gamipress_specific_new_comment' 		=> $comments_post_types,
		'gamipress_user_specific_post_comment'  => $comments_post_types,
        'gamipress_specific_spam_comment' 		=> $comments_post_types,
		'gamipress_specific_post_visit'  		=> $public_post_types,
		'gamipress_user_specific_post_visit'  	=> $public_post_types,
	) );

}

/**
 * GamiPress triggers for the points and points type selector
 *
 * @since 2.2.6
 *
 * @return array Array of all points triggers
 */
function gamipress_get_points_triggers() {

    return apply_filters( 'gamipress_points_triggers', array(
        'earn-points',
        'points-balance',
        'gamipress_expend_points',
    ) );

}

/**
 * GamiPress triggers for the achievement type selector
 *
 * @since 2.2.6
 *
 * @return array Array of all achievement type triggers
 */
function gamipress_get_achievement_type_triggers() {

    return apply_filters( 'gamipress_achievement_type_triggers', array(
        'specific-achievement',
        'any-achievement',
        'all-achievements',
        'revoke-specific-achievement',
        'revoke-any-achievement'
    ) );

}

/**
 * GamiPress triggers for the rank type selector
 *
 * @since 2.2.6
 *
 * @return array Array of all rank type triggers
 */
function gamipress_get_rank_type_triggers() {

    return apply_filters( 'gamipress_rank_type_triggers', array(
        'earn-rank',
        'revoke-rank',
    ) );

}

/**
 * GamiPress triggers for the post type selector
 *
 * @since 2.2.6
 *
 * @return array Array of all post type triggers
 */
function gamipress_get_post_type_triggers() {

    return apply_filters( 'gamipress_post_type_triggers', array(
        'gamipress_new_comment_post_type',
        'gamipress_user_post_comment_post_type',
        'gamipress_spam_comment_post_type',
        'gamipress_spam_comment_post_type',
        'gamipress_publish_post_type',
        'gamipress_delete_post_type',
        'gamipress_post_type_visit',
        'gamipress_user_post_type_visit',
    ) );

}

/**
 * GamiPress triggers for the user role selector
 *
 * @since 2.2.6
 *
 * @return array Array of all user role triggers
 */
function gamipress_get_user_role_triggers() {

    return apply_filters( 'gamipress_user_role_triggers', array(
        'gamipress_add_specific_role',
        'gamipress_set_specific_role',
        'gamipress_remove_specific_role',
    ) );

}

/**
 * GamiPress specific activity triggers query args (used on requirements UI and on ajax get posts)
 *
 * @since  1.3.6
 *
 * @param array|string 	$query_args
 * @param string 		$activity_trigger
 *
 * @return array|string
 */
function gamipress_get_specific_activity_triggers_query_args( $query_args, $activity_trigger ) {

    /**
     * Specific activity triggers query args (used on requirements UI and on ajax get posts)
     *
     * Note: Use $_REQUEST for all given parameters
     * @see gamipress_ajax_get_posts()
     *
     * @since  1.3.6
     *
     * @param array|string 	$query_args
     * @param string 		$activity_trigger
     *
     * @return array|string
     */
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

    $label = '';
	$activity_triggers = gamipress_get_activity_triggers();

	foreach( $activity_triggers as $group => $group_triggers ) {
		if( isset( $group_triggers[$activity_trigger] ) ) {
            $label = $group_triggers[$activity_trigger];
        }
	}

	if( gamipress_starts_with( $activity_trigger, 'gamipress_unlock_' ) ) {

        // Check if trigger is for unlocking ANY and ALL posts of an achievement type
	    $achievement_type = $activity_trigger;
	    $achievement_type = str_replace( 'gamipress_unlock_all_', '', $achievement_type );
	    $achievement_type = str_replace( 'gamipress_unlock_', '', $achievement_type );
	    $data = gamipress_get_achievement_type( $achievement_type );

        if ( $data ) {
            if( gamipress_starts_with( $activity_trigger, 'gamipress_unlock_all_' ) )
                $label = sprintf( __( 'Unlocked all %s', 'gamipress' ), $data['plural_name'] );
            else
                $label = sprintf( __( 'Unlocked a %s', 'gamipress' ), $data['singular_name'] );
        }

    } else if( gamipress_starts_with( $activity_trigger, 'gamipress_revoke_' ) ) {

        // Check if trigger is for revoking ANY posts of an achievement type
        $achievement_type = $activity_trigger;
        $achievement_type = str_replace( 'gamipress_revoke_', '', $achievement_type );
        $data = gamipress_get_achievement_type( $achievement_type );

        if ( $data ) {
            $label = sprintf( __( 'Get a %s revoked', 'gamipress' ), $data['singular_name'] );
        }

    }

    /**
     * Available filter to override an activity trigger label
     *
     * @since  1.9.9
     *
     * @param string $label
     * @param string $activity_trigger
     *
     * @return string
     */
	return apply_filters( 'gamipress_get_activity_trigger_label', $label, $activity_trigger );

}

/**
 * Helper function for returning a specific activity trigger label
 *
 * @since  1.0.0
 *
 * @param string $activity_trigger
 *
 * @return string
 */
function gamipress_get_specific_activity_trigger_label( $activity_trigger ) {

	$specific_activity_trigger_labels = apply_filters( 'gamipress_specific_activity_trigger_label', array(
		'gamipress_specific_new_comment' 		=> __( 'Comment on %s', 'gamipress' ),
		'gamipress_user_specific_post_comment'  => __( 'Get a comment on %s', 'gamipress' ),
        'gamipress_specific_spam_comment' 		=> __( 'Get a comment on %s marked as spam', 'gamipress' ),
		'gamipress_specific_post_visit'  		=> __( 'Visit %s', 'gamipress' ),
		'gamipress_user_specific_post_visit'  	=> __( '%s gets visited', 'gamipress' ),
	) );

	if( isset( $specific_activity_trigger_labels[$activity_trigger] ) ) {
		return $specific_activity_trigger_labels[$activity_trigger];
	}

	return '';

}

/**
 * Helper function to get the title of a given specific ID for a specific activity trigger
 *
 * @since 	1.4.0
 * @updated 1.4.8 Added $site_id parameter
 *
 * @param integer $specific_id      The specific ID
 * @param string  $trigger_type     The requirement trigger type
 * @param integer $site_id     		The site ID
 *
 * @return string                   The specific title
 */
function gamipress_get_specific_activity_trigger_post_title( $specific_id, $trigger_type, $site_id ) {

	$override = apply_filters( 'gamipress_specific_activity_trigger_post_title', 'no_override', $specific_id, $trigger_type, $site_id );

	if( $override !== 'no_override' ) {
		return $override;
	}

	if( gamipress_is_network_wide_active() && $site_id !== get_current_blog_id() ) {

		// Switch to the given site to get the post title from this site
		switch_to_blog( $site_id );

		$post_title = get_post_field( 'post_title', $specific_id );

		// Restore the current blog
        restore_current_blog();

		return $post_title;

	}

	return get_post_field( 'post_title', $specific_id );

}

/**
 * Helper function to determine if a given specific ID is public to get his permalink
 *
 * @since 1.5.0
 *
 * @param integer $specific_id      The specific ID
 * @param string  $trigger_type     The requirement trigger type
 * @param integer $site_id     		The site ID
 *
 * @return bool
 */
function gamipress_is_specific_activity_trigger_post_type_public( $specific_id, $trigger_type, $site_id ) {

	$override = apply_filters( 'gamipress_specific_activity_trigger_is_post_type_public', 'no_override', $specific_id, $trigger_type, $site_id );

	if( $override !== 'no_override' ) {
		return (bool) $override;
	}

    // Get all public registered post types
    $public_post_types = get_post_types( array( 'public' => true ) );

	if( gamipress_is_network_wide_active() && $site_id !== get_current_blog_id() ) {

		// Switch to the given site to get the post title from this site
		switch_to_blog( $site_id );

		$post_type = get_post_field( 'post_type', $specific_id );

		// Restore the current blog
        restore_current_blog();

	} else {
        $post_type = get_post_field( 'post_type', $specific_id );
    }

	return in_array( $post_type, $public_post_types );

}

/**
 * Helper function to get the permalink of a given specific ID for a specific activity trigger
 *
 * @since 	1.5.0
 *
 * @param integer $specific_id      The specific ID
 * @param string  $trigger_type     The requirement trigger type
 * @param integer $site_id     		The site ID
 *
 * @return false|string             The specific permalink
 */
function gamipress_get_specific_activity_trigger_permalink( $specific_id, $trigger_type, $site_id ) {

    $override = apply_filters( 'gamipress_specific_activity_trigger_permalink', 'no_override', $specific_id, $trigger_type, $site_id );

    if( $override !== 'no_override' ) {
        return $override;
    }

    if( ! gamipress_is_specific_activity_trigger_post_type_public( $specific_id, $trigger_type, $site_id ) ) {
        return false;
    }

    if( absint( $specific_id ) === 0 ) {
        return false;
    }

    if( gamipress_is_network_wide_active() && $site_id !== get_current_blog_id() ) {

        // Switch to the given site to get the post title from this site
        switch_to_blog( $site_id );

        $permalink = get_permalink( $specific_id );

        // Restore the current blog
        restore_current_blog();

        return $permalink;

    }

    return get_permalink( $specific_id );

}

/**
 * Load up our activity triggers so we can add actions to them
 *
 * @since 1.0.0
 */
function gamipress_load_activity_triggers() {

	// Grab our activity triggers
	$activity_triggers = gamipress_get_activity_triggers();
    $excluded_to_load = gamipress_get_activity_triggers_excluded_to_load();

	// Loop through each achievement type and add triggers for unlocking/revoking them
	foreach ( gamipress_get_achievement_types() as $achievement_type => $data ) {

		// Add trigger for unlocking/revoking ANY and ALL posts for each achievement type
		$activity_triggers['GamiPress']['gamipress_unlock_' . $achievement_type] = sprintf( __( 'Unlocked a %s', 'gamipress' ), $data['singular_name'] );
		$activity_triggers['GamiPress']['gamipress_unlock_all_' . $achievement_type] = sprintf( __( 'Unlocked all %s', 'gamipress' ), $data['plural_name'] );
        $activity_triggers['GamiPress']['gamipress_revoke_' . $achievement_type] = sprintf( __( 'Revoked a %s', 'gamipress' ), $data['singular_name'] );

	}

	// Loop through each trigger and add our trigger event to the hook
	foreach ( $activity_triggers as $group => $group_triggers ) {

		foreach( $group_triggers as $trigger => $label ) {

            // Hook if trigger is not excluded to be loaded
            if( ! in_array( $trigger, $excluded_to_load ) ) {
			    add_action( $trigger, 'gamipress_trigger_event', 10, 20 );
            }

		}

	}

}
add_action( 'gamipress_init', 'gamipress_load_activity_triggers' );

/**
 * Get activity triggers excluded to be loaded automatically from gamipress_load_activity_triggers()
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_get_activity_triggers_excluded_to_load() {

    return apply_filters( 'gamipress_activity_triggers_excluded_to_load', array(
        'gamipress_login',
        'gamipress_register'
    ) );

}

/**
 * Get activity triggers excluded from activity time limits
 *
 * @since 1.5.9
 *
 * @return array
 */
function gamipress_get_activity_triggers_excluded_from_activity_limit() {

    return apply_filters( 'gamipress_activity_triggers_excluded_from_activity_limit', array(
        'gamipress_register',
        'earn-points',
        'points-balance',
        'earn-rank',
        'revoke-rank'
    ) );

}

/**
 * Handle each of our activity triggers
 *
 * If method is called directly, pass an array of arguments with next items:
 * array(
 * 	'event' 		=> 'gamipress_login',
 * 	'user_id' 		=> 1,
 *  'specific_id' 	=> 100 // Just if is an specific trigger
 * )
 *
 * @since 	1.0.0
 * @updated 1.4.3 Added the ability to be called directly
 *
 * @return mixed    Returns an array will all achievements awarded or false if none has been awarded
 */
function gamipress_trigger_event() {

    // Get the current site
	$site_id = get_current_blog_id();

	$args = func_get_args();

	// Check if method has been called directly
	if( isset( $args[0] ) && is_array( $args[0] ) && isset( $args[0]['event'] ) ) {
		$args = $args[0];
    }

	// Grab our current trigger
	$trigger = ( isset( $args['event'] ) ? $args['event'] : current_filter() );

	// gamipress_unlock_all_{type}, gamipress_unlock_{type} and gamipress_revoke_{type} are excluded from this check
	if( ! ( gamipress_starts_with( $trigger, 'gamipress_unlock_' ) || gamipress_starts_with( $trigger, 'gamipress_revoke_' ) ) ) {

        /**
         * Filter to decide if GamiPress should log all events triggered (filter in replacement to the option "log_all_events")
         *
         * @since 2.5.3
         *
         * @param bool $log_all_events
         *
         * @return bool
         */
        $log_all_events = apply_filters( 'gamipress_log_all_events', false );

		// Check if log all events is enabled
		if( ! $log_all_events ) {

            // If not achievements listening it, then return
			if( ! gamipress_trigger_has_listeners( $trigger, $site_id, $args ) ) {
				return false;
            }

		}

	}

	// Grab the user ID
	$user_id = ( isset( $args['user_id'] ) ? $args['user_id'] : gamipress_trigger_get_user_id( $trigger, $args ) );
	$user_data = get_user_by( 'id', $user_id );

	// Sanity check, if we don't have a user object, bail here
	if ( ! is_object( $user_data ) ) {
		return false;
    }

	// If the user doesn't satisfy the trigger requirements, bail here
	if ( ! apply_filters( 'gamipress_user_deserves_trigger', true, $user_id, $trigger, $site_id, $args ) ) {
		return false;
    }

	// Update hook count for this user
	gamipress_update_user_trigger_count( $user_id, $trigger, $site_id, $args );

	// Register event triggered in logs
    gamipress_log_event_triggered( $user_id, $trigger, $site_id, $args );

	// Check if any achievements are earned based on this trigger event
	$triggered_achievements = gamipress_get_triggered_requirements( $user_id, $trigger, $site_id, $args );

	$awarded_achievements = array();

	foreach ( $triggered_achievements as $achievement ) {

	    $award_result = gamipress_maybe_award_achievement_to_user( $achievement->ID, $user_id, $trigger, $site_id, $args );

		if( $award_result === true ) {
            $awarded_achievements[] = $achievement->ID;
        }
	}

	return ( count( $awarded_achievements ) ? $awarded_achievements : false );

}

/**
 * Gets the event arg parameter
 *
 * @since 1.8.8
 *
 * @param array    	$args   Event args
 * @param string 	$key    The args key to get
 * @param int    	$index The index key to fallback
 *
 * @return mixed
 */
function gamipress_get_event_arg( $args = array(), $key = '', $index = 0 ) {
    return ( isset( $args[$key] ) ? $args[$key] : ( isset( $args[$index] ) ? $args[$index] : '' ) );
}

/**
 * Logs the event triggered ono gamipress_trigger_event() function
 *
 * @since 1.6.2
 *
 * @param int    	$user_id
 * @param string 	$trigger
 * @param int    	$site_id
 * @param array 	$args
 */
function gamipress_log_event_triggered( $user_id, $trigger, $site_id, $args ) {

    // Log meta data
    $log_meta = array(
        'pattern' => gamipress_get_option( 'trigger_log_pattern', __( '{user} triggered {trigger_type} (x{count})', 'gamipress' ) ),
        'count' => gamipress_get_user_trigger_count( $user_id, $trigger, 0, $site_id, $args ),
        //'trigger_type' => $trigger, // Removed since 1.4.7
    );

    // On multisite, search for site logs
    if( is_multisite() && gamipress_is_network_wide_active() ) {
        $log_meta['site_id'] = $site_id;
    }

    // If is specific trigger then try to get the attached id
    if( in_array( $trigger, array_keys( gamipress_get_specific_activity_triggers() ) ) ) {

        $specific_id = ( isset( $args['specific_id'] ) ? $args['specific_id'] : gamipress_specific_trigger_get_id( $trigger, $args ) );

        // If there is a specific id, then add it to the log meta data
        if( $specific_id !== 0 ) {
            $log_meta['achievement_post'] = $specific_id;
            $log_meta['achievement_post_site_id'] = $site_id;
        }

    }

    /**
     * Available filter to insert custom meta data
     *
     * @since 1.6.2
     *
     * @param array 	$log_meta
     * @param int 	    $user_id
     * @param string 	$trigger
     * @param int 	    $site_id
     * @param array 	$args
     *
     * @return array
     */
    $log_meta = apply_filters( 'gamipress_log_event_trigger_meta_data', $log_meta, $user_id, $trigger, $site_id, $args );

    // Mark the count in the log entry
    gamipress_insert_log( 'event_trigger', $user_id, 'private', $trigger, $log_meta );

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
		case 'gamipress_post_visit':
		case 'gamipress_specific_post_visit':
			// Add the published/deleted/visited post ID
			$log_meta['post_id'] = gamipress_get_event_arg( $args, 'post_id', 0 );
			break;
        case 'gamipress_publish_post_type':
        case 'gamipress_delete_post_type':
        case 'gamipress_post_type_visit':
        case 'gamipress_user_post_type_visit':
            // Add the post ID and type
            $log_meta['post_id'] = gamipress_get_event_arg( $args, 'post_id', 0 );
            $log_meta['post_type'] = gamipress_get_event_arg( $args, 'post_type', 2 );
            break;
		case 'gamipress_user_post_visit':
		case 'gamipress_user_specific_post_visit':
			// Add the visited post ID
			$log_meta['post_id'] = gamipress_get_event_arg( $args, 'post_id', 0 );
			$log_meta['visitor_id'] = gamipress_get_event_arg( $args, 'user_id', 2 );
			break;
		case 'gamipress_new_comment':
		case 'gamipress_specific_new_comment':
		case 'gamipress_user_post_comment':
		case 'gamipress_user_specific_post_comment':
		case 'gamipress_spam_comment':
		case 'gamipress_specific_spam_comment':
			// Add the comment ID and post commented ID
			$log_meta['comment_id'] = gamipress_get_event_arg( $args, 'comment_id', 0 );
			$log_meta['comment_post_id'] = gamipress_get_event_arg( $args, 'post_id', 2 );
			break;
        case 'gamipress_new_comment_post_type':
        case 'gamipress_user_post_comment_post_type':
        case 'gamipress_spam_comment_post_type':
            // Add the comment ID, post commented ID and type
            $log_meta['comment_id'] = gamipress_get_event_arg( $args, 'comment_id', 0 );
            $log_meta['comment_post_id'] = gamipress_get_event_arg( $args, 'post_id', 2 );
            $log_meta['comment_post_type'] = gamipress_get_event_arg( $args, 'post_type', 3 );
            break;
		case 'gamipress_expend_points':
			// Add the post ID, the amount of points and the points type
			$log_meta['post_id'] = gamipress_get_event_arg( $args, 'post_id', 0 );
			$log_meta['points'] = gamipress_get_event_arg( $args, 'points', 2 );
			$log_meta['points_type'] = gamipress_get_event_arg( $args, 'points_type', 3 );
			break;
		case gamipress_starts_with( $trigger, 'gamipress_unlock_' ):
            if( ! gamipress_starts_with( $trigger, 'gamipress_unlock_all_' ) ) {
                $log_meta['post_id'] = gamipress_get_event_arg( $args, 'post_id', 1 );
            }
            break;
		case gamipress_starts_with( $trigger, 'gamipress_revoke_' ):
            $log_meta['post_id'] = gamipress_get_event_arg( $args, 'post_id', 1 );
            break;
        case 'gamipress_login':
        case 'gamipress_register':
        case 'gamipress_site_visit':
		default:
			// Nothing to store
			break;
	}

	return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_log_event_trigger_extended_meta_data', 10, 5 );

/**
 * Extended meta data to filter the logs count
 *
 * @since 2.0.5
 *
 * @param  array    $log_meta       The meta data to filter the logs count
 * @param  int      $user_id        The given user's ID
 * @param  string   $trigger        The given trigger we're checking
 * @param  int      $since 	        The since timestamp where retrieve the logs
 * @param  int      $site_id        The desired Site ID to check
 * @param  array    $args           The triggered args or requirement object
 *
 * @return array                    The meta data to filter the logs count
 */
function gamipress_get_user_trigger_count_log_extended_meta( $log_meta, $user_id, $trigger, $since, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_publish_post_type':
        case 'gamipress_delete_post_type':
        case 'gamipress_post_type_visit':
        case 'gamipress_user_post_type_visit':
            // Filter by the post type
            $log_meta['post_type'] = gamipress_get_event_arg( $args, 'post_type', 2 );

            // $args could be a requirement object
            if( isset( $args['post_type_required'] ) ) {
                $log_meta['post_type'] = $args['post_type_required'];
            }
            break;
        case 'gamipress_new_comment_post_type':
        case 'gamipress_user_post_comment_post_type':
        case 'gamipress_spam_comment_post_type':
            // Filter by the post type
            $log_meta['comment_post_type'] = gamipress_get_event_arg( $args, 'post_type', 3 );

            // $args could be a requirement object
            if( isset( $args['post_type_required'] ) ) {
                $log_meta['comment_post_type'] = $args['post_type_required'];
            }
            break;
        case 'specific-achievement':
            // Filter by the post ID
            $log_meta['post_id'] = gamipress_get_event_arg( $args, 'post_id', 1 );

            if( isset( $args['achievement_post'] ) && absint( $log_meta['achievement_post'] ) === 0 ) {
                $log_meta['post_id'] = $args['achievement_post'];
            }

            // Update the trigger type
            $log_meta['trigger_type'] = 'gamipress_unlock_' . gamipress_get_post_type( $log_meta['post_id'] );
            break;
        case 'revoke-specific-achievement':
            // Filter by the post ID
            $log_meta['post_id'] = gamipress_get_event_arg( $args, 'post_id', 1 );

            if( isset( $args['achievement_post'] ) && absint( $log_meta['achievement_post'] ) === 0 ) {
                $log_meta['post_id'] = $args['achievement_post'];
            }

            // Update the trigger type
            $log_meta['trigger_type'] = 'gamipress_revoke_' . gamipress_get_post_type( $log_meta['post_id'] );
            break;
    }

    return $log_meta;

}
add_filter( 'gamipress_get_user_trigger_count_log_meta', 'gamipress_get_user_trigger_count_log_extended_meta', 10, 6 );

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

	// On multisite, search for site logs
	if( is_multisite() && gamipress_is_network_wide_active() ) {
        $log_meta['site_id'] = $site_id;
    }

	switch ( $trigger ) {
		case 'gamipress_publish_post':
		case 'gamipress_publish_page':
		case 'gamipress_publish_post_type':
		case 'gamipress_delete_post':
		case 'gamipress_delete_page':
		case 'gamipress_delete_post_type':
			// User can not publish/delete same post more times, so check it
			$log_meta['post_id'] = gamipress_get_event_arg( $args, 'post_id', 0 );
			$return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
			break;
		case 'gamipress_user_post_visit':
		case 'gamipress_user_specific_post_visit':
			// Prevent award author for receive repeated visits
			$log_meta['post_id'] = $args[0];
			$log_meta['visitor_id'] = $args[2];

			// Guests are allowed to trigger the visit unlimited times
			if( $log_meta['visitor_id'] !== 0 ) {
				$return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
			}
			break;
		case 'gamipress_new_comment':
		case 'gamipress_specific_new_comment':
		case 'gamipress_new_comment_post_type':
		case 'gamipress_user_post_comment':
		case 'gamipress_user_specific_post_comment':
		case 'gamipress_user_post_comment_post_type':
        case 'gamipress_spam_comment':
        case 'gamipress_specific_spam_comment':
        case 'gamipress_spam_comment_post_type':
			// User can not publish same comment more times, so check it
			$log_meta['comment_id'] = $args[0];
			$return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
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

	// If gamipress_trigger_event() has called directly, then get user ID from args
	if( isset( $args['event'] ) && isset( $args['user_id'] ) ) {
		return $args['user_id'];
	}

	switch ( $trigger ) {
		case 'gamipress_login':
		case 'gamipress_register':
		case 'gamipress_site_visit':
        case 'gamipress_add_role':
        case 'gamipress_add_specific_role':
        case 'gamipress_set_role':
        case 'gamipress_set_specific_role':
        case 'gamipress_remove_role':
        case 'gamipress_remove_specific_role':
		case gamipress_starts_with( $trigger, 'gamipress_unlock_' ):
		case gamipress_starts_with( $trigger, 'gamipress_revoke_' ):
        case 'gamipress_update_user_meta_any_value':
        case 'gamipress_update_user_meta_specific_value':
        case 'gamipress_update_post_meta_any_value':
        case 'gamipress_update_post_meta_specific_value':
			$user_id = $args[0];
			break;
		case 'gamipress_publish_post':
		case 'gamipress_publish_page':
		case 'gamipress_publish_post_type':
		case 'gamipress_delete_post':
		case 'gamipress_delete_page':
		case 'gamipress_delete_post_type':
		case 'gamipress_new_comment':
		case 'gamipress_specific_new_comment':
		case 'gamipress_new_comment_post_type':
		case 'gamipress_user_post_comment':
		case 'gamipress_user_specific_post_comment':
		case 'gamipress_user_post_comment_post_type':
        case 'gamipress_spam_comment':
        case 'gamipress_specific_spam_comment':
        case 'gamipress_spam_comment_post_type':
		case 'gamipress_post_visit':
		case 'gamipress_specific_post_visit':
		case 'gamipress_post_type_visit':
		case 'gamipress_user_post_visit':
		case 'gamipress_user_specific_post_visit':
		case 'gamipress_user_post_type_visit':
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

	// If gamipress_trigger_event() has called directly, then get user ID from args
	if( isset( $args['event'] ) && isset( $args['specific_id'] ) ) {
		return $args['specific_id'];
	}

	switch ( $trigger ) {
		case 'gamipress_specific_new_comment':
		case 'gamipress_user_specific_post_comment':
        case 'gamipress_specific_spam_comment':
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

	$posts  	= GamiPress()->db->posts;
	$postmeta  	= GamiPress()->db->postmeta;

    $triggers_count = gamipress_get_triggers_listeners_count();
	$listeners_count = ( isset( $triggers_count[$trigger] ) ? $triggers_count[$trigger] : 0 );

	// If is specific trigger then try to get the attached id
	if( in_array( $trigger, array_keys( gamipress_get_specific_activity_triggers() ) ) && isset( $triggers_count[$trigger] ) ) {

	    // Reset the listeners count since is a specific trigger
        $listeners_count = 0;
		$specific_id = 0;

		// If isset this key it means $args is a requirement object
		if( isset( $args['achievement_post'] ) ) {
			$specific_id = absint( $args['achievement_post'] );
		} else if( ! empty( $args ) ) {
			$specific_id = gamipress_specific_trigger_get_id( $trigger, $args );
		}

		// If there is a specific id, then try to find the count
		if( $specific_id !== 0 ) {

            $cache = gamipress_get_cache( "{$trigger}_{$specific_id}_listeners_count", false );

            // If result already cached, return it
            if( $cache !== false ) {
                $listeners_count = absint( $cache );
            } else {
                $listeners_count = $wpdb->get_var( $wpdb->prepare(
                    "SELECT COUNT(*)
                    FROM   {$posts} AS p
                    LEFT JOIN {$postmeta} AS pm ON ( p.ID = pm.post_id AND pm.meta_key = '_gamipress_trigger_type' )
                    LEFT JOIN {$postmeta} AS pm2 ON ( p.ID = pm2.post_id AND pm2.meta_key = '_gamipress_achievement_post' )
                    WHERE p.post_status = 'publish'
					AND p.post_type IN ( '" . implode( "', '", gamipress_get_requirement_types_slugs() ) . "' )
					AND pm.meta_value = %s
					AND pm2.meta_value = %s",
                    $trigger,
                    $specific_id
                ) );

                // Cache listeners count
                gamipress_save_cache( "{$trigger}_{$specific_id}_listeners_count", $listeners_count );
            }

		}

	}

	$has_listeners = ( absint( $listeners_count ) > 0 );

    /**
     * Filter to override if trigger has listeners
     *
     * @since 1.0.8
     *
     * @param bool 	    $has_listeners
     * @param string 	$trigger
     * @param integer 	$site_id
     * @param array 	$args
     *
     * @return bool
     */
	return apply_filters( 'gamipress_trigger_has_listeners', $has_listeners, $trigger, $site_id, $args );
}

/**
 * Get all triggers listeners count
 *
 * @since 1.8.0
 *
 * @return array
 */
function gamipress_get_triggers_listeners_count() {

    $cache = gamipress_get_cache( 'gamipress_triggers_listeners_count', false );

    // If result already cached, return it
    if( is_array( $cache ) ) {
        return $cache;
    }

    global $wpdb;

    $posts  	= GamiPress()->db->posts;
    $postmeta  	= GamiPress()->db->postmeta;

    // Get an array with each trigger count
    $results = $wpdb->get_results(
        "SELECT pm.meta_value AS 'trigger', COUNT(*) AS 'count'
         FROM {$postmeta} AS pm
         LEFT JOIN {$posts} AS p ON ( pm.post_id = p.ID )
         WHERE p.post_status = 'publish'
         AND p.post_type IN ( '" . implode( "', '", gamipress_get_requirement_types_slugs() ) . "' )
         AND pm.meta_key = '_gamipress_trigger_type'
         AND pm.meta_value != '' 
         GROUP BY pm.meta_value"
    );

    $listeners_count = array();

    // Turn the results array into a more accessible associative array
    foreach( $results as $result )
        $listeners_count[$result->trigger] = absint( $result->count );

    // Cache listeners count
    gamipress_save_cache( "gamipress_triggers_listeners_count", $listeners_count );

    /**
     * Filter to override if triggers listeners count
     *
     * @since 1.8.0
     *
     * @param array $listeners_count
     *
     * @return array
     */
    return apply_filters( 'gamipress_get_triggers_listeners_count', $listeners_count );

}

/**
 * Return triggered requirements by a specific trigger
 *
 * @since   1.6.1
 * @updated 1.7.9 Improvement on requirements with specific triggers querying them by the specific ID to reduce the amount of triggered requirements
 * @updated 1.7.9 Added $user_id, $site_id and $args parameters
 *
 * @param int 	    $user_id    The User ID
 * @param string 	$trigger    The trigger type
 * @param int 	    $site_id    The site ID where event has been triggered
 * @param array 	$args       The event arguments
 *
 * @return array
 */
function gamipress_get_triggered_requirements( $user_id, $trigger, $site_id, $args ) {

    $triggered_requirements = array();
    $proceed                = true;
    $cache_key              = "{$trigger}_triggered_requirements";
    $query_args             = array(
        'fields'        => 'ids',
        'post_status'   => 'publish',
        'meta_query'    => array(
            '_gamipress_trigger_type' => $trigger,
        ),
    );

    // If is specific trigger then try to get the attached id
    if( in_array( $trigger, array_keys( gamipress_get_specific_activity_triggers() ) ) ) {

        $specific_id = 0;

        // If isset this key it means $args is a requirement object
        if( isset( $args['achievement_post'] ) ) {
            $specific_id = absint( $args['achievement_post'] );
        } else if( ! empty( $args ) ) {
            $specific_id = gamipress_specific_trigger_get_id( $trigger, $args );
        }

        // If there is a specific id, then try to find the count
        if( $specific_id !== 0 ) {

            $cache_key = "{$trigger}_{$specific_id}_triggered_requirements";

            // Add the achievement post ID
            $query_args['meta_query']['_gamipress_achievement_post'] = $specific_id;

        } else {
            // Set proceed to false since the assigned post is not correctly assigned
            $proceed = false;
        }

    } else if( gamipress_starts_with( $trigger, 'gamipress_unlock_' ) ) {
        // Check if trigger is for unlocking ANY and ALL posts of an achievement type

        $event_trigger = $trigger;

        // Events for unlock an achievement of type or all requires to change the trigger
        if( gamipress_starts_with( $trigger, 'gamipress_unlock_all_' ) ) {
            // Replace "gamipress_unlock_all_{achievement-type}" to "all-achievements"
            $event_trigger = 'all-achievements';
        } else if( gamipress_starts_with( $trigger, 'gamipress_unlock_' ) ) {
            // Replace "gamipress_unlock_{achievement-type}" to "any-achievement"
            $event_trigger = 'any-achievement';
        }

        // Update the trigger with the correct trigger
        $query_args['meta_query']['_gamipress_trigger_type'] = $event_trigger;

        // Get the achievement type
        $achievement_type = str_replace( 'gamipress_unlock_all_', '', $trigger );
        $achievement_type = str_replace( 'gamipress_unlock_', '', $achievement_type );

        // Add the achievement type
        $query_args['meta_query']['_gamipress_achievement_type'] = $achievement_type;

    } else if( gamipress_starts_with( $trigger, 'gamipress_revoke_' ) ) {
        // Check if trigger is for revoking ANY posts of an achievement type

        $event_trigger = $trigger;

        // Get the achievement type
        $achievement_type = str_replace( 'gamipress_revoke_', '', $trigger );

        if( in_array( $achievement_type, gamipress_get_achievement_types_slugs() ) ) {
            // Replace "gamipress_revoke_{achievement-type}" to "revoke-any-achievement"
            $event_trigger = 'revoke-any-achievement';

            // Add the achievement type
            $query_args['meta_query']['_gamipress_achievement_type'] = $achievement_type;
        }

        // Update the trigger with the correct trigger
        $query_args['meta_query']['_gamipress_trigger_type'] = $event_trigger;

    }

    /**
     * Filter to modify triggered requirements cache key
     *
     * @since   1.8.8
     *
     * @param bool 	    $proceed
     * @param int 	    $user_id    The User ID
     * @param string 	$trigger    The trigger type
     * @param int 	    $site_id    The site ID where event has been triggered
     * @param array 	$args       The event arguments
     *
     * @return bool
     */
    $proceed = apply_filters( 'gamipress_get_triggered_requirements_proceed', $proceed, $user_id, $trigger, $site_id, $args );

    /**
     * Filter to modify triggered requirements cache key
     *
     * @since   1.8.8
     *
     * @param string 	$cache_key  The cache key to store cached results
     * @param int 	    $user_id    The User ID
     * @param string 	$trigger    The trigger type
     * @param int 	    $site_id    The site ID where event has been triggered
     * @param array 	$args       The event arguments
     *
     * @return string
     */
    $cache_key = apply_filters( 'gamipress_get_triggered_requirements_cache_key', $cache_key, $user_id, $trigger, $site_id, $args );

    /**
     * Filter to modify triggered requirements query args
     *
     * @since   1.8.8
     *
     * @see gamipress_query_requirements()
     *
     * @param array 	$query_args The query args to query triggered requirements
     * @param int 	    $user_id    The User ID
     * @param string 	$trigger    The trigger type
     * @param int 	    $site_id    The site ID where event has been triggered
     * @param array 	$args       The event arguments
     *
     * @return array
     */
    $query_args = apply_filters( 'gamipress_get_triggered_requirements_query_args', $query_args, $user_id, $trigger, $site_id, $args );

    if( $proceed ) {

        $cache = gamipress_get_cache( $cache_key, false );

        // If result already cached, return it
        if( $cache !== false && is_array( $cache ) ) {
            $triggered_requirements = $cache;
        } else {
            // Get requirements with this query args
            $triggered_requirements = gamipress_query_requirements( $query_args );

            // Cache function result
            gamipress_save_cache( $cache_key, $triggered_requirements );

        }

    }

    /**
     * Filter to modify triggered requirements by a specific trigger
     *
     * @since   1.6.1
     * @updated 1.7.9 Added $user_id, $site_id and $args parameters
     *
     * @param array 	$triggered_requirements
     * @param int 	    $user_id    The User ID
     * @param string 	$trigger    The trigger type
     * @param int 	    $site_id    The site ID where event has been triggered
     * @param array 	$args       The event arguments
     *
     * @return array
     */
    $triggered_requirements = apply_filters( 'gamipress_get_triggered_requirements', $triggered_requirements, $user_id, $trigger, $site_id, $args );

    return $triggered_requirements;

}

/**
 * Sort triggered requirements by rank priority in order to send them ordered to the awards engine
 *
 * @since 1.6.2
 *
 * @param array 	$triggered_requirements
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_sort_triggered_rank_requirements( $triggered_requirements, $user_id, $trigger, $site_id, $args ) {

    $reordered_requirements = array();

    // Loop all triggered requirements
    foreach( $triggered_requirements as $index => $requirement ) {

        // If is rank requirement, add it to the reordered requirements array
        if( gamipress_get_post_type( $requirement->ID ) === 'rank-requirement' ) {

            // Get the requirement's rank ID
            $rank_id = absint( gamipress_get_post_field( 'post_parent', $requirement->ID ) );

            if( $rank_id !== 0 ) {

                // Get the rank's priority and add the requirement to reordered requirements array
                $priority = gamipress_get_rank_priority( $rank_id );

                if( ! isset( $reordered_requirements[$priority] ) )
                    $reordered_requirements[$priority] = array();

                $reordered_requirements[$priority][] = $requirement;

                // Remove requirement from triggered requirements (will be added after again)
                unset( $triggered_requirements[$index] );

            }

        }

    }

    // Append rank requirements reordered to the triggered requirements array
    foreach( $reordered_requirements as $requirements ) {
        $triggered_requirements = array_merge( $triggered_requirements, $requirements );
    }

    return $triggered_requirements;

}
add_filter( 'gamipress_get_triggered_requirements', 'gamipress_sort_triggered_rank_requirements', 10, 5 );

/**
 * Delete cache of a specific trigger
 *
 * @since   1.6.1
 * @updated 1.7.9 Added support to the brand new triggered requirements cache
 *
 * @param string $trigger
 *
 * @return bool
 */
function gamipress_delete_trigger_cache( $trigger ) {

    global $wpdb;

    // Bail if empty trigger
    if( empty( $trigger ) ) {
        return false;
    }

    // Listeners count cache varies if is specific trigger
    if( in_array( $trigger, array_keys( gamipress_get_specific_activity_triggers() ) ) ) {

        // Cache prefix and suffix
        $trigger = sanitize_text_field( $trigger );
        $prefix = "gamipress_cache_{$trigger}_";
        $triggered_suffix = '_triggered_requirements';
        $listeners_suffix = '_listeners_count';

        // Delete listeners count cache for specific trigger

        if( gamipress_is_network_wide_active() ) {
            // Multi site installs
            $wpdb->query( "DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE '{$prefix}%' AND ( meta_key LIKE '%{$triggered_suffix}' OR meta_key LIKE '%{$listeners_suffix}' )");
        } else {
            // Single site installs
            $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%' AND ( option_name LIKE '%{$triggered_suffix}' OR option_name LIKE '%{$listeners_suffix}' )" );
        }

    } else if ( $trigger === 'any-achievement' || $trigger === 'all-achievements' || $trigger === 'revoke-any-achievement' ) {

        // Cache prefix and suffix
        $prefix = 'gamipress_cache_gamipress_unlock_';
        $triggered_suffix = '_triggered_requirements';
        $listeners_suffix = '_listeners_count';

        if( $trigger === 'revoke-any-achievement' ) {
            $prefix = 'gamipress_cache_gamipress_revoke_';
        }

        // Delete listeners count cache for unlock all/specific achievement
        if( gamipress_is_network_wide_active() ) {
            // Multi site installs
            $wpdb->query( "DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE '{$prefix}%' AND ( meta_key LIKE '%{$triggered_suffix}' OR meta_key LIKE '%{$listeners_suffix}' )");
        } else {
            // Single site installs
            $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%' AND ( option_name LIKE '%{$triggered_suffix}' OR option_name LIKE '%{$listeners_suffix}' )" );
        }

    } else {

        // Delete triggered requirements cache
        gamipress_delete_cache( "{$trigger}_triggered_requirements" );

        // Delete listeners count cache for not specific trigger
        gamipress_delete_cache( "{$trigger}_listeners_count" );
    }

    // Delete all listeners count cache
    gamipress_delete_cache( 'gamipress_triggers_listeners_count' );

    return true;

}

/**
 * Wrapper function for returning a user's array of sprung triggers
 *
 * @since  1.0.0
 *
 * @param  integer $user_id The given user's ID
 * @param  integer $site_id The desired Site ID to check
 *
 * @return array            An array of the triggers a user has triggered
 */
function gamipress_get_user_triggers( $user_id = 0, $site_id = 0 ) {

	// Grab all of the user's triggers
	$user_triggers = ( $exists = gamipress_get_user_meta( $user_id, '_gamipress_triggered_triggers' ) ) ? $exists : array( $site_id => array() );

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
 * Get the count of the number of times a user has triggered a particular trigger
 *
 * @since   1.0.0
 * @updated 1.6.2 Added use of gamipress_get_user_triggers() function when $since is 0
 * @updated 1.6.3 Revert back 1.6.2 and full function refactoring to make use of gamipress_get_user_log_count() function and added new filters
 *
 * @param  int      $user_id    The given user's ID
 * @param  string   $trigger    The given trigger we're checking
 * @param  int      $since 	    The since timestamp where retrieve the logs
 * @param  int      $site_id    The desired Site ID to check
 * @param  array    $args       The triggered args or requirement object
 *
 * @return int                  The total number of times a user has triggered the trigger
 */
function gamipress_get_user_trigger_count( $user_id, $trigger, $since = 0, $site_id = 0, $args = array() ) {

	// If not properly upgrade to required version fallback to compatibility function
	if( ! is_gamipress_upgraded_to( '1.4.7' ) ) {
		return gamipress_get_user_trigger_count_old_147(  $user_id, $trigger, $since, $site_id, $args );
	}

	// Set to current site id
	if ( ! $site_id ) {
		$site_id = get_current_blog_id();
    }

    // Setup the meta data to filter the logs count
    $log_meta = array(
        'type'          => 'event_trigger',
        'trigger_type'  => $trigger,
    );

    // If is specific trigger then try to get the attached id
    if( in_array( $trigger, array_keys( gamipress_get_specific_activity_triggers() ) ) ) {

        $specific_id = 0;

        // If isset this key it means $args is a requirement object
        if( isset( $args['achievement_post'] ) ) {
            $specific_id = absint( $args['achievement_post'] );
        } else if( ! empty( $args ) ) {
            $specific_id = gamipress_specific_trigger_get_id( $trigger, $args );
        }

        // If there is a specific id, then try to find the count
        if( $specific_id !== 0 ) {
            $log_meta['achievement_post'] = $specific_id;
        }

    }

    /**
     * Filter to override the meta data to filter the logs count
     *
     * @since   1.6.3
     *
     * @param  array    $log_meta       The meta data to filter the logs count
     * @param  int      $user_id        The given user's ID
     * @param  string   $trigger        The given trigger we're checking
     * @param  int      $since 	        The since timestamp where retrieve the logs
     * @param  int      $site_id        The desired Site ID to check
     * @param  array    $args           The triggered args or requirement object
     *
     * @return array                    The meta data to filter the logs count
     */
    $log_meta = apply_filters( 'gamipress_get_user_trigger_count_log_meta', $log_meta, $user_id, $trigger, $since, $site_id, $args );

    // Get the trigger count
    $trigger_count = gamipress_get_user_log_count( absint( $user_id ), $log_meta, $since );

    /**
     * Filter to override the number of times a user has triggered a particular trigger
     *
     * @since   1.6.3
     *
     * @param  int      $trigger_count  The total number of times a user has triggered the trigger
     * @param  int      $user_id        The given user's ID
     * @param  string   $trigger        The given trigger we're checking
     * @param  int      $since 	        The since timestamp where retrieve the logs
     * @param  int      $site_id        The desired Site ID to check
     * @param  array    $args           The triggered args or requirement object
     *
     * @return int                      The total number of times a user has triggered the trigger
     */
    $trigger_count = apply_filters( 'gamipress_get_user_trigger_count', absint( $trigger_count ), $user_id, $trigger, $since, $site_id, $args );

    // Return the current count for the given trigger
	return absint( $trigger_count );

}

/**
 * Update the user's trigger count for a given trigger by 1
 *
 * @since  1.0.0
 *
 * @param  integer 	$user_id 	The given user's ID
 * @param  string  	$trigger 	The trigger we're updating
 * @param  integer 	$site_id 	The desired Site ID to update
 * @param  array 	$args 		The triggered args
 *
 * @return integer          	The updated trigger count
 */
function gamipress_update_user_trigger_count( $user_id, $trigger, $site_id = 0, $args = array() ) {

	// Set to current site id
	if ( ! $site_id ) {
		$site_id = get_current_blog_id();
    }

	// Get the user triggered triggers
	$user_triggers = gamipress_get_user_triggers( $user_id, $site_id );

	if( isset( $user_triggers[$site_id][$trigger] ) ) {
		// If already have this count, just retrieve it
		$trigger_count = absint( $user_triggers[$site_id][$trigger] );
	} else {
		// Grab the current count directly from the database
		$trigger_count = absint( gamipress_get_user_trigger_count( $user_id, $trigger, 0, $site_id, $args ) );
	}

	// Increase the current count by 1
	$trigger_count += (int) apply_filters( 'gamipress_update_user_trigger_count', 1, $user_id, $trigger, $site_id, $args );

	// Update the triggers array with the new count
	$user_triggers[$site_id][$trigger] = $trigger_count;
	gamipress_update_user_meta( $user_id, '_gamipress_triggered_triggers', $user_triggers );

	// Send back our trigger count for other purposes
	return $trigger_count;

}

/**
 * Reset a user's trigger count for a given trigger to 0 or reset ALL triggers
 *
 * @since  1.0.0
 *
 * @param  integer $user_id The given user's ID
 * @param  string  $trigger The trigger we're updating (or "all" to dump all triggers)
 * @param  integer $site_id The desired Site ID to update (or "all" to dump across all sites)
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
	gamipress_update_user_meta( $user_id, '_gamipress_triggered_triggers', $user_triggers );

}
