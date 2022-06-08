<?php
/**
 * Rank Functions
 *
 * @package     GamiPress\Rank_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.3.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Check if post is a registered GamiPress rank.
 *
 * @since  1.3.1
 *
 * @param  object|int|string $post Post object or ID.
 * @return bool                    True if post is an rank, otherwise false.
 */
function gamipress_is_rank( $post = null ) {

    // Assume we are working with a rank object
    $return = true;

    if( gettype($post) === 'string' ) {
        $post_type = $post;
    } else {
        $post_type = gamipress_get_post_type( $post );
    }

    // If post type is NOT a registered rank type, it cannot be an rank
    if ( ! in_array( $post_type, gamipress_get_rank_types_slugs() ) ) {
        $return = false;
    }

    // If we pass both previous tests, this is a valid rank (with filter to override)
    return apply_filters( 'gamipress_is_rank', $return, $post, $post_type );

}

/**
 * Return registered ranks
 *
 * @since 1.3.1
 *
 * @return array Array of ranks as WP_Post
 */
function gamipress_get_ranks( $args = array() ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.5.1' ) ) {
        return gamipress_get_ranks_old( $args );
    }

    // Setup our defaults
    $defaults = array(
        'post_type'         => array_merge( gamipress_get_rank_types_slugs(), gamipress_get_requirement_types_slugs() ),
        'numberposts'       => -1,
        'orderby'           => 'menu_order',
        'suppress_filters'  => false,
        'rank_relationship' => 'any',
    );

    $args = wp_parse_args( $args, $defaults );

    // Since 1.5.1, requirements has their parent stored in the post_parent field, so it isn't required at all
    if ( isset( $args['parent_of'] ) ) {

        $post_parent = absint( gamipress_get_post_field( 'post_parent', $args['parent_of'] ) );

        if( $post_parent === 0 ) {
            return array();
        }

        $args['post__in'] = array( $post_parent );

    }

    // Since 1.5.1, requirements has their parent stored in the post_parent field, so it isn't required at all
    if ( isset( $args['children_of'] ) ) {

        $args['post_parent']    = $args['children_of'];

        // When looking to get rank requirements, order is important to sequential requirements
        $args['orderby']        = 'menu_order';
        $args['order']          = 'ASC';

    }

    // Get our ranks posts
    $ranks = get_posts( $args );

    return $ranks;

}

/**
 * Return user current rank id
 *
 * @since 1.3.1
 *
 * @param integer   $user_id    The given user's ID
 * @param string    $rank_type  The rank type
 *
 * @return integer
 */
function gamipress_get_user_rank_id( $user_id = null, $rank_type = '' ) {

    global $wpdb;

    if( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    $meta = '_gamipress_rank';

    if( ! empty( $rank_type ) ) {
        $meta = "_gamipress_{$rank_type}_rank";
    }

    $current_rank_id = gamipress_get_user_meta( $user_id, $meta );

    if( ! $current_rank_id ) {

        $posts  = GamiPress()->db->posts;

        // Get lowest priority rank as default rank to all users
        $current_rank_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT p.ID
			FROM {$posts} AS p
			WHERE p.post_type = %s
			 AND p.post_status = %s
			ORDER BY menu_order ASC
			LIMIT 1",
            $rank_type,
            'publish'
        ) );

    }

    return apply_filters( 'gamipress_get_user_rank_id', absint( $current_rank_id ), $user_id );

}

/**
 * Return user current rank
 *
 * @since 1.3.1
 *
 * @param integer   $user_id    The given user's ID
 * @param string    $rank_type  The rank type
 *
 * @return bool|WP_Post
 */
