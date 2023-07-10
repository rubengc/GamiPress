<?php
/**
 * Listeners
 *
 * @package GamiPress\HappyForms\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Form submission listener
 *
 * @since 1.0.0
 *
 * @param array $submission Submission data.
 * @param array $form   Current form data.
 */
function gamipress_happyforms_submission_listener( $submission, $form ) {

    // Login is required
    if ( ! is_user_logged_in() ) return;

    $user_id = get_current_user_id();
    $form_id = absint( $form['ID'] );

    // Trigger event for submit a new form
    do_action( 'gamipress_happyforms_new_form_submission', $form_id, $user_id );

    // Trigger event for submit a specific form
    do_action( 'gamipress_happyforms_specific_new_form_submission', $form_id, $user_id );

}
add_action( 'happyforms_submission_success', 'gamipress_happyforms_submission_listener', 10, 2 );

/**
 * Field submission listener
 *
 * @since 1.0.0
 *
 * @param array $submission Submission data.
 * @param array $form   Current form data.
 */
function gamipress_happyforms_field_submission_listener( $submission, $form ) {

    // Login is required
    if ( ! is_user_logged_in() ) return;

    $user_id = get_current_user_id();
    $form_id = absint( $form['ID'] );

    // Loop all fields to trigger events per field value
    foreach ( $submission as $field_name => $field_value ) {

        $field = array(
            'name' => $field_name,
            'value' => $field_value,
        );

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
        if( apply_filters( 'gamipress_happyforms_exclude_field', false, $field_name, $field_value, $field ) )
            continue;

        // Trigger event for submit a specific field value
        do_action( 'gamipress_happyforms_field_value_submission', $form_id, $user_id, $field_name, $field_value );

        // Trigger event for submit a specific field value of a specific form
        do_action( 'gamipress_happyforms_specific_field_value_submission', $form_id, $user_id, $field_name, $field_value );
    }

}
add_action( 'happyforms_submission_success', 'gamipress_happyforms_field_submission_listener', 10, 2 );

