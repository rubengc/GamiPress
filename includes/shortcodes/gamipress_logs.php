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
 * Register [gamipress_logs] shortcode.
 *
 * @since 1.0.0
 */
function gamipress_register_logs_shortcode() {
    gamipress_register_shortcode( 'gamipress_logs', array(
        'name'            => __( 'Logs', 'gamipress' ),
        'description'     => __( 'Output a list of logs.', 'gamipress' ),
        'output_callback' => 'gamipress_logs_shortcode',
        'fields'      => array(
            'user_id' => array(
                'name'        => __( 'User ID', 'gamipress' ),
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
 * Logs List Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
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
        'user_id'     => '0',
        'limit'       => '10',
        'orderby'     => 'date',
        'order'       => 'ASC',
        'include'     => '',
        'exclude'     => '',
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

    // GamiPress template args global
    $gamipress_template_args = $atts;

    // Query Achievements
    $args = array(
        'orderby'        =>	$atts['orderby'],
        'order'          =>	$atts['order'],
        'items_per_page' =>	$atts['limit'],
        'access'         => 'public',
    );

    // User
    if( absint( $atts['user_id'] ) !== 0 ) {
        $args['user_id'] = $atts['user_id'];
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

    return $output;

}

// CMB2 detects 'default' => 'date' as invalid callback because php has the date() function
function gamipress_logs_order_by_default_cb() {
    return 'date';
}
