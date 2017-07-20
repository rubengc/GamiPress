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
		if( $slug === 'step' || $slug === 'points-award' ) {
			continue;
		}

		$achievement_types[$slug] = $data['plural_name'];
	}

	gamipress_register_shortcode( 'gamipress_achievements', array(
		'name'            => __( 'Achievement List', 'gamipress' ),
		'description'     => __( 'Output a list of achievements.', 'gamipress' ),
		'output_callback' => 'gamipress_achievements_shortcode',
		'fields'      => array(
			'type' => array(
				'name'        => __( 'Achievement Type(s)', 'gamipress' ),
				'description' => __( 'Single, or comma-separated list of, achievement type(s) to display.', 'gamipress' ),
				'type'        => 'select_multiple',
				'options'      => $achievement_types,
				'default'     => 'all',
			),
			'thumbnail' => array(
				'name'        => __( 'Show Thumbnails', 'gamipress' ),
				'description' => __( 'Display achievements featured images.', 'gamipress' ),
				'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
			'excerpt' => array(
				'name'        => __( 'Show Excerpts', 'gamipress' ),
				'description' => __( 'Display achievements short descriptions.', 'gamipress' ),
				'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
			'steps' => array(
				'name'        => __( 'Show Steps', 'gamipress' ),
				'description' => __( 'Display achievements steps.', 'gamipress' ),
				'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
			'show_filter' => array(
				'name'        => __( 'Show Filter', 'gamipress' ),
				'description' => __( 'Display filter controls.', 'gamipress' ),
				'type' 	=> 'checkbox',
				'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
			'show_search' => array(
				'name'        => __( 'Show Search', 'gamipress' ),
				'description' => __( 'Display a search input.', 'gamipress' ),
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
				'type'        => 'select_multiple',
				'default'     => '',
				'options_cb'  => 'gamipress_options_cb_posts'
			),
			'exclude' => array(
				'name'        => __( 'Exclude', 'gamipress' ),
				'description' => __( 'Comma-separated list of specific achievement IDs to exclude.', 'gamipress' ),
				'type'        => 'select_multiple',
				'default'     => '',
				'options_cb'  => 'gamipress_options_cb_posts'
			),
			'wpms' => array(
				'name'        => __( 'Include Multisite Achievements', 'gamipress' ),
				'description' => __( 'Show achievements from all network sites.', 'gamipress' ),
				'type' 	=> 'checkbox',
				'classes' => 'gamipress-switch',
			),
		),
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
		'type'        => 'all',
		'limit'       => '10',
		'show_filter' => 'yes',
		'show_search' => 'yes',
		'user_id'     => '0',
		'wpms'        => 'no',
		'orderby'     => 'menu_order',
		'order'       => 'ASC',
		'include'     => '',
		'exclude'     => '',
		'meta_key'    => '',
		'meta_value'  => '',

		// Single achievement atts
		'thumbnail' => 'yes',
		'excerpt'	  => 'yes',
		'steps'	  => 'yes',
	), $atts, 'gamipress_achievements' );

	wp_enqueue_style( 'gamipress' );

	// Single type check to use dynamic template
	$is_single_type = false;
	$types = explode( ',', $atts['type'] );

	if ( 'all' !== $atts['type'] && count( $types ) === 1 ) {
		$is_single_type = true;
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