function gamipress_get_user_rank( $user_id = null, $rank_type = '' ) {

    global $wpdb;

    if( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    $current_rank_id = gamipress_get_user_rank_id( $user_id, $rank_type );

    $rank = gamipress_get_post( $current_rank_id );

    if( $rank ) {
        return apply_filters( 'gamipress_get_user_rank', $rank, $user_id, $current_rank_id );
    }

    return false;

}

/**
 * Return user next rank id
 *
 * @since 1.3.1
 *
 * @param integer   $user_id    The given user's ID
 * @param string    $rank_type  The rank type
 *
 * @return integer
 */
function gamipress_get_next_user_rank_id( $user_id = null, $rank_type = '' ) {

    global $wpdb;

    if( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    $current_rank_id = gamipress_get_user_rank_id( $user_id, $rank_type );

    $next_rank_id = gamipress_get_next_rank_id( $current_rank_id );

    return apply_filters( 'gamipress_get_next_user_rank_id', absint( $next_rank_id ), $user_id, $rank_type, $current_rank_id );

}

/**
 * Return user next rank
 *
 * @since 1.3.1
 *
 * @param integer   $user_id    The given user's ID
 * @param string    $rank_type  The rank type
 *
 * @return bool|WP_Post
 */
function gamipress_get_next_user_rank( $user_id = null, $rank_type = '' ) {

    global $wpdb;

    if( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    $current_rank_id = gamipress_get_user_rank_id( $user_id, $rank_type );

    $next_rank = gamipress_get_next_rank( $current_rank_id );

    if( $next_rank ) {
        return apply_filters( 'gamipress_get_next_user_rank', $next_rank, $user_id, $rank_type, $current_rank_id );
    }

    return false;

}

/**
 * Return next rank id based of given rank priority
 *
 * @since 1.3.1
 *
 * @param integer $rank_id The given rank's ID
 *
 * @return integer
 */
function gamipress_get_next_rank_id( $rank_id = null ) {

    global $wpdb;

    if( $rank_id === null ) {
        $rank_id = get_the_ID();
    }

    $cache = gamipress_get_cache( 'next_rank_id', array(), false );

    // If result already cached, return it
    if( isset( $cache[$rank_id] ) ) {
        return $cache[$rank_id];
    }

    $rank_type = gamipress_get_post_type( $rank_id );

    $posts  = GamiPress()->db->posts;

    // Get lowest priority rank but bigger than current one
    $next_rank_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT p.ID
        FROM {$posts} AS p
        WHERE p.post_type = %s
         AND p.post_status = %s
         AND p.menu_order > %s
        ORDER BY menu_order ASC
        LIMIT 1",
        $rank_type,
        'publish',
        gamipress_get_post_field( 'menu_order', $rank_id )
    ) );

    $next_rank_id = absint( $next_rank_id );

    if( $next_rank_id === $rank_id ) {
        $next_rank_id = 0;
    }

    $next_rank_id = apply_filters( 'gamipress_get_next_rank_id', $next_rank_id, $rank_id );

    // Cache function result
    $cache[$rank_id] = $next_rank_id;

    gamipress_set_cache( 'next_rank_id', $cache );

    return $next_rank_id;

}

/**
 * Return next rank based of given rank priority
 *
 * @since 1.3.1
 *
 * @param integer $rank_id The given rank's ID
 *
 * @return bool|WP_Post
 */
function gamipress_get_next_rank( $rank_id = null ) {

    if( $rank_id === null ) {
        $rank_id = get_the_ID();
    }

    $next_rank_id = gamipress_get_next_rank_id( $rank_id );

    if( $next_rank_id === 0 ) {
        return false;
    }

    $rank = gamipress_get_post( $next_rank_id );

    if( $rank ) {
        return apply_filters( 'gamipress_get_next_rank', $rank, $next_rank_id, $rank_id );
    }

    return false;

}

/**
 * Return previous rank id based of given rank priority
 *
 * @since 1.3.1
 *
 * @param integer $rank_id The given rank's ID
 *
 * @return integer
 */
function gamipress_get_prev_rank_id( $rank_id = null ) {

    global $wpdb;

    if( $rank_id === null ) {
        $rank_id = get_the_ID();
    }

    $cache = gamipress_get_cache( 'prev_rank_id', array(), false );

    // If result already cached, return it
    if( isset( $cache[$rank_id] ) ) {
        return $cache[$rank_id];
    }

    $rank_type = gamipress_get_post_type( $rank_id );

    $posts  = GamiPress()->db->posts;

    // Get highest priority rank but less than current one
    $prev_rank_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT p.ID
        FROM {$posts} AS p
        WHERE p.post_type = %s
         AND p.post_status = %s
         AND p.menu_order < %s
        ORDER BY menu_order DESC
        LIMIT 1",
        $rank_type,
        'publish',
        gamipress_get_post_field( 'menu_order', $rank_id )
    ) );

    $prev_rank_id = absint( $prev_rank_id );

    if( $prev_rank_id === $rank_id ) {
        $prev_rank_id = 0;
    }

    $prev_rank_id = apply_filters( 'gamipress_get_prev_rank_id', $prev_rank_id, $rank_id );

    // Cache function result
    $cache[$rank_id] = $prev_rank_id;

    gamipress_set_cache( 'prev_rank_id', $cache );

    return $prev_rank_id;

}

/**
 * Return previous rank based of given rank priority
 *
 * @since 1.3.1
 *
 * @param integer $rank_id The given rank's ID
 *
 * @return bool|WP_Post
 */
function gamipress_get_prev_rank( $rank_id = null ) {

    if( $rank_id === null ) {
        $rank_id = get_the_ID();
    }

    $prev_rank_id = gamipress_get_prev_rank_id( $rank_id );

    if( $prev_rank_id === 0 ) {
        return false;
    }

    $rank = gamipress_get_post( $prev_rank_id );

    if( $rank ) {
        return apply_filters( 'gamipress_get_prev_rank', $rank, $prev_rank_id, $rank_id );
    }

    return false;

}

/**
 * Return previous user rank
 *
 * @since 1.4.3
 *
 * @param integer $user_id The given user's ID
 * @param string  $rank_type The given rank's type
 *
 * @return bool|int
 */
