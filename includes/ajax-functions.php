<?php
/**
 * Ajax Functions
 *
 * @package     GamiPress\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax Helper for returning achievements
 *
 * @since 1.0.0
 * @return void
 */
function gamipress_ajax_get_achievements() {
	global $blog_id;

	// Setup our AJAX query vars
	$type       = isset( $_REQUEST['type'] )       ? $_REQUEST['type']       : false;
	$limit      = isset( $_REQUEST['limit'] )      ? $_REQUEST['limit']      : false;
	$offset     = isset( $_REQUEST['offset'] )     ? $_REQUEST['offset']     : false;
	$filter     = isset( $_REQUEST['filter'] )     ? $_REQUEST['filter']     : false;
	$search     = isset( $_REQUEST['search'] )     ? $_REQUEST['search']     : false;
	$user_id    = isset( $_REQUEST['user_id'] )    ? $_REQUEST['user_id']    : false;
	$orderby    = isset( $_REQUEST['orderby'] )    ? $_REQUEST['orderby']    : false;
	$order      = isset( $_REQUEST['order'] )      ? $_REQUEST['order']      : false;
	$wpms       = isset( $_REQUEST['wpms'] )       ? $_REQUEST['wpms']       : false;
	$include    = isset( $_REQUEST['include'] )    ? $_REQUEST['include']    : array();
	$exclude    = isset( $_REQUEST['exclude'] )    ? $_REQUEST['exclude']    : array();
	$meta_key   = isset( $_REQUEST['meta_key'] )   ? $_REQUEST['meta_key']   : '';
	$meta_value = isset( $_REQUEST['meta_value'] ) ? $_REQUEST['meta_value'] : '';

	// Get the current user if one wasn't specified
	if( ! $user_id )
		$user_id = get_current_user_id();

	// Setup template vars
	$template_args = array(
		'thumbnail' => isset( $_REQUEST['thumbnail'] ) ? $_REQUEST['thumbnail'] : 'yes',
		'excerpt'	=> isset( $_REQUEST['excerpt'] ) ? $_REQUEST['excerpt'] : 'yes',
		'steps'	    => isset( $_REQUEST['steps'] ) ? $_REQUEST['steps'] : 'yes',
		'earners'	=> isset( $_REQUEST['earners'] ) ? $_REQUEST['earners'] : 'no',
		'user_id' 	=> $user_id,
	);

	// Convert $type to properly support multiple achievement types
	if ( 'all' == $type ) {
		$type = gamipress_get_achievement_types_slugs();

		// Drop points awards from our list of "all" achievements
		$points_award_key = array_search( 'points-award', $type );
		if ( $points_award_key ) {
			unset( $type[$points_award_key] );
		}

		// Drop steps from our list of "all" achievements
		$step_key = array_search( 'step', $type );
		if ( $step_key ) {
			unset( $type[$step_key] );
		}
	} else {
		$type = explode( ',', $type );
	}

	// Build $include array
	if ( ! is_array( $include ) ) {
		$include = explode( ',', $include );
	}

	// Build $exclude array
	if ( ! is_array( $exclude ) ) {
		$exclude = explode( ',', $exclude );
	}

    // Initialize our output and counters
    $achievements = '';
    $achievement_count = 0;
    $query_count = 0;

    // Grab our hidden badges (used to filter the query)
	$hidden = gamipress_get_hidden_achievement_ids( $type );

	// If we're polling all sites, grab an array of site IDs
	if( $wpms && $wpms != 'false' )
		$sites = gamipress_get_network_site_ids();
	// Otherwise, use only the current site
	else
		$sites = array( $blog_id );

	// Loop through each site (default is current site only)
	foreach( $sites as $site_blog_id ) {

		// If we're not polling the current site, switch to the site we're polling
		if ( $blog_id != $site_blog_id ) {
			switch_to_blog( $site_blog_id );
		}

		// Grab user earned achievements (used to filter the query)
		$earned_ids = gamipress_get_user_earned_achievement_ids( $user_id, $type );

		// Query Achievements
		$args = array(
			'post_type'      =>	$type,
			'orderby'        =>	$orderby,
			'order'          =>	$order,
			'posts_per_page' =>	$limit,
			'offset'         => $offset,
			'post_status'    => 'publish',
			'post__not_in'   => array_diff( $hidden, $earned_ids )
		);

		// Filter - query completed or non completed achievements
		if ( $filter == 'completed' ) {
			$args[ 'post__in' ] = $earned_ids;
		}elseif( $filter == 'not-completed' ) {
			$args[ 'post__not_in' ] = array_merge( $hidden, $earned_ids );
		}

		if ( '' !== $meta_key && '' !== $meta_value ) {
			$args[ 'meta_key' ] = $meta_key;
			$args[ 'meta_value' ] = $meta_value;
		}

		// Include certain achievements
		if ( ! empty( $include ) ) {
			$args[ 'post__not_in' ] = array_diff( $args[ 'post__not_in' ], $include );
			$args[ 'post__in' ] = array_merge( $args[ 'post__in' ], $include  );
		}

		// Exclude certain achievements
		if ( ! empty( $exclude ) ) {
			$args[ 'post__not_in' ] = array_merge( $args[ 'post__not_in' ], $exclude );
		}

		// Search
		if ( $search ) {
			$args[ 's' ] = $search;
		}

		// Loop Achievements
		$achievement_posts = new WP_Query( $args );
		$query_count += $achievement_posts->found_posts;
		while ( $achievement_posts->have_posts() ) : $achievement_posts->the_post();

			$achievements .= gamipress_render_achievement( get_the_ID(), $template_args );

			$achievement_count++;

		endwhile;

		// Sanity helper: if we're filtering for complete and we have no
		// earned achievements, $achievement_posts should definitely be false
		/*if ( 'completed' == $filter && empty( $earned_ids ) )
			$achievements = '';*/

		// Display a message for no results
		if ( empty( $achievements ) ) {
			$current = current( $type );
			// If we have exactly one achivement type, get its plural name, otherwise use "achievements"
			$post_type_plural = ( 1 == count( $type ) && ! empty( $current ) ) ? get_post_type_object( $current )->labels->name : __( 'achievements' , 'gamipress' );

			// Setup our completion message
			$achievements .= '<div class="gamipress-no-results">';

			if ( 'completed' == $filter ) {
				$achievements .= '<p>' . sprintf( __( 'No completed %s to display at this time.', 'gamipress' ), strtolower( $post_type_plural ) ) . '</p>';
			} else {
				$achievements .= '<p>' . sprintf( __( 'No %s to display at this time.', 'gamipress' ), strtolower( $post_type_plural ) ) . '</p>';
			}

			$achievements .= '</div><!-- .gamipress-no-results -->';
		}

		if ( $blog_id != $site_blog_id ) {
			// Come back to current blog
			restore_current_blog();
		}

	}

	// Send back our successful response
	wp_send_json_success( array(
		'message'     => $achievements,
		'offset'      => $offset + $limit,
		'query_count' => $query_count,
		'achievement_count' => $achievement_count,
		'type'        => $type,
	) );
}
add_action( 'wp_ajax_gamipress_get_achievements', 'gamipress_ajax_get_achievements' );
add_action( 'wp_ajax_nopriv_gamipress_get_achievements', 'gamipress_ajax_get_achievements' );

