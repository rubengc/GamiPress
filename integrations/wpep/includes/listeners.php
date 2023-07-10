<?php
/**
 * Listeners
 *
 * @package GamiPress\WPEP\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Check status constant
$gamipress_wpep_assessment_status = 'status';
if( defined('WPEP_USER_ASSESSMENT_STATUS') )
    $gamipress_wpep_assessment_status = WPEP_USER_ASSESSMENT_STATUS;

// Check score constant
$gamipress_wpep_assessment_score = 'score';
if( defined('WPEP_USER_ASSESSMENT_SCORE') )
    $gamipress_wpep_assessment_score = WPEP_USER_ASSESSMENT_SCORE;

/**
 * Lesson listener
 *
 * @since 1.0.0
 *
 * @param $lesson_id
 * @param $is_completed
 * @param $user_id
 */
function gamipress_wpep_lesson_listener( $lesson_id, $is_completed, $user_id ) {

    // Bail if not completed or user is not set
    if( ! $is_completed || ! $user_id )
        return;

    $course_id = WPEP\Entity\Course::instance()->get_course_id_by_lesson_id( $lesson_id );

    // Bail if lesson hasn't a course
    if( $course_id === null )
        return;

    // Check lesson completion

    // Trigger complete any lesson
    do_action( 'gamipress_wpep_complete_lesson', $lesson_id, $user_id, $course_id, $is_completed );

    // Trigger complete specific lesson
    do_action( 'gamipress_wpep_complete_specific_lesson', $lesson_id, $user_id, $course_id, $is_completed );

    // Check course completion
    $course_completed = wpep_user_course_is_completed( $course_id, $user_id, true );

    if( $course_completed ) {

        // Trigger complete any course
        do_action( 'gamipress_wpep_complete_course', $course_id, $user_id, $lesson_id, $is_completed );

        // Trigger complete specific course
        do_action( 'gamipress_wpep_complete_specific_course', $course_id, $user_id, $lesson_id, $is_completed );

    }

}
add_action( 'wpep_user_activity_lesson_status', 'gamipress_wpep_lesson_listener', 10, 3 );

/**
 * Assessment listener
 *
 * @since 1.0.0
 *
 * @param $status
 * @param $assessment_id
 * @param $question_id
 * @param $question_answer_id
 * @param $user_id
 */
function gamipress_wpep_assessment_listener( $status, $assessment_id, $question_id, $question_answer_id, $user_id ) {

    // Bail if user is not set
    if( ! $user_id )
        return;

    // Check completed constant
    $complete_status = 'completed';
    if( defined('WPEP_USER_ASSESSMENT_STATUS_COMPLETED') )
        $complete_status = WPEP_USER_ASSESSMENT_STATUS_COMPLETED;

    // Check failed constant
    $failed_status = 'failed';
    if( defined('WPEP_USER_ASSESSMENT_STATUS_FAILED') )
        $failed_status = WPEP_USER_ASSESSMENT_STATUS_FAILED;

    if( $status === $complete_status ) {

        // Trigger complete any assessment
        do_action( 'gamipress_wpep_complete_assessment', $assessment_id, $user_id, $question_id, $question_answer_id, $status );

        // Trigger complete specific assessment
        do_action( 'gamipress_wpep_complete_specific_assessment', $assessment_id, $user_id, $question_id, $question_answer_id, $status );

    } else if( $status === $failed_status ) {

        // Trigger fail any assessment
        do_action( 'gamipress_wpep_fail_assessment', $assessment_id, $user_id, $question_id, $question_answer_id, $status );

        // Trigger fail specific assessment
        do_action( 'gamipress_wpep_fail_specific_assessment', $assessment_id, $user_id, $question_id, $question_answer_id, $status );

    }

}
add_action( 'wpep_user_set_assessment_data_' . $gamipress_wpep_assessment_status, 'gamipress_wpep_assessment_listener', 10, 5 );

/**
 * Assessment score listener
 *
 * @since 1.0.0
 *
 * @param $grading
 * @param $assessment_id
 * @param $question_id
 * @param $question_answer_id
 * @param $user_id
 */
function gamipress_wpep_assessment_score_listener( $grading, $assessment_id, $question_id, $question_answer_id, $user_id ) {

    // Bail if user is not set
    if( ! $user_id )
        return;

    $grading = absint( $grading );

    // Bail if no score given
    if( $grading === 0 )
        return;

    // Min grade

    // Trigger complete any assessment with a min grade
    do_action( 'gamipress_wpep_complete_assessment_min_grade', $assessment_id, $user_id, $question_id, $question_answer_id, $grading );

    // Trigger complete specific assessment with a min grade
    do_action( 'gamipress_wpep_complete_specific_assessment_min_grade', $assessment_id, $user_id, $question_id, $question_answer_id, $grading );

    // Max grade

    // Trigger complete any assessment with a max grade
    do_action( 'gamipress_wpep_complete_assessment_max_grade', $assessment_id, $user_id, $question_id, $question_answer_id, $grading );

    // Trigger complete specific assessment with a max grade
    do_action( 'gamipress_wpep_complete_specific_assessment_max_grade', $assessment_id, $user_id, $question_id, $question_answer_id, $grading );

    // Between grade

    // Trigger complete any assessment between grades
    do_action( 'gamipress_wpep_complete_assessment_between_grade', $assessment_id, $user_id, $question_id, $question_answer_id, $grading );

    // Trigger complete specific assessment between grades
    do_action( 'gamipress_wpep_complete_specific_assessment_between_grade', $assessment_id, $user_id, $question_id, $question_answer_id, $grading );

}
add_action( 'wpep_user_set_assessment_data_' . $gamipress_wpep_assessment_score, 'gamipress_wpep_assessment_score_listener', 10, 5 );