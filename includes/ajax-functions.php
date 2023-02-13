<?php
/**
 * Ajax Functions
 *
 * @package     GamiPress\Ajax_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax Helper for returning achievements
 *
 * @since   1.0.0
 * @updated 1.7.9 Added nonce usage
 *
 * @return void
 */
function gamipress_ajax_get_achievements() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress', 'nonce' );

	// Send back our successful response
	wp_send_json_success( gamipress_achievements_shortcode_query( $_REQUEST ) );

}
add_action( 'wp_ajax_gamipress_get_achievements', 'gamipress_ajax_get_achievements' );
add_action( 'wp_ajax_nopriv_gamipress_get_achievements', 'gamipress_ajax_get_achievements' );

/**
 * Ajax Helper for returning logs
 *
 * @since   1.4.9
 * @updated 1.7.9 Added nonce usage
 *
 * @return void
 */
function gamipress_ajax_get_logs() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress', 'nonce' );

	// Set current page var
    if( isset( $_REQUEST['page'] ) && absint( $_REQUEST['page'] ) > 1 ) {
        set_query_var( 'paged', absint( $_REQUEST['page'] ) );
    }

    $atts = $_REQUEST;

    // Unset non required shortcode atts
    unset( $atts['action'] );
    unset( $atts['page'] );

	// Send back our successful response
	wp_send_json_success( gamipress_do_shortcode( 'gamipress_logs', $atts ) );

}
add_action( 'wp_ajax_gamipress_get_logs', 'gamipress_ajax_get_logs' );
add_action( 'wp_ajax_nopriv_gamipress_get_logs', 'gamipress_ajax_get_logs' );

/**
 * Ajax Helper for returning user earnings
 *
 * @since   1.4.9
 * @updated 1.7.9 Added nonce usage
 *
 * @return void
 */
function gamipress_ajax_get_user_earnings() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress', 'nonce' );

	// Set current page var
	if( isset( $_REQUEST['page'] ) && absint( $_REQUEST['page'] ) > 1 ) {
		set_query_var( 'paged', absint( $_REQUEST['page'] ) );
    }

    $atts = $_REQUEST;

    // Unset non required shortcode atts
    unset( $atts['action'] );
    unset( $atts['page'] );

    // Sanitize
    foreach( $atts as $attr => $value ) {
        $atts[$attr] = sanitize_text_field( $value );
    }

	// Send back our successful response
	wp_send_json_success( gamipress_do_shortcode( 'gamipress_earnings', $atts ) );

}
add_action( 'wp_ajax_gamipress_get_user_earnings', 'gamipress_ajax_get_user_earnings' );
add_action( 'wp_ajax_nopriv_gamipress_get_user_earnings', 'gamipress_ajax_get_user_earnings' );

/**
 * Ajax Helper for save email settings
 *
 * @since 2.2.1
 *
 * @return void
 */
function gamipress_ajax_save_email_settings() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress', 'nonce' );

    if( ! isset( $_REQUEST['setting'] ) ) {
        wp_send_json_error( __( 'Invalid setting key.', 'gamipress' ) );
    }

    if( ! isset( $_REQUEST['value'] ) ) {
        wp_send_json_error( __( 'Invalid setting value.', 'gamipress' ) );
    }

    // Sanitize the setting key
    $setting = sanitize_text_field( $_REQUEST['setting'] );
    $setting = sanitize_key( $setting );

    // Sanitize the setting value
    $value = sanitize_text_field( $_REQUEST['value'] );

    // The unique accepted values are "yes" or "no"
    if( $value !== 'yes' && $value !== 'no' ) {
        $value = 'yes';
    }

    $user_id = get_current_user_id();

    // Bail if user is not logged in
    if( $user_id === 0 ) {
        wp_send_json_error( __( 'Please, log in to update your email preferences.', 'gamipress' ) );
    }

    $user_settings = gamipress_get_user_email_settings( $user_id );

    $user_settings[$setting] = $value;

    switch ( $setting ) {
        case 'all':
            // Update all user settings to the same value
            foreach( $user_settings as $user_setting => $setting_value ) {
                $user_settings[$user_setting] = $value;
            }
            break;
        case 'points_types':
        case 'achievement_types':
        case 'rank_types':
            // Update the group settings to the same value
            foreach( $user_settings as $user_setting => $setting_value ) {
                if( gamipress_starts_with( $user_setting, $setting . '_' ) ) {
                    $user_settings[$user_setting] = $value;
                }
            }
            break;
    }

    update_user_meta( $user_id, 'gamipress_email_settings', $user_settings );

    // Send back our successful response
    wp_send_json_success( __( 'Email preferences saved successfully.', 'gamipress' ) );

}
add_action( 'wp_ajax_gamipress_save_email_settings', 'gamipress_ajax_save_email_settings' );

