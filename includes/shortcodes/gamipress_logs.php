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
                    'menu_order' => __( 'Menu Order', 'gamipress' ),
                    'ID'         => __( 'Log ID', 'gamipress' ),
                    'title'      => __( 'Log Title', 'gamipress' ),
                    'date'       => __( 'Published Date', 'gamipress' ),
                    'modified'   => __( 'Last Modified Date', 'gamipress' ),
                    'author'     => __( 'Achievement Author', 'gamipress' ),
                    'rand'       => __( 'Random', 'gamipress' ),
                ),
                'default'     => 'menu_order',
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
                'type'        => 'select_multiple',
                'default'     => '',
                'options_cb'  => 'gamipress_options_cb_posts'
            ),
            'exclude' => array(
                'name'        => __( 'Exclude', 'gamipress' ),
                'description' => __( 'Comma-separated list of specific log entries IDs to exclude.', 'gamipress' ),
                'type'        => 'select_multiple',
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
    global $gamipress_template_args;

    $gamipress_template_args = array();

    $atts = shortcode_atts( array(
        'user_id'     => '0',
        'limit'       => '10',
        'orderby'     => 'menu_order',
        'order'       => 'ASC',
        'include'     => '',
        'exclude'     => '',
    ), $atts, 'gamipress_logs' );

    wp_enqueue_style( 'gamipress' );

    // GamiPress template args global
    $gamipress_template_args = $atts;

    // Query Achievements
    $args = array(
        'post_type'      =>	'gamipress-log',
        'orderby'        =>	$atts['orderby'],
        'order'          =>	$atts['order'],
        'posts_per_page' =>	$atts['limit'],
        'post_status'    => 'publish',
    );

    // User
    if( $atts['user_id'] !== '0' ) {
        $args['author'] = $atts['user_id'];
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
        $args[ 'post__in' ] = $include;
    }

    // Exclude certain achievements
    if ( isset( $exclude ) && ! empty( $exclude ) ) {
        $args[ 'post__not_in' ] = $exclude;
    }

    $gamipress_template_args['query'] = new WP_Query( $args );

    ob_start();
        gamipress_get_template_part( 'logs' );
    $output = ob_get_clean();

    return $output;

}