/**
 * AJAX Helper for selecting users in Shortcode Embedder
 *
 * @since 1.0.0
 */
function gamipress_ajax_get_users() {

	// If no query was sent, die here
	if ( ! isset( $_REQUEST['q'] ) ) {
		$_REQUEST['q'] = '';
	}

	global $wpdb;

	// Pull back the search string
	$search = esc_sql( like_escape( $_REQUEST['q'] ) );

	$sql = "SELECT ID, user_login FROM {$wpdb->users}";

	// Build our query
	if ( !empty( $search ) ) {
		$sql .= " WHERE user_login LIKE '%{$search}%'";
	}

	if( empty( $_REQUEST['q'] ) ) {
		$sql .= " LIMIT 10";
	}

	// Fetch our results (store as associative array)
	$results = $wpdb->get_results( $sql, 'ARRAY_A' );

	// Return our results
	wp_send_json_success( $results );
}
add_action( 'wp_ajax_gamipress_get_users', 'gamipress_ajax_get_users' );

/**
 * AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_ajax_get_posts() {
	global $wpdb;

	// Pull back the search string
	$search = isset( $_REQUEST['q'] ) ? like_escape( $_REQUEST['q'] ) : '';

	// Post type conditional
	$post_type = ( isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] :  array( 'post', 'page' ) );

	if ( is_array( $post_type ) ) {
		$post_type = sprintf( 'AND p.post_type IN(\'%s\')', implode( "','", $post_type ) );
	} else {
		$post_type = sprintf( 'AND p.post_type = \'%s\'', $post_type );
	}

	$results = $wpdb->get_results( $wpdb->prepare(
		"
		SELECT p.ID, p.post_title
		FROM   $wpdb->posts AS p
		WHERE  p.post_title LIKE %s
		       {$post_type}
		       AND p.post_status = 'publish'
		",
		"%%{$search}%%"
	) );

	// Return our results
	wp_send_json_success( $results );
}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_ajax_get_posts' );

/**
 * AJAX Helper for selecting posts in Shortcode Embedder
 *
 * @since 1.0.0
 */
