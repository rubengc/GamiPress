<?php
/**
 * Triggers
 *
 * @package GamiPress\LifterLMS\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register LifterLMS specific triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_lifterlms_activity_triggers( $triggers ) {

    $key = __( 'LifterLMS', 'gamipress' );

    $triggers[$key] = array(

        // Quizzes
        'gamipress_lifterlms_complete_quiz'                     => __( 'Complete a quiz', 'gamipress' ),
        'gamipress_lifterlms_complete_specific_quiz'            => __( 'Complete a specific quiz', 'gamipress' ),

        'gamipress_lifterlms_pass_quiz'                         => __( 'Pass a quiz', 'gamipress' ),
        'gamipress_lifterlms_pass_specific_quiz'                => __( 'Pass a specific quiz', 'gamipress' ),

        'gamipress_lifterlms_fail_quiz'                         => __( 'Fail a quiz', 'gamipress' ),
        'gamipress_lifterlms_fail_specific_quiz'                => __( 'Fail a specific quiz', 'gamipress' ),
        // Lessons
        'gamipress_lifterlms_complete_lesson'                   => __( 'Complete a lesson', 'gamipress' ),
        'gamipress_lifterlms_complete_specific_lesson'          => __( 'Complete a specific lesson', 'gamipress' ),
        'gamipress_lifterlms_complete_specific_course_lesson'   => __( 'Complete a lesson of a specific course', 'gamipress' ),
        // Sections
        'gamipress_lifterlms_complete_section'                  => __( 'Complete a section', 'gamipress' ),
        'gamipress_lifterlms_complete_specific_section'         => __( 'Complete a specific section', 'gamipress' ),
        'gamipress_lifterlms_complete_specific_course_section'  => __( 'Complete a section of a specific course', 'gamipress' ),
        // Courses
        'gamipress_lifterlms_enroll_course'                     => __( 'Enroll a course', 'gamipress' ),
        'gamipress_lifterlms_enroll_specific_course'            => __( 'Enroll a specific course', 'gamipress' ),
        'gamipress_lifterlms_complete_course'                   => __( 'Complete a course', 'gamipress' ),
        'gamipress_lifterlms_complete_specific_course'          => __( 'Complete a specific course', 'gamipress' ),
        // Memberships
        'gamipress_lifterlms_enroll_membership'                 => __( 'Enroll a membership', 'gamipress' ),
        'gamipress_lifterlms_enroll_specific_membership'        => __( 'Enroll a specific membership', 'gamipress' ),
        // Access Plans
        'gamipress_lifterlms_purchase_access_plan'              => __( 'Purchase a access plan', 'gamipress' ),
        'gamipress_lifterlms_purchase_specific_access_plan'     => __( 'Purchase a specific access plan', 'gamipress' ),
        // Certificate
        'gamipress_lifterlms_earn_certificate'                  => __( 'Earn a certificate', 'gamipress' ),
        'gamipress_lifterlms_earn_specific_certificate'         => __( 'Earn a specific certificate', 'gamipress' ),
    );

    // Assignments
    if( class_exists( 'LifterLMS_Assignments' ) ) {

        // Merge new events with integration's ones
        $triggers[$key] = array_merge( $triggers[$key], array(

            'gamipress_lifterlms_submit_assignment'             => __( 'Submit an assignment', 'gamipress' ),
            'gamipress_lifterlms_submit_specific_assignment'    => __( 'Submit a specific assignment', 'gamipress' ),

            'gamipress_lifterlms_pass_assignment'               => __( 'Pass an assignment', 'gamipress' ),
            'gamipress_lifterlms_pass_specific_assignment'      => __( 'Pass a specific assignment', 'gamipress' ),

            'gamipress_lifterlms_fail_assignment'               => __( 'Fail an assignment', 'gamipress' ),
            'gamipress_lifterlms_fail_specific_assignment'      => __( 'Fail a specific assignment', 'gamipress' ),
        ) );
    }

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_lifterlms_activity_triggers' );

/**
 * Register LifterLMS specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_lifterlms_specific_activity_triggers( $specific_activity_triggers ) {

    // Quizzes
    $specific_activity_triggers['gamipress_lifterlms_complete_specific_quiz'] = array( 'llms_quiz' );
    $specific_activity_triggers['gamipress_lifterlms_pass_specific_quiz'] = array( 'llms_quiz' );
    $specific_activity_triggers['gamipress_lifterlms_fail_specific_quiz'] = array( 'llms_quiz' );
    // Lessons
    $specific_activity_triggers['gamipress_lifterlms_complete_specific_lesson'] = array( 'lesson' );
    $specific_activity_triggers['gamipress_lifterlms_complete_specific_course_lesson'] = array( 'course' );
    // Sections
    $specific_activity_triggers['gamipress_lifterlms_complete_specific_section'] = array( 'section' );
    $specific_activity_triggers['gamipress_lifterlms_complete_specific_course_section'] = array( 'course' );
    // Courses
    $specific_activity_triggers['gamipress_lifterlms_enroll_specific_course'] = array( 'course' );
    $specific_activity_triggers['gamipress_lifterlms_complete_specific_course'] = array( 'course' );
    // Memberships
    $specific_activity_triggers['gamipress_lifterlms_enroll_specific_membership'] = array( 'llms_membership' );
    // Access Plans
    $specific_activity_triggers['gamipress_lifterlms_purchase_specific_access_plan'] = array( 'llms_access_plan' );
    // Certificates
    $specific_activity_triggers['gamipress_lifterlms_earn_specific_certificate'] = array( 'llms_certificate' );
    // Assignments
    $specific_activity_triggers['gamipress_lifterlms_submit_specific_assignment'] = array( 'llms_assignment' );
    $specific_activity_triggers['gamipress_lifterlms_pass_specific_assignment'] = array( 'llms_assignment' );
    $specific_activity_triggers['gamipress_lifterlms_fail_specific_assignment'] = array( 'llms_assignment' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_lifterlms_specific_activity_triggers' );

/**
 * Register LifterLMS specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_lifterlms_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Quizzes
    $specific_activity_trigger_labels['gamipress_lifterlms_complete_specific_quiz'] = __( 'Complete the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_lifterlms_pass_specific_quiz'] = __( 'Pass the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_lifterlms_fail_specific_quiz'] = __( 'Fail the quiz %s', 'gamipress' );
    // Lessons
    $specific_activity_trigger_labels['gamipress_lifterlms_complete_specific_lesson'] = __( 'Complete the lesson %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_lifterlms_complete_specific_course_lesson'] = __( 'Complete a lesson of the course %s', 'gamipress' );
    // Sections
    $specific_activity_trigger_labels['gamipress_lifterlms_complete_specific_section'] = __( 'Complete the section %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_lifterlms_complete_specific_course_section'] = __( 'Complete a section of the course %s', 'gamipress' );
    // Courses
    $specific_activity_trigger_labels['gamipress_lifterlms_enroll_specific_course'] = __( 'Enroll the course %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_lifterlms_complete_specific_course'] = __( 'Complete the course %s', 'gamipress' );
    // Memberships
    $specific_activity_trigger_labels['gamipress_lifterlms_enroll_specific_membership'] = __( 'Enroll the membership %s', 'gamipress' );
    // Access Plans
    $specific_activity_trigger_labels['gamipress_lifterlms_purchase_specific_access_plan'] = __( 'Purchase the access plan %s', 'gamipress' );
    // Certificates
    $specific_activity_trigger_labels['gamipress_lifterlms_earn_specific_certificate'] = __( 'Earn the certificate %s', 'gamipress' );
    // Assignments
    $specific_activity_trigger_labels['gamipress_lifterlms_submit_specific_assignment'] = __( 'Submit the assignment %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_lifterlms_pass_specific_assignment'] = __( 'Pass the assignment %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_lifterlms_fail_specific_assignment'] = __( 'Fail the assignment %s', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_lifterlms_specific_activity_trigger_label' );

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
function gamipress_lifterlms_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Quizzes
        case 'gamipress_lifterlms_complete_quiz':
        case 'gamipress_lifterlms_complete_specific_quiz':

        case 'gamipress_lifterlms_pass_quiz':
        case 'gamipress_lifterlms_pass_specific_quiz':

        case 'gamipress_lifterlms_fail_quiz':
        case 'gamipress_lifterlms_fail_specific_quiz':
        // Lessons
        case 'gamipress_lifterlms_complete_lesson':
        case 'gamipress_lifterlms_complete_specific_lesson':
        case 'gamipress_lifterlms_complete_specific_course_lesson':
        // Sections
        case 'gamipress_lifterlms_complete_section':
        case 'gamipress_lifterlms_complete_specific_section':
        case 'gamipress_lifterlms_complete_specific_course_section':
        // Courses
        case 'gamipress_lifterlms_enroll_course':
        case 'gamipress_lifterlms_enroll_specific_course':
        case 'gamipress_lifterlms_complete_course':
        case 'gamipress_lifterlms_complete_specific_course':
        // Memberships
        case 'gamipress_lifterlms_enroll_membership':
        case 'gamipress_lifterlms_enroll_specific_membership':
        // Access Plans
        case 'gamipress_lifterlms_purchase_access_plan':
        case 'gamipress_lifterlms_purchase_specific_access_plan':
        // Certificate
        case 'gamipress_lifterlms_earn_certificate':
        case 'gamipress_lifterlms_earn_specific_certificate':
        // Assignments
        case 'gamipress_lifterlms_submit_assignment':
        case 'gamipress_lifterlms_submit_specific_assignment':

        case 'gamipress_lifterlms_pass_assignment':
        case 'gamipress_lifterlms_pass_specific_assignment':

        case 'gamipress_lifterlms_fail_assignment':
        case 'gamipress_lifterlms_fail_specific_assignment':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_lifterlms_trigger_get_user_id', 10, 3 );

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
function gamipress_lifterlms_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        // Quizzes
        case 'gamipress_lifterlms_complete_specific_quiz':
        case 'gamipress_lifterlms_pass_specific_quiz':
        case 'gamipress_lifterlms_fail_specific_quiz':
        // Lessons
        case 'gamipress_lifterlms_complete_specific_lesson':
        // Sections
        case 'gamipress_lifterlms_complete_specific_section':
        // Courses
        case 'gamipress_lifterlms_enroll_specific_course':
        case 'gamipress_lifterlms_complete_specific_course':
        // Memberships
        case 'gamipress_lifterlms_enroll_specific_membership':
        // Access Plans
        case 'gamipress_lifterlms_purchase_specific_access_plan':
        // Certificate
        case 'gamipress_lifterlms_earn_specific_certificate':
        // Assignments
        case 'gamipress_lifterlms_submit_specific_assignment':
        case 'gamipress_lifterlms_pass_specific_assignment':
        case 'gamipress_lifterlms_fail_specific_assignment':
            $specific_id = $args[0];
            break;
        // Lessons
        case 'gamipress_lifterlms_complete_specific_course_lesson':
        // Sections
        case 'gamipress_lifterlms_complete_specific_course_section':
            $specific_id = $args[2];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_lifterlms_specific_trigger_get_id', 10, 3 );

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
function gamipress_lifterlms_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Quizzes
        case 'gamipress_lifterlms_complete_quiz':
        case 'gamipress_lifterlms_complete_specific_quiz':

        case 'gamipress_lifterlms_pass_quiz':
        case 'gamipress_lifterlms_pass_specific_quiz':

        case 'gamipress_lifterlms_fail_quiz':
        case 'gamipress_lifterlms_fail_specific_quiz':
            // Add the quiz, lesson and course IDs
            $log_meta['quiz_id'] = $args[0];
            $log_meta['lesson_id'] = $args[2];
            $log_meta['course_id'] = $args[3];
            break;
            // Lessons
        case 'gamipress_lifterlms_complete_lesson':
        case 'gamipress_lifterlms_complete_specific_lesson':
        case 'gamipress_lifterlms_complete_specific_course_lesson':
            // Add the lesson and course IDs
            $log_meta['lesson_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            break;
        case 'gamipress_lifterlms_complete_section':
        case 'gamipress_lifterlms_complete_specific_section':
        case 'gamipress_lifterlms_complete_specific_course_section':
            // Add the section and course IDs
            $log_meta['section_id'] = $args[0];
            $log_meta['section_id'] = $args[2];
            break;
        // Courses
        case 'gamipress_lifterlms_enroll_course':
        case 'gamipress_lifterlms_enroll_specific_course':
        case 'gamipress_lifterlms_complete_course':
        case 'gamipress_lifterlms_complete_specific_course':
            // Add the course ID
            $log_meta['course_id'] = $args[0];
            break;
        // Memberships
        case 'gamipress_lifterlms_enroll_membership':
        case 'gamipress_lifterlms_enroll_specific_membership':
            // Add the membership ID
            $log_meta['membership_id'] = $args[0];
            break;
        // Access Plans
        case 'gamipress_lifterlms_purchase_access_plan':
        case 'gamipress_lifterlms_purchase_specific_access_plan':
            // Add the plan ID
            $log_meta['plan_id'] = $args[0];
            break;
        // Certificate
        case 'gamipress_lifterlms_earn_certificate':
        case 'gamipress_lifterlms_earn_specific_certificate':
            // Add the certificate ID
            $log_meta['certificate_id'] = $args[0];
            break;
        // Assignments
        case 'gamipress_lifterlms_submit_assignment':
        case 'gamipress_lifterlms_submit_specific_assignment':
        case 'gamipress_lifterlms_pass_assignment':
        case 'gamipress_lifterlms_pass_specific_assignment':
        case 'gamipress_lifterlms_fail_assignment':
        case 'gamipress_lifterlms_fail_specific_assignment':
            // Add the assignment ID
            $log_meta['assignment_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_lifterlms_log_event_trigger_meta_data', 10, 5 );