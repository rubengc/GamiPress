<?php
/**
 * Admin Settings Page
 *
 * @package     GamiPress\Admin\Settings
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_DIR . 'includes/admin/settings/general.php';
require_once GAMIPRESS_DIR . 'includes/admin/settings/social.php';
require_once GAMIPRESS_DIR . 'includes/admin/settings/style.php';
require_once GAMIPRESS_DIR . 'includes/admin/settings/email.php';
require_once GAMIPRESS_DIR . 'includes/admin/settings/logs.php';
require_once GAMIPRESS_DIR . 'includes/admin/settings/network.php';

/**
 * Register GamiPress Settings with Settings API.
 *
 * @since  1.0.0
 *
 * @return void
 */
function gamipress_register_settings() {

	register_setting( 'gamipress_settings', 'gamipress_settings' );

}
add_action( 'admin_init', 'gamipress_register_settings' );

/**
 * Register settings page.
 *
 * @since  1.0.0
 *
 * @return void
 */
function gamipress_register_settings_page() {

    $tabs = array();
    $boxes = array();

    $is_settings_page = ( isset( $_GET['page'] ) && $_GET['page'] === 'gamipress_settings' );

    if( $is_settings_page ) {

        // Loop settings sections
        foreach( gamipress_get_settings_sections() as $section_id => $section ) {

            $meta_boxes = array();

            /**
             * Filter: gamipress_settings_{$section_id}_meta_boxes
             *
             * @param array $meta_boxes
             *
             * @return array
             */
            $meta_boxes = apply_filters( "gamipress_settings_{$section_id}_meta_boxes", $meta_boxes );

            if( ! empty( $meta_boxes ) ) {

                // Loop settings section meta boxes
                foreach( $meta_boxes as $meta_box_id => $meta_box ) {

                    // Check meta box tabs
                    if( isset( $meta_box['tabs'] ) && ! empty( $meta_box['tabs'] ) ) {

                        // Loop meta box tabs
                        foreach( $meta_box['tabs'] as $tab_id => $tab ) {

                            $tab['id'] = $tab_id;

                            $meta_box['tabs'][$tab_id] = $tab;

                        }

                    }

                    // Only add settings meta box if has fields
                    if( isset( $meta_box['fields'] ) && ! empty( $meta_box['fields'] ) ) {

                        // Loop meta box fields
                        foreach( $meta_box['fields'] as $field_id => $field ) {

                            $field['id'] = $field_id;

                            // Support for group fields
                            if( isset( $field['fields'] ) && is_array( $field['fields'] ) ) {

                                foreach( $field['fields'] as $group_field_id => $group_field ) {

                                    $field['fields'][$group_field_id]['id'] = $group_field_id;

                                }

                            }

                            $meta_box['fields'][$field_id] = $field;

                        }

                        $meta_box['id'] = $meta_box_id;

                        $meta_box['display_cb'] = false;
                        $meta_box['admin_menu_hook'] = false;
                        $meta_box['priority'] = 'high'; // Fixes issue with CMB2 2.9.0

                        $meta_box['show_on'] = array(
                            'key'   => 'options-page',
                            'value' => array( 'gamipress_settings' ),
                        );

                        $box = new_cmb2_box( $meta_box );

                        $box->object_type( 'options-page' );

                        $boxes[] = $box;

                    }
                }

                $tabs[] = array(
                    'id'    => $section_id,
                    'title' => ( ( isset( $section['icon'] ) ) ? '<i class="dashicons ' . $section['icon'] . '"></i>' : '' ) . $section['title'],
                    'desc'  => '',
                    'boxes' => array_keys( $meta_boxes ),
                );
            }
        }

    }

    $minimum_role = gamipress_get_manager_capability();

    try {
        // Create the options page
        new Cmb2_Metatabs_Options( array(
            'key'      => 'gamipress_settings',
            'class'    => 'gamipress-page',
            'title'    => __( 'Settings', 'gamipress' ),
            'topmenu'  => 'gamipress',
            'cols'     => 1,
            'boxes'    => $boxes,
            'tabs'     => $tabs,
            'menuargs' => array(
                'menu_title' => __( 'Settings', 'gamipress' ),
                'capability'        => $minimum_role,
                'view_capability'   => $minimum_role,
            ),
            'savetxt' => __( 'Save Settings', 'gamipress' ),
            'resettxt' => __( 'Reset Settings', 'gamipress' ),
        ) );
    } catch ( Exception $e ) {

    }

}
add_action( 'cmb2_admin_init', 'gamipress_register_settings_page', 12 );

/**
 * GamiPress registered settings sections
 *
 * @since  1.0.1
 *
 * @return array
 */
function gamipress_get_settings_sections() {

    $gamipress_settings_sections = array(
        'general' => array(
            'title' => __( 'General', 'gamipress' ),
            'icon' => 'dashicons-admin-settings',
        ),
        'social' => array(
            'title' => __( 'Social', 'gamipress' ),
            'icon' => 'dashicons-share',
        ),
        'style' => array(
            'title' => __( 'Style', 'gamipress' ),
            'icon' => 'dashicons-admin-appearance',
        ),
        'email' => array(
            'title' => __( 'Emails', 'gamipress' ),
            'icon' => 'dashicons-email-alt',
        ),
        'logs' => array(
            'title' => __( 'Logs', 'gamipress' ),
            'icon' => 'dashicons-editor-alignleft',
        ),
        'addons' => array(
            'title' => __( 'Add-ons', 'gamipress' ),
            'icon' => 'dashicons-admin-plugins',
        ),
    );

    if( is_multisite() ) {
        $gamipress_settings_sections['network'] = array(
            'title' => __( 'Network', 'gamipress' ),
            'icon' => 'dashicons-networking',
        );
    }

    return apply_filters( 'gamipress_settings_sections', $gamipress_settings_sections );

}

/**
 * Get capability required for GamiPress administration.
 *
 * @since  1.0.0
 *
 * @return string User capability.
 */
function gamipress_get_manager_capability() {

    return gamipress_get_option( 'minimum_role', 'manage_options' );

}
