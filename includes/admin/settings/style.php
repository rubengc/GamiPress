<?php
/**
 * Admin Style Settings
 *
 * @package     GamiPress\Admin\Settings\Style
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.3.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Style Settings meta boxes
 *
 * @since  1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_settings_style_meta_boxes( $meta_boxes ) {

    $meta_boxes['style-settings'] = array(
        'title' => gamipress_dashicon( 'admin-appearance' ) . __( 'Style Settings', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_style_settings_fields', array(
            'disable_css' => array(
                'name' => __( 'Disable frontend CSS', 'gamipress' ),
                'desc' => __( 'Check this option to stop enqueue frontend CSS resources.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            'disable_js' => array(
                'name' => __( 'Disable frontend Javascript', 'gamipress' ),
                'desc' => __( 'Check this option to stop enqueue frontend Javascript resources.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_style_meta_boxes', 'gamipress_settings_style_meta_boxes' );