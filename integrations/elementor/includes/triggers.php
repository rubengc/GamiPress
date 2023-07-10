<?php
/**
 * Triggers
 *
 * @package GamiPress\Elementor_Forms\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin specific triggers
 *
 * @since  1.0.0
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_elementor_forms_activity_triggers( $triggers ) {

    $triggers[__( 'Elementor Forms', 'gamipress-elementor-forms-integration' )] = array(
        'gamipress_elementor_forms_new_form_submission'                => __( 'Successful submit a form', 'gamipress-elementor-forms-integration' ),
        'gamipress_elementor_forms_specific_new_form_submission'       => __( 'Successful submit a specific form', 'gamipress-elementor-forms-integration' ),
        'gamipress_elementor_forms_field_value_submission'             => __( 'Submit a specific field value', 'gamipress-elementor-forms-integration' ),
        'gamipress_elementor_forms_specific_field_value_submission'    => __( 'Submit a specific field value on a specific form', 'gamipress-elementor-forms-integration' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_elementor_forms_activity_triggers' );

/**
 * Build custom activity trigger label
 *
 * @since  1.0.0
 *
 * @param string    $title
 * @param integer   $requirement_id
 * @param array     $requirement
 *
 * @return string
 */
function gamipress_elementor_forms_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $form_name = ( isset( $requirement['elementor_forms_form_name'] ) ) ? $requirement['elementor_forms_form_name'] : '';
    $field_name = ( isset( $requirement['elementor_forms_field_name'] ) ) ? $requirement['elementor_forms_field_name'] : '';
    $field_value = ( isset( $requirement['elementor_forms_field_value'] ) ) ? $requirement['elementor_forms_field_value'] : '';

    switch( $requirement['trigger_type'] ) {
        // Specific form
        // Specific field value on a specific form
        case 'gamipress_elementor_forms_specific_new_form_submission':
            return sprintf( __( 'Submit %s form', 'gamipress-elementor-forms-integration' ), $form_name );
            break;
        // Specific field value
        case 'gamipress_elementor_forms_field_value_submission':
            return sprintf( __( 'Submit a form setting field "%s" to "%s" value', 'gamipress-elementor-forms-integration' ), $field_name, $field_value );
            break;
        // Specific field value on a specific form
        case 'gamipress_elementor_forms_specific_field_value_submission':
            return sprintf( __( 'Submit %s form setting field "%s" to "%s" value', 'gamipress-elementor-forms-integration' ), $form_name, $field_name, $field_value );
            break;
    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_elementor_forms_activity_trigger_label', 10, 3 );

/**
 * Get user for a given trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $user_id user ID to override.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 * @return integer          User ID.
 */
function gamipress_elementor_forms_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_elementor_forms_new_form_submission':
        case 'gamipress_elementor_forms_specific_new_form_submission':
        case 'gamipress_elementor_forms_field_value_submission':
        case 'gamipress_elementor_forms_specific_field_value_submission':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_elementor_forms_trigger_get_user_id', 10, 3);

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.0
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_elementor_forms_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_elementor_forms_new_form_submission':
        case 'gamipress_elementor_forms_specific_new_form_submission':
            // Add the form name
            $log_meta['form_name'] = $args[0];
            break;
        case 'gamipress_elementor_forms_field_value_submission':
        case 'gamipress_elementor_forms_specific_field_value_submission':
            // Add the form name, field name and value
            $log_meta['form_name'] = $args[0];
            $log_meta['field_name'] = $args[2];
            $log_meta['field_value'] = $args[3];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_elementor_forms_log_event_trigger_meta_data', 10, 5 );

/**
 * Override the meta data to filter the logs count
 *
 * @since   1.0.0
 *
 * @param  array    $log_meta       The meta data to filter the logs count
 * @param  int      $user_id        The given user's ID
 * @param  string   $trigger        The given trigger we're checking
 * @param  int      $since 	        The since timestamp where retrieve the logs
 * @param  int      $site_id        The desired Site ID to check
 * @param  array    $args           The triggered args or requirement object
 *
 * @return array                    The meta data to filter the logs count
 */
function gamipress_elementor_forms_get_user_trigger_count_log_meta( $log_meta, $user_id, $trigger, $since, $site_id, $args ) {

    switch( $trigger ) {
        case 'gamipress_elementor_forms_field_value_submission':
        case 'gamipress_elementor_forms_specific_field_value_submission':
            if( isset( $args[2] ) && isset( $args[3] ) ) {
                // Add the field name and value
                $log_meta['field_name'] = $args[2];
                $log_meta['field_value'] = $args[3];
            }

            // $args could be a requirement object
            if( isset( $args['elementor_forms_field_name'] ) && isset( $args['elementor_forms_field_value'] ) ) {
                // Add the field name and value
                $log_meta['field_name'] = $args['elementor_forms_field_name'];
                $log_meta['field_value'] = $args['elementor_forms_field_value'];
            }
            break;
    }

    return $log_meta;

}
add_filter( 'gamipress_get_user_trigger_count_log_meta', 'gamipress_elementor_forms_get_user_trigger_count_log_meta', 10, 6 );

/**
 * Override the count log for array fields
 *
 * @since   1.0.0
 *
 * @param  int      $trigger_count  The total number of times a user has triggered the trigger
 * @param  int      $user_id        The given user's ID
 * @param  string   $trigger        The given trigger we're checking
 * @param  int      $since 	        The since timestamp where retrieve the logs
 * @param  int      $site_id        The desired Site ID to check
 * @param  array    $args           The triggered args or requirement object
 *
 * @return int                      The total number of times a user has triggered the trigger
 */
function gamipress_elementor_forms_get_user_trigger_count( $trigger_count, $user_id, $trigger, $since, $site_id, $args ) {

    switch( $trigger ) {
        case 'gamipress_elementor_forms_field_value_submission':
        case 'gamipress_elementor_forms_specific_field_value_submission':

            if( $trigger_count === 0 ) {

                $log_meta = array(
                    'type'          => 'event_trigger',
                    'trigger_type'  => $trigger,
                );

                $log_meta = apply_filters( 'gamipress_get_user_trigger_count_log_meta', $log_meta, $user_id, $trigger, $since, $site_id, $args );

                if( isset( $log_meta['field_value'] ) ) {
                    $log_meta[] = array(
                        'key' => 'field_value',
                        'value' => '%"' . $log_meta['field_value'] . '"%',
                        'compare' => 'LIKE',
                    );

                    unset( $log_meta['field_value'] );

                    $trigger_count = absint( gamipress_get_user_log_count( absint( $user_id ), $log_meta, $since ) );
                }
            }
            break;
    }

    return $trigger_count;

}
add_filter( 'gamipress_get_user_trigger_count', 'gamipress_elementor_forms_get_user_trigger_count', 10, 6 );

/**
 * Extra data fields
 *
 * @since 1.0.5
 *
 * @param array     $fields
 * @param int       $log_id
 * @param string    $type
 *
 * @return array
 */
function gamipress_elementor_forms_log_extra_data_fields( $fields, $log_id, $type ) {

    $prefix = '_gamipress_';

    $log = ct_get_object( $log_id );
    $trigger = $log->trigger_type;

    if( $type !== 'event_trigger' ) {
        return $fields;
    }

    switch( $trigger ) {
        case 'gamipress_elementor_forms_field_value_submission':
        case 'gamipress_elementor_forms_specific_field_value_submission':

            $field_value = ct_get_object_meta( $log_id, $prefix . 'field_value', true );

            $fields[] = array(
                'name'      => __( 'Form name', 'gamipress-elementor-forms-integration' ),
                'desc'      => __( 'The submitted form name.', 'gamipress-elementor-forms-integration' ),
                'id'        => $prefix . 'form_name',
                'type'      => 'text',
            );
            $fields[] = array(
                'name'      => __( 'Field name', 'gamipress-elementor-forms-integration' ),
                'desc'      => __( 'Field name attached to this log.', 'gamipress-elementor-forms-integration' ),
                'id'        => $prefix . 'field_name',
                'type'      => 'text',
            );
            $fields[] = array(
                'name'      => __( 'Field value', 'gamipress-elementor-forms-integration' ),
                'desc'      => __( 'Field value attached to this log.', 'gamipress-elementor-forms-integration' ),
                'id'        => $prefix . 'field_value',
                'type'      => ( is_array( $field_value ) ? 'advanced_select' : 'text' ),
                'multiple'  => ( is_array( $field_value ) && count( $field_value ) > 1 ? true : false ),
                'options'   => ( is_array( $field_value ) ? $field_value : array() ),
            );
            break;
    }

    return $fields;

}
add_filter( 'gamipress_log_extra_data_fields', 'gamipress_elementor_forms_log_extra_data_fields', 10, 3 );