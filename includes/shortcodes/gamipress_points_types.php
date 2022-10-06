<?php
/**
 * GamiPress Points Types Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Points_Types
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register [gamipress_points_types] shortcode
 *
 * @since 1.0.0
 */
function gamipress_register_points_types_shortcode() {

    gamipress_register_shortcode( 'gamipress_points_types', array(
        'name'              => __( 'Points Types', 'gamipress' ),
        'description'       => __( 'Display a list of points types with their points awards.', 'gamipress' ),
        'icon' 	            => 'star-filled',
        'group' 	        => 'gamipress',
        'output_callback'   => 'gamipress_points_types_shortcode',
        'fields'      => array(
            'type' => array(
                'name'              => __( 'Points Type(s)', 'gamipress' ),
                'description'       => __( 'Points type(s) to display.', 'gamipress' ),
                'shortcode_desc'    => __( 'Single or comma-separated list of points type(s) to display.', 'gamipress' ),
                'type'              => 'advanced_select',
                'multiple'          => true,
                'classes' 	        => 'gamipress-selector',
                'attributes' 	    => array(
                    'data-placeholder' => __( 'Default: All', 'gamipress' ),
                ),
                'options_cb'        => 'gamipress_options_cb_points_types',
                'default'           => 'all',
            ),
            'columns' => array(
                'name'        => __( 'Columns', 'gamipress' ),
                'description' => __( 'Columns to divide each points type.', 'gamipress' ),
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
            'title_size' => array(
                'name'              => __( 'Title Size', 'gamipress' ),
                'description'       => __( 'The points type title size.', 'gamipress' ),
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
            'thumbnail' => array(
                'name'        => __( 'Show Thumbnail', 'gamipress' ),
                'description' => __( 'Display the points type featured image.', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),
            'thumbnail_size' => array(
                'name'        => __( 'Thumbnail Size (in pixels)', 'gamipress' ),
                'description' => __( 'The points type featured image size in pixels. Leave empty to use the image size from settings.', 'gamipress' ),
                'type' 	=> 'text',
                'attributes' => array(
                    'type' => 'number',
                )
            ),
            'awards' => array(
                'name'        => __( 'Show Points Awards', 'gamipress' ),
                'description' => __( 'Display the points type awards.', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes',
            ),
            'deducts' => array(
                'name'        => __( 'Show Points Deductions', 'gamipress' ),
                'description' => __( 'Display the points type deducts.', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes',
            ),
            'toggle' => array(
                'name'        => __( 'Show Points Awards/Deducts Toggle', 'gamipress' ),
                'description' => __( 'Display the points type awards and deducts toggle.', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),
            'heading' => array(
                'name'        => __( 'Show Points Awards/Deducts Heading', 'gamipress' ),
                'description' => __( 'Display the points type awards and deducts heading text.', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),
            'heading_size' => array(
                'name'              => __( 'Points Awards/Deducts Heading Size', 'gamipress' ),
                'description'       => __( 'The the points type awards and deducts heading text size.', 'gamipress' ),
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
            'layout' => array(
                'name'        => __( 'Layout', 'gamipress' ),
                'description' => __( 'Layout to show the points type.', 'gamipress' ),
                'type' 		  => 'radio',
                'options' 	  => gamipress_get_layout_options(),
                'default' 	  => 'left',
                'inline' 	  => true,
                'classes' 	  => 'gamipress-image-options'
            ),
            'align' => array(
                'name'        => __( 'Alignment', 'gamipress' ),
                'description' => __( 'Alignment to show the points.', 'gamipress' ),
                'type' 		  => 'radio',
                'options' 	  => gamipress_get_alignment_options(),
                'default' 	  => 'none',
                'inline' 	  => true,
                'classes' 	  => 'gamipress-image-options'
            ),
            'current_user' => array(
                'name'          => __( 'Current User', 'gamipress' ),
                'description'   => __( 'Show points awards and deducts earned by the current logged in user.', 'gamipress' ),
                'type' 		    => 'checkbox',
                'classes' 	    => 'gamipress-switch',
                'default' 	  => 'yes',
            ),
            'user_id' => array(
                'name'          => __( 'User', 'gamipress' ),
                'description'   => __( 'Show points awards and deducts earned by a specific user.', 'gamipress' ),
                'type'          => 'select',
                'default'       => '',
                'options_cb'    => 'gamipress_options_cb_users'
            ),
            'wpms' => array(
                'name'        => __( 'Include Multisite Points Types', 'gamipress' ),
                'description' => __( 'Show points types from all network sites.', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
            ),
        ),
    ) );
}
add_action( 'init', 'gamipress_register_points_types_shortcode' );

/**
 * Points Types Shortcode
 *
 * @since  1.0.0
 *
 * @param  array    $atts       Shortcode attributes
 * @param  string   $content    Shortcode content
 *
 * @return string 	   HTML markup
 */
function gamipress_points_types_shortcode( $atts = array(), $content = '' ) {

    global $gamipress_template_args, $blog_id;

    // Initialize GamiPress template args global
    $gamipress_template_args = array();

    $shortcode = 'gamipress_points_types';

    $atts = shortcode_atts( gamipress_points_types_shortcode_defaults(), $atts, $shortcode );

    // Single type check to use dynamic template
    $is_single_type = false;
    $types = explode( ',', $atts['type'] );

    if( $atts['type'] === 'all') {
        $types = gamipress_get_points_types_slugs();
    } else if ( count( $types ) === 1 ) {
        $is_single_type = true;
    }

    // Force to set current user as user ID
    if( $atts['current_user'] === 'yes' ) {
        $atts['user_id'] = get_current_user_id();
    } else if( absint( $atts['user_id'] ) === 0 ) {
        $atts['user_id'] = get_current_user_id();
    }

    // ---------------------------
    // Shortcode Errors
    // ---------------------------

    if( $is_single_type ) {

        // Check if points type is valid
        if ( ! in_array( $atts['type'], gamipress_get_points_types_slugs() ) ) {
            return gamipress_shortcode_error( __( 'The type provided isn\'t a valid registered points type.', 'gamipress' ), $shortcode );
        }

    } else if( $atts['type'] !== 'all' ) {

        // Let's check if all types provided are wrong
        $all_types_wrong = true;

        foreach( $types as $type ) {
            if ( in_array( $type, gamipress_get_points_types_slugs() ) ) {
                $all_types_wrong = false;
            }
        }

        // just notify error if all types are wrong
        if( $all_types_wrong ) {
            return gamipress_shortcode_error( __( 'All types provided aren\'t valid registered points types.', 'gamipress' ), $shortcode );
        }

    }

    // ---------------------------
    // Shortcode Processing
    // ---------------------------

    // Enqueue assets
    gamipress_enqueue_scripts();

    // If we're polling all sites, grab an array of site IDs
    if( $atts['wpms'] === 'yes' && ! gamipress_is_network_wide_active() )
        $sites = gamipress_get_network_site_ids();
    // Otherwise, use only the current site
    else
        $sites = array( $blog_id );

    // GamiPress template args global
    $gamipress_template_args = $atts;

    // Get the points count of all registered network sites
    $gamipress_template_args['points-types'] = array();

    // Loop through each site (default is current site only)
    foreach( $sites as $site_blog_id ) {

        // If we're not polling the current site, switch to the site we're polling
        if ( $blog_id != $site_blog_id ) {
            switch_to_blog( $site_blog_id );
        }

        foreach( $types as $points_type ) {
            // Initialize points type
            if( ! isset( $gamipress_template_args['points-types'][$points_type] ) ) {
                $gamipress_template_args['points-types'][$points_type] = array(
                    'awards' => array(),
                    'deducts' => array(),
                );
            }

            // Setup points awards
            if( $atts['awards'] === 'yes' ) {

                $points_awards = gamipress_get_points_type_points_awards( $points_type );

                if( $points_awards ) {
                    $gamipress_template_args['points-types'][$points_type]['awards'] += $points_awards;
                }
            }

            // Setup points deducts
            if( $atts['deducts'] === 'yes' ) {

                $points_deducts = gamipress_get_points_type_points_deducts( $points_type );

                if( $points_deducts ) {
                    $gamipress_template_args['points-types'][$points_type]['deducts'] += $points_deducts;
                }
            }
        }



        if ( $blog_id != $site_blog_id && is_multisite() ) {
            // Come back to current blog
            restore_current_blog();
        }

    }

    ob_start();
    gamipress_get_template_part( 'points-types', ( $is_single_type ? $atts['type'] : null ) );
    $output = ob_get_clean();

    /**
     * Filter to override shortcode output
     *
     * @since 1.6.5
     *
     * @param string    $output     Final output
     * @param array     $atts       Shortcode attributes
     * @param string    $content    Shortcode content
     */
    return apply_filters( 'gamipress_points_types_shortcode_output', $output, $atts, $content );

}

/**
 * Points types shortcode defaults attributes values
 *
 * @since 2.3.1
 *
 * @return array
 */
function gamipress_points_types_shortcode_defaults() {

    return apply_filters( 'gamipress_points_types_shortcode_defaults', array(
        'type'              => 'all',
        'columns'           => '1',
        'title_size'        => 'h2',
        'thumbnail'         => 'yes',
        'thumbnail_size'    => '',
        'awards'            => 'yes',
        'deducts'           => 'yes',
        'toggle'            => 'yes',
        'heading'           => 'yes',
        'heading_size'      => 'h4',
        'layout'            => 'left',
        'align'	  		    => 'none',
        'current_user'      => 'yes',
        'user_id'           => '0',
        'wpms'              => 'no',
    ) );

}