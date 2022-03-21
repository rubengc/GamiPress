<?php
/**
 * GamiPress Achievement Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Achievement
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
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
		'description'     	=> __( 'Display a single achievement.', 'gamipress' ),
		'icon' 	            => 'awards',
		'group' 	        => 'gamipress',
		'output_callback' 	=> 'gamipress_achievement_shortcode',
		'fields'      		=> array(
			'id' => array(
				'name'              => __( 'Achievement', 'gamipress' ),
				'description'       => __( 'The achievement to render.', 'gamipress' ),
				'shortcode_desc'    => __( 'The ID of the achievement to render.', 'gamipress' ),
				'type'              => 'select',
                'classes' 	        => 'gamipress-post-selector',
                'attributes' 	    => array(
                    'data-post-type' => implode( ',',  gamipress_get_achievement_types_slugs() ),
                    'data-placeholder' => __( 'Select an achievement', 'gamipress' ),
                ),
				'default'           => '',
				'options_cb'        => 'gamipress_options_cb_posts'
			),
			'title' => array(
				'name'              => __( 'Show Title', 'gamipress' ),
				'description'       => __( 'Display the achievement title.', 'gamipress' ),
				'type' 		        => 'checkbox',
				'classes' 	        => 'gamipress-switch',
				'default'           => 'yes'
			),
            'title_size' => array(
                'name'              => __( 'Title Size', 'gamipress' ),
                'description'       => __( 'The achievement title size.', 'gamipress' ),
                'type' 		        => 'select',
                'classes' 		    => 'gamipress-font-size',
                'options' 	        => array(
                    'h1'    => __( 'Heading 1', 'gamipress' ),
                    'h2'    => __( 'Heading 2', 'gamipress' ),
                    'h3'    => __( 'Heading 3', 'gamipress' ),
                    'h4'    => __( 'Heading 4', 'gamipress' ),
                    'h5'    => __( 'Heading 5', 'gamipress' ),
                    'h6'    => __( 'Heading 6', 'gamipress' ),
                    'p'     => __( 'Paragraph', 'gamipress' ),
                ),
                'default'           => 'h2'
            ),
			'link' => array(
				'name'        => __( 'Show Link', 'gamipress' ),
				'description' => __( 'Add a link on achievement title to the achievement page.', 'gamipress' ),
				'type' 	        => 'checkbox',
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
            'thumbnail_size' => array(
                'name'        => __( 'Thumbnail Size (in pixels)', 'gamipress' ),
                'description' => __( 'The achievement featured image size in pixels. Leave empty to use the image size from settings.', 'gamipress' ),
                'type' 	=> 'text',
                'attributes' => array(
                    'type' => 'number',
                )
            ),
			'points_awarded' => array(
				'name'        => __( 'Show Points Awarded', 'gamipress' ),
				'description' => __( 'Display the achievement points awarded (on achievements where this setting is set).', 'gamipress' ),
				'type' 	=> 'checkbox',
				'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
            'points_awarded_thumbnail' => array(
                'name'        => __( 'Show Points Awarded Thumbnail', 'gamipress' ),
                'description' => __( 'Display the thumbnail of the points awarded.', 'gamipress' ),
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
                'description' => __( 'Display the times the user has earned this achievement (only for achievements that can be earned more that 1 time).', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),
            'global_times_earned' => array(
                'name'        => __( 'Show Times Earned By All Users', 'gamipress' ),
                'description' => __( 'Display the times that all users have earned this achievement.', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
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
            'heading' => array(
                'name'        => __( 'Show Steps Heading', 'gamipress' ),
                'description' => __( 'Display the achievement steps heading text.', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),
            'heading_size' => array(
                'name'              => __( 'Steps Heading Size', 'gamipress' ),
                'description'       => __( 'The achievement steps heading text size.', 'gamipress' ),
                'type' 		        => 'select',
                'classes' 		    => 'gamipress-font-size',
                'options' 	        => array(
                    'h1'    => __( 'Heading 1', 'gamipress' ),
                    'h2'    => __( 'Heading 2', 'gamipress' ),
                    'h3'    => __( 'Heading 3', 'gamipress' ),
                    'h4'    => __( 'Heading 4', 'gamipress' ),
                    'h5'    => __( 'Heading 5', 'gamipress' ),
                    'h6'    => __( 'Heading 6', 'gamipress' ),
                    'p'     => __( 'Paragraph', 'gamipress' ),
                ),
                'default'           => 'h4'
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
            'earners_limit' => array(
                'name'        => __( 'Maximum Earners', 'gamipress' ),
                'description' => __( 'Set the maximum number of earners to show (0 for no maximum).', 'gamipress' ),
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                    'step' => '1',
                ),
                'default' => '0'
            ),
			'layout' => array(
				'name'        => __( 'Layout', 'gamipress' ),
				'description' => __( 'Layout to show the achievement.', 'gamipress' ),
				'type' 		  => 'radio',
				'options' 	  => gamipress_get_layout_options(),
				'default' 	  => 'left',
				'inline' 	  => true,
				'classes' 	  => 'gamipress-image-options'
			),
            'align' => array(
                'name'        => __( 'Alignment', 'gamipress' ),
                'description' => __( 'Alignment to show the achievement.', 'gamipress' ),
                'type' 		  => 'radio',
                'options' 	  => gamipress_get_alignment_options(),
                'default' 	  => 'none',
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
 * @param  array $atts      Shortcode attributes
 * @param  string $content  Shortcode content
 *
 * @return string 	   HTML markup
 */