function gamipress_get_prev_user_rank_id( $user_id = null, $rank_type = '' ) {

    if( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    $old_meta = "_gamipress_{$rank_type}_previous_rank";
    $prev_user_rank_id = absint( gamipress_get_user_meta( $user_id, $old_meta ) );

    // Check if there is a previous rank stored, if not, try to get previous rank of current user rank
    if( $prev_user_rank_id === 0 ) {

        $user_rank_id = gamipress_get_user_rank_id( $user_id, $rank_type );

        // If there is not previous rank and not current rank, return lowest priority rank ID
        if( ! $user_rank_id ) {
            return gamipress_get_lowest_priority_rank_id( $rank_type );
        }

        $prev_user_rank_id = gamipress_get_prev_rank_id( $user_rank_id );
    }

    if( $prev_user_rank_id ) {
        return apply_filters( 'gamipress_get_prev_user_rank_id', $prev_user_rank_id, $user_id, $rank_type );
    }

    return false;

}

/**
 * Helper function to check if a rank is the lowest priority rank (aka the default rank to anyone)
 *
 * @since 1.3.6
 *
 * @param integer $rank_id The given rank's ID
 *
 * @return bool
 */
function gamipress_is_lowest_priority_rank( $rank_id = null ) {

    if( $rank_id === null ) {
        $rank_id = get_the_ID();
    }

    $prev_rank_id = gamipress_get_prev_rank_id( $rank_id );

    // Return true if previous rank is 0 or the same that given one
    return (bool) ( $rank_id === $prev_rank_id || $prev_rank_id === 0 );

}

/**
 * Get the lowest priority rank ID of a rank type
 *
 * @since 1.4.3
 *
 * @param string $rank_type The given rank's type
 *
 * @return int
 */
function gamipress_get_lowest_priority_rank_id( $rank_type = null ) {

    global $wpdb;

    $posts = GamiPress()->db->posts;

    // Get the lowest priority rank
    $rank_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT p.ID
        FROM {$posts} AS p
        WHERE p.post_type = %s
         AND p.post_status = %s
        ORDER BY menu_order ASC
        LIMIT 1",
        $rank_type,
        'publish'
    ) );

    // Return true if previous rank is 0 or the same that given one
    return apply_filters( 'gamipress_get_lowest_priority_rank_id', absint( $rank_id ), $rank_type );

}

/**
 * Award rank to a user
 *
 * @since 1.4.3
 *
 * @param integer 			$rank_id 		The rank is being awarded
 * @param integer 			$user_id 		The given user's ID
 * @param array 			$args			Array of extra arguments
 *
 * @return bool|WP_Post                     WP Post object of newly rank awarded, false if process fails
 */
function gamipress_award_rank_to_user( $rank_id = 0, $user_id = 0, $args = array() ) {

    // Bail if not is a valid rank
    if( ! gamipress_is_rank( $rank_id ) ) {
        return false;
    }

    // Initialize args
    $args = wp_parse_args( $args, array(
        'admin_id' => 0,
        'achievement_id' => null,
    ) );

    // Available action for triggering other processes
    do_action( 'gamipress_award_rank_to_user', $user_id, $rank_id, $args );

    return gamipress_update_user_rank( $user_id, $rank_id, $args['admin_id'], $args['achievement_id'] );

}

/**
 * Revoke rank to a user
 *
 * @since 1.4.3
 *
 * @param integer 			$user_id 		The given user's ID
 * @param integer 			$rank_id 		The rank is being revoked
 * @param integer 			$new_rank_id    The new user rank, if not provided user will get the previous rank
 * @param array 			$args			Array of extra arguments
 *
 * @return bool|WP_Post                     WP Post object of newly rank revoked, false if process fails
 */
function gamipress_revoke_rank_to_user( $user_id = 0, $rank_id = 0, $new_rank_id = 0, $args = array() ) {

    // Bail if not is a valid rank
    if( ! gamipress_is_rank( $rank_id ) ) {
        return false;
    }

    // Initialize args
    $args = wp_parse_args( $args, array(
        'admin_id' => 0,
        'achievement_id' => null,
    ) );

    // If not new rank provided, then get the previous user rank
    if( $new_rank_id === 0 ) {
        $rank_type = gamipress_get_post_type( $rank_id );

        $new_rank_id = gamipress_get_prev_user_rank_id( $user_id, $rank_type );
    }

    // Available action for triggering other processes
    do_action( 'gamipress_revoke_rank_to_user', $user_id, $rank_id, $new_rank_id, $args );

    // Removes the rank from the user earnings table
    gamipress_revoke_achievement_to_user( $rank_id, $user_id );

    // Set the new rank to the user
    return gamipress_update_user_rank( $user_id, $new_rank_id, $args['admin_id'], $args['achievement_id'] );

}

