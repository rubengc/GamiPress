<?php
/**
 * Listeners
 *
 * @package GamiPress\Charitable\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Donation
 *
 * @since 1.0.0
 *
 * @param int     $donation_id The donation ID.
 * @param WP_Post $post        Instance of `WP_Post`.
 */
function gamipress_charitable_make_donation_listener( $donation_id, $post ) {

    $donation = charitable_get_donation( $donation_id );
       
    $donor = $donation->get_donor_data();

    $user = get_user_by( 'email', $donor['email'] );

    // Bail if no user
    if ( empty( $user ) ) {
        return;
    }
    
    // Get campaigns.
    $campaigns = $donation->get_campaign_donations();
    
    // Bail no campaigns.
    if ( empty( $campaigns ) ) {
        return;
    }

    // Bail if not approved status
    if ( ! charitable_is_approved_status( get_post_status( $donation_id ) ) ) {
        return false;
    }

    $old_status = ! empty( $_POST['original_post_status'] ) ? $_POST['original_post_status'] : '';

    // Bail if same status
    if ( $old_status === $post->post_status ) {
        return;
    }

    // Submit testimonial
    do_action( 'gamipress_charitable_make_donation', $donation_id, $user->ID );

}
add_action( 'charitable_donation_save', 'gamipress_charitable_make_donation_listener', 10, 2 );
