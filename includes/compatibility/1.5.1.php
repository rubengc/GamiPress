<?php
/**
 * GamiPress 1.5.1 compatibility functions
 *
 * @package     GamiPress\1.5.1
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.5.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function gamipress_pre_init_151() {

    global $wpdb;

    // Setup the P2P tables for backward compatibility
    GamiPress()->db->p2p        = ( property_exists( $wpdb, 'p2p' ) ? $wpdb->p2p : $wpdb->prefix . 'p2p' );
    GamiPress()->db->p2pmeta 	= ( property_exists( $wpdb, 'p2pmeta' ) ? $wpdb->p2pmeta : $wpdb->prefix . 'p2pmeta' );

    // Multisite support
    if( gamipress_is_network_wide_active() ) {

        GamiPress()->db->p2p        = $wpdb->base_prefix . 'p2p';
        GamiPress()->db->p2pmeta 	= $wpdb->base_prefix . 'p2pmeta';

    }

}
add_action( 'init', 'gamipress_pre_init_151', 6 );

/**
 * Replace per site queries to root site when GamiPress is active network wide
 *
 * @since 1.4.0
 *
 * @param string    $request
 * @param WP_Query  $wp_query
 *
 * @return string
 */
function gamipress_network_wide_post_request_151( $request, $wp_query ) {

    global $wpdb;

    if( is_gamipress_upgraded_to( '1.5.1' ) ) {
        return $request;
    }

    // If GamiPress is active network wide and we are not in main site, then filter all queries to our post types
    if(
        gamipress_is_network_wide_active()
        && ! is_main_site()
        && isset( $wp_query->query_vars['post_type'] )
    ) {

        $post_type = $wp_query->query_vars['post_type'];

        if( is_array( $post_type ) ) {
            $post_type = $post_type[0];
        }

        if(
            in_array( $post_type, array( 'points-type', 'achievement-type', 'rank-type' ) )
            || in_array( $post_type, gamipress_get_requirement_types_slugs() )
            || in_array( $post_type, gamipress_get_achievement_types_slugs() )
            || in_array( $post_type, gamipress_get_rank_types_slugs() )
        ) {

            $p2p        = ( property_exists( $wpdb, 'p2p' ) ? $wpdb->p2p : $wpdb->prefix . 'p2p' );
            $p2pmeta 	= ( property_exists( $wpdb, 'p2pmeta' ) ? $wpdb->p2pmeta : $wpdb->prefix . 'p2pmeta' );

            // Replace {prefix}{site}p2p to {prefix}p2p
            $request = str_replace( $p2p, "{$wpdb->base_prefix}p2p", $request );

            // Replace {prefix}{site}p2pmeta to {prefix}p2pmeta
            $request = str_replace( $p2pmeta, "{$wpdb->base_prefix}p2pmeta", $request );
        }

    }

    return $request;

}
add_filter( 'posts_request', 'gamipress_network_wide_post_request_151', 10, 2 );

/**
 * Listener for daily visits
 *
 * Triggers: gamipress_site_visit, gamipress_specific_post_visit
 *
 * @deprecated
 *
 * @since  1.0.0
 *
 * @return void
 */
function gamipress_site_visit_listener_old() {

    // Bail if is an ajax request
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return;
    }

    // Bail if is admin area
    if( is_admin() ) {
        return;
    }

    // Bail if not logged in
    if( ! is_user_logged_in() ) {
        return;
    }

    // Current User ID
    $user_id = get_current_user_id();
    $now = strtotime( date( 'Y-m-d', current_time( 'timestamp' ) ) );

    // Website daily visit
    $count = gamipress_get_user_trigger_count( $user_id, 'gamipress_site_visit', $now );

    // Trigger daily visit action if not triggered today
    if( $count === 0 ) {
        do_action( 'gamipress_site_visit', $user_id );
    }

    global $post;

    if( $post ) {

        // Post daily visit
        $count = gamipress_get_user_trigger_count( $user_id, 'gamipress_specific_post_visit', $now, 0, array( $post->ID, $user_id, $post ) );

        // Trigger daily post visit action if not triggered today
        if( $count === 0 ) {

            // Trigger any post visit
            do_action( 'gamipress_post_visit', $post->ID, $user_id, $post );

            // Trigger specific post visit
            do_action( 'gamipress_specific_post_visit', $post->ID, $user_id, $post );

        }

    }
}
// Since 1.5.1 visits has been tracked through ajax
//add_action( 'wp_head', 'gamipress_site_visit_listener_old' );