/**
 * Upgrade the given user to the next rank of a rank type
 *
 * @since 1.4.3
 *
 * @param int       $user_id    The given user's ID
 * @param string    $rank_type  The given rank's type
 *
 * @return bool|WP_Post         WP Post object of newly rank upgraded, false if process fails
 */
function gamipress_upgrade_user_to_next_rank( $user_id = 0, $rank_type = '' ) {

    // Bail if not is a valid rank type
    if( ! gamipress_is_rank( $rank_type ) ) {
        return false;
    }

    $user_rank_id = gamipress_get_user_rank_id( $user_id, $rank_type );
    $next_rank_id = gamipress_get_next_rank_id( $user_rank_id );

    // User is already on higher rank, so bail here
    if( $next_rank_id === $user_rank_id ) {
        return false;
    }

    // Award the next rank to the user
    return gamipress_award_rank_to_user( $next_rank_id, $user_id );

}

/**
 * Downgrade the given user to the previous rank of a rank type
 *
 * @since 1.4.3
 *
 * @param int       $user_id    The given user's ID
 * @param string    $rank_type  The given rank's type
 *
 * @return bool|WP_Post         WP Post object of newly rank downgraded, false if process fails
 */
function gamipress_downgrade_user_to_prev_rank( $user_id = 0, $rank_type = '' ) {

    // Bail if not is a valid rank type
    if( ! gamipress_is_rank( $rank_type ) ) {
        return false;
    }

    $user_rank_id = gamipress_get_user_rank_id( $user_id, $rank_type );
    $prev_rank_id = gamipress_get_prev_rank_id( $user_rank_id );

    // User is already on lowest rank, so bail here
    if( $prev_rank_id === $user_rank_id ) {
        return false;
    }

    // Award the previous rank to the user
    return gamipress_revoke_rank_to_user( $user_id, $user_rank_id, $prev_rank_id );

}

/**
 * Update user rank to the given one
 *
 * @since 1.3.1
 *
 * @param integer $user_id        The given user's ID
 * @param integer $rank_id        The new rank the user is being awarded
 * @param integer $admin_id       If being awarded by an admin, the admin's user ID
 * @param integer $achievement_id The achievement that generated the rank upgrade, if applicable
 *
 * @return bool|WP_Post
 */
function gamipress_update_user_rank( $user_id = 0, $rank_id = 0, $admin_id = 0, $achievement_id = null ) {

    if( ! $rank_id )
        return false;

    if( ! $user_id )
        $user_id = get_current_user_id();

    $new_rank = gamipress_get_post( $rank_id );

    // Get user old rank if has one
    $old_rank = gamipress_get_user_rank( $user_id, $new_rank->post_type );

    // If is the same rank, return
    if( $old_rank && $new_rank && $new_rank->ID === $old_rank->ID )
        return $new_rank;

    // Check if is a valid rank and is not the same rank as current one
    if( $new_rank && gamipress_is_rank( $new_rank ) ) {

        $meta = "_gamipress_{$new_rank->post_type}_rank";

        // Update the user rank and the time when this rank has been earned
        gamipress_update_user_meta( $user_id, $meta, $new_rank->ID );
        gamipress_update_user_meta( $user_id, $meta . '_earned_time', current_time( 'timestamp' ) );

        if( $old_rank ) {

            // Stores the user old rank to meet it for revokes
            $old_meta = "_gamipress_{$new_rank->post_type}_previous_rank";
            gamipress_update_user_meta( $user_id, $old_meta, $old_rank->ID );

        }

        // Available action for triggering other processes
        do_action( 'gamipress_update_user_rank', $user_id, $new_rank, $old_rank, $admin_id, $achievement_id );

        // Maybe award some points-based achievements
        foreach ( gamipress_get_rank_based_achievements() as $achievement ) {
            gamipress_maybe_award_achievement_to_user( $achievement->ID, $user_id );
        }

        return $new_rank;
    }

    return false;

}

/**
 * Register a user's updated rank as user earning
 *
 * @since 1.3.1
 *
 * @param integer $user_id        The user ID
 * @param WP_Post $new_rank       New rank post object
 * @param WP_Post $old_rank       Old rank post object
 * @param integer $admin_id       An admin ID (if admin-awarded)
 * @param integer $achievement_id Achievement that has been triggered it
 */
function gamipress_register_user_rank_earning( $user_id, $new_rank, $old_rank, $admin_id = 0, $achievement_id = null ) {

    // Setup our achievement object
    $achievement_object = gamipress_build_achievement_object( $new_rank->ID );

    // Update user's earned achievements
    gamipress_update_user_achievements( array( 'user_id' => $user_id, 'new_achievements' => array( $achievement_object ) ) );

}
add_action( 'gamipress_update_user_rank', 'gamipress_register_user_rank_earning', 10, 5 );

