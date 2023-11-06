<?php
/**
 * Triggers
 *
 * @package GamiPress\Thrive_Leads\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Thrive Leads specific triggers
 *
 * @since  1.0.0
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_thrive_leads_activity_triggers( $triggers ) {

    $triggers[__( 'Thrive Leads', 'gamipress' )] = array(
        'gamipress_thrive_leads_new_form_submission'             => __( 'Successful submit a form', 'gamipress' ),
        'gamipress_thrive_leads_specific_new_form_submission'    => __( 'Successful submit a specific form', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_thrive_leads_activity_triggers' );

/**
 * Register Thrive Leads specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_thrive_leads_specific_activity_triggers( $specific_activity_triggers ) {

    $specific_activity_triggers['gamipress_thrive_leads_specific_new_form_submission'] = array( 'thrive_form' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_thrive_leads_specific_activity_triggers' );

/**
 * Get Thrive Leads specific activity trigger post title
 *
 * @since  1.0.1
 *
 * @param  string   $post_title
 * @param  integer  $specific_id
 * @param  string   $trigger_type
 * @return string
 */
function gamipress_thrive_leads_specific_activity_trigger_post_title( $post_title, $specific_id, $trigger_type ) {

    switch( $trigger_type ) {
        case 'gamipress_thrive_leads_specific_new_form_submission':
            if( absint( $specific_id ) !== 0 ) {
                $form = gamipress_thrive_leads_get_form_title( $specific_id );

                $post_title = $form;
            }
            break;
    }

    return $post_title;

}
add_filter( 'gamipress_specific_activity_trigger_post_title', 'gamipress_thrive_leads_specific_activity_trigger_post_title', 10, 3 );

/**
 * Register Thrive Leads specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_thrive_leads_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_thrive_leads_specific_new_form_submission'] = __( 'Submit %s', 'gamipress' );   

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_thrive_leads_specific_activity_trigger_label' );

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
function gamipress_thrive_leads_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_thrive_leads_new_form_submission':
        case 'gamipress_thrive_leads_specific_new_form_submission':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_thrive_leads_trigger_get_user_id', 10, 3);


/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer  $specific_id Specific ID.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          Specific ID.
 */
function gamipress_thrive_leads_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_thrive_leads_specific_new_form_submission':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_thrive_leads_specific_trigger_get_id', 10, 3 );

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
function gamipress_thrive_leads_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_thrive_leads_new_form_submission':
        case 'gamipress_thrive_leads_specific_new_form_submission':
            // Add the form ID
            $log_meta['form_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_thrive_leads_log_event_trigger_meta_data', 10, 5 );