/**
 * Listener for user post visits
 *
 * Triggers: gamipress_user_post_visit, gamipress_user_specific_post_visit
 *
 * @deprecated
 *
 * @since  1.2.9
 *
 * @return void
 */
function gamipress_user_post_visit_listener() {

    // Bail if is an ajax request
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return;
    }

    // Bail if is admin area
    if( is_admin() ) {
        return;
    }

    // Current User ID
    $user_id = get_current_user_id();

    global $post;

    if( $post ) {

        $post_author = absint( $post->post_author );

        // Trigger user post visit action to the author if visitor not is the author
        if( $post_author && $post_author !== $user_id ) {
            do_action( 'gamipress_user_post_visit', $post->ID, $post_author, $user_id, $post );
            do_action( 'gamipress_user_specific_post_visit', $post->ID, $post_author, $user_id, $post );
        }

    }

}
// Since 1.5.1 visits has been tracked through ajax
//add_action( 'wp_head', 'gamipress_user_post_visit_listener' );

/* --------------------------
 * Requirement functions
   -------------------------- */

/**
 * Get the sort order for a given requirement
 *
 * @since  1.0.5
 *
 * @param  integer $requirement_id The given requirement's post ID
 *
 * @return integer          The requirement's sort order
 */
function gamipress_get_requirement_menu_order_old( $requirement_id = 0 ) {

    global $wpdb;

    $p2p        = GamiPress()->db->p2p;
    $p2pmeta    = GamiPress()->db->p2pmeta;

    $p2p_id = $wpdb->get_var( $wpdb->prepare( "SELECT p2p_id FROM $p2p WHERE p2p_from = %d", $requirement_id ) );

    $menu_order = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $p2pmeta WHERE p2p_id=%d AND meta_key='order'", $p2p_id ) );

    if ( ! $menu_order || $menu_order === 'NaN' ) {
        $menu_order = '0';
    }

    return $menu_order;

}

/**
 * Helper function for comparing our requirement sort order (used in uasort() in gamipress_create_points_awards_meta_box())
 *
 * @since  1.0.5
 *
 * @param integer $requirement_x The order number of our given requirement
 * @param integer $requirement_y The order number of the requirement we're comparing against
 *
 * @return integer        0 if the order matches, -1 if it's lower, 1 if it's higher
 */
function gamipress_compare_requirements_order_old( $requirement_x = 0, $requirement_y = 0 ) {

    if( ! property_exists( $requirement_x, 'order' ) ) {
        return 0;
    }

    if ( $requirement_x->order == $requirement_y->order ) {
        return 0;
    }

    return ( $requirement_x->order < $requirement_y->order ) ? -1 : 1;

}

/**
 * Get the the ID of a P2P connection (p2p_id) to a given requirement ID (p2p_from)
 *
 * @deprecated
 *
 * @since  1.0.6
 *
 * @param  integer $requirement_id The given post ID
 *
 * @return integer           The resulting connection
 */
function gamipress_get_requirement_connection_id( $requirement_id = 0 ) {

    global $wpdb;

    $p2p = GamiPress()->db->p2p;

    $p2p_id = $wpdb->get_var( $wpdb->prepare( "SELECT p2p_id FROM $p2p WHERE p2p_from = %d ", $requirement_id ) );

    return $p2p_id;

}

/**
 * Get the the ID of a P2P connected object (p2p_to) to a given requirement ID (p2p_from)
 *
 * @deprecated Just used on 1.2.7 upgrade
 *
 * @since  1.0.6
 *
 * @param  integer $requirement_id The given post ID
 *
 * @return integer           The resulting connected post ID
 */
