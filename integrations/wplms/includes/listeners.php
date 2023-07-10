<?php
/**
 * Listeners
 *
 * @package GamiPress\WPLMS\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// --------------------------------
// Course
// --------------------------------

/**
 * Subscribe a course
 *
 * @param integer $course_id
 * @param integer $user_id
 */
function gamipress_wplms_subscribe_course( $course_id, $user_id ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    // Subscribe any course
    do_action( 'gamipress_wplms_subscribe_course', $course_id, $user_id );

    // Subscribe specific course
    do_action( 'gamipress_wplms_subscribe_specific_course', $course_id, $user_id );

}
add_action( 'wplms_course_subscribed', 'gamipress_wplms_subscribe_course', 10, 2 );

/**
 * Start a course
 *
 * @param integer $course_id
 * @param integer $user_id
 */
function gamipress_wplms_start_course( $course_id, $user_id ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    // Start any course
    do_action( 'gamipress_wplms_start_course', $course_id, $user_id );

    // Start specific course
    do_action( 'gamipress_wplms_start_specific_course', $course_id, $user_id );

}
add_action( 'wplms_start_course', 'gamipress_wplms_start_course', 10, 2 );

/**
 * Complete a course
 *
 * @param integer $course_id
 * @param integer $user_id
 */
function gamipress_wplms_complete_course( $course_id, $user_id ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    // Complete any course
    do_action( 'gamipress_wplms_complete_course', $course_id, $user_id );

    // Complete specific course
    do_action( 'gamipress_wplms_complete_specific_course', $course_id, $user_id );

}
add_action( 'wplms_submit_course', 'gamipress_wplms_complete_course', 10, 2 );

/**
 * Complete a course with a minimum mark
 *
 * @param integer $course_id
 * @param null $marks
 * @param integer $user_id
 */
function gamipress_wplms_complete_course_minimum_mark( $course_id, $marks, $user_id ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    // Complete any course with a minimum mark
    do_action( 'gamipress_wplms_complete_course_minimum_mark', $course_id, $user_id, $marks );

    // Complete specific course with a minimum mark
    do_action( 'gamipress_wplms_complete_specific_course_minimum_mark', $course_id, $user_id, $marks );

}
add_action( 'wplms_evaluate_course', 'gamipress_wplms_complete_course_minimum_mark', 10, 3 );

/**
 * Review a course
 *
 * @param integer $course_id
 * @param integer $rating
 * @param string $title
 */
function gamipress_wplms_review_course( $course_id, $rating, $title ) {

    $user_id = get_current_user_id();

    // Review any course
    do_action( 'gamipress_wplms_review_course', $course_id, $user_id );

    // Review specific course
    do_action( 'gamipress_wplms_review_specific_course', $course_id, $user_id );

}
add_action( 'wplms_course_review', 'gamipress_wplms_review_course', 10, 3 );

/**
 * Unsubscribe a course
 *
 * @param integer $course_id
 * @param integer $user_id
 */
function gamipress_wplms_unsubscribe_course( $course_id, $user_id ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    // Unsubscribe any course
    do_action( 'gamipress_wplms_unsubscribe_course', $course_id, $user_id );

    // Unsubscribe specific course
    do_action( 'gamipress_wplms_unsubscribe_specific_course', $course_id, $user_id );

}
add_action( 'wplms_course_unsubscribe', 'gamipress_wplms_unsubscribe_course', 10, 2 );

/**
 * Retake a course
 *
 * @param integer $course_id
 * @param integer $user_id
 */
function gamipress_wplms_retake_course( $course_id, $user_id ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    // Retake any course
    do_action( 'gamipress_wplms_retake_course', $course_id, $user_id );

    // Retake specific course
    do_action( 'gamipress_wplms_retake_specific_course', $course_id, $user_id );

}
add_action( 'wplms_course_retake', 'gamipress_wplms_retake_course', 10, 2 );

// --------------------------------
// Quiz
// --------------------------------

/**
 * Start a quiz
 *
 * @param integer $quiz_id
 * @param integer $user_id
 */
function gamipress_wplms_start_quiz( $quiz_id, $user_id ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    // Start any quiz
    do_action( 'gamipress_wplms_start_quiz', $quiz_id, $user_id );

    // Start specific quiz
    do_action( 'gamipress_wplms_start_specific_quiz', $quiz_id, $user_id );

}
add_action( 'wplms_start_quiz', 'gamipress_wplms_start_quiz', 10, 2 );

/**
 * Complete a quiz
 *
 * @param integer $quiz_id
 * @param integer $user_id
 * @param array $answers
 */
