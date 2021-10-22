<?php
/**
 * Import/Export Points Tool
 *
 * @package     GamiPress\Admin\Tools\Import_Export_Points
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.6.4
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Import/Export Points Tool meta boxes
 *
 * @since  1.6.4
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_import_export_points_tool_meta_boxes( $meta_boxes ) {

    $meta_boxes['import-export-points'] = array(
        'title' => gamipress_dashicon( 'star-filled' ) . __( 'User Points', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_import_export_points_tool_fields', array(

            // Export

            'export_points_points_types' => array(
                'name' => __( 'Points Types To Export', 'gamipress' ),
                'desc' => __( 'Choose the points types to export.', 'gamipress' ),
                'type' => 'multicheck',
                'classes' => 'gamipress-switch',
                'options_cb' => 'gamipress_options_cb_points_types',
                'option_all' => false,
            ),
            'export_points_user_field' => array(
                'name' => __( 'User Field', 'gamipress' ),
                'desc' => __( 'Choose the field to display on user column.', 'gamipress' ),
                'type' => 'select',
                'options' => array(
                    'id'        => __( 'ID', 'gamipress' ),
                    'username'  => __( 'Username', 'gamipress' ),
                    'email'     => __( 'Email', 'gamipress' ),
                ),
                'default' => 'email'
            ),
            'export_points' => array(
                'label' => __( 'Export User Points', 'gamipress' ),
                'type' => 'button',
                'icon' => 'dashicons-download',
                'button' => 'primary',
                'action' => 'export_points'

            ),

            // Import

            'import_points_file' => array(
                'name' => __( 'CSV File', 'gamipress' ),
                'type' => 'text',
                'attributes' => array(
                    'type' => 'file',
                    'accept' => '.csv'
                )
            ),
            'import_actions' => array(
                'type' => 'multi_buttons',
                'buttons' => array(
                    'import_points' => array(
                        'label' => __( 'Import User Points', 'gamipress' ),
                        'type' => 'button',
                        'button' => 'primary',
                        'icon' => 'dashicons-upload',
                        'action' => 'import_points'
                    ),
                    'download_points_csv_template' => array(
                        'label' =>  __( 'Download CSV Template', 'gamipress' ),
                        'type' => 'button',
                        'icon' => 'dashicons-media-spreadsheet',
                    )
                ),
            ),

        ) ),
        'vertical_tabs' => true,
        'tabs' => apply_filters( 'gamipress_import_export_points_tool_tabs', array(
            'export_points' => array(
                'icon' => 'dashicons-download',
                'title' => __( 'Export', 'gamipress' ),
                'fields' => array(
                    'export_points_points_types',
                    'export_points_user_field',
                    'export_points',
                ),
            ),
            'import_points' => array(
                'icon' => 'dashicons-upload',
                'title' => __( 'Import', 'gamipress' ),
                'fields' => array(
                    'import_points_file',
                    'import_actions',
                ),
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_tools_import_export_meta_boxes', 'gamipress_import_export_points_tool_meta_boxes' );

/**
 * AJAX handler for the export points tool
 *
 * @since 1.6.4
 */
function gamipress_import_export_points_tool_ajax_export() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    global $wpdb;

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    // Setup vars
    $points_types = gamipress_get_points_types();
    $desired_points_types = ( isset( $_REQUEST['points_types'] ) ? $_REQUEST['points_types'] : array() );
    $user_field = ( isset( $_REQUEST['user_field'] ) ? sanitize_text_field( $_REQUEST['user_field'] ) : 'email' );
    $loop = ( isset( $_REQUEST['loop'] ) ? absint( $_REQUEST['loop'] ) : 0 );
    $limit = 200;
    $offset = $limit * $loop;
    $items_to_export = array();

    if( $loop === 0 ) {
        // Set the CSV headers
        $items_to_export[] = array(
            'user'          => __( 'User', 'gamipress' ),
            'points'        => __( 'Points', 'gamipress' ),
            'points_type'   => __( 'Points Type (slug)', 'gamipress' )
        );
    }

    // Check the points types received
    if( empty( $desired_points_types ) ) {
        wp_send_json_error( __( 'You need to choose at least 1 points type to export.', 'gamipress' ) );
    }

    if( ! is_array( $desired_points_types ) ) {
        wp_send_json_error( __( 'You need to choose at least 1 points type to export.', 'gamipress' ) );
    }

    ignore_user_abort( true );

    if ( ! gamipress_is_function_disabled( 'set_time_limit' ) ) {
        set_time_limit( 0 );
    }

    // Get stored users
    $users = gamipress_get_users( array(
        'offset' => $offset,
        'limit' => $limit,
    ) );

    if( empty( $users ) ) {
        // Return a success message
        wp_send_json_success( __( 'User\'s points balances export process has been done successfully.', 'gamipress' ) );
    }

    // Let's to get the data from our users
    foreach( $users as $user ) {

        $user_column = '';

        switch( $user_field ) {
            case 'id':
                $user_column = $user->ID;
                break;
            case 'username':
                $user_column = $user->user_login;
                break;
            case 'email':
                $user_column = $user->user_email;
                break;
        }

        foreach( $desired_points_types as $points_type ) {

            // Skip not registered points types
            if( ! isset( $points_types[$points_type] ) )
                continue;

            // Export a row per points type
            $items_to_export[] = array(
                'user' => $user_column,
                'points' => gamipress_get_user_points( $user->ID, $points_type ),
                'points_type' => $points_type
            );

        }

    }

    $exported_users = $limit * ( $loop + 1 );

    // Get the users count
    $users_count = gamipress_get_users_count();

    $total = $users_count - $exported_users;

    if( $total > 0 ) {
        // Return a run again message
        wp_send_json_success( array(
            'run_again' => true,
            'items'     => $items_to_export,
            'message'   => sprintf( __( '%d remaining users', 'gamipress' ), ( $users_count - $exported_users ) ),
        ) );
    } else {
        // Return a run again message
        wp_send_json_success( array(
            'run_again' => false,
            'items'     => $items_to_export,
            'message'   => __( 'User\'s points balances export process has been done successfully.', 'gamipress' ),
        ) );
    }

}
add_action( 'wp_ajax_gamipress_import_export_points_tool_export', 'gamipress_import_export_points_tool_ajax_export' );


