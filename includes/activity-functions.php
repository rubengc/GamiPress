<?php
/**
 * Achievement Activity Functions
 *
 * @package     GamiPress\Activity_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the UNIX timestamp for the last activity on an achievement for a given user
 *
 * @since   1.0.0
 * @updated 1.6.1 Added special support to rank requirements getting last activity from previous earned rank
 *
 * @param  integer $achievement_id  The given achievements post ID
 * @param  integer $user_id  		The given user's ID
 * @return integer           		The UNIX timestamp for the last reported achievement activity
 */
function gamipress_achievement_last_user_activity( $achievement_id = 0, $user_id = 0 ) {

	// Assume the user has no history with this achievement
	$since = 0;

	// Attempt to grab the achievement earned date
	if ( $date_earned = gamipress_get_last_earning_datetime( array( 'user_id' => $user_id, 'post_id' => $achievement_id ) ) ) {

		// Return the achievement date earned
        $since = $date_earned + 1;

    // If hasn't earned it and is a rank requirement, then grab the rank earned date
	} else if( gamipress_get_post_type( $achievement_id ) === 'rank-requirement' ) {

	    $rank = gamipress_get_rank_requirement_rank( $achievement_id );

	    if( $rank ) {

	        // Just get rank earned time if rank is not the lowest priority one (aka default rank)
	        if( ! gamipress_is_lowest_priority_rank( gamipress_get_user_rank_id( $user_id, $rank->post_type ) ) ) {

	            // Set since from previous earned time rank
                $since = gamipress_get_rank_earned_time( $user_id, $rank->post_type ) + 1;
            }
        }


    }

	// Finally, return our time
	return $since;

}
