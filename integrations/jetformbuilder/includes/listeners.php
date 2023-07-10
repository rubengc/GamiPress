<?php
/**
 * Listeners
 *
 * @package GamiPress\JetFormBuilder\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Form submission listener
 *
 * @since 1.0.0
 *
 * @param Jet_Form_Builder\Form_Handler $form_handler
 * @param boolean $is_success
 */
function gamipress_jetformbuilder_submission_listener( $form_handler, $is_success ) {

    // Bail if form is not sent
    if ( $form_handler->response_args['status'] !== 'success'  ) {
        return;
    }

    $user_id = get_current_user_id();

    // Login is required
    if ( $user_id === 0 ) return;

    // To get the form ID
    $form_id = $form_handler->form_id;

    // Bail if form is not sent
    if ( empty( $is_success ) ) {
        return;
    }

    // On submit a new form, triggers gamipress_jetformbuilder_new_form_submission
    do_action( 'gamipress_jetformbuilder_new_form_submission', $form_id, $user_id );

    // On submit a new specific form, triggers gamipress_jetformbuilder_specific_new_form_submission
    do_action( 'gamipress_jetformbuilder_specific_new_form_submission', $form_id, $user_id );

}
add_action( 'jet-form-builder/form-handler/after-send', 'gamipress_jetformbuilder_submission_listener', 10, 2 );

/**
 * Field submission listener
 *
 * @since 1.0.0
 *
 * @param Jet_Form_Builder\Form_Handler $form_handler
 * @param boolean $is_success
 */
function gamipress_jetformbuilder_field_submission_listener( $form_handler, $is_success ) {

    // Bail if form is not sent
    if ( $form_handler->response_args['status'] !== 'success'  ) {
        return;
    }
    
    $user_id = get_current_user_id();
    
    // Login is required
    if ( $user_id === 0 ) {
        return;
    }
    
    $form_id = $form_handler->form_id;
    
    // Bail if form is not sent
    if ( empty( $is_success ) ) {
        return;
    }
    
    $form_fields = gamipress_jetformbuilder_get_form_fields_values( $form_handler );

    foreach ( $form_fields as $field_name => $field_value ) {    

        // Trigger event for submit a specific field value
        do_action( 'gamipress_jetformbuilder_field_value_submission', $form_id, $user_id, $field_name, $field_value );

        // Trigger event for submit a specific field value of a specific form
        do_action( 'gamipress_jetformbuilder_specific_field_value_submission', $form_id, $user_id, $field_name, $field_value );
    }

}
add_action( 'jet-form-builder/form-handler/after-send', 'gamipress_jetformbuilder_field_submission_listener', 10, 2 );

