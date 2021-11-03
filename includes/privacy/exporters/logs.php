<?php
/**
 * Logs Exporters
 *
 * @package     GamiPress\Privacy\Exporters\Logs
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.5.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register exporter for user logs.
 *
 * @since 1.5.0
 *
 * @param array $exporters
 *
 * @return array
 */
function gamipress_privacy_register_logs_exporters( $exporters ) {

    $exporters[] = array(
        'exporter_friendly_name'    => __( 'Activity Logs', 'gamipress' ),
        'callback'                  => 'gamipress_privacy_logs_exporter',
    );

    return $exporters;

}
add_filter( 'wp_privacy_personal_data_exporters', 'gamipress_privacy_register_logs_exporters' );

/**
 * Exporter for user logs.
 *
 * @since 1.5.0
 *
 * @param string    $email_address
 * @param int       $page
 *
 * @return array
 */
function gamipress_privacy_logs_exporter( $email_address, $page = 1 ) {

    global $wpdb;

    // Setup query vars
    $logs = GamiPress()->db->logs;

    // Important: keep always SELECT *, %d is user ID, and limit/offset will added automatically
    $query = "SELECT * FROM {$logs} WHERE user_id = %d";
    $count_query = str_replace( "SELECT *", "SELECT COUNT(*)", $query );

    // Setup vars
    $export_items   = array();
    $limit = 500;
    $offset = $limit * ( $page - 1 );
    $done = true;

    $user = get_user_by( 'email', $email_address );

    if ( $user && $user->ID ) {

        // Get user logs
        $user_logs = $wpdb->get_results( $wpdb->prepare(
            $query . " LIMIT {$offset}, {$limit}",
            $user->ID
        ) );

        if( is_array( $user_logs ) ) {

            foreach( $user_logs as $user_log ) {

                // Add the user log to the exported items array
                $export_items[] = array(
                    'group_id'    => 'gamipress-logs',
                    'group_label' => __( 'Activity Logs', 'gamipress' ),
                    'item_id'     => "gamipress-logs-{$user_log->log_id}",
                    'data'        => gamipress_privacy_get_log_data( $user_log ),
                );

            }

        }

        // Check remaining items
        $exported_items_count = $limit * $page;
        $items_count = absint( $wpdb->get_var( $wpdb->prepare( $count_query, $user->ID ) ) );

        // Process done!
        $done = (bool) ( $exported_items_count >= $items_count );

    }

    // Return our exported items
    return array(
        'data' => $export_items,
        'done' => $done
    );

}

/**
 * Function to retrieve log data.
 *
 * @since 1.5.0
 *
 * @param stdClass $log
 *
 * @return array
 */
function gamipress_privacy_get_log_data( $log ) {

    // Prefix for meta data
    $prefix = '_gamipress_';

    // Setup CT table
    ct_setup_table( 'gamipress_logs' );

    $data = array();

    // Log title

    $data['title'] = array(
        'name' => __( 'Log', 'gamipress' ),
        'value' => $log->title,
    );

    // Log type

    $log_types = gamipress_get_log_types();

    $data['type'] = array(
        'name' => __( 'Type', 'gamipress' ),
        'value' => isset( $log_types[$log->type] ) ? $log_types[$log->type] : $log->type,
    );

    if( $log->type === 'event_trigger' ) {

        // Log event

        $data['trigger_type'] = array(
            'name' => __( 'Event', 'gamipress' ),
            'value' => gamipress_get_activity_trigger_label( $log->trigger_type ),
        );

    }

    // If is a specific activity trigger, then add the achievement_post field
    if( in_array( $log->trigger_type, array_keys( gamipress_get_specific_activity_triggers() ) ) ) {

        $post_id = ct_get_object_meta( $log->log_id, $prefix . 'achievement_post', true );
        $post_site_id = ct_get_object_meta( $log->log_id, $prefix . 'achievement_post_site_id', true );

        // Log assigned post

        $data['post'] = array(
            'name' => __( 'Assigned Post', 'gamipress' ),
            'value' => gamipress_get_specific_activity_trigger_post_title( $post_id, $log->trigger_type, $post_site_id ),
        );

        // Log assigned post permalink

        $permalink = gamipress_get_specific_activity_trigger_permalink( $post_id, $log->trigger_type, $post_site_id );

        if( $permalink ) {

            $data['post_url'] = array(
                'name' => __( 'Assigned Post URL', 'gamipress' ),
                'value' => $permalink,
            );

        }


    }

    // Log date

    $data['date'] = array(
        'name' => __( 'Date', 'gamipress' ),
        'value' => $log->date,
    );

    /**
     * User log to export
     *
     * @param array     $data           The user logs data to export
     * @param int       $user_id        The user ID
     * @param stdClass  $log            The log object
     */
    return apply_filters( 'gamipress_privacy_get_log_data', $data, $log->user_id, $log );

}