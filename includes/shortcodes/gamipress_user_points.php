<?php
/**
 * GamiPress User Points Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_User_Points
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register [gamipress_user_points] shortcode
 *
 * @since 1.0.0
 */
function gamipress_register_user_points_shortcode() {

    gamipress_register_shortcode( 'gamipress_user_points', array(
        'name'              => __( 'User Points', 'gamipress' ),
        'description'       => __( 'Display a user points balance.', 'gamipress' ),
        'icon' 	            => 'star-filled',
        'group' 	        => 'gamipress',
        'output_callback'   => 'gamipress_user_points_shortcode',
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
            'thumbnail' => array(
                'name'          => __( 'Show Thumbnail', 'gamipress' ),
                'description'   => __( 'Display the points type featured image.', 'gamipress' ),
                'type' 	        => 'checkbox',
                'classes'       => 'gamipress-switch',
                'default'       => 'yes'
            ),
            'thumbnail_size' => array(
                'name'        => __( 'Thumbnail Size (in pixels)', 'gamipress' ),
                'description' => __( 'The points type featured image size in pixels. Leave empty to use the image size from settings.', 'gamipress' ),
                'type' 	=> 'text',
                'attributes' => array(
                    'type' => 'number',
                )
            ),
            'label' => array(
                'name'          => __( 'Show Points Type Label', 'gamipress' ),
                'description'   => __( 'Display the points type label (singular or plural name, based on the amount of points).', 'gamipress' ),
                'type' 	        => 'checkbox',
                'classes'       => 'gamipress-switch',
                'default'       => 'yes'
            ),
            'current_user' => array(
                'name'          => __( 'Current User', 'gamipress' ),
                'description'   => __( 'Show points earned by the current logged in user.', 'gamipress' ),
                'type' 		    => 'checkbox',
                'classes' 	    => 'gamipress-switch',
                'default' 	    => 'yes',
            ),
            'user_id' => array(
                'name'          => __( 'User', 'gamipress' ),
                'description'   => __( 'Show points earned by a specific user.', 'gamipress' ),
                'type'          => 'select',
                'default'       => '',
                'options_cb'    => 'gamipress_options_cb_users'
            ),
            'period' => array(
                'name'          => __( 'Period', 'gamipress' ),
                'desc' 	        => __( 'Filter points balance based on a specific period selected. By default "None" that will display user current points balance.', 'gamipress' ),
                'type' 	        => 'select',
                'options_cb' 	=> 'gamipress_get_time_periods',
            ),
            'period_start' => array(
                'name' 	        => __( 'Start Date', 'gamipress' ),
                'desc' 	        => __( 'Period start date. Leave blank to no filter by a start date (points balance will be filtered only to the end date).', 'gamipress' )
                                . '<br>' . __( 'Accepts any valid PHP date format.', 'gamipress' ) . ' (<a href="https://gamipress.com/docs/advanced/date-fields" target="_blank">' .  __( 'More information', 'gamipress' ) .  '</a>)',
                'type'          => 'text',
            ),
            'period_end' => array(
                'name' 	        => __( 'End Date', 'gamipress' ),
                'desc' 	        => __( 'Period end date. Leave blank to no filter by an end date (points balance will be filtered from the start date to today).', 'gamipress' )
                                . '<br>' . __( 'Accepts any valid PHP date format.', 'gamipress' ) . ' (<a href="https://gamipress.com/docs/advanced/date-fields" target="_blank">' .  __( 'More information', 'gamipress' ) .  '</a>)',
                'type'          => 'text',
            ),
            'inline' => array(
                'name'          => __( 'Inline', 'gamipress' ),
                'description'   => __( 'Show points balance inline (as text).', 'gamipress' ),
                'type' 	        => 'checkbox',
                'classes'       => 'gamipress-switch',
            ),
            'columns' => array(
                'name'          => __( 'Columns', 'gamipress' ),
                'description'   => __( 'Columns to divide each points balance.', 'gamipress' ),
                'type' 	        => 'select',
                'options'       => array(
                    '1' => __( '1 Column', 'gamipress' ),
                    '2' => __( '2 Columns', 'gamipress' ),
                    '3' => __( '3 Columns', 'gamipress' ),
                    '4' => __( '4 Columns', 'gamipress' ),
                    '5' => __( '5 Columns', 'gamipress' ),
                    '6' => __( '6 Columns', 'gamipress' ),
                ),
                'default'       => '1'
            ),
            'layout' => array(
                'name'          => __( 'Layout', 'gamipress' ),
                'description'   => __( 'Layout to show the points.', 'gamipress' ),
                'type' 		    => 'radio',
                'options' 	    => gamipress_get_layout_options(),
                'default' 	    => 'left',
                'inline' 	    => true,
                'classes' 	    => 'gamipress-image-options'
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
            'wpms' => array(
                'name'          => __( 'Include Multisite Points', 'gamipress' ),
                'description'   => __( 'Show user points from all network sites.', 'gamipress' ),
                'type' 	        => 'checkbox',
                'classes'       => 'gamipress-switch',
            ),
        ),
    ) );

}
add_action( 'init', 'gamipress_register_user_points_shortcode' );