/**
 * AJAX handler for the import points tool
 *
 * @since 1.6.4
 */
function gamipress_import_export_points_tool_ajax_import() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    // Check parameters received
    if( ! isset( $_FILES['file'] ) ) {
        wp_send_json_error( __( 'No file to import.', 'gamipress' ) );
    }

    $import_file = $_FILES['file']['tmp_name'];

    if( empty( $import_file ) ) {
        wp_send_json_error( __( 'Can\'t retrieve the file to import, check server file permissions.', 'gamipress' ) );
    }

    ignore_user_abort( true );

    if ( ! gamipress_is_function_disabled( 'set_time_limit' ) ) {
        set_time_limit( 0 );
    }

    // Retrieve the content from the file
    $file_contents = file_get_contents( $import_file );

    if( empty( $file_contents ) ) {
        wp_send_json_error( __( 'Empty file, so nothing to import.', 'gamipress' ) );
    }

    // Setup vars
    $points_types = gamipress_get_points_types();

    // Explode by line breaks
    $lines = explode( "\n", $file_contents );

    foreach( $lines as $number => $line ) {

        $columns = str_getcsv( $line );

        if( count( $columns ) >= 3 ) {

            $user = false;
            $points = 0;
            $points_type = '';
            $log_description = '';
            $deduct = false;

            // User
            if( isset( $columns[0] ) && ! empty( $columns[0] ) ) {

                $user_field = 'login';

                if( filter_var( $columns[0], FILTER_VALIDATE_EMAIL ) ) {
                    $user_field = 'email';
                } else if( is_numeric( $columns[0] ) ) {
                    $user_field = 'id';
                }

                $user = get_user_by( $user_field, $columns[0] );

            }

            // Points
            if( isset( $columns[1] ) && is_numeric( $columns[1] ) ) {

                // If points amount has a negative sign, then user is looking for deduct
                if ( substr( $columns[1], 0, 1 ) === '-') {
                    $deduct = true;
                    $columns[1] = substr( $columns[1], 1); // Remove the negative sign
                }

                $points = absint( $columns[1] );

            }

            // Points Type
            if( isset( $columns[2] ) && ! empty( $columns[2] ) && isset( $points_types[$columns[2]] ) ) {

                $points_type = $columns[2];

            }

            // Log Description
            if( isset( $columns[3] ) && ! empty( $columns[3] ) ) {

                $log_description = $columns[3];

            }

            // Check if everything is done
            if( $user && $points > 0 && ! empty( $points_type ) ) {

                // When award points passing an admin ID, we need to pass the full new amount
                $current_points = gamipress_get_user_points( $user->ID, $points_type );

                $args = array(
                    'admin_id'  => get_current_user_id(),
                    'reason'    => $log_description,
                    'log_type'  => 'points_award'
                );

                // If log description is empty, let GamiPress to setup it from log settings
                if( empty( $log_description ) ) {
                    $args = array( 'admin_id'  => get_current_user_id() );
                }

                if( $deduct ) {
                    // Deduct points to the user
                    gamipress_deduct_points_to_user( $user->ID, $current_points - $points, $points_type, $args );
                } else {
                    // Award points to the user
                    gamipress_award_points_to_user( $user->ID, $points + $current_points, $points_type, $args );
                }

            }

        }

    }

    // Return a success message
    wp_send_json_success( __( 'User\'s points balances has been updated successfully.', 'gamipress' ) );
}
add_action( 'wp_ajax_gamipress_import_export_points_tool_import', 'gamipress_import_export_points_tool_ajax_import' );