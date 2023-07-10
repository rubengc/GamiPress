<?php
/**
 * Listeners
 *
 * @package GamiPress\Gravity_Forms\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Form submission listener
 *
 * @since 1.0.0
 *
 * @param $lead
 * @param $form
 */
function gamipress_gf_submission_listener( $lead, $form ) {

    // Login is required
    if ( ! is_user_logged_in() )  {
        return;
    }

    $user_id = absint( $lead['created_by'] );
    $form_id = absint( $lead['form_id'] );

    // Trigger event for submit a new form
    do_action( 'gamipress_gf_new_form_submission', $form_id, $user_id );

    // Trigger event for submit a specific form
    do_action( 'gamipress_gf_specific_new_form_submission', $form_id, $user_id );

}
add_action( 'gform_after_submission', 'gamipress_gf_submission_listener', 10, 2 );

/**
 * Field submission listener
 *
 * @since 1.0.9
 *
 * @param $lead
 * @param $form
 */
function gamipress_gf_field_submission_listener( $lead, $form ) {

    // Login is required
    if ( ! is_user_logged_in() ) return;

    $user_id = absint( $lead['created_by'] );
    $form_id = absint( $lead['form_id'] );
    $fields = $form['fields'];

    // Loop all fields to trigger events per field value
    foreach ( $fields as $field ) {

        // Excluded fields
        if( in_array( $field->type, array( 'captcha', 'section' ) ) ) {
            continue;
        }

        $field_name = $field->id;
        $field_value = GFFormsModel::get_lead_field_value( $lead, $field );

        // Turn serialized values into array
        if ( $field->type === 'list' ) {

            // On list fields is required to unserialize
            $field_value = maybe_unserialize( $field_value );

        } else if ( $field->type === 'name' ) {

            $new_value = array();

            foreach( $field_value as $value ) {
                if( ! empty( $value ) ) {
                    $new_value[] = $value;
                }
            }

            $field_value = $new_value;

        } else if ( $field->type === 'multiselect' ) {

            // Multiselect value is a json
            $field_value = json_decode( $field_value );

        }  else if ( $field->type === 'checkbox' ) {

            // On checkboxes, values are stored on {field_id}.{choice_number}
            $field_value = array();
            $choice_number = 1;

            foreach ( $field->choices as $choice ) {
                $value = ( isset( $lead[$field->id . '.' . $choice_number] ) ? $lead[$field->id . '.' . $choice_number] : '' );

                // Not checked options are empty
                if( ! empty( $value ) ) {
                    $field_value[] = $value;
                }

                $choice_number++;
            }

        }

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
        if( apply_filters( 'gamipress_gravity_forms_exclude_field', false, $field_name, $field_value, $field, $form_id, $user_id ) ) {
            continue;
        }

        // Trigger event for submit a specific field value
        do_action( 'gamipress_gf_field_value_submission', $form_id, $user_id, $field_name, $field_value );

        // Trigger event for submit a specific field value of a specific form
        do_action( 'gamipress_gf_specific_field_value_submission', $form_id, $user_id, $field_name, $field_value );
    }

}
add_action( 'gform_after_submission', 'gamipress_gf_field_submission_listener', 10, 2 );

