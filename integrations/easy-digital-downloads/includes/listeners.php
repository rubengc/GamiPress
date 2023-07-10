<?php
/**
 * Listeners
 *
 * @package GamiPress\Easy_Digital_Downloads\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Purchase listener
 *
 * @since 1.0.0
 *
 * @param int $payment_id
 */
function gamipress_edd_new_purchase( $payment_id ) {

    $payment = edd_get_payment( $payment_id );

    // Bail if payment object not exists
    if( ! $payment ) return;

    $user_id = $payment->user_id;

    // Trigger new purchase
    do_action( 'gamipress_edd_new_purchase', $payment_id, $user_id );

    $cart_details = $payment->cart_details;

    // Bail if cart is not well setup
    if ( ! is_array( $cart_details ) ) return;

    // On purchase, trigger events on each download purchased
    foreach ( $cart_details as $index => $item ) {

        // Setup vars
        $download_id = $item['id'];
        $download = get_post( $download_id );
        $quantity = isset( $item['quantity'] ) ? absint( $item['quantity'] ) : 1;

        // Trigger events same times as item quantity
        for ( $i = 0; $i < $quantity; $i++ ) {

            // Trigger new download sale to award the vendor
            do_action( 'gamipress_edd_new_sale', $download_id, $download->post_author, $payment_id, $payment );

            // Trigger new download purchase
            do_action( 'gamipress_edd_new_download_purchase', $download_id, $user_id, $payment_id, $quantity );

            // Trigger specific download purchase
            do_action( 'gamipress_edd_specific_download_purchase', $download_id, $user_id, $payment_id, $quantity );

            if( $item['item_price'] > 0 ) {
                // Trigger new paid download purchase
                do_action( 'gamipress_edd_new_paid_download_purchase', $download_id, $user_id, $payment_id, $quantity );
            } else {
                // Trigger new free download purchase
                do_action( 'gamipress_edd_new_free_download_purchase', $download_id, $user_id, $payment_id, $quantity );
            }

            if( edd_has_variable_prices( $download_id ) &&
                isset( $item['item_number'] ) && is_array( $item['item_number'] )
                && isset( $item['item_number']['options'] ) && is_array( $item['item_number']['options'] )
                && isset( $item['item_number']['options']['price_id'] ) ) {

                $price_id = absint( $item['item_number']['options']['price_id'] );

                // Trigger specific download variation purchase
                do_action( 'gamipress_edd_download_variation_purchase', $download_id, $user_id, $price_id, $payment_id, $quantity );
            }

            // Get an array of categories IDs attached to the download
            $categories = gamipress_edd_get_download_term_ids( $download_id, 'download_category' );

            if( count( $categories ) ) {

                foreach( $categories as $category_id ) {
                    // Trigger specific download category purchase (trigger 1 event per category)
                    do_action( 'gamipress_edd_download_category_purchase', $download_id, $user_id, $category_id, $payment_id, $quantity );
                }

            }

            // Get an array of tags IDs attached to the download
            $tags = gamipress_edd_get_download_term_ids( $download_id, 'download_tag' );

            if( count( $tags ) ) {

                foreach( $tags as $tag_id ) {
                    // Trigger specific download tag purchase (trigger 1 event per tag)
                    do_action( 'gamipress_edd_download_tag_purchase', $download_id, $user_id, $tag_id, $payment_id, $quantity );
                }

            }

        }

    }

}
// TODO: edd_complete_purchase happens before init action so GamiPress is not setup yet, to solve this, action has been switched to edd_after_payment_actions
//add_action( 'edd_complete_purchase', 'gamipress_edd_new_purchase' );
add_action( 'edd_after_payment_actions', 'gamipress_edd_new_purchase' );

/**
 * Refund listener
 *
 * @since 1.1.5
 *
 * @param $payment
 */
