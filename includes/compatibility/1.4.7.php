<?php
/**
 * GamiPress 1.4.7 compatibility functions
 *
 * @package     GamiPress\1.4.7
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.4.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get an array of post IDs for achievements that are marked as "hidden"
 *
 * @deprecated
 *
 * @see gamipress_is_achievement_hidden()
 *
 * @since  1.0.0
 *
 * @param  integer $achievement_id Limit the array to a specific id of achievement
 *
 * @return array  An array of hidden achievement post IDs
 */

function gamipress_get_hidden_achievement_by_id( $achievement_id ) {

    // Grab our hidden achievements
    global $wpdb;

    $posts    = GamiPress()->db->posts;
    $postmeta = GamiPress()->db->postmeta;

    //Get hidden achievement posts.
    $hidden_achievements = $wpdb->get_results( $wpdb->prepare(
        "
		 SELECT *
		 FROM {$posts} AS p
		 JOIN {$postmeta} AS pm
		 ON p.ID = pm.post_id
		 WHERE p.ID = %d
		 AND pm.meta_key = '_gamipress_hidden'
		 AND pm.meta_value = 'hidden'
         ",
        $achievement_id));

    // Return our results
    return $hidden_achievements;
}

/**
 * Posts a log entry when a user unlocks any achievement post
 *
 * @since  1.0.0
 * @updated  1.2.8 Added $type
 *
 * @param  string   $type  	    The log type
 * @param  int      $user_id  	The user ID
 * @param  string   $access     Access to this log ( public|private )
 * @param  array    $log_meta   Log meta data
 *
 * @return integer             	The log ID of the newly created log entry
 */
function gamipress_insert_log_old_147( $type = '', $user_id = 0, $access = 'public', $log_meta = array() ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {

        $log_meta['type'] = $type;

        return gamipress_insert_log_old( $user_id, $access, $log_meta );
    }

    // Setup table
    ct_setup_table( 'gamipress_logs' );

    // Post data
    $log_data = array(
        'title'	        => '',
        'description'	=> '',
        'type' 	        => $type,
        'access'	    => $access,
        'user_id'	    => $user_id === 0 ? get_current_user_id() : absint( $user_id ),
        'date'	        => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
    );

    // Auto-generated post title
    $log_data['title'] = gamipress_parse_log_pattern( $log_meta['pattern'], $log_data, $log_meta );

    // Store log entry
    $log_id = ct_insert_object( $log_data );

    // Store log meta data
    if ( $log_id && ! empty( $log_meta ) ) {

        foreach ( (array) $log_meta as $key => $meta ) {

            ct_update_object_meta( $log_id, '_gamipress_' . sanitize_key( $key ), $meta );

        }

    }

    // Hook to add custom data
    do_action( 'gamipress_insert_log', $log_id, $log_data, $log_meta, $user_id );

    ct_reset_setup_table();

    return $log_id;

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
function gamipress_get_user_trigger_count_old_147( $user_id, $trigger, $since = 0, $site_id = 0, $args = array() ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {
        return gamipress_get_user_trigger_count_old(  $user_id, $trigger, $since, $site_id, $args );
    }

    global $wpdb;

    $logs 		= GamiPress()->db->logs;
    $logs_meta 	= GamiPress()->db->logs_meta;

    // Set to current site id
    if ( ! $site_id )
        $site_id = get_current_blog_id();

    // Setup date conditional
    $date = '';

    if( $since !== 0 ) {
        $since = date( 'Y-m-d H:i:s', $since );

        $date = "AND l.date >= '$since'";
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
                "SELECT COUNT(*)
				FROM   {$logs} AS l
				INNER JOIN {$logs_meta} AS lm1 ON ( l.log_id = lm1.log_id )
				INNER JOIN {$logs_meta} AS lm2 ON ( l.log_id = lm2.log_id )
				WHERE l.user_id = %d
					AND l.type = %s
					{$date}
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
            "SELECT COUNT(*)
			FROM   {$logs} AS l
			INNER JOIN {$logs_meta} AS lm ON ( l.log_id = lm.log_id )
			WHERE l.user_id = %d
				AND l.type = %s
				{$date}
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