function gamipress_get_requirement_connected_id( $requirement_id = 0 ) {

    global $wpdb;

    $p2p = GamiPress()->db->p2p;

    if( ! gamipress_database_table_exists( $p2p ) ) {
        return 0;
    }

    $p2p_to = $wpdb->get_var( $wpdb->prepare( "SELECT p2p_to FROM $p2p WHERE p2p_from = %d ", $requirement_id ) );

    return $p2p_to;

}

/**
 * Return post assigned requirements
 *
 * Note: This function has been reworked in order to search on p2p tables to retrieve requirements of the given post ID
 *
 * @since 1.0.0
 *
 * @param null $post_id
 * @param $requirement_type
 * @param $post_status
 *
 * @return array|bool
 */
function gamipress_get_assigned_requirements_old( $post_id = null, $requirement_type = '', $post_status = 'publish' ) {

    global $post, $wpdb;

    if( $post_id === null ) {
        $post_id = $post->ID;
    }

    // For full backward compatibility, get the new requirements to check them with old requirements
    $new_requirements = get_posts( array(
        'post_type'         => $requirement_type,
        'post_parent'       => $post_id,
        'post_status'       => $post_status,
        'orderby'			=> 'menu_order',
        'order'				=> 'ASC',
        'posts_per_page'    => -1,
        'suppress_filters'  => false,
    ) );

    // Build an array just with IDs to being compared with old requirements
    $new_requirements_ids = array();

    foreach( $new_requirements as $new_requirement ) {
        $new_requirements_ids[] = $new_requirement->ID;
    }

    // Perform the old function
    $post_type = gamipress_get_post_type( $post_id );

    $p2p        = GamiPress()->db->p2p;
    $p2pmeta    = GamiPress()->db->p2pmeta;

    $requirements = $wpdb->get_results( "SELECT p2p.p2p_from AS ID FROM {$p2p} AS p2p WHERE p2p.p2p_to = {$post_id} AND p2p.p2p_type = '{$requirement_type}-to-{$post_type}'" );

    foreach( $requirements as $key => $requirement ) {

        // Just add not in new requiremetns array
        if( ! in_array( $requirement->ID, $new_requirements_ids ) ) {
            $new_requirements[] = get_post( $requirement->ID );
        }

        //$requirements[$key] = get_post( $requirement->ID );

    }

    // Return new requirements instead
    //return $requirements;
    return $new_requirements;

}

/* --------------------------
 * Points functions
   -------------------------- */

/**
 * Get Points Award Points Type
 *
 * @since  1.0.0
 *
 * @param  integer     $points_award_id The given points award's post ID
 * @return object|bool                 The post object of the points type, or false if none
 */
function gamipress_get_points_award_points_type_old( $points_award_id = 0 ) {

    global $wpdb;

    // Grab the current post ID if no points_award_id was specified
    if ( ! $points_award_id ) {
        global $post;
        $points_award_id = $post->ID;
    }

    // Requirements UI is storing post parents without get 1.5.1 upgrade so first check if post_parent has been assigned yet
    $points_type_id = absint( gamipress_get_post_field( 'post_parent', $points_award_id ) );

    if( $points_type_id !== 0 ) {
        // If has parent, return his post object
        return get_post( $points_type_id );
    }

    $p2p = GamiPress()->db->p2p;

    $points_type_id = absint( $wpdb->get_var( "SELECT p2p.p2p_to FROM {$p2p} AS p2p WHERE p2p.p2p_from = {$points_award_id} AND p2p.p2p_type = 'points-award-to-points-type'" ) );

    if( $points_type_id !== 0 ) {
        // If has parent, return his post object
        return get_post( $points_type_id );
    } else {
        return false;
    }

}

/**
 * Get Points Deduction Points Type
 *
 * @since  1.3.7
 *
 * @param  integer     $points_deduct_id The given points deduct's post ID
 * @return object|bool                 The post object of the points type, or false if none
 */
