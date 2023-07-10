<?php
/**
 * Listeners
 *
 * @package GamiPress\Elementor_Forms\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Form submission listener
 *
 * @since 1.0.0
 *
 * @param Form_Record $record
 * @param Ajax_Handler $handler
 */
function gamipress_elementor_forms_submission_listener( $record, $handler ) {

    $form_name = $record->get_form_settings( 'form_name' );
    $user_id = get_current_user_id();

    // Login is required
    if ( $user_id === 0 ) {
        return;
    }

    // Trigger event for submit a new form
    do_action( 'gamipress_elementor_forms_new_form_submission', $form_name, $user_id );

    // Trigger event for submit a specific form
    do_action( 'gamipress_elementor_forms_specific_new_form_submission', $form_name, $user_id );

}
add_action( 'elementor_pro/forms/new_record', 'gamipress_elementor_forms_submission_listener', 10, 2 );

/**
 * Field submission listener
 *
 * @since 1.0.0
 *
 * @param Form_Record $record
 * @param Ajax_Handler $handler
 */
function gamipress_elementor_forms_field_submission_listener( $record, $handler ) {

    $form_name = $record->get_form_settings( 'form_name' );
    $user_id = get_current_user_id();

    // Login is required
    if ( $user_id === 0 ) {
        return;
    }

    $fields = $record->get( 'fields' );

    // Loop all fields to trigger events per field value
    foreach ( $fields as $field_id => $field ) {

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
        if( apply_filters( 'gamipress_elementor_forms_exclude_field', false, $field_name, $field_value, $field ) )
            continue;

        // Trigger event for submit a specific field value
        do_action( 'gamipress_elementor_forms_field_value_submission', $form_name, $user_id, $field_name, $field_value );

        // Trigger event for submit a specific field value of a specific form
        do_action( 'gamipress_elementor_forms_specific_field_value_submission', $form_name, $user_id, $field_name, $field_value );
    }

}
add_action( 'elementor_pro/forms/new_record', 'gamipress_elementor_forms_field_submission_listener', 10, 2 );

