<?php
/**
 * Listeners
 *
 * @package GamiPress\Tutor\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Complete quiz listener
 *
 * @since 1.0.0
 *
 * @param int $attempt_id
 */
function gamipress_tutor_complete_quiz( $attempt_id ) {

    $attempt = tutor_utils()->get_attempt( $attempt_id );
    $user_id = get_current_user_id();
    $quiz_id = $attempt->quiz_id;

    // Bail if is not a quiz
    if ( 'tutor_quiz' !== get_post_type( $quiz_id ) ) {
        return;
    }

    // Bail if attempt isn't finished yet
    if ( ! in_array( $attempt->attempt_status, array( 'attempt_ended', 'review_required' ) ) ) {
        return;
    }

    $course = tutor_utils()->get_course_by_quiz( $quiz_id );
    $course_id = $course->ID;

    // Get the course categories
    $terms_id = gamipress_tutor_get_term_ids( $course_id);

    // Complete any quiz
    do_action( 'gamipress_tutor_complete_quiz', $quiz_id, $user_id, $course_id, $attempt_id );

    // Complete specific quiz
    do_action( 'gamipress_tutor_complete_specific_quiz', $quiz_id, $user_id, $course_id, $attempt_id );

    // Complete any quiz of a specific course
    do_action( 'gamipress_tutor_complete_quiz_specific_course', $quiz_id, $user_id, $course_id, $attempt_id );

    // Complete any quiz of a specific category
    do_action( 'gamipress_tutor_complete_quiz_course_category', $quiz_id, $user_id, $course_id, $attempt_id, $terms_id );

    $grade = ( $attempt->earned_marks * 100 ) / $attempt->total_marks;
    $passing_grade = (int) tutor_utils()->get_quiz_option( $attempt->quiz_id, 'passing_grade', 0 );

    // Just trigger events if user has asked all questions correctly
    if( $grade >= $passing_grade  ) {

        // Pass any quiz
        do_action( 'gamipress_tutor_pass_quiz', $quiz_id, $user_id, $course_id, $attempt_id );

        // Pass specific quiz
        do_action( 'gamipress_tutor_pass_specific_quiz', $quiz_id, $user_id, $course_id, $attempt_id );

        // Pass any quiz of a specific course
        do_action( 'gamipress_tutor_pass_quiz_specific_course', $quiz_id, $user_id, $course_id, $attempt_id );

        // Pass any quiz of a specific category
        do_action( 'gamipress_tutor_pass_quiz_course_category', $quiz_id, $user_id, $course_id, $attempt_id, $terms_id );

    } else {

        // Fail any quiz
        do_action( 'gamipress_tutor_fail_quiz', $quiz_id, $user_id, $course_id, $attempt_id );

        // Fail specific quiz
        do_action( 'gamipress_tutor_fail_specific_quiz', $quiz_id, $user_id, $course_id, $attempt_id );

        // Fail any quiz of a specific course
        do_action( 'gamipress_tutor_fail_quiz_specific_course', $quiz_id, $user_id, $course_id, $attempt_id );

        // Fail any quiz of a specific category
        do_action( 'gamipress_tutor_fail_quiz_course_category', $quiz_id, $user_id, $course_id, $attempt_id, $terms_id );

    }

}
add_action( 'tutor_quiz/attempt_ended', 'gamipress_tutor_complete_quiz', 10, 1 );

/**
 * Complete lesson listener
 *
 * @since 1.0.0
 *
 * @param int $lesson_id
 */
function gamipress_tutor_complete_lesson( $lesson_id ) {

    $user_id = get_current_user_id();

    $course_id = tutor_utils()->get_course_id_by_lesson( $lesson_id );

    // Get the course categories
    $terms_id = gamipress_tutor_get_term_ids( $course_id );

    // Complete any lesson
    do_action( 'gamipress_tutor_complete_lesson', $lesson_id, $user_id, $course_id );

    // Complete specific lesson
    do_action( 'gamipress_tutor_complete_specific_lesson', $lesson_id, $user_id, $course_id );

    // Complete any lesson of a specific course
    do_action( 'gamipress_tutor_complete_lesson_specific_course', $lesson_id, $user_id, $course_id );
    
    // Complete any lesson of a specific category
    do_action( 'gamipress_tutor_complete_lesson_course_category', $lesson_id, $user_id, $course_id, $terms_id );

}
add_action( 'tutor_lesson_completed_after', 'gamipress_tutor_complete_lesson' );

/**
 * Enroll course listener
 *
 * @since 1.0.0
 *
 * @param int $course_id
 */
function gamipress_tutor_enroll_course( $course_id ) {

    $user_id = get_current_user_id();

    // Get the course categories
    $terms_id = gamipress_tutor_get_term_ids( $course_id);

    // Enroll any course
    do_action( 'gamipress_tutor_enroll_course', $course_id, $user_id );

    // Enroll specific course
    do_action( 'gamipress_tutor_enroll_specific_course', $course_id, $user_id );

    // Enroll specific category
    do_action( 'gamipress_tutor_enroll_course_category', $course_id, $user_id, $terms_id );

}
add_action( 'tutor_after_enroll', 'gamipress_tutor_enroll_course' );

/**
 * Complete course listener
 *
 * @since 1.0.0
 *
 * @param int $course_id
 */
function gamipress_tutor_complete_course( $course_id ) {

    $user_id = get_current_user_id();

    // Get the course categories
    $terms_id = gamipress_tutor_get_term_ids( $course_id);

    // Complete any course
    do_action( 'gamipress_tutor_complete_course', $course_id, $user_id );

    // Complete specific course
    do_action( 'gamipress_tutor_complete_specific_course', $course_id, $user_id );

    // Complete specific category
    do_action( 'gamipress_tutor_complete_course_category', $course_id, $user_id, $terms_id );

}
add_action( 'tutor_course_complete_after', 'gamipress_tutor_complete_course' );