/**
 * Log a user's updated rank
 *
 * @since 1.3.1
 *
 * @param integer $user_id        The user ID
 * @param WP_Post $new_rank       New rank post object
 * @param WP_Post $old_rank       Old rank post object
 * @param integer $admin_id       An admin ID (if admin-awarded)
 * @param integer $achievement_id Achievement that has been triggered it
 */
function gamipress_log_user_rank( $user_id, $new_rank, $old_rank, $admin_id = 0, $achievement_id = null ) {

    $log_meta = array(
        'rank_id' => ( $new_rank ? $new_rank->ID : 0 ),
        'old_rank_id' => ( $old_rank ? $old_rank->ID : 0 ),
    );

    $access = 'public';
    $trigger = '';

    // Alter our log pattern if this was an admin action
    if ( $admin_id ) {
        $type = 'rank_award';
        $access = 'private';

        $log_meta['pattern'] = gamipress_get_option( 'rank_awarded_log_pattern', __( '{admin} ranked {user} to {rank_type} {rank}', 'gamipress' ) );
        $log_meta['admin_id'] = $admin_id;
        $trigger = 'gamipress_award_rank';
    } else {
        $type = 'rank_earn';
        $log_meta['pattern'] = gamipress_get_option( 'rank_earned_log_pattern', __( '{user} ranked to {rank_type} {rank}', 'gamipress' ) );

        if( $achievement_id ) {
            $log_meta['achievement_id'] = $achievement_id;
            $trigger = gamipress_get_post_meta( $achievement_id, '_gamipress_trigger_type' );
        }

        if( empty( $trigger ) ) {
            $trigger = 'gamipress_unlock_rank';
        }
    }

    // Create the log entry
    gamipress_insert_log( $type, $user_id, $access, $trigger, $log_meta );

}
add_action( 'gamipress_update_user_rank', 'gamipress_log_user_rank', 10, 5 );

/**
 * Get rank requirement's rank
 *
 * @since  1.3.1
 *
 * @param  integer     $rank_requirement_id The given rank requirement's post ID
 *
 * @return object|bool                      The post object of the rank, or false if none
 */
function gamipress_get_rank_requirement_rank( $rank_requirement_id = 0 ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.5.1' ) ) {
        return gamipress_get_rank_requirement_rank_old( $rank_requirement_id );
    }

    // Grab the current post ID if no rank requirement ID was specified
    if ( ! $rank_requirement_id ) {
        global $post;
        $rank_requirement_id = $post->ID;
    }

    // The rank requirement's rank is the post parent
    $rank_id = absint( gamipress_get_post_field( 'post_parent', $rank_requirement_id ) );

    // If has parent, return his post object
    if( $rank_id !== 0 )
        return gamipress_get_post( $rank_id );
    else
        return false;
}

/**
 * Get Rank Earned Time
 *
 * @since  1.3.1
 *
 * @param  integer    $user_id      The given rank requirement's post ID
 * @param  string     $rank_type    The given rank requirement's post ID
 *
 * @return integer                  Timestamp of user has earned the last rank of this type, if not, fall back to the rank created date
 */
function gamipress_get_rank_earned_time( $user_id = 0, $rank_type = '' ) {

    $earned_time = absint( gamipress_get_user_meta( $user_id, "_gamipress_{$rank_type}_rank_earned_time" ) );

    // If user has not earned a rank of this type, try to get the lowest priority rank and get its publish date
    if( $earned_time === 0 ) {
        $rank = gamipress_get_user_rank( $user_id, $rank_type );

        if( $rank )
            $earned_time = strtotime( $rank->post_date );
    }

    return $earned_time;

}

/**
 * Get rank requirements
 *
 * @since   1.3.1
 * @updated 1.5.1 Added $post_status parameter
 *
 * @param integer   $rank_id        The given rank requirement's post ID
 * @param string 	$post_status 	The rank requirements status (publish by default)
 *
 * @return array                    An array of post objects with the rank requirements
 */
function gamipress_get_rank_requirements( $rank_id = 0, $post_status = 'publish' ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.5.1' ) ) {
        // gamipress_get_assigned_requirements() has backward compatibility and merge new and old results
        return gamipress_get_assigned_requirements( $rank_id, 'rank-requirement', $post_status );
    }

    // Grab the current post ID if no rank_id was specified
    if ( ! $rank_id ) {
        global $post;
        $rank_id = $post->ID;
    }

    $cache = gamipress_get_cache( 'rank_requirements', array(), false );

    // If result already cached, return it
    if( isset( $cache[$rank_id] ) && isset( $cache[$rank_id][$post_status] ) ) {
        return $cache[$rank_id][$post_status];
    }

    $requirements = get_posts( array(
        'post_type'         => 'rank-requirement',
        'post_parent'       => $rank_id,
        'post_status'       => $post_status,
        'orderby'			=> 'menu_order',
        'order'				=> 'ASC',
        'posts_per_page'    => -1,
        'suppress_filters'  => false,
    ) );

    // Cache function result
    if( ! isset( $cache[$rank_id] ) ){
        $cache[$rank_id] = array();
    }

    $cache[$rank_id][$post_status] = $requirements;

    gamipress_set_cache( 'rank_requirements', $cache );

    // Return rank requirements array
    return $requirements;

}

