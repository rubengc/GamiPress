<?php
/**
 * Import/Export Achievements Tool
 *
 * @package     GamiPress\Admin\Tools\Import_Export_Achievements
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.6.4
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Import/Export Achievements Tool meta boxes
 *
 * @since  1.6.4
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_import_export_achievements_tool_meta_boxes( $meta_boxes ) {

    $meta_boxes['import-export-achievements'] = array(
        'title' => gamipress_dashicon( 'awards' ) . __( 'User Achievements', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_import_export_achievements_tool_fields', array(

            // Export

            'export_achievements_achievement_types' => array(
                'name' => __( 'Achievement Types To Export', 'gamipress' ),
                'desc' => __( 'Choose the achievement types to export.', 'gamipress' ),
                'type' => 'multicheck',
                'classes' => 'gamipress-switch',
                'options_cb' => 'gamipress_options_cb_achievement_types',
                'option_all' => false,
            ),
            'export_achievements_user_field' => array(
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
            'export_achievements_achievement_field' => array(
                'name' => __( 'Achievement Field', 'gamipress' ),
                'desc' => __( 'Choose the field to display on achievements column.', 'gamipress' ),
                'type' => 'select',
                'options' => array(
                    'id'        => __( 'ID', 'gamipress' ),
                    'title'     => __( 'Title', 'gamipress' ),
                    'slug'      => __( 'Slug', 'gamipress' ),
                ),
                'default' => 'slug'
            ),
            'export_achievements' => array(
                'label' => __( 'Export User Achievements', 'gamipress' ),
                'type' => 'button',
                'icon' => 'dashicons-download',
                'button' => 'primary',
                'action' => 'export_achievements'

            ),

            // Import

            'import_achievements_file' => array(
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
                    'import_achievements' => array(
                        'label' => __( 'Import User Achievements', 'gamipress' ),
                        'type' => 'button',
                        'button' => 'primary',
                        'icon' => 'dashicons-upload',
                        'action' => 'import_achievements'
                    ),
                    'download_achievements_csv_template' => array(
                        'label' =>  __( 'Download CSV Template', 'gamipress' ),
                        'type' => 'button',
                        'icon' => 'dashicons-media-spreadsheet',
                    )
                ),
            ),

        ) ),
        'vertical_tabs' => true,
        'tabs' => apply_filters( 'gamipress_import_export_achievements_tool_tabs', array(
            'export_achievements' => array(
                'icon' => 'dashicons-download',
                'title' => __( 'Export', 'gamipress' ),
                'fields' => array(
                    'export_achievements_achievement_types',
                    'export_achievements_user_field',
                    'export_achievements_achievement_field',
                    'export_achievements',
                ),
            ),
            'import_achievements' => array(
                'icon' => 'dashicons-upload',
                'title' => __( 'Import', 'gamipress' ),
                'fields' => array(
                    'import_achievements_file',
                    'import_actions',
                ),
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_tools_import_export_meta_boxes', 'gamipress_import_export_achievements_tool_meta_boxes' );

/**
 * AJAX handler for the export achievements tool
 *
 * @since 1.6.4
 */
