<?php
/**
 * Admin Settings Pages
 *
 * @package     GamiPress\Admin\Settings
 * @since       1.0.0
 */

/**
 * Register GamiPress Settings with Settings API.
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
 * @return mixed Option value or default parameter value if not exists.
 */
function gamipress_get_option( $option_name, $default = false ) {

    if( GamiPress()->settings === null ) {
        GamiPress()->settings = get_option( 'gamipress_settings' );
    }

    return isset( GamiPress()->settings[ $option_name ] ) ? GamiPress()->settings[ $option_name ] : $default;

}

/**
 * GamiPress registered settings
 *
 * @since  1.0.1
 *
 * @return array
 */
function gamipress_get_settings() {

    $gamipress_settings = array(
		'general' => array(
			'title' => __( 'General', 'gamipress' ),
			'icon' => 'dashicons-admin-settings',
            'groups' => apply_filters( 'gamipress_settings_general_section_groups', array(
                'general-settings' => array(
                    'title' => __( 'General Settings', 'gamipress' ),
                    'fields' => array(
                        'minimum_role' => array(
                            'name' => __( 'Minimum role to administer GamiPress', 'gamipress' ),
                            'type' => 'select',
                            'options' => array(
                                'manage_options' => __( 'Administrator', 'gamipress' ),
                                'delete_others_posts' => __( 'Editor', 'gamipress' ),
                                'publish_posts' => __( 'Author', 'gamipress' ),
                            ),
                        ),
                        'achievement_image_size' => array(
                            'name' => __( 'Achievement Image Size', 'gamipress' ),
                            'type' => 'size',
                        ),
                    )
                )
			) )
		),
		'style' => array(
			'title' => __( 'Style', 'gamipress' ),
			'icon' => 'dashicons-admin-appearance',
            'groups' => apply_filters( 'gamipress_settings_style_section_groups', array(
                'style-settings' => array(
                    'title' => __( 'Style Settings', 'gamipress' ),
                    'fields' => array(
                        'disable_css' => array(
                            'name' => __( 'Disable frontend CSS', 'gamipress' ),
                            'type' => 'checkbox',
                            'classes' => 'gamipress-switch',
                        ),
                        'disable_js' => array(
                            'name' => __( 'Disable frontend Javascript', 'gamipress' ),
                            'type' => 'checkbox',
                            'classes' => 'gamipress-switch',
                        ),
                    )
			    )
			) )
		),
        'logs' => array(
            'title' => __( 'Logs', 'gamipress' ),
            'icon' => 'dashicons-editor-alignleft',
            'groups' => apply_filters( 'gamipress_settings_logs_section_groups', array(
                'logs-pattern-settings' => array(
                    'title' => __( 'Logs Patterns', 'gamipress' ),
                    'fields' => array(
                        'log_pattern_title' => array(
                            'name' => '',
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
                    )
                )
            ) )
        ),
    );

    if( is_multisite() ) {
		$gamipress_settings['network'] = array(
			'title' => __( 'Network', 'gamipress' ),
			'icon' => 'dashicons-networking',
            'groups' => apply_filters( 'gamipress_settings_network_section_groups', array(
                'network-settings' => array(
                    'title' => __( 'Network Settings', 'gamipress' ),
                    'fields' => array(
                        'ms_show_all_achievements' => array(
                            'name' => __( 'Show achievements earned across all sites on the network', 'gamipress' ),
                            'type' => 'checkbox',
                            'classes' => 'gamipress-switch',
                        )
                    )
			    )
			) )
		);
    }

    return apply_filters( 'gamipress_settings', $gamipress_settings );

}

/**
 * Register settings page.
 *
 * @since  1.0.2
 *
 * @return void
 */
function gamipress_register_settings_page() {

    $tabs = array();
    $boxes = array();

    // Parse GamiPress settings
    foreach( gamipress_get_settings() as $section_id => $section ) {

        // Section groups
        $groups = array();

        foreach ($section['groups'] as $group_id => $group) {

            $group_fields = array();

            foreach ($group['fields'] as $field_id => $field) {
                $field['id'] = $field_id;

                $group_fields[] = $field;
            }

            // Groups
            $box = new_cmb2_box( array(
                'id'      => $group_id,
                'title'   => $group['title'],
                'show_on' =>array(
                    'key'   => 'options-page',
                    'value' => array( 'gamipress_settings' ),
                ),
                'fields' => $group_fields
            ) );

            $box->object_type( 'options-page' );

            $boxes[] = $box;
            $groups[] = $group_id;
        }

        $tabs[] = array(
            'id'    => $section_id,
            'title' => ( ( isset( $section['icon'] ) ) ? '<i class="dashicons ' . $section['icon'] . '"></i>' : '' ) . $section['title'],
            'desc'  => '',
            'boxes' => $groups,
        );
    }

    // Create the options page
    new Cmb2_Metatabs_Options( array(
        'key'      => 'gamipress_settings',
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
add_action( 'cmb2_admin_init', 'gamipress_register_settings_page' );

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