function gamipress_ajax_get_achievements_options() {
	global $wpdb;

	// Pull back the search string
	$search = isset( $_REQUEST['q'] ) ? like_escape( $_REQUEST['q'] ) : '';
	$achievement_types = isset( $_REQUEST['post_type'] ) && 'all' !== $_REQUEST['post_type']
		? array( esc_sql( $_REQUEST['post_type'] ) )
		: array_diff( gamipress_get_achievement_types_slugs(), array( 'step', 'points-award' ) );
	$post_type = sprintf( 'AND p.post_type IN(\'%s\')', implode( "','", $achievement_types ) );

	$results = $wpdb->get_results( $wpdb->prepare(
		"
		SELECT p.ID, p.post_title
		FROM   $wpdb->posts AS p 
		JOIN $wpdb->postmeta AS pm
		ON p.ID = pm.post_id
		WHERE  p.post_title LIKE %s
		       {$post_type}
		       AND p.post_status = 'publish'
		       AND pm.meta_key = %s
		       AND pm.meta_value = %s
		",
		"%%{$search}%%",
		"_gamipress_hidden",
		"show"
	) );

	// Return our results
	wp_send_json_success( $results );
}
add_action( 'wp_ajax_gamipress_get_achievements_options', 'gamipress_ajax_get_achievements_options' );

/**
 * AJAX helper for getting our posts and returning select options
 *
 * @since   1.0.0
 * @updated 1.0.5
 */
function gamipress_achievement_post_ajax_handler() {

    // Grab our achievement type from the AJAX request
    $requirement_type = $_REQUEST['requirement_type'];

    if( ! in_array( $requirement_type, gamipress_get_requirement_types_slugs() ) ) {
        die();
    }

    $achievement_type = $_REQUEST['achievement_type'];
    $exclude_posts = (array) $_REQUEST['excluded_posts'];

    if( $requirement_type === 'step' ) {
        $requirements = gamipress_get_step_requirements( $_REQUEST['step_id'] );
    } else {
        $requirements = gamipress_get_points_award_requirements( $_REQUEST['points_award_id'] );
    }

    // If we don't have an achievement type, bail now
    if ( empty( $achievement_type ) ) {
        die();
    }

    // Grab all our posts for this achievement type
    $achievements = get_posts( array(
        'post_type'      => $achievement_type,
        'post__not_in'   => $exclude_posts,
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ));

    // Setup our output
    $output = '<option value="">' . __( 'Choose an achievement', 'gamipress') . '</option>';
    foreach ( $achievements as $achievement ) {
        $output .= '<option value="' . $achievement->ID . '" ' . selected( $requirements['achievement_post'], $achievement->ID, false ) . '>' . $achievement->post_title . '</option>';
    }

    // Send back our results and die like a man
    echo $output;
    die();

}
add_action( 'wp_ajax_gamipress_requirement_achievement_post', 'gamipress_achievement_post_ajax_handler' );
