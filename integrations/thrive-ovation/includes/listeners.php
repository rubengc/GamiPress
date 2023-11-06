<?php
/**
 * Listeners
 *
 * @package GamiPress\Thrive_Ovation\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * File gets downloaded
 *
 * @since 1.0.0
 *
 * @param  array  	$testimonial_details
 * @param  array  	$user_details
 */
function gamipress_thrive_ovation_submit_testimonial_listener( $testimonial_details, $user_details ) {

     // Bail if empty user details
     if ( empty( $user_details ) ) {
        return;
    }

    // Submit testimonial
    do_action( 'gamipress_thrive_ovation_submit_testimonial', $testimonial_details['testimonial_id'], $user_details->data->ID );

}
add_action( 'thrive_ovation_testimonial_submit', 'gamipress_thrive_ovation_submit_testimonial_listener', 10, 2 );
