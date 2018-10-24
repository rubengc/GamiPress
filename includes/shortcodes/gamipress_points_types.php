<?php
/**
 * GamiPress Points Types Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Points_Types
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
        'name'            => __( 'Points Types', 'gamipress' ),
        'description'     => __( 'Output a list of points types with their points awards.', 'gamipress' ),
        'output_callback' => 'gamipress_points_types_shortcode',
        'fields'      => array(
            'type' => array(
                'name'        => __( 'Points Type(s)', 'gamipress' ),
                'description' => __( 'Single or comma-separated list of points type(s) to display.', 'gamipress' ),
                'type'        => 'advanced_select',
                'multiple'    => true,
                'options_cb'  => 'gamipress_options_cb_points_types',
                'default'     => 'all',
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
            'thumbnail' => array(
                'name'        => __( 'Show Thumbnail', 'gamipress' ),
                'description' => __( 'Display the points type featured image.', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),
            'awards' => array(
                'name'        => __( 'Show Points Awards', 'gamipress' ),
                'description' => __( 'Display the points type points awards.', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes',
            ),
            'deducts' => array(
                'name'        => __( 'Show Points Deducts', 'gamipress' ),
                'description' => __( 'Display the points type points deducts.', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes',
            ),
            'toggle' => array(
                'name'        => __( 'Show Points Awards/Deducts Toggle', 'gamipress' ),
                'description' => __( 'Display the points type points awards and deducts toggle.', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),
            'layout' => array(
                'name'        => __( 'Layout', 'gamipress' ),
                'description' => __( 'Layout to show the points type.', 'gamipress' ),
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
 * @param  array $atts Shortcode attributes
 *
 * @return string 	   HTML markup
 */
function gamipress_points_types_shortcode( $atts = array () ) {

    global $gamipress_template_args, $blog_id;

    // Initialize GamiPress template args global
    $gamipress_template_args = array();

    $atts = shortcode_atts( array(
        // Points atts
        'type'      => 'all',
        'columns'   => '1',
        'thumbnail' => 'yes',
        'awards'    => 'yes',
        'deducts'   => 'yes',
        'toggle'    => 'yes',
        'layout'    => 'left',
        'wpms'      => 'no',
    ), $atts, 'gamipress_points' );

    // Single type check to use dynamic template
    $is_single_type = false;
    $types = explode( ',', $atts['type'] );

    if( $atts['type'] === 'all') {
        $types = gamipress_get_points_types_slugs();
    } else if ( count( $types ) === 1 ) {
        $is_single_type = true;
    }

    // ---------------------------
    // Shortcode Errors
    // ---------------------------

    if( $is_single_type ) {

        // Check if points type is valid
        if ( ! in_array( $atts['type'], gamipress_get_points_types_slugs() ) )
            return gamipress_shortcode_error( __( 'The type provided isn\'t a valid registered points type.', 'gamipress' ), 'gamipress_points_types' );

    } else if( $atts['type'] !== 'all' ) {

        // let's check if all types provided are wrong
        $all_types_wrong = true;

        foreach( $types as $type ) {
            if ( ! in_array( $type, gamipress_get_points_types_slugs() ) )
                $all_types_wrong = true;
        }

        // just notify error if all types are wrong
        if( $all_types_wrong )
            return gamipress_shortcode_error( __( 'All types provided aren\'t valid registered points types.', 'gamipress' ), 'gamipress_points_types' );

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



        if ( $blog_id != $site_blog_id ) {
            // Come back to current blog
            restore_current_blog();
        }

    }

    ob_start();
    if( $is_single_type ) {
        gamipress_get_template_part( 'points-types', $atts['type'] );
    } else {
        gamipress_get_template_part( 'points-types' );
    }
    $output = ob_get_clean();

    return $output;

}