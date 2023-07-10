<?php
/**
 * Triggers
 *
 * @package GamiPress\MasterStudy_LMS\Triggers
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
function gamipress_masterstudy_lms_activity_triggers( $triggers ) {

    $triggers[__( 'MasterStudy LMS', 'gamipress' )] = array(
        // Quiz
        'gamipress_masterstudy_lms_complete_quiz' => __( 'Complete a quiz', 'gamipress' ),
        'gamipress_masterstudy_lms_complete_specific_quiz' => __( 'Complete a specific quiz', 'gamipress' ),
        'gamipress_masterstudy_lms_pass_quiz' => __( 'Pass a quiz', 'gamipress' ),
        'gamipress_masterstudy_lms_pass_specific_quiz' => __( 'Pass a specific quiz', 'gamipress' ),
        'gamipress_masterstudy_lms_fail_quiz' => __( 'Fail a quiz', 'gamipress' ),
        'gamipress_masterstudy_lms_fail_specific_quiz' => __( 'Fail a specific quiz', 'gamipress' ),
        // Lesson
        'gamipress_masterstudy_lms_complete_lesson' => __( 'Complete a lesson', 'gamipress' ),
        'gamipress_masterstudy_lms_complete_specific_lesson' => __( 'Complete a specific lesson', 'gamipress' ),
        'gamipress_masterstudy_lms_complete_lesson_specific_course' => __( 'Complete a lesson of a specific', 'gamipress' ),
        // Course
        'gamipress_masterstudy_lms_enroll_course' => __( 'Enroll any course', 'gamipress' ),
        'gamipress_masterstudy_lms_enroll_specific_course' => __( 'Enroll a specific course', 'gamipress' ),
        'gamipress_masterstudy_lms_complete_course' => __( 'Complete a course', 'gamipress' ),
        'gamipress_masterstudy_lms_complete_specific_course' => __( 'Complete a specific course', 'gamipress' ),
        'gamipress_masterstudy_lms_download_course_certificate' => __( 'Download a course certificate', 'gamipress' ),
        'gamipress_masterstudy_lms_download_specific_course_certificate' => __( 'Download a specific course certificate', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_masterstudy_lms_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_masterstudy_lms_specific_activity_triggers( $specific_activity_triggers ) {

    // Quiz
    $specific_activity_triggers['gamipress_masterstudy_lms_complete_specific_quiz'] = array( 'stm-quizzes' );
    $specific_activity_triggers['gamipress_masterstudy_lms_pass_specific_quiz'] = array( 'stm-quizzes' );
    $specific_activity_triggers['gamipress_masterstudy_lms_fail_specific_quiz'] = array( 'stm-quizzes' );
    // Lesson
    $specific_activity_triggers['gamipress_masterstudy_lms_complete_specific_lesson'] = array( 'stm-lessons' );
    $specific_activity_triggers['gamipress_masterstudy_lms_complete_lesson_specific_course'] = array( 'stm-courses' );
    // Course
    $specific_activity_triggers['gamipress_masterstudy_lms_enroll_specific_course'] = array( 'stm-courses' );
    $specific_activity_triggers['gamipress_masterstudy_lms_complete_specific_course'] = array( 'stm-courses' );
    $specific_activity_triggers['gamipress_masterstudy_lms_download_specific_course_certificate'] = array( 'stm-courses' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_masterstudy_lms_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_masterstudy_lms_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Quiz
    $specific_activity_trigger_labels['gamipress_masterstudy_lms_complete_specific_quiz'] = __( 'Complete the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_masterstudy_lms_pass_specific_quiz'] = __( 'Pass the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_masterstudy_lms_fail_specific_quiz'] = __( 'Fail the quiz %s', 'gamipress' );
    // Lesson
    $specific_activity_trigger_labels['gamipress_masterstudy_lms_complete_specific_lesson'] = __( 'Complete the lesson %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_masterstudy_lms_complete_lesson_specific_course'] = __( 'Complete a lesson of the course %s', 'gamipress' );
    // Course
    $specific_activity_trigger_labels['gamipress_masterstudy_lms_enroll_specific_course'] = __( 'Enroll the course %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_masterstudy_lms_complete_specific_course'] = __( 'Complete the course %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_masterstudy_lms_download_specific_course_certificate'] = __( 'Download the certificate of the course %s', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_masterstudy_lms_specific_activity_trigger_label' );

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
function gamipress_masterstudy_lms_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Quiz
        case 'gamipress_masterstudy_lms_complete_quiz':
        case 'gamipress_masterstudy_lms_complete_specific_quiz':
        case 'gamipress_masterstudy_lms_pass_quiz':
        case 'gamipress_masterstudy_lms_pass_specific_quiz':
        case 'gamipress_masterstudy_lms_fail_quiz':
        case 'gamipress_masterstudy_lms_fail_specific_quiz':
        // Lesson
        case 'gamipress_masterstudy_lms_complete_lesson':
        case 'gamipress_masterstudy_lms_complete_specific_lesson':
        case 'gamipress_masterstudy_lms_complete_lesson_specific_course':
        // Course
        case 'gamipress_masterstudy_lms_enroll_course':
        case 'gamipress_masterstudy_lms_enroll_specific_course':
        case 'gamipress_masterstudy_lms_complete_course':
        case 'gamipress_masterstudy_lms_complete_specific_course':
        case 'gamipress_masterstudy_lms_download_course_certificate':
        case 'gamipress_masterstudy_lms_download_specific_course_certificate':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_masterstudy_lms_trigger_get_user_id', 10, 3 );

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
function gamipress_masterstudy_lms_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        // Quiz
        case 'gamipress_masterstudy_lms_complete_specific_quiz':
        case 'gamipress_masterstudy_lms_pass_specific_quiz':
        case 'gamipress_masterstudy_lms_fail_specific_quiz':
        // Lesson
        case 'gamipress_masterstudy_lms_complete_specific_lesson':
        // Course
        case 'gamipress_masterstudy_lms_enroll_specific_course':
        case 'gamipress_masterstudy_lms_complete_specific_course':
        case 'gamipress_masterstudy_lms_download_specific_course_certificate':
            $specific_id = $args[0];
            break;
        case 'gamipress_masterstudy_lms_complete_lesson_specific_course':
            $specific_id = $args[2];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_masterstudy_lms_specific_trigger_get_id', 10, 3 );

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
function gamipress_masterstudy_lms_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Quiz
        case 'gamipress_masterstudy_lms_complete_quiz':
        case 'gamipress_masterstudy_lms_complete_specific_quiz':
        case 'gamipress_masterstudy_lms_pass_quiz':
        case 'gamipress_masterstudy_lms_pass_specific_quiz':
        case 'gamipress_masterstudy_lms_fail_quiz':
        case 'gamipress_masterstudy_lms_fail_specific_quiz':
            // Add the quiz and course IDs
            $log_meta['quiz_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            break;
        // Lesson
        case 'gamipress_masterstudy_lms_complete_lesson':
        case 'gamipress_masterstudy_lms_complete_specific_lesson':
        case 'gamipress_masterstudy_lms_complete_lesson_specific_course':
            // Add the lesson and course IDs
            $log_meta['lesson_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            break;
        // Course
        case 'gamipress_masterstudy_lms_enroll_course':
        case 'gamipress_masterstudy_lms_enroll_specific_course':
        case 'gamipress_masterstudy_lms_complete_course':
        case 'gamipress_masterstudy_lms_complete_specific_course':
        case 'gamipress_masterstudy_lms_download_course_certificate':
        case 'gamipress_masterstudy_lms_download_specific_course_certificate':
            // Add the course ID
            $log_meta['course_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_masterstudy_lms_log_event_trigger_meta_data', 10, 5 );