function gamipress_achievement_shortcode( $atts = array(), $content = '' ) {

    $shortcode = 'gamipress_achievement';

    $original_atts = $atts;

	$atts = shortcode_atts( gamipress_achievement_shortcode_defaults(), $atts, $shortcode );

    // ---------------------------
	// Shortcode Errors
    // ---------------------------

    // Get the achievement post
    $achievement = gamipress_get_post( $atts['id'] );
    $is_achievement = gamipress_is_achievement( $achievement );

    // Return if achievement id not specified
    if ( empty( $original_atts['id'] ) && ! $is_achievement ) {
        return gamipress_shortcode_error( __( 'Please, provide the achievement ID.', 'gamipress' ), $shortcode );
    }

    // Check if we're dealing with an achievement post
    if ( ! $is_achievement ) {
        return gamipress_shortcode_error( __( 'The id provided doesn\'t belong to a valid achievement.', 'gamipress' ), $shortcode );
    }

    // ---------------------------
    // Shortcode Processing
    // ---------------------------

    // Enqueue assets
	gamipress_enqueue_scripts();

	// On network wide active installs, we need to switch to main blog mostly for posts permalinks and thumbnails
    $blog_id = gamipress_switch_to_main_site_if_network_wide_active();

    // Initialize user ID to avoid undefined index errors
	if( ! isset( $atts['user_id'] ) ) {
        $atts['user_id'] = get_current_user_id();
    }

    // Get the current user if none wasn't specified
    if( absint( $atts['user_id'] ) === 0 ) {
        $atts['user_id'] = get_current_user_id();
    }

	// Get the post content and format the achievement display
	$output = gamipress_render_achievement( $achievement, $atts );

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
    return apply_filters( 'gamipress_achievement_shortcode_output', $output, $atts, $content );

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
		'id' 				        => get_the_ID(),
		'title' 			        => 'yes',
		'title_size' 			    => 'h2',
		'link' 				        => 'yes',
		'thumbnail' 		        => 'yes',
		'thumbnail_size' 		    => '',
		'points_awarded' 	        => 'yes',
		'points_awarded_thumbnail' 	=> 'yes',
		'excerpt'	  		        => 'yes',
        'times_earned' 	            => 'yes',
        'global_times_earned' 	    => 'no',
		'steps'	  			        => 'yes',
		'toggle' 			        => 'yes',
		'heading' 			        => 'yes',
		'heading_size' 			    => 'h4',
		'unlock_button' 	        => 'yes',
		'earners'	  		        => 'no',
		'earners_limit'	            => '0',
		'layout'	  		        => 'left',
		'align'	  		            => 'none',
	) );

}
