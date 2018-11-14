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
 * Register the [gamipress_achievement] shortcode
 *
 * @since 1.0.0
 */
function gamipress_register_achievement_shortcode() {

	gamipress_register_shortcode( 'gamipress_achievement', array(
		'name'            	=> __( 'Single Achievement', 'gamipress' ),
		'description'     	=> __( 'Render a single achievement.', 'gamipress' ),
		'icon' 	            => 'awards',
		'output_callback' 	=> 'gamipress_achievement_shortcode',
		'fields'      		=> array(
			'id' => array(
				'name'        => __( 'Achievement ID', 'gamipress' ),
				'description' => __( 'The ID of the achievement to render.', 'gamipress' ),
				'type'        => 'select',
				'default'     => '',
				'options_cb'  => 'gamipress_options_cb_posts'
			),
			'title' => array(
				'name'        => __( 'Show Title', 'gamipress' ),
				'description' => __( 'Display the achievement title.', 'gamipress' ),
				'type' 		  => 'checkbox',
				'classes' 	  => 'gamipress-switch',
				'default' => 'yes'
			),
			'link' => array(
				'name'        => __( 'Show Link', 'gamipress' ),
				'description' => __( 'Add a link on achievement title to the achievement page.', 'gamipress' ),
				'type' 	=> 'checkbox',
				'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
			'thumbnail' => array(
				'name'        => __( 'Show Thumbnail', 'gamipress' ),
				'description' => __( 'Display the achievement featured image.', 'gamipress' ),
				'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
			'points_awarded' => array(
				'name'        => __( 'Show Points Awarded', 'gamipress' ),
				'description' => __( 'Display the achievement points awarded (on achievements where this setting is set).', 'gamipress' ),
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
            'times_earned' => array(
                'name'        => __( 'Show Times Earned', 'gamipress' ),
                'description' => __( 'Display the times user has earned this achievement (on achievements allowed to be earned more that 1 time).', 'gamipress' ),
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
			'toggle' => array(
				'name'        => __( 'Show Steps Toggle', 'gamipress' ),
				'description' => __( 'Display the achievement steps toggle.', 'gamipress' ),
				'type' 	=> 'checkbox',
				'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
			'unlock_button' => array(
				'name'        => __( 'Show Unlock Button', 'gamipress' ),
				'description' => __( 'Display the "Unlock using points" (on achievements where unlock with points is allowed).', 'gamipress' ),
				'type' 	=> 'checkbox',
				'classes' => 'gamipress-switch'
			),
			'earners' => array(
				'name'        => __( 'Show Earners', 'gamipress' ),
				'description' => __( 'Display a list of users that has earned the achievement.', 'gamipress' ),
				'type' 	=> 'checkbox',
				'classes' => 'gamipress-switch'
			),
			'layout' => array(
				'name'        => __( 'Layout', 'gamipress' ),
				'description' => __( 'Layout to show the achievement.', 'gamipress' ),
				'type' 		  => 'radio',
				'options' 	  => array(
					'left' 		=> '<img src="' . GAMIPRESS_URL . 'assets/img/layout-left.svg">' . __( 'Left', 'gamipress' ),
					'top' 		=> '<img src="' . GAMIPRESS_URL . 'assets/img/layout-top.svg">' . __( 'Top', 'gamipress' ),
					'right' 	=> '<img src="' . GAMIPRESS_URL . 'assets/img/layout-right.svg">' . __( 'Right', 'gamipress' ),
					'bottom' 	=> '<img src="' . GAMIPRESS_URL . 'assets/img/layout-bottom.svg">' . __( 'Bottom', 'gamipress' ),
					'none' 		=> '<img src="' . GAMIPRESS_URL . 'assets/img/layout-none.svg">' . __( 'None', 'gamipress' ),
				),
				'default' 	  => 'left',
				'inline' 	  => true,
				'classes' 	  => 'gamipress-image-options'
			),
		),
	) );

}
add_action( 'init', 'gamipress_register_achievement_shortcode' );

/**
 * Single achievement shortcode
 *
 * @since 1.0.0
 *
 * @param  array $atts Shortcode attributes
 *
 * @return string 	   HTML markup
 */
function gamipress_achievement_shortcode( $atts = array() ) {

    $original_atts = $atts;

	$atts = shortcode_atts( gamipress_achievement_shortcode_defaults(), $atts, 'gamipress_achievement' );

    // ---------------------------
	// Shortcode Errors
    // ---------------------------

    // Get the achievement post
    $achievement = gamipress_get_post( $atts['id'] );
    $is_achievement = gamipress_is_achievement( $achievement );

    // Return if achievement id not specified
    if ( empty( $original_atts['id'] ) && ! $is_achievement )
        return gamipress_shortcode_error( __( 'Please, provide the id attribute.', 'gamipress' ), 'gamipress_achievement' );

    // Check if we're dealing with an achievement post
    if ( ! $is_achievement )
        return gamipress_shortcode_error( __( 'The id provided doesn\'t belong to a valid achievement.', 'gamipress' ), 'gamipress_achievement' );

    // ---------------------------
    // Shortcode Processing
    // ---------------------------

    // Enqueue assets
	gamipress_enqueue_scripts();

	// On network wide active installs, we need to switch to main blog mostly for posts permalinks and thumbnails
	if( gamipress_is_network_wide_active() && ! is_main_site() ) {
		$blog_id = get_current_blog_id();
		switch_to_blog( get_main_site_id() );
	}

    // Initialize user ID to avoid undefined index errors
	if( ! isset( $atts['user_id'] ) )
        $atts['user_id'] = get_current_user_id();

    // Get the current user if none wasn't specified
    if( absint( $atts['user_id'] ) === 0 )
        $atts['user_id'] = get_current_user_id();

	// Get the post content and format the achievement display
	$output = gamipress_render_achievement( $achievement, $atts );

	// If switched to blog, return back to que current blog
	if( isset( $blog_id ) ) {
		switch_to_blog( $blog_id );
	}

	// Return our rendered achievement
	return $output;

}

/**
 * Single achievement shortcode defaults attributes values
 *
 * @since 1.3.9.4
 *
 * @return array
 */
function gamipress_achievement_shortcode_defaults() {

	return apply_filters( 'gamipress_achievement_shortcode_defaults', array(
		'id' 				=> get_the_ID(),
		'title' 			=> 'yes',
		'link' 				=> 'yes',
		'thumbnail' 		=> 'yes',
		'points_awarded' 	=> 'yes',
		'excerpt'	  		=> 'yes',
        'times_earned' 	    => 'yes',
		'steps'	  			=> 'yes',
		'toggle' 			=> 'yes',
		'unlock_button' 	=> 'yes',
		'earners'	  		=> 'no',
		'layout'	  		=> 'left',
	) );

}
