<?php
/**
 * GamiPress Inline Achievement Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Inline_Achievement
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       2.3.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_inline_achievement] shortcode
 *
 * @since 2.3.1
 */
function gamipress_register_inline_achievement_shortcode() {

	gamipress_register_shortcode( 'gamipress_inline_achievement', array(
		'name'            	=> __( 'Inline Achievement', 'gamipress' ),
		'description'     	=> __( 'Display a single achievement inline.', 'gamipress' ),
		'icon' 	            => 'awards',
		'group' 	        => 'gamipress',
		'output_callback' 	=> 'gamipress_inline_achievement_shortcode',
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
			'link' => array(
				'name'        => __( 'Show Link', 'gamipress' ),
				'description' => __( 'Show achievement as a link to the achievement page.', 'gamipress' ),
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
		),
	) );

}
add_action( 'init', 'gamipress_register_inline_achievement_shortcode' );

/**
 * Single achievement shortcode
 *
 * @since 2.3.1
 *
 * @param  array $atts      Shortcode attributes
 * @param  string $content  Shortcode content
 *
 * @return string 	   HTML markup
 */
function gamipress_inline_achievement_shortcode( $atts = array(), $content = '' ) {

    global $post, $gamipress_template_args;

    $shortcode = 'gamipress_inline_achievement';

    $original_atts = $atts;

	$atts = shortcode_atts( gamipress_inline_achievement_shortcode_defaults(), $atts, $shortcode );

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

    $gamipress_template_args = $atts;

    // Set up the post
    $post = $achievement;
    setup_postdata( $post );

    // Template rendering
    ob_start();
    gamipress_get_template_part( 'inline-achievement', $achievement->post_type );
    $output = ob_get_clean();

    $output = gamipress_parse_inline_output( $output );

    // Reset the post set up
    wp_reset_postdata();

	// If switched to blog, return back to que current blog
    if( $blog_id !== get_current_blog_id() && is_multisite() ) {
        restore_current_blog();
    }

    /**
     * Filter to override shortcode output
     *
     * @since 2.3.1
     *
     * @param string    $output     Final output
     * @param array     $atts       Shortcode attributes
     * @param string    $content    Shortcode content
     */
    return apply_filters( 'gamipress_inline_achievement_shortcode_output', $output, $atts, $content );

}

/**
 * Inline achievement shortcode defaults attributes values
 *
 * @since 2.3.1
 *
 * @return array
 */
function gamipress_inline_achievement_shortcode_defaults() {

    return apply_filters( 'gamipress_inline_achievement_shortcode_defaults', array(
        'id' 				        => get_the_ID(),
        'link' 				        => 'yes',
        'thumbnail' 		        => 'yes',
        'thumbnail_size' 		    => '',
    ) );

}
