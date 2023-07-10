<?php
/**
 * Listeners
 *
 * @package GamiPress\WPAdverts\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Publish advert listener
 *
 * @param string    $new_status The new post status
 * @param string    $old_status The old post status
 * @param WP_Post   $post       The post
 */
function gamipress_wpadverts_publish_advert( $new_status, $old_status, $post ) {

    // Bail if not is an advert
    if( $post->post_type !== 'advert' ) {
        return;
    }

    // Bail if post has been already published
    if( $old_status === 'publish' ) {
        return;
    }

    // Bail if post is not published
    if( $new_status !== 'publish' ) {
        return;
    }

    // Trigger publish an advert
    do_action( 'gamipress_wpadverts_new_advert', $post->ID, $post->post_author, $post );

    $price = (float) get_post_meta( $post->ID, 'adverts_price', true );

    if( $price === 0 ) {
        // Trigger publish a free advert
        do_action( 'gamipress_wpadverts_new_free_advert', $post->ID, $post->post_author, $post );
    } else {
        // Trigger publish a paid advert
        do_action( 'gamipress_wpadverts_new_paid_advert', $post->ID, $post->post_author, $post );
    }

}
add_action( 'transition_post_status', 'gamipress_wpadverts_publish_advert', 10, 3 );

/**
 * Send a message listener
 *
 * @param int           $post_id
 * @param Adverts_Form  $form
 */
function gamipress_wpadverts_send_message( $post_id, $form ) {

    $post = get_post( $post_id );

    // Bail if post not exists
    if( ! $post ) {
        return;
    }

    // Bail if not is an advert
    if( $post->post_type !== 'advert' ) {
        return;
    }

    // Trigger receive a message
    do_action( 'gamipress_wpadverts_receive_message', $post->ID, $post->post_author, $post );

    // Trigger receive a message from a specific advert
    do_action( 'gamipress_wpadverts_specific_receive_message', $post->ID, $post->post_author, $post );

    // First try to get the user from the message email
    $email = $form->get_value( 'message_email' );
    $user = get_user_by_email( $email );
    $user_id = 0;

    if( $user ) {
        $user_id = $user->ID;
    }

    if( $user_id === 0 ) {
        $user_id = get_current_user_id();
    }

    // Bail if can't find the user ID
    if( $user_id === 0 ) {
        return;
    }

    // Trigger send a message
    do_action( 'gamipress_wpadverts_send_message', $post->ID, $user_id, $post );

    // Trigger send a message to a specific advert author
    do_action( 'gamipress_wpadverts_specific_send_message', $post->ID, $user_id, $post );

}
add_action( 'adext_contact_form_send', 'gamipress_wpadverts_send_message', 10, 2 );

/**
 * Payment completed
 *
 * @param WP_Post  $payment
 */
function gamipress_wpadverts_payment_completed( $payment ) {

    $advert_id = get_post_meta( $payment->ID, '_adverts_object_id', true );
    $user_id = absint( get_post_meta( $payment->ID, '_adverts_user_id', true ) );

    // Bail if can't find the user ID
    if( $user_id === 0 ) {
        return;
    }

    $type = get_post_meta( $payment->ID, '_adverts_payment_type', true );

    // Bail if hasn't a type
    if( ! $type ) {
        return;
    }

    if( $type === 'adverts-pricing' ) {
        // Trigger pay an advert
        do_action( 'gamipress_wpadverts_pay_advert', $advert_id, $user_id, $payment->ID );

        // Trigger pay a specific advert
        do_action( 'gamipress_wpadverts_pay_specific_advert', $advert_id, $user_id, $payment->ID );
    } else if( $type === 'adverts-renewal' ) {
        // Trigger renew an advert
        do_action( 'gamipress_wpadverts_renew_advert', $advert_id, $user_id, $payment->ID );

        // Trigger renew a specific advert
        do_action( 'gamipress_wpadverts_renew_specific_advert', $advert_id, $user_id, $payment->ID );
    }

}
add_action( 'adverts_payment_completed', 'gamipress_wpadverts_payment_completed', 10, 1 );