<?php
/**
 * GamiPress Ranks Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Ranks
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register [gamipress_ranks] shortcode
 *
 * @since 1.0.0
 */
function gamipress_register_ranks_shortcode() {

	// Setup the rank fields
	$rank_fields = GamiPress()->shortcodes['gamipress_rank']->fields;

	unset( $rank_fields['id'] );

	gamipress_register_shortcode( 'gamipress_ranks', array(
		'name'              => __( 'Rank List', 'gamipress' ),
		'description'       => __( 'Display a list of ranks.', 'gamipress' ),
        'icon' 	            => 'rank',
        'group' 	        => 'gamipress',
		'output_callback'   => 'gamipress_ranks_shortcode',
		'tabs' => array(
			'general' => array(
				'icon' => 'dashicons-admin-generic',
				'title' => __( 'General', 'gamipress' ),
				'fields' => array(
					'type',
					'columns',
				),
			),
			'rank' => array(
				'icon' => 'dashicons-awards',
				'title' => __( 'Rank', 'gamipress' ),
				'fields' => array_keys( $rank_fields ),
			),
			'query' => array(
				'icon' => 'dashicons-search',
				'title' => __( 'Query', 'gamipress' ),
				'fields' => array(
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
				'name'              => __( 'Rank Type(s)', 'gamipress' ),
				'description'       => __( 'Rank type(s) to display.', 'gamipress' ),
				'shortcode_desc'    => __( 'Single or comma-separated list of rank type(s) to display.', 'gamipress' ),
				'type'              => 'advanced_select',
				'multiple'          => true,
                'classes' 	        => 'gamipress-selector',
                'attributes' 	    => array(
                    'data-placeholder' => __( 'Default: All', 'gamipress' ),
                ),
				'options_cb'        => 'gamipress_options_cb_rank_types',
				'default'           => 'all',
			),
			'columns' => array(
				'name'        => __( 'Columns', 'gamipress' ),
				'description' => __( 'Columns to divide ranks.', 'gamipress' ),
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
			'orderby' => array(
				'name'        => __( 'Order By', 'gamipress' ),
				'description' => __( 'Parameter to use for sorting.', 'gamipress' ),
				'type'        => 'select',
				'options'      => array(
					'priority' 	 		=> __( 'Priority', 'gamipress' ),
					'ID'         		=> __( 'Rank ID', 'gamipress' ),
					'title'      		=> __( 'Title', 'gamipress' ),
					'date'       		=> __( 'Published date', 'gamipress' ),
					'modified'   		=> __( 'Last modified date', 'gamipress' ),
					'rand'       		=> __( 'Random', 'gamipress' ),
					'points_to_unlock'  => __( 'Points to unlock', 'gamipress' ),
				),
				'default'     => 'priority',
			),
			'order' => array(
				'name'        => __( 'Order', 'gamipress' ),
				'description' => __( 'Sort order.', 'gamipress' ),
				'type'        => 'select',
				'options'      => array( 'ASC' => __( 'Ascending', 'gamipress' ), 'DESC' => __( 'Descending', 'gamipress' ) ),
				'default'     => 'DESC',
			),
			'current_user' => array(
				'name'        => __( 'Current User', 'gamipress' ),
				'description' => __( 'Show the current logged in user ranks.', 'gamipress' ),
				'type' 		  => 'checkbox',
				'classes' 	  => 'gamipress-switch',
			),
			'user_id' => array(
				'name'        => __( 'User', 'gamipress' ),
				'description' => __( 'Show a specific user ranks.', 'gamipress' ),
				'type'        => 'select',
				'default'     => '',
				'options_cb'  => 'gamipress_options_cb_users'
			),
			'include' => array(
				'name'              => __( 'Include', 'gamipress' ),
				'description'       => __( 'Ranks to include.', 'gamipress' ),
				'shortcode_desc'    => __( 'Comma-separated list of specific rank IDs to include.', 'gamipress' ),
				'type'              => 'advanced_select',
				'multiple'          => true,
				'default'           => '',
                'classes' 	        => 'gamipress-post-selector',
                'attributes' 	    => array(
                    'data-post-type' => implode( ',',  gamipress_get_rank_types_slugs() ),
                    'data-placeholder' => __( 'Select ranks', 'gamipress' ),
                ),
				'options_cb'        => 'gamipress_options_cb_posts'
			),
			'exclude' => array(
				'name'              => __( 'Exclude', 'gamipress' ),
				'description'       => __( 'Ranks to exclude.', 'gamipress' ),
				'shortcode_desc'    => __( 'Comma-separated list of specific rank IDs to exclude.', 'gamipress' ),
				'type'              => 'advanced_select',
				'multiple'          => true,
                'classes' 	        => 'gamipress-post-selector',
                'attributes' 	    => array(
                    'data-post-type' => implode( ',',  gamipress_get_rank_types_slugs() ),
                    'data-placeholder' => __( 'Select ranks', 'gamipress' ),
                ),
				'default'           => '',
				'options_cb'        => 'gamipress_options_cb_posts'
			),
			'wpms' => array(
				'name'        => __( 'Include Multisite Ranks', 'gamipress' ),
				'description' => __( 'Show ranks from all network sites.', 'gamipress' ),
				'type' 		  => 'checkbox',
				'classes' 	  => 'gamipress-switch',
			),
		), $rank_fields ),
	) );

}
add_action( 'init', 'gamipress_register_ranks_shortcode' );

/**
 * Rank List Shortcode
 *
 * @since  1.0.0
 *
 * @param  array    $atts       Shortcode attributes
 * @param  string   $content    Shortcode content
 *
 * @return string 	   HTML markup
 */
function gamipress_ranks_shortcode( $atts = array(), $content = '' ) {

	global $gamipress_template_args;

	// Initialize GamiPress template args global
	$gamipress_template_args = array();

    $shortcode = 'gamipress_ranks';

	$atts = shortcode_atts( array_merge( array(
		// Ranks atts
		'type'        	=> 'all',
		'columns'       => '1',
		'current_user' 	=> 'no',
		'user_id' 		=> '0',
		'wpms'        	=> 'no',
		'orderby'     	=> 'priority',
		'order'       	=> 'DESC',
		'include'     	=> '',
		'exclude'     	=> '',
	), gamipress_rank_shortcode_defaults() ), $atts, $shortcode );

	// Single type check to use dynamic template
	$is_single_type = false;
	$types = explode( ',', $atts['type'] );

	if( $atts['type'] === 'all') {
		$types = gamipress_get_rank_types_slugs();
	} else if ( count( $types ) === 1 ) {
		$is_single_type = true;
	}

    // ---------------------------
    // Shortcode Errors
    // ---------------------------

    if( $is_single_type ) {

        // Check if rank type is valid
        if ( ! in_array( $atts['type'], gamipress_get_rank_types_slugs() ) )
            return gamipress_shortcode_error( __( 'The type provided isn\'t a valid registered rank type.', 'gamipress' ), $shortcode );

    } else if( $atts['type'] !== 'all' ) {

        // Let's check if all types provided are wrong
        $all_types_wrong = true;

        foreach( $types as $type ) {
            if ( in_array( $type, gamipress_get_rank_types_slugs() ) )
                $all_types_wrong = false;
        }

        // just notify error if all types are wrong
        if( $all_types_wrong )
            return gamipress_shortcode_error( __( 'All types provided aren\'t valid registered rank types.', 'gamipress' ), $shortcode );

    }

    // ---------------------------
    // Shortcode Processing
    // ---------------------------

    // Enqueue assets
    gamipress_enqueue_scripts();

    // On network wide active installs, we need to switch to main blog mostly for posts permalinks and thumbnails
    $blog_id = gamipress_switch_to_main_site_if_network_wide_active();

	// If we're polling all sites, grab an array of site IDs
	if( $atts['wpms'] === 'yes' && ! gamipress_is_network_wide_active() )
		$sites = gamipress_get_network_site_ids();
	// Otherwise, use only the current site
	else
		$sites = array( get_current_blog_id() );

	// On network wide active installs, force to just loop main site
	if( gamipress_is_network_wide_active() ) {
		$sites = array( get_main_site_id() );
	}

	// Just render ranks of users if current_user is yes or a specific user_id has been defined
	$is_user_ranks = false;

	// Force to set current user as user ID
	if( $atts['current_user'] === 'yes' ) {
		$atts['user_id'] = get_current_user_id();
		$is_user_ranks = true;
	} else if( absint( $atts['user_id'] ) !== 0 ) {
		$is_user_ranks = true;
	} else if( absint( $atts['user_id'] ) === 0 ) {
		$atts['user_id'] = get_current_user_id();
	}

	// GamiPress template args global
	$gamipress_template_args = $atts;

	$gamipress_template_args['is_user_ranks'] = $is_user_ranks;

	// Setup template vars
	$template_args = array(
		'user_id' 		=> $atts['user_id'], // User ID on rank is used to meet to which user apply earned checks
	);

	$rank_fields = GamiPress()->shortcodes['gamipress_rank']->fields;

	unset( $rank_fields['id'] );

	// Loop rank shortcode fields to pass to the rank template
	foreach( $rank_fields as $field_id => $field_args ) {
		if( isset( $atts[$field_id] ) )
			$template_args[$field_id] = $atts[$field_id];
	}

	$gamipress_template_args['template_args'] = $template_args;

	// Setup query args if not is a user ranks
	if( ! $is_user_ranks ) {

		// Turn order by value into the real value
		if( $atts['orderby'] === 'priority') {
			$atts['orderby'] = 'menu_order';
		}

		$query_args = array(
			'post_type'         =>	'', // Is set on each rank type
			'orderby'           =>	$atts['orderby'],
			'order'             =>	$atts['order'],
			'posts_per_page'    =>	-1,
			'post_status'       => 'publish',
			'suppress_filters'  => false,
		);

		$include = ! is_array( $atts['include'] ) && ! empty( $atts['include'] ) ? explode( ',', $atts['include'] ) : $atts['include'];
		$exclude = ! is_array( $atts['exclude'] ) && ! empty( $atts['exclude'] ) ? explode( ',', $atts['exclude'] ) : $atts['exclude'];

		// Include certain ranks
		if ( ! empty( $include ) ) {
			$query_args[ 'post__in' ] = $include;
		}

		// Exclude certain ranks
		if ( ! empty( $exclude ) ) {
			$query_args[ 'post__not_in' ] = $exclude;
		}

		// Order By
		if( $atts['orderby'] === 'points_to_unlock' ) {
			$query_args['meta_key'] = '_gamipress_points_to_unlock';
			$query_args['orderby'] = 'meta_value_num';
		}

	}

	// Get the ranks of all registered network sites
	$gamipress_template_args['rank-types'] = array();

	// Loop through each site (default is current site only)
	foreach( $sites as $site_blog_id ) {

		// If we're not polling the current site, switch to the site we're polling
        $current_site_blog_id = get_current_blog_id();

		if ( $current_site_blog_id != $site_blog_id ) {
			switch_to_blog( $site_blog_id );
		}

		foreach( $types as $rank_type ) {

			// Initialize ranks type IDs array
			if( ! isset( $gamipress_template_args['rank-types'][$rank_type] ) ) {
				$gamipress_template_args['rank-types'][$rank_type] = array();
			}

			if( $is_user_ranks ) {
				// if is user ranks, then just get current user rank
				$gamipress_template_args['rank-types'][$rank_type][] = gamipress_get_user_rank_id( $atts['user_id'], $rank_type );
			} else {

				// Set the dynamic post_type
				$query_args['post_type'] = $rank_type;

				$query = new WP_Query( $query_args );
				$rank_posts = $query->get_posts();

				// Loop ranks
				foreach( $rank_posts as $rank_post ) {
					// Add the rank ID to the rank type IDs array
					$gamipress_template_args['rank-types'][$rank_type][] = $rank_post->ID;
				}

			}


		}



		if ( $current_site_blog_id != $site_blog_id && is_multisite() ) {
			// Come back to current blog
			restore_current_blog();
		}

	}

	ob_start();
	if( $is_single_type ) {
		gamipress_get_template_part( 'ranks', $atts['type'] );
	} else {
		gamipress_get_template_part( 'ranks' );
	}
	$output = ob_get_clean();

	// If switched to blog, return back to que current blog
    if( $blog_id !== get_current_blog_id() && is_multisite() ) {
        restore_current_blog();
    }

    /**
     * Filter to override shortcode output
     *
     * @since 1.6.5
     *
     * @param string    $output     Final output
     * @param array     $atts       Shortcode attributes
     * @param string    $content    Shortcode content
     */
    return apply_filters( 'gamipress_ranks_shortcode_output', $output, $atts, $content );

}
