<?php
/**
 * Triggers
 *
 * @package GamiPress\Tutor\Triggers
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
function gamipress_tutor_activity_triggers( $triggers ) {

    $triggers[__( 'Tutor LMS', 'gamipress' )] = array(

        // Quizzes
        'gamipress_tutor_complete_quiz'                    => __( 'Complete a quiz', 'gamipress' ),
        'gamipress_tutor_complete_specific_quiz'           => __( 'Complete a specific quiz', 'gamipress' ),
        'gamipress_tutor_complete_quiz_specific_course'    => __( 'Complete any quiz of a specific course', 'gamipress' ),
        'gamipress_tutor_complete_quiz_course_category'    => __( 'Complete a quiz of a course of a category', 'gamipress' ),

        // Pass
        'gamipress_tutor_pass_quiz'                    => __( 'Successfully pass a quiz', 'gamipress' ),
        'gamipress_tutor_pass_specific_quiz'           => __( 'Successfully pass a specific quiz', 'gamipress' ),
        'gamipress_tutor_pass_quiz_specific_course'    => __( 'Successfully pass a quiz of a specific course', 'gamipress' ),
        'gamipress_tutor_pass_quiz_course_category'    => __( 'Successfully pass a quiz of a course of a category', 'gamipress' ),

        // Fail
        'gamipress_tutor_fail_quiz'                    => __( 'Fail a quiz', 'gamipress' ),
        'gamipress_tutor_fail_specific_quiz'           => __( 'Fail a specific quiz', 'gamipress' ),
        'gamipress_tutor_fail_quiz_specific_course'    => __( 'Fail a quiz of a specific course', 'gamipress' ),
        'gamipress_tutor_fail_quiz_course_category'    => __( 'Fail a quiz of a course of a category', 'gamipress' ),

        // Lessons
        'gamipress_tutor_complete_lesson'                  => __( 'Complete a lesson', 'gamipress' ),
        'gamipress_tutor_complete_specific_lesson'         => __( 'Complete a specific lesson', 'gamipress' ),
        'gamipress_tutor_complete_lesson_specific_course'  => __( 'Complete a lesson of a specific course', 'gamipress' ),
        'gamipress_tutor_complete_lesson_course_category'  => __( 'Complete a lesson of a course of a category', 'gamipress' ),

        // Courses
        'gamipress_tutor_enroll_course'             => __( 'Enroll a course', 'gamipress' ),
        'gamipress_tutor_enroll_specific_course'    => __( 'Enroll a specific course', 'gamipress' ),
        'gamipress_tutor_enroll_course_category'    => __( 'Enroll a course of a category', 'gamipress' ),
        'gamipress_tutor_complete_course'           => __( 'Complete a course', 'gamipress' ),
        'gamipress_tutor_complete_specific_course'  => __( 'Complete a specific course', 'gamipress' ),
        'gamipress_tutor_complete_course_category'  => __( 'Complete a course of a category', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_tutor_activity_triggers' );

/**
 * Register Tutor specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_tutor_specific_activity_triggers( $specific_activity_triggers ) {


    $quiz   = apply_filters( 'tutor_quiz_post_type', 'tutor_quiz' );
    $lesson = apply_filters( 'tutor_lesson_post_type', 'lesson' );
    $course = apply_filters( 'tutor_course_post_type', 'courses' );

    // Quizzes
    $specific_activity_triggers['gamipress_tutor_complete_specific_quiz'] = array( $quiz );
    $specific_activity_triggers['gamipress_tutor_complete_quiz_specific_course'] = array( $course );

    $specific_activity_triggers['gamipress_tutor_pass_specific_quiz'] = array( $quiz );
    $specific_activity_triggers['gamipress_tutor_pass_quiz_specific_course'] = array( $course );

    $specific_activity_triggers['gamipress_tutor_fail_specific_quiz'] = array( $quiz );
    $specific_activity_triggers['gamipress_tutor_fail_quiz_specific_course'] = array( $course );

    // Lessons
    $specific_activity_triggers['gamipress_tutor_complete_specific_lesson'] = array( $lesson );
    $specific_activity_triggers['gamipress_tutor_complete_lesson_specific_course'] = array( $course );

    // Courses
    $specific_activity_triggers['gamipress_tutor_enroll_specific_course'] = array( $course );
    $specific_activity_triggers['gamipress_tutor_complete_specific_course'] = array( $course );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_tutor_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_tutor_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Quizzes
    $specific_activity_trigger_labels['gamipress_tutor_complete_specific_quiz'] = __( 'Complete the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_tutor_complete_quiz_specific_course'] = __( 'Complete any quiz of the course %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_tutor_pass_specific_quiz'] = __( 'Pass the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_tutor_pass_quiz_specific_course'] = __( 'Pass a quiz of the course %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_tutor_fail_specific_quiz'] = __( 'Fail the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_tutor_fail_quiz_specific_course'] = __( 'Fail a quiz of the course %s', 'gamipress' );

    // Lessons
    $specific_activity_trigger_labels['gamipress_tutor_complete_specific_lesson'] = __( 'Complete the lesson %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_tutor_complete_lesson_specific_course'] = __( 'Complete a lesson of the course %s', 'gamipress' );

    // Courses
    $specific_activity_trigger_labels['gamipress_tutor_enroll_specific_course'] = __( 'Enroll the course %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_tutor_complete_specific_course'] = __( 'Complete the course %s', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_tutor_specific_activity_trigger_label' );

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
function gamipress_tutor_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $tutor_category = ( isset( $requirement['tutor_category'] ) ) ? $requirement['tutor_category'] : '';

    // Get the JetEngine post types
    $term = get_term_by( 'id', $tutor_category, 'course-category' );

    switch( $requirement['trigger_type'] ) {
        case 'gamipress_tutor_complete_quiz_course_category':
            return sprintf( __( 'Complete a quiz of a course of "%s" category', 'gamipress' ), $term->name );
            break;
        case 'gamipress_tutor_pass_quiz_course_category':
            return sprintf( __( 'Successfully pass a quiz of a course of %s category', 'gamipress' ), $term->name );
            break;
        case 'gamipress_tutor_fail_quiz_course_category':
            return sprintf( __( 'Fail a quiz of a course of %s category', 'gamipress' ), $term->name );
            break;
        case 'gamipress_tutor_complete_lesson_course_category':
            return sprintf( __( 'Complete a lesson of a course of %s category', 'gamipress' ), $term->name );
            break;
        case 'gamipress_tutor_complete_course_category':
            return sprintf( __( 'Complete a course of %s category', 'gamipress' ), $term->name );
            break;
        case 'gamipress_tutor_enroll_course_category':
            return sprintf( __( 'Enroll a course of %s category', 'gamipress' ), $term->name );
            break;
        
    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_tutor_activity_trigger_label', 10, 3 );

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
function gamipress_tutor_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Quizzes
        case 'gamipress_tutor_complete_quiz':
        case 'gamipress_tutor_complete_specific_quiz':
        case 'gamipress_tutor_complete_quiz_specific_course':
        case 'gamipress_tutor_pass_quiz':
        case 'gamipress_tutor_pass_specific_quiz':
        case 'gamipress_tutor_pass_quiz_specific_course':
        case 'gamipress_tutor_fail_quiz':
        case 'gamipress_tutor_fail_specific_quiz':
        case 'gamipress_tutor_fail_quiz_specific_course':

        // Lessons
        case 'gamipress_tutor_complete_lesson':
        case 'gamipress_tutor_complete_specific_lesson':
        case 'gamipress_tutor_complete_lesson_specific_course':

        // Courses
        case 'gamipress_tutor_enroll_course':
        case 'gamipress_tutor_enroll_specific_course':
        case 'gamipress_tutor_complete_course':
        case 'gamipress_tutor_complete_specific_course':

        // Categories
        case 'gamipress_tutor_complete_quiz_course_category':
        case 'gamipress_tutor_pass_quiz_course_category':
        case 'gamipress_tutor_fail_quiz_course_category':
        case 'gamipress_tutor_complete_lesson_course_category':
        case 'gamipress_tutor_complete_course_category':
        case 'gamipress_tutor_enroll_course_category':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_tutor_trigger_get_user_id', 10, 3 );

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
function gamipress_tutor_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_tutor_complete_specific_quiz':
        case 'gamipress_tutor_pass_specific_quiz':
        case 'gamipress_tutor_fail_specific_quiz':
        case 'gamipress_tutor_complete_specific_lesson':
        case 'gamipress_tutor_enroll_specific_course':
        case 'gamipress_tutor_complete_specific_course':
            $specific_id = $args[0];
            break;
        case 'gamipress_tutor_complete_quiz_specific_course':
        case 'gamipress_tutor_pass_quiz_specific_course':
        case 'gamipress_tutor_fail_quiz_specific_course':
        case 'gamipress_tutor_complete_lesson_specific_course':
            $specific_id = $args[2];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_tutor_specific_trigger_get_id', 10, 3 );

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
function gamipress_tutor_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {

        // Quizzes
        case 'gamipress_tutor_complete_quiz':
        case 'gamipress_tutor_complete_specific_quiz':
        case 'gamipress_tutor_complete_quiz_specific_course':
        case 'gamipress_tutor_pass_quiz':
        case 'gamipress_tutor_pass_specific_quiz':
        case 'gamipress_tutor_pass_quiz_specific_course':
        case 'gamipress_tutor_fail_quiz':
        case 'gamipress_tutor_fail_specific_quiz':
        case 'gamipress_tutor_fail_quiz_specific_course':
            // Add the quiz and course IDs
            $log_meta['quiz_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            break;

        // Lessons
        case 'gamipress_tutor_complete_lesson':
        case 'gamipress_tutor_complete_specific_lesson':
        case 'gamipress_tutor_complete_lesson_specific_course':
            // Add the lesson and course IDs
            $log_meta['lesson_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            break;

        // Courses
        case 'gamipress_tutor_enroll_course':
        case 'gamipress_tutor_enroll_specific_course':
        case 'gamipress_tutor_complete_course':
        case 'gamipress_tutor_complete_specific_course':
            // Add the course ID
            $log_meta['course_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_tutor_log_event_trigger_meta_data', 10, 5 );