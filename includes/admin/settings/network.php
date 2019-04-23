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
 * Network Settings meta boxes
 *
 * @since  1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_settings_network_meta_boxes( $meta_boxes ) {

    $meta_boxes['network-settings'] = array(
        'title' => gamipress_dashicon( 'admin-multisite' ) . __( 'Network Settings', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_network_settings_fields', array(
            'ms_show_all_achievements' => array(
                'name' => __( 'Show achievements earned across all sites on the network', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            )
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_network_meta_boxes', 'gamipress_settings_network_meta_boxes' );