<?php
/**
 * Listeners
 *
 * @package GamiPress\WS_Form\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Form submission listener
 *
 * @since 1.0.0
 *
 * @param object $ws_form_submit
 */
function gamipress_ws_form_submission_listener( $ws_form_submit ) {

    // Login is required
    if ( ! is_user_logged_in() )  {
        return;
    }

    $user_id = absint( $ws_form_submit->user_id );
    $form_id = absint( $ws_form_submit->form_id );

    // Trigger event for submit a new form
    do_action( 'gamipress_ws_form_new_form_submission', $form_id, $user_id );

    // Trigger event for submit a specific form
    do_action( 'gamipress_ws_form_specific_new_form_submission', $form_id, $user_id );

}
add_action( 'wsf_submit_post_complete', 'gamipress_ws_form_submission_listener' );

/**
 * Field submission listener
 *
 * @since 1.0.0
 *
 * @param object $ws_form_submit
 */
function gamipress_ws_form_field_submission_listener( $ws_form_submit ) {

    // Login is required
    if ( ! is_user_logged_in() ) return;

    $user_id = absint( $ws_form_submit->user_id );
    $form_id = absint( $ws_form_submit->form_id );

    // Get all fields from form
    $fields = wsf_form_get_fields( $ws_form_submit->form_object );

    // Loop all fields to trigger events per field value
    foreach ( $fields as $field ) {

        // Get field type
        if( ! isset( $field->type ) ) {
            continue;
        }
        $field_type = $field->type;

        // Check field type for submit_save
        if( ! isset( $field_types[$field_type] ) ) {
            continue;
        }
        $field_type_config = $field_types[$field_type];
        $submit_save = isset( $field_type_config['submit_save'] ) ? $field_type_config['submit_save'] : false;

        // Excluded fields
        if( ! $submit_save ) {
            continue;
        }

        $field_name = $field->id;
        $field_value = wsf_submit_get_value( $ws_form_submit, WS_FORM_FIELD_PREFIX . $field_name );

        /**
         * Excluded fields event by filter
         *
         * @since 1.1.0
         *
         * @param bool      $exclude        Whatever to exclude or not, by default false
         * @param string    $field_name     Field name
         * @param mixed     $field_value    Field value
         * @param array     $field          Field setup array
         * @param int       $form_id        The form ID
         * @param int       $user_id        The user ID
         */
        if( apply_filters( 'gamipress_ws_form_exclude_field', false, $field_name, $field_value, $field, $form_id, $user_id ) ) {
            continue;
        }

        // Trigger event for submit a specific field value
        do_action( 'gamipress_ws_form_field_value_submission', $form_id, $user_id, $field_name, $field_value );

        // Trigger event for submit a specific field value of a specific form
        do_action( 'gamipress_ws_form_specific_field_value_submission', $form_id, $user_id, $field_name, $field_value );
    }

}
add_action( 'wsf_submit_post_complete', 'gamipress_ws_form_field_submission_listener' );