function gamipress_get_points_deduct_points_type_old( $points_deduct_id = 0 ) {

    global $wpdb;

    // Grab the current post ID if no points_award_id was specified
    if ( ! $points_deduct_id ) {
        global $post;
        $points_deduct_id = $post->ID;
    }

    // Requirements UI is storing post parents without get 1.5.1 upgrade so first check if post_parent has been assigned yet
    $points_type_id = absint( gamipress_get_post_field( 'post_parent', $points_deduct_id ) );

    if( $points_type_id !== 0 ) {
        // If has parent, return his post object
        return get_post( $points_type_id );
    }

    $p2p = GamiPress()->db->p2p;

    $points_type_id = absint( $wpdb->get_var( "SELECT p2p.p2p_to FROM {$p2p} AS p2p WHERE p2p.p2p_from = {$points_deduct_id} AND p2p.p2p_type = 'points-award-to-points-type'" ) );

    if( $points_type_id !== 0 ) {
        // If has parent, return his post object
        return get_post( $points_type_id );
    } else {
        return false;
    }

}

/* --------------------------
 * Achievement functions
   -------------------------- */

/**
 * Get an array of achievements
 *
 * @since  1.0.0
 *
 * @param  array $args An array of our relevant arguments
 * @return array       An array of the queried achievements
 */
function gamipress_get_achievements_old( $args = array() ) {

    // Setup our defaults
    $defaults = array(
        'post_type'                => array_merge( gamipress_get_achievement_types_slugs(), gamipress_get_requirement_types_slugs() ),
        'numberposts'			   => -1,
        'suppress_filters'         => false,
        'achievement_relationship' => 'any',
    );

    $args = wp_parse_args( $args, $defaults );

    // Hook join functions for joining to P2P table to retrieve the parent of an achievement
    if ( isset( $args['parent_of'] ) ) {
        add_filter( 'posts_join', 'gamipress_get_achievements_parents_join' );
        add_filter( 'posts_where', 'gamipress_get_achievements_parents_where', 10, 2 );
    }

    // Hook join functions for joining to P2P table to retrieve the children of an achievement
    if ( isset( $args['children_of'] ) ) {
        add_filter( 'posts_join', 'gamipress_get_achievements_children_join', 10, 2 );
        add_filter( 'posts_where', 'gamipress_get_achievements_children_where', 10, 2 );
        add_filter( 'posts_orderby', 'gamipress_get_achievements_children_orderby' );
    }

    // Get our achievement posts
    $achievements = get_posts( $args );

    // Remove all our filters
    remove_filter( 'posts_join', 'gamipress_get_achievements_parents_join' );
    remove_filter( 'posts_where', 'gamipress_get_achievements_parents_where' );
    remove_filter( 'posts_join', 'gamipress_get_achievements_children_join' );
    remove_filter( 'posts_where', 'gamipress_get_achievements_children_where' );
    remove_filter( 'posts_orderby', 'gamipress_get_achievements_children_orderby' );

    return $achievements;

}

/**
 * Modify the WP_Query Join filter for achievement children
 *
 * @deprecated
 *
 * @since  1.0.0
 *
 * @param  string $join         The query "join" string
 * @param  object $query_object The complete query object
 * @return string 				The updated "join" string
 */
function gamipress_get_achievements_children_join( $join = '', $query_object = null ) {

    $posts    	= GamiPress()->db->posts;
    $p2p 		= GamiPress()->db->p2p;
    $p2pmeta 	= GamiPress()->db->p2pmeta;

    $join .= " LEFT JOIN {$p2p} AS p2p ON p2p.p2p_from = {$posts}.ID";

    //if ( isset( $query_object->query_vars['achievement_relationship'] ) && $query_object->query_vars['achievement_relationship'] !== 'any' ) {
    //$join .= " LEFT JOIN {$p2pmeta} AS p2pm1 ON p2pm1.p2p_id = p2p.p2p_id";
    //}

    $join .= " LEFT JOIN {$p2pmeta} AS p2pm2 ON p2pm2.p2p_id = p2p.p2p_id";

    return $join;

}

