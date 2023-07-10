<?php
/**
 * Listeners
 *
 * @package GamiPress\Sensei\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Complete quiz
 *
 * @since 1.0.0
 *
 * @param  integer $user_id         ID of user being graded
 * @param  integer $quiz_id         ID of quiz
 * @param  integer $grade           Grade received
 * @param  integer $quiz_passmark   Quiz required pass mark
 * @param  string $quiz_grade_type  default 'auto'
 */
function gamipress_sensei_complete_quiz( $user_id, $quiz_id, $grade, $quiz_passmark, $quiz_grade_type ) {

    // Complete any quiz
    do_action( 'gamipress_sensei_complete_quiz', $quiz_id, $user_id );

    // Complete specific quiz
    do_action( 'gamipress_sensei_complete_specific_quiz', $quiz_id, $user_id );

    // Complete any quiz with a minimum percent grade
    do_action( 'gamipress_sensei_complete_quiz_grade', $quiz_id, $user_id, $grade );

    // Complete specific quiz with a minimum percent grade
    do_action( 'gamipress_sensei_complete_specific_quiz_grade', $quiz_id, $user_id, $grade );

    // Complete any quiz with a maximum percent grade
    do_action( 'gamipress_sensei_complete_quiz_max_grade', $quiz_id, $user_id, $grade );

    // Complete specific quiz with a maximum percent grade
    do_action( 'gamipress_sensei_complete_specific_quiz_max_grade', $quiz_id, $user_id, $grade );

    // If user has successfully passed the quiz
    if( $grade > $quiz_passmark ) {

        // Pass any quiz
        do_action( 'gamipress_sensei_pass_quiz', $quiz_id, $user_id, $grade );

        // Pass specific quiz
        do_action( 'gamipress_sensei_pass_specific_quiz', $quiz_id, $user_id, $grade );

    } else {
        // User has failed the quiz

        // Fail any quiz
        do_action( 'gamipress_sensei_fail_quiz', $quiz_id, $user_id, $grade );

        // Fail specific quiz
        do_action( 'gamipress_sensei_fail_specific_quiz', $quiz_id, $user_id, $grade );
    }

}
add_action( 'sensei_user_quiz_grade', 'gamipress_sensei_complete_quiz', 10, 5 );

/**
 * Complete lesson
 *
 * @since 1.0.0
 *
 * @param  integer $user_id         ID of user being graded
 * @param  integer $lesson_id       ID of lesson
 */
function gamipress_sensei_complete_lesson( $user_id, $lesson_id ) {

    $course_id = get_post_meta( $lesson_id, '_lesson_course', true );

    // Complete any lesson
    do_action( 'gamipress_sensei_complete_lesson', $lesson_id, $user_id, $course_id );

    // Complete specific lesson
    do_action( 'gamipress_sensei_complete_specific_lesson', $lesson_id, $user_id, $course_id );

}
add_action( 'sensei_user_lesson_end', 'gamipress_sensei_complete_lesson', 10, 2 );

/**
 * Enroll in a course
 *
 * @since 1.0.0
 *
 * @param  integer $user_id         ID of user being graded
 * @param  integer $course_id       ID of course
 */
function gamipress_sensei_start_course( $user_id, $course_id ) {

    // Enroll in a course
    do_action( 'gamipress_sensei_start_course', $course_id, $user_id );

}
add_action( 'sensei_user_course_start', 'gamipress_sensei_start_course', 10, 2 );

/**
 * Complete course
 *
 * @since 1.0.0
 *
 * @param  integer $user_id         ID of user being graded
 * @param  integer $course_id       ID of course
 */
function gamipress_sensei_complete_course( $user_id, $course_id ) {

    // Complete any course
    do_action( 'gamipress_sensei_complete_course', $course_id, $user_id );

    // Complete specific course
    do_action( 'gamipress_sensei_complete_specific_course', $course_id, $user_id );

}
add_action( 'sensei_user_course_end', 'gamipress_sensei_complete_course', 10, 2 );
