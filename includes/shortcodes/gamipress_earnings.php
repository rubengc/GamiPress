<?php
/**
 * GamiPress Earnings Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Earnings
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
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
        'description'     => __( 'Display a list of user earnings.', 'gamipress' ),
        'group' 	      => 'gamipress',
        'output_callback' => 'gamipress_earnings_shortcode',
        'tabs' => array(
            'general' => array(
                'icon' => 'dashicons-admin-generic',
                'title' => __( 'General', 'gamipress' ),
                'fields' => array(
                    'current_user',
                    'user_id',
                    'force_responsive',
                    'limit',
                    'pagination',
                    'order',
                    'include',
                    'exclude',
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
                    'achievements_without_points',
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
                'description' => __( 'Show only earned items by a specific user. Leave blank to show earned items of all users.', 'gamipress' ),
                'type'        => 'select',
                'default'     => '',
                'options_cb'  => 'gamipress_options_cb_users'
            ),
            'force_responsive' => array(
                'name' 	=> __( 'Force Responsive', 'gamipress' ),
                'desc' 	=> __( 'Force to display the earnings table with the responsive style even if is displayed in a big screen.', 'gamipress' ),
                'type' 	=> 'checkbox',
                'classes'   => 'gamipress-switch',
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
            'include' => array(
                'name'              => __( 'Include', 'gamipress' ),
                'description'       => __( 'Comma-separated list of specific earnings IDs to include.', 'gamipress' ),
                'type'              => 'text',
            ),
            'exclude' => array(
                'name'              => __( 'Exclude', 'gamipress' ),
                'description'       => __( 'Comma-separated list of specific earnings IDs to exclude.', 'gamipress' ),
                'type'              => 'text',
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
                'default' 	        => 'all',
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
                'name'              => __( 'Achievement Type(s)', 'gamipress' ),
                'description'       => __( 'Achievements type(s) to display.', 'gamipress' ),
                'shortcode_desc'    => __( 'Single or comma-separated list of achievements type(s) to display.', 'gamipress' ),
                'type'              => 'advanced_select',
                'multiple'          => true,
                'classes' 	        => 'gamipress-selector',
                'attributes' 	    => array(
                    'data-placeholder' => __( 'Default: All', 'gamipress' ),
                ),
                'options_cb'        => 'gamipress_options_cb_achievement_types',
                'default' 	        => 'all',
            ),
            'steps' => array(
                'name'        => __( 'Show Steps', 'gamipress' ),
                'description' => __( 'Show steps completed.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' 	  => 'yes',
            ),
            'achievements_without_points' => array(
                'name'        => __( 'Show Achievements Without Points', 'gamipress' ),
                'description' => __( 'Show achievements that do not award points.', 'gamipress' ),
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
                'name'              => __( 'Rank Type(s)', 'gamipress' ),
                'description'       => __( 'Ranks type(s) to display.', 'gamipress' ),
                'shortcode_desc'    => __( 'Single or comma-separated list of ranks type(s) to display.', 'gamipress' ),
                'type'              => 'advanced_select',
                'multiple'          => true,
                'classes' 	        => 'gamipress-selector',
                'attributes' 	    => array(
                    'data-placeholder' => __( 'Default: All', 'gamipress' ),
                ),
                'options_cb'        => 'gamipress_options_cb_rank_types',
                'default' 	        => 'all',
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
 * @param  array    $atts       Shortcode attributes
 * @param  string   $content    Shortcode content
 *
 * @return string 	   HTML markup
 */
