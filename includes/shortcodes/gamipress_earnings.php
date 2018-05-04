<?php
/**
 * GamiPress Earnings Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Earnings
 * @since       1.3.9
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register [gamipress_earnings] shortcode
 *
 * @since 1.3.9
 */
function gamipress_register_earnings_shortcode() {

    gamipress_register_shortcode( 'gamipress_earnings', array(
        'name'            => __( 'User Earnings', 'gamipress' ),
        'description'     => __( 'Output a list of user earnings.', 'gamipress' ),
        'output_callback' => 'gamipress_earnings_shortcode',
        'tabs' => array(
            'general' => array(
                'icon' => 'dashicons-admin-generic',
                'title' => __( 'General', 'gamipress' ),
                'fields' => array(
                    'current_user',
                    'user_id',
                    'limit',
                    'pagination',
                    'order',
                ),
            ),
            'points' => array(
                'icon' => 'dashicons-star-filled',
                'title' => __( 'Points', 'gamipress' ),
                'fields' => array(
                    'points',
                    'points_types',
                    'awards',
                    'deducts'
                ),
            ),
            'achievements' => array(
                'icon' => 'dashicons-awards',
                'title' => __( 'Achievements', 'gamipress' ),
                'fields' => array(
                    'achievements',
                    'achievement_types',
                    'steps',
                ),
            ),
            'ranks' => array(
                'icon' => 'dashicons-rank',
                'title' => __( 'Ranks', 'gamipress' ),
                'fields' => array(
                    'ranks',
                    'rank_types',
                    'rank_requirements',
                ),
            ),
        ),
        'fields'      => array(
            'current_user' => array(
                'name'        => __( 'Current User', 'gamipress' ),
                'description' => __( 'Show only earned items of the current logged in user.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' 	  => 'yes',
            ),
            'user_id' => array(
                'name'        => __( 'User', 'gamipress' ),
                'description' => __( 'Show only earned items by a specific user.', 'gamipress' ),
                'type'        => 'select',
                'default'     => '',
                'options_cb'  => 'gamipress_options_cb_users'
            ),
            'limit' => array(
                'name'        => __( 'Limit', 'gamipress' ),
                'description' => __( 'Number of items to display.', 'gamipress' ),
                'type'        => 'text',
                'default'     => 10,
            ),
            'pagination' => array(
                'name'        => __( 'Enable Pagination', 'gamipress' ),
                'description' => __( 'Show pagination links to navigate through all earned items.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' 	  => 'yes',
            ),
            'order' => array(
                'name'        => __( 'Order', 'gamipress' ),
                'description' => __( 'Sort order.', 'gamipress' ),
                'type'        => 'select',
                'options'      => array( 'DESC' => __( 'Newest', 'gamipress' ), 'ASC' => __( 'Older', 'gamipress' ) ),
                'default'     => 'DESC',
            ),

            // Points types

            'points' => array(
                'name'        => __( 'Show Points', 'gamipress' ),
                'description' => __( 'Show points earned and deducted.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' 	  => 'yes',
            ),
            'points_types' => array(
                'name'        => __( 'Points Type(s)', 'gamipress' ),
                'description' => __( 'Single or comma-separated list of points type(s) to display.', 'gamipress' ),
                'type'        => 'advanced_select',
                'multiple'    => true,
                'options_cb'  => 'gamipress_options_cb_points_types',
                'default' 	  => 'all',
            ),
            'awards' => array(
                'name'        => __( 'Show Points Awards', 'gamipress' ),
                'description' => __( 'Show points awarded.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' 	  => 'yes',
            ),
            'deducts' => array(
                'name'        => __( 'Show Points Deductions', 'gamipress' ),
                'description' => __( 'Show points deducted.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' 	  => 'yes',
            ),

            // Achievement types

            'achievements' => array(
                'name'        => __( 'Show Achievements', 'gamipress' ),
                'description' => __( 'Show achievements earned.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' 	  => 'yes',
            ),
            'achievement_types' => array(
                'name'        => __( 'Achievement Type(s)', 'gamipress' ),
                'description' => __( 'Single or comma-separated list of achievements type(s) to display.', 'gamipress' ),
                'type'        => 'advanced_select',
                'multiple'    => true,
                'options_cb'  => 'gamipress_options_cb_achievement_types',
                'default' 	  => 'all',
            ),
            'steps' => array(
                'name'        => __( 'Show Steps', 'gamipress' ),
                'description' => __( 'Show steps completed.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' 	  => 'yes',
            ),

            // Rank types

            'ranks' => array(
                'name'        => __( 'Show Ranks', 'gamipress' ),
                'description' => __( 'Show ranks reached.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' 	  => 'yes',
            ),
            'rank_types' => array(
                'name'        => __( 'Rank Type(s)', 'gamipress' ),
                'description' => __( 'Single or comma-separated list of ranks type(s) to display.', 'gamipress' ),
                'type'        => 'advanced_select',
                'multiple'    => true,
                'options_cb'  => 'gamipress_options_cb_rank_types',
                'default' 	  => 'all',
            ),
            'rank_requirements' => array(
                'name'        => __( 'Show Rank Requirements', 'gamipress' ),
                'description' => __( 'Show rank requirements completed.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' 	  => 'yes',
            ),
        ),
    ) );

}
add_action( 'init', 'gamipress_register_earnings_shortcode' );

/**
 * User Earnings Shortcode
 *
 * @since  1.3.9
 *
 * @param  array $atts Shortcode attributes
 *
 * @return string 	   HTML markup
 */
function gamipress_earnings_shortcode( $atts = array () ) {

    global $gamipress_template_args;

    $gamipress_template_args = array();

    $atts = shortcode_atts( array(
        'current_user'      => 'yes',
        'user_id'           => '0',
        'limit'             => '10',
        'pagination'        => 'yes',
        'order'             => 'DESC',

        'points'            => 'yes',
        'points_types'      => 'all',
        'awards'            => 'yes',
        'deducts'           => 'yes',

        'achievements'      => 'yes',
        'achievement_types' => 'all',
        'steps'             => 'yes',

        'ranks'             => 'yes',
        'rank_types'        => 'all',
        'rank_requirements' => 'yes',
    ), $atts, 'gamipress_earnings' );

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

    // Return if not user ID provided or current user is a guest
    if( absint( $atts['user_id'] ) === 0 ) {
        return '';
    }

    // GamiPress template args global
    $gamipress_template_args = $atts;

    $gamipress_template_args['query'] = gamipress_earnings_shortcode_query( $atts );

    if( ! $gamipress_template_args['query'] ) {
        return '';
    }

    ob_start();
    gamipress_get_template_part( 'earnings' );
    $output = ob_get_clean();

    // If switched to blog, return back to que current blog
    if( isset( $blog_id ) ) {
        switch_to_blog( $blog_id );
    }

    return $output;

}

/**
 * User Earnings Shortcode Query
 *
 * @since  1.4.9
 *
 * @param  array $args Query arguments
 *
 * @return CT_Query
 */
function gamipress_earnings_shortcode_query( $args = array () ) {

    // Query args
    $query_args = array(
        'user_id'           => $args['user_id'],
        'orderby'           => 'date',
        'order'             => $args['order'],
        'items_per_page'    => $args['limit'],
        'paged'             => max( 1, get_query_var( 'paged' ) )
    );

    $types = array();
    $points_types = array();

    // Points types
    if( $args['points'] === 'yes' ) {

        if( $args['points_types'] === 'all') {
            $points_types = gamipress_get_points_types_slugs();
        } else {
            $points_types = explode( ',', $args['points_types'] );
        }

        // Points awards
        if( $args['awards'] === 'yes' ) {
            $types[] = 'points-award';
        }

        // Points deducts
        if( $args['deducts'] === 'yes' ) {
            $types[] = 'points-deduct';
        }
    }

    // Achievement types
    if( $args['achievements'] === 'yes' ) {

        if( $args['achievement_types'] === 'all') {
            $achievement_types = gamipress_get_achievement_types_slugs();
        } else {
            $achievement_types = explode( ',', $args['achievement_types'] );
        }

        $types = array_merge( $types, $achievement_types );

        // Step
        if( $args['steps'] === 'yes' ) {
            $types[] = 'step';
        }
    }

    // Rank types
    if( $args['ranks'] === 'yes' ) {

        if( $args['rank_types'] === 'all') {
            $rank_types = gamipress_get_rank_types_slugs();
        } else {
            $rank_types = explode( ',', $args['rank_types'] );
        }

        $types = array_merge( $types, $rank_types );

        // Rank requirements
        if( $args['rank_requirements'] === 'yes' ) {
            $types[] = 'rank-requirement';
        }
    }

    // Remove types that has 'all' value
    foreach( $types as $index => $type ) {
        if( $type === 'all' ) {
            unset( $types[$index] );
        }
    }

    // Return if not types selected
    if( empty( $types ) ) {
        return false;
    }

    $query_args['post_type'] = $types;

    if( ! empty( $points_types ) ) {
        $query_args['points_type'] = $points_types;

        // If looking to show achievements or ranks, some of them do not award any points so wee need to add the empty points type value
        if( $args['achievements'] === 'yes' || $args['ranks'] === 'yes' ) {
            $points_types[] = '';
        }
    }

    // Setup table
    ct_setup_table( 'gamipress_user_earnings' );

    return new CT_Query( $query_args );

}