function gamipress_edd_purchase_refund( $payment ) {

    $payment = edd_get_payment( $payment );

    // Bail if payment object not exists
    if( ! $payment ) return;

    $payment_id = $payment->ID;
    $user_id = $payment->user_id;

    // Trigger purchase refund
    do_action( 'gamipress_edd_purchase_refund', $payment_id, $user_id );

    $cart_details = $payment->cart_details;

    // Bail if cart is not well setup
    if ( ! is_array( $cart_details ) ) return;

    // On refund, trigger events on each download purchased
    foreach ( $cart_details as $index => $item ) {

        // Setup vars
        $download_id = $item['id'];
        $download = get_post( $download_id );
        $quantity = isset( $item['quantity'] ) ? absint( $item['quantity'] ) : 1;

        // Trigger events same times as item quantity
        for ( $i = 0; $i < $quantity; $i++ ) {

            // Trigger download refund to award the vendor
            do_action( 'gamipress_edd_user_download_refund', $download_id, $download->post_author, $payment_id, $payment );

            // Trigger download refund
            do_action( 'gamipress_edd_download_refund', $download_id, $user_id, $payment_id, $quantity );

            // Trigger specific download refund
            do_action( 'gamipress_edd_specific_download_refund', $download_id, $user_id, $payment_id, $quantity );

            if( edd_has_variable_prices( $download_id ) &&
                isset( $item['item_number'] ) && is_array( $item['item_number'] )
                && isset( $item['item_number']['options'] ) && is_array( $item['item_number']['options'] )
                && isset( $item['item_number']['options']['price_id'] ) ) {

                $price_id = absint( $item['item_number']['options']['price_id'] );

                // Trigger specific download variation refund
                do_action( 'gamipress_edd_download_variation_refund', $download_id, $user_id, $price_id, $payment_id, $quantity );
            }

            // Get an array of categories IDs attached to the download
            $categories = gamipress_edd_get_download_term_ids( $download_id, 'download_category' );

            if( count( $categories ) ) {

                foreach( $categories as $category_id ) {
                    // Trigger specific download category refund (trigger 1 event per category)
                    do_action( 'gamipress_edd_download_category_refund', $download_id, $user_id, $category_id, $payment_id, $quantity );
                }

            }

            // Get an array of tags IDs attached to the download
            $tags = gamipress_edd_get_download_term_ids( $download_id, 'download_tag' );

            if( count( $tags ) ) {

                foreach( $tags as $tag_id ) {
                    // Trigger specific download tag refund (trigger 1 event per tag)
                    do_action( 'gamipress_edd_download_tag_refund', $download_id, $user_id, $tag_id, $payment_id, $quantity );
                }

            }

        }

    }

}
add_action( 'edd_post_refund_payment', 'gamipress_edd_purchase_refund' );

/**
 * [EDD FES] Approve download listener
 *
 * @since 1.0.0
 *
 * @param int $download_id
 */
function gamipress_edd_approve_download( $download_id = 0 ) {

    $download = get_post( $download_id );

    if( $download ) {
        // Trigger approve download
        do_action( 'gamipress_edd_approve_download', $download_id, $download->post_author, $download );
    }

}
add_action( 'fes_approve_download_admin', 'gamipress_edd_approve_download', 10 );

/**
 * [EDD Wish Lists] Add to wish list listener
 *
 * @since 1.0.0
 *
 * @param int $list_id
 * @param int $download_id
 * @param array $options
 */
function gamipress_edd_add_to_wish_list( $list_id, $download_id, $options ) {

    $post = get_post( $list_id );

    // Trigger add download to wish list
    do_action( 'gamipress_edd_add_to_wish_list', $download_id, $post->post_author, $list_id, $post );

    // Trigger add specific download to wish list
    do_action( 'gamipress_edd_add_specific_to_wish_list', $download_id, $post->post_author, $list_id, $post );

}
add_action( 'edd_wl_post_add_to_list', 'gamipress_edd_add_to_wish_list', 10, 3 );