/**
 * Helper function to retrieve an rank post thumbnail
 *
 * @since  1.3.1
 *
 * @param  integer      $post_id    The rank's post ID
 * @param  string|array $image_size Image size to use. Accepts any valid image size, or
 *                                  an array of width and height values in pixels (in that order).
 *                                  Default 'gamipress-rank'.
 * @param  string       $class      A custom class to use for the image tag
 *
 * @return string              Our formatted image tag
 */
function gamipress_get_rank_post_thumbnail( $post_id = 0, $image_size = 'gamipress-rank', $class = 'gamipress-rank-thumbnail' ) {

    // Get our rank thumbnail
    $image = get_the_post_thumbnail( $post_id, $image_size, array( 'class' => $class ) );

    // If we don't have an image...
    if ( ! $image ) {

        // Grab our rank type's post thumbnail
        $rank = get_page_by_path( gamipress_get_post_type( $post_id ), OBJECT, 'rank-type' );
        $image = is_object( $rank ) ? get_the_post_thumbnail( $rank->ID, $image_size, array( 'class' => $class ) ) : false;

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

            // Available filter: 'gamipress_default_rank_post_thumbnail'
            $default_thumbnail = apply_filters( 'gamipress_default_rank_post_thumbnail', '', $rank, $image_sizes );

            if( ! empty( $default_thumbnail ) ) {
                $image = '<img src="' . $default_thumbnail . '" width="' . $image_sizes['width'] . '" height="' . $image_sizes['height'] . '" class="' . $class . '">';
            }

        }
    }

    // Return our image tag
    return $image;
}

/**
 * Get an array of all users who have currently on a given rank
 *
 * @since   1.3.1
 * @updated 1.6.7 Added $args parameter
 * @updated 1.6.9 Make earner list work on default ranks
 *
 * @param  int      $rank_id    The given rank's post ID
 * @param  array    $args       Array of arguments that modifies the earners list result
 *
 * @return array                Array of user objects
 */
function gamipress_get_rank_earners( $rank_id = 0, $args = array() ) {

    global $wpdb;

    // Setup the meta_key
    $rank_type = gamipress_get_post_type( $rank_id );

    $meta = '_gamipress_rank';

    if( ! empty( $rank_type ) ) {
        $meta = "_gamipress_{$rank_type}_rank";
    }

    // Setup vars
    $from = "{$wpdb->usermeta} AS u ";
    $where = "u.meta_key = '{$meta}' AND u.meta_value = '{$rank_id}' ";

    // On multisite, get earners only of the current site
    if( gamipress_is_network_wide_active() ) {
        $from .= "RIGHT JOIN {$wpdb->usermeta} AS umcap ON ( 
        umcap.user_id = u.user_id 
        AND umcap.meta_key = '" . $wpdb->get_blog_prefix( gamipress_get_original_site_id() ) . "capabilities' 
        ) ";
    }


    // If is lowest priority rank then requires an extra check to get users that doesn't have the rank meta
    if( gamipress_is_lowest_priority_rank( $rank_id ) ) {

        $where .= "OR NOT EXISTS (
            SELECT meta.*
            FROM {$wpdb->usermeta} as meta
            WHERE meta.meta_key = '{$meta}'
            AND meta.user_id = u.user_id
        ) ";

    }

    $group_by = "u.user_id";
    $order_by = '';
    $limit = '';

    // Setup default args
    $defaults = array(
        'limit'     => -1,
        'orderby'   => 'u.user_id',
        'order'     => 'DESC',
    );

    /**
     * Filters the earners args
     *
     * @since 1.6.7
     *
     * @param array     $args       Array of given arguments
     * @param int       $rank_id    The given rank's post ID
     *
     * @return array
     */
    $args = apply_filters( 'gamipress_get_rank_earners_args', $args, $rank_id );

    $args = wp_parse_args( $args, $defaults );

    // Setup limit
    if( $args['limit'] > 0 ) {
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

    // Build an array of wp users based of IDs found
    $earned_users = array();

    foreach( $earners as $earner_id ) {

        if( absint( $earner_id ) === 0 ) {
            continue;
        }

        $earned_users[] = new WP_User( $earner_id );
    }

    return $earned_users;

}

/**
 * Build an unordered list of users who have earned a given rank
 *
 * @since   1.3.1
 * @updated 1.6.7 Added $args parameter
 *
 * @param  int      $rank_id    The given rank's post ID
 * @param  array    $args       Array of arguments that modifies the earners list result
 *
 * @return string               Concatenated markup
 */
