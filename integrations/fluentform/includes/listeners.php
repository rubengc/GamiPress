<?php
/**
 * Listeners
 *
 * @package GamiPress\FluentForm\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Form submission listener
 *
 * @since 1.0.0
 *
 * @param int $submission_id
 * @param array $form_data
 * @param \FluentForm\App\Modules\Form\Form $form
 */
function gamipress_fluentform_submission_listener( $submission_id, $form_data, $form ) {

    $form_id = $form->id;
    $user_id = get_current_user_id();

    // Login is required
    if ( $user_id === 0 ) {
        return;
    }

    // Trigger event for submit a new form
    do_action( 'gamipress_fluentform_new_form_submission', $form_id, $user_id );

    // Trigger event for submit a specific form
    do_action( 'gamipress_fluentform_specific_new_form_submission', $form_id, $user_id );

}
add_action( 'fluentform/before_submission_confirmation', 'gamipress_fluentform_submission_listener', 10, 3 );

/**
 * Field submission listener
 *
 * @since 1.0.0
 *
 * @param int $submission_id
 * @param array $form_data
 * @param \FluentForm\App\Modules\Form\Form $form
 */
function gamipress_fluentform_field_submission_listener( $submission_id, $form_data, $form ) {

    $form_id = $form->id;
    $user_id = get_current_user_id();

    // Login is required
    if ( $user_id === 0 ) {
        return;
    }

    $fields = $form_data;

    // Loop all fields to trigger events per field value
    foreach ( $fields as $field_name => $field_value ) {

        // Used for hook
        $field = array( $field_name => $field_value );

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
        if( apply_filters( 'gamipress_fluentform_exclude_field', false, $field_name, $field_value, $field ) )
            continue;

        // Trigger event for submit a specific field value
        do_action( 'gamipress_fluentform_field_value_submission', $form_id, $user_id, $field_name, $field_value );

        // Trigger event for submit a specific field value of a specific form
        do_action( 'gamipress_fluentform_specific_field_value_submission', $form_id, $user_id, $field_name, $field_value );
    }

}
add_action( 'fluentform/before_submission_confirmation', 'gamipress_fluentform_field_submission_listener', 10, 3 );

