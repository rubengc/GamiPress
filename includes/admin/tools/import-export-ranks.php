<?php
/**
 * Import/Export Ranks Tool
 *
 * @package     GamiPress\Admin\Tools\Import_Export_Ranks
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.6.4
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Import/Export Ranks Tool meta boxes
 *
 * @since  1.6.4
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_import_export_ranks_tool_meta_boxes( $meta_boxes ) {

    $meta_boxes['import-export-ranks'] = array(
        'title' => gamipress_dashicon( 'rank' ) . __( 'User Ranks', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_import_export_ranks_tool_fields', array(

            // Export

            'export_ranks_rank_types' => array(
                'name' => __( 'Rank Types To Export', 'gamipress' ),
                'desc' => __( 'Choose the rank types to export.', 'gamipress' ),
                'type' => 'multicheck',
                'classes' => 'gamipress-switch',
                'options_cb' => 'gamipress_options_cb_rank_types',
                'option_all' => false,
            ),
            'export_ranks_user_field' => array(
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
            'export_ranks_rank_field' => array(
                'name' => __( 'Rank Field', 'gamipress' ),
                'desc' => __( 'Choose the field to display on rank column.', 'gamipress' ),
                'type' => 'select',
                'options' => array(
                    'id'        => __( 'ID', 'gamipress' ),
                    'title'     => __( 'Title', 'gamipress' ),
                    'slug'      => __( 'Slug', 'gamipress' ),
                ),
                'default' => 'slug'
            ),
            'export_ranks' => array(
                'label' => __( 'Export User Ranks', 'gamipress' ),
                'type' => 'button',
                'icon' => 'dashicons-download',
                'button' => 'primary',
                'action' => 'export_ranks'

            ),

            // Import

            'import_ranks_file' => array(
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
                    'import_ranks' => array(
                        'label' => __( 'Import User Ranks', 'gamipress' ),
                        'type' => 'button',
                        'button' => 'primary',
                        'icon' => 'dashicons-upload',
                        'action' => 'import_ranks'
                    ),
                    'download_ranks_csv_template' => array(
                        'label' =>  __( 'Download CSV Template', 'gamipress' ),
                        'type' => 'button',
                        'icon' => 'dashicons-media-spreadsheet',
                    )
                ),
            ),

        ) ),
        'vertical_tabs' => true,
        'tabs' => apply_filters( 'gamipress_import_export_ranks_tool_tabs', array(
            'export_ranks' => array(
                'icon' => 'dashicons-download',
                'title' => __( 'Export', 'gamipress' ),
                'fields' => array(
                    'export_ranks_rank_types',
                    'export_ranks_user_field',
                    'export_ranks_rank_field',
                    'export_ranks',
                ),
            ),
            'import_ranks' => array(
                'icon' => 'dashicons-upload',
                'title' => __( 'Import', 'gamipress' ),
                'fields' => array(
                    'import_ranks_file',
                    'import_actions',
                ),
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_tools_import_export_meta_boxes', 'gamipress_import_export_ranks_tool_meta_boxes' );

/**
 * AJAX handler for the export ranks tool
 *
 * @since 1.6.4
 */
function gamipress_import_export_ranks_tool_ajax_export() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    global $wpdb;

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    // Setup vars
    $rank_types = gamipress_get_rank_types();
    $desired_rank_types = ( isset( $_REQUEST['rank_types'] ) ? $_REQUEST['rank_types'] : array() );
    $user_field = ( isset( $_REQUEST['user_field'] ) ? sanitize_text_field( $_REQUEST['user_field'] ) : 'email' );
    $rank_field = ( isset( $_REQUEST['rank_field'] ) ? sanitize_text_field( $_REQUEST['rank_field'] ) : 'slug' );
    $loop = ( isset( $_REQUEST['loop'] ) ? absint( $_REQUEST['loop'] ) : 0 );
    $limit = 200;
    $offset = $limit * $loop;
    $items_to_export = array();

    if( $loop === 0 ) {
        // Set the CSV headers
        $items_to_export[] = array(
            'user'      => __( 'User', 'gamipress' ),
            'rank'      => __( 'Rank', 'gamipress' ),
            'rank_type' => __( 'Rank Type (slug)', 'gamipress' ),
        );
    }

    // Check the rank types received
    if( empty( $desired_rank_types ) ) {
        wp_send_json_error( __( 'You need to choose at least 1 rank type to export.', 'gamipress' ) );
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
        wp_send_json_success( __( 'User\'s ranks export process has been done successfully.', 'gamipress' ) );
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

        foreach( $desired_rank_types as $rank_type ) {

            // Skip not registered rank types
            if( ! isset( $rank_types[$rank_type] ) )
                continue;

            $rank_column = '';

            // get the user rank
            switch( $rank_field ) {
                case 'id':
                    $rank_column = gamipress_get_user_rank_id( $user->ID, $rank_type );
                    break;
                case 'title':
                    $rank = gamipress_get_user_rank( $user->ID, $rank_type );
                    $rank_column = $rank->post_title;
                    break;
                case 'slug':
                    $rank = gamipress_get_user_rank( $user->ID, $rank_type );
                    $rank_column = $rank->post_name;
                    break;
            }

            // Export a row per rank type
            $items_to_export[] = array(
                'user' => $user_column,
                'rank' => $rank_column,
                'rank_type' => $rank_type,
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
            'message'   => __( 'User\'s ranks export process has been done successfully.', 'gamipress' ),
        ) );
    }

}
add_action( 'wp_ajax_gamipress_import_export_ranks_tool_export', 'gamipress_import_export_ranks_tool_ajax_export' );


/**
 * AJAX handler for the import ranks tool
 *
 * @since 1.6.4
 */
function gamipress_import_export_ranks_tool_ajax_import() {
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
    $rank_types = gamipress_get_rank_types_slugs();
    $post_type_where = "AND post_type IN('" . implode( "', '", $rank_types ) . "')";

    // Explode by line breaks
    $lines = explode( "\n", $file_contents );

    foreach( $lines as $number => $line ) {

        $columns = str_getcsv( $line );

        if( count( $columns ) >= 2 ) {

            $user = false;
            $rank = '';

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

            // Rank
            if( isset( $columns[1] ) && ! empty( $columns[1] ) ) {

                $rank = $columns[1];

            }

            // Check if everything is done
            if( $user && ! empty( $rank ) ) {

                $revoke = false;

                // If rank has a negative sign, then user is looking to revoke
                if ( substr( $rank, 0, 1 ) === '-') {
                    $revoke = true;
                    $rank = substr( $rank, 1); // Remove the negative sign
                }

                if( is_numeric( $rank ) ) {

                    // Search by ID
                    $rank_post = gamipress_get_post( $rank );

                } else {

                    // Search by title
                    $rank_post = $wpdb->get_row( $wpdb->prepare(
                        "SELECT * FROM {$posts} WHERE post_title = %s {$post_type_where}",
                        $rank
                    ) );

                    if( ! $rank_post ) {
                        // Search by slug
                        $rank_post = $wpdb->get_row( $wpdb->prepare(
                            "SELECT * FROM {$posts} WHERE post_name = %s {$post_type_where}",
                            $rank
                        ) );

                    }

                }

                // Check if post object is a rank
                if( $rank_post && gamipress_is_rank( $rank_post ) ) {

                    if( $revoke ) {
                        $new_rank_id = gamipress_get_prev_user_rank_id( $user->ID, $rank_post->post_type );

                        // Revoke the rank to the user
                        gamipress_revoke_rank_to_user( $user->ID, $rank_post->ID, $new_rank_id, array( 'admin_id' => get_current_user_id() ) );
                    } else {
                        // Award the rank to the user
                        gamipress_award_rank_to_user( $rank_post->ID, $user->ID, array( 'admin_id' => get_current_user_id() ) );
                    }
                }

            }

        }

    }

    // Return a success message
    wp_send_json_success( __( 'Ranks has been awarded successfully to all users.', 'gamipress' ) );
}
add_action( 'wp_ajax_gamipress_import_export_ranks_tool_import', 'gamipress_import_export_ranks_tool_ajax_import' );