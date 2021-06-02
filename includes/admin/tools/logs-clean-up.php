<?php
/**
 * Logs Clean Up Tool
 *
 * @package     GamiPress\Admin\Tools\Logs Clean Up
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.8.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Reset Data Tool meta boxes
 *
 * @since  1.8.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_logs_clean_up_tool_meta_boxes( $meta_boxes ) {

    $log_types = gamipress_get_log_types();
    $descriptions = array(
        'default' => __( 'This log has been registered by a plugin so contact his author for more details.', 'gamipress' ),
        'event_trigger' => __( 'Used to log users events perform. GamiPress uses these logs to determine the times a user did a specific action (for example, to award an achievement that requires commenting 3 times). Is safe to delete the old ones.', 'gamipress' ),
        'achievement_earn' => __( 'Used to log users achievement unlocks. Is not recommended to delete them.', 'gamipress' ),
        'achievement_award' => __( 'Used to log achievement awards perform by administrators. Is safe to delete them.', 'gamipress' ),
        'points_earn' => __( 'Used to log users points gains. Is not recommended to delete them.', 'gamipress' ),
        'points_deduct' => __( 'Used to log users points loss. Is not recommended to delete them.', 'gamipress' ),
        'points_expend' => __( 'Used to log users points expended (mainly, for unlocking achievements and ranks through expending points). Is not recommended to delete them.', 'gamipress' ),
        'points_award' => __( 'Used to log positive points movements perform by administrators. Is safe to delete them.', 'gamipress' ),
        'points_revoke' => __( 'Used to log negative points movements perform by administrators. Is safe to delete them.', 'gamipress' ),
        'rank_earn' => __( 'Used to log users rank unlocks. Is not recommended to delete them.', 'gamipress' ),
        'rank_award' => __( 'Used to log rank awards perform by administrators. Is safe to delete them.', 'gamipress' ),
    );

    /**
     * Filter to override log types descriptions for the Logs Clean Up Tool
     *
     * @since  1.8.0
     *
     * @param array $descriptions Default log types descriptions
     *
     * @return array
     */
    $descriptions = apply_filters( 'gamipress__logs_clean_up_tool_log_types_descriptions', $descriptions );

    foreach( $log_types as $log_type => $label ) {
        $description = ( isset( $descriptions[$log_type] ) ? $descriptions[$log_type] : $descriptions['default'] );

        $log_types[$log_type] = '<strong>' . $label . '</strong><br><small>' . $description . '</small>';
    }

    $meta_boxes['logs-clean-up'] = array(
        'title' => gamipress_dashicon( 'trash' ) . __( 'Logs Clean Up', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_logs_clean_up_tool_fields', array(
            'logs_type' => array(
                'name' => __( 'Type', 'gamipress' ),
                'desc' => __( 'Choose the log\'s type you want to clean up.', 'gamipress' ),
                'type' => 'multicheck',
                'classes' => 'gamipress-switch',
                'options' => $log_types,
            ),
            'from' => array(
                'name' => __( 'From (Optional)', 'gamipress' ),
                'desc' => '<br>' . __( 'Choose the date from you want to clean up. Logs registered <strong>after</strong> this date will be deleted.', 'gamipress' )
                    . '<br>' . __( 'Leave blank to no filter by this date.', 'gamipress' ),
                'type' => 'text_date_timestamp',
            ),
            'to' => array(
                'name' => __( 'To (Optional)', 'gamipress' ),
                'desc' => '<br>' . __( 'Choose the date until you want to clean up. Logs registered <strong>before</strong> this date will be deleted.', 'gamipress' )
                    . '<br>' . __( 'Leave blank to no filter by this date.', 'gamipress' ),
                'type' => 'text_date_timestamp',
            ),
            'logs_clean_up_actions' => array(
                'desc' => __( 'You can click on the "Count Logs" button to preview how many logs will be affected.', 'gamipress' ),
                'type' => 'multi_buttons',
                'buttons' => array(
                    'logs_clean_up_count' => array(
                        'label' =>  __( 'Count Logs', 'gamipress' ),
                        'type' => 'button',
                    ),
                    'logs_clean_up' => array(
                        'label' => __( 'Clean Logs', 'gamipress' ),
                        'type' => 'button',
                        'button' => 'primary',
                    ),
                ),
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_tools_general_meta_boxes', 'gamipress_logs_clean_up_tool_meta_boxes' );

/**
 * Common handler for both logs clean up functions
 *
 * @since   1.8.0
 */
function gamipress_ajax_logs_clean_up_tool_checks() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    // Check parameters given
    if( ! isset( $_POST['log_types'] ) || empty( $_POST['log_types'] ) ) {
        wp_send_json_error( __( 'Please, choose at least 1 log type.', 'gamipress' ) );
    }

    ignore_user_abort( true );

    if ( ! gamipress_is_function_disabled( 'set_time_limit' ) ) {
        set_time_limit( 0 );
    }
}

/**
 * Setup the WHERE clause for both logs clean up functions
 *
 * @since   1.8.0
 *
 * @return string
 */
function gamipress_ajax_logs_clean_up_tool_where() {

    $logs = GamiPress()->db->logs;

    // Setup vars
    $log_types = esc_sql( $_POST['log_types'] );
    $from = strtotime($_POST['from'] );
    $to = strtotime( $_POST['to'] );

    $sql = "FROM {$logs} WHERE type IN ( '" . implode( "', '", $log_types ) . "' )";

    // From date
    if( $from ) {
        // Ensure format
        $from = date( 'Y-m-d', $from );

        $sql .= "AND date >= '{$from}'";
    }

    // To date
    if( $to ) {
        // Ensure format
        $to = date( 'Y-m-d', $to );

        $sql .= "AND date <= '{$to}'";
    }

    return $sql;
}

/**
 * Ajax handler for the logs clean up count tool
 *
 * @since   1.8.0
 */
function gamipress_ajax_logs_clean_up_count_tool() {
    // Process the tool checks (common for both processes)
    gamipress_ajax_logs_clean_up_tool_checks();

    global $wpdb;

    $where = gamipress_ajax_logs_clean_up_tool_where();

    $count =  $wpdb->get_var( "SELECT COUNT(*) {$where}" );

    // Return a success message
    wp_send_json_success( sprintf( __( '%d logs will be deleted.', 'gamipress' ), $count ) );

}
add_action( 'wp_ajax_gamipress_logs_clean_up_count_tool', 'gamipress_ajax_logs_clean_up_count_tool' );

/**
 * Ajax handler for the logs clean up tool
 *
 * @since   1.8.0
 */
function gamipress_ajax_logs_clean_up_tool() {
    // Process the tool checks (common for both processes)
    gamipress_ajax_logs_clean_up_tool_checks();

    global $wpdb;

    $logs       = GamiPress()->db->logs;
    $logs_meta  = GamiPress()->db->logs_meta;

    $where = gamipress_ajax_logs_clean_up_tool_where();

    // Delete logs
    $wpdb->query( "DELETE {$where}" );

    // Delete orphaned log metas
    $wpdb->query( "DELETE lm FROM {$logs_meta} lm LEFT JOIN {$logs} l ON l.log_id = lm.log_id WHERE l.log_id IS NULL" );

    // Return a success message
    wp_send_json_success( __( 'Logs clean up process has been done successfully.', 'gamipress' ) );

}
add_action( 'wp_ajax_gamipress_logs_clean_up_tool', 'gamipress_ajax_logs_clean_up_tool' );