<?php
/**
 * Rank Functions
 *
 * @package     GamiPress\Rank_Functions
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

    // Assume we are working with an rank object
    $return = true;

    if( gettype($post) === 'string' ) {
        $post_type = $post;
    } else {
        $post_type = get_post_type( $post );
    }

    // If post type is NOT a registered rank type, it cannot be an rank
    if ( ! in_array( $post_type, gamipress_get_rank_types_slugs() ) ) {
        $return = false;
    }

    // If we pass both previous tests, this is a valid rank (with filter to override)
    return apply_filters( 'gamipress_is_rank', $return, $post, $post_type );
}

/**
 * Get GamiPress Rank Types
 *
 * Returns a multidimensional array of slug, single name and plural name for all rank types.
 *
 * @since  1.3.1
 *
 * @return array An array of our registered rank types
 */
function gamipress_get_rank_types() {
    return GamiPress()->rank_types;
}

/**
 * Get GamiPress Rank Type Slugs
 *
 * @since  1.3.1
 *
 * @return array An array of all our registered rank type slugs (empty array if none)
 */
function gamipress_get_rank_types_slugs() {
    // Assume we have no registered rank types
    $rank_type_slugs = array();

    // If we do have any rank types, loop through each and add their slug to our array
    foreach ( GamiPress()->rank_types as $slug => $data ) {
        $rank_type_slugs[] = $slug;
    }

    // Finally, return our data
    return $rank_type_slugs;
}

/**
 * Get the desired Rank Type singular
 *
 * @since  1.3.1
 *
 * @param string $rank_type
 *
 * @return string
 */
function gamipress_get_rank_type_singular( $rank_type = '' ) {

    if( isset( GamiPress()->rank_types[$rank_type] ) ) {
        return GamiPress()->rank_types[$rank_type]['singular_name'];
    }

    return __( 'Rank', 'gamipress' );

}

/**
 * Get the desired Rank Type plural
 *
 * @since  1.3.1
 *
 * @param string $rank_type
 *
 * @return string
 */
function gamipress_get_rank_plural( $rank_type = '' ) {

    if( isset( GamiPress()->rank_types[$rank_type] ) ) {
        return GamiPress()->rank_types[$rank_type]['plural_name'];
    }

    return __( 'Ranks', 'gamipress' );

}

/**
 * Return registered ranks
 *
 * @since 1.3.1
 *
 * @return array Array of ranks as WP_Post
 */
