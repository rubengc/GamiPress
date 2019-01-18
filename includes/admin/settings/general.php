<?php
/**
 * Admin General Settings
 *
 * @package     GamiPress\Admin\Settings\General
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.3.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * General Settings meta boxes
 *
 * @since  1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_settings_general_meta_boxes( $meta_boxes ) {

    $meta_boxes['general-settings'] = array(
        'title' => __( 'General Settings', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_general_settings_fields', array(
            'minimum_role' => array(
                'name' => __( 'Minimum role to administer GamiPress', 'gamipress' ),
                'desc' => __( 'Minimum role an user needs to access to GamiPress management areas.', 'gamipress' ),
                'type' => 'select',
                'options' => array(
                    'manage_options' => __( 'Administrator', 'gamipress' ),
                    'delete_others_posts' => __( 'Editor', 'gamipress' ),
                    'publish_posts' => __( 'Author', 'gamipress' ),
                ),
            ),
            'points_image_size' => array(
                'name' => __( 'Points Image Size', 'gamipress' ),
                'desc' => __( 'Maximum dimensions for the points featured image.', 'gamipress' ),
                'type' => 'size',
                'default' => array(
                    'width' => 50,
                    'height' => 50,
                ),
            ),
            'achievement_image_size' => array(
                'name' => __( 'Achievement Image Size', 'gamipress' ),
                'desc' => __( 'Maximum dimensions for the achievements featured image.', 'gamipress' ),
                'type' => 'size',
            ),
            'rank_image_size' => array(
                'name' => __( 'Rank Image Size', 'gamipress' ),
                'desc' => __( 'Maximum dimensions for ranks featured image.', 'gamipress' ),
                'type' => 'size',
            ),
            'disable_shortcodes_editor' => array(
                'name' => __( 'Disable Shortcodes Editor', 'gamipress' ),
                'desc' => __( 'Check this option to disable the shortcodes editor.', 'gamipress' ) . '<br>'
                . '<small>' . __( 'Check this option if you are experiencing black screens in your theme settings or in your page builder forms.', 'gamipress' ) . '</small>',
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            'debug_mode' => array(
                'name' => __( 'Debug Mode', 'gamipress' ),
                'desc' => __( 'Check this option to enable the debug mode.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
        ) )
    );

    $automatic_updates_plugins = array();

    /**
     * Hook to register a plugin on GamiPress automatic updates feature
     *
     * @since  1.1.4
     *
     * @param array $automatic_updates_plugins Registered plugins for automatic updates
     */
    $automatic_updates_plugins = apply_filters( 'gamipress_automatic_updates_plugins', $automatic_updates_plugins );

    $meta_boxes['automatic-updates-settings'] = array(
        'title' => __( 'Automatic Updates', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_general_settings_fields', array(
            'automatic_updates' => array(
                'name' => __( 'Automatic Updates', 'gamipress' ),
                'desc' => __( 'Check this option to automatically get the latest features, bugfixes and security updates as they are released.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            'automatic_updates_plugins' => array(
                'name' => __( 'Plugins', 'gamipress' ),
                'desc' => __( 'Check GamiPress add-ons you want to automatically update.', 'gamipress' ),
                'type' => 'multicheck',
                'classes' => 'gamipress-switch',
                'options' => $automatic_updates_plugins
            ),
        ) )
    );

    // if not plugins for automatic updates, then remove field
    if( empty( $automatic_updates_plugins ) ) {
        unset( $meta_boxes['automatic-updates-settings']['fields']['automatic_updates_plugins'] );
    }

    return $meta_boxes;

}
add_filter( 'gamipress_settings_general_meta_boxes', 'gamipress_settings_general_meta_boxes' );