function gamipress_wplms_complete_quiz( $quiz_id, $user_id, $answers ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    // Complete any quiz
    do_action( 'gamipress_wplms_complete_quiz', $quiz_id, $user_id );

    // Complete specific quiz
    do_action( 'gamipress_wplms_complete_specific_quiz', $quiz_id, $user_id );

}
add_action( 'wplms_submit_quiz', 'gamipress_wplms_complete_quiz', 10, 3 );

/**
 * Complete a quiz with a minimum mark
 *
 * @param integer $quiz_id
 * @param string $total_marks             Can be a single number of a range separated by "-"
 * @param integer $user_id
 * @param string $max_marks               Can be a single number of a range separated by "-"
 */
function gamipress_wplms_complete_quiz_minimum_mark( $quiz_id, $total_marks, $user_id, $max_marks ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    // Complete any quiz with a minimum mark
    do_action( 'gamipress_wplms_complete_quiz_minimum_mark', $quiz_id, $user_id, $total_marks, $max_marks );

    // Complete specific quiz with a minimum mark
    do_action( 'gamipress_wplms_complete_specific_quiz_minimum_mark', $quiz_id, $user_id, $total_marks, $max_marks );

}
add_action( 'wplms_evaluate_quiz', 'gamipress_wplms_complete_quiz_minimum_mark', 10, 4 );

/**
 * Retake a quiz
 *
 * @param integer $quiz_id
 * @param integer $user_id
 */
function gamipress_wplms_retake_quiz( $quiz_id, $user_id ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    // Retake any quiz
    do_action( 'gamipress_wplms_retake_quiz', $quiz_id, $user_id );

    // Retake specific quiz
    do_action( 'gamipress_wplms_retake_specific_quiz', $quiz_id, $user_id );

}
add_action( 'wplms_quiz_retake', 'gamipress_wplms_retake_quiz', 10, 2 );

// --------------------------------
// Assignment
// --------------------------------

/**
 * Start an assignment
 *
 * @param integer $assignment_id
 * @param integer $user_id
 */
function gamipress_wplms_start_assignment( $assignment_id, $user_id ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    // Start any assignment
    do_action( 'gamipress_wplms_start_assignment', $assignment_id, $user_id );

    // Start specific assignment
    do_action( 'gamipress_wplms_start_specific_assignment', $assignment_id, $user_id );

}
add_action( 'wplms_start_assignment', 'gamipress_wplms_start_assignment', 10, 2 );

/**
 * Complete an assignment
 *
 * @param integer $assignment_id
 * @param integer $user_id
 */
function gamipress_wplms_complete_assignment( $assignment_id, $user_id ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    // Complete any assignment
    do_action( 'gamipress_wplms_complete_assignment', $assignment_id, $user_id );

    // Complete specific assignment
    do_action( 'gamipress_wplms_complete_specific_assignment', $assignment_id, $user_id );

}
add_action( 'wplms_submit_assignment', 'gamipress_wplms_complete_assignment', 10, 2 );

/**
 * Complete an assignment with a minimum mark
 *
 * @param integer $assignment_id
 * @param string $marks             Can be a single number of a range separated by "-"
 * @param integer $user_id
 * @param string $max_marks         Can be a single number of a range separated by "-"
 */
function gamipress_wplms_complete_assignment_minimum_mark( $assignment_id, $marks, $user_id, $max_marks ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    // Complete any assignment with a minimum mark
    do_action( 'gamipress_wplms_complete_assignment_minimum_mark', $assignment_id, $user_id, $marks, $max_marks );

    // Complete specific assignment with a minimum mark
    do_action( 'gamipress_wplms_complete_specific_assignment_minimum_mark', $assignment_id, $user_id, $marks, $max_marks );

}
add_action( 'wplms_evaluate_assignment', 'gamipress_wplms_complete_assignment_minimum_mark', 10, 4 );

// --------------------------------
// Unit
// --------------------------------

/**
 * Complete an unit
 *
 * @param integer $unit_id
 * @param integer $info
 * @param integer $course_id
 * @param integer $user_id
 */
function gamipress_wplms_complete_unit( $unit_id, $course_progress = null, $course_id, $user_id = null ) {

    if( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    // Complete any unit
    do_action( 'gamipress_wplms_complete_unit', $unit_id, $user_id, $course_id, $course_progress );

    // Complete specific unit
    do_action( 'gamipress_wplms_complete_specific_unit', $unit_id, $user_id, $course_id, $course_progress );

}
add_action( 'wplms_unit_complete', 'gamipress_wplms_complete_unit', 10, 4 );