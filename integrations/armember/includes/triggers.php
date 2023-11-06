<?php
/**
 * Triggers
 *
 * @package GamiPress\ARMember\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register ARMember specific triggers
 *
 * @since  1.0.0
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_armember_activity_triggers( $triggers ) {

    $triggers[__( 'ARMember', 'gamipress' )] = array(
        'gamipress_armember_add_membership'             => __( 'Add to membership plan', 'gamipress' ),
        'gamipress_armember_add_specific_membership'    => __( 'Add to specific membership plan', 'gamipress' ),
        'gamipress_armember_cancel_membership'          => __( 'Cancel any membership plan', 'gamipress' ),
        'gamipress_armember_cancel_specific_membership' => __( 'Cancel a specific membership plan', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_armember_activity_triggers' );

/**
 * Register specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_armember_specific_activity_triggers( $specific_activity_triggers ) {
    
    $specific_activity_triggers['gamipress_armember_add_specific_membership'] = array( 'armember_membership' );
    $specific_activity_triggers['gamipress_armember_cancel_specific_membership'] = array( 'armember_membership' );
    
    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_armember_specific_activity_triggers' );

/**
 * Register specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_armember_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_armember_add_specific_membership'] = __( 'Add to %s membership plan', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_armember_cancel_specific_membership'] = __( 'Cancel %s membership plan', 'gamipress' );
    
    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_armember_specific_activity_trigger_label' );

/**
 * Get plugin specific activity trigger post title
 *
 * @since  1.0.0
 *
 * @param  string   $post_title
 * @param  integer  $specific_id
 * @param  string   $trigger_type
 *
 * @return string
 */
function gamipress_armember_specific_activity_trigger_post_title( $post_title, $specific_id, $trigger_type ) {

    switch( $trigger_type ) {
        case 'gamipress_armember_add_specific_membership':
        case 'gamipress_armember_cancel_specific_membership':
            if( absint( $specific_id ) !== 0 ) {
                // Get the membership plan title
                $plan_title = gamipress_armember_get_plan_title( $specific_id );

                $post_title = $plan_title;
            }
            break;
    }

    return $post_title;

}
add_filter( 'gamipress_specific_activity_trigger_post_title', 'gamipress_armember_specific_activity_trigger_post_title', 10, 3 );

/**
 * Get plugin specific activity trigger permalink
 *
 * @since  1.0.0
 *
 * @param  string   $permalink
 * @param  integer  $specific_id
 * @param  string   $trigger_type
 * @param  integer  $site_id
 *
 * @return string
 */
function gamipress_armember_specific_activity_trigger_permalink( $permalink, $specific_id, $trigger_type, $site_id ) {

    switch( $trigger_type ) {
        case 'gamipress_armember_add_specific_membership':
        case 'gamipress_armember_cancel_specific_membership':
            $permalink = '';
            break;
    }

    return $permalink;

}
add_filter( 'gamipress_specific_activity_trigger_permalink', 'gamipress_armember_specific_activity_trigger_permalink', 10, 4 );


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
function gamipress_armember_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_armember_add_membership':
        case 'gamipress_armember_add_specific_membership':
        case 'gamipress_armember_cancel_membership':
        case 'gamipress_armember_cancel_specific_membership':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_armember_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $specific_id Specific ID to override.
 * @param  string  $trigger     Trigger name.
 * @param  array   $args        Passed trigger args.
 *
 * @return integer              Specific ID.
 */
function gamipress_armember_specific_trigger_get_id( $specific_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_armember_add_specific_membership':
        case 'gamipress_armember_cancel_specific_membership':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;

}

add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_armember_specific_trigger_get_id', 10, 3 );

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
function gamipress_armember_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_armember_add_membership':
        case 'gamipress_armember_add_specific_membership':
        case 'gamipress_armember_cancel_membership':
        case 'gamipress_armember_cancel_specific_membership':
            // Add the membership ID
            $log_meta['membership_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_armember_log_event_trigger_meta_data', 10, 5 );