/**
 * Modify the WP_Query Where filter for achievement children
 *
 * @deprecated
 *
 * @since  1.0.0
 *
 * @param  string $where        The query "where" string
 * @param  object $query_object The complete query object
 * @return string 				The updated query "where" string
 */
function gamipress_get_achievements_children_where( $where = '', $query_object = null ) {

    global $wpdb;

    // ^^ add required and optional. right now just returns all achievements.

    // Check for required relationship
    //if ( isset( $query_object->query_vars['achievement_relationship'] ) && $query_object->query_vars['achievement_relationship'] === 'required' )
    //$where .= " AND p2pm1.meta_key = 'Required'";

    // Check for optional relationship
    //if ( isset( $query_object->query_vars['achievement_relationship'] ) && $query_object->query_vars['achievement_relationship'] === 'optional' )
    //$where .= " AND p2pm1.meta_key = 'Optional'";

    // Filter by order meta
    $where .= " AND p2pm2.meta_key ='order'";

    $where .= $wpdb->prepare( ' AND p2p.p2p_to = %d', $query_object->query_vars['children_of'] );

    return $where;

}

/**
 * Modify the WP_Query OrderBy filter for achievement children
 *
 * @deprecated
 *
 * @since  1.0.0
 *
 * @param  string $orderby The query "orderby" string
 * @return string 		   The updated "orderby" string
 */
function gamipress_get_achievements_children_orderby( $orderby = '' ) {

    return $orderby = 'p2pm2.meta_value ASC';

}

/**
 * Modify the WP_Query Join filter for achievement parents
 *
 * @deprecated
 *
 * @since  1.0.0
 *
 * @param  string $join The query "join" string
 * @return string 	    The updated "join" string
 */
function gamipress_get_achievements_parents_join( $join = '' ) {

    $posts  = GamiPress()->db->posts;
    $p2p 	= GamiPress()->db->p2p;

    $join .= " LEFT JOIN {$p2p} AS p2p ON p2p.p2p_to = {$posts}.ID";

    return $join;

}

/**
 * Modify the WP_Query Where filter for achievement parents
 *
 * @deprecated
 *
 * @since  1.0.0
 *
 * @param  string $where The query "where" string
 * @param  object $query_object The complete query object
 *
 * @return string        appended sql where statement
 */
function gamipress_get_achievements_parents_where( $where = '', $query_object = null ) {

    global $wpdb;

    $where .= $wpdb->prepare( ' AND p2p.p2p_from = %d', $query_object->query_vars['parent_of'] );

    return $where;

}

function gamipress_update_achievement_type_151( $original_type = '', $new_type = '' ) {

    // Bail if properly upgrade to required version
    if( is_gamipress_upgraded_to( '1.5.1' ) ) {
        return;
    }

    gamipress_update_p2p_achievement_types( $original_type, $new_type );

}
add_action( 'gamipress_update_achievement_type', 'gamipress_update_achievement_type_151', 10, 2 );

/**
 * Change all p2p connections of one achievement type to a new type.
 *
 * @deprecated
 *
 * @since 1.0.0
 *
 * @param string $original_type Original achievement type.
 * @param string $new_type      New achievement type.
 */
function gamipress_update_p2p_achievement_types( $original_type = '', $new_type = '' ) {

    global $wpdb;

    $p2p = GamiPress()->db->p2p;

    $p2p_relationships = array(
        "step-to-{$original_type}" => "step-to-{$new_type}",
        "{$original_type}-to-step" => "{$new_type}-to-step",
        "{$original_type}-to-points-award" => "{$new_type}-to-points-award",
        "{$original_type}-to-points-deduct" => "{$new_type}-to-points-deduct",
        "{$original_type}-to-rank-requirement" => "{$new_type}-to-rank-requirement",
    );

    foreach ( $p2p_relationships as $old => $new ) {
        $wpdb->query( $wpdb->prepare( "UPDATE {$p2p} SET p2p_type = %s WHERE p2p_type = %s", $new, $old ) );
    }

}

