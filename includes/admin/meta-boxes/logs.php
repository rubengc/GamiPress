<?php
/**
 * Logs Meta Boxes
 *
 * @package     GamiPress\Admin\Meta_Boxes\Logs
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.4.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register requirements meta boxes
 *
 * @since 1.0.0
 */
function gamipress_logs_meta_boxes() {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_';

    // Title
    gamipress_add_meta_box(
        'gamipress-log-title',
        __( 'Title', 'gamipress' ),
        'gamipress_logs',
        array(
            'title' => array(
                'name' 	=> __( 'Title', 'gamipress' ),
                'type' 	=> 'text',
                'attributes' => array(
                    'placeholder' => __( 'Enter title here. Leave empty if you want to generate it from the pattern.', 'gamipress' ),
                )
            ),
        ),
        array(
            'priority' => 'high',
        )
    );

    // Log Data
    gamipress_add_meta_box(
        'log-data',
        __( 'Log Data', 'gamipress' ),
        'gamipress_logs',
        array(
            'user_id' => array(
                'name' 	=> __( 'User', 'gamipress' ),
                'desc' 	=> __( 'User assigned to this log.', 'gamipress' ),
                'type' 	=> 'select',
                'options_cb' => 'gamipress_options_cb_users'
            ),
            'type' => array(
                'name' 	=> __( 'Type', 'gamipress' ),
                'desc' 	=> __( 'The log type.', 'gamipress' ),
                'type' 	=> 'select',
                'options' 	=> gamipress_get_log_types(),
            ),
            $prefix . 'pattern' => array(
                'name' 	=> __( 'Pattern', 'gamipress' ),
                'desc' 	=> __( 'The log output pattern. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html(),
                'type' 	=> 'text',
            ),
        ),
        array( 'priority' => 'high' )
    );

}
add_action( 'gamipress_init_gamipress_logs_meta_boxes', 'gamipress_logs_meta_boxes' );