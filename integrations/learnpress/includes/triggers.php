<?php
/**
 * Triggers
 *
 * @package GamiPress\LearnPress\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register LearnPress specific triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_lp_activity_triggers( $triggers ) {

    $triggers[__( 'LearnPress', 'gamipress' )] = array(
        // Course
        'gamipress_lp_enroll_course'            => __( 'Enroll any course', 'gamipress' ),
        'gamipress_lp_enroll_specific_course'   => __( 'Enroll a specific course', 'gamipress' ),
        'gamipress_lp_finish_course'            => __( 'Finish a course', 'gamipress' ),
        'gamipress_lp_finish_specific_course'   => __( 'Finish a specific course', 'gamipress' ),
        // Lesson
        'gamipress_lp_complete_lesson'          => __( 'Complete a lesson', 'gamipress' ),
        'gamipress_lp_complete_specific_lesson' => __( 'Complete a specific lesson', 'gamipress' ),
        // Finish quiz
        'gamipress_lp_finish_quiz'                  => __( 'Finish a quiz', 'gamipress' ),
        'gamipress_lp_finish_specific_quiz'         => __( 'Finish a specific quiz', 'gamipress' ),
        'gamipress_lp_finish_quiz_specific_course'  => __( 'Finish a quiz of a specific course', 'gamipress' ),
        // Pass quiz
        'gamipress_lp_pass_quiz'                    => __( 'Pass a quiz', 'gamipress' ),
        'gamipress_lp_pass_specific_quiz'           => __( 'Pass a specific quiz', 'gamipress' ),
        'gamipress_lp_pass_quiz_specific_course'    => __( 'Pass a quiz of a specific course', 'gamipress' ),
        // Fail quiz
        'gamipress_lp_fail_quiz'                    => __( 'Fail a quiz', 'gamipress' ),
        'gamipress_lp_fail_specific_quiz'           => __( 'Fail a specific quiz', 'gamipress' ),
        'gamipress_lp_fail_quiz_specific_course'    => __( 'Fail a quiz of a specific course', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_lp_activity_triggers' );

/**
 * Register LearnPress specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_lp_specific_activity_triggers( $specific_activity_triggers ) {

    // Course
    $specific_activity_triggers['gamipress_lp_enroll_specific_course'] = array( LP_COURSE_CPT );
    $specific_activity_triggers['gamipress_lp_finish_specific_course'] = array( LP_COURSE_CPT );
    // Lesson
    $specific_activity_triggers['gamipress_lp_complete_specific_lesson'] = array( LP_LESSON_CPT );
    // Finish quiz
    $specific_activity_triggers['gamipress_lp_finish_specific_quiz'] = array( LP_QUIZ_CPT );
    $specific_activity_triggers['gamipress_lp_finish_quiz_specific_course'] = array( LP_COURSE_CPT );
    // Pass quiz
    $specific_activity_triggers['gamipress_lp_pass_specific_quiz'] = array( LP_QUIZ_CPT );
    $specific_activity_triggers['gamipress_lp_pass_quiz_specific_course'] = array( LP_COURSE_CPT );
    // Fail quiz
    $specific_activity_triggers['gamipress_lp_fail_specific_quiz'] = array( LP_QUIZ_CPT );
    $specific_activity_triggers['gamipress_lp_fail_quiz_specific_course'] = array( LP_COURSE_CPT );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_lp_specific_activity_triggers' );

/**
 * Register LearnPress specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_lp_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Course
    $specific_activity_trigger_labels['gamipress_lp_enroll_specific_course'] = __( 'Enroll the course %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_lp_finish_specific_course'] = __( 'Finish the course %s', 'gamipress' );
    // Lesson
    $specific_activity_trigger_labels['gamipress_lp_complete_specific_lesson'] = __( 'Complete the lesson %s', 'gamipress' );
    // Finish quiz
    $specific_activity_trigger_labels['gamipress_lp_finish_specific_quiz'] = __( 'Finish the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_lp_finish_quiz_specific_course'] = __( 'Finish any quiz of the course %s', 'gamipress' );
    // Pass quiz
    $specific_activity_trigger_labels['gamipress_lp_pass_specific_quiz'] = __( 'Pass the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_lp_pass_quiz_specific_course'] = __( 'Pass any quiz of the course %s', 'gamipress' );
    // Fail quiz
    $specific_activity_trigger_labels['gamipress_lp_fail_specific_quiz'] = __( 'Fail the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_lp_fail_quiz_specific_course'] = __( 'Fail any quiz of the course %s', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_lp_specific_activity_trigger_label' );

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
function gamipress_lp_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Course
        case 'gamipress_lp_enroll_course':
        case 'gamipress_lp_enroll_specific_course':
        case 'gamipress_lp_finish_course':
        case 'gamipress_lp_finish_specific_course':
        // Lesson
        case 'gamipress_lp_complete_lesson':
        case 'gamipress_lp_complete_specific_lesson':
        // Finish quiz
        case 'gamipress_lp_finish_quiz':
        case 'gamipress_lp_finish_specific_quiz':
        case 'gamipress_lp_finish_quiz_specific_course':
        // Pass quiz
        case 'gamipress_lp_pass_quiz':
        case 'gamipress_lp_pass_specific_quiz':
        case 'gamipress_lp_pass_quiz_specific_course':
        // Fail quiz
        case 'gamipress_lp_fail_quiz':
        case 'gamipress_lp_fail_specific_quiz':
        case 'gamipress_lp_fail_quiz_specific_course':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_lp_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.1
 *
 * @param  integer  $specific_id Specific ID.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          Specific ID.
 */
function gamipress_lp_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_lp_enroll_specific_course':
        case 'gamipress_lp_finish_specific_course':
        case 'gamipress_lp_complete_specific_lesson':
        case 'gamipress_lp_finish_specific_quiz':
        case 'gamipress_lp_pass_specific_quiz':
        case 'gamipress_lp_fail_specific_quiz':
            $specific_id = $args[0];
            break;
        case 'gamipress_lp_finish_quiz_specific_course':
        case 'gamipress_lp_pass_quiz_specific_course':
        case 'gamipress_lp_fail_quiz_specific_course':
            $specific_id = $args[2];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_lp_specific_trigger_get_id', 10, 3 );

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
function gamipress_lp_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Course
        case 'gamipress_lp_enroll_course':
        case 'gamipress_lp_enroll_specific_course':
        case 'gamipress_lp_finish_course':
        case 'gamipress_lp_finish_specific_course':
            // Add the course ID
            $log_meta['course_id'] = $args[0];
            break;
        // Lesson
        case 'gamipress_lp_complete_lesson':
        case 'gamipress_lp_complete_specific_lesson':
            // Add the lesson ID
            $log_meta['lesson_id'] = $args[0];
            break;
        // Finish quiz
        case 'gamipress_lp_finish_quiz':
        case 'gamipress_lp_finish_specific_quiz':
        case 'gamipress_lp_finish_quiz_specific_course':
        // Pass quiz
        case 'gamipress_lp_pass_quiz':
        case 'gamipress_lp_pass_specific_quiz':
        case 'gamipress_lp_pass_quiz_specific_course':
        // Fail quiz
        case 'gamipress_lp_fail_quiz':
        case 'gamipress_lp_fail_specific_quiz':
        case 'gamipress_lp_fail_quiz_specific_course':
            // Add the course ID
            $log_meta['quiz_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_lp_log_event_trigger_meta_data', 10, 5 );