// EDD Downloads Lists
function gamipress_edd_add_to_list( $list_id, $download_id, $options, $list ) {

    $post = get_post( $list_id );

    // Trigger add download to list
    do_action( 'gamipress_edd_' . $list . '_download', $download_id, $post->post_author, $list_id, $post );

    // Trigger add specific download to list
    do_action( 'gamipress_edd_' . $list . '_specific_download', $download_id, $post->post_author, $list_id, $post );

}
add_action( 'edd_downloads_list_add_to_list', 'gamipress_edd_add_to_list', 10, 4 );

// EDD Reviews
function gamipress_edd_approved_review_listener( $comment_ID, $comment ) {

    // Enforce array for both hooks (wp_insert_comment uses object, comment_{status}_comment uses array)
    if ( is_object( $comment ) ) {
        $comment = get_object_vars( $comment );
    }

    $comment_ID = absint( $comment_ID );
    $user_id = absint( $comment[ 'user_id' ] );
    $post_id = absint( $comment[ 'comment_post_ID' ] );
    $post = get_post( $post_id );

    // Check if comment is a review
    if ( $comment[ 'comment_type' ] != 'edd_review' ) {
        return;
    }

    // Check if comment is approved
    if ( 1 != (int) $comment[ 'comment_approved' ] ) {
        return;
    }

    // Trigger review actions
    do_action( 'gamipress_edd_new_review', $comment_ID, $user_id, $post_id, $comment );
    do_action( 'gamipress_edd_specific_new_review', $comment_ID, $user_id, $post_id, $comment );

    if ( absint( $post->post_author ) !== 0 ) {
        // Trigger get review actions to product author
        do_action( 'gamipress_edd_get_review', $comment_ID, absint( $post->post_author ), $post_id, $user_id, $comment );
        do_action( 'gamipress_edd_get_specific_review', $comment_ID, absint( $post->post_author ), $post_id, $user_id, $comment );
    }

}
add_action( 'comment_approved_edd_review', 'gamipress_edd_approved_review_listener', 10, 2 );
add_action( 'wp_insert_comment', 'gamipress_edd_approved_review_listener', 10, 2 );

// EDD Download Pages
function gamipress_edd_approve_download_page( $download_page_id = 0 ) {

    $download_page = get_post( $download_page_id );

    if( $download_page ) {
        // Trigger approve download page
        do_action( 'gamipress_edd_approve_edd_download_page', $download_page_id, $download_page->post_author, $download_page );
    }

}
add_action( 'edd_download_pages_fes_approve_download_page_admin', 'gamipress_edd_approve_download_page', 10 );

// EDD Social Discounts
function gamipress_edd_share_download( $return ) {

    // Is share is valid, then trigger share download event
    if( $return['msg'] === 'valid' ) {
        // Trigger share download
        do_action( 'gamipress_edd_share_download', $return['product_id'], get_current_user_id() );

        // Trigger share specific download
        do_action( 'gamipress_edd_share_specific_download', $return['product_id'], get_current_user_id() );
    }

    return $return;

}
add_filter( 'edd_social_discounts_ajax_return', 'gamipress_edd_share_download', 10 );

/**
 * Trigger listener
 *
 * @since 1.0.0
 *
 * @param int $purchase_value   The lifetime value
 * @param int $value            The download purchase value
 * @param int $id               The customer ID
 */
function gamipress_edd_lifetime_value( $purchase_value, $value, $id ) {

    // Bail if no user
    if ( $id === 0 ){
        return;
    }

    // Trigger lifetime value
    do_action( 'gamipress_edd_lifetime_value', $purchase_value, $id, $value );

}
add_action( 'edd_customer_post_increase_value', 'gamipress_edd_lifetime_value', 10, 3 );
add_action( 'edd_customer_post_decrease_value', 'gamipress_edd_lifetime_value', 10, 3 );