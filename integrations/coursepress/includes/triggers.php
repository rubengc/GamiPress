<?php
/**
 * Triggers
 *
 * @package GamiPress\CoursePress\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register CoursePress specific triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_coursepress_activity_triggers( $triggers ) {

    $triggers[__( 'CoursePress', 'gamipress' )] = array(

        // Modules
        'gamipress_coursepress_complete_module'                 => __( 'Complete a module', 'gamipress' ),
        'gamipress_coursepress_complete_specific_module'        => __( 'Complete a specific module', 'gamipress' ),
        'gamipress_coursepress_complete_module_specific_unit'   => __( 'Complete a module of a specific unit', 'gamipress' ),
        'gamipress_coursepress_complete_module_specific_course' => __( 'Complete a module of a specific course', 'gamipress' ),

        // Units
        'gamipress_coursepress_complete_unit'                   => __( 'Complete a unit', 'gamipress' ),
        'gamipress_coursepress_complete_specific_unit'          => __( 'Complete a specific unit', 'gamipress' ),
        'gamipress_coursepress_complete_unit_specific_course'   => __( 'Complete a unit of a specific course', 'gamipress' ),

        // Courses
        'gamipress_coursepress_complete_course'                 => __( 'Complete a course', 'gamipress' ),
        'gamipress_coursepress_complete_specific_course'        => __( 'Complete a specific course', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_coursepress_activity_triggers' );

/**
 * Register CoursePress specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_coursepress_specific_activity_triggers( $specific_activity_triggers ) {

    // Modules
    $specific_activity_triggers['gamipress_coursepress_complete_specific_module'] = array( 'module' );
    $specific_activity_triggers['gamipress_coursepress_complete_module_specific_unit'] = array( 'unit' );
    $specific_activity_triggers['gamipress_coursepress_complete_module_specific_course'] = array( 'course' );

    // Units
    $specific_activity_triggers['gamipress_coursepress_complete_specific_unit'] = array( 'unit' );
    $specific_activity_triggers['gamipress_coursepress_complete_unit_specific_course'] = array( 'course' );

    // Courses
    $specific_activity_triggers['gamipress_coursepress_complete_specific_course'] = array( 'course' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_coursepress_specific_activity_triggers' );

/**
 * Register CoursePress specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_coursepress_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Modules
    $specific_activity_trigger_labels['gamipress_coursepress_complete_specific_module']         = __( 'Complete the module %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_coursepress_complete_module_specific_unit']    = __( 'Complete a module of the unit %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_coursepress_complete_module_specific_course']  = __( 'Complete a module of the course %s', 'gamipress' );

    // Units
    $specific_activity_trigger_labels['gamipress_coursepress_complete_specific_unit']           = __( 'Complete the unit %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_coursepress_complete_unit_specific_course']    = __( 'Complete a unit of the course %s', 'gamipress' );

    // Courses
    $specific_activity_trigger_labels['gamipress_coursepress_complete_specific_course']         = __( 'Complete the course %s', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_coursepress_specific_activity_trigger_label' );

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
function gamipress_coursepress_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Modules
        case 'gamipress_coursepress_complete_module':
        case 'gamipress_coursepress_complete_specific_module':
        case 'gamipress_coursepress_complete_module_specific_unit':
        case 'gamipress_coursepress_complete_module_specific_course':
        // Units
        case 'gamipress_coursepress_complete_unit':
        case 'gamipress_coursepress_complete_specific_unit':
        case 'gamipress_coursepress_complete_unit_specific_course':
        // Courses
        case 'gamipress_coursepress_complete_course':
        case 'gamipress_coursepress_complete_specific_course':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_coursepress_trigger_get_user_id', 10, 3 );

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
function gamipress_coursepress_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_coursepress_complete_specific_module':
        case 'gamipress_coursepress_complete_specific_unit':
        case 'gamipress_coursepress_complete_specific_course':
            $specific_id = $args[0];
            break;
        case 'gamipress_coursepress_complete_module_specific_unit':
        case 'gamipress_coursepress_complete_unit_specific_course':
            $specific_id = $args[2];
            break;
        case 'gamipress_coursepress_complete_module_specific_course':
            $specific_id = $args[3];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_coursepress_specific_trigger_get_id', 10, 3 );

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
function gamipress_coursepress_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {

        // Modules
        case 'gamipress_coursepress_complete_module':
        case 'gamipress_coursepress_complete_specific_module':
        case 'gamipress_coursepress_complete_module_specific_unit':
        case 'gamipress_coursepress_complete_module_specific_course':
            // Add the module, unit and course IDs
            $log_meta['module_id'] = $args[0];
            $log_meta['unit_id'] = $args[2];
            $log_meta['course_id'] = $args[3];
            break;

        // Units
        case 'gamipress_coursepress_complete_unit':
        case 'gamipress_coursepress_complete_specific_unit':
        case 'gamipress_coursepress_complete_unit_specific_course':
            // Add the unit and course IDs
            $log_meta['unit_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            break;

        // Courses
        case 'gamipress_coursepress_complete_course':
        case 'gamipress_coursepress_complete_specific_course':
            // Add the course ID
            $log_meta['course_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_coursepress_log_event_trigger_meta_data', 10, 5 );