function gamipress_earnings_shortcode( $atts = array(), $content = '' ) {

    global $gamipress_template_args;

    // Initialize GamiPress template args global
    $gamipress_template_args = array();

    $shortcode = 'gamipress_earnings';

    $shortcode_defaults = array(
        'current_user'                  => 'yes',
        'user_id'                       => '0',
        'force_responsive'              => '',
        'limit'                         => '10',
        'pagination'                    => 'yes',
        'order'                         => 'DESC',
        'include'                       => '',
        'exclude'                       => '',

        'points'                        => 'yes',
        'points_types'                  => 'all',
        'awards'                        => 'yes',
        'deducts'                       => 'yes',

        'achievements'                  => 'yes',
        'achievement_types'             => 'all',
        'steps'                         => 'yes',
        'achievements_without_points'   => 'yes',

        'ranks'                         => 'yes',
        'rank_types'                    => 'all',
        'rank_requirements'             => 'yes',
    );

    /**
     * Filters shortcode defaults
     *
     * @since 1.0.0
     *
     * @param array $shortcode_defaults
     *
     * @return array
     */
    $shortcode_defaults = apply_filters( 'gamipress_earnings_shortcode_defaults', $shortcode_defaults );

    $atts = shortcode_atts( $shortcode_defaults, $atts, $shortcode );

    gamipress_enqueue_scripts();

    // On network wide active installs, we need to switch to main blog mostly for posts permalinks and thumbnails
    $blog_id = gamipress_switch_to_main_site_if_network_wide_active();

    // Force to set current user as user ID
    if( $atts['current_user'] === 'yes' ) {

        /**
         * Filter to override shortcode workflow with not logged in users when current user is set to yes
         *
         * @since 1.7.4.2
         *
         * @param bool      $empty_if_not_logged_in     Final workflow to follow
         * @param array     $atts                       Shortcode attributes
         * @param string    $content                    Shortcode content
         */
        $empty_if_not_logged_in = apply_filters( 'gamipress_earnings_shortcode_empty_if_not_logged_in', true, $atts, $content );

        // Return if current_user is set to yes and current user is a guest
        if( get_current_user_id() === 0 && $empty_if_not_logged_in )
            return '';

        $atts['user_id'] = get_current_user_id();

    }

    // GamiPress template args global
    $gamipress_template_args = $atts;

    // Earnings query
    $gamipress_template_args['query'] = gamipress_earnings_shortcode_query( $atts );

    if( ! $gamipress_template_args['query'] ) {
        return '';
    }

    // Earnings columns
    $gamipress_template_args['columns'] = array();

    if( absint( $atts['user_id'] ) === 0 ) {
        $gamipress_template_args['columns']['user']     = __( 'User', 'gamipress' );
    }

    $gamipress_template_args['columns']['thumbnail']    = __( 'Thumbnail', 'gamipress' );
    $gamipress_template_args['columns']['description']  = __( 'Description', 'gamipress' );
    $gamipress_template_args['columns']['date']         = __( 'Date', 'gamipress' );
    $gamipress_template_args['columns']['points']       = __( 'Points', 'gamipress' );

    // Render the earnings template
    ob_start();
    gamipress_get_template_part( 'earnings' );
    $output = ob_get_clean();

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
    return apply_filters( 'gamipress_earnings_shortcode_output', $output, $atts, $content );

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
        'orderby'           => 'date',
        'order'             => $args['order'],
        'items_per_page'    => $args['limit'],
        'paged'             => max( 1, get_query_var( 'paged' ) )
    );

    $query_args['post_type'] = array();
    $query_args['points_type'] = array();
    $query_args['post_type__not_in'] = array();
    $query_args['parent_post_type'] = array();

    // User
    if( isset( $args['user_id'] ) && absint( $args['user_id'] ) !== 0 ) {
        $query_args['user_id'] = $args['user_id'];
    }

    // Build $include array
    if ( ! is_array( $args['include'] ) && ! empty( $args['include'] ) ) {
        $include = explode( ',', $args['include'] );
    }

    // Build $exclude array
    if ( ! is_array( $args['exclude'] ) && ! empty( $args['exclude'] ) ) {
        $exclude = explode( ',', $args['exclude'] );
    }

    // Include certain user earnings
    if ( isset( $include ) && ! empty( $include ) ) {
        $query_args[ 'user_earning__in' ] = $include;
    }

    // Exclude certain user earnings
    if ( isset( $exclude ) && ! empty( $exclude ) ) {
        $query_args[ 'user_earning__not_in' ] = $exclude;
    }

    // Points types
    if( $args['points'] === 'yes' ) {

        if( $args['points_types'] === 'all') {
            $query_args['points_type'] = gamipress_get_points_types_slugs();
        } else {
            $query_args['points_type'] = explode( ',', $args['points_types'] );
        }

        // For custom earnings points to the points type
        $query_args['post_type'][] = 'points-type';

        // Points awards
        if( $args['awards'] === 'yes' ) {
            $query_args['post_type'][] = 'points-award';
        }  else {
            $query_args['post_type__not_in'][] = 'points-award';
        }

        // Points deducts
        if( $args['deducts'] === 'yes' ) {
            $query_args['post_type'][] = 'points-deduct';
        }  else {
            $query_args['post_type__not_in'][] = 'points-deduct';
        }

    } else {
        $query_args['post_type__not_in'][] = 'points-type';
        $query_args['post_type__not_in'][] = 'points-award';
        $query_args['post_type__not_in'][] = 'points-deduct';
    }

    // Achievement types
    if( $args['achievements'] === 'yes' ) {

        if( $args['achievement_types'] === 'all') {
            $achievement_types = gamipress_get_achievement_types_slugs();
        } else {
            $achievement_types = explode( ',', $args['achievement_types'] );
        }

        $query_args['post_type'] = array_merge( $query_args['post_type'], $achievement_types );

        // Step
        if( $args['steps'] === 'yes' ) {
            if( $args['achievement_types'] === 'all') {
                $query_args['post_type'][] = 'step';
            } else {
                $query_args['parent_post_type'] = array_merge( $query_args['parent_post_type'], $achievement_types );
            }
        } else {
            $query_args['post_type__not_in'][] = 'step';
        }

    } else {
        $query_args['post_type__not_in'] = array_merge( $query_args['post_type__not_in'], gamipress_get_achievement_types_slugs() );
        $query_args['post_type__not_in'][] = 'step';
    }

    // Rank types
    if( $args['ranks'] === 'yes' ) {

        if( $args['rank_types'] === 'all') {
            $rank_types = gamipress_get_rank_types_slugs();
        } else {
            $rank_types = explode( ',', $args['rank_types'] );
        }

        $query_args['post_type'] = array_merge( $query_args['post_type'], $rank_types );

        // Rank requirements
        if( $args['rank_requirements'] === 'yes' ) {
            if( $args['rank_types'] === 'all') {
                $query_args['post_type'][] = 'rank-requirement';
            } else {
                $query_args['parent_post_type'] = array_merge( $query_args['parent_post_type'], $rank_types );
            }
        } else {
            $query_args['post_type__not_in'][] = 'rank-requirement';
        }

    } else {
        $query_args['post_type__not_in'] = array_merge( $query_args['post_type__not_in'], gamipress_get_rank_types_slugs() );
        $query_args['post_type__not_in'][] = 'rank-requirement';
    }

    // Remove types that has 'all' value
    foreach( $query_args['post_type'] as $index => $type ) {
        if( $type === 'all' ) {
            unset( $query_args['post_type'][$index] );
        }
    }

    // Return if not types selected
    if( empty( $query_args['post_type'] ) ) {
        return false;
    }

    if( ! empty( $query_args['points_type'] ) ) {
        $query_args['force_types'] = true;
    }

    // If looking to show achievements that do not award any points, then need to add the empty points type value and force the post and points types queries
    if( $args['achievements_without_points'] === 'yes' ) {
        $query_args['points_type'][] = '';
        $query_args['force_types'] = true;
    }

    /**
     * Filters earnings shortcode query args
     *
     * @since 2.0.7
     *
     * @param array $query_args Query args to be passed to CT_Query
     * @param array $args       Function received args
     *
     * @return array
     */
    $query_args = apply_filters( 'gamipress_earnings_shortcode_query_args', $query_args, $args );

    // Setup table
    ct_setup_table( 'gamipress_user_earnings' );

    return new CT_Query( $query_args );

}
