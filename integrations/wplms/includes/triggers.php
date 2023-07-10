<?php
/**
 * Triggers
 *
 * @package GamiPress\WPLMS\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_wplms_activity_triggers( $triggers ) {

    $triggers[__( 'WPLMS', 'gamipress' )] = array(

        // Course
        'gamipress_wplms_subscribe_course'                      => __( 'Subscribe on a course', 'gamipress' ),
        'gamipress_wplms_subscribe_specific_course'             => __( 'Subscribe on a specific course', 'gamipress' ),
        'gamipress_wplms_start_course'                          => __( 'Start a course', 'gamipress' ),
        'gamipress_wplms_start_specific_course'                 => __( 'Start a specific course', 'gamipress' ),
        'gamipress_wplms_complete_course'                       => __( 'Complete a course', 'gamipress' ),
        'gamipress_wplms_complete_specific_course'              => __( 'Complete a specific course', 'gamipress' ),
        'gamipress_wplms_complete_course_minimum_mark'          => __( 'Complete a course with a minimum mark', 'gamipress' ),
        'gamipress_wplms_complete_specific_course_minimum_mark' => __( 'Complete a specific course with a minimum mark', 'gamipress' ),
        'gamipress_wplms_review_course'                         => __( 'Review a course', 'gamipress' ),
        'gamipress_wplms_review_specific_course'                => __( 'Review a specific course', 'gamipress' ),
        'gamipress_wplms_unsubscribe_course'                    => __( 'Unsubscribe from a course', 'gamipress' ),
        'gamipress_wplms_unsubscribe_specific_course'           => __( 'Unsubscribe from a specific course', 'gamipress' ),
        'gamipress_wplms_retake_course'                         => __( 'Retake a course', 'gamipress' ),
        'gamipress_wplms_retake_specific_course'                => __( 'Retake a specific course', 'gamipress' ),

        // Quiz
        'gamipress_wplms_start_quiz'                                => __( 'Start a quiz', 'gamipress' ),
        'gamipress_wplms_start_specific_quiz'                       => __( 'Start a specific quiz', 'gamipress' ),
        'gamipress_wplms_complete_quiz'                             => __( 'Complete a quiz', 'gamipress' ),
        'gamipress_wplms_complete_specific_quiz'                    => __( 'Complete a specific quiz', 'gamipress' ),
        'gamipress_wplms_complete_quiz_minimum_mark'                => __( 'Complete a quiz with a minimum mark', 'gamipress' ),
        'gamipress_wplms_complete_specific_quiz_minimum_mark'       => __( 'Complete a specific quiz with a minimum mark', 'gamipress' ),
        'gamipress_wplms_retake_quiz'                               => __( 'Retake a quiz', 'gamipress' ),
        'gamipress_wplms_retake_specific_quiz'                      => __( 'Retake a specific quiz', 'gamipress' ),

        // Assignment
        'gamipress_wplms_start_assignment'                          => __( 'Start an assignment', 'gamipress' ),
        'gamipress_wplms_start_specific_assignment'                 => __( 'Start a specific assignment', 'gamipress' ),
        'gamipress_wplms_complete_assignment'                       => __( 'Complete an assignment', 'gamipress' ),
        'gamipress_wplms_complete_specific_assignment'              => __( 'Complete a specific assignment', 'gamipress' ),
        'gamipress_wplms_complete_assignment_minimum_mark'          => __( 'Complete an assignment with a minimum mark', 'gamipress' ),
        'gamipress_wplms_complete_specific_assignment_minimum_mark' => __( 'Complete a specific assignment with a minimum mark', 'gamipress' ),

        // Unit
        'gamipress_wplms_complete_unit'                             => __( 'Complete an unit', 'gamipress' ),
        'gamipress_wplms_complete_specific_unit'                    => __( 'Complete a specific unit', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_wplms_activity_triggers' );

/**
 * Register specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_wplms_specific_activity_triggers( $specific_activity_triggers ) {

    // Course
    $specific_activity_triggers['gamipress_wplms_subscribe_specific_course'] = array( 'course' );
    $specific_activity_triggers['gamipress_wplms_start_specific_course'] = array( 'course' );
    $specific_activity_triggers['gamipress_wplms_complete_specific_course'] = array( 'course' );
    $specific_activity_triggers['gamipress_wplms_complete_specific_course_minimum_mark'] = array( 'course' );
    $specific_activity_triggers['gamipress_wplms_review_specific_course'] = array( 'course' );
    $specific_activity_triggers['gamipress_wplms_unsubscribe_specific_course'] = array( 'course' );
    $specific_activity_triggers['gamipress_wplms_retake_specific_course'] = array( 'course' );

    // Quiz
    $specific_activity_triggers['gamipress_wplms_start_specific_quiz'] = array( 'quiz' );
    $specific_activity_triggers['gamipress_wplms_complete_specific_quiz'] = array( 'quiz' );
    $specific_activity_triggers['gamipress_wplms_complete_specific_quiz_minimum_mark'] = array( 'quiz' );
    $specific_activity_triggers['gamipress_wplms_retake_specific_quiz'] = array( 'quiz' );

    // Assignment
    $specific_activity_triggers['gamipress_wplms_start_specific_assignment'] = array( 'assignment' );
    $specific_activity_triggers['gamipress_wplms_complete_specific_assignment'] = array( 'assignment' );
    $specific_activity_triggers['gamipress_wplms_complete_specific_assignment_minimum_mark'] = array( 'assignment' );

    // Unit
    $specific_activity_triggers['gamipress_wplms_complete_specific_unit'] = array( 'unit' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_wplms_specific_activity_triggers' );

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
function gamipress_wplms_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $minimum_score = ( isset( $requirement['wplms_score'] ) ) ? absint( $requirement['wplms_score'] ) : 0;

    switch( $requirement['trigger_type'] ) {
        // Course
        case 'gamipress_wplms_complete_course_minimum_mark':
            return sprintf( __( 'Completed a course with a minimum mark of %d', 'gamipress' ), $minimum_score );
            break;
        case 'gamipress_wplms_complete_specific_course_minimum_mark':
            $post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Complete the course %s with a minimum mark of %d', 'gamipress' ), get_the_title( $post_id ), $minimum_score );
            break;
        // Quiz
        case 'gamipress_wplms_complete_quiz_minimum_mark':
            return sprintf( __( 'Completed a quiz with a minimum mark of %d', 'gamipress' ), $minimum_score );
            break;
        case 'gamipress_wplms_complete_specific_quiz_minimum_mark':
            $post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Complete the quiz %s with a minimum mark of %d', 'gamipress' ), get_the_title( $post_id ), $minimum_score );
            break;
        // Assignment
        case 'gamipress_wplms_complete_assignment_minimum_mark':
            return sprintf( __( 'Completed an assignment with a minimum mark of %d', 'gamipress' ), $minimum_score );
            break;
        case 'gamipress_wplms_complete_specific_assignment_minimum_mark':
            $post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Complete the assignment %s with a minimum mark of %d', 'gamipress' ), get_the_title( $post_id ), $minimum_score );
            break;
    }

    return $title;

}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_wplms_activity_trigger_label', 10, 3 );

/**
 * Register specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 *
 * @return array
 */
