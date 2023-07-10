<?php
/**
 * Listeners
 *
 * @package GamiPress\LearnPress\Listeners
 * @since   1.0.0
 * @updated 1.0.6 to add compatibility to LearnPress 3.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Enroll course
function gamipress_lp_enroll_course( $order_id, $course_id, $user_id ) {

    // Enroll any course
    do_action( 'gamipress_lp_enroll_course', $course_id, $user_id );

    // Enroll a specific course
    do_action( 'gamipress_lp_enroll_specific_course', $course_id, $user_id );

}
add_action( 'learnpress/user/course-enrolled', 'gamipress_lp_enroll_course', 10, 3 );

// Finish course
function gamipress_lp_finish_course( $course_id, $user_id, $return ) {

    // Finish any course
    do_action( 'gamipress_lp_finish_course', $course_id, $user_id );

    // Finish a specific course
    do_action( 'gamipress_lp_finish_specific_course', $course_id, $user_id );

}
add_action( 'learn-press/user-course-finished', 'gamipress_lp_finish_course', 10, 3 );

// Complete lesson
function gamipress_lp_complete_lesson( $lesson_id, $course_id, $user_id ) {

    // Complete any lesson
    do_action( 'gamipress_lp_complete_lesson', $lesson_id, $user_id );

    // Complete a specific lesson
    do_action( 'gamipress_lp_complete_specific_lesson', $lesson_id, $user_id );

}
add_action( 'learn-press/user-completed-lesson', 'gamipress_lp_complete_lesson', 10, 3 );

// Finish quiz
function gamipress_lp_finish_quiz( $quiz_id, $course_id, $user_id ) {

    // Finish any quiz
    do_action( 'gamipress_lp_finish_quiz', $quiz_id, $user_id, $course_id );

    // Finish a specific quiz
    do_action( 'gamipress_lp_finish_specific_quiz', $quiz_id, $user_id, $course_id );

    // Finish any quiz of a specific course
    do_action( 'gamipress_lp_finish_quiz_specific_course', $quiz_id, $user_id, $course_id );

}
add_action( 'learn-press/user/quiz-finished', 'gamipress_lp_finish_quiz', 10, 3 );

// Pass/Fail quiz
function gamipress_lp_pass_fail_quiz( $quiz_id, $course_id, $user_id ) {

    $user = learn_press_get_user( $user_id );

    if( ! ( $user instanceof LP_User ) ) {
        return;
    }

    $user_quiz = $user->get_item_data( $quiz_id, $course_id );

    if( ! $user_quiz ) {
        return;
    }

    // Calculate the results to meet if the quiz has been passed
    $result = $user_quiz->calculate_results();
    $percent = $result['mark'] ? ( $result['user_mark'] / $result['mark'] ) * 100 : 0;
    $grade = '';
    if ( $user_quiz->get_status() === 'completed' ) {
        $grade = $percent >= $user_quiz->get_quiz()->get_data( 'passing_grade' ) ? 'passed' : 'failed';
    }

    if( $grade === 'passed' ) {

        // Pass any quiz
        do_action( 'gamipress_lp_pass_quiz', $quiz_id, $user_id, $course_id );

        // Pass a specific quiz
        do_action( 'gamipress_lp_pass_specific_quiz', $quiz_id, $user_id, $course_id );

        // Pass any quiz of a specific course
        do_action( 'gamipress_lp_pass_quiz_specific_course', $quiz_id, $user_id, $course_id );

    } else {

        // Fail any quiz
        do_action( 'gamipress_lp_fail_quiz', $quiz_id, $user_id, $course_id );

        // Fail a specific quiz
        do_action( 'gamipress_lp_fail_specific_quiz', $quiz_id, $user_id, $course_id );

        // Fail any quiz of a specific course
        do_action( 'gamipress_lp_fail_quiz_specific_course', $quiz_id, $user_id, $course_id );

    }

}
add_action( 'learn-press/user/quiz-finished', 'gamipress_lp_pass_fail_quiz', 10, 3 );
