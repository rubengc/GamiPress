<?php
/**
 * GamiPress Achievements Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Achievements
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
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

	$achievement_fields = GamiPress()->shortcodes['gamipress_achievement']->fields;

	unset( $achievement_fields['id'] );

	gamipress_register_shortcode( 'gamipress_achievements', array(
		'name'              => __( 'Achievement List', 'gamipress' ),
		'description'       => __( 'Display a list of achievements.', 'gamipress' ),
        'icon' 	            => 'awards',
        'group' 	        => 'gamipress',
		'output_callback'   => 'gamipress_achievements_shortcode',
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
					'search_value',
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
				'name'              => __( 'Achievement Type(s)', 'gamipress' ),
				'description'       => __( 'Achievement type(s) to display.', 'gamipress' ),
				'shortcode_desc'    => __( 'Single or comma-separated list of achievement type(s) to display.', 'gamipress' ),
				'type'              => 'advanced_select',
                'classes' 	        => 'gamipress-selector',
                'attributes' 	    => array(
                    'data-placeholder' => __( 'Default: All', 'gamipress' ),
                ),
				'multiple'          => true,
				'options_cb'        => 'gamipress_options_cb_achievement_types',
				'default'           => 'all',
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
			'search_value' => array(
				'name'        => __( 'Initial Search Value', 'gamipress' ),
				'description' => __( 'Set search initial value. If you hide the search, user won\'t be able to change this value.', 'gamipress' ),
				'type' 	=> 'text',
				'default' => ''
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
					'menu_order' 		=> __( 'Menu order', 'gamipress' ),
					'ID'         		=> __( 'Achievement', 'gamipress' ),
					'title'      		=> __( 'Title', 'gamipress' ),
					'date'       		=> __( 'Published date', 'gamipress' ),
					'modified'   		=> __( 'Last modified date', 'gamipress' ),
					'author'     		=> __( 'Author', 'gamipress' ),
					'rand'       		=> __( 'Random', 'gamipress' ),
					'points_awarded'    => __( 'Points awarded', 'gamipress' ),
					'points_to_unlock'  => __( 'Points to unlock', 'gamipress' ),
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
				'description' => __( 'Show achievements earned by the current logged in user.', 'gamipress' ),
				'type' 		  => 'checkbox',
				'classes' 	  => 'gamipress-switch',
			),
			'user_id' => array(
				'name'        => __( 'User', 'gamipress' ),
				'description' => __( 'Show achievements earned by a specific user.', 'gamipress' ),
				'type'        => 'select',
				'default'     => '',
                'options_cb'  => 'gamipress_options_cb_users'
			),
			'include' => array(
				'name'              => __( 'Include', 'gamipress' ),
				'description'       => __( 'Achievements to include.', 'gamipress' ),
				'shortcode_desc'    => __( 'Comma-separated list of specific achievement IDs to include.', 'gamipress' ),
				'type'              => 'advanced_select',
				'multiple'          => true,
				'classes' 	        => 'gamipress-post-selector',
                'attributes' 	    => array(
                    'data-post-type' => implode( ',',  gamipress_get_achievement_types_slugs() ),
                    'data-placeholder' => __( 'Select achievements', 'gamipress' ),
                ),
				'default'           => '',
				'options_cb'        => 'gamipress_options_cb_posts'
			),
			'exclude' => array(
				'name'              => __( 'Exclude', 'gamipress' ),
				'description'       => __( 'Achievements to exclude.', 'gamipress' ),
				'shortcode_desc'    => __( 'Comma-separated list of specific achievement IDs to exclude.', 'gamipress' ),
				'type'              => 'advanced_select',
				'multiple'          => true,
				'classes' 	        => 'gamipress-post-selector',
                'attributes' 	    => array(
                    'data-post-type' => implode( ',',  gamipress_get_achievement_types_slugs() ),
                    'data-placeholder' => __( 'Select achievements', 'gamipress' ),
                ),
				'default'           => '',
				'options_cb'        => 'gamipress_options_cb_posts'
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
 * @param  array    $atts      Shortcode attributes
 * @param  string   $content   Shortcode content
 *
 * @return string 	   HTML markup
 */
