<?php
/**
 * Listeners
 *
 * @package GamiPress\MasterStudy_LMS\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Complete a quiz listener
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param int $quiz_id
 * @param int $progress
 */
function gamipress_masterstudy_lms_complete_quiz( $user_id, $quiz_id, $progress ) {

    // Interesting GET vars
    $course_id = intval( $_GET['course_id'] );
    //$quiz_id = intval( $_GET['quiz_id'] );

    // Complete any quiz
    do_action( 'gamipress_masterstudy_lms_complete_quiz', $quiz_id, $user_id, $course_id, $progress );

    // Complete specific quiz
    do_action( 'gamipress_masterstudy_lms_complete_specific_quiz', $quiz_id, $user_id, $course_id, $progress );

    $passed = ( current_filter() === 'stm_lms_quiz_passed' );

    if( $passed ) {
        // Pass any quiz
        do_action( 'gamipress_masterstudy_lms_pass_quiz', $quiz_id, $user_id, $course_id, $progress );

        // Pass specific quiz
        do_action( 'gamipress_masterstudy_lms_pass_specific_quiz', $quiz_id, $user_id, $course_id, $progress );
    } else {
        // Fail any quiz
        do_action( 'gamipress_masterstudy_lms_fail_quiz', $quiz_id, $user_id, $course_id, $progress );

        // Fail specific quiz
        do_action( 'gamipress_masterstudy_lms_fail_specific_quiz', $quiz_id, $user_id, $course_id, $progress );
    }

}
add_action( 'stm_lms_quiz_passed', 'gamipress_masterstudy_lms_complete_quiz', 10, 3 );
add_action( 'stm_lms_quiz_failed', 'gamipress_masterstudy_lms_complete_quiz', 10, 3 );

/**
 * Complete a lesson listener
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param int $lesson_id
 */
function gamipress_masterstudy_lms_complete_lesson( $user_id, $lesson_id ) {

    // Interesting GET vars
    $course_id = intval( $_GET['course'] );
    //$lesson_id = intval( $_GET['lesson'] );

    // Complete any lesson
    do_action( 'gamipress_masterstudy_lms_complete_lesson', $lesson_id, $user_id, $course_id );

    // Complete specific lesson
    do_action( 'gamipress_masterstudy_lms_complete_specific_lesson', $lesson_id, $user_id, $course_id );

    if( $course_id !== 0 ) {
        // Complete any lesson of a specific course
        do_action( 'gamipress_masterstudy_lms_complete_lesson_specific_course', $lesson_id, $user_id, $course_id );
    }

}
add_action( 'stm_lms_lesson_passed', 'gamipress_masterstudy_lms_complete_lesson', 10, 2 );

/**
 * Enroll course listener
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param int $course_id
 */
function gamipress_masterstudy_lms_enroll_course( $user_id, $course_id ) {

    // Enroll any course
    do_action( 'gamipress_masterstudy_lms_enroll_course', $course_id, $user_id );

    // Enroll specific course
    do_action( 'gamipress_masterstudy_lms_enroll_specific_course', $course_id, $user_id );

}
add_action( 'add_user_course', 'gamipress_masterstudy_lms_enroll_course', 10, 2 );

/**
 * Complete course listener
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param int $course_id
 * @param int $progress Is a percent, 100 means complete
 */
function gamipress_masterstudy_lms_course_progress_updated( $course_id, $user_id, $progress ) {

    // Bail if course not completed
    if( $progress < 100 ) return;

    // Complete any course
    do_action( 'gamipress_masterstudy_lms_complete_course', $course_id, $user_id );

    // Complete specific course
    do_action( 'gamipress_masterstudy_lms_complete_specific_course', $course_id, $user_id );

}
add_action( 'stm_lms_progress_updated', 'gamipress_masterstudy_lms_course_progress_updated', 10, 3 );

/**
 * Download a course certificate listener
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param int $course_id
 */
function gamipress_masterstudy_lms_download_certificate( $user_id, $course_id ) {

    // Download any course certificate
    do_action( 'gamipress_masterstudy_lms_download_course_certificate', $course_id, $user_id );

    // Download a specific course certificate
    do_action( 'gamipress_masterstudy_lms_download_specific_course_certificate', $course_id, $user_id );

}
add_action( 'stm_lms_certificate_generated', 'gamipress_masterstudy_lms_download_certificate', 10, 2  );


