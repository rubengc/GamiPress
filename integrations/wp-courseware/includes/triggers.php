<?php
/**
 * Triggers
 *
 * @package GamiPress\WP_Courseware\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register WP Courseware specific triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_wpcw_activity_triggers( $triggers ) {


    $triggers[__( 'WP Courseware', 'gamipress' )] = array(
        'gamipress_wpcw_complete_unit' => __( 'Complete a unit', 'gamipress' ),
        'gamipress_wpcw_complete_specific_unit' => __( 'Complete a specific unit', 'gamipress' ),

        'gamipress_wpcw_complete_module' => __( 'Complete a module', 'gamipress' ),
        'gamipress_wpcw_complete_specific_module' => __( 'Complete a specific module', 'gamipress' ),

        'gamipress_wpcw_complete_course' => __( 'Complete a course', 'gamipress' ),
        'gamipress_wpcw_complete_specific_course' => __( 'Complete a specific course', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_wpcw_activity_triggers' );

/**
 * Register WP Courseware specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_wpcw_specific_activity_triggers( $specific_activity_triggers ) {

    $specific_activity_triggers['gamipress_wpcw_complete_specific_unit'] = array( 'course_unit' );
    $specific_activity_triggers['gamipress_wpcw_complete_specific_module'] = array( 'wpcw_modules' );
    $specific_activity_triggers['gamipress_wpcw_complete_specific_course'] = array( 'wpcw_courses' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_wpcw_specific_activity_triggers' );

/**
 * Build custom activity trigger label
 *
 * @param string    $title
 * @param integer   $requirement_id
 * @param array     $requirement
 *
 * @return string
 */
function gamipress_wpcw_activity_trigger_label( $title, $requirement_id, $requirement ) {

    global $wpdb;

    $achievement_post_id = absint( $requirement['achievement_post'] );

    switch( $requirement['trigger_type'] ) {
// TODO: Deprecated, Units are now CPT posts with post_type as 'course_unit'
//        case 'gamipress_wpcw_complete_specific_unit':
//            $unit_title = $wpdb->get_var( $wpdb->prepare(
//                "SELECT unit_title FROM {$wpdb->prefix}wpcw_units WHERE unit_id = %d",
//                $achievement_post_id
//            ) );
//
//            return sprintf( __( 'Complete the unit %s', 'gamipress' ), $unit_title );
//            break;
        case 'gamipress_wpcw_complete_specific_module':
            $module_title = $wpdb->get_var( $wpdb->prepare(
                "SELECT module_title FROM {$wpdb->prefix}wpcw_modules WHERE module_id = %d",
                $achievement_post_id
            ) );

            return sprintf( __( 'Complete the module %s', 'gamipress' ), $module_title );
            break;
        case 'gamipress_wpcw_complete_specific_course':
            $course_title = $wpdb->get_var( $wpdb->prepare(
                "SELECT course_title FROM {$wpdb->prefix}wpcw_courses WHERE course_id = %d",
                $achievement_post_id
            ) );

            return sprintf( __( 'Complete the course %s', 'gamipress' ), $course_title );
            break;
    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_wpcw_activity_trigger_label', 10, 3 );

/**
 * Register WP Courseware specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_wpcw_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_wpcw_complete_specific_unit'] = __( 'Complete the unit %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wpcw_complete_specific_module'] = __( 'Complete the module %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wpcw_complete_specific_course'] = __( 'Complete the course %s', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_wpcw_specific_activity_trigger_label' );

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
function gamipress_wpcw_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_wpcw_complete_unit':
        case 'gamipress_wpcw_complete_specific_unit':
        case 'gamipress_wpcw_complete_module':
        case 'gamipress_wpcw_complete_specific_module':
        case 'gamipress_wpcw_complete_course':
        case 'gamipress_wpcw_complete_specific_course':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_wpcw_trigger_get_user_id', 10, 3 );

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
function gamipress_wpcw_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_wpcw_complete_specific_unit':
        case 'gamipress_wpcw_complete_specific_module':
        case 'gamipress_wpcw_complete_specific_course':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_wpcw_specific_trigger_get_id', 10, 3 );

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
function gamipress_wpcw_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_wpcw_complete_unit':
        case 'gamipress_wpcw_complete_specific_unit':
            // Add the quiz and course IDs
            $log_meta['unit_id'] = $args[0];
            $log_meta['module_id'] = $args[2];
            $log_meta['course_id'] = $args[3];
            break;
        case 'gamipress_wpcw_complete_module':
        case 'gamipress_wpcw_complete_specific_module':
            // Add the module IDs
            $log_meta['module_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            break;
        case 'gamipress_wpcw_complete_course':
        case 'gamipress_wpcw_complete_specific_course':
            // Add the course IDs
            $log_meta['course_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_wpcw_log_event_trigger_meta_data', 10, 5 );