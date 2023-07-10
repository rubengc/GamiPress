<?php
/**
 * Recount Activity
 *
 * @package GamiPress\Easy_Digital_Downloads\Admin\Recount_Activity
 * @since 1.0.3
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add recountable options to the Recount Activity Tool
 *
 * @since 1.0.3
 *
 * @param array $recountable_activity_triggers
 *
 * @return array
 */
function gamipress_edd_recountable_activity_triggers( $recountable_activity_triggers ) {

    // Easy Digital Downloads
    $recountable_activity_triggers[__( 'Easy Digital Downloads', 'gamipress' )] = array(
        'edd_purchases' => __( 'Recount purchases', 'gamipress' ),
    );

    // EDD FES
    if( class_exists('EDD_Front_End_Submissions') ) {

        // Update purchases label with vendor sales
        $recountable_activity_triggers[__( 'Easy Digital Downloads', 'gamipress' )]['edd_purchases'] = __( 'Recount purchases and vendor sales', 'gamipress' );

        $recountable_activity_triggers[__( 'EDD - FrontEnd Submissions', 'gamipress' )] = array(
            'edd_approved_downloads' => __( 'Recount vendor approved downloads', 'gamipress' ),
        );
    }

    // EDD Wish Lists
    if( class_exists('EDD_Wish_Lists') ) {
        $recountable_activity_triggers[__( 'EDD - Wish Lists', 'gamipress' )] = array(
            'edd_wish_lists' => __( 'Recount wish lists', 'gamipress' ),
        );
    }

    // EDD Downloads Lists
    if( class_exists('EDD_Downloads_Lists') ) {

        // Update wish list label with downloads list
        $recountable_activity_triggers[__( 'EDD - Wish Lists', 'gamipress' )]['edd_wish_lists'] = __( 'Recount wish lists and download lists', 'gamipress' );
    }

    // EDD Reviews
    if( class_exists('EDD_Reviews') ) {
        $triggers[__( 'EDD - Reviews', 'gamipress' )] = array(
            'edd_reviews' => __( 'Recount reviews', 'gamipress' ),
        );
    }

    // EDD Download Pages
    if( class_exists('EDD_Download_Pages') ) {

        // EDD FrontEnd Submissions + EDD Download Pages
        if( class_exists('EDD_FES') ) {
            $recountable_activity_triggers[__( 'EDD - Download Pages', 'gamipress' )]['edd_approved_download_pages'] = __( 'Recount vendor approved download pages', 'gamipress' );
        }
    }

    // EDD Social Discounts
    if( class_exists('EDD_Social_Discounts') ) {
        // Not recountable
    }

    return $recountable_activity_triggers;

}
add_filter( 'gamipress_recountable_activity_triggers', 'gamipress_edd_recountable_activity_triggers' );

/**
 * Recount purchases
 *
 * @since 1.0.3
 *
 * @param array $response
 *
 * @return array $response
 */
function gamipress_edd_activity_recount_purchases( $response ) {

    global $wpdb;

    // Get all published payments
    $payments = $wpdb->get_results( $wpdb->prepare(
        "SELECT p.ID FROM $wpdb->posts AS p WHERE p.post_type = %s AND p.post_status = 'publish'",
        'edd_payment'
    ) );

    foreach( $payments as $payment ) {
        // Trigger new purchase action for each payment
        gamipress_edd_new_purchase( $payment->ID );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_edd_purchases', 'gamipress_edd_activity_recount_purchases' );

/**
 * Recount approved downloads
 *
 * @since 1.0.3
 *
 * @param array $response
 *
 * @return array $response
 */
function gamipress_edd_activity_recount_approved_downloads( $response ) {

    global $wpdb;

    // Get all published downloads
    $downloads = $wpdb->get_results( $wpdb->prepare(
        "SELECT p.ID FROM $wpdb->posts AS p WHERE p.post_type = %s AND p.post_status = 'publish'",
        'download'
    ) );

    foreach( $downloads as $download ) {
        // Trigger new approved download action for each download
        gamipress_edd_approve_download( $download->ID );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_edd_approved_downloads', 'gamipress_edd_activity_recount_approved_downloads' );

/**
 * Recount wish list
 *
 * @since 1.0.3
 *
 * @param array $response
 *
 * @return array $response
 */
function gamipress_edd_activity_recount_wish_lists( $response ) {

    global $wpdb;

    // Get all wish lists downloads
    $lists = $wpdb->get_results( $wpdb->prepare(
        "SELECT p.ID, p.post_author FROM $wpdb->posts AS p WHERE p.post_type = %s",
        'edd_wish_list'
    ) );

    foreach( $lists as $list ) {

        $list_items = get_post_meta( $list->ID, 'edd_wish_list', true );

        if( $list_items ) {
            foreach( $list_items as $item ) {

                // Trigger wish lists actions
                gamipress_edd_add_to_wish_list( $list->ID, $item['id'], array() );

                // If download lists is active
                if( class_exists('EDD_Downloads_Lists') ) {

                    foreach( edd_downloads_lists()->get_lists() as $list_type => $list_args ) {

                        $user_list = get_user_meta( $list->post_author, 'edd_downloads_lists_' . $list_type . '_id', true );

                        if( absint( $user_list ) === absint( $list->ID ) ) {
                            // Trigger downloads lists actions
                            gamipress_edd_add_to_list( $list->ID, $item['id'], array(), $list_type );
                        }
                    }


                }

            }
        }

    }

    return $response;

}
add_filter( 'gamipress_activity_recount_edd_wish_lists', 'gamipress_edd_activity_recount_wish_lists' );

/**
 * Recount reviews
 *
 * @since 1.0.3
 *
 * @param array $response
 *
 * @return array $response
 */
function gamipress_edd_activity_recount_reviews( $response ) {

    global $wpdb;

    // Get all stored users
    $users = $wpdb->get_results( "SELECT {$wpdb->users}.ID FROM {$wpdb->users}" );

    foreach( $users as $user ) {
        // Get all user approved reviews
        $comments = $wpdb->get_results( $wpdb->prepare(
            "
            SELECT *
            FROM $wpdb->comments AS c
            WHERE c.user_id = %s
		       AND c.comment_approved = '1'
		       AND c.comment_type = 'edd_review'
            ",
            $user->ID
        ) );

        foreach( $comments as $comment ) {
            // Trigger review actions
            do_action( 'gamipress_edd_specific_new_review', (int) $comment->ID, (int) $comment->user_id, $comment->comment_post_ID, $comment );
            do_action( 'gamipress_edd_new_review', (int) $comment->ID, (int) $comment->user_id, $comment->comment_post_ID, $comment );
        }
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_edd_reviews', 'gamipress_edd_activity_recount_reviews' );

/**
 * Recount approved download pages
 *
 * @since 1.0.3
 *
 * @param array $response
 *
 * @return array $response
 */
function gamipress_edd_activity_recount_approved_download_pages( $response ) {

    global $wpdb;

    // Get all published downloads
    $download_pages = $wpdb->get_results( $wpdb->prepare(
        "SELECT p.ID FROM $wpdb->posts AS p WHERE p.post_type = %s AND p.post_status = 'publish'",
        'edd_download_page'
    ) );

    foreach( $download_pages as $download_page ) {
        // Trigger new approved download page action for each download page
        gamipress_edd_approve_download_page( $download_page->ID );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_edd_approved_download_pages', 'gamipress_edd_activity_recount_approved_download_pages' );