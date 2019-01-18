<?php
/**
 * User Earnings Erasers
 *
 * @package     GamiPress\Privacy\Erasers\User_Earnings
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.5.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register eraser for user user earnings.
 *
 * @since 1.5.0
 *
 * @param array $erasers
 *
 * @return array
 */
function gamipress_privacy_register_user_earnings_erasers( $erasers ) {

    $erasers[] = array(
        'eraser_friendly_name'    => __( 'User Earnings', 'gamipress' ),
        'callback'                  => 'gamipress_privacy_user_earnings_eraser',
    );

    return $erasers;

}
add_filter( 'wp_privacy_personal_data_erasers', 'gamipress_privacy_register_user_earnings_erasers' );

/**
 * Eraser for user user earnings.
 *
 * @since 1.5.0
 *
 * @param string    $email_address
 * @param int       $page
 *
 * @return array
 */
function gamipress_privacy_user_earnings_eraser( $email_address, $page = 1 ) {

    global $wpdb;

    // Setup query vars
    $user_earnings = GamiPress()->db->user_earnings;

    // Important: keep always SELECT *, %d is user ID, and limit/offset will added automatically
    $query = "DELETE FROM {$user_earnings} WHERE user_id = %d";
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

        // Erase user earnings
        $erased = $wpdb->query( $wpdb->prepare( $query, $user->ID ) );

        if( $erased ) {

            $messages[] = sprintf( __( 'Removed %d user earnings.', 'gamipress' ), $erased );

        }

        // Check remaining items
        $items_count = absint( $wpdb->get_var( $wpdb->prepare( $count_query, $user->ID ) ) );

        // Process done!
        $response['done'] = (bool) ( $items_count === 0 );

    }

    // Return our erased items
    return $response;

}