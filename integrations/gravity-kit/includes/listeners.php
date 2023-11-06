<?php
/**
 * Listeners
 *
 * @package GamiPress\Gravity_Kit\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Entry approved listener
 *
 * @since 1.0.0
 *
 * @param int $entry_id ID of entry
 */
function gamipress_gravity_kit_entry_approved_listener( $entry_id ) {

    // Login is required
    if ( ! is_user_logged_in() )  {
        return;
    }

    $user_id = gamipress_gravity_kit_get_entry_user ( $entry_id );

    $form_id = gamipress_gravity_kit_get_entry_form( $entry_id );

    // Trigger event for entry approved in any form
    do_action( 'gamipress_gravity_kit_entry_approved_any_form', $form_id, $user_id, $entry_id );

    // Trigger event for entry approved in specific form
    do_action( 'gamipress_gravity_kit_entry_approved_specific_form', $form_id, $user_id, $entry_id );

}
add_action( 'gravityview/approve_entries/approved', 'gamipress_gravity_kit_entry_approved_listener' );

/**
 * Entry disapproved listener
 *
 * @since 1.0.0
 *
 * @param int $entry_id ID of entry
 */
function gamipress_gravity_kit_entry_disapproved_listener( $entry_id ) {

    // Login is required
    if ( ! is_user_logged_in() )  {
        return;
    }

    $user_id = gamipress_gravity_kit_get_entry_user ( $entry_id );

    $form_id = gamipress_gravity_kit_get_entry_form( $entry_id );

    // Trigger event for entry disapproved in any form
    do_action( 'gamipress_gravity_kit_entry_disapproved_any_form', $form_id, $user_id, $entry_id );

    // Trigger event for entry disapproved in specific form
    do_action( 'gamipress_gravity_kit_entry_disapproved_specific_form', $form_id, $user_id, $entry_id );

}
add_action( 'gravityview/approve_entries/disapproved', 'gamipress_gravity_kit_entry_disapproved_listener' );
