<?php
/**
 * Listeners
 *
 * @package GamiPress\LifterLMS\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Complete lesson, section, course, or course track listener
 *
 * @since 1.0.0
 *
 * @param integer   $user_id
 * @param integer   $object_id
 */
function gamipress_lifterlms_common_listener( $user_id, $object_id) {

    $object_type = str_replace( 'lifterlms_', '', str_replace( '_completed', '', current_filter() ) );

    // Used for lesson and section
    $course = llms_get_post_parent_course( $object_id );
    $course_id = null;

    if( $course ) {
        $course_id = $course->get( 'id' );
    }

    // Complete any lesson, section, course, or course track
    do_action( "gamipress_lifterlms_complete_{$object_type}", $object_id, $user_id, $course_id );

    // Complete specific lesson, section, course, or course track
    do_action( "gamipress_lifterlms_complete_specific_{$object_type}", $object_id, $user_id, $course_id );

    if( $course_id !== null ) {
        // Complete specific course lesson or section
        do_action( "gamipress_lifterlms_complete_specific_course_{$object_type}", $object_id, $user_id, $course_id );
    }

}
//add_action( 'llms_mark_complete', 'gamipress_lifterlms_common_listener', 10, 4 ); // Old hook
add_action( 'lifterlms_course_completed', 'gamipress_lifterlms_common_listener', 10, 2 );
add_action( 'lifterlms_lesson_completed', 'gamipress_lifterlms_common_listener', 10, 2 );
add_action( 'lifterlms_section_completed', 'gamipress_lifterlms_common_listener', 10, 2 );

/**
 * Complete, pass and fail quiz listeners
 *
 * @since 1.0.4
 *
 * @param int               $user_id
 * @param int               $quiz_id
 * @param LLMS_Quiz_Attempt $quiz
 */
function gamipress_lifterlms_complete_quiz( $user_id, $quiz_id, $quiz ) {

    $action = 'complete';

    switch( current_filter() ) {
        case 'lifterlms_quiz_passed':
            $action = 'pass';
            break;
        case 'lifterlms_quiz_failed':
            $action = 'fail';
            break;
    }

    $lesson_id = $quiz->get( 'lesson_id' );
    $course = llms_get_post_parent_course( $lesson_id );
    $course_id = null;

    if( $course ) {
        $course_id = $course->get( 'id' );
    }

    // Complete, pass or fail any quiz
    do_action( "gamipress_lifterlms_{$action}_quiz", $quiz_id, $user_id, $lesson_id, $course_id, $quiz );

    // Complete, pass or fail specific quiz
    do_action( "gamipress_lifterlms_{$action}_specific_quiz", $quiz_id, $user_id, $lesson_id, $course_id, $quiz );

}
add_action( 'lifterlms_quiz_completed', 'gamipress_lifterlms_complete_quiz', 10, 3 );
add_action( 'lifterlms_quiz_passed', 'gamipress_lifterlms_complete_quiz', 10, 3 );
add_action( 'lifterlms_quiz_failed', 'gamipress_lifterlms_complete_quiz', 10, 3 );

/**
 * Enroll in a course listener
 *
 * @since 1.0.6
 *
 * @param int   $user_id
 * @param int   $product_id
 */
function gamipress_lifterlms_enroll_course( $user_id, $product_id ) {

    // Enroll any course
    do_action( 'gamipress_lifterlms_enroll_course', $product_id, $user_id );

    // Enroll specific course
    do_action( 'gamipress_lifterlms_enroll_specific_course', $product_id, $user_id );

}
add_action( 'llms_user_enrolled_in_course', 'gamipress_lifterlms_enroll_course', 10, 2 );

/**
 * Enroll in a membership listener
 *
 * @since 1.0.6
 *
 * @param int   $user_id
 * @param int   $product_id
 */
function gamipress_lifterlms_enroll_membership( $user_id, $product_id ) {

    // Enroll any membership
    do_action( 'gamipress_lifterlms_enroll_membership', $product_id, $user_id );

    // Enroll specific membership
    do_action( 'gamipress_lifterlms_enroll_specific_membership', $product_id, $user_id );

}
add_action( 'llms_user_added_to_membership_level', 'gamipress_lifterlms_enroll_membership', 10, 2 );

/**
 * Purchase access plan listener
 *
 * @since 1.0.6
 *
 * @param int   $user_id
 * @param int   $plan_id
 */
function gamipress_lifterlms_purchase_access_pass( $user_id, $plan_id ) {

    // Purchase any membership
    do_action( 'gamipress_lifterlms_purchase_access_plan', $plan_id, $user_id );

    // Purchase specific membership
    do_action( 'gamipress_lifterlms_purchase_specific_access_plan', $plan_id, $user_id );

}
add_action( 'lifterlms_access_plan_purchased', 'gamipress_lifterlms_purchase_access_pass', 10, 2 );

/**
 * Earn certificate listener
 *
 * @since 1.0.6
 *
 * @param int   $user_id
 * @param int   $new_user_certificate_id (ID of post of the type 'llms_my_certificate')
 * @param int   $related_post_id
 */
function gamipress_lifterlms_earn_certificate( $user_id, $new_user_certificate_id, $related_post_id ) {

    // Main certificate is on '_llms_certificate_template' meta
    $certificate_id = get_post_meta( $new_user_certificate_id, '_llms_certificate_template', true );

    // Purchase any membership
    do_action( 'gamipress_lifterlms_earn_certificate', $certificate_id, $user_id );

    // Purchase specific membership
    do_action( 'gamipress_lifterlms_earn_specific_certificate', $certificate_id, $user_id );

}
add_action( 'llms_user_earned_certificate', 'gamipress_lifterlms_earn_certificate', 10, 3 );


/**
 * Assignment submission listener
 *
 * @since 1.0.3
 *
 * @param LLMS_Assignment_Submission $submission
 */
function gamipress_lifterlms_assignment_submitted( $submission ) {

    $assignment = $submission->get_assignment();
    $user_id = $submission->get( 'user_id' );

    // Submit any assignment
    do_action( 'gamipress_lifterlms_submit_assignment', $assignment->id, $user_id );

    // Submit specific assignment
    do_action( 'gamipress_lifterlms_submit_specific_assignment', $assignment->id, $user_id );

    // Task lists get auto-approved
    if ( 'tasklist' === $assignment->get( 'assignment_type' ) ) {
        // Pass any assignment
        do_action( 'gamipress_lifterlms_pass_assignment', $assignment->id, $user_id );

        // Pass specific assignment
        do_action( 'gamipress_lifterlms_pass_specific_assignment', $assignment->id, $user_id );
    }

}
add_action( 'llms_assignment_submitted', 'gamipress_lifterlms_assignment_submitted' );

/**
 * Assignment submission listener
 *
 * @since 1.0.3
 *
 * @param LLMS_Assignment_Submission $submission
 */
function gamipress_lifterlms_assignment_graded( $submission ) {

    $assignment = $submission->get_assignment();
    $user_id = $submission->get( 'user_id' );
    $status = $submission->get( 'status' );

    // Pass/Fail any assignment
    do_action( "gamipress_lifterlms_{$status}_assignment", $assignment->id, $user_id );

    // Pass/Fail specific assignment
    do_action( "gamipress_lifterlms_{$status}_specific_assignment", $assignment->id, $user_id );

}
add_action( 'llms_assignment_graded', 'gamipress_lifterlms_assignment_graded' );