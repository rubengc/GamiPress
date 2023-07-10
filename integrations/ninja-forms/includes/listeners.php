<?php
/**
 * Listeners
 *
 * @package GamiPress\Ninja_Forms\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Form submission listener
 *
 * @since 1.0.0
 *
 * @param array $data
 */
function gamipress_nf_form_submission_listener( $data ) {

    $form_id = $data['form_id'];
    $user_id = get_current_user_id();

    // Login is required
    if ( $user_id === 0 ) return;

    // Trigger event for submit a new form
    do_action( 'gamipress_nf_new_form_submission', $form_id, $user_id );

    // Trigger event for submit a specific form
    do_action( 'gamipress_nf_specific_new_form_submission', $form_id, $user_id );

}
add_action( 'ninja_forms_after_submission', 'gamipress_nf_form_submission_listener' );

/**
 * Field submission listener
 *
 * @since 1.0.0
 *
 * @param array $data
 */
function gamipress_nf_field_submission_listener( $data ) {

    $form_id = $data['form_id'];
    $user_id = get_current_user_id();

    // Login is required
    if ( $user_id === 0 ) return;

    $fields = $data['fields'];

    // Loop all fields to trigger events per field value
    foreach ( $fields as $field ) {

        // Excluded fields
        if( in_array( $field['type'], array( 'captcha', 'section', 'submit' ) ) )
            continue;

        $field_name = $field['key'];
        $field_value = $field['value'];

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
        if( apply_filters( 'gamipress_ninja_forms_exclude_field', false, $field_name, $field_value, $field ) )
            continue;

        // Trigger event for submit a specific field value
        do_action( 'gamipress_nf_field_value_submission', $form_id, $user_id, $field_name, $field_value );

        // Trigger event for submit a specific field value of a specific form
        do_action( 'gamipress_nf_specific_field_value_submission', $form_id, $user_id, $field_name, $field_value );
    }

}
add_action( 'ninja_forms_after_submission', 'gamipress_nf_field_submission_listener' );