function gamipress_get_rank_earners_list( $rank_id = 0, $args = array() ) {

    /**
     * Filters the earners list args
     *
     * @since 1.6.7
     *
     * @param array     $args       Array of given arguments
     * @param int       $rank_id    The given rank's post ID
     *
     * @return array
     */
    $args = apply_filters( 'gamipress_get_rank_earners_list_args', $args, $rank_id );

    // Grab our users
    $earners = gamipress_get_rank_earners( $rank_id, $args );
    $output = '';

    // Only generate output if we have earners
    if ( ! empty( $earners ) )  {

        /**
         * Filters the rank earners heading text
         *
         * @since 1.8.6
         *
         * @param string    $heading_text   The heading text
         * @param int       $rank_id        The given rank's post ID
         * @param array     $args           Array of given arguments
         *
         * @return string
         */
        $heading_text = apply_filters( 'gamipress_rank_earners_heading', __( 'People who have reached this:', 'gamipress' ), $rank_id, $args );

        // Loop through each user and build our output
        $output .= '<h4>' . $heading_text . '</h4>';

        $output .= '<ul class="gamipress-rank-earners-list rank-' . $rank_id . '-earners-list">';

        foreach ( $earners as $user ) {

            $user_url = get_author_posts_url( $user->ID );

            /**
             * Filters the rank earner url
             *
             * @since 1.8.6
             *
             * @param string    $user_url       The user URl, by default the get_author_posts_url()
             * @param int       $user_id        The rendered user ID
             * @param int       $rank_id        The given rank's post ID
             * @param array     $args           Array of given arguments
             *
             * @return string
             */
            $user_url = apply_filters( 'gamipress_rank_earner_user_url', $user_url, $user->ID, $rank_id, $args );

            /**
             * Filters the rank earner display
             *
             * @since 1.8.6
             *
             * @param string    $user_display   The user display, by default the user display name
             * @param int       $user_id        The rendered user ID
             * @param int       $rank_id        The given rank's post ID
             * @param array     $args           Array of given arguments
             *
             * @return string
             */
            $user_display = apply_filters( 'gamipress_rank_earner_user_display', $user->display_name, $user->ID, $rank_id, $args );

            $user_content = '<li>'
                    . '<a href="' . $user_url . '">'
                        . get_avatar( $user->ID )
                        . '<span class="earner-display-name">' . $user_display . '</span>'
                    . '</a>'
                . '</li>';

            /**
             * Filters the earners list user output
             *
             * @since 1.0.0
             * @updated 1.6.7 Added $rank_id and $args arguments
             *
             * @param string    $user_content   User output
             * @param int       $user_id        The rendered user ID
             * @param int       $rank_id        The given rank's post ID
             * @param array     $args           Array of given arguments
             *
             * @return string
             */
            $output .= apply_filters( 'gamipress_get_rank_earners_list_user', $user_content, $user->ID, $rank_id, $args );
        }

        $output .= '</ul>';

    }

    /**
     * Filters the rank earners list output
     *
     * @since 1.0.0
     *
     * @param string    $output         Achievement earners list output
     * @param int       $rank_id        The given rank's post ID
     * @param array     $args           Array of given arguments
     * @param array     $earners        Array of rank earners
     *
     * @return string
     */
    return apply_filters( 'gamipress_get_rank_earners_list', $output, $rank_id, $args, $earners );
}

/**
 * Return the given rank's priority
 *
 * @since  1.3.7
 *
 * @param  integer|WP_Post  $rank_id    The given rank's post ID or rank's post object
 *
 * @return integer                      The given rank's priority
 */
function gamipress_get_rank_priority( $rank_id = 0 ) {

    global $wpdb;

    if( gamipress_get_post_field( 'post_status', $rank_id ) === 'auto-draft' ) {

        $rank_type = gamipress_get_post_type( $rank_id );

        $posts  = GamiPress()->db->posts;

        // Get higher menu order
        $last = $wpdb->get_var( $wpdb->prepare(
            "SELECT p.menu_order
			FROM {$posts} AS p
			WHERE p.post_type = %s
			 AND p.post_status = %s
			ORDER BY menu_order DESC
			LIMIT 1",
            $rank_type,
            'publish'
        ) );

        return absint( $last ) + 1;
    }

    return absint( gamipress_get_post_field( 'menu_order', $rank_id ) );

}

/**
 * Flush rewrite rules whenever an rank type is published.
 *
 * @since 1.3.1
 *
 * @param string $new_status New status.
 * @param string $old_status Old status.
 * @param object $post       Post object.
 */
function gamipress_flush_rewrite_on_published_rank( $new_status, $old_status, $post ) {
    if ( 'rank-type' === $post->post_type && 'publish' === $new_status && 'publish' !== $old_status ) {
        gamipress_flush_rewrite_rules();
    }
}
add_action( 'transition_post_status', 'gamipress_flush_rewrite_on_published_rank', 10, 3 );

/**
 * Update all dependent data if rank type name has changed.
 *
 * @since  1.3.1
 *
 * @param  array $data      Post data.
 * @param  array $post_args Post args.
 * @return array            Updated post data.
 */
