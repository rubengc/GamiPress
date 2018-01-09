<?php
/**
 * GamiPress Rank Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Rank
 * @since       1.3.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_rank] shortcode
 *
 * @since 1.3.1
 */
function gamipress_register_rank_shortcode() {

	gamipress_register_shortcode( 'gamipress_rank', array(
		'name'            => __( 'Single Rank', 'gamipress' ),
		'description'     => __( 'Render a single rank.', 'gamipress' ),
		'output_callback' => 'gamipress_rank_shortcode',
		'fields'      => array(
			'id' => array(
				'name'        => __( 'Rank ID', 'gamipress' ),
				'description' => __( 'The ID of the rank to render.', 'gamipress' ),
				'type'        => 'select',
				'default'     => '',
				'options_cb'  => 'gamipress_options_cb_posts'
			),
			'title' => array(
				'name'        => __( 'Show Title', 'gamipress' ),
				'description' => __( 'Display the rank title.', 'gamipress' ),
				'type' 	=> 'checkbox',
				'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
			'thumbnail' => array(
				'name'        => __( 'Show Thumbnail', 'gamipress' ),
				'description' => __( 'Display the rank featured image.', 'gamipress' ),
				'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
			'excerpt' => array(
				'name'        => __( 'Show Excerpt', 'gamipress' ),
				'description' => __( 'Display the rank short description.', 'gamipress' ),
				'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
			'requirements' => array(
				'name'        => __( 'Show Requirements', 'gamipress' ),
				'description' => __( 'Display the rank requirements.', 'gamipress' ),
				'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
			'toggle' => array(
				'name'        => __( 'Show Requirements Toggle', 'gamipress' ),
				'description' => __( 'Display the rank requirements toggle.', 'gamipress' ),
				'type' 	=> 'checkbox',
				'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
			'earners' => array(
				'name'        => __( 'Show Earners', 'gamipress' ),
				'description' => __( 'Display a list of users that actually are in this rank.', 'gamipress' ),
				'type' 	=> 'checkbox',
				'classes' => 'gamipress-switch'
			),
		),
	) );

}
add_action( 'init', 'gamipress_register_rank_shortcode' );

/**
 * Single Rank Shortcode
 *
 * @since  1.3.1
 *
 * @param  array $atts Shortcode attributes
 *
 * @return string 	   HTML markup
 */
function gamipress_rank_shortcode( $atts = array() ) {

	$atts = shortcode_atts( array(

	  	'id' 			=> get_the_ID(),
	  	'user_id' 		=> '0',
	  	'title' 		=> 'yes',
	  	'thumbnail' 	=> 'yes',
	  	'excerpt'	  	=> 'yes',
	  	'requirements'	=> 'yes',
	  	'toggle' 		=> 'yes',
	  	'earners'	  	=> 'no',

	), $atts, 'gamipress_rank' );

	// return if post id not specified
	if ( empty($atts['id']) )
	  return;

	gamipress_enqueue_scripts();

	// Get the post content and format the rank display
	$rank = get_post( $atts['id'] );

	// Get the current user if one wasn't specified
	if( absint( $atts['user_id'] ) === 0 )
		$atts['user_id'] = get_current_user_id();

	$output = '';

	// If we're dealing with an rank post
	if ( gamipress_is_rank( $rank ) ) {
		$output .= gamipress_render_rank( $rank, $atts );
	}

	// Return our rendered rank
	return $output;
}
