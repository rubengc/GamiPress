<?php
/**
 * Listeners
 *
 * @package GamiPress\Formidable_Forms\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Form submission listener
 *
 * @since 1.0.0
 *
 * @param $conf_method
 * @param $form
 * @param $form_options
 * @param $entry_id
 * @param $extra_args
 */
function gamipress_frm_submission_listener( $conf_method, $form, $form_options, $entry_id, $extra_args ) {

    // Login is required
    if ( ! is_user_logged_in() ) return;

    $user_id = get_current_user_id();
    $form_id = absint( $form->id );

    // Trigger event for submit a new form
    do_action( 'gamipress_frm_new_form_submission', $form_id, $user_id );

    // Trigger event for submit a specific form
    do_action( 'gamipress_frm_specific_new_form_submission', $form_id, $user_id );

}
add_action( 'frm_success_action', 'gamipress_frm_submission_listener', 10, 5 );

/**
 * Field submission listener
 *
 * @since 1.0.3
 *
 * @param $conf_method
 * @param $form
 * @param $form_options
 * @param $entry_id
 * @param $extra_args
 */
function gamipress_frm_field_submission_listener( $conf_method, $form, $form_options, $entry_id, $extra_args ) {

    // Login is required
    if ( ! is_user_logged_in() ) return;

    $user_id = get_current_user_id();
    $form_id = absint( $form->id );
    $fields = FrmFieldsHelper::get_form_fields( $form->id );
    $entry_values = new FrmEntryValues( $entry_id );
    $field_values = $entry_values->get_field_values();

    // Loop all fields to trigger events per field value
    foreach ( $fields as $field ) {

        $field_name = $field->field_key;
        $field_value = ( isset( $field_values[$field->id] ) ? $field_values[$field->id]->get_saved_value() : '' );

        /**
         * Excluded fields event by filter
         *
         * @since 1.0.4
         *
         * @param bool      $exclude        Whatever to exclude or not, by default false
         * @param string    $field_name     Field name
         * @param mixed     $field_value    Field value
         * @param array     $field          Field setup array
         * @param int       $form_id        The form ID
         * @param int       $user_id        The user ID
         */
        if( apply_filters( 'gamipress_formidable_forms_exclude_field', false, $field_name, $field_value, $field, $form_id, $user_id ) )
            continue;

        // Trigger event for submit a specific field value
        do_action( 'gamipress_frm_field_value_submission', $form_id, $user_id, $field_name, $field_value );

        // Trigger event for submit a specific field value of a specific form
        do_action( 'gamipress_frm_specific_field_value_submission', $form_id, $user_id, $field_name, $field_value );
    }

}
add_action( 'frm_success_action', 'gamipress_frm_field_submission_listener', 10, 5 );