function gamipress_wplms_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Course
    $specific_activity_trigger_labels['gamipress_wplms_subscribe_specific_course'] = __( 'Subscribe to the course %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wplms_start_specific_course'] = __( 'Start the course %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wplms_complete_specific_course'] = __( 'Complete the course %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wplms_review_specific_course'] = __( 'Review the course %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wplms_unsubscribe_specific_course'] = __( 'Unsubscribe from the course %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wplms_retake_specific_course'] = __( 'Retake the course %s', 'gamipress' );

    // Quiz
    $specific_activity_trigger_labels['gamipress_wplms_start_specific_quiz'] = __( 'Start the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wplms_complete_specific_quiz'] = __( 'Complete the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wplms_retake_specific_quiz'] = __( 'Retake the quiz %s', 'gamipress' );

    // Assignment
    $specific_activity_trigger_labels['gamipress_wplms_start_specific_assignment'] = __( 'Start the assignment %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wplms_complete_specific_assignment'] = __( 'Complete the assignment %s', 'gamipress' );

    // Unit
    $specific_activity_trigger_labels['gamipress_wplms_complete_specific_unit'] = __( 'Complete the unit %s', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_wplms_specific_activity_trigger_label' );

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
function gamipress_wplms_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Course
        case 'gamipress_wplms_subscribe_course':
        case 'gamipress_wplms_subscribe_specific_course':
        case 'gamipress_wplms_start_course':
        case 'gamipress_wplms_start_specific_course':
        case 'gamipress_wplms_complete_course':
        case 'gamipress_wplms_complete_specific_course':
        case 'gamipress_wplms_complete_course_minimum_mark':
        case 'gamipress_wplms_complete_specific_course_minimum_mark':
        case 'gamipress_wplms_review_course':
        case 'gamipress_wplms_review_specific_course':
        case 'gamipress_wplms_unsubscribe_course':
        case 'gamipress_wplms_unsubscribe_specific_course':
        case 'gamipress_wplms_retake_course':
        case 'gamipress_wplms_retake_specific_course':
        // Quiz
        case 'gamipress_wplms_start_quiz':
        case 'gamipress_wplms_start_specific_quiz':
        case 'gamipress_wplms_complete_quiz':
        case 'gamipress_wplms_complete_specific_quiz':
        case 'gamipress_wplms_complete_quiz_minimum_mark':
        case 'gamipress_wplms_complete_specific_quiz_minimum_mark':
        case 'gamipress_wplms_retake_quiz':
        case 'gamipress_wplms_retake_specific_quiz':
        // Assignment
        case 'gamipress_wplms_start_assignment':
        case 'gamipress_wplms_start_specific_assignment':
        case 'gamipress_wplms_complete_assignment':
        case 'gamipress_wplms_complete_specific_assignment':
        case 'gamipress_wplms_complete_assignment_minimum_mark':
        case 'gamipress_wplms_complete_specific_assignment_minimum_mark':
        // Unit
        case 'gamipress_wplms_complete_unit':
        case 'gamipress_wplms_complete_specific_unit':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_wplms_trigger_get_user_id', 10, 3 );

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
function gamipress_wplms_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        // Course
        case 'gamipress_wplms_subscribe_specific_course':
        case 'gamipress_wplms_start_specific_course':
        case 'gamipress_wplms_complete_specific_course':
        case 'gamipress_wplms_complete_specific_course_minimum_mark':
        case 'gamipress_wplms_review_specific_course':
        case 'gamipress_wplms_unsubscribe_specific_course':
        case 'gamipress_wplms_retake_specific_course':
        // Quiz
        case 'gamipress_wplms_start_specific_quiz':
        case 'gamipress_wplms_complete_specific_quiz':
        case 'gamipress_wplms_complete_specific_quiz_minimum_mark':
        case 'gamipress_wplms_retake_specific_quiz':
        // Assignment
        case 'gamipress_wplms_start_specific_assignment':
        case 'gamipress_wplms_complete_specific_assignment':
        case 'gamipress_wplms_complete_specific_assignment_minimum_mark':
        // Unit
        case 'gamipress_wplms_complete_specific_unit':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_wplms_specific_trigger_get_id', 10, 3 );

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
function gamipress_wplms_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Course
        case 'gamipress_wplms_subscribe_course':
        case 'gamipress_wplms_subscribe_specific_course':
        case 'gamipress_wplms_start_course':
        case 'gamipress_wplms_start_specific_course':
        case 'gamipress_wplms_complete_course':
        case 'gamipress_wplms_complete_specific_course':
        case 'gamipress_wplms_review_course':
        case 'gamipress_wplms_review_specific_course':
        case 'gamipress_wplms_unsubscribe_course':
        case 'gamipress_wplms_unsubscribe_specific_course':
        case 'gamipress_wplms_retake_course':
        case 'gamipress_wplms_retake_specific_course':
            // Add the course ID
            $log_meta['course_id'] = $args[0];
            break;
        case 'gamipress_wplms_complete_course_minimum_mark':
        case 'gamipress_wplms_complete_specific_course_minimum_mark':
            // Add the course ID and score
            $log_meta['course_id'] = $args[0];
            $log_meta['score'] = $args[2];
            break;
        // Quiz
        case 'gamipress_wplms_start_quiz':
        case 'gamipress_wplms_start_specific_quiz':
        case 'gamipress_wplms_complete_quiz':
        case 'gamipress_wplms_complete_specific_quiz':
        case 'gamipress_wplms_retake_quiz':
        case 'gamipress_wplms_retake_specific_quiz':
            // Add the quiz ID
            $log_meta['quiz_id'] = $args[0];
            break;
        case 'gamipress_wplms_complete_quiz_minimum_mark':
        case 'gamipress_wplms_complete_specific_quiz_minimum_mark':
            // Add the quiz ID and score
            $log_meta['quiz_id'] = $args[0];
            $log_meta['score'] = $args[2];
            break;

        // Assignment
        case 'gamipress_wplms_start_assignment':
        case 'gamipress_wplms_start_specific_assignment':
        case 'gamipress_wplms_complete_assignment':
        case 'gamipress_wplms_complete_specific_assignment':
            // Add the assignment ID
            $log_meta['assignment_id'] = $args[0];
            break;
        case 'gamipress_wplms_complete_assignment_minimum_mark':
        case 'gamipress_wplms_complete_specific_assignment_minimum_mark':
            // Add the assignment ID and score
            $log_meta['assignment_id'] = $args[0];
            $log_meta['score'] = $args[2];
            break;

        // Unit
        case 'gamipress_wplms_complete_unit':
        case 'gamipress_wplms_complete_specific_unit':
            // Add the unit and course IDs
            $log_meta['unit_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_wplms_log_event_trigger_meta_data', 10, 5 );