function gamipress_achievements_shortcode( $atts = array(), $content = '' ) {

	global $gamipress_template_args;

	// Initialize GamiPress template args global
	$gamipress_template_args = array();

    $shortcode = 'gamipress_achievements';

	$atts = shortcode_atts( array_merge( array(
		// Achievements atts
		'type'        	    => 'all',
		'columns'           => '1',
		'filter' 	  	    => 'yes',
		'filter_value' 	    => 'all',
		'search' 	  	    => 'yes',
		'search_value' 	  	=> '',
		'load_more' 	    => 'yes',
		'current_user'      => 'no',
		'user_id'     	    => '0',
		'limit'       	    => '10',
		'orderby'     	    => 'menu_order',
		'order'       	    => 'ASC',
		'include'     	    => '',
		'exclude'     	    => '',
		'wpms'        	    => 'no',
	), gamipress_achievement_shortcode_defaults() ), $atts, $shortcode );

	// Single type check to use dynamic template
	$is_single_type = false;
	$types = explode( ',', $atts['type'] );

	if ( $atts['type'] !== 'all' && count( $types ) === 1 ) {
		$is_single_type = true;
	}

    // ---------------------------
    // Shortcode Errors
    // ---------------------------

    if( $is_single_type ) {

        // Check if achievement type is valid
        if ( ! in_array( $atts['type'], gamipress_get_achievement_types_slugs() ) )
            return gamipress_shortcode_error( __( 'The type provided isn\'t a valid registered achievement type.', 'gamipress' ), $shortcode );

    } else if( $atts['type'] !== 'all' ) {

        // Let's check if all types provided are wrong
        $all_types_wrong = true;

        foreach( $types as $type ) {
            if ( in_array( $type, gamipress_get_achievement_types_slugs() ) )
                $all_types_wrong = false;
        }

        // just notify error if all types are wrong
        if( $all_types_wrong )
            return gamipress_shortcode_error( __( 'All types provided aren\'t valid registered achievement types.', 'gamipress' ), $shortcode );

    }

    // ---------------------------
    // Shortcode Processing
    // ---------------------------

    // Enqueue assets
    gamipress_enqueue_scripts();

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
    $query_args['search'] = isset( $atts['search_value'] ) ? $atts['search_value'] : '';

    $query = gamipress_achievements_shortcode_query( $query_args );

    // GamiPress template args global
    // Important! This var need to be set before render the achievements list to prevent get overwritten
    // by gamipress_render_achievement() used on gamipress_achievements_shortcode_query()
    $gamipress_template_args = $atts;
    $gamipress_template_args['query'] = $query;
    $gamipress_template_args['types'] = $types;

    // If we're dealing with multiple achievement types
    if ( 'all' === $gamipress_template_args['type'] ) {
        $gamipress_template_args['plural_label'] = __( 'achievements', 'gamipress' );
    } else {
        $gamipress_template_args['plural_label'] = ( 1 == count( $types ) && ! empty( $types[0] ) ) ? get_post_type_object( $types[0] )->labels->name : __( 'achievements', 'gamipress' );
    }

	ob_start();
	if( $is_single_type ) {
		gamipress_get_template_part( 'achievements', $atts['type'] );
	} else {
		gamipress_get_template_part( 'achievements' );
	}
	$output = ob_get_clean();

    /**
     * Filter to override shortcode output
     *
     * @since 1.6.5
     *
     * @param string    $output     Final output
     * @param array     $atts       Shortcode attributes
     * @param string    $content    Shortcode content
     */
    return apply_filters( 'gamipress_achievements_shortcode_output', $output, $atts, $content );

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
function gamipress_achievements_shortcode_query( $args = array() ) {

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
	$showed_ids    	= isset( $args['showed_ids'] )    	? $args['showed_ids']   : array();

	// On network wide active installs, we need to switch to main blog mostly for posts permalinks and thumbnails
    $blog_id = gamipress_switch_to_main_site_if_network_wide_active();

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

	// Ensure user ID as int
    $user_id = absint( $user_id );

	// Setup template vars
	$template_args = array(
		'user_id' => $user_id, // User ID on achievement is used to meet to which user apply earned checks
	);

	$achievement_fields = GamiPress()->shortcodes['gamipress_achievement']->fields;

	unset( $achievement_fields['id'] );

	// Loop achievement shortcode fields to pass them to the achievement template
	foreach( $achievement_fields as $field_id => $field_args ) {
		if( isset( $args[$field_id] ) )
			$template_args[$field_id] = $args[$field_id];
	}

	// Convert $type to properly support multiple achievement types
	if ( $type === 'all') {
		$type = gamipress_get_achievement_types_slugs();
    } else {
		$type = explode( ',', $type );
    }

	// Prevent empty strings to be turned an array by explode()
	if ( ! is_array( $include ) && empty( $include ) ) {
		$include = array();
    }

	// Build $exclude array
	if ( ! is_array( $exclude ) && empty( $exclude ) ) {
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

	if( $wpms && $wpms !== 'no' ) {
        // If we're polling all sites, grab an array of site IDs
		$sites = gamipress_get_network_site_ids();
	} else {
        // Otherwise, use only the current site
		$sites = array( get_current_blog_id() );
	}

	// On network wide active installs, force to just loop main site
	if( gamipress_is_network_wide_active() )
		$sites = array( get_main_site_id() );

	// Loop through each site (default is current site only)
	foreach( $sites as $site_blog_id ) {

		// If we're not polling the current site, switch to the site we're polling
        $current_site_blog_id = get_current_blog_id();

		if ( $current_site_blog_id != $site_blog_id )
			switch_to_blog( $site_blog_id );

		// Grab user earned achievements (used to filter the query)
		$earned_ids = gamipress_get_user_earned_achievement_ids( $user_id, $type );

        // If filter is set to load earned achievements and user hasn't earned anything, don't need to continue
        if( ! ( $filter === 'completed' && empty( $earned_ids ) ) ) {

            // Query Achievements
            $query_args = array(
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
            if( $filter === 'completed' && ! empty( $earned_ids ) ) {
                // Include earned achievements
                $query_args[ 'post__in' ] = $earned_ids;
            } else if( $filter === 'not-completed' && ! empty( $earned_ids ) ) {
                // Exclude earned achievements
                $query_args[ 'post__not_in' ] = array_merge( $hidden, $earned_ids );
            }

            // Include certain achievements
            if( ! empty( $include ) ) {
                $query_args[ 'post__not_in' ] = array_diff( $query_args[ 'post__not_in' ], $include );
                $query_args[ 'post__in' ] = array_merge( $query_args[ 'post__in' ], $include  );
            }

            // Exclude certain achievements
            if( ! empty( $exclude ) ) {
                $query_args[ 'post__not_in' ] = array_merge( $query_args[ 'post__not_in' ], $exclude );
            }

            // Search
            if( $search ) {
                $query_args[ 's' ] = $search;
            }

			// Order By
			if( in_array( $orderby, array( 'points_awarded', 'points_to_unlock' ) ) ) {
                $query_args['meta_key'] = ( $orderby === 'points_awarded' ? '_gamipress_points' : '_gamipress_points_to_unlock' );
                $query_args['orderby'] = 'meta_value_num';
			}

			// Process already displayed achievements
            if( ! empty( $showed_ids ) ) {
                // Exclude already displayed achievements
                $query_args[ 'post__in' ] = array_diff( $query_args[ 'post__in' ], $showed_ids  );
                $query_args[ 'post__not_in' ] = array_merge( $query_args[ 'post__not_in' ], $showed_ids );
                // Offset not needed since displayed post are getting already excluded
                unset( $query_args['offset'] );
            }

            // Prevent to display posts excluded
            if( ! empty( $query_args[ 'post__in' ] ) && ! empty( $query_args[ 'post__not_in' ] ) ) {
                $query_args[ 'post__in' ] = array_diff( $query_args[ 'post__in' ], $query_args[ 'post__not_in' ]  );
            }

            /**
             * Filters achievements list query args
             *
             * @since 1.5.9
             *
             * @param array $query_args Query args to be passed to WP_Query
             * @param array $args       Function received args (Note: to pass your own args on achievements list request, check JS event 'gamipress_achievements_list_request_data')
             *
             * @return array
             */
            $query_args = apply_filters( 'gamipress_achievements_shortcode_query_args', $query_args, $args );

            // Query achievements
            $achievement_posts = new WP_Query( $query_args );
            $query_count = absint( $achievement_posts->found_posts );

            // Add displayed post to the count since they have been excluded on the query
            if( ! empty( $showed_ids ) )
                $query_count += count( $showed_ids );

            // Loop achievements found
            while( $achievement_posts->have_posts() ) : $achievement_posts->the_post();
                // Render the achievement passing the template args
                $achievements .= gamipress_render_achievement( get_the_ID(), $template_args );
                $achievement_count++;
            endwhile;

        }

		// Display a message for no results
		if( empty( $achievements ) && $offset === 0 ) {

			$current = current( $type );

			// If we have exactly one achievement type, get its plural name, otherwise use "achievements"
			$post_type_plural = ( count( $type ) == 1 && ! empty( $current ) ) ? gamipress_get_achievement_type_plural( $current ) : __( 'achievements' , 'gamipress' );

			// Setup our completion message
			$achievements .= '<div class="gamipress-no-results">';

			if ( 'completed' === $filter )
				$no_results_text = sprintf( __( 'No completed %s to display.', 'gamipress' ), strtolower( $post_type_plural ) );
			else if ( 'not-completed' === $filter )
				$no_results_text = sprintf( __( 'You completed all %s.', 'gamipress' ), strtolower( $post_type_plural ) );
			else
				$no_results_text = sprintf( __( 'No %s to display.', 'gamipress' ), strtolower( $post_type_plural ) );

			/**
			 * Filter achievements no results text
			 *
             * @since 1.0.0
             *
			 * @param string $no_results_text
             *
             * @return string
			 */
			$no_results_text = apply_filters( 'gamipress_achievements_no_results_text', $no_results_text );

			$achievements .= '<p>' . $no_results_text . '</p>';

			$achievements .= '</div><!-- .gamipress-no-results -->';
		}

        // Come back to current blog
		if( $current_site_blog_id != $site_blog_id && is_multisite() )
			restore_current_blog();

	}

	// If switched to blog, return back to que current blog
	if( $blog_id !== get_current_blog_id() && is_multisite() )
        restore_current_blog();

	$query = array(
        'achievements'      => $achievements,
        'offset'            => $offset + $limit,
        'query_count'       => $query_count,
        'achievement_count' => $achievement_count,
    );

    /**
     * Filter the achievements query
     *
     * @since 1.6.5
     *
     * @param array $response
     * @param array $args
     *
     * @return array
     */
	return apply_filters( 'gamipress_achievements_shortcode_query', $query, $args );

}
