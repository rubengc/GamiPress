<?php
/**
 * GamiPress Rank Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Rank
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
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
		'name'              => __( 'Single Rank', 'gamipress' ),
		'description'       => __( 'Display a single rank.', 'gamipress' ),
        'icon' 	            => 'rank',
        'group' 	        => 'gamipress',
		'output_callback'   => 'gamipress_rank_shortcode',
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
			'title' => array(
				'name'        => __( 'Show Title', 'gamipress' ),
				'description' => __( 'Display the rank title.', 'gamipress' ),
				'type' 	=> 'checkbox',
				'classes' => 'gamipress-switch',
				'default' => 'yes'
			),
            'title_size' => array(
                'name'              => __( 'Title Size', 'gamipress' ),
                'description'       => __( 'The rank title size.', 'gamipress' ),
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
            'heading' => array(
                'name'        => __( 'Show Requirements Heading', 'gamipress' ),
                'description' => __( 'Display the rank requirements heading text.', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),
            'heading_size' => array(
                'name'              => __( 'Requirements Heading Size', 'gamipress' ),
                'description'       => __( 'The rank requirements heading text size.', 'gamipress' ),
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
				'description' => __( 'Display the "Unlock using points" (on ranks where unlock with points is allowed).', 'gamipress' ),
				'type' 	=> 'checkbox',
				'classes' => 'gamipress-switch'
			),
			'earners' => array(
				'name'        => __( 'Show Earners', 'gamipress' ),
				'description' => __( 'Display a list of users that actually are in this rank.', 'gamipress' ),
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
				'description' => __( 'Layout to show the rank.', 'gamipress' ),
				'type' 		  => 'radio',
				'options' => gamipress_get_layout_options(),
				'default' 	  => 'left',
				'inline' 	  => true,
				'classes' 	  => 'gamipress-image-options'
			),
            'align' => array(
                'name'        => __( 'Alignment', 'gamipress' ),
                'description' => __( 'Alignment to show the rank.', 'gamipress' ),
                'type' 		  => 'radio',
                'options' 	  => gamipress_get_alignment_options(),
                'default' 	  => 'none',
                'inline' 	  => true,
                'classes' 	  => 'gamipress-image-options'
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
 * @param  array    $atts       Shortcode attributes
 * @param  string   $content    Shortcode content
 *
 * @return string 	   HTML markup
 */
function gamipress_rank_shortcode( $atts = array(), $content = '' ) {

    $shortcode = 'gamipress_rank';

    $original_atts = $atts;

	$atts = shortcode_atts( gamipress_rank_shortcode_defaults(), $atts, $shortcode );

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

	// If we're dealing with an rank post
    $output = gamipress_render_rank( $rank, $atts );

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
    return apply_filters( 'gamipress_rank_shortcode_output', $output, $atts, $content );
}

/**
 * Single rank shortcode defaults attributes values
 *
 * @since 1.3.9.4
 *
 * @return array
 */
function gamipress_rank_shortcode_defaults() {

	return apply_filters( 'gamipress_rank_shortcode_defaults', array(
		'id' 			        => get_the_ID(),
		'title' 		        => 'yes',
		'title_size' 		    => 'h2',
		'link' 			        => 'yes',
		'thumbnail' 	        => 'yes',
		'thumbnail_size' 	    => '',
		'excerpt'	  	        => 'yes',
		'requirements'	        => 'yes',
		'toggle' 		        => 'yes',
		'heading' 	            => 'yes',
		'heading_size' 	        => 'h4',
		'unlock_button'         => 'yes',
		'earners'	  	        => 'no',
		'earners_limit'	        => '0',
		'layout'	  	        => 'left',
		'align'	  	            => 'none',
	) );

}
