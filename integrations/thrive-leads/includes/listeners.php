<?php
/**
 * Listeners
 *
 * @package GamiPress\Thrive_Leads\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Form submission listener
 *
 * @since 1.0.0
 *
 * @param $post
 */
function gamipress_thrive_leads_submission_listener( $post ) {

    // Login is required
    if ( ! is_user_logged_in() ) return;

    $user_id = get_current_user_id();
    $form_id = $post['thrive_leads']['tl_data']['_key'];

    // Trigger event for submit a new form
    do_action( 'gamipress_thrive_leads_new_form_submission', $form_id, $user_id );

    // Trigger event for submit a specific form
    do_action( 'gamipress_thrive_leads_specific_new_form_submission', $form_id, $user_id );

}
add_action( 'tcb_api_form_submit', 'gamipress_thrive_leads_submission_listener', 10, 1 );
