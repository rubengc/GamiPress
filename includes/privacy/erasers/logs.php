<?php
/**
 * Logs Erasers
 *
 * @package     GamiPress\Privacy\Erasers\Logs
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.5.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register eraser for user logs.
 *
 * @since 1.5.0
 *
 * @param array $erasers
 *
 * @return array
 */
function gamipress_privacy_register_logs_erasers( $erasers ) {

    $erasers[] = array(
        'eraser_friendly_name'    => __( 'Activity Logs', 'gamipress' ),
        'callback'                  => 'gamipress_privacy_logs_eraser',
    );

    return $erasers;

}
add_filter( 'wp_privacy_personal_data_erasers', 'gamipress_privacy_register_logs_erasers' );

/**
 * Eraser for user logs.
 *
 * @since 1.5.0
 *
 * @param string    $email_address
 * @param int       $page
 *
 * @return array
 */
function gamipress_privacy_logs_eraser( $email_address, $page = 1 ) {

    global $wpdb;

    // Setup query vars
    $logs = GamiPress()->db->logs;

    // Important: %d is user ID
    $query = "DELETE FROM {$logs} WHERE user_id = %d";
    $count_query = str_replace( "DELETE", "SELECT COUNT(*)", $query );

    // Setup vars
    $response = array(
        'items_removed'  => true,
        'items_retained' => false,
        'messages'       => array(),
        'done'           => true
    );

    $user = get_user_by( 'email', $email_address );

    if ( $user && $user->ID ) {

        // Erase user logs
        $erased = $wpdb->query( $wpdb->prepare( $query, $user->ID ) );

        if( $erased ) {

            $messages[] = sprintf( __( 'Removed %d logs.', 'gamipress' ), $erased );

        }

        // Check remaining items
        $items_count = absint( $wpdb->get_var( $wpdb->prepare( $count_query, $user->ID ) ) );

        // Process done!
        $response['done'] = (bool) ( $items_count === 0 );

    }

    // Return our erased items
    return $response;

}