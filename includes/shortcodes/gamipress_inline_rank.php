<?php
/**
 * GamiPress Inline Rank Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Inline_Rank
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       2.3.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the [gamipress_inline_rank] shortcode
 *
 * @since 2.3.1
 */
function gamipress_register_inline_rank_shortcode() {

	gamipress_register_shortcode( 'gamipress_inline_rank', array(
		'name'              => __( 'Inline Rank', 'gamipress' ),
		'description'       => __( 'Display a single rank inline.', 'gamipress' ),
        'icon' 	            => 'rank',
        'group' 	        => 'gamipress',
		'output_callback'   => 'gamipress_inline_rank_shortcode',
		'fields'      	  => array(
			'id' => array(
				'name'              => __( 'Rank', 'gamipress' ),
				'description'       => __( 'Rank to render.', 'gamipress' ),
				'shortcode_desc'    => __( 'The ID of the rank to render.', 'gamipress' ),
				'type'              => 'select',
                'classes' 	        => 'gamipress-post-selector',
                'attributes' 	    => array(
                    'data-post-type' => implode( ',',  gamipress_get_rank_types_slugs() ),
                    'data-placeholder' => __( 'Select a rank', 'gamipress' ),
                ),
				'default'           => '',
				'options_cb'        => 'gamipress_options_cb_posts'
			),
			'link' => array(
				'name'        => __( 'Show Link', 'gamipress' ),
				'description' => __( 'Add a link on rank title to the rank page.', 'gamipress' ),
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
            'thumbnail_size' => array(
                'name'        => __( 'Thumbnail Size (in pixels)', 'gamipress' ),
                'description' => __( 'The rank featured image size in pixels. Leave empty to use the image size from settings.', 'gamipress' ),
                'type' 	=> 'text',
                'attributes' => array(
                    'type' => 'number',
                )
            ),
		),
	) );

}
add_action( 'init', 'gamipress_register_inline_rank_shortcode' );

/**
 * Single Rank Shortcode
 *
 * @since  2.3.1
 *
 * @param  array    $atts       Shortcode attributes
 * @param  string   $content    Shortcode content
 *
 * @return string 	   HTML markup
 */
function gamipress_inline_rank_shortcode( $atts = array(), $content = '' ) {

    global $post, $gamipress_template_args;

    $shortcode = 'gamipress_inline_rank';

    $original_atts = $atts;

	$atts = shortcode_atts( gamipress_inline_rank_shortcode_defaults(), $atts, $shortcode );

    // ---------------------------
    // Shortcode Errors
    // ---------------------------

    // Get the rank post
    $rank = gamipress_get_post( $atts['id'] );
    $is_rank = gamipress_is_rank( $rank );

    // Return if rank id not specified
    if ( empty( $original_atts['id'] ) && ! $is_rank )
        return gamipress_shortcode_error( __( 'Please, provide the rank ID.', 'gamipress' ), $shortcode );

    // Check if we're dealing with a rank post
    if ( ! $is_rank )
        return gamipress_shortcode_error( __( 'The id provided doesn\'t belong to a valid rank.', 'gamipress' ), $shortcode );

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
    $post = $rank;
    setup_postdata( $post );

    // Template rendering
    ob_start();
    gamipress_get_template_part( 'inline-rank', $rank->post_type );
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
    return apply_filters( 'gamipress_inline_rank_shortcode_output', $output, $atts, $content );
}

/**
 * Single rank shortcode defaults attributes values
 *
 * @since 2.3.1
 *
 * @return array
 */
function gamipress_inline_rank_shortcode_defaults() {

	return apply_filters( 'gamipress_inline_rank_shortcode_defaults', array(
		'id' 			        => get_the_ID(),
		'link' 			        => 'yes',
		'thumbnail' 	        => 'yes',
		'thumbnail_size' 	    => '',
	) );

}
