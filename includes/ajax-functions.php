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
 *
 * @return void
 */
function gamipress_ajax_get_achievements() {

	// Setup our AJAX query vars
	$type       	= isset( $_REQUEST['type'] )       ? $_REQUEST['type']       : false;
	$limit      	= isset( $_REQUEST['limit'] )      ? $_REQUEST['limit']      : false;
	$offset     	= isset( $_REQUEST['offset'] )     ? $_REQUEST['offset']     : false;
	$filter     	= isset( $_REQUEST['filter'] )     ? $_REQUEST['filter']     : false;
	$search     	= isset( $_REQUEST['search'] )     ? $_REQUEST['search']     : false;
	$current_user   = isset( $_REQUEST['current_user'] )    ? $_REQUEST['current_user']    : false;
	$user_id    	= isset( $_REQUEST['user_id'] )    ? $_REQUEST['user_id']    : false;
	$orderby    	= isset( $_REQUEST['orderby'] )    ? $_REQUEST['orderby']    : false;
	$order      	= isset( $_REQUEST['order'] )      ? $_REQUEST['order']      : false;
	$wpms       	= isset( $_REQUEST['wpms'] )       ? $_REQUEST['wpms']       : false;
	$include    	= isset( $_REQUEST['include'] )    ? $_REQUEST['include']    : array();
	$exclude    	= isset( $_REQUEST['exclude'] )    ? $_REQUEST['exclude']    : array();

	// On network wide active installs, we need to switch to main blog mostly for posts permalinks and thumbnails
	if( gamipress_is_network_wide_active() && ! is_main_site() ) {
		$blog_id = get_current_blog_id();
		switch_to_blog( get_main_site_id() );
	}

	// Force to set current user as user ID
	if( $current_user ) {
		$user_id = get_current_user_id();
	}

	// Get the current user if one wasn't specified
	if( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	// Setup template vars
	$template_args = array(
		'user_id' 	=> $user_id, // User ID on achievement is used to meet to which user apply earned checks
	);

	$achievement_fields = GamiPress()->shortcodes['gamipress_achievement']->fields;

	unset( $achievement_fields['id'] );

	// Loop achievement shortcode fields to pass to the rank template
	foreach( $achievement_fields as $field_id => $field_args ) {

		if( isset( $_REQUEST[$field_id] ) ) {
			$template_args[$field_id] = $_REQUEST[$field_id];
		}

	}

	// Convert $type to properly support multiple achievement types
	if ( 'all' == $type ) {
		$type = gamipress_get_achievement_types_slugs();
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

    // Grab our hidden achievements (used to filter the query)
	$hidden = gamipress_get_hidden_achievement_ids( $type );

	// If we're polling all sites, grab an array of site IDs
	if( $wpms && $wpms !== 'no' )
		$sites = gamipress_get_network_site_ids();
	// Otherwise, use only the current site
	else
		$sites = array( get_current_blog_id() );

	// On network wide active installs, force to just loop main site
	if( gamipress_is_network_wide_active() ) {
		$sites = array( get_main_site_id() );
	}

	// On network wide active installs, force to just loop main site
	if( gamipress_is_network_wide_active() ) {
		$sites = array( get_main_site_id() );
	}

	// Loop through each site (default is current site only)
	foreach( $sites as $site_blog_id ) {

		// If we're not polling the current site, switch to the site we're polling
		if ( get_current_blog_id() != $site_blog_id ) {
			switch_to_blog( $site_blog_id );
		}

		// Grab user earned achievements (used to filter the query)
		$earned_ids = gamipress_get_user_earned_achievement_ids( $user_id, $type );

		// Query Achievements
		$args = array(
			'post_type'      =>	$type,
			'orderby'        =>	$orderby,
			'order'          =>	$order,
			'posts_per_page' =>	absint( $limit ),
			'offset'         => absint( $offset ),
			'post_status'    => 'publish',
			'post__not_in'   => array_diff( $hidden, $earned_ids )
		);

		// Filter - query completed or non completed achievements
		if ( $filter == 'completed' ) {
			$args[ 'post__in' ] = $earned_ids;
		}elseif( $filter == 'not-completed' ) {
			$args[ 'post__not_in' ] = array_merge( $hidden, $earned_ids );
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
		$query_count = absint( $achievement_posts->found_posts );
		while ( $achievement_posts->have_posts() ) : $achievement_posts->the_post();

			$achievements .= gamipress_render_achievement( get_the_ID(), $template_args );

			$achievement_count++;

		endwhile;

		// Display a message for no results
		if ( empty( $achievements ) ) {

			$current = current( $type );

			// If we have exactly one achievement type, get its plural name, otherwise use "achievements"
			$post_type_plural = ( 1 == count( $type ) && ! empty( $current ) ) ? get_post_type_object( $current )->labels->name : __( 'achievements' , 'gamipress' );

			// Setup our completion message
			$achievements .= '<div class="gamipress-no-results">';

			if ( 'completed' == $filter ) {
				$no_results_text = sprintf( __( 'No completed %s to display.', 'gamipress' ), strtolower( $post_type_plural ) );
			} else {
				$no_results_text = sprintf( __( 'No %s to display.', 'gamipress' ), strtolower( $post_type_plural ) );
			}

			/**
			 * Filter achievements no results text
			 *
			 * @param string $no_results_text
			 */
			$no_results_text = apply_filters( 'gamipress_achievements_no_results_text', $no_results_text );

			$achievements .= '<p>' . $no_results_text . '</p>';

			$achievements .= '</div><!-- .gamipress-no-results -->';
		}

		if ( get_current_blog_id() != $site_blog_id ) {
			// Come back to current blog
			restore_current_blog();
		}

	}

	// If switched to blog, return back to que current blog
	if( isset( $blog_id ) ) {
		switch_to_blog( $blog_id );
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

	if ( ! empty( $search ) ) {
		$where = " WHERE user_login LIKE '%{$search}%'";
		$where .= " OR user_email LIKE '%{$search}%'";
		$where .= " OR display_name LIKE '%{$search}%'";
	}

	// Pagination args
	$page = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;
	$limit = 20;
	$offset = $limit * ( $page - 1 );

	// Fetch our results (store as associative array)
	$results = $wpdb->get_results(
		"SELECT ID, user_login, user_email, display_name
		 FROM {$wpdb->users}
		 {$where}
		 LIMIT {$offset}, {$limit}",
		'ARRAY_A' );

	$count = $wpdb->get_var(
		"SELECT COUNT(*)
		 FROM {$wpdb->users}
		 {$where}"
	);

	$response = array(
		'results' => $results,
		'more_results' => absint( $count ) > $offset,
	);

	// Return our results
	wp_send_json_success( $response );
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

	// Check for extra conditionals
	$where = '';

	if( isset( $_REQUEST['trigger_type'] ) ) {

		$query_args = array();
		$trigger_type = $_REQUEST['trigger_type'];

		$query_args = gamipress_get_specific_activity_triggers_query_args( $query_args, $trigger_type );

		if( isset( $query_args ) ) {

			if( is_array( $query_args ) ) {
				// If is an array of conditionals, then build the new conditionals
				foreach( $query_args as $field => $value ) {
					$where .= " AND p.{$field} = '$value'";
				}
			} else {
				$where = $query_args;
			}

		}
	}

	// Pagination args
	$page = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;
	$limit = 20;
	$offset = $limit * ( $page - 1 );

	// On this query, keep $wpdb->posts to get current site posts
	$results = $wpdb->get_results( $wpdb->prepare(
		"SELECT p.ID, p.post_title, p.post_type
		FROM   {$wpdb->posts} AS p
		WHERE  1=1
			   {$post_type}
		       {$where}
			   AND p.post_title LIKE %s
		       AND p.post_status IN( 'publish', 'inherit' )
	    LIMIT {$offset}, {$limit}",
		"%%{$search}%%"
	) );

	$count = $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*)
		FROM   {$wpdb->posts} AS p
		WHERE  1=1
			   {$post_type}
		       {$where}
			   AND p.post_title LIKE %s
		       AND p.post_status IN( 'publish', 'inherit' )",
		"%%{$search}%%"
	) );

	$response = array(
		'results' => $results,
		'more_results' => absint( $count ) > $offset,
	);

	// Return our results
	wp_send_json_success( $response );
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
		: gamipress_get_achievement_types_slugs();
	$post_type = sprintf( 'AND p.post_type IN(\'%s\')', implode( "','", $achievement_types ) );

	// For single type, is not needed to add the post type, but for multiples types is a better option to distinguish them easily
	$select = 'p.ID, p.post_title';

	if( count( $achievement_types ) > 1 ) {
		$select = 'p.ID, p.post_title, p.post_type';
	}

	$posts    	= GamiPress()->db->posts;
	$postmeta 	= GamiPress()->db->postmeta;

	$results = $wpdb->get_results( $wpdb->prepare(
		"SELECT {$select}
		FROM {$posts} AS p
		JOIN {$postmeta} AS pm
		ON p.ID = pm.post_id
		WHERE  p.post_title LIKE %s
		       {$post_type}
		       AND p.post_status = 'publish'
		       AND pm.meta_key = %s
		       AND pm.meta_value = %s",
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
 * @updated 1.3.0
 * @updated 1.3.5 Make function accessible through gamipress_get_achievements_options_html action
 */
function gamipress_achievement_post_ajax_handler() {

	$selected = '';

    // If requirement_id requested, then retrieve the selected option from this requirement
    if( isset( $_REQUEST['requirement_id'] ) && ! empty( $_REQUEST['requirement_id'] ) ) {

		$requirements = gamipress_get_requirement_object( $_REQUEST['requirement_id'] );

		$selected = isset( $requirements['achievement_post'] ) ? $requirements['achievement_post'] : '';
    } else if( isset( $_REQUEST['selected'] ) && ! empty( $_REQUEST['selected'] ) ) {
		$selected = $_REQUEST['selected'];
	}

	$achievement_type = $_REQUEST['achievement_type'];
	$exclude_posts = isset( $_REQUEST['excluded_posts'] ) ? (array) $_REQUEST['excluded_posts'] : array();

    // If we don't have an achievement type, bail now
    if ( empty( $achievement_type ) ) {
        die();
    }

	$achievement_types = gamipress_get_achievement_types();

	if( ! isset( $achievement_types[$achievement_type] ) ) {
		return;
	}

	$singular_name = ! empty( $achievement_types[$achievement_type]['singular_name'] ) ? $achievement_types[$achievement_type]['singular_name'] : __( 'Achievement', 'gamipress' );

    // Grab all our posts for this achievement type
    $achievements = get_posts( array(
        'post_type'         => $achievement_type,
        'post__not_in'      => $exclude_posts,
        'posts_per_page'    => -1,
        'orderby'           => 'title',
        'order'             => 'ASC',
        'suppress_filters'  => false,
    ));

    // Setup our output
    $output = '<option value="">' . sprintf( __( 'Choose the %s', 'gamipress' ), $singular_name ) . '</option>';
    foreach ( $achievements as $achievement ) {
        $output .= '<option value="' . $achievement->ID . '" ' . selected( $selected, $achievement->ID, false ) . '>' . $achievement->post_title . '</option>';
    }

    // Send back our results and die like a man
    echo $output;
    die();

}
add_action( 'wp_ajax_gamipress_requirement_achievement_post', 'gamipress_achievement_post_ajax_handler' );
add_action( 'wp_ajax_gamipress_get_achievements_options_html', 'gamipress_achievement_post_ajax_handler' );

/**
 * AJAX Helper for selecting ranks in achievement earned by
 *
 * @since 1.3.1
 */
function gamipress_ajax_get_ranks_options_html() {
	global $wpdb;

	// Post type conditional
	$post_type = ( isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] :  gamipress_get_rank_types_slugs() );

	if ( is_array( $post_type ) ) {
		$post_type = sprintf( 'AND p.post_type IN(\'%s\')', implode( "','", $post_type ) );
		$singular_name = __( 'Rank', 'gamipress' );
	} else {
		$singular_name = gamipress_get_rank_type_singular( $post_type );
		$post_type = sprintf( 'AND p.post_type = \'%s\'', $post_type );
	}

	$selected = '';

	// If requirement_id requested, then retrieve the selected option from this requirement
	if( isset( $_REQUEST['requirement_id'] ) && ! empty( $_REQUEST['requirement_id'] ) ) {

		$requirements = gamipress_get_requirement_object( $_REQUEST['requirement_id'] );

		$selected = isset( $requirements['rank_required'] ) ? $requirements['rank_required'] : '';
	} else if( isset( $_REQUEST['selected'] ) && ! empty( $_REQUEST['selected'] ) ) {
		$selected = $_REQUEST['selected'];
	}

	$posts    	= GamiPress()->db->posts;

	$ranks = $wpdb->get_results( $wpdb->prepare(
		"SELECT p.ID, p.post_title
		FROM {$posts} AS p
		WHERE p.post_status = %s
			{$post_type}
		ORDER BY p.post_type ASC, p.menu_order DESC",
		'publish'
	) );

	// Setup our output
	$output = '<option value="">' . sprintf( __( 'Choose the %s', 'gamipress' ), $singular_name ) . '</option>';
	foreach ( $ranks as $rank ) {
		$output .= '<option value="' . $rank->ID . '" ' . selected( $selected, $rank->ID, false ) . '>' . $rank->post_title . '</option>';
	}

	// Send back our results and die like a man
	echo $output;
	die();
}
add_action( 'wp_ajax_gamipress_get_ranks_options_html', 'gamipress_ajax_get_ranks_options_html' );

/**
 * AJAX Helper for selecting ranks in Shortcode Embedder
 *
 * @since 1.3.1
 */
function gamipress_ajax_get_ranks_options() {
	global $wpdb;

	// Pull back the search string
	$search = isset( $_REQUEST['q'] ) ? like_escape( $_REQUEST['q'] ) : '';

	// For single type, is not needed to add the post type, but for multiples types is a better option to distinguish them easily
	$select = 'p.ID, p.post_title';

	// Post type conditional
	$post_type = ( isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : gamipress_get_rank_types_slugs() );

	if ( is_array( $post_type ) ) {
		$post_type = sprintf( 'AND p.post_type IN(\'%s\')', implode( "','", $post_type ) );

		$select = 'p.ID, p.post_title, p.post_type';
	} else {
		$post_type = sprintf( 'AND p.post_type = \'%s\'', $post_type );
	}

	$posts    	= GamiPress()->db->posts;

	$ranks = $wpdb->get_results( $wpdb->prepare(
		"SELECT {$select}
		FROM {$posts} AS p
		WHERE p.post_status = %s
			{$post_type}
		 AND p.post_title LIKE %s
		ORDER BY p.post_type ASC, p.menu_order DESC",
		'publish',
		"%%{$search}%%"
	) );

	// Return our results
	wp_send_json_success( $ranks );
}
add_action( 'wp_ajax_gamipress_get_ranks_options', 'gamipress_ajax_get_ranks_options' );

/**
 * Ajax function to check and unlock an achievement by expend an amount of points
 *
 * @since 1.3.7
 *
 * @return void
 */
function gamipress_ajax_unlock_achievement_with_points() {

	$achievement_id = isset( $_POST['achievement_id'] ) ? $_POST['achievement_id'] : 0;

	$achievement = gamipress_get_post( $achievement_id );

	// Return if achievement not exists
	if( ! $achievement ) {
		wp_send_json_error( __( 'Achievement not found.', 'gamipress' ) );
	}

	$achievement_types = gamipress_get_achievement_types();

	if( ! isset( $achievement_types[$achievement->post_type] ) ) {
		wp_send_json_error( __( 'Invalid achievement.', 'gamipress' ) );
	}

	$achievement_type = $achievement_types[$achievement->post_type];

	$user_id = get_current_user_id();

	// Guest not supported yet (basically because they has not points)
	if( $user_id === 0 ) {
		wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
	}

	// Return if this option not was enabled
	if( ! (bool) gamipress_get_post_meta( $achievement_id, '_gamipress_unlock_with_points' ) ) {
		wp_send_json_error( sprintf( __( 'You are not allowed to unlock this %s.', 'gamipress' ), $achievement_type['singular_name'] ) );
	}

	$points = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_points_to_unlock' ) );

	// Return if no points configured
	if( $points === 0 ) {
		wp_send_json_error( sprintf( __( 'You are not allowed to unlock this %s.', 'gamipress' ), $achievement_type['singular_name'] ) );
	}

	$earned = gamipress_achievement_user_exceeded_max_earnings( $user_id, $achievement_id );

	// Return if user has completely earned this achievement
	if( $earned ) {
		wp_send_json_error( sprintf( __( 'You already unlocked this %s.', 'gamipress' ), $achievement_type['singular_name'] ) );
	}

	// Setup points type
	$points_types = gamipress_get_points_types();
	$points_type = gamipress_get_post_meta( $achievement_id, '_gamipress_points_type_to_unlock' );

	// Default points label
	$points_label = __( 'Points', 'gamipress' );

	if( isset( $points_types[$points_type] ) ) {
		// Points type label
		$points_label = $points_types[$points_type]['plural_name'];
	}

	// Setup user points
	$user_points = gamipress_get_user_points( $user_id, $points_type );

	if( $user_points < $points ) {
		wp_send_json_error( sprintf( __( 'Insufficient %s.', 'gamipress' ), $points_label ) );
	}

	// Deduct points to user
	gamipress_deduct_points_to_user( $user_id, $points, $points_type, array(
		'log_type' => 'points_expend',
		'reason' => gamipress_get_option( 'points_expended_log_pattern', __( '{user} expended {points} {points_type} for a new total of {total_points} {points_type}', 'gamipress' ) )
	) );

	// Award the achievement to the user
	gamipress_award_achievement_to_user( $achievement_id, $user_id );

	$congratulations = gamipress_get_post_meta( $achievement_id, '_gamipress_congratulations_text' );

	if( empty( $congratulations ) ) {
		$congratulations = sprintf( __( 'Congratulations! You unlocked the %s %s.', 'gamipress' ), $achievement_type['singular_name'], $achievement->post_title );
	}

	// Filter to change congratulations message
	$congratulations = apply_filters( 'gamipress_achievement_unlocked_with_points_congratulations', $congratulations, $achievement_id, $user_id, $points, $points_type );

	/**
	 * Achievement unlocked with points action
	 *
	 * @since 1.3.7
	 *
	 * @param integer $achievement_id 	The achievement unlocked ID
	 * @param integer $user_id 			The user ID
	 * @param integer $points 			The amount of points expended
	 * @param string  $points_type 		The points type of the amount of points expended
	 */
	do_action( 'gamipress_achievement_unlocked_with_points', $achievement_id, $user_id, $points, $points_type );

	wp_send_json_success( $congratulations );

}
add_action( 'wp_ajax_gamipress_unlock_achievement_with_points', 'gamipress_ajax_unlock_achievement_with_points' );

/**
 * Ajax function to check and unlock a rank by expend an amount of points
 *
 * @since 1.3.7
 *
 * @return void
 */
function gamipress_ajax_unlock_rank_with_points() {

	$rank_id = isset( $_POST['rank_id'] ) ? $_POST['rank_id'] : 0;

	$rank = gamipress_get_post( $rank_id );

	// Return if rank not exists
	if( ! $rank ) {
		wp_send_json_error( __( 'Rank not found.', 'gamipress' ) );
	}

	$rank_types = gamipress_get_rank_types();

	if( ! isset( $rank_types[$rank->post_type] ) ) {
		wp_send_json_error( __( 'Invalid rank.', 'gamipress' ) );
	}

	$rank_type = $rank_types[$rank->post_type];

	$user_id = get_current_user_id();

	// Guest not supported yet (basically because they has not points)
	if( $user_id === 0 ) {
		wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
	}

	// Return if this option not was enabled
	if( ! (bool) gamipress_get_post_meta( $rank_id, '_gamipress_unlock_with_points' ) ) {
		wp_send_json_error( sprintf( __( 'You are not allowed to unlock this %s.', 'gamipress' ), $rank_type['singular_name'] ) );
	}

	$points = absint( gamipress_get_post_meta( $rank_id, '_gamipress_points_to_unlock' ) );

	// Return if no points configured
	if( $points === 0 ) {
		wp_send_json_error( sprintf( __( 'You are not allowed to unlock this %s.', 'gamipress' ), $rank_type['singular_name'] ) );
	}

	$user_rank = gamipress_get_user_rank( $user_id, $rank_type );

	// Return if user is in a higher rank
	if( gamipress_get_rank_priority( $rank_id ) <= gamipress_get_rank_priority( $user_rank ) ) {
		wp_send_json_error( sprintf( __( 'You are already in a higher %s.', 'gamipress' ), $rank_type['singular_name'] ) );
	}

	// Setup points type
	$points_types = gamipress_get_points_types();
	$points_type = gamipress_get_post_meta( $rank_id, '_gamipress_points_type_to_unlock' );

	// Default points label
	$points_label = __( 'Points', 'gamipress' );

	if( isset( $points_types[$points_type] ) ) {
		// Points type label
		$points_label = $points_types[$points_type]['plural_name'];
	}

	// Setup user points
	$user_points = gamipress_get_user_points( $user_id, $points_type );

	if( $user_points < $points ) {
		wp_send_json_error( sprintf( __( 'Insufficient %s.', 'gamipress' ), $points_label ) );
	}

	// Deduct points to user
	gamipress_deduct_points_to_user( $user_id, $points, $points_type, array(
		'log_type' => 'points_expend',
		'reason' => gamipress_get_option( 'points_expended_log_pattern', __( '{user} expended {points} {points_type} for a new total of {total_points} {points_type}', 'gamipress' ) )
	) );

	// Award the rank to the user
	gamipress_update_user_rank( $user_id, $rank_id );

	$congratulations = gamipress_get_post_meta( $rank_id, '_gamipress_congratulations_text' );

	if( empty( $congratulations ) ) {
		$congratulations = sprintf( __( 'Congratulations! You reached to the %s %s.', 'gamipress' ), $rank_type['singular_name'], $rank->post_title );
	}

	// Filter to change congratulations message
	$congratulations = apply_filters( 'gamipress_rank_unlocked_with_points_congratulations', $congratulations, $rank_id, $user_id, $points, $points_type );

	/**
	 * Achievement unlocked with points action
	 *
	 * @since 1.3.7
	 *
	 * @param integer $rank_id 			The rank unlocked ID
	 * @param integer $user_id 			The user ID
	 * @param integer $points 			The amount of points expended
	 * @param string  $points_type 		The points type of the amount of points expended
	 */
	do_action( 'gamipress_rank_unlocked_with_points', $rank_id, $user_id, $points, $points_type );

	wp_send_json_success( $congratulations );

}
add_action( 'wp_ajax_gamipress_unlock_rank_with_points', 'gamipress_ajax_unlock_rank_with_points' );
