<?php
/**
 * GamiPress Achievements Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Achievements
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register [gamipress_achievements] shortcode
 *
 * @since 1.0.0
 */
function gamipress_register_achievements_shortcode() {

	// Setup a custom array of achievement types
	$achievement_types = array( 'all' => __( 'All', 'gamipress' ) );

	foreach ( gamipress_get_achievement_types() as $slug => $data ) {

		$achievement_types[$slug] = $data['plural_name'];

	}

	$achievement_fields = GamiPress()->shortcodes['gamipress_achievement']->fields;

	unset( $achievement_fields['id'] );

	gamipress_register_shortcode( 'gamipress_achievements', array(
		'name'            => __( 'Achievement List', 'gamipress' ),
		'description'     => __( 'Output a list of achievements.', 'gamipress' ),
		'output_callback' => 'gamipress_achievements_shortcode',
		'tabs' => array(
			'general' => array(
				'icon' => 'dashicons-admin-generic',
				'title' => __( 'General', 'gamipress' ),
				'fields' => array(
					'type',
					'columns',
					'filter',
					'filter_value',
					'search',
					'load_more',
				),
			),
			'achievement' => array(
				'icon' => 'dashicons-awards',
				'title' => __( 'Achievement', 'gamipress' ),
				'fields' => array_keys( $achievement_fields ),
			),
			'query' => array(
				'icon' => 'dashicons-search',
				'title' => __( 'Query', 'gamipress' ),
				'fields' => array(
					'limit',
					'orderby',
					'order',
					'current_user',
					'user_id',
					'include',
					'exclude',
					'wpms',
				),
			),
		),
		'fields'      => array_merge( array(
			'type' => array(
				'name'        => __( 'Achievement Type(s)', 'gamipress' ),
				'description' => __( 'Single or comma-separated list of achievement type(s) to display.', 'gamipress' ),
				'type'        => 'advanced_select',
				'multiple'    => true,
				'options'     => $achievement_types,
				'default'     => 'all',
			),
			'columns' => array(
				'name'        => __( 'Columns', 'gamipress' ),
				'description' => __( 'Columns to divide achievements.', 'gamipress' ),
				'type' 	=> 'select',
				'options' => array(
					'1' => __( '1 Column', 'gamipress' ),
					'2' => __( '2 Columns', 'gamipress' ),
					'3' => __( '3 Columns', 'gamipress' ),
					'4' => __( '4 Columns', 'gamipress' ),
					'5' => __( '5 Columns', 'gamipress' ),
					'6' => __( '6 Columns', 'gamipress' ),
				),
				'default' => '1'
			),
			'filter' => array(
				'name'        => __( 'Show Filter', 'gamipress' ),
				'description' => __( 'Display filter input.', 'gamipress' ),
				'type' 	=> 'checkbox',
				'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
            'filter_value' => array(
                'name'        => __( 'Initial Filter Value', 'gamipress' ),
                'description' => __( 'Set filter initial value. If you hide the filter, user won\'t be able to change this value.', 'gamipress' ),
                'type' 	=> 'select',
                'options' => array(
                    'all'           => __( 'All Achievements', 'gamipress' ),
                    'completed'     => __( 'Completed Achievements', 'gamipress' ),
                    'not-completed' => __( 'Not Completed Achievements', 'gamipress' ),
                ),
                'default' => 'all'
            ),
			'search' => array(
				'name'        => __( 'Show Search', 'gamipress' ),
				'description' => __( 'Display a search input.', 'gamipress' ),
				'type' 	=> 'checkbox',
				'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
			'load_more' => array(
				'name'        => __( 'Show the "Load More" button', 'gamipress' ),
				'description' => __( 'Display a load more button.', 'gamipress' ),
				'type' 	=> 'checkbox',
				'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
			'limit' => array(
				'name'        => __( 'Limit', 'gamipress' ),
				'description' => __( 'Number of achievements to display.', 'gamipress' ),
				'type'        => 'text',
				'default'     => 10,
			),
			'orderby' => array(
				'name'        => __( 'Order By', 'gamipress' ),
				'description' => __( 'Parameter to use for sorting.', 'gamipress' ),
				'type'        => 'select',
				'options'      => array(
					'menu_order' => __( 'Menu Order', 'gamipress' ),
					'ID'         => __( 'Achievement', 'gamipress' ),
					'title'      => __( 'Achievement Title', 'gamipress' ),
					'date'       => __( 'Published Date', 'gamipress' ),
					'modified'   => __( 'Last Modified Date', 'gamipress' ),
					'author'     => __( 'Achievement Author', 'gamipress' ),
					'rand'       => __( 'Random', 'gamipress' ),
				),
				'default'     => 'menu_order',
			),
			'order' => array(
				'name'        => __( 'Order', 'gamipress' ),
				'description' => __( 'Sort order.', 'gamipress' ),
				'type'        => 'select',
				'options'      => array( 'ASC' => __( 'Ascending', 'gamipress' ), 'DESC' => __( 'Descending', 'gamipress' ) ),
				'default'     => 'ASC',
			),
			'current_user' => array(
				'name'        => __( 'Current User', 'gamipress' ),
				'description' => __( 'Show only achievements earned by the current logged in user.', 'gamipress' ),
				'type' 		  => 'checkbox',
				'classes' 	  => 'gamipress-switch',
			),
			'user_id' => array(
				'name'        => __( 'User', 'gamipress' ),
				'description' => __( 'Show only achievements earned by a specific user.', 'gamipress' ),
				'type'        => 'select',
				'default'     => '',
                'options_cb'  => 'gamipress_options_cb_users'
			),
			'include' => array(
				'name'        => __( 'Include', 'gamipress' ),
				'description' => __( 'Comma-separated list of specific achievement IDs to include.', 'gamipress' ),
				'type'        => 'advanced_select',
				'multiple'    => true,
				'default'     => '',
				'options_cb'  => 'gamipress_options_cb_posts'
			),
			'exclude' => array(
				'name'        => __( 'Exclude', 'gamipress' ),
				'description' => __( 'Comma-separated list of specific achievement IDs to exclude.', 'gamipress' ),
				'type'        => 'advanced_select',
				'multiple'    => true,
				'default'     => '',
				'options_cb'  => 'gamipress_options_cb_posts'
			),
			'wpms' => array(
				'name'        => __( 'Include Multisite Achievements', 'gamipress' ),
				'description' => __( 'Show achievements from all network sites.', 'gamipress' ),
				'type' 		  => 'checkbox',
				'classes' 	  => 'gamipress-switch',
			),
		), $achievement_fields ),
	) );

}
add_action( 'init', 'gamipress_register_achievements_shortcode' );

/**
 * Achievement List Shortcode
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes
 *
 * @return string 	   HTML markup
 */
function gamipress_achievements_shortcode( $atts = array () ) {

	global $gamipress_template_args;

	// Initialize GamiPress template args global
	$gamipress_template_args = array();

	$atts = shortcode_atts( array_merge( array(
		// Achievements atts
		'type'        	    => 'all',
		'limit'       	    => '10',
		'columns'           => '1',
		'filter' 	  	    => 'yes',
		'filter_value' 	    => 'all',
		'search' 	  	    => 'yes',
		'load_more' 	    => 'yes',
		'current_user'      => 'no',
		'user_id'     	    => '0',
		'wpms'        	    => 'no',
		'orderby'     	    => 'menu_order',
		'order'       	    => 'ASC',
		'include'     	    => '',
		'exclude'     	    => '',
	), gamipress_achievement_shortcode_defaults() ), $atts, 'gamipress_achievements' );

	gamipress_enqueue_scripts();

	// Single type check to use dynamic template
	$is_single_type = false;
	$types = explode( ',', $atts['type'] );

	if ( 'all' !== $atts['type'] && count( $types ) === 1 ) {
		$is_single_type = true;
	}

	// Force to set current user as user ID
	if( $atts['current_user'] === 'yes' ) {
		$atts['user_id'] = get_current_user_id();
	} else if( absint( $atts['user_id'] ) === 0 ) {
		$atts['user_id'] = get_current_user_id();
	}

    // Setup a query args to get the first page of achievements
    $query_args = $atts;

    // Initializes filter and search values
    $query_args['filter'] = $atts['filter_value'];
    $query_args['search'] = '';

    $query = gamipress_achievements_shortcode_query( $query_args );

    // GamiPress template args global
    // Important! This var need to be set before render the achievements list to prevent get overwritten by gamipress_render_achievement() used on gamipress_achievements_shortcode_query()
    $gamipress_template_args = $atts;
    $gamipress_template_args['query'] = $query;

	ob_start();
	if( $is_single_type ) {
		gamipress_get_template_part( 'achievements', $atts['type'] );
	} else {
		gamipress_get_template_part( 'achievements' );
	}
	$output = ob_get_clean();

	return $output;

}

/**
 * Achievement List Shortcode Query
 *
 * @since  1.4.5
 *
 * @param  array $args
 *
 * @return array
 */
function gamipress_achievements_shortcode_query( $args ) {

	// Setup our AJAX query vars
	$type       	= isset( $args['type'] )       		? $args['type']       	: false;
	$limit      	= isset( $args['limit'] )      		? $args['limit']      	: false;
	$offset     	= isset( $args['offset'] )     		? $args['offset']     	: false;
	$filter     	= isset( $args['filter'] )     		? $args['filter']     	: false;
	$search     	= isset( $args['search'] )     		? $args['search']     	: false;
	$current_user   = isset( $args['current_user'] )    ? $args['current_user'] : false;
	$user_id    	= isset( $args['user_id'] )         ? $args['user_id']    	: false;
	$orderby    	= isset( $args['orderby'] )    		? $args['orderby']    	: false;
	$order      	= isset( $args['order'] )      		? $args['order']      	: false;
	$wpms       	= isset( $args['wpms'] )       		? $args['wpms']       	: false;
	$include    	= isset( $args['include'] )    		? $args['include']    	: array();
	$exclude    	= isset( $args['exclude'] )    		? $args['exclude']    	: array();

	// On network wide active installs, we need to switch to main blog mostly for posts permalinks and thumbnails
	if( gamipress_is_network_wide_active() && ! is_main_site() ) {
		$blog_id = get_current_blog_id();
		switch_to_blog( get_main_site_id() );
	}

    // Turn no attributes to false
    if( $current_user === 'no' ) {
        $current_user = false;
    }

    if( $wpms === 'no' ) {
        $wpms = false;
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

		if( isset( $args[$field_id] ) ) {
			$template_args[$field_id] = $args[$field_id];
		}

	}

	// Convert $type to properly support multiple achievement types
	if ( 'all' == $type ) {
		$type = gamipress_get_achievement_types_slugs();
	} else {
		$type = explode( ',', $type );
	}

	// Prevent empty strings to be turned an array by explode()
	if ( ! is_array( $include ) && empty( $include ) ) {
		$include = array();
	}

	// Build $exclude array
	if ( ! is_array( $exclude )  && empty( $exclude ) ) {
		$exclude = array();
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
			'post_type'      	=> $type,
			'orderby'        	=> $orderby,
			'order'          	=> $order,
			'posts_per_page' 	=> absint( $limit ),
			'offset'         	=> absint( $offset ),
			'post_status'    	=> 'publish',
			'post__in' 			=> array(),
			'post__not_in'   	=> array_diff( $hidden, $earned_ids )
		);

		// Filter - query completed or non completed achievements
		if( $filter == 'completed' && ! empty( $earned_ids ) ) {
			// Include earned achievements
			$args[ 'post__in' ] = $earned_ids;
		} else if( $filter == 'not-completed' && ! empty( $earned_ids ) ) {
			// Exclude earned achievements
			$args[ 'post__not_in' ] = array_merge( $hidden, $earned_ids );
		}

		// Include certain achievements
		if( ! empty( $include ) ) {
			$args[ 'post__not_in' ] = array_diff( $args[ 'post__not_in' ], $include );
			$args[ 'post__in' ] = array_merge( $args[ 'post__in' ], $include  );
		}

		// Exclude certain achievements
		if( ! empty( $exclude ) ) {
			$args[ 'post__not_in' ] = array_merge( $args[ 'post__not_in' ], $exclude );
		}

		// Search
		if( $search ) {
			$args[ 's' ] = $search;
		}

		// Loop Achievements
		$achievement_posts = new WP_Query( $args );
		$query_count = absint( $achievement_posts->found_posts );

		while( $achievement_posts->have_posts() ) : $achievement_posts->the_post();

			$achievements .= gamipress_render_achievement( get_the_ID(), $template_args );

			$achievement_count++;

		endwhile;

		// Display a message for no results
		if( empty( $achievements ) ) {

			$current = current( $type );

			// If we have exactly one achievement type, get its plural name, otherwise use "achievements"
			$post_type_plural = ( 1 == count( $type ) && ! empty( $current ) ) ? get_post_type_object( $current )->labels->name : __( 'achievements' , 'gamipress' );

			// Setup our completion message
			$achievements .= '<div class="gamipress-no-results">';

			if ( 'completed' == $filter ) {
				$no_results_text = sprintf( __( 'No completed %s to display.', 'gamipress' ), strtolower( $post_type_plural ) );
			} else if ( 'not-completed' == $filter ) {
				$no_results_text = sprintf( __( 'You completed all %s.', 'gamipress' ), strtolower( $post_type_plural ) );
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

		if( get_current_blog_id() != $site_blog_id ) {
			// Come back to current blog
			restore_current_blog();
		}

	}

	// If switched to blog, return back to que current blog
	if( isset( $blog_id ) ) {
		switch_to_blog( $blog_id );
	}

	return array(
		'achievements'      => $achievements,
		'offset'            => $offset + $limit,
		'query_count'       => $query_count,
		'achievement_count' => $achievement_count,
	);

}
