<?php
/**
 * Admin Settings Page
 *
 * @package     GamiPress\Admin\Settings
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

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
 * Helper function to get an option value.
 *
 * @since  1.0.1
 *
 * @param string    $option_name
 * @param bool      $default
 *
 * @return mixed Option value or default parameter value if not exists.
 */
function gamipress_get_option( $option_name, $default = false ) {

    if( GamiPress()->settings === null ) {
        GamiPress()->settings = get_option( 'gamipress_settings' );
    }

    return isset( GamiPress()->settings[ $option_name ] ) ? GamiPress()->settings[ $option_name ] : $default;

}

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

                        $meta_box['fields'][$field_id] = $field;

                    }

                    $meta_box['id'] = $meta_box_id;

                    $meta_box['display_cb'] = false;
                    $meta_box['admin_menu_hook'] = false;

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

    // Create the options page
    new Cmb2_Metatabs_Options( array(
        'key'      => 'gamipress_settings',
        'class'    => 'gamipress-page',
        'title'    => __( 'Settings', 'gamipress' ),
        'topmenu'  => 'gamipress',
        'view_capability' => gamipress_get_manager_capability(),
        'cols'     => 1,
        'boxes'    => $boxes,
        'tabs'     => $tabs,
        'menuargs' => array(
            'menu_title' => __( 'Settings', 'gamipress' ),
        ),
        'savetxt' => __( 'Save Settings', 'gamipress' ),
        'resettxt' => __( 'Reset Settings', 'gamipress' ),
    ) );

}
add_action( 'cmb2_admin_init', 'gamipress_register_settings_page', 11 );

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
        'style' => array(
            'title' => __( 'Style', 'gamipress' ),
            'icon' => 'dashicons-admin-appearance',
        ),
        'logs' => array(
            'title' => __( 'Logs', 'gamipress' ),
            'icon' => 'dashicons-editor-alignleft',
        ),
        'addons' => array(
            'title' => __( 'Add-ons', 'gamipress' ),
            'icon' => 'dashicons-admin-plugins',
        ),
        'licenses' => array(
            'title' => __( 'Licenses', 'gamipress' ),
            'icon' => 'dashicons-admin-network',
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
 * General Settings meta boxes
 *
 * @since  1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_settings_general_meta_boxes( $meta_boxes ) {

    $automatic_updates_plugins = array();

    /**
     * Hook to register a plugin on GamiPress automatic updates feature
     *
     * @since  1.1.4
     *
     * @param array $automatic_updates_plugins Registered plugins for automatic updates
     */
    $automatic_updates_plugins = apply_filters( 'gamipress_automatic_updates_plugins', $automatic_updates_plugins );

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
            'achievement_image_size' => array(
                'name' => __( 'Achievement Image Size', 'gamipress' ),
                'desc' => __( 'Maximum dimensions for the achievements featured image.', 'gamipress' ),
                'type' => 'size',
            ),
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
            'debug_mode' => array(
                'name' => __( 'Debug Mode', 'gamipress' ),
                'desc' => __( 'Check this option to enable the debug mode.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
        ) )
    );

    // if not plugins for automatic updates, then remove field
    if( empty( $automatic_updates_plugins ) ) {
        unset( $meta_boxes['general-settings']['fields']['automatic_updates_plugins'] );
    }

    return $meta_boxes;

}
add_filter( 'gamipress_settings_general_meta_boxes', 'gamipress_settings_general_meta_boxes' );

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
        'title' => __( 'Style Settings', 'gamipress' ),
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

