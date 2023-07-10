<?php
/**
 * Listeners
 *
 * @package GamiPress\Contact_Form_7\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Form submission listener
 *
 * @since 1.0.0
 *
 * @param $form
 * @param $result
 */
function gamipress_wpcf7_submission_listener( $form, $result ) {

    // Check if form was correctly filled
    if( ! in_array( $result['status'], array( 'mail_sent', 'mail_failed' ) ) ) return;

    $form_id = ( version_compare( WPCF7_VERSION, '4.8', '<' ) ) ? $form->id : $form->id();
    $user_id = get_current_user_id();

    // Login is required
    if ( $user_id === 0 ) return;

    // On submit a new form, triggers gamipress_wpcf7_new_form_submission
    do_action( 'gamipress_wpcf7_new_form_submission', $form_id, $user_id );

    // On submit a new specific form, triggers gamipress_wpcf7_specific_new_form_submission
    do_action( 'gamipress_wpcf7_specific_new_form_submission', $form_id, $user_id );

}
add_action( 'wpcf7_submit', 'gamipress_wpcf7_submission_listener', 10, 2 );

/**
 * Field submission listener
 *
 * @since 1.0.0
 *
 * @param $form
 */
function gamipress_wpcf7_field_submission_listener( $form, $result ) {

    // Check if form was correctly filled
    if( ! in_array( $result['status'], array( 'mail_sent', 'mail_failed' ) ) ) return;

    $form_id = ( version_compare( WPCF7_VERSION, '4.8', '<' ) ) ? $form->id : $form->id();
    $user_id = get_current_user_id();

    // Login is required
    if ( $user_id === 0 ) return;

    // Check if form submission is correctly setup
    $submission = WPCF7_Submission::get_instance();
    if ( ! $submission ) return;

    // Check if posted data is correctly setup
    $posted_data = $submission->get_posted_data();
    if ( ! $posted_data ) return;

    $fields = $form->scan_form_tags();

    foreach ( (array) $fields as $field ) {

        if ( empty( $field->name ) ) continue;

        $field_name = $field->name;
        $field_value = ( isset( $posted_data[$field->name] ) ? $posted_data[$field->name] : '' );

        /**
         * Excluded fields event by filter
         *
         * @since 1.1.0
         *
         * @param bool      $exclude        Whatever to exclude or not, by default false
         * @param string    $field_name     Field name
         * @param mixed     $field_value    Field value
         * @param array     $field          Field setup array
         */
        if( apply_filters( 'gamipress_contact_forms_7_exclude_field', false, $field_name, $field_value, $field ) )
            continue;

        // Trigger event for submit a specific field value
        do_action( 'gamipress_wpcf7_field_value_submission', $form_id, $user_id, $field_name, $field_value );

        // Trigger event for submit a specific field value of a specific form
        do_action( 'gamipress_wpcf7_specific_field_value_submission', $form_id, $user_id, $field_name, $field_value );
    }

}
add_action( 'wpcf7_submit', 'gamipress_wpcf7_field_submission_listener', 10, 2 );