function gamipress_get_ranks( $args = array() ) {

    // Setup our defaults
    $defaults = array(
        'post_type'                => gamipress_get_rank_types_slugs(),
        'orderby'                  => 'menu_order',
        'suppress_filters'         => false,
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
 * @since  1.3.1
 *
 * @param  string $join         The query "join" string
 * @param  object $query_object The complete query object
 *
 * @return string 				The updated "join" string
 */
function gamipress_get_ranks_children_join( $join = '', $query_object = null ) {

    global $wpdb;

    $join .= " LEFT JOIN $wpdb->p2p AS p2p ON p2p.p2p_from = $wpdb->posts.ID";

    if ( isset( $query_object->query_vars['rank_relationship'] ) && $query_object->query_vars['rank_relationship'] != 'any' )
        $join .= " LEFT JOIN $wpdb->p2pmeta AS p2pm1 ON p2pm1.p2p_id = p2p.p2p_id";

    $join .= " LEFT JOIN $wpdb->p2pmeta AS p2pm2 ON p2pm2.p2p_id = p2p.p2p_id";

    return $join;

}

/**
 * Modify the WP_Query Where filter for rank children
 *
 * @since  1.3.1
 *
 * @param  string $where        The query "where" string
 * @param  object $query_object The complete query object
 *
 * @return string 				The updated query "where" string
 */
function gamipress_get_ranks_children_where( $where = '', $query_object ) {

    global $wpdb;

    if ( isset( $query_object->query_vars['rank_relationship'] ) && $query_object->query_vars['rank_relationship'] == 'required' )
        $where .= " AND p2pm1.meta_key ='Required'";

    if ( isset( $query_object->query_vars['rank_relationship'] ) && $query_object->query_vars['rank_relationship'] == 'optional' )
        $where .= " AND p2pm1.meta_key ='Optional'";

    // ^^ TODO, add required and optional. right now just returns all ranks.
    $where .= " AND p2pm2.meta_key ='order'";
    $where .= $wpdb->prepare( ' AND p2p.p2p_to = %d', $query_object->query_vars['children_of'] );

    return $where;

}

/**
 * Modify the WP_Query OrderBy filter for rank children
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
 * @since  1.3.1
 *
 * @param  string $join The query "join" string
 *
 * @return string 	    The updated "join" string
 */
function gamipress_get_ranks_parents_join( $join = '' ) {

    global $wpdb;

    $join .= " LEFT JOIN $wpdb->p2p AS p2p ON p2p.p2p_to = $wpdb->posts.ID";

    return $join;

}

/**
 * Modify the WP_Query Where filter for rank parents
 *
 * @since  1.3.1
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

    $current_rank_id = get_user_meta( $user_id, $meta, true );

    if( ! $current_rank_id ) {

        // Get lowest priority rank as default rank to all users
        $current_rank_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT p.ID
			FROM {$wpdb->posts} AS p
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

    $rank = get_post( $current_rank_id );

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

    $rank_type = get_post_type( $rank_id );

    // Get lowest priority rank but bigger than current one
    $next_rank_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT p.ID
        FROM {$wpdb->posts} AS p
        WHERE p.post_type = %s
         AND p.post_status = %s
         AND p.menu_order > %s
        ORDER BY menu_order ASC
        LIMIT 1",
        $rank_type,
        'publish',
        get_post_field( 'menu_order', $rank_id )
    ) );

    if( absint( $next_rank_id ) === $rank_id ) {
        $next_rank_id = 0;
    }


    return apply_filters( 'gamipress_get_next_rank_id', absint( $next_rank_id ), $rank_id );

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

    global $wpdb;

    if( $rank_id === null ) {
        $rank_id = get_the_ID();
    }

    $next_rank_id = gamipress_get_next_rank_id( $rank_id );

    if( $next_rank_id === 0 ) {
        return false;
    }

    $rank = get_post( $next_rank_id );

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

    $rank_type = get_post_type( $rank_id );

    // Get highest priority rank but less than current one
    $prev_rank_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT p.ID
        FROM {$wpdb->posts} AS p
        WHERE p.post_type = %s
         AND p.post_status = %s
         AND p.menu_order < %s
        ORDER BY menu_order DESC
        LIMIT 1",
        $rank_type,
        'publish',
        get_post_field( 'menu_order', $rank_id )
    ) );

    if( absint( $prev_rank_id ) === $rank_id ) {
        $prev_rank_id = 0;
    }

    return apply_filters( 'gamipress_get_prev_rank_id', absint( $prev_rank_id ), $rank_id );

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

    global $wpdb;

    if( $rank_id === null ) {
        $rank_id = get_the_ID();
    }

    $prev_rank_id = gamipress_get_prev_rank_id( $rank_id );

    if( $prev_rank_id === 0 ) {
        return false;
    }

    $rank = get_post( $prev_rank_id );

    if( $rank ) {
        return apply_filters( 'gamipress_get_prev_rank', $rank, $prev_rank_id, $rank_id );
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

    if( ! $rank_id ) {
        return false;
    }

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    $old_rank = gamipress_get_user_rank( $user_id );

    $new_rank = get_post( $rank_id );

    // Check if is a valid rank and is not the same rank as current one
    if( $new_rank && gamipress_is_rank( $new_rank ) && $new_rank->ID !== $old_rank->ID ) {

        $meta = "_gamipress_{$new_rank->post_type}_rank";

        // Update the user rank and the time when this rank has been earned
        update_user_meta( $user_id, $meta, $rank_id );
        update_user_meta( $user_id, $meta . 'earned_time', current_time( 'timestamp' ) );

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
        'rank_id' => $new_rank->ID,
        'old_rank_id' => $old_rank->ID,
    );

    $access = 'public';

    // Alter our log pattern if this was an admin action
    if ( $admin_id ) {
        $type = 'rank_award';
        $access = 'private';

        $log_meta['pattern'] = gamipress_get_option( 'rank_awarded_log_pattern', __( '{admin} ranked {user} to {rank_type} {rank}', 'gamipress' ) );
        $log_meta['admin_id'] = $admin_id;
    } else {
        $type = 'rank_earn';
        $log_meta['pattern'] = gamipress_get_option( 'rank_earned_log_pattern', __( '{user} ranked to {rank_type} {rank}', 'gamipress' ) );
        $log_meta['achievement_id'] = $achievement_id;
    }

    // Create the log entry
    gamipress_insert_log( $type, $user_id, $access, $log_meta );

}
add_action( 'gamipress_update_user_rank', 'gamipress_log_user_rank', 10, 5 );

/**
 * Get Rank Requirement Rank
 *
 * @since  1.3.1
 *
 * @param  integer     $rank_requirement_id The given rank requirement's post ID
 *
 * @return object|bool                      The post object of the rank, or false if none
 */
function gamipress_get_rank_requirement_rank( $rank_requirement_id = 0 ) {
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

    $earned_time = absint( get_user_meta( $user_id, "_gamipress_{$rank_type}_rank_earned_time", true ) );

    // If user has not earned a rank of this type, try to get the lowest priority rank and get its publish date
    if( $earned_time === 0 ) {

        $rank = gamipress_get_user_rank( $user_id, $rank_type );

        if( $rank ) {
            $earned_time = strtotime( $rank->post_date );
        }

    }

    return $earned_time;

}

/**
 * Get Rank Requirements
 *
 * @since  1.3.1
 *
 * @param  integer     $rank_id The given rank requirement's post ID
 * @return array                An array of post objects with the rank requirements
 */
function gamipress_get_rank_requirements( $rank_id = 0 ) {
    // Grab the current post ID if no rank_id was specified
    if ( ! $rank_id ) {
        global $post;
        $rank_id = $post->ID;
    }

    $rank_type = get_post_type( $rank_id );

    $requirements = get_posts( array(
        'post_type'           => 'rank-requirement',
        'posts_per_page'      => -1,
        'suppress_filters'    => false,
        'connected_direction' => 'to',
        'connected_type'      => 'rank-requirement-to-' . $rank_type,
        'connected_items'     => $rank_id,
    ));

    // Return rank requirements array
    return $requirements;
}

/**
 * Helper function to retrieve an rank post thumbnail
 *
 * @since  1.3.1
 *
 * @param  integer $post_id    The rank's post ID
 * @param  string  $image_size The name of a registered custom image size
 * @param  string  $class      A custom class to use for the image tag
 *
 * @return string              Our formatted image tag
 */
function gamipress_get_rank_post_thumbnail( $post_id = 0, $image_size = 'gamipress-rank', $class = 'gamipress-rank-thumbnail' ) {

    // Get our rank thumbnail
    $image = get_the_post_thumbnail( $post_id, $image_size, array( 'class' => $class ) );

    // If we don't have an image...
    if ( ! $image ) {

        // Grab our rank type's post thumbnail
        $rank = get_page_by_path( get_post_type(), OBJECT, 'rank-type' );
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
    return get_the_post_thumbnail( $post_id, $image_size, array( 'class' => $class ) );
}

/**
 * Build an unordered list of users who have earned a given rank
 *
 * @since  1.3.1
 *
 * @param  integer $rank_id The given rank's post ID
 *
 * @return string                  Concatenated markup
 */
function gamipress_get_rank_earners_list( $rank_id = 0 ) {

    // Grab our users
    $earners = gamipress_get_rank_earners( $rank_id );
    $output = '';

    // Only generate output if we have earners
    if ( ! empty( $earners ) )  {

        // Loop through each user and build our output
        $output .= '<h4>' . apply_filters( 'gamipress_earners_heading', __( 'People who have reached this rank:', 'gamipress' ) ) . '</h4>';

        $output .= '<ul class="gamipress-rank-earners-list rank-' . $rank_id . '-earners-list">';

        foreach ( $earners as $user ) {
            $user_content = '<li><a href="' . get_author_posts_url( $user->ID ) . '">' . get_avatar( $user->ID ) . '</a></li>';

            $output .= apply_filters( 'gamipress_get_rank_earners_list_user', $user_content, $user->ID );
        }

        $output .= '</ul>';

    }

    // Return our concatenated output
    return apply_filters( 'gamipress_get_rank_earners_list', $output, $rank_id, $earners );
}

/**
 * Get an array of all users who have currently on a given rank
 *
 * @since  1.3.1
 *
 * @param  integer $rank_id The given rank's post ID
 *
 * @return array            Array of user objects
 */
function gamipress_get_rank_earners( $rank_id = 0 ) {

    global $wpdb;

    $rank_type = get_post_type( $rank_id );

    $meta = '_gamipress_rank';

    if( ! empty( $rank_type ) ) {
        $meta = "_gamipress_{$rank_type}_rank";
    }

    $earners = $wpdb->get_col( $wpdb->prepare( "
		SELECT u.user_id
		FROM {$wpdb->usermeta} AS u
		WHERE  meta_key = %s
		       AND meta_value LIKE %s
		GROUP BY u.user_id
	",
        $meta,
        $rank_id
    ) );

    // Build an array of wp users based of IDs found
    $earned_users = array();

    foreach( $earners as $earner_id ) {
        $earned_users[] = new WP_User( $earner_id );
    }

    return $earned_users;

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

    if ( gamipress_rank_type_changed( $post_args ) ) {

        $original_type = get_post( $post_args['ID'] )->post_name;
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
    gamipress_update_p2p_rank_types( $original_type, $new_type );
    gamipress_update_earned_meta_rank_types( $original_type, $new_type );
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
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'post_type'      => $original_type,
        'fields'         => 'id',
    ) );

    foreach ( $items as $item ) {
        set_post_type( $item->ID, $new_type );
    }

}

/**
 * Change all p2p connections of one rank type to a new type.
 *
 * @since 1.3.1
 *
 * @param string $original_type Original rank type.
 * @param string $new_type      New rank type.
 */
function gamipress_update_p2p_rank_types( $original_type = '', $new_type = '' ) {

    global $wpdb;

    $p2p_relationships = array(
        "rank-requirement-to-{$original_type}" => "rank-requirement-to-{$new_type}",
        "{$original_type}-to-rank-requirement" => "{$new_type}-to-rank-requirement",
        "{$original_type}-to-points-award" => "{$new_type}-to-points-award",
    );

    foreach ( $p2p_relationships as $old => $new ) {
        $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->p2p SET p2p_type = %s WHERE p2p_type = %s", $new, $old ) );
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
        array(
            'post_type' => $new_type
        ),
        array(
            'post_type' => $original_type
        )
    );

    global $wpdb;

    $wpdb->get_results( $wpdb->prepare(
        "
		UPDATE $wpdb->usermeta
		SET meta_key = %s
		WHERE meta_key = %s
		",
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