/**
 * Logs Settings meta boxes
 *
 * @since  1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_settings_logs_meta_boxes( $meta_boxes ) {

    $meta_boxes['logs-patterns-settings'] = array(
        'title' => __( 'Logs', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_logs_patterns_settings_fields', array(
            'only_log_events_with_listeners' => array(
                'name' => __( 'Only log activities in use', 'gamipress' ),
                'desc' => __( 'Check this option to just log triggered activities that has a points awards or steps looking for it.', 'gamipress' )
                    . '<br>' . __( 'GamiPress will stop storing unused activities logs like user daily visits.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            'log_patterns_title' => array(
                'name' => __( 'Logs Patterns', 'gamipress' ),
                'description' => __( 'From this settings you can modify the default pattern for upcoming log entries of each category.', 'gamipress' ),
                'type' => 'title',
            ),
            'trigger_log_pattern' => array(
                'name' => __( 'Activity trigger', 'gamipress' ),
                'description' => __( 'Used to register user activity triggered. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{user}', '{trigger_type}', '{count}' ) ),
                'type' => 'text',
                'default' => __( '{user} triggered {trigger_type} (x{count})', 'gamipress' ),
            ),
            'points_earned_log_pattern' => array(
                'name' => __( 'Points earned', 'gamipress' ),
                'description' => __( 'Used when user earns points. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{user}', '{points}', '{points_type}', '{total_points}' ) ),
                'type' => 'text',
                'default' => __( '{user} earned {points} {points_type} for a new total of {total_points} {points_type}', 'gamipress' ),
            ),
            'requirement_complete_log_pattern' => array(
                'name' => __( 'Points award/step complete', 'gamipress' ),
                'description' => __( 'Used when user completes a points award or step. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{user}', '{achievement}', '{achievement_type}' ) ),
                'type' => 'text',
                'default' => __( '{user} completed the {achievement_type} {achievement}', 'gamipress' ),
            ),
            'achievement_earned_log_pattern' => array(
                'name' => __( 'Achievement earned', 'gamipress' ),
                'description' => __( 'Used when user earns an achievement. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{user}', '{achievement}', '{achievement_type}' ) ),
                'type' => 'text',
                'default' => __( '{user} unlocked the {achievement} {achievement_type}', 'gamipress' ),
            ),
            'points_awarded_log_pattern' => array(
                'name' => __( 'Points awarded', 'gamipress' ),
                'description' => __( 'Used when an admin awards an user with points. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{admin}', '{user}', '{points}', '{points_type}', '{total_points}' ) ),
                'type' => 'text',
                'default' => __( '{admin} awarded {user} {points} {points_type} for a new total of {total_points} {points_type}', 'gamipress' ),
            ),
            'achievement_awarded_log_pattern' => array(
                'name' => __( 'Achievement awarded', 'gamipress' ),
                'description' => __( 'Used when an admin awards an user with an achievement. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{admin}', '{user}', '{achievement}', '{achievement_type}' ) ),
                'type' => 'text',
                'default' => __( '{admin} awarded {user} with the the {achievement} {achievement_type}', 'gamipress' ),
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_logs_meta_boxes', 'gamipress_settings_logs_meta_boxes' );

/**
 * Licenses Settings meta boxes
 *
 * @since  1.1.1
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_settings_licenses_meta_boxes( $meta_boxes ) {

    // Get our add-ons
    $plugins = gamipress_plugins_api();

    // Loop settings section meta boxes
    foreach( $meta_boxes as $meta_box_id => $meta_box ) {

        // Only add settings meta box if has fields
        if( isset( $meta_box['fields'] ) && ! empty( $meta_box['fields'] ) ) {

            // Loop meta box fields
            foreach( $meta_box['fields'] as $field_id => $field ) {

                // Update edd_license fields with default parameters to the GamiPress server
                if( $field['type'] === 'edd_license' ) {

                    // if not server provider, then add GamiPress server
                    if( ! isset( $field['server'] ) ) {
                        $field['server'] = 'https://gamipress.com/edd-sl-api';
                    }

                    // Check if is a GamiPress hosted plugin
                    if( $field['server'] === 'https://gamipress.com/edd-sl-api' ) {

                        // Renew link
                        $field['renew_license_link'] = 'https://gamipress.com/renew-a-license';

                        // Before field row hook to render some extra information
                        $field['before_row'] = 'gamipress_license_field_before';

                        // Try to find the plugin thumbnail from plugins API
                        if ( ! is_wp_error( $plugins )
                            && isset( $field['file'] )
                            && ! isset( $field['thumbnail'] ) ) {

                            foreach ( $plugins as $plugin ) {

                                $slug = basename( $field['file'], '.php' );

                                if( $slug === $plugin->info->slug ) {
                                    $field['thumbnail'] = $plugin->info->thumbnail;

                                    // Thumbnail found so exit loop
                                    break;
                                }

                            }

                        }

                    }

                    // Update the field definition
                    $meta_boxes[$meta_box_id]['fields'][$field_id] = $field;
                }
            }

        }

    }

    return $meta_boxes;

}
add_filter( 'gamipress_settings_licenses_meta_boxes', 'gamipress_settings_licenses_meta_boxes', 9999 );

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
        'title' => __( 'Network Settings', 'gamipress' ),
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

/**
 * License field thumbnail.
 *
 * @since  1.1.1
 *
 * @param  array        $field_args Current field args
 * @param  CMB2_Field   $field      Current field object
 */
function gamipress_license_field_before( $field_args, $field ) {

    if( isset( $field_args['thumbnail'] ) && ! empty( $field_args['thumbnail'] ) ) : ?>
    <div class="gamipress-license-thumbnail">
        <img src="<?php echo $field_args['thumbnail']; ?>" alt="<?php echo $field_args['item_name']; ?>">
    </div>
    <?php endif;

}