/**
 * Returns achievements that may be earned when the given achievement is earned.
 *
 * @since  1.0.0
 * @param  integer $achievement_id The given achievement's post ID
 * @return array                   An array of achievements that are dependent on the given achievement
 */
function gamipress_get_dependent_achievements_old( $achievement_id = 0 ) {

    global $wpdb;

    // Grab the current achievement ID if none specified
    if ( ! $achievement_id ) {
        global $post;
        $achievement_id = $post->ID;
    }

    $posts    	= GamiPress()->db->posts;
    $postmeta 	= GamiPress()->db->postmeta;
    $p2p 		= GamiPress()->db->p2p;

    // Grab posts that can be earned by unlocking the given achievement
    $specific_achievements = $wpdb->get_results( $wpdb->prepare(
        "
		SELECT *
		FROM   {$posts} as posts,
		       {$p2p} as p2p
            WHERE  posts.ID = p2p.p2p_to
		       AND p2p.p2p_from = %d
		",
        $achievement_id
    ) );

    // Grab posts triggered by unlocking any/all of the given achievement's type
    $type_achievements = $wpdb->get_results( $wpdb->prepare(
        "
		SELECT *
		FROM   {$posts} as posts,
		       {$postmeta} as meta
		WHERE  posts.ID = meta.post_id
		       AND meta.meta_key = '_gamipress_achievement_type'
		       AND meta.meta_value = %s
		",
        gamipress_get_post_type( $achievement_id )
    ) );

    // Merge our dependent achievements together
    $achievements = array_merge( $specific_achievements, $type_achievements );

    // Available filter to modify an achievement's dependents
    return apply_filters( 'gamipress_dependent_achievements', $achievements, $achievement_id );
}

/**
 * Returns achievements that must be earned to earn given achievement.
 *
 * @since  1.0.0
 * @param  integer $achievement_id The given achievement's post ID
 * @return array                   An array of achievements that are dependent on the given achievement
 */
function gamipress_get_required_achievements_for_achievement_old( $achievement_id = 0 ) {

    global $wpdb;

    // Grab the current achievement ID if none specified
    if ( ! $achievement_id ) {
        global $post;
        $achievement_id = $post->ID;
    }

    // Don't retrieve requirements if achievement is not earned by steps
    if ( gamipress_get_post_meta( $achievement_id, '_gamipress_earned_by' ) !== 'triggers' )
        return false;

    $posts    	= GamiPress()->db->posts;
    $p2p    	= GamiPress()->db->p2p;
    $p2pmeta    = GamiPress()->db->p2pmeta;

    // Grab our requirements for this achievement
    $requirements = $wpdb->get_results( $wpdb->prepare(
        "
		SELECT   *
		FROM     $posts as posts
		         LEFT JOIN $p2p as p2p
		                   ON p2p.p2p_from = posts.ID
		         LEFT JOIN $p2pmeta AS p2pmeta
		                   ON p2p.p2p_id = p2pmeta.p2p_id
		WHERE    p2p.p2p_to = %d
		         AND p2pmeta.meta_key = %s
		ORDER BY CAST( p2pmeta.meta_value as SIGNED ) ASC
		",
        $achievement_id,
        'order'
    ) );

    return $requirements;

}

/**
 * Get requirements that by some way have been unassigned from their achievement (normally it happens if achievement has been removed but not the requirements)
 *
 * @deprecated Since 1.5.1, requirements has their assigned item in the post_parent field
 *
 * @since  1.0.6
 *
 * @return array|bool                  Array of the requirements, or false if none
 */