function gamipress_maybe_update_rank_type( $data = array(), $post_args = array() ) {

    // Bail if not is main site, on network wide installs ranks are just available on main site
    if( gamipress_is_network_wide_active() && ! is_main_site() ) {
        return $data;
    }

    // Bail if not is a points type
    if( $post_args['post_type'] !== 'rank-type') {
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

    if ( gamipress_rank_type_changed( $post_args ) ) {

        $original_type = gamipress_get_post( $post_args['ID'] )->post_name;
        $new_type = $post_args['post_name'];

        $data['post_name'] = gamipress_update_rank_types( $original_type, $new_type );

        add_filter( 'redirect_post_location', 'gamipress_rank_type_rename_redirect', 99 );

    }

    return $data;
}
add_filter( 'wp_insert_post_data' , 'gamipress_maybe_update_rank_type' , 99, 2 );

/**
 * Check if an rank type name has changed.
 *
 * @since  1.3.1
 *
 * @param  array $post_args Post args.
 * @return bool             True if name has changed, otherwise false.
 */
function gamipress_rank_type_changed( $post_args = array() ) {

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return false;
    }

    $original_post = ( !empty( $post_args['ID'] ) && isset( $post_args['ID'] ) ) ? get_post( $post_args['ID'] ) : null;
    $status = false;

    if ( is_object( $original_post ) ) {
        if (
            'rank-type' === $post_args['post_type']
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
 * Replace all instances of one rank type with another.
 *
 * @since  1.3.1
 *
 * @param  string $original_type Original rank type.
 * @param  string $new_type      New rank type.
 * @return string                New rank type.
 */
function gamipress_update_rank_types( $original_type = '', $new_type = '' ) {

    // Sanity check to prevent alterating core posts
    if ( empty( $original_type ) || in_array( $original_type, array( 'post', 'page', 'attachment', 'revision', 'nav_menu_item' ) ) ) {
        return $new_type;
    }

    gamipress_update_ranks_rank_types( $original_type, $new_type );
    gamipress_update_earned_meta_rank_types( $original_type, $new_type );

    /**
     * Action triggered when a rank type gets updated (called before flush rewrite rules)
     *
     * @since  1.5.1
     *
     * @param  string 	$original_type 	The original type slug.
     * @param  string 	$new_type 		The new type slug.
     */
    do_action( 'gamipress_update_rank_type', $original_type, $new_type );

    gamipress_flush_rewrite_rules();

    return $new_type;

}

/**
 * Change all ranks of one type to a new type.
 *
 * @since 1.3.1
 *
 * @param string $original_type Original rank type.
 * @param string $new_type      New rank type.
 */
function gamipress_update_ranks_rank_types( $original_type = '', $new_type = '' ) {

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
 * Change all earned meta from one rank type to another.
 *
 * @since 1.3.1
 *
 * @param string $original_type Original rank type.
 * @param string $new_type      New rank type.
 */
function gamipress_update_earned_meta_rank_types( $original_type = '', $new_type = '' ) {

    // Setup CT object
    $ct_table = ct_setup_table( 'gamipress_user_earnings' );

    $ct_table->db->update(
        array( 'post_type' => $new_type ),
        array( 'post_type' => $original_type )
    );

    ct_reset_setup_table();

    global $wpdb;

    $wpdb->get_results( $wpdb->prepare(
        "UPDATE {$wpdb->usermeta}
		SET meta_key = %s
		WHERE meta_key = %s",
        "_gamipress_{$new_type}_rank",
        "_gamipress_{$original_type}_rank"
    ) );

}

/**
 * Redirect to include custom rename message.
 *
 * @since  1.3.1
 *
 * @param  string $location Original URI.
 * @return string           Updated URI.
 */
function gamipress_rank_type_rename_redirect( $location = '' ) {

    remove_filter( 'redirect_post_location', __FUNCTION__, 99 );

    return add_query_arg( 'message', 99, $location );

}

/**
 * Filter the "post updated" messages to include support for rank types.
 *
 * @since 1.3.1
 *
 * @param array $messages Array of messages to display.
 *
 * @return array $messages Compiled list of messages.
 */
function gamipress_rank_type_update_messages( $messages ) {

    $messages['rank-type'] = array_fill( 1, 10, __( 'Rank Type saved successfully.', 'gamipress' ) );
    $messages['rank-type']['99'] = sprintf( __('Rank Type renamed successfully. <p>All ranks of this type, and all active and earned user ranks, have been updated <strong>automatically</strong>.</p> All shortcodes, %s, and URIs that reference the old rank type slug must be updated <strong>manually</strong>.', 'gamipress'), '<a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">' . __( 'widgets', 'gamipress' ) . '</a>' );

    return $messages;

}
add_filter( 'post_updated_messages', 'gamipress_rank_type_update_messages' );