/**
 * AJAX Helper for selecting users in Shortcode Embedder
 *
 * @since 1.0.0
 */
function gamipress_ajax_get_users() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

	// If no word query sent, initialize it
	if ( ! isset( $_REQUEST['q'] ) ) {
		$_REQUEST['q'] = '';
    }

	global $wpdb;

	// Pull back the search string
	$search = esc_sql( $wpdb->esc_like( $_REQUEST['q'] ) );
	$where = '';
    $from = "FROM {$wpdb->users} as u ";

	if ( ! empty( $search ) ) {
		$where = "WHERE ( u.user_login LIKE '%{$search}%' ";
		$where .= "OR u.user_email LIKE '%{$search}%' ";
		$where .= "OR u.display_name LIKE '%{$search}%' ";
		$where .= "OR u.ID LIKE '%{$search}%' ) ";
	}

    // Get users from the current site
    if( is_multisite() ) {
        $from .= "LEFT JOIN {$wpdb->usermeta} AS umcap ON ( umcap.user_id = u.ID ) ";

        // Check if where have been initialized or not
        if( empty( $where ) ) {
            $where = "WHERE ";
        } else {
            $where .= "AND ";
        }

        $where .= "umcap.meta_key = '" . $wpdb->get_blog_prefix() . "capabilities' ";
    }

	// Pagination args
	$page = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;
	$limit = 20;
	$offset = $limit * ( $page - 1 );

	// Fetch our results (store as associative array)
	$results = $wpdb->get_results(
		"SELECT ID, user_login, user_email, display_name
		 {$from}
		 {$where}
		 LIMIT {$offset}, {$limit}",
		'ARRAY_A'
    );

	$count = absint( $wpdb->get_var( "SELECT COUNT(*) {$from} {$where}" ) );

    /**
     * Ajax users results (used on almost every post selector)
     * Note: Use $_REQUEST for all given parameters
     *
     * @since  1.0.0
     *
     * @param array $results
     *
     * @return array
     */
    $results = apply_filters( 'gamipress_ajax_get_user_results', $results );

	$response = array(
		'results' => $results,
		'more_results' => $count > $offset,
	);

	// Return our results
	wp_send_json_success( $response );
}
add_action( 'wp_ajax_gamipress_get_users', 'gamipress_ajax_get_users' );

/**
 * AJAX Helper for selecting posts
 *
 * @since 1.0.0
 * @updated 1.4.8 Added multisite support
 */
