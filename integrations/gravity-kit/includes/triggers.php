<?php
/**
 * Triggers
 *
 * @package GamiPress\Gravity_Kit\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin specific triggers
 *
 * @since 1.0.0
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_gravity_kit_activity_triggers( $triggers ) {

    $triggers[__( 'Gravity Kit', 'gamipress' )] = array(
        'gamipress_gravity_kit_entry_approved_any_form'         => __( 'Entry approved from any form', 'gamipress' ),
        'gamipress_gravity_kit_entry_approved_specific_form'    => __( 'Entry approved from a specific form', 'gamipress' ),
        'gamipress_gravity_kit_entry_disapproved_any_form'      => __( 'Entry disapproved from any form', 'gamipress' ),
        'gamipress_gravity_kit_entry_disapproved_specific_form' => __( 'Entry disapproved from a specific form', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_gravity_kit_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_gravity_kit_specific_activity_triggers( $specific_activity_triggers ) {

    $specific_activity_triggers['gamipress_gravity_kit_entry_approved_specific_form'] = array( 'gravity_form' );
    $specific_activity_triggers['gamipress_gravity_kit_entry_disapproved_specific_form'] = array( 'gravity_form' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_gravity_kit_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_gravity_kit_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_gravity_kit_entry_approved_specific_form'] = __( 'Approved entry from %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_gravity_kit_entry_disapproved_specific_form'] = __( 'Disapproved entry from %s', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_gravity_kit_specific_activity_trigger_label' );

/**
 * Get plugin specific activity trigger post title
 *
 * @since  1.0.6
 *
 * @param  string   $post_title
 * @param  integer  $specific_id
 * @param  string   $trigger_type
 * @param  string   $site_id
 * @return string
 */
function gamipress_gravity_kit_specific_activity_trigger_post_title( $post_title, $specific_id, $trigger_type, $site_id ) {

    global $wpdb;

    switch( $trigger_type ) {
        case 'gamipress_gravity_kit_entry_approved_specific_form':
        case 'gamipress_gravity_kit_entry_disapproved_specific_form':
            if( absint( $specific_id ) !== 0 ) {

                if( gamipress_is_network_wide_active() && $site_id !== get_current_blog_id() ) {
                    // Switch to site
                    switch_to_blog($site_id);

                    $post_title = $site_forms = $wpdb->get_var( $wpdb->prepare(
                        "SELECT p.title FROM {$wpdb->prefix}gf_form AS p WHERE  p.id = %s",
                        "$specific_id"
                    ) );

                    // Restore current site
                    restore_current_blog();
                } else {
                    $post_title = $site_forms = $wpdb->get_var( $wpdb->prepare(
                        "SELECT p.title FROM {$wpdb->prefix}gf_form AS p WHERE  p.id = %s",
                        "$specific_id"
                    ) );
                }


            }
            break;
    }

    return $post_title;

}
add_filter( 'gamipress_specific_activity_trigger_post_title', 'gamipress_gravity_kit_specific_activity_trigger_post_title', 10, 4 );

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
function gamipress_gravity_kit_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_gravity_kit_entry_approved_any_form':
        case 'gamipress_gravity_kit_entry_approved_specific_form':
        case 'gamipress_gravity_kit_entry_disapproved_any_form':
        case 'gamipress_gravity_kit_entry_disapproved_specific_form':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_gravity_kit_trigger_get_user_id', 10, 3);


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
function gamipress_gravity_kit_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_gravity_kit_entry_approved_specific_form':
        case 'gamipress_gravity_kit_entry_disapproved_specific_form':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_gravity_kit_specific_trigger_get_id', 10, 3 );

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.1
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_gravity_kit_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_gravity_kit_entry_approved_any_form':
        case 'gamipress_gravity_kit_entry_approved_specific_form':
        case 'gamipress_gravity_kit_entry_disapproved_any_form':
        case 'gamipress_gravity_kit_entry_disapproved_specific_form':
            // Add the form ID
            $log_meta['form_id'] = $args[0];
            $log_meta['entry_id'] = $args[2];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_gravity_kit_log_event_trigger_meta_data', 10, 5 );
