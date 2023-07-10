<?php
/**
 * Listeners for LearnPress < 3.0
 *
 * @package GamiPress\LearnPress\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Enroll course
function gamipress_lp_enroll_course( $course_id, $user_id, $inserted ) {

    // Enroll any course
    do_action( 'gamipress_lp_enroll_course', $course_id, $user_id );

    // Enroll specific course
    do_action( 'gamipress_lp_enroll_specific_course', $course_id, $user_id );

}
add_action( 'learn_press_user_enrolled_course', 'gamipress_lp_enroll_course', 10, 3 );

// Finish course
function gamipress_lp_finish_course( $course_id, $user_id, $return ) {

    // Finish any course
    do_action( 'gamipress_lp_finish_course', $course_id, $user_id );

    // Finish specific course
    do_action( 'gamipress_lp_finish_specific_course', $course_id, $user_id );

}
add_action( 'learn_press_user_finish_course', 'gamipress_lp_finish_course', 10, 3 );

// Complete lesson
function gamipress_lp_complete_lesson( $lesson_id, $result, $user_id ) {

    // Complete any lesson
    do_action( 'gamipress_lp_complete_lesson', $lesson_id, $user_id );

    // Complete specific lesson
    do_action( 'gamipress_lp_complete_specific_lesson', $lesson_id, $user_id );

}
add_action( 'learn_press_user_complete_lesson', 'gamipress_lp_complete_lesson', 10, 3 );

// Finish quiz
function gamipress_lp_finish_quiz( $quiz_id, $course_id, $user_id ) {

    // Finish any course
    do_action( 'gamipress_lp_finish_quiz', $quiz_id, $user_id, $course_id );

    // Finish specific course
    do_action( 'gamipress_lp_finish_specific_quiz', $quiz_id, $user_id, $course_id );

}
add_action( 'learn_press_user_finish_quiz', 'gamipress_lp_finish_quiz', 10, 3 );
