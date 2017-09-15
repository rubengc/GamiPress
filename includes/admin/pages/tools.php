<?php
/**
 * Admin Tools Page
 *
 * @package     GamiPress\Admin\Tools
 * @since       1.1.5
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register tools page.
 *
 * @since  1.1.5
 *
 * @return void
 */
function gamipress_register_tools_page() {

    $tabs = array();
    $boxes = array();

    // Loop tools sections
    foreach( gamipress_get_tools_sections() as $section_id => $section ) {

        $meta_boxes = array();

        /**
         * Filter: gamipress_tools_{$section_id}_meta_boxes
         *
         * @param array $meta_boxes
         *
         * @return array
         */
        $meta_boxes = apply_filters( "gamipress_tools_{$section_id}_meta_boxes", $meta_boxes );

        if( ! empty( $meta_boxes ) ) {

            // Loop tools section meta boxes
            foreach( $meta_boxes as $meta_box_id => $meta_box ) {

                // Only add tools meta box if has fields
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
                        'value' => array( 'gamipress_tools' ),
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
        'key'      => 'gamipress_tools',
        'class'    => 'gamipress-page',
        'title'    => __( 'Tools', 'gamipress' ),
        'topmenu'  => 'gamipress',
        'view_capability' => gamipress_get_manager_capability(),
        'cols'     => 1,
        'boxes'    => $boxes,
        'tabs'     => $tabs,
        'menuargs' => array(
            'menu_title' => __( 'Tools', 'gamipress' ),
        ),
        'savetxt' => false,
        'resettxt' => false,
    ) );

}
add_action( 'cmb2_admin_init', 'gamipress_register_tools_page', 10 );

/**
 * GamiPress registered tools sections
 *
 * @since  1.1.5
 *
 * @return array
 */
function gamipress_get_tools_sections() {

    $gamipress_tools_sections = array(
        'general' => array(
            'title' => __( 'General', 'gamipress' ),
            'icon' => 'dashicons-admin-tools',
        ),
        'system' => array(
            'title' => __( 'System', 'gamipress' ),
            'icon' => 'dashicons-performance',
        ),
    );

    return apply_filters( 'gamipress_tools_sections', $gamipress_tools_sections );

}

/**
 * General Tools meta boxes
 *
 * @since  1.1.5
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_tools_general_meta_boxes( $meta_boxes ) {

    $meta_boxes['clean-data'] = array(
        'title' => __( 'Clean Data', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_clean_data_tool_fields', array(
            'clean_data_actions' => array(
                'desc' => __( 'Occasionally, as a consequence of the deletion of data, there may be entries that are not related to any other, like a step without an achievement.', 'gamipress' )
                    . '<br>' . __( 'This tool will search this data to completely remove it from your server.', 'gamipress' ),
                'type' => 'multi_buttons',
                'buttons' => array(
                    'search_data_to_clean' => array(
                        'label' => __( 'Search data to clean', 'gamipress' ),
                    ),
                    'clean_data' => array(
                        'label' => __( 'Proceed with the cleanup', 'gamipress' ),
                        'button' => 'primary'
                    )
                ),
            ),
        ) )
    );

    $meta_boxes['reset-data'] = array(
        'title' => __( 'Reset Data', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_reset_data_tool_fields', array(
            'data_to_reset' => array(
                'desc' => __( 'Choose the stored data you want to reset.', 'gamipress' ),
                'type' => 'multicheck',
                'classes' => 'gamipress-switch',
                'options' => array(
                    'achievement_types' => __( 'Achievement Types', 'gamipress' ),
                    'achievements' => __( 'Achievements', 'gamipress' ),
                    'steps' => __( 'Steps', 'gamipress' ),
                    'points_types' => __( 'Points Types', 'gamipress' ),
                    'points_awards' => __( 'Points Awards', 'gamipress' ),
                    'logs' => __( 'Logs', 'gamipress' ),
                ),
            ),
            'reset_data' => array(
                'label' => __( 'Reset Data', 'gamipress' ),
                'desc' => __( '<strong>Important!</strong> Just use this tool on a test environment or if really you know what are you doing.', 'gamipress' ),
                'type' => 'button',
                'button' => 'primary'
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_tools_general_meta_boxes', 'gamipress_tools_general_meta_boxes' );

/**
 * System Tools meta boxes
 *
 * @since  1.1.5
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_tools_system_meta_boxes( $meta_boxes ) {

    global $wpdb;

    $meta_boxes['server-info'] = array(
        'title' => __( 'Server Info', 'gamipress' ),
        'classes' => 'gamipress-list-table',
        'fields' => apply_filters( 'gamipress_server_info_tool_fields', array(
            'hosting_provider' => array(
                'name' => __( 'Hosting Provider', 'gamipress' ),
                'type' => 'display',
                'value' => gamipress_get_hosting_provider(),
            ),
            'php_version' => array(
                'name' => __( 'PHP Version', 'gamipress' ),
                'type' => 'display',
                'value' => PHP_VERSION,
            ),
            'db_version' => array(
                'name' => __( 'MySQL Version', 'gamipress' ),
                'type' => 'display',
                'value' => $wpdb->db_version(),
            ),
            'server_software' => array(
                'name' => __( 'Webserver Info', 'gamipress' ),
                'type' => 'display',
                'value' => $_SERVER['SERVER_SOFTWARE'],
            ),
            'php_title' => array(
                'name' => __( 'PHP Configuration', 'gamipress' ),
                'type' => 'title',
            ),
            'php_memory_limit' => array(
                'name' => __( 'Memory Limit', 'gamipress' ),
                'type' => 'display',
                'value' => ini_get( 'memory_limit' ),
            ),
            'php_max_execution_time' => array(
                'name' => __( 'Time Limit', 'gamipress' ),
                'type' => 'display',
                'value' => ini_get( 'max_execution_time' ),
            ),
            'php_upload_max_filesize' => array(
                'name' => __( 'Upload Max Size', 'gamipress' ),
                'type' => 'display',
                'value' => ini_get( 'upload_max_filesize' ),
            ),
            'php_post_max_size' => array(
                'name' => __( 'Post Max Size', 'gamipress' ),
                'type' => 'display',
                'value' => ini_get( 'post_max_size' ),
            ),
            'php_max_input_vars' => array(
                'name' => __( 'Max Input Vars', 'gamipress' ),
                'type' => 'display',
                'value' => ini_get( 'max_input_vars' ),
            ),
            'php_display_errors' => array(
                'name' => __( 'Display Errors', 'gamipress' ),
                'type' => 'display',
                'value' => ( ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' ),
            ),
        ) )
    );

    // Get WordPress Theme info
    $theme_data   = wp_get_theme();
    $theme        = $theme_data->Name . ' (' . $theme_data->Version . ')';
    $parent_theme = $theme_data->Template;

    if ( ! empty( $parent_theme ) ) {
        $parent_theme_data = wp_get_theme( $parent_theme );
        $parent_theme      = $parent_theme_data->Name . ' (' . $parent_theme_data->Version . ')';
    }

    $plugins = get_plugins();
    $active_plugins = get_option( 'active_plugins', array() );
    $active_plugins_output = '';

    foreach ( $plugins as $plugin_path => $plugin ) {
        // If the plugin isn't active, don't show it.
        if ( ! in_array( $plugin_path, $active_plugins ) )
            continue;

        $active_plugins_output .= $plugin['Name'] . ' (' . $plugin['Version'] . ')' . '<br>';
    }

    $meta_boxes['wordpress-info'] = array(
        'title' => __( 'WordPress Info', 'gamipress' ),
        'classes' => 'gamipress-list-table',
        'fields' => apply_filters( 'gamipress_wordpress_info_tool_fields', array(
            'site_url' => array(
                'name' => __( 'Site URL', 'gamipress' ),
                'type' => 'display',
                'value' => site_url(),
            ),
            'home_url' => array(
                'name' => __( 'Home URL', 'gamipress' ),
                'type' => 'display',
                'value' => home_url(),
            ),
            'multisite' => array(
                'name' => __( 'Multisite', 'gamipress' ),
                'type' => 'display',
                'value' => ( is_multisite() ? 'Yes' : 'No' ),
            ),
            'wp_version' => array(
                'name' => __( 'Version', 'gamipress' ),
                'type' => 'display',
                'value' => get_bloginfo( 'version' ),
            ),
            'wp_locale' => array(
                'name' => __( 'Language', 'gamipress' ),
                'type' => 'display',
                'value' => ( ! empty( get_locale() ) ? get_locale() : 'en_US' ),
            ),
            'wp_permalink' => array(
                'name' => __( 'Permalink Structure', 'gamipress' ),
                'type' => 'display',
                'value' => ( get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : 'Default' ),
            ),
            'wp_abspath' => array(
                'name' => __( 'Absolute Path', 'gamipress' ),
                'type' => 'display',
                'value' => ABSPATH,
            ),
            'wp_debug' => array(
                'name' => __( 'Debug', 'gamipress' ),
                'type' => 'display',
                'value' => ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ),
            ),
            'wp_memory_limit' => array(
                'name' => __( 'Memory Limit', 'gamipress' ),
                'type' => 'display',
                'value' => WP_MEMORY_LIMIT,
            ),
            'wp_table_prefix' => array(
                'name' => __( 'Table Prefix:', 'gamipress' ),
                'type' => 'display',
                'value' => $wpdb->prefix,
            ),
            'wp_theme' => array(
                'name' => __( 'Active Theme', 'gamipress' ),
                'type' => 'display',
                'value' => $theme,
            ),
            'wp_parent_theme' => array(
                'name' => __( 'Parent Theme', 'gamipress' ),
                'type' => 'display',
                'value' => $parent_theme,
            ),
            'wp_active_plugins' => array(
                'name' => __( 'Active Plugins', 'gamipress' ),
                'type' => 'display',
                'value' => $active_plugins_output,
            ),
        ) )
    );

    $achievement_types = gamipress_get_achievement_types();
    $achievement_types_output = '';

    foreach ( $achievement_types as $achievement_type_slug => $achievement_type ) {
        if ( in_array( $achievement_type_slug, gamipress_get_requirement_types_slugs() ) )
            continue;

        $achievement_types_output .= $achievement_type['singular_name'] . ' - ' . $achievement_type['plural_name'] . ' - ' . $achievement_type_slug . ' (#' . $achievement_type['ID'] . ')' . '<br>';
    }

    $points_types = gamipress_get_points_types();
    $points_types_output = '';

    foreach ( $points_types as $points_type_slug => $points_type ) {

        $points_types_output .= $points_type['singular_name'] . ' - ' . $points_type['plural_name'] . ' - ' . $points_type_slug . ' (#' . $points_type['ID'] . ')' . '<br>';

    }

    if( GamiPress()->settings === null ) {
        GamiPress()->settings = get_option( 'gamipress_settings' );
    }

    $gamipress_settings_output = '';

    foreach ( GamiPress()->settings as $setting_key => $setting_value ) {

        if( is_array( $setting_value ) ) {
            $setting_value = json_encode( $setting_value );
        }

        $gamipress_settings_output .= $setting_key . ': ' . $setting_value . '<br>';

    }

    $meta_boxes['gamipress-info'] = array(
        'title' => __( 'GamiPress Info', 'gamipress' ),
        'classes' => 'gamipress-list-table',
        'fields' => apply_filters( 'gamipress_gamipress_info_tool_fields', array(
            'achievement_types' => array(
                'name' => __( 'Achievement Types', 'gamipress' ),
                'type' => 'display',
                'value' => $achievement_types_output,
            ),
            'points_types' => array(
                'name' => __( 'Points Types', 'gamipress' ),
                'type' => 'display',
                'value' => $points_types_output,
            ),
            'gamipress_settings' => array(
                'name' => __( 'Settings', 'gamipress' ),
                'type' => 'display',
                'value' => $gamipress_settings_output,
            ),
        ) )
    );

    $meta_boxes['download-system-info'] = array(
        'title' => __( 'Download System Info', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_download_system_info_tool_fields', array(
            'download_system_info' => array(
                'label' => __( 'Download System Info File', 'gamipress' ),
                'type' => 'button',
                'button' => 'primary',
                'action' => 'download_system_info',
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_tools_system_meta_boxes', 'gamipress_tools_system_meta_boxes' );

/**
 * GamiPress Tools bottom
 *
 * @since 1.1.5
 *
 * @param string $content   Content to be filtered
 * @param string $page      Current page slug
 *
 * @return mixed string $host if detected, false otherwise
 */
function gamipress_after_tools_page( $content, $page ) {

    if( $page !== 'gamipress_tools' ) {
        return $content;
    }

    ob_start();
    ?>
    <!-- Reset Data Dialog -->
    <div id="reset-data-dialog" title="Are you sure you want to delete this data?" style="display: none;">
        <span><?php _e( 'This action will delete all of the entries you selected. It will be completely <strong>irrecoverable</strong>.', 'gamipress' ); ?></span>
        <br><br>
        <span><?php _e( 'Please, confirm the data that will be removed:', 'gamipress' ); ?></span>
        <div id="reset-data-reminder"></div>
    </div>
    <?php
    $content .= ob_get_clean();

    return $content;
}
add_filter( 'cmb2metatabs_after_form', 'gamipress_after_tools_page', 10, 2 );

/**
 * Return the hosting provider this site is using if possible
 *
 * Taken from Easy Digital Downloads
 *
 * @since 1.1.5
 *
 * @return mixed string $host if detected, false otherwise
 */
function gamipress_get_hosting_provider() {
    $host = false;

    if( defined( 'WPE_APIKEY' ) ) {
        $host = 'WP Engine';
    } elseif( defined( 'PAGELYBIN' ) ) {
        $host = 'Pagely';
    } elseif( DB_HOST == 'localhost:/tmp/mysql5.sock' ) {
        $host = 'ICDSoft';
    } elseif( DB_HOST == 'mysqlv5' ) {
        $host = 'NetworkSolutions';
    } elseif( strpos( DB_HOST, 'ipagemysql.com' ) !== false ) {
        $host = 'iPage';
    } elseif( strpos( DB_HOST, 'ipowermysql.com' ) !== false ) {
        $host = 'IPower';
    } elseif( strpos( DB_HOST, '.gridserver.com' ) !== false ) {
        $host = 'MediaTemple Grid';
    } elseif( strpos( DB_HOST, '.pair.com' ) !== false ) {
        $host = 'pair Networks';
    } elseif( strpos( DB_HOST, '.stabletransit.com' ) !== false ) {
        $host = 'Rackspace Cloud';
    } elseif( strpos( DB_HOST, '.sysfix.eu' ) !== false ) {
        $host = 'SysFix.eu Power Hosting';
    } elseif( strpos( $_SERVER['SERVER_NAME'], 'Flywheel' ) !== false ) {
        $host = 'Flywheel';
    } else {
        // Adding a general fallback for data gathering
        $host = 'DBH: ' . DB_HOST . ', SRV: ' . $_SERVER['SERVER_NAME'];
    }

    return $host;
}

/**
 * AJAX handler for the search data to clean action
 *
 * @since 1.1.5
 */
function gamipress_ajax_search_data_to_clean() {

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    $requirements = gamipress_get_unassigned_requirements();
    $found_results = count( $requirements );
    $data = array(
        'found_results' => $found_results,
        'message' => ''
    );

    // Return a success message
    if( $requirements && $found_results > 0 ) {
        $data['message'] = sprintf( _n( '%s entry found.', '%s entries found.', $found_results, 'gamipress' ), $found_results );
    } else {
        $data['message'] = __( 'No data to clean.', 'gamipress' );
    }

    $data['message'] = '<span>' . $data['message'] . '</span>';

    wp_send_json_success( $data );
}
add_action( 'wp_ajax_gamipress_search_data_to_clean', 'gamipress_ajax_search_data_to_clean' );

/**
 * AJAX handler for the clean data tool
 *
 * @since 1.1.5
 */
function gamipress_ajax_clean_data_tool() {

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    $requirements = gamipress_get_unassigned_requirements();

    foreach( $requirements as $requirement ) {
        wp_delete_post( $requirement['ID'] );
    }

    // Return a success message
    wp_send_json_success( __( 'Cleanup process has been done successfully.', 'gamipress' ) );
}
add_action( 'wp_ajax_gamipress_clean_data_tool', 'gamipress_ajax_clean_data_tool' );

/**
 * AJAX handler for the reset data tool
 *
 * @since 1.1.5
 */
function gamipress_ajax_reset_data_tool() {

    // Check parameters received
    if( ! isset( $_POST['items'] ) || empty( $_POST['items'] ) ) {
        wp_send_json_error( __( 'No items selected.', 'gamipress' ) );
    }

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    global $wpdb;

    foreach( $_POST['items'] as $item ) {

        switch( $item ) {
            case 'achievement_types':
                $wpdb->delete( $wpdb->posts, array(
                    'post_type' => 'achievement-type'
                ) );
                break;
            case 'achievements':
                break;
            case 'steps':
                $wpdb->delete( $wpdb->posts, array(
                    'post_type' => 'step'
                ) );
                break;
            case 'points_types':
                $wpdb->delete( $wpdb->posts, array(
                    'post_type' => 'points-type'
                ) );
                break;
            case 'points_awards':
                $wpdb->delete( $wpdb->posts, array(
                    'post_type' => 'points-award'
                ) );
                break;
            case 'logs':
                $wpdb->delete( $wpdb->posts, array(
                    'post_type' => 'gamipress-log'
                ) );
                break;
            default:
                do_action( 'gamipress_reset_data_tool_reset', $item );
                break;
        }

    }

    // Return a success message
    wp_send_json_success( __( 'Data has been reset successfully.', 'gamipress' ) );
}
add_action( 'wp_ajax_gamipress_reset_data_tool', 'gamipress_ajax_reset_data_tool' );

/**
 * Download System Info action
 *
 * @since 1.1.5
 */
function gamipress_action_download_system_info() {

    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    nocache_headers();

    header( 'Content-Type: text/plain' );
    header( 'Content-Disposition: attachment; filename="gamipress-system-info.txt"' );

    $meta_boxes = array();
    $output = '';

    $meta_boxes = gamipress_tools_system_meta_boxes( $meta_boxes );

    $output .= 'GAMIPRESS SYSTEM INFO START';

    foreach( $meta_boxes as $meta_box_id => $meta_box ) {

        if( $meta_box_id === 'download-system-info' ) {
            continue;
        }
        $output .= "\n\n" . '---------------------------------------------' . "\n";
        $output .= $meta_box['title'] . "\n";
        $output .= '---------------------------------------------' . "\n\n";

        if( isset( $meta_box['fields'] ) && ! empty( $meta_box['fields'] ) ) {

            // Loop meta box fields
            foreach( $meta_box['fields'] as $field_id => $field ) {

                if( $field['type'] === 'title' ) {
                    $output .= "\n----- " . $field['name'] . " -----\n\n";
                } else if( $field['type'] === 'display' ) {
                    $output .= str_pad( $field['name'] . ':', 30 ) . $field['value'] . "\n";
                }

            }

        }
    }

    $output .= "\n" . 'GAMIPRESS SYSTEM INFO END';

    $output = str_replace( '<br>', "\n", $output );

    echo $output;
    die();

}
add_action( 'gamipress_action_post_download_system_info', 'gamipress_action_download_system_info' );