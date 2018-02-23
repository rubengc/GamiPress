<?php
/**
 * GamiPress Logs Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Logs
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register [gamipress_logs] shortcode
 *
 * @since 1.0.0
 */
function gamipress_register_logs_shortcode() {
    gamipress_register_shortcode( 'gamipress_logs', array(
        'name'            => __( 'Logs', 'gamipress' ),
        'description'     => __( 'Output a list of logs.', 'gamipress' ),
        'output_callback' => 'gamipress_logs_shortcode',
        'fields'      => array(
            'current_user' => array(
                'name'        => __( 'Current User', 'gamipress' ),
                'description' => __( 'Show only logs of the current logged in user.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
            ),
            'user_id' => array(
                'name'        => __( 'User', 'gamipress' ),
                'description' => __( 'Show only logs by a specific user. Leave blank to show logs of all users.', 'gamipress' ),
                'type'        => 'select',
                'default'     => '',
                'options_cb'  => 'gamipress_options_cb_users'
            ),
            'limit' => array(
                'name'        => __( 'Limit', 'gamipress' ),
                'description' => __( 'Number of log entries to display.', 'gamipress' ),
                'type'        => 'text',
                'default'     => 10,
            ),
            'pagination' => array(
                'name'        => __( 'Enable Pagination', 'gamipress' ),
                'description' => __( 'Show pagination links to navigate through all logs.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' 	  => 'yes',
            ),
            'orderby' => array(
                'name'        => __( 'Order By', 'gamipress' ),
                'description' => __( 'Parameter to use for sorting.', 'gamipress' ),
                'type'        => 'select',
                'options'      => array(
                    'date'       => __( 'Date', 'gamipress' ),
                    'log_id'     => __( 'Log ID', 'gamipress' ),
                    'title'      => __( 'Log Title', 'gamipress' ),
                    'user_id'    => __( 'Log Author', 'gamipress' ),
                    'rand'       => __( 'Random', 'gamipress' ),
                ),
                'default_cb'     => 'gamipress_logs_order_by_default_cb',
            ),
            'order' => array(
                'name'        => __( 'Order', 'gamipress' ),
                'description' => __( 'Sort order.', 'gamipress' ),
                'type'        => 'select',
                'options'      => array( 'ASC' => __( 'Ascending', 'gamipress' ), 'DESC' => __( 'Descending', 'gamipress' ) ),
                'default'     => 'ASC',
            ),
            'include' => array(
                'name'        => __( 'Include', 'gamipress' ),
                'description' => __( 'Comma-separated list of specific log entries IDs to include.', 'gamipress' ),
                'type'        => 'advanced_select',
                'multiple'    => true,
                'default'     => '',
                'options_cb'  => 'gamipress_options_cb_posts'
            ),
            'exclude' => array(
                'name'        => __( 'Exclude', 'gamipress' ),
                'description' => __( 'Comma-separated list of specific log entries IDs to exclude.', 'gamipress' ),
                'type'        => 'advanced_select',
                'multiple'    => true,
                'default'     => '',
                'options_cb'  => 'gamipress_options_cb_posts'
            ),
        ),
    ) );
}
add_action( 'init', 'gamipress_register_logs_shortcode' );

/**
 * Logs List Shortcode
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes
 *
 * @return string 	   HTML markup
 */
function gamipress_logs_shortcode( $atts = array () ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {
        return gamipress_logs_shortcode_old( $atts );
    }

    global $gamipress_template_args;

    $gamipress_template_args = array();

    // Setup table
    ct_setup_table( 'gamipress_logs' );

    $atts = shortcode_atts( array(
        'current_user'  => 'no',
        'user_id'       => '0',
        'limit'         => '10',
        'pagination'    => 'yes',
        'orderby'       => 'date',
        'order'         => 'ASC',
        'include'       => '',
        'exclude'       => '',
    ), $atts, 'gamipress_logs' );

    // Turn old orderby values into new ones
    switch( $atts['orderby'] ) {
        case 'ID':
            $atts['orderby'] = 'log_id';
            break;
        case 'menu_order':
        case 'modified':
            $atts['orderby'] = 'date';
            break;
        case 'author':
            $atts['orderby'] = 'user_id';
            break;
    }

    gamipress_enqueue_scripts();

    // On network wide active installs, we need to switch to main blog mostly for posts permalinks and thumbnails
    if( gamipress_is_network_wide_active() && ! is_main_site() ) {
        $blog_id = get_current_blog_id();
        switch_to_blog( get_main_site_id() );
    }

    // GamiPress template args global
    $gamipress_template_args = $atts;

    // Query args
    $args = array(
        'orderby'        =>	$atts['orderby'],
        'order'          =>	$atts['order'],
        'items_per_page' =>	$atts['limit'],
        'paged'          => max( 1, get_query_var( 'paged' ) ),
        'access'         => 'public', // At frontend just show public logs
    );

    // User
    if( absint( $atts['user_id'] ) !== 0 ) {
        $args['user_id'] = $atts['user_id'];
    }

    // Force to set current user as user ID
    if( $atts['current_user'] === 'yes' ) {

        // Return if current_user is set to yes and current user is a guest
        if( get_current_user_id() === 0 ) {
            return '';
        }

        $args['user_id'] = get_current_user_id();
    }

    // Build $include array
    if ( ! is_array( $atts['include'] ) && ! empty( $atts['include'] ) ) {
        $include = explode( ',', $atts['include'] );
    }

    // Build $exclude array
    if ( ! is_array( $atts['exclude'] ) && ! empty( $atts['exclude'] ) ) {
        $exclude = explode( ',', $atts['exclude'] );
    }

    // Include certain achievements
    if ( isset( $include ) && ! empty( $include ) ) {
        $args[ 'log__in' ] = $include;
    }

    // Exclude certain achievements
    if ( isset( $exclude ) && ! empty( $exclude ) ) {
        $args[ 'log__not_in' ] = $exclude;
    }

    $gamipress_template_args['query'] = new CT_Query( $args );

    ob_start();
        gamipress_get_template_part( 'logs' );
    $output = ob_get_clean();

    // If switched to blog, return back to que current blog
    if( isset( $blog_id ) ) {
        switch_to_blog( $blog_id );
    }

    return $output;

}

// CMB2 detects 'default' => 'date' as invalid callback because php has the date() function
function gamipress_logs_order_by_default_cb() {
    return 'date';
}
