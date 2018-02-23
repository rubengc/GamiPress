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

    // Setup a custom array of points types
    $points_types = array(
        'all' => __( 'All', 'gamipress' ) ,
        '' => __( 'Default points', 'gamipress' ) ,
    );

    foreach ( gamipress_get_points_types() as $slug => $data ) {
        $points_types[$slug] = $data['plural_name'];
    }

    gamipress_register_shortcode( 'gamipress_points', array(
        'name'            => __( 'User Points Balance', 'gamipress' ),
        'description'     => __( 'Output an user points balance.', 'gamipress' ),
        'output_callback' => 'gamipress_points_shortcode',
        'fields'      => array(
            'type' => array(
                'name'        => __( 'Points Type(s)', 'gamipress' ),
                'description' => __( 'Single or comma-separated list of points type(s) to display.', 'gamipress' ),
                'type'        => 'advanced_select',
                'multiple'    => true,
                'options'     => $points_types,
                'default'     => 'all',
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
                'description' => __( 'Show only points earned by a specific user. Leave blank to show points of logged in user.', 'gamipress' ),
                'type'        => 'select',
                'default'     => '',
                'options_cb'  => 'gamipress_options_cb_users'
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
        'columns'       => '1',
        'thumbnail'     => 'yes',
        'label'         => 'yes',
        'current_user'  => 'no',
        'user_id'       => '0',
        'layout'        => 'left',
        'wpms'          => 'no',
    ), $atts, 'gamipress_points' );

    gamipress_enqueue_scripts();

    // On network wide active installs, we need to switch to main blog mostly for posts permalinks and thumbnails
    if( gamipress_is_network_wide_active() && ! is_main_site() ) {
        $blog_id = get_current_blog_id();
        switch_to_blog( get_main_site_id() );
    }

    // Single type check to use dynamic template
    $is_single_type = false;
    $types = explode( ',', $atts['type'] );

    if( empty( $atts['type'] ) || $atts['type'] === 'all' || in_array( 'all', $types ) ) {
        $types = gamipress_get_points_types_slugs();
    } else if ( count( $types ) === 1 ) {
        $is_single_type = true;
    }

    // Force to set current user as user ID
    if( $atts['current_user'] === 'yes' ) {
        $atts['user_id'] = get_current_user_id();
    }

    // Get the current user if one wasn't specified
    if( absint( $atts['user_id'] ) === 0 )
        $atts['user_id'] = get_current_user_id();

    // If we're polling all sites, grab an array of site IDs
    if( $atts['wpms'] === 'yes' ) {
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

            $gamipress_template_args['points'][$points_type] += gamipress_get_user_points( $atts['user_id'], $points_type );
        }

        if ( get_current_blog_id() != $site_blog_id ) {
            // Come back to current blog
            restore_current_blog();
        }

    }

    ob_start();
    if( $is_single_type ) {
        gamipress_get_template_part( 'points', $atts['type'] );
    } else {
        gamipress_get_template_part( 'points' );
    }
    $output = ob_get_clean();

    // If switched to blog, return back to que current blog
    if( isset( $blog_id ) ) {
        switch_to_blog( $blog_id );
    }

    return $output;

}
