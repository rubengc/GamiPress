<?php
/**
 * GamiPress Points Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Points
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register [gamipress_points] shortcode
 *
 * @since 1.0.0
 */
function gamipress_register_points_shortcode() {

    gamipress_register_shortcode( 'gamipress_points', array(
        'name'              => __( 'User Points Balance', 'gamipress' ),
        'description'       => __( 'Output an user points balance.', 'gamipress' ),
        'icon' 	            => 'star-filled',
        'output_callback'   => 'gamipress_points_shortcode',
        'fields'      => array(
            'type' => array(
                'name'        => __( 'Points Type(s)', 'gamipress' ),
                'description' => __( 'Single or comma-separated list of points type(s) to display.', 'gamipress' ),
                'type'        => 'advanced_select',
                'multiple'    => true,
                'options_cb'  => 'gamipress_options_cb_points_types',
                'default'     => 'all',
            ),
            'thumbnail' => array(
                'name'        => __( 'Show Thumbnail', 'gamipress' ),
                'description' => __( 'Display the points type featured image.', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),
            'label' => array(
                'name'        => __( 'Show Points Type Label', 'gamipress' ),
                'description' => __( 'Display the points type label (singular or plural name, based on the amount of points).', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
                'default' => 'yes'
            ),
            'current_user' => array(
                'name'        => __( 'Current User', 'gamipress' ),
                'description' => __( 'Show only points earned by the current logged in user.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
            ),
            'user_id' => array(
                'name'        => __( 'User', 'gamipress' ),
                'description' => __( 'Show only points earned by a specific user. Leave blank to show the site points (points sum of all users).', 'gamipress' ),
                'type'        => 'select',
                'default'     => '',
                'options_cb'  => 'gamipress_options_cb_users'
            ),
            'inline' => array(
                'name'        => __( 'Inline', 'gamipress' ),
                'description' => __( 'Show points balance inline (as text).', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            'columns' => array(
                'name'        => __( 'Columns', 'gamipress' ),
                'description' => __( 'Columns to divide each points balance.', 'gamipress' ),
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
            'layout' => array(
                'name'        => __( 'Layout', 'gamipress' ),
                'description' => __( 'Layout to show the points.', 'gamipress' ),
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
                'name'        => __( 'Include Multisite Points', 'gamipress' ),
                'description' => __( 'Show points from all network sites.', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
            ),
        ),
    ) );

}
add_action( 'init', 'gamipress_register_points_shortcode' );

/**
 * Points Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes
 *
 * @return string 	   HTML markup
 */
function gamipress_points_shortcode( $atts = array () ) {

    global $gamipress_template_args;

    // Initialize GamiPress template args global
    $gamipress_template_args = array();

    $atts = shortcode_atts( array(
        // Points atts
        'type'          => 'all',
        'thumbnail'     => 'yes',
        'label'         => 'yes',
        'current_user'  => 'no',
        'user_id'       => '0',
        'inline'        => 'no',
        'columns'       => '1',
        'layout'        => 'left',
        'wpms'          => 'no',
    ), $atts, 'gamipress_points' );

    // Single type check to use dynamic template
    $is_single_type = false;
    $types = explode( ',', $atts['type'] );

    if( empty( $atts['type'] ) || $atts['type'] === 'all' || in_array( 'all', $types ) ) {
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
            return gamipress_shortcode_error( __( 'The type provided isn\'t a valid registered points type.', 'gamipress' ), 'gamipress_points' );

    } else if( $atts['type'] !== 'all' ) {

        // Let's check if all types provided are wrong
        $all_types_wrong = true;

        foreach( $types as $type ) {
            if ( in_array( $type, gamipress_get_points_types_slugs() ) )
                $all_types_wrong = false;
        }

        // just notify error if all types are wrong
        if( $all_types_wrong )
            return gamipress_shortcode_error( __( 'All types provided aren\'t valid registered points types.', 'gamipress' ), 'gamipress_points' );

    }

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

    // Force to set current user as user ID
    if( $atts['current_user'] === 'yes' ) {
        $atts['user_id'] = get_current_user_id();
    }

    // Get the current user if one wasn't specified
    //if( absint( $atts['user_id'] ) === 0 )
        //$atts['user_id'] = get_current_user_id();

    // If we're polling all sites, grab an array of site IDs
    if( $atts['wpms'] === 'yes' && ! gamipress_is_network_wide_active() ) {
        $sites = gamipress_get_network_site_ids();
    // Otherwise, use only the current site
    } else {
        $sites = array( get_current_blog_id() );
    }

    // GamiPress template args global
    $gamipress_template_args = $atts;

    // Get the points count of all registered network sites
    $gamipress_template_args['points'] = array();

    // Loop through each site (default is current site only)
    foreach( $sites as $site_blog_id ) {

        // If we're not polling the current site, switch to the site we're polling
        if ( get_current_blog_id() != $site_blog_id ) {
            switch_to_blog( $site_blog_id );
        }

        foreach( $types as $points_type ) {
            if( ! isset( $gamipress_template_args['points'][$points_type] ) ) {
                $gamipress_template_args['points'][$points_type] = 0;
            }

            if( $atts['current_user'] === 'no' && absint( $atts['user_id'] ) === 0 ) {
                // Site points
                $gamipress_template_args['points'][$points_type] += gamipress_get_site_points( $points_type );
            } else {
                // User points
                $gamipress_template_args['points'][$points_type] += gamipress_get_user_points( $atts['user_id'], $points_type );
            }


        }

        if ( get_current_blog_id() != $site_blog_id ) {
            // Come back to current blog
            restore_current_blog();
        }

    }

    if( $atts['inline'] === 'yes' ) {

        $output = '';

        // Get the last points type to show to meet if should append the separator or not
        $last_points_type = key( array_slice( $gamipress_template_args['points'], -1, 1, true ) );

        // Inline rendering
        foreach( $gamipress_template_args['points'] as $points_type => $amount ) {

            $label_position = gamipress_get_points_type_label_position( $points_type );

            $output .=
                // Thumbnail
                ( $gamipress_template_args['thumbnail'] === 'yes' ? gamipress_get_points_type_thumbnail( $points_type ) . ' ' : '' )
                // Points label (before)
                . ( $gamipress_template_args['label'] === 'yes' && $label_position === 'before' ? gamipress_get_points_amount_label( $amount, $points_type ) . ' ' : '' )
                // Points amount
                . gamipress_format_amount( $amount, $points_type )
                // Points label (after)
                . ( $gamipress_template_args['label'] === 'yes' && $label_position !== 'before' ? ' ' . gamipress_get_points_amount_label( $amount, $points_type ) : '' )
                // Points separator
                . ( $points_type !== $last_points_type ? ', ' : '' );
        }

    } else {

        // Template rendering
        ob_start();
        if( $is_single_type ) {
            gamipress_get_template_part( 'points', $atts['type'] );
        } else {
            gamipress_get_template_part( 'points' );
        }
        $output = ob_get_clean();

    }

    // If switched to blog, return back to que current blog
    if( isset( $blog_id ) ) {
        switch_to_blog( $blog_id );
    }

    return $output;

}
