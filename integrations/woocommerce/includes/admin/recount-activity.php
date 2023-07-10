<?php
/**
 * Recount Activity
 *
 * @package GamiPress\WooCommerce\Admin\Recount_Activity
 * @since 1.0.4
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add recountable options to the Recount Activity Tool
 *
 * @since 1.0.4
 *
 * @param array $recountable_activity_triggers
 *
 * @return array
 */
function gamipress_wc_recountable_activity_triggers( $recountable_activity_triggers ) {

    $recountable_activity_triggers[__( 'WooCommerce', 'gamipress' )] = array(
        'wc_purchases' => __( 'Recount purchases', 'gamipress' ),
        'wc_reviews' => __( 'Recount reviews', 'gamipress' ),
    );

    return $recountable_activity_triggers;

}
add_filter( 'gamipress_recountable_activity_triggers', 'gamipress_wc_recountable_activity_triggers' );

/**
 * Recount purchases
 *
 * @since 1.0.4
 *
 * @param array $response
 *
 * @return array $response
 */
function gamipress_wc_activity_recount_purchases( $response, $loop, $limit, $offset ) {

    global $wpdb;

    // Get all stored users count
    $users_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users}" ) );

    // On first loop send an informational text
    if( $loop === 0 && $users_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count );

        // Return early to inform
        return $response;
    }

    // Get all stored users
    $users = $wpdb->get_results( "SELECT {$wpdb->users}.ID FROM {$wpdb->users} LIMIT {$offset}, {$limit}" );

    foreach( $users as $user ) {
        // Get all completed orders
        $orders = wc_get_orders( array(
            'limit'    => -1,
            'status'   => 'completed',
            'customer' => $user->ID,
            'return' => 'ids',
        ) );

        foreach( $orders as $order_id ) {
            // Trigger new purchase action for each payment
            gamipress_wc_new_purchase( $order_id );
        }

    }

    $recounted_users = $limit * ( $loop + 1 );

    // Check remaining users
    if( $recounted_users < $users_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining users to finish recount', 'gamipress' ), ( $users_count - $recounted_users ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_wc_purchases', 'gamipress_wc_activity_recount_purchases', 10, 4 );

/**
 * Recount reviews
 *
 * @since 1.0.4
 *
 * @param array $response
 *
 * @return array $response
 */
function gamipress_wc_activity_recount_reviews( $response, $loop, $limit, $offset ) {

    global $wpdb;

    // Get all stored users count
    $users_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users}" ) );

    // On first loop send an informational text
    if( $loop === 0 && $users_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count );

        // Return early to inform
        return $response;
    }

    // Get all stored users
    $users = $wpdb->get_results( "SELECT {$wpdb->users}.ID FROM {$wpdb->users} LIMIT {$offset}, {$limit}" );

    foreach( $users as $user ) {
        // Get all user approved reviews
        $comments = $wpdb->get_results( $wpdb->prepare(
            "
            SELECT *
            FROM $wpdb->comments AS c
            WHERE c.user_id = %s
		       AND c.comment_approved = '1'
		       AND c.comment_type = 'review'
            ",
            $user->ID
        ) );

        foreach( $comments as $comment ) {
            // Trigger review actions
            do_action( 'gamipress_wc_specific_new_review', (int) $comment->ID, (int) $comment->user_id, $comment->comment_post_ID, $comment );
            do_action( 'gamipress_wc_new_review', (int) $comment->ID, (int) $comment->user_id, $comment->comment_post_ID, $comment );
        }
    }

    $recounted_users = $limit * ( $loop + 1 );

    // Check remaining users
    if( $recounted_users < $users_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining users to finish recount', 'gamipress' ), ( $users_count - $recounted_users ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_wc_reviews', 'gamipress_wc_activity_recount_reviews', 10, 4 );