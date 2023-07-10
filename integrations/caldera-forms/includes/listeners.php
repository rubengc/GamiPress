<?php
/**
 * Listeners
 *
 * @package GamiPress\Caldera_Forms\Listeners
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
 * @param $referrer
 * @param $process_id
 * @param $entry_id
 */
function gamipress_cf_submission_listener( $form, $referrer, $process_id, $entry_id ) {

    // Login is required
    if ( ! is_user_logged_in() ) return;

    $user_id = get_current_user_id();
    $form_id = $form['ID']; // Caldera Forms ID can be strings

    // Trigger event for submit a new form
    do_action( 'gamipress_cf_new_form_submission', $form_id, $user_id );

    // Trigger event for submit a specific form
    do_action( 'gamipress_cf_specific_new_form_submission', $form_id, $user_id );

}
add_action( 'caldera_forms_submit_post_process', 'gamipress_cf_submission_listener', 10, 4 );

/**
 * Field submission listener
 *
 * @since 1.0.0
 *
 * @param $form
 * @param $referrer
 * @param $process_id
 * @param $entry_id
 */
function gamipress_cf_field_submission_listener( $form, $referrer, $process_id, $entry_id ) {

    // Login is required
    if ( ! is_user_logged_in() ) return;

    $user_id = get_current_user_id();
    $form_id = $form['ID']; // Caldera Forms ID can be strings
    $form = Caldera_Forms_Forms::get_form( $form_id );
    $fields = $form['fields'];

    // Loop all fields to trigger events per field value
    foreach ( $fields as $field_id => $field ) {

        $field_name = $field_id;
        $field_value = Caldera_Forms::get_field_data( $field_id, $form, $entry_id );

        /**
         * Excluded fields event by filter
         *
         * @since 1.0.4
         *
         * @param bool      $exclude        Whatever to exclude or not, by default false
         * @param string    $field_name     Field name
         * @param mixed     $field_value    Field value
         * @param array     $field          Field setup array
         */
        if( apply_filters( 'gamipress_caldera_forms_exclude_field', false, $field_name, $field_value, $field ) )
            continue;

        // Trigger event for submit a specific field value
        do_action( 'gamipress_cf_field_value_submission', $form_id, $user_id, $field_name, $field_value );

        // Trigger event for submit a specific field value of a specific form
        do_action( 'gamipress_cf_specific_field_value_submission', $form_id, $user_id, $field_name, $field_value );
    }

}
add_action( 'caldera_forms_submit_post_process', 'gamipress_cf_field_submission_listener', 10, 4 );

