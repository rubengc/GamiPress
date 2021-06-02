<?php
/**
 * Import/Export Settings Tool
 *
 * @package     GamiPress\Admin\Tools\Import_Export_Settings
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.1.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Import/Export Settings Tool meta boxes
 *
 * @since  1.1.7
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_import_export_settings_tool_meta_boxes( $meta_boxes ) {

    $meta_boxes['import-export-settings'] = array(
        'title' => gamipress_dashicon( 'admin-settings' ) . __( 'Settings', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_import_export_settings_tool_fields', array(
            'export_settings' => array(
                'label'     => __( 'Export Settings', 'gamipress' ),
                'desc'      => __( 'Export settings from this site as a file to easily import this configuration to another site.', 'gamipress' ),
                'type'      => 'button',
                'button'    => 'primary',
                'icon'      => 'dashicons-download',
                'action'    => 'export_settings'
            ),
            'import_settings_file' => array(
                'type'          => 'text',
                'attributes'    => array( 'type' => 'file' )
            ),
            'import_settings' => array(
                'label'     => __( 'Import Settings', 'gamipress' ),
                'type'      => 'button',
                'button'    => 'primary',
                'icon'      => 'dashicons-upload',
            ),
        ) ),
        'vertical_tabs' => true,
        'tabs' => apply_filters( 'gamipress_import_export_settings_tool_tabs', array(
            'export_settings' => array(
                'icon' => 'dashicons-download',
                'title' => __( 'Export', 'gamipress' ),
                'fields' => array(
                    'export_settings',
                ),
            ),
            'import_settings' => array(
                'icon' => 'dashicons-upload',
                'title' => __( 'Import', 'gamipress' ),
                'fields' => array(
                    'import_settings_file',
                    'import_settings',
                ),
            ),
        ) ),
    );

    return $meta_boxes;

}
add_filter( 'gamipress_tools_import_export_meta_boxes', 'gamipress_import_export_settings_tool_meta_boxes' );

/**
 * Export Settings action
 *
 * @since 1.1.7
 */
function gamipress_action_export_settings() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    if( ! current_user_can( gamipress_get_manager_capability() ) )
        return;

    nocache_headers();

    header( 'Content-Type: text/plain' );
    header( 'Content-Disposition: attachment; filename="gamipress-settings-export.txt"' );

    $settings = get_option( 'gamipress_settings' );

    echo json_encode( $settings );
    die();

}
add_action( 'gamipress_action_post_export_settings', 'gamipress_action_export_settings' );

/**
 * AJAX handler for the import settings tool
 *
 * @since 1.1.7
 */
function gamipress_ajax_import_settings_tool() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Check parameters received
    if( ! isset( $_FILES['file'] ) )
        wp_send_json_error( __( 'No settings to import.', 'gamipress' ) );

    $import_file = $_FILES['file']['tmp_name'];

    if( empty( $import_file ) )
        wp_send_json_error( __( 'Can not retrieve the file to import, check server file permissions.', 'gamipress' ) );

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    // Retrieve the settings from the file and convert the json object to an array
    $settings = json_decode( file_get_contents( $import_file ), true ) ;

    if( ! is_array( $settings ) || empty( $settings ) ) {
        wp_send_json_error( __( 'Empty settings, so nothing to import.', 'gamipress' ) );
    }

    update_option( 'gamipress_settings', $settings );

    // Return a success message
    wp_send_json_success( __( 'Settings has been imported successfully.', 'gamipress' ) );
}
add_action( 'wp_ajax_gamipress_import_settings_tool', 'gamipress_ajax_import_settings_tool' );