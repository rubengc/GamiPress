<?php
/**
 * Import/Export Earnings Tool
 *
 * @package     GamiPress\Admin\Tools\Import_Export_Earnings
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       2.0.3
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Import/Export Achievements Tool meta boxes
 *
 * @since  2.0.3
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_import_export_earnings_tool_meta_boxes( $meta_boxes ) {

    // Setup vars
    $points_types = gamipress_get_points_types();
    $achievement_types = gamipress_get_achievement_types();
    $rank_types = gamipress_get_rank_types();

    $options = array();

    // Points types options
    if( count( $points_types ) ) $options['all-points-types'] = __( 'All Points Types', 'gamipress' );

    foreach( $points_types as $slug => $data ) {
        $options[$slug . '-points-type'] = '<strong>' . $data['plural_name'] . '</strong>';
        $options[$slug . '-points-awards'] = __( 'Points Awards', 'gamipress' );
        $options[$slug . '-points-deducts'] = __( 'Points Deductions', 'gamipress' );
    }

    // Achievement types options
    if( count( $achievement_types ) ) $options['all-achievement-types'] = __( 'All Achievement Types', 'gamipress' );

    foreach( $achievement_types as $slug => $data ) {
        $options[$slug . '-achievement-type'] = '<strong>' . $data['singular_name'] . '</strong>';
        $options[$slug . '-achievements'] = __( 'Achievements', 'gamipress' );
        $options[$slug . '-steps'] = __( 'Steps', 'gamipress' );
    }

    // Rank types options
    if( count( $rank_types ) ) $options['all-rank-types'] = __( 'All Rank Types', 'gamipress' );

    foreach( $rank_types as $slug => $data ) {
        $options[$slug . '-rank-type'] = '<strong>' . $data['singular_name'] . '</strong>';
        $options[$slug . '-ranks'] = __( 'Ranks', 'gamipress' );
        $options[$slug . '-rank-requirements'] = __( 'Requirements', 'gamipress' );
    }

    $meta_boxes['import-export-earnings'] = array(
        'title' => gamipress_dashicon( 'awards' ) . __( 'Export User Earnings', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_import_export_earnings_tool_fields', array(
            'export_earnings_types' => array(
                'name' => __( 'Earnings Types To Export', 'gamipress' ),
                'desc' => __( 'Choose the earnings types to export.', 'gamipress' ),
                'type' => 'multicheck',
                'classes' => 'gamipress-switch gamipress-all-types-multicheck',
                'options' => $options,
            ),
            'export_earnings_user_field' => array(
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
            'export_earnings_post_field' => array(
                'name' => __( 'Element Field', 'gamipress' ),
                'desc' => __( 'Choose the field to display for the element (achievement, step, rank, etc) assigned to the earning.', 'gamipress' ),
                'type' => 'select',
                'options' => array(
                    'id'        => __( 'ID', 'gamipress' ),
                    'title'     => __( 'Title', 'gamipress' ),
                    'slug'      => __( 'Slug', 'gamipress' ),
                ),
                'default' => 'slug'
            ),
            'export_earnings_from' => array(
                'name' => __( 'From (Optional)', 'gamipress' ),
                'desc' => '<br>' . __( 'Choose the date from you want to export. User earnings registered <strong>after</strong> this date will be exported.', 'gamipress' )
                    . '<br>' . __( 'Leave blank to no filter by this date.', 'gamipress' ),
                'type' => 'text_date_timestamp',
            ),
            'export_earnings_to' => array(
                'name' => __( 'To (Optional)', 'gamipress' ),
                'desc' => '<br>' . __( 'Choose the date until you want to export. User earnings registered <strong>before</strong> this date will be exported.', 'gamipress' )
                    . '<br>' . __( 'Leave blank to no filter by this date.', 'gamipress' ),
                'type' => 'text_date_timestamp',
            ),
            'export_earnings' => array(
                'label' => __( 'Export User Earnings', 'gamipress' ),
                'type' => 'button',
                'icon' => 'dashicons-download',
                'button' => 'primary',
                'action' => 'export_earnings'
            ),
        ) ),
    );

    return $meta_boxes;

}
add_filter( 'gamipress_tools_import_export_meta_boxes', 'gamipress_import_export_earnings_tool_meta_boxes' );

/**
 * AJAX handler for the export earnings tool
 *
 * @since 2.0.3
 */
function gamipress_import_export_earnings_tool_ajax_export() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    global $wpdb;

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    // Setup types
    $points_types = gamipress_get_points_types();
    $achievement_types = gamipress_get_achievement_types();
    $rank_types = gamipress_get_rank_types();
    $points_types_slugs = gamipress_get_points_types_slugs();
    $achievement_types_slugs = gamipress_get_achievement_types_slugs();
    $rank_types_slugs = gamipress_get_rank_types_slugs();
    $all_types = array_merge( $points_types, $achievement_types, $rank_types );
    $all_types_slugs = array_merge( $points_types_slugs, $achievement_types_slugs, $rank_types_slugs );
    $suffixes = array(
        '-points-type',
            '-points-awards',
            '-points-deducts',
        '-achievement-type',
            '-achievements',
                '-steps',
        '-rank-type',
            '-ranks',
                '-rank-requirements',
    );

    // Setup vars
    $types = ( isset( $_REQUEST['types'] ) ? $_REQUEST['types'] : array() );
    $user_field = ( isset( $_REQUEST['user_field'] ) ? sanitize_text_field( $_REQUEST['user_field'] ) : 'email' );
    $post_field = ( isset( $_REQUEST['post_field'] ) ? sanitize_text_field( $_REQUEST['post_field'] ) : 'slug' );
    $from = ( isset( $_REQUEST['from'] ) ? sanitize_text_field( $_REQUEST['from'] ) : '' );
    $to = ( isset( $_REQUEST['to'] ) ? sanitize_text_field( $_REQUEST['to'] ) : '' );

    // Setup from
    if( $from !== '' && strtotime( $from ) === false ) {
        $from = '';
    }

    // Setup to
    if( $to !== '' && strtotime( $to ) === false ) {
        $to = '';
    }

    if( $to !== '' ) {
        $to = date( 'Y-m-d 23:59:59', strtotime( $to ) );
    }

    $post_types = array();
    $points_types = array( '' ); // Add an empty points type for elements without a points type

    foreach( $types as $type ) {

        foreach( $suffixes as $suffix ) {

            // If option does not ends with one of suffixes then is not correct
            if( ! gamipress_ends_with( $type, $suffix ) ) {
                continue;
            }

            // Remove the suffix to get the type
            $type = str_replace( $suffix, '', $type );

            // Skip if not is a registered type option
            if( ! in_array( $type, $all_types_slugs ) ) {
                continue;
            }

            switch( $suffix ) {
                case '-points-type':
                case '-achievement-type':
                case '-rank-type':
                case '-achievements':
                case '-ranks':
                    // Add the post type
                    $post_types[] = $type;

                    if( $suffix === '-points-type' ) {
                        $post_types[] = 'points-type'; // To include manual awards/deductions
                        $points_types[] = $type;
                    }
                    break;
                case '-points-awards':
                case '-points-deducts':
                case '-steps':
                case '-rank-requirements':
                    // Remove first "-"
                    $post_type = substr( $suffix, 1 );
                    // Remove last "s"
                    $post_type = rtrim( $post_type, 's' );

                    // Add the post type
                    $post_types[] = $post_type;
                    break;
            }

        }

    }

    // Check the achievement types received
    if( empty( $post_types ) ) {
        wp_send_json_error( __( 'You need to choose at least 1 type to export.', 'gamipress' ) );
    }

    ignore_user_abort( true );

    if ( ! gamipress_is_function_disabled( 'set_time_limit' ) ) {
        set_time_limit( 0 );
    }

    $loop = ( isset( $_REQUEST['loop'] ) ? absint( $_REQUEST['loop'] ) : 0 );
    $limit = 200;
    $offset = $limit * $loop;
    $items_to_export = array();

    if( $loop === 0 ) {
        // Set the CSV headers
        $items_to_export[] = array(
            'title'         => __( 'Description', 'gamipress' ),
            'user'          => __( 'User', 'gamipress' ),
            'post'          => __( 'Element', 'gamipress' ),
            'post_type'     => __( 'Element Type', 'gamipress' ),
            'points'        => __( 'Points', 'gamipress' ),
            'points_type'   => __( 'Points Type', 'gamipress' ),
            'date'          => __( 'Date', 'gamipress' ),
        );
    }

    $query = array(
        'post_type' => $post_types,
        'points_type' => $points_types,
        'after' => $from,
        'before' => $to,
    );

    $where = gamipress_get_earnings_where( $query );

    // Merge all wheres
    $where = implode( ' AND ', $where );

    $user_earnings = GamiPress()->db->user_earnings;

    if( ! gamipress_is_network_wide_active() ) {
        // Get users earnings from the current site
        $earnings = $wpdb->get_results(
            "SELECT * 
            FROM {$user_earnings} AS ue
            LEFT JOIN {$wpdb->usermeta} AS umcap ON ( umcap.user_id = ue.user_id ) 
            WHERE {$where} AND umcap.meta_key = '" . $wpdb->get_blog_prefix() . "capabilities'
            ORDER BY ue.date DESC
            LIMIT {$offset}, {$limit}"
        );
    } else {
        // Get all stored user earnings
        $earnings = $wpdb->get_results( "SELECT * FROM {$user_earnings} AS ue WHERE {$where} ORDER BY ue.date DESC LIMIT {$offset}, {$limit}" );
    }

    if( empty( $earnings ) ) {
        // Return a success message
        wp_send_json_success( __( 'User\'s earnings export process has been done successfully.', 'gamipress' ) );
    }

    // Let's to setup the items to export
    foreach( $earnings as $earning ) {

        $user_column = '';
        $user = get_userdata( $earning->user_id );

        switch( $user_field ) {
            case 'id':
                $user_column = ( $user ? $user->ID : '' );
                break;
            case 'username':
                $user_column = ( $user ? $user->user_login : '' );
                break;
            case 'email':
                $user_column = ( $user ? $user->user_email : '' );
                break;
        }

        $post_column = '';
        $post = gamipress_get_post( $earning->post_id );

        switch( $post_field ) {
            case 'id':
                $post_column = ( $post ? $post->ID : '' );
                break;
            case 'title':
                $post_column = ( $post ? $post->post_title : '' );
                break;
            case 'slug':
                $post_column = ( $post ? $post->post_name : '' );
                break;
        }

        // Export a row per achievement type
        $items_to_export[] = array(
            'title'         => $earning->title,
            'user'          => $user_column,
            'post'          => $post_column,
            'post_type'     => $earning->post_type,
            'points'        => $earning->points,
            'points_type'   => $earning->points_type,
            'date'          => $earning->date
        );

    }

    $exported_earnings = $limit * ( $loop + 1 );

    if( ! gamipress_is_network_wide_active() ) {
        // Count users earnings from the current site
        $earnings_count = absint( $wpdb->get_var(
            "SELECT COUNT(*)
            FROM {$user_earnings} AS ue
            LEFT JOIN {$wpdb->usermeta} AS umcap ON ( umcap.user_id = ue.user_id ) 
            WHERE {$where} AND umcap.meta_key = '" . $wpdb->get_blog_prefix() . "capabilities'
            ORDER BY ue.date DESC"
        ) );
    } else {
        // Count all stored user earnings
        $earnings_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$user_earnings} AS ue WHERE {$where} ORDER BY ue.date DESC" ) );
    }

    $total = $earnings_count - $exported_earnings;

    if( $total > 0 ) {
        // Return a run again message
        wp_send_json_success( array(
            'run_again' => true,
            'items'     => $items_to_export,
            'message'   => sprintf( __( '%d remaining earnings', 'gamipress' ), ( $earnings_count - $exported_earnings ) ),
        ) );
    } else {
        // Return a run again message
        wp_send_json_success( array(
            'run_again' => false,
            'items'     => $items_to_export,
            'message'   => __( 'User\'s earnings export process has been done successfully.', 'gamipress' ),
        ) );
    }

}
add_action( 'wp_ajax_gamipress_import_export_earnings_tool_export', 'gamipress_import_export_earnings_tool_ajax_export' );