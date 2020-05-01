<?php
/**
 * System Info Tool
 *
 * @package     GamiPress\Admin\Tools\System_Info
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.1.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register System Info Tool meta boxes
 *
 * @since   1.1.7
 * @updated 1.5.9 Added the GamiPress installation date on GamiPress info box
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_system_info_tool_meta_boxes( $meta_boxes ) {

    global $wpdb;

    $download = ( defined( 'GAMIPRESS_DOWNLOADING_SYSTEM_INFO' ) && GAMIPRESS_DOWNLOADING_SYSTEM_INFO );

    // ----------------------------------------------------
    // Server Info
    // ----------------------------------------------------

    $meta_boxes['server-info'] = array(
        'title' => ( ! $download ? gamipress_dashicon( 'dashboard' ) : '' ) . __( 'Server Info', 'gamipress' ),
        'classes' => 'gamipress-list-table',
        'fields' => apply_filters( 'gamipress_server_info_tool_fields', array(
            'hosting_provider' => array(
                'name' => __( 'Hosting Provider', 'gamipress' ),
                'type' => 'display',
                'value' => gamipress_get_hosting_provider(),
            ),
            'php_os' => array(
                'name' => __( 'Operating System', 'gamipress' ),
                'type' => 'display',
                'value' => PHP_OS,
            ),
            'php_version' => array(
                'name' => __( 'PHP Version', 'gamipress' ),
                'type' => 'display',
                'value' => PHP_VERSION,
                'classes' => ( version_compare( PHP_VERSION, '7.0.0', '>=' ) ? 'gamipress-label-success' : 'gamipress-label-danger' ),
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

    // ----------------------------------------------------
    // Database Info
    // ----------------------------------------------------

    // Check database tables
    $logs_exists = gamipress_database_table_exists( GamiPress()->db->logs );
    $logs_meta_exists = gamipress_database_table_exists( GamiPress()->db->logs_meta );
    $user_earnings_exists = gamipress_database_table_exists( GamiPress()->db->user_earnings );
    $user_earnings_meta_exists = gamipress_database_table_exists( GamiPress()->db->user_earnings_meta );
    $last_upgrade = get_option( 'gamipress_version', '1.0.0' );
    $last_required_upgrade = gamipress_get_last_required_upgrade();

    $meta_boxes['db-info'] = array(
        'title' => ( ! $download ? gamipress_dashicon( 'cloud' ) : '' ) . __( 'Database Info', 'gamipress' ),
        'classes' => 'gamipress-list-table',
        'fields' => apply_filters( 'gamipress_db_info_tool_fields', array(
            'db_host' => array(
                'name' => __( 'Database Host', 'gamipress' ),
                'type' => 'display',
                'value' => DB_HOST,
            ),
            'db_version' => array(
                'name' => __( 'MySQL Version', 'gamipress' ),
                'type' => 'display',
                'value' => $wpdb->db_version(),
                'classes' => ( version_compare( $wpdb->db_version(), '5.0', '>=' ) ? 'gamipress-label-success' : 'gamipress-label-danger' ),
            ),
            'gamipress_last_upgrade' => array(
                'name' => __( 'Last Upgrade', 'gamipress' ),
                'type' => 'display',
                'value' => $last_upgrade,
                'classes' => ( version_compare( $last_upgrade, $last_required_upgrade, '>=' ) ? 'gamipress-label-success' : 'gamipress-label-danger' ),
            ),
            'gamipress_logs' => array(
                'name' => __( 'Logs Database', 'gamipress' ),
                'type' => 'display',
                'value' => ( $logs_exists ? 'Yes' : 'No' ),
                'classes' => ( $logs_exists ? 'gamipress-label-success' : 'gamipress-label-danger' ),
            ),
            'gamipress_logs_meta' => array(
                'name' => __( 'Logs Meta Database', 'gamipress' ),
                'type' => 'display',
                'value' => ( $logs_meta_exists ? 'Yes' : 'No' ),
                'classes' => ( $logs_meta_exists ? 'gamipress-label-success' : 'gamipress-label-danger' ),
            ),
            'gamipress_user_earnigns' => array(
                'name' => __( 'User Earnings Database', 'gamipress' ),
                'type' => 'display',
                'value' => ( $user_earnings_exists ? 'Yes' : 'No' ),
                'classes' => ( $user_earnings_exists ? 'gamipress-label-success' : 'gamipress-label-danger' ),
            ),
            'gamipress_user_earnigns_meta' => array(
                'name' => __( 'User Earnings Meta Database', 'gamipress' ),
                'type' => 'display',
                'value' => ( $user_earnings_meta_exists ? 'Yes' : 'No' ),
                'classes' => ( $user_earnings_meta_exists ? 'gamipress-label-success' : 'gamipress-label-danger' ),
            ),
        ) )
    );

    // ----------------------------------------------------
    // WordPress Info
    // ----------------------------------------------------

    $locale = get_locale();

    $timezone = get_option( 'timezone_string' );

    if ( ! $timezone ) {
        $timezone = get_option( 'gmt_offset' );
    }

    // Get WordPress Theme info
    $theme_data   = wp_get_theme();
    $theme        = $theme_data->Name . ' (' . $theme_data->Version . ')';
    $parent_theme = $theme_data->Template;

    if ( ! empty( $parent_theme ) ) {
        $parent_theme_data = wp_get_theme( $parent_theme );
        $parent_theme      = $parent_theme_data->Name . ' (' . $parent_theme_data->Version . ')';
    }

    // Retrieve current plugin information
    if( ! function_exists( 'get_plugins' ) ) {
        include ABSPATH . '/wp-admin/includes/plugin.php';
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
        'title' => ( ! $download ? gamipress_dashicon( 'wordpress' ) : '' ) . __( 'WordPress Info', 'gamipress' ),
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
                'value' => ( ! empty( $locale ) ? $locale : 'en_US' ),
            ),
            'wp_timezone' => array(
                'name' => __( 'Timezone', 'gamipress' ),
                'type' => 'display',
                'value' => $timezone,
            ),
            'wp_date' => array(
                'name' => __( 'Site Date', 'gamipress' ),
                'type' => 'display',
                'value' => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
            ),
            'wp_date_utc' => array(
                'name' => __( 'Universal Date', 'gamipress' ),
                'type' => 'display',
                'value' => date_i18n( 'Y-m-d H:i:s', $timestamp_with_offset = false, $gmt = false ),
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

    // ----------------------------------------------------
    // GamiPress Info
    // ----------------------------------------------------

    // Get all points types
    $points_types = gamipress_get_points_types();
    $points_types_output = '';

    foreach ( $points_types as $points_type_slug => $points_type ) {
        $points_types_output .= $points_type['singular_name'] . ' - ' . $points_type['plural_name'] . ' - ' . $points_type_slug . ' (#' . $points_type['ID'] . ')' . '<br>';
    }

    // Get all achievement types
    $achievement_types = gamipress_get_achievement_types();
    $achievement_types_output = '';

    foreach ( $achievement_types as $achievement_type_slug => $achievement_type ) {
        $achievement_types_output .= $achievement_type['singular_name'] . ' - ' . $achievement_type['plural_name'] . ' - ' . $achievement_type_slug . ' (#' . $achievement_type['ID'] . ')' . '<br>';
    }

    // Get all rank types
    $rank_types = gamipress_get_rank_types();
    $rank_types_output = '';

    foreach ( $rank_types as $rank_type_slug => $rank_type ) {
        $rank_types_output .= $rank_type['singular_name'] . ' - ' . $rank_type['plural_name'] . ' - ' . $rank_type_slug . ' (#' . $rank_type['ID'] . ')' . '<br>';
    }

    // Listeners count
    $listeners_count = gamipress_get_triggers_listeners_count();
    $listeners_count_output = '';

    foreach( $listeners_count as $trigger => $count ) {
        $label = gamipress_get_activity_trigger_label( $trigger );

        if( empty( $label ) )
            $label = $trigger . ' ' . __( '(Event\'s plugin not installed)', 'gamipress' );

        $listeners_count_output .= ( ! $download ? $label : $trigger ) . ': ' . $count . '<br>';
    }

    // Get all settings stored
    if( GamiPress()->settings === null ) {
        if( gamipress_is_network_wide_active() ) {
            GamiPress()->settings = ( $exists = get_site_option( 'gamipress_settings' ) ) ? $exists : array();
        } else {
            GamiPress()->settings = ( $exists = get_option( 'gamipress_settings' ) ) ? $exists : array();
        }
    }

    $gamipress_settings_output = '';

    if( GamiPress()->settings ) {

        foreach ( GamiPress()->settings as $setting_key => $setting_value ) {

            if( is_array( $setting_value ) )
                $setting_value = json_encode( $setting_value );

            $gamipress_settings_output .= $setting_key . ': ' . $setting_value . '<br>';

        }

    }

    // Get the installation date
    if( gamipress_is_network_wide_active() ) {
        $gamipress_install_date = ( $exists = get_site_option( 'gamipress_install_date' ) ) ? $exists : '';
    } else {
        $gamipress_install_date = ( $exists = get_option( 'gamipress_install_date' ) ) ? $exists : '';
    }

    $meta_boxes['gamipress-info'] = array(
        'title' => ( ! $download ? gamipress_dashicon( 'gamipress' ) : '' ) . __( 'GamiPress Info', 'gamipress' ),
        'classes' => 'gamipress-list-table',
        'fields' => apply_filters( 'gamipress_gamipress_info_tool_fields', array(
            'points_types' => array(
                'name' => __( 'Points Types', 'gamipress' ),
                'type' => 'display',
                'value' => $points_types_output,
            ),
            'achievement_types' => array(
                'name' => __( 'Achievement Types', 'gamipress' ),
                'type' => 'display',
                'value' => $achievement_types_output,
            ),
            'rank_types' => array(
                'name' => __( 'Rank Types', 'gamipress' ),
                'type' => 'display',
                'value' => $rank_types_output,
            ),
            'listeners_count' => array(
                'name' => __( 'Events Listeners', 'gamipress' ),
                'type' => 'display',
                'value' => $listeners_count_output,
            ),
            'gamipress_install_date' => array(
                'name' => __( 'Installation Date', 'gamipress' ),
                'type' => 'display',
                'value' => $gamipress_install_date,
            ),
            'gamipress_settings' => array(
                'name' => __( 'Settings', 'gamipress' ),
                'type' => 'display',
                'value' => $gamipress_settings_output,
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_tools_system_meta_boxes', 'gamipress_system_info_tool_meta_boxes' );

/**
 * Register Download System Info Tool meta boxes
 *
 * @since  1.1.7
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_download_system_info_tool_meta_boxes( $meta_boxes ) {

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
add_filter( 'gamipress_tools_system_meta_boxes', 'gamipress_download_system_info_tool_meta_boxes', 9999 );

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
        $host = $_SERVER['SERVER_NAME'];
    }

    return $host;
}

/**
 * Download System Info action
 *
 * @since 1.1.5
 */
function gamipress_action_download_system_info() {

    if( ! current_user_can( gamipress_get_manager_capability() ) )
        return;

    nocache_headers();

    header( 'Content-Type: text/plain' );
    header( 'Content-Disposition: attachment; filename="gamipress-system-info.txt"' );

    // Define a global var to meet that this execution is to download a system info file
    if( ! defined( 'GAMIPRESS_DOWNLOADING_SYSTEM_INFO' ) )
        define( 'GAMIPRESS_DOWNLOADING_SYSTEM_INFO', true );

    $meta_boxes = array();
    $output = '';

    $meta_boxes = gamipress_system_info_tool_meta_boxes( $meta_boxes );

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