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
 * Register [gamipress_achievements] shortcode.
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
				'description' => __( 'Display filter controls.', 'gamipress' ),
				'type' 	=> 'checkbox',
				'classes' => 'gamipress-switch',
				'default' => 'yes'
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
					'ID'         => __( 'Achievement ID', 'gamipress' ),
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
				'name'        => __( 'User ID', 'gamipress' ),
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
 * Achievement List Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_achievements_shortcode( $atts = array () ) {
	global $gamipress_template_args;

	// Initialize GamiPress template args global
	$gamipress_template_args = array();

	$atts = shortcode_atts( array(
		// Achievements atts
		'type'        	=> 'all',
		'limit'       	=> '10',
		'columns'       => '1',
		'filter' 	  	=> 'yes',
		'search' 	  	=> 'yes',
		'load_more' 	=> 'yes',
		'current_user'  => 'no',
		'user_id'     	=> '0',
		'wpms'        	=> 'no',
		'orderby'     	=> 'menu_order',
		'order'       	=> 'ASC',
		'include'     	=> '',
		'exclude'     	=> '',

		// Single achievement atts
		'title' 		=> 'yes',
		'thumbnail' 	=> 'yes',
		'excerpt'	  	=> 'yes',
		'steps'	  		=> 'yes',
		'toggle' 		=> 'yes',
		'earners'	  	=> 'no',
	), $atts, 'gamipress_achievements' );

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

	// GamiPress template args global
	$gamipress_template_args = $atts;

	ob_start();
	if( $is_single_type ) {
		gamipress_get_template_part( 'achievements', $atts['type'] );
	} else {
		gamipress_get_template_part( 'achievements' );
	}
	$output = ob_get_clean();

	return $output;

}