/**
 * User Points Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array    $atts       Shortcode attributes
 * @param  string   $content    Shortcode content
 *
 * @return string 	   HTML markup
 */
function gamipress_user_points_shortcode( $atts = array(), $content = '' ) {

    global $gamipress_template_args;

    // Initialize GamiPress template args global
    $gamipress_template_args = array();

    $shortcode = 'gamipress_user_points';

    $atts = shortcode_atts( array(
        // Points atts
        'type'              => 'all',
        'thumbnail'         => 'yes',
        'thumbnail_size'    => '',
        'label'             => 'yes',
        'current_user'      => 'yes',
        'user_id'           => '0',
        'period'            => '',
        'period_start'      => '',
        'period_end'        => '',
        'inline'            => 'no',
        'columns'           => '1',
        'layout'            => 'left',
        'align'	  		    => 'none',
        'wpms'              => 'no',
    ), $atts, $shortcode );

    // Single type check to use dynamic template
    $is_single_type = false;
    $types = explode( ',', $atts['type'] );

    if ( empty( $atts['type'] ) || $atts['type'] === 'all' || in_array( 'all', $types ) ) {
        $types = gamipress_get_points_types_slugs();
    } else if ( count( $types ) === 1 ) {
        $is_single_type = true;
    }

    // Date range (based on period given)
    if( $atts['period'] === 'custom' ) {

        $date_range = array(
            'start' => gamipress_date( 'Y-m-d', $atts['period_start'] ),
            'end' => gamipress_date( 'Y-m-d', $atts['period_end'] ),
        );

    } else {
        $date_range = gamipress_get_period_range( $atts['period'] );
    }

    // ---------------------------
    // Shortcode Errors
    // ---------------------------

    if( $is_single_type ) {

        // Check if points type is valid
        if ( ! in_array( $atts['type'], gamipress_get_points_types_slugs() ) ) {
            return gamipress_shortcode_error( __( 'The type provided isn\'t a valid registered points type.', 'gamipress' ), $shortcode );
        }

    } else if ( $atts['type'] !== 'all' ) {

        // Let's check if all types provided are wrong
        $all_types_wrong = true;

        foreach ( $types as $type ) {
            if ( in_array( $type, gamipress_get_points_types_slugs() ) ) {
                $all_types_wrong = false;
            }
        }

        // just notify error if all types are wrong
        if ( $all_types_wrong ) {
            return gamipress_shortcode_error( __( 'All types provided aren\'t valid registered points types.', 'gamipress' ), $shortcode );
        }

    }

    // ---------------------------
    // Shortcode Processing
    // ---------------------------

    // Enqueue assets
    gamipress_enqueue_scripts();

    // On network wide active installs, we need to switch to main blog mostly for posts permalinks and thumbnails
    $blog_id = gamipress_switch_to_main_site_if_network_wide_active();

    // Force to set current user as user ID
    if( $atts['current_user'] === 'yes' ) {
        $atts['user_id'] = get_current_user_id();
    } else if( absint( $atts['user_id'] ) === 0 ) {
        $atts['user_id'] = get_current_user_id();
    }

    if( $atts['wpms'] === 'yes' && ! gamipress_is_network_wide_active() ) {
        // If we're polling all sites, grab an array of site IDs
        $sites = gamipress_get_network_site_ids();
    } else {
        // Otherwise, use only the current site
        $sites = array( $blog_id );
    }

    // GamiPress template args global
    $gamipress_template_args = $atts;

    // Get the points count of all registered network sites
    $gamipress_template_args['points'] = array();

    // Loop through each site (default is current site only)
    foreach( $sites as $site_blog_id ) {

        // If we're not polling the current site, switch to the site we're polling
        $current_site_blog_id = get_current_blog_id();

        if ( $current_site_blog_id != $site_blog_id ) {
            switch_to_blog( $site_blog_id );
        }

        foreach( $types as $points_type ) {

            // Initialize points type var
            if( ! isset( $gamipress_template_args['points'][$points_type] ) ) {
                $gamipress_template_args['points'][$points_type] = 0;
            }

            // Leave balance to 0 if no user provided
            if( $atts['user_id'] === 0 ) {
                continue;
            }

            // Setup extra arguments to pass to the get points functions
            $args = array(
                'date_query' => array(
                    'after' => $date_range['start'],    // After start date
                    'before' => $date_range['end'],     // Before end date
                )
            );

            // User points
            $points = gamipress_get_user_points( $atts['user_id'], $points_type, $args );

            $gamipress_template_args['points'][$points_type] += $points;

        }

        if ( $current_site_blog_id != $site_blog_id && is_multisite() ) {
            // Come back to current blog
            restore_current_blog();
        }

    }

    // Template rendering
    ob_start();
    gamipress_get_template_part( ( $atts['inline'] === 'yes' ? 'inline-points' : 'points' ), ( $is_single_type ? $atts['type'] : null ) );
    $output = ob_get_clean();

    if( $atts['inline'] === 'yes' ) {
        $output = gamipress_parse_inline_output( $output );
    }

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
    return apply_filters( 'gamipress_user_points_shortcode_output', $output, $atts, $content );

}