function gamipress_ajax_get_posts() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

	global $wpdb;

	// Pull back the search string
	$search = isset( $_REQUEST['q'] ) ? esc_sql( $wpdb->esc_like( $_REQUEST['q'] ) ) : '';

	// Setup where conditions (initialized with 1=1)
    $where = '1=1';

	// Post type conditional
	$post_type = ( isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] :  array( 'post', 'page' ) );

	if ( is_array( $post_type ) ) {

	    // Sanitize all post types given
        foreach( $post_type as $i => $value ) {
            $post_type[$i] = sanitize_text_field( $value );
        }

		$post_type = sprintf( ' AND p.post_type IN(\'%s\')', implode( "','", $post_type ) );
	} else {

	    // Sanitize the post type
        $post_type = sanitize_text_field( $post_type );

		$post_type = sprintf( ' AND p.post_type = \'%s\'', $post_type );
	}

    $where .= $post_type;

	// Post title and ID conditional
    $where .= " AND ( p.post_title LIKE '%{$search}%' OR p.ID LIKE '%{$search}%' )";

	// Post status conditional
    $where .= " AND p.post_status IN( 'publish', 'private', 'inherit' )";

	// Check for trigger type extra conditionals
	if( isset( $_REQUEST['trigger_type'] ) ) {

		$query_args = array();
		$trigger_type = sanitize_text_field( $_REQUEST['trigger_type'] );

		// Get trigger type query args (This function is filtered!)
		$query_args = gamipress_get_specific_activity_triggers_query_args( $query_args, $trigger_type );

		if( ! empty( $query_args ) ) {

			if( is_array( $query_args ) ) {
				// If is an array of conditionals, then build the new conditionals
				foreach( $query_args as $field => $value ) {
					$where .= " AND p.{$field} = '$value'";
				}
			} else {
			    // Leave an extra space if query args doesn't have one
				$where .= ' ' . $query_args;
			}

		}
	}

    /**
     * Ajax posts query args (used on almost every post selector)
     *
     * Note: Use $_REQUEST for all given parameters
     *
     * @since  1.6.6
     *
     * @param string $query_args
     *
     * @return array|string
     */
    $extra_query_args = apply_filters( 'gamipress_ajax_get_posts_query_args', '' );

    // Check for extra conditionals
    if( ! empty( $extra_query_args ) ) {

        if( is_array( $extra_query_args ) ) {
            // If is an array of conditionals, then build the new conditionals
            foreach( $extra_query_args as $field => $value ) {
                $where .= " AND p.{$field} = '$value'";
            }
        } else {
            // Leave an extra space if extra query args doesn't have one
            $where .= ' ' . $extra_query_args;
        }

    }

    // Setup from (from is filtered to allow joins)
    $from = "{$wpdb->posts} AS p";

    /**
     * Ajax posts from (used on almost every post selector)
     * Note: Use $_REQUEST for all given parameters
     *
     * @since  1.6.6
     *
     * @param string $from By default '{$wpdb->posts} AS p'
     *
     * @return string
     */
    $from = apply_filters( 'gamipress_ajax_get_posts_from', $from );

    /**
     * Ajax posts where (used on almost every post selector)
     * Note: Use $_REQUEST for all given parameters
     *
     * @since  1.6.6
     *
     * @param string $where Contains all wheres
     *
     * @return string
     */
    $where = apply_filters( 'gamipress_ajax_get_posts_where', $where );

    // Setup order by
    $order_by = "p.post_type ASC, p.menu_order DESC";

    /**
     * Ajax posts order by (used on almost every post selector)
     * Note: Use $_REQUEST for all given parameters
     *
     * @since  1.6.6
     *
     * @param string $order_by By default 'p.post_type ASC, p.menu_order DESC'
     *
     * @return string
     */
    $order_by = apply_filters( 'gamipress_ajax_get_posts_order_by', $order_by );

	// Pagination args
	$page = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;
	$limit = 20;
	$offset = $limit * ( $page - 1 );

	if( gamipress_is_network_wide_active() ) {

		$results = array();
		$count = 0;

		foreach( gamipress_get_network_site_ids() as $site_id ) {

		    // Switch to site
			switch_to_blog( $site_id );

			// Get the current site name to append it to results
			$site_name = get_bloginfo( 'name' );

            // Setup from after switch site to get the site's posts table correctly
            $from = "{$wpdb->posts} AS p";

            /**
             * Ajax posts from (for current switched site)
             *
             * @since  1.7.4
             *
             * @param string    $from       By default '{$wpdb->posts} AS p'
             * @param int       $site_id    Site ID
             *
             * @return string
             */
            $from = apply_filters( 'gamipress_ajax_get_posts_site_from', $from, $site_id );

			// On this query, keep $wpdb->posts to get sub site posts
			$site_results = $wpdb->get_results(
				"SELECT p.ID, p.post_title, p.post_type
				 FROM {$from}
				 WHERE {$where}
                 ORDER BY {$order_by}
				 LIMIT {$offset}, {$limit}"
			);

			// Loop all site results to add the site ID and name
			foreach( $site_results as $index => $site_result ) {

				$site_result->site_id = absint( $site_id );
				$site_result->site_name = $site_name;

				$site_results[$index] = $site_result;
			}

			// Merge it to all results
			$results = array_merge( $results, $site_results );

			$count += absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$from} WHERE {$where}" ) );

            // Restore current site
            restore_current_blog();

		}

	} else {

		// On this query, keep $wpdb->posts to get current site posts
		$results = $wpdb->get_results(
			"SELECT p.ID, p.post_title, p.post_type
             FROM {$from}
             WHERE {$where}
             ORDER BY {$order_by}
             LIMIT {$offset}, {$limit}"
		);

		$count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$from} WHERE {$where}" ) );

	}

	$response = array(
		'results' => $results,
		'more_results' => $count > $limit && $count > $offset,
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
	$search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';
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
		       AND p.post_status IN( 'publish', 'private', 'inherit' )
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
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

	$selected = '';

    // If requirement_id requested, then retrieve the selected option from this requirement
    if( isset( $_REQUEST['requirement_id'] ) && ! empty( $_REQUEST['requirement_id'] ) ) {

		$requirements = gamipress_get_requirement_object( absint( $_REQUEST['requirement_id'] ) );

		$selected = isset( $requirements['achievement_post'] ) ? $requirements['achievement_post'] : '';
    } else if( isset( $_REQUEST['selected'] ) && ! empty( $_REQUEST['selected'] ) ) {
		$selected = $_REQUEST['selected'];
	}

    // Sanitize
    $selected = absint( $selected );

	$achievement_type = sanitize_text_field( $_REQUEST['achievement_type'] );
	$exclude_posts = isset( $_REQUEST['excluded_posts'] ) ? (array) $_REQUEST['excluded_posts'] : array();

    // If we don't have an achievement type, bail now
    if ( empty( $achievement_type ) ) {
        die();
    }

	$achievement_types = gamipress_get_achievement_types();

	if( ! isset( $achievement_types[$achievement_type] ) ) {
		return;
	}

	// Sanitize excluded posts
    foreach( $exclude_posts as $i => $exclude_post ) {
        $exclude_posts[$i] = absint( $exclude_post );
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
        $achievement_id = absint( $achievement->ID );
        $output .= '<option value="' . $achievement_id . '" ' . selected( $selected, $achievement_id, false ) . '>' . $achievement->post_title . '</option>';
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
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

	global $wpdb;

	// Post type conditional
	$post_type = ( isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] :  gamipress_get_rank_types_slugs() );

	if ( is_array( $post_type ) ) {

        // Sanitize all post types given
        foreach( $post_type as $i => $value ) {
            $post_type[$i] = sanitize_text_field( $value );
        }

		$post_type = sprintf( 'AND p.post_type IN(\'%s\')', implode( "','", $post_type ) );
		$singular_name = __( 'Rank', 'gamipress' );
	} else {

        // Sanitize the post type
        $post_type = sanitize_text_field( $post_type );

		$singular_name = gamipress_get_rank_type_singular( $post_type, true );
		$post_type = sprintf( 'AND p.post_type = \'%s\'', $post_type );
	}

	$selected = '';

	// If requirement_id requested, then retrieve the selected option from this requirement
	if( isset( $_REQUEST['requirement_id'] ) && ! empty( $_REQUEST['requirement_id'] ) ) {

		$requirements = gamipress_get_requirement_object( absint( $_REQUEST['requirement_id'] ) );

		$selected = isset( $requirements['rank_required'] ) ? $requirements['rank_required'] : '';
	} else if( isset( $_REQUEST['selected'] ) && ! empty( $_REQUEST['selected'] ) ) {
		$selected = $_REQUEST['selected'];
	}

    // Sanitize
    $selected = absint( $selected );

	$posts = GamiPress()->db->posts;

	$ranks = $wpdb->get_results(
	    "SELECT p.ID, p.post_title
		FROM {$posts} AS p
		WHERE p.post_status IN( 'publish', 'private', 'inherit' )
        {$post_type}
		ORDER BY p.post_type ASC, p.menu_order DESC"
    );

	// Setup our output
	$output = '<option value="">' . sprintf( __( 'Choose the %s', 'gamipress' ), $singular_name ) . '</option>';
	foreach ( $ranks as $rank ) {
        $rank_id = absint( $rank->ID );
		$output .= '<option value="' . $rank_id . '" ' . selected( $selected, $rank_id, false ) . '>' . $rank->post_title . '</option>';
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
	$search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

	// For single type, is not needed to add the post type, but for multiples types is a better option to distinguish them easily
	$select = 'p.ID, p.post_title';

	// Post type conditional
	$post_type = ( isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : gamipress_get_rank_types_slugs() );

	if ( is_array( $post_type ) ) {

        // Sanitize all post types given
        foreach( $post_type as $i => $value ) {
            $post_type[$i] = sanitize_text_field( $value );
        }

		$post_type = sprintf( 'AND p.post_type IN(\'%s\')', implode( "','", $post_type ) );

		$select = 'p.ID, p.post_title, p.post_type';
	} else {

        // Sanitize the post type
        $post_type = sanitize_text_field( $post_type );

		$post_type = sprintf( 'AND p.post_type = \'%s\'', $post_type );
	}

	$posts    	= GamiPress()->db->posts;

	$ranks = $wpdb->get_results( $wpdb->prepare(
		"SELECT {$select}
		FROM {$posts} AS p
		WHERE p.post_status IN( 'publish', 'private', 'inherit' )
        {$post_type}
		 AND p.post_title LIKE %s
		ORDER BY p.post_type ASC, p.menu_order DESC",
		"%%{$search}%%"
	) );

	// Return our results
	wp_send_json_success( $ranks );
}
add_action( 'wp_ajax_gamipress_get_ranks_options', 'gamipress_ajax_get_ranks_options' );

/**
 * Ajax function to check and unlock an achievement by expend an amount of points
 *
 * @since   1.3.7
 * @updated 1.7.9 Added nonce usage
 *
 * @return void
 */
function gamipress_ajax_unlock_achievement_with_points() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress', 'nonce' );

	$achievement_id = isset( $_POST['achievement_id'] ) ? absint( $_POST['achievement_id'] ) : 0;

	$achievement = gamipress_get_post( $achievement_id );

	// Return if achievement not exists
	if( ! $achievement )
		wp_send_json_error( __( 'Achievement not found.', 'gamipress' ) );

	$achievement_types = gamipress_get_achievement_types();

	// Return if not a valid achievement
	if( ! isset( $achievement_types[$achievement->post_type] ) )
		wp_send_json_error( __( 'Invalid achievement.', 'gamipress' ) );

	$achievement_type = $achievement_types[$achievement->post_type];

	$user_id = get_current_user_id();

	// Guest not supported yet (basically because they has not points)
	if( $user_id === 0 )
		wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );

	// Return if this option not was enabled
	if( ! (bool) gamipress_get_post_meta( $achievement_id, '_gamipress_unlock_with_points' ) )
		wp_send_json_error( sprintf( __( 'You are not allowed to unlock this %s.', 'gamipress' ), $achievement_type['singular_name'] ) );

	$points = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_points_to_unlock' ) );

	// Return if no points configured
	if( $points === 0 )
		wp_send_json_error( sprintf( __( 'You are not allowed to unlock this %s.', 'gamipress' ), $achievement_type['singular_name'] ) );

	$earned = gamipress_achievement_user_exceeded_max_earnings( $user_id, $achievement_id );

	// Return if user has completely earned this achievement
	if( $earned )
		wp_send_json_error( sprintf( __( 'You already unlocked this %s.', 'gamipress' ), $achievement_type['singular_name'] ) );

	// Setup points type
	$points_types = gamipress_get_points_types();
	$points_type = gamipress_get_post_meta( $achievement_id, '_gamipress_points_type_to_unlock' );

	// Default points label
	$points_label = __( 'Points', 'gamipress' );

    // Points type label
	if( isset( $points_types[$points_type] ) )
		$points_label = $points_types[$points_type]['plural_name'];

	// Setup user points
	$user_points = gamipress_get_user_points( $user_id, $points_type );

	// Return if insufficient points
	if( $user_points < $points ) {

        $message = sprintf( __( 'Insufficient %s.', 'gamipress' ), $points_label );

        /**
         * Available filter to override the insufficient points text when unlocking a rank using points
         *
         * @since   1.0.5
         *
         * @param string    $message        The insufficient points message
         * @param int       $achievement_id The achievement ID
         * @param int       $user_id        The current logged in user ID
         * @param int       $points         The required amount of points
         * @param string    $points_type    The required amount points type
         */
        $message = apply_filters( 'gamipress_unlock_achievement_with_points_insufficient_points_message', $message, $achievement_id, $user_id, $points, $points_type );

		wp_send_json_error( $message );
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

    $congratulations = wpautop( $congratulations );
    $congratulations = do_shortcode( $congratulations );

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
 * @since   1.3.7
 * @updated 1.7.9 Added nonce usage
 *
 * @return void
 */
function gamipress_ajax_unlock_rank_with_points() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress', 'nonce' );

	$rank_id = isset( $_POST['rank_id'] ) ? absint( $_POST['rank_id'] ) : 0;

	$rank = gamipress_get_post( $rank_id );

	// Return if rank not exists
	if( ! $rank ) {
		wp_send_json_error( __( 'Rank not found.', 'gamipress' ) );
    }

	$rank_types = gamipress_get_rank_types();

	// Return if not a valid rank
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

    // Bail if not is the next rank to unlock
    if( gamipress_get_next_user_rank_id( $user_id, $rank->post_type ) !== $rank_id ) {
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

    // Points type label
	if( isset( $points_types[$points_type] ) )
		$points_label = $points_types[$points_type]['plural_name'];

	// Setup user points
	$user_points = gamipress_get_user_points( $user_id, $points_type );

	// Return if insufficient points
	if( $user_points < $points ) {

        $message = sprintf( __( 'Insufficient %s.', 'gamipress' ), $points_label );

        /**
         * Available filter to override the insufficient points text when unlocking a rank using points
         *
         * @since   1.0.5
         *
         * @param string    $message        The insufficient points message
         * @param int       $rank_id        The rank ID
         * @param int       $user_id        The current logged in user ID
         * @param int       $points         The required amount of points
         * @param string    $points_type    The required amount points type
         */
        $message = apply_filters( 'gamipress_unlock_rank_with_points_insufficient_points_message', $message, $rank_id, $user_id, $points, $points_type );

        wp_send_json_error( $message );
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

    $congratulations = wpautop( $congratulations );
    $congratulations = do_shortcode( $congratulations );

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
