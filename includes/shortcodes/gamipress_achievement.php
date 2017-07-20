<?php
/**
 * GamiPress Achievement Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Achievement
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_achievement] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_register_achievement_shortcode() {
	gamipress_register_shortcode( 'gamipress_achievement', array(
		'name'            => __( 'Single Achievement', 'gamipress' ),
		'description'     => __( 'Render a single achievement.', 'gamipress' ),
		'output_callback' => 'gamipress_achievement_shortcode',
		'fields'      => array(
			'id' => array(
				'name'        => __( 'Achievement ID', 'gamipress' ),
				'description' => __( 'The ID of the achievement to render.', 'gamipress' ),
				'type'        => 'select',
				'default'     => '',
				'options_cb'  => 'gamipress_options_cb_posts'
			),
			'thumbnail' => array(
				'name'        => __( 'Show Thumbnail', 'gamipress' ),
				'description' => __( 'Display the achievement featured image.', 'gamipress' ),
				'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
			'excerpt' => array(
				'name'        => __( 'Show Excerpt', 'gamipress' ),
				'description' => __( 'Display the achievement short description.', 'gamipress' ),
				'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
			'steps' => array(
				'name'        => __( 'Show Steps', 'gamipress' ),
				'description' => __( 'Display the achievement steps.', 'gamipress' ),
				'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
		),
	) );
}
add_action( 'init', 'gamipress_register_achievement_shortcode' );

/**
 * Single Achievement Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_achievement_shortcode( $atts = array() ) {
	// get the post id
	$atts = shortcode_atts( array(
	  'id' => get_the_ID(),
	  'thumbnail' => 'yes',
	  'excerpt'	  => 'yes',
	  'steps'	  => 'yes',
	), $atts, 'gamipress_achievement' );

	// return if post id not specified
	if ( empty($atts['id']) )
	  return;

	wp_enqueue_style( 'gamipress' );
	wp_enqueue_script( 'gamipress-achievements' );

	// get the post content and format the badge display
	$achievement = get_post( $atts['id'] );
	$output = '';

	// If we're dealing with an achievement post
	if ( gamipress_is_achievement( $achievement ) ) {
		$output .= gamipress_render_achievement( $achievement, $atts );
	}

	// Return our rendered achievement
	return $output;
}