function gamipress_import_export_achievements_tool_ajax_export() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    global $wpdb;

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    // Setup vars
    $achievement_types = gamipress_get_achievement_types();
    $desired_achievement_types = ( isset( $_REQUEST['achievement_types'] ) ? $_REQUEST['achievement_types'] : array() );
    $user_field = ( isset( $_REQUEST['user_field'] ) ? sanitize_text_field( $_REQUEST['user_field'] ) : 'email' );
    $achievement_field = ( isset( $_REQUEST['achievement_field'] ) ? sanitize_text_field( $_REQUEST['achievement_field'] ) : 'slug' );
    $loop = ( isset( $_REQUEST['loop'] ) ? absint( $_REQUEST['loop'] ) : 0 );
    $limit = 200;
    $offset = $limit * $loop;
    $items_to_export = array();

    if( $loop === 0 ) {
        // Set the CSV headers
        $items_to_export[] = array(
            'user'              => __( 'User', 'gamipress' ),
            'achievements'      => __( 'Achievements', 'gamipress' ),
            'achievement_type'  => __( 'Achievements Type (slug)', 'gamipress' ),
        );
    }

    // Check the achievement types received
    if( empty( $desired_achievement_types ) ) {
        wp_send_json_error( __( 'You need to choose at least 1 achievement type to export.', 'gamipress' ) );
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
        wp_send_json_success( __( 'User\'s achievements export process has been done successfully.', 'gamipress' ) );
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

        foreach( $desired_achievement_types as $achievement_type ) {

            // Skip not registered achievement types
            if( ! isset( $achievement_types[$achievement_type] ) )
                continue;

            $user_achievements = gamipress_get_user_achievements( array(
                'user_id'          => $user->ID,
                'achievement_type' => $achievement_type,
            ) );

            if( $user_achievements ) {

                $achievements = array();

                // Loop all user achievements to build an array of desired achievement field to be imploded by comma
                foreach( $user_achievements as $user_achievement ) {

                    switch( $achievement_field ) {
                        case 'id':
                            $achievements[] = $user_achievement->ID;
                            break;
                        case 'title':
                            $achievement = gamipress_get_post( $user_achievement->ID );
                            $achievements[] = $achievement->post_title;
                            break;
                        case 'slug':
                            $achievement = gamipress_get_post( $user_achievement->ID );
                            $achievements[] = $achievement->post_name;
                            break;
                    }

                }

                // Export a row per achievement type
                $items_to_export[] = array(
                    'user' => $user_column,
                    'achievements' => implode( ',', $achievements ),
                    'achievement_type' => $achievement_type,
                );
            }

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
            'message'   => __( 'User\'s achievements export process has been done successfully.', 'gamipress' ),
        ) );
    }

}
add_action( 'wp_ajax_gamipress_import_export_achievements_tool_export', 'gamipress_import_export_achievements_tool_ajax_export' );


/**
 * AJAX handler for the import achievements tool
 *
 * @since 1.6.4
 */
function gamipress_import_export_achievements_tool_ajax_import() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    global $wpdb;

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
    $posts = GamiPress()->db->posts;
    $achievement_types = gamipress_get_achievement_types_slugs();
    $post_type_where = "AND post_type IN('" . implode( "', '", $achievement_types ) . "')";

    // Explode by line breaks
    $lines = explode( "\n", $file_contents );

    foreach( $lines as $number => $line ) {

        $columns = str_getcsv( $line );

        if( count( $columns ) >= 2 ) {

            $user = false;
            $achievements = array();

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

            // Achievements
            if( isset( $columns[1] ) && ! empty( $columns[1] ) ) {

                $achievements = explode( ',', $columns[1] );

            }

            // Check if everything is done
            if( $user && count( $achievements ) > 0 ) {

                // Loop all achievements that will be awarded
                foreach( $achievements as $achievement ) {

                    $revoke = false;

                    // If achievement has a negative sign, then user is looking to revoke
                    if ( substr( $achievement, 0, 1 ) === '-') {
                        $revoke = true;
                        $achievement = substr( $achievement, 1); // Remove the negative sign
                    }

                    if( is_numeric( $achievement ) ) {

                        // Search by ID
                        $achievement_post = gamipress_get_post( $achievement );

                    } else {

                        // Search by title
                        $achievement_post = $wpdb->get_row( $wpdb->prepare(
                            "SELECT * FROM {$posts} WHERE post_title = %s {$post_type_where}",
                            $achievement
                        ) );

                        if( ! $achievement_post ) {
                            // Search by slug
                            $achievement_post = $wpdb->get_row( $wpdb->prepare(
                                "SELECT * FROM {$posts} WHERE post_name = %s {$post_type_where}",
                                $achievement
                            ) );

                        }

                    }

                    // Check if post object is an achievement
                    if( $achievement_post && gamipress_is_achievement( $achievement_post ) ) {

                        if( $revoke ) {
                            // Revoke the achievement to the user
                            gamipress_revoke_achievement_to_user( $achievement_post->ID, $user->ID );
                        } else {
                            // Award the achievement to the user
                            gamipress_award_achievement_to_user( $achievement_post->ID, $user->ID, get_current_user_id() );
                        }


                    }
                }

            }

        }

    }

    // Return a success message
    wp_send_json_success( __( 'Achievements has been awarded successfully to all users.', 'gamipress' ) );
}
add_action( 'wp_ajax_gamipress_import_export_achievements_tool_import', 'gamipress_import_export_achievements_tool_ajax_import' );