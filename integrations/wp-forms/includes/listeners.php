<?php
/**
 * Listeners
 *
 * @package GamiPress\WP_Forms\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Form submission listener
 *
 * @since 1.0.0
 *
 * @param $fields
 * @param $entry
 * @param $form_data
 * @param $entry_id
 */
function gamipress_wp_forms_submission_listener( $fields, $entry, $form_data, $entry_id ) {

    // Login is required
    if ( ! is_user_logged_in() ) return;

    $user_id = get_current_user_id();
    $form_id = absint( $form_data['id'] );

    // Trigger event for submit a new form
    do_action( 'gamipress_wp_forms_new_form_submission', $form_id, $user_id );

    // Trigger event for submit a specific form
    do_action( 'gamipress_wp_forms_specific_new_form_submission', $form_id, $user_id );

}
add_action( 'wpforms_process_complete', 'gamipress_wp_forms_submission_listener', 10, 4 );

/**
 * Field submission listener
 *
 * @since 1.0.0
 *
 * @param $fields
 * @param $entry
 * @param $form_data
 * @param $entry_id
 */
function gamipress_wp_forms_field_submission_listener( $fields, $entry, $form_data, $entry_id ) {

    // Login is required
    if ( ! is_user_logged_in() ) return;

    $user_id = get_current_user_id();
    $form_id = absint( $form_data['id'] );

    // Loop all fields to trigger events per field value
    foreach ( $fields as $field_id => $field ) {

        $field_name = $field['name'];
        $field_value = $field['value'];

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
        if( apply_filters( 'gamipress_wp_forms_exclude_field', false, $field_name, $field_value, $field ) )
            continue;

        // Trigger event for submit a specific field value
        do_action( 'gamipress_wp_forms_field_value_submission', $form_id, $user_id, $field_name, $field_value );

        // Trigger event for submit a specific field value of a specific form
        do_action( 'gamipress_wp_forms_specific_field_value_submission', $form_id, $user_id, $field_name, $field_value );
    }

}
add_action( 'wpforms_process_complete', 'gamipress_wp_forms_field_submission_listener', 10, 4 );

