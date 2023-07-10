<?php
/**
 * Listeners
 *
 * @package GamiPress\Divi\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Form submission listener
 *
 * @since 1.0.0
 *
 * @param array $fields_values
 * @param bool  $et_contact_error
 * @param array $contact_form_info
 */
function gamipress_divi_submission_listener( $fields_values, $et_contact_error, $contact_form_info ) {

    $user_id = get_current_user_id();

    // Login is required
    if ( $user_id === 0 ) {
        return;
    }

    // Bail if submission has an error
    if ( $et_contact_error === true ) {
        return;
    }

    // Bail if form does not have an ID
    if ( ! isset( $contact_form_info['contact_form_unique_id'] ) ) {
        return;
    }

    $form_id = $contact_form_info['contact_form_unique_id'];
    $post_id = $contact_form_info['post_id']; // Post where form is located

    // Trigger event for submit a new form
    do_action( 'gamipress_divi_new_form_submission', $form_id, $user_id );

    // Trigger event for submit a specific form
    do_action( 'gamipress_divi_specific_new_form_submission', $form_id, $user_id );

}
add_action( 'et_pb_contact_form_submit', 'gamipress_divi_submission_listener', 999, 3 );

/**
 * Field submission listener
 *
 * @since 1.0.0
 *
 * @param array $fields_values
 * @param bool  $et_contact_error
 * @param array $contact_form_info
 */
function gamipress_divi_field_submission_listener( $fields_values, $et_contact_error, $contact_form_info ) {

    $user_id = get_current_user_id();

    // Login is required
    if ( $user_id === 0 ) {
        return;
    }

    // Bail if submission has an error
    if ( $et_contact_error === true ) {
        return;
    }

    // Bail if form does not have an ID
    if ( ! isset( $contact_form_info['contact_form_unique_id'] ) ) {
        return;
    }

    $form_id = $contact_form_info['contact_form_unique_id'];
    $post_id = $contact_form_info['post_id']; // Post where form is located

    // Loop all fields to trigger events per field value
    foreach ( $fields_values as $field_id => $field ) {

        $field_name = $field_id;
        $field_value = $field['raw_value'];

        /**
         * Excluded fields event by filter
         *
         * @since 1.0.5
         *
         * @param bool      $exclude        Whatever to exclude or not, by default false
         * @param string    $field_name     Field name
         * @param mixed     $field_value    Field value
         * @param array     $field          Field setup array
         */
        if( apply_filters( 'gamipress_divi_exclude_field', false, $field_name, $field_value, $field ) )
            continue;

        // Trigger event for submit a specific field value
        do_action( 'gamipress_divi_field_value_submission', $form_id, $user_id, $field_name, $field_value );

        // Trigger event for submit a specific field value of a specific form
        do_action( 'gamipress_divi_specific_field_value_submission', $form_id, $user_id, $field_name, $field_value );
    }

}
add_action( 'et_pb_contact_form_submit', 'gamipress_divi_field_submission_listener', 999, 3 );

