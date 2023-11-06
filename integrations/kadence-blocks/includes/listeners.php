<?php
/**
 * Listeners
 *
 * @package GamiPress\Kadence_Blocks\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Form submission listener
 *
 * @since 1.0.0
 *
 * @param array $form_args
 * @param array $fields
 * @param string $form_id
 * @param int $post_id
 */
function gamipress_kadence_blocks_submission_listener( $form_args, $fields, $form_id, $post_id ) {

    // Login is required
    if ( ! is_user_logged_in() )  {
        return;
    }

    $user_id = get_current_user_id();

    // Trigger event for submit a new form
    do_action( 'gamipress_kadence_blocks_new_form_submission', $form_id, $user_id );

    // Trigger event for submit a specific form
    do_action( 'gamipress_kadence_blocks_specific_new_form_submission', $form_id, $user_id );

}
add_action( 'kadence_blocks_form_submission', 'gamipress_kadence_blocks_submission_listener', 10, 4 );

/**
 * Field submission listener
 *
 * @since 1.0.9
 *
 * @param array $form_args
 * @param array $fields
 * @param string $form_id
 * @param int $post_id
 */
function gamipress_kadence_blocks_field_submission_listener( $form_args, $fields, $form_id, $post_id ) {

    // Login is required
    if ( ! is_user_logged_in() ) return;

    $user_id = get_current_user_id();

    $form_fields = gamipress_kadence_blocks_get_form_fields_values( $fields );

    // Loop all fields to trigger events per field value
    foreach ( $form_fields as $field_name => $field_value ) {

        // Trigger event for submit a specific field value
        do_action( 'gamipress_kadence_blocks_field_value_submission', $form_id, $user_id, $field_name, $field_value );

        // Trigger event for submit a specific field value of a specific form
        do_action( 'gamipress_kadence_blocks_specific_field_value_submission', $form_id, $user_id, $field_name, $field_value );
    }

}
add_action( 'kadence_blocks_form_submission', 'gamipress_kadence_blocks_field_submission_listener', 10, 4 );

/**
 * Get form fields values
 *
 * @since 1.0.0
 *
 * @param array $fields
 *
 * @return array
 */
function gamipress_kadence_blocks_get_form_fields_values( $fields ) {

    $form_fields = array();

    // Loop all fields
    foreach ( $fields as $field_name => $field_value ) {
        
        if( is_array( $field_value ) ) {

            $field_name = $field_value['label'];
            $value = ( isset( $field_value['value'] ) ? $field_value['value'] : '' );

            $form_fields[$field_name] = $value;

        }
    }

    return $form_fields;

}