function gamipress_get_unassigned_requirements() {

    global $wpdb;

    $posts  = GamiPress()->db->posts;
    $p2p    = GamiPress()->db->p2p;

    $requirements = $wpdb->get_results( "
        SELECT p.ID
        FROM {$posts} AS p
        LEFT JOIN {$p2p} AS p2p
        ON p2p.p2p_from = p.ID
        WHERE p.post_type IN( 'points-award', 'points-deduct', 'step', 'rank-requirement' )
        AND p2p.p2p_from IS NULL
    ", ARRAY_A );

    // If it has results, return them, otherwise return false
    if ( ! empty( $requirements ) )
        return $requirements;
    else
        return false;

}

/* --------------------------
 * Rank functions
   -------------------------- */

function gamipress_update_rank_types_151( $original_type = '', $new_type = '' ) {

    // Bail if properly upgrade to required version
    if( is_gamipress_upgraded_to( '1.5.1' ) ) {
        return;
    }

    gamipress_update_p2p_rank_types( $original_type, $new_type );

}
add_action( 'gamipress_update_rank_type', 'gamipress_update_rank_types_151', 10, 2 );

/**
 * Change all p2p connections of one rank type to a new type.
 *
 * @deprecated
 *
 * @since 1.3.1
 *
 * @param string $original_type Original rank type.
 * @param string $new_type      New rank type.
 */
function gamipress_update_p2p_rank_types( $original_type = '', $new_type = '' ) {

    global $wpdb;

    $p2p = GamiPress()->db->p2p;

    $p2p_relationships = array(
        "rank-requirement-to-{$original_type}" => "rank-requirement-to-{$new_type}",
        "{$original_type}-to-rank-requirement" => "{$new_type}-to-rank-requirement",
        "{$original_type}-to-points-award" => "{$new_type}-to-points-award",
        "{$original_type}-to-points-deduct" => "{$new_type}-to-points-deduct",
    );

    foreach ( $p2p_relationships as $old => $new ) {
        $wpdb->query( $wpdb->prepare( "UPDATE $p2p SET p2p_type = %s WHERE p2p_type = %s", $new, $old ) );
    }

}

/**
 * Get rank requirement's rank
 *
 * @since  1.3.1
 *
 * @param  integer     $rank_requirement_id The given rank requirement's post ID
 *
 * @return object|bool                      The post object of the rank, or false if none
 */
function gamipress_get_rank_requirement_rank_old( $rank_requirement_id = 0 ) {

    // Grab the current post ID if no rank_requirement_id was specified
    if ( ! $rank_requirement_id ) {
        global $post;
        $rank_requirement_id = $post->ID;
    }

    // Grab our requirement's rank
    $ranks = gamipress_get_ranks( array( 'parent_of' => $rank_requirement_id ) );

    // If it has a rank, return it, otherwise return false
    if ( ! empty( $ranks ) )
        return $ranks[0];
    else
        return false;

}

/**
 * Return registered ranks
 *
 * @since 1.3.1
 *
 * @return array Array of ranks as WP_Post
 */
function gamipress_get_ranks_old( $args = array() ) {

    // Setup our defaults
    $defaults = array(
        'post_type'         => array_merge( gamipress_get_rank_types_slugs(), gamipress_get_requirement_types_slugs() ),
        'numberposts'       => -1,
        'orderby'           => 'menu_order',
        'suppress_filters'  => false,
        'rank_relationship' => 'any',
    );

    $args = wp_parse_args( $args, $defaults );

    // Hook join functions for joining to P2P table to retrieve the parent of a rank
    if ( isset( $args['parent_of'] ) ) {
        add_filter( 'posts_join', 'gamipress_get_ranks_parents_join' );
        add_filter( 'posts_where', 'gamipress_get_ranks_parents_where', 10, 2 );
    }

    // Hook join functions for joining to P2P table to retrieve the children of a rank
    if ( isset( $args['children_of'] ) ) {
        add_filter( 'posts_join', 'gamipress_get_ranks_children_join', 10, 2 );
        add_filter( 'posts_where', 'gamipress_get_ranks_children_where', 10, 2 );
        add_filter( 'posts_orderby', 'gamipress_get_ranks_children_orderby' );
    }

    // Get our ranks posts
    $ranks = get_posts( $args );

    // Remove all our filters
    remove_filter( 'posts_join', 'gamipress_get_ranks_parents_join' );
    remove_filter( 'posts_where', 'gamipress_get_ranks_parents_where' );
    remove_filter( 'posts_join', 'gamipress_get_ranks_children_join' );
    remove_filter( 'posts_where', 'gamipress_get_ranks_children_where' );
    remove_filter( 'posts_orderby', 'gamipress_get_ranks_children_orderby' );

    return $ranks;

}

/**
 * Modify the WP_Query Join filter for rank children
 *
 * @deprecated
 *
 * @since  1.3.1
 *
 * @param  string $join         The query "join" string
 * @param  object $query_object The complete query object
 *
 * @return string 				The updated "join" string
 */
function gamipress_get_ranks_children_join( $join = '', $query_object = null ) {

    $posts      = GamiPress()->db->posts;
    $p2p        = GamiPress()->db->p2p;
    $p2pmeta    = GamiPress()->db->p2pmeta;

    $join .= " LEFT JOIN {$p2p} AS p2p ON p2p.p2p_from = {$posts}.ID";

    //if ( isset( $query_object->query_vars['rank_relationship'] ) && $query_object->query_vars['rank_relationship'] != 'any' )
    //$join .= " LEFT JOIN {$p2pmeta} AS p2pm1 ON p2pm1.p2p_id = p2p.p2p_id";

    $join .= " LEFT JOIN {$p2pmeta} AS p2pm2 ON p2pm2.p2p_id = p2p.p2p_id";

    return $join;

}

/**
 * Modify the WP_Query Where filter for rank children
 *
 * @deprecated
 *
 * @since  1.3.1
 *
 * @param  string $where        The query "where" string
 * @param  object $query_object The complete query object
 *
 * @return string 				The updated query "where" string
 */
function gamipress_get_ranks_children_where( $where = '', $query_object = null ) {

    global $wpdb;

    // ^^ add required and optional. right now just returns all ranks.

    // Check for required relationship
    //if ( isset( $query_object->query_vars['rank_relationship'] ) && $query_object->query_vars['rank_relationship'] == 'required' )
    //$where .= " AND p2pm1.meta_key ='Required'";

    // Check for optional relationship
    //if ( isset( $query_object->query_vars['rank_relationship'] ) && $query_object->query_vars['rank_relationship'] == 'optional' )
    //$where .= " AND p2pm1.meta_key ='Optional'";


    // Filter by order meta
    $where .= " AND p2pm2.meta_key ='order'";

    $where .= $wpdb->prepare( ' AND p2p.p2p_to = %d', $query_object->query_vars['children_of'] );

    return $where;

}

/**
 * Modify the WP_Query OrderBy filter for rank children
 *
 * @deprecated
 *
 * @since  1.3.1
 *
 * @param  string $orderby The query "orderby" string
 *
 * @return string 		   The updated "orderby" string
 */
function gamipress_get_ranks_children_orderby( $orderby = '' ) {

    return $orderby = 'p2pm2.meta_value ASC';

}

/**
 * Modify the WP_Query Join filter for rank parents
 *
 * @deprecated
 *
 * @since  1.3.1
 *
 * @param  string $join The query "join" string
 *
 * @return string 	    The updated "join" string
 */
function gamipress_get_ranks_parents_join( $join = '' ) {

    $posts  = GamiPress()->db->posts;
    $p2p    = GamiPress()->db->p2p;

    $join .= " LEFT JOIN {$p2p} AS p2p ON p2p.p2p_to = {$posts}.ID";

    return $join;

}

/**
 * Modify the WP_Query Where filter for rank parents
 *
 * @deprecated
 *
 * @since  1.3.1
 *
 * @param  string $where The query "where" string
 * @param  object $query_object The complete query object
 *
 * @return string        appended sql where statement
 */
function gamipress_get_ranks_parents_where( $where = '', $query_object = null ) {

    global $wpdb;

    $where .= $wpdb->prepare( ' AND p2p.p2p_from = %d', $query_object->query_vars['parent_of'] );

    return $where;

}