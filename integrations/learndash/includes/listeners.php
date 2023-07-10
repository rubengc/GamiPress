<?php
/**
 * Listeners
 *
 * @package GamiPress\LearnDash\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

$quiz_submitted_hook = ( defined( 'LEARNDASH_VERSION' ) && version_compare( LEARNDASH_VERSION, '3.0.0', '>=' ) ? 'learndash_quiz_submitted' : 'learndash_quiz_completed' );

/**
 * Complete quiz
 *
 * @since 1.0.0
 *
 * @param array $quiz_data array(
 *      'course' => WP_Post,
 *      'quiz' => WP_Post,
 *      'pass' => integer,
 *      'percentage' => integer,
 * )
 * @param WP_User $current_user
 */
function gamipress_ld_complete_quiz_listener( $quiz_data, $current_user ) {

    $quiz_id = gamipress_ld_get_post_id( $quiz_data['quiz'] );
    $course_id = gamipress_ld_get_post_id( $quiz_data['course'] );

    // Complete any quiz
    do_action( 'gamipress_ld_complete_quiz', $quiz_id, $current_user->ID, $course_id, $quiz_data );

    // Complete specific quiz
    do_action( 'gamipress_ld_complete_specific_quiz', $quiz_id, $current_user->ID, $course_id, $quiz_data );

    if( $course_id ) {
        // Complete any quiz of a specific course
        do_action( 'gamipress_ld_complete_quiz_specific_course', $quiz_id, $current_user->ID, $course_id, $quiz_data );

        // Course category
        $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_category' );
        if( ! empty( $terms_ids ) )
            do_action( 'gamipress_ld_complete_quiz_course_category', $quiz_id, $current_user->ID, $course_id, $terms_ids, $quiz_data );

        // Course tag
        $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_tag' );
        if( ! empty( $terms_ids ) )
            do_action( 'gamipress_ld_complete_quiz_course_tag', $quiz_id, $current_user->ID, $course_id, $terms_ids, $quiz_data );

    }
}
add_action( $quiz_submitted_hook, 'gamipress_ld_complete_quiz_listener', 10, 2 );

/**
 * Complete quiz at minimum grade
 *
 * @since 1.0.0
 *
 * @param array $quiz_data array(
 *      'course' => WP_Post,
 *      'quiz' => WP_Post,
 *      'pass' => integer,
 *      'percentage' => integer,
 * )
 * @param WP_User $current_user
 */
function gamipress_ld_complete_quiz_min_grade_listener( $quiz_data, $current_user ) {

    $quiz_id = gamipress_ld_get_post_id( $quiz_data['quiz'] );
    $course_id = gamipress_ld_get_post_id( $quiz_data['course'] );
    $score = absint( $quiz_data['percentage'] );

    // Complete any quiz with a minimum percent grade
    do_action( 'gamipress_ld_complete_quiz_grade', $quiz_id, $current_user->ID, $course_id, $score, $quiz_data );

    // Complete specific quiz with a minimum percent grade
    do_action( 'gamipress_ld_complete_specific_quiz_grade', $quiz_id, $current_user->ID, $course_id, $score, $quiz_data );

    if( $course_id ) {
        // Complete any quiz of a specific course with a minimum percent grade
        do_action( 'gamipress_ld_complete_quiz_specific_course_grade', $quiz_id, $current_user->ID, $course_id, $score, $quiz_data );

        // Course category
        $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_category' );
        if( ! empty( $terms_ids ) )
            do_action( 'gamipress_ld_complete_quiz_course_category_grade', $quiz_id, $current_user->ID, $course_id, $score, $terms_ids, $quiz_data );

        // Course tag
        $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_tag' );
        if( ! empty( $terms_ids ) )
            do_action( 'gamipress_ld_complete_quiz_course_tag_grade', $quiz_id, $current_user->ID, $course_id, $score, $terms_ids, $quiz_data );
    }

}
add_action( $quiz_submitted_hook, 'gamipress_ld_complete_quiz_min_grade_listener', 10, 2 );

/**
 * Complete quiz at maximum grade
 *
 * @since 1.0.0
 *
 * @param array $quiz_data array(
 *      'course' => WP_Post,
 *      'quiz' => WP_Post,
 *      'pass' => integer,
 *      'percentage' => integer,
 * )
 * @param WP_User $current_user
 */
function gamipress_ld_complete_quiz_max_grade_listener( $quiz_data, $current_user ) {

    $quiz_id = gamipress_ld_get_post_id( $quiz_data['quiz'] );
    $course_id = gamipress_ld_get_post_id( $quiz_data['course'] );
    $score = absint( $quiz_data['percentage'] );

    // Complete any quiz with a maximum percent grade
    do_action( 'gamipress_ld_complete_quiz_max_grade', $quiz_id, $current_user->ID, $course_id, $score, $quiz_data );

    // Complete specific quiz with a maximum percent grade
    do_action( 'gamipress_ld_complete_specific_quiz_max_grade', $quiz_id, $current_user->ID, $course_id, $score, $quiz_data );

    if( $course_id ) {
        // Complete any quiz of a specific course with a maximum percent grade
        do_action( 'gamipress_ld_complete_quiz_specific_course_max_grade', $quiz_id, $current_user->ID, $course_id, $score, $quiz_data );

        // Course category
        $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_category' );
        if( ! empty( $terms_ids ) )
            do_action( 'gamipress_ld_complete_quiz_course_category_max_grade', $quiz_id, $current_user->ID, $course_id, $score, $terms_ids, $quiz_data );

        // Course tag
        $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_tag' );
        if( ! empty( $terms_ids ) )
            do_action( 'gamipress_ld_complete_quiz_course_tag_max_grade', $quiz_id, $current_user->ID, $course_id, $score, $terms_ids, $quiz_data );
    }

}
add_action( $quiz_submitted_hook, 'gamipress_ld_complete_quiz_max_grade_listener', 10, 2 );

/**
 * Complete quiz between grades
 *
 * @since 1.0.0
 *
 * @param array $quiz_data array(
 *      'course' => WP_Post,
 *      'quiz' => WP_Post,
 *      'pass' => integer,
 *      'percentage' => integer,
 * )
 * @param WP_User $current_user
 */
function gamipress_ld_complete_quiz_between_grade_listener( $quiz_data, $current_user ) {

    $quiz_id = gamipress_ld_get_post_id( $quiz_data['quiz'] );
    $course_id = gamipress_ld_get_post_id( $quiz_data['course'] );
    $score = absint( $quiz_data['percentage'] );

    // Complete any quiz on a range of percent grade
    do_action( 'gamipress_ld_complete_quiz_between_grade', $quiz_id, $current_user->ID, $course_id, $score, $quiz_data );

    // Complete specific quiz on a range of percent grade
    do_action( 'gamipress_ld_complete_specific_quiz_between_grade', $quiz_id, $current_user->ID, $course_id, $score, $quiz_data );

    if( $course_id ) {
        // Complete any quiz of a specific course on a range of percent grade
        do_action( 'gamipress_ld_complete_quiz_specific_course_between_grade', $quiz_id, $current_user->ID, $course_id, $score, $quiz_data );

        // Course category
        $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_category' );
        if( ! empty( $terms_ids ) )
            do_action( 'gamipress_ld_complete_quiz_course_category_between_grade', $quiz_id, $current_user->ID, $course_id, $score, $terms_ids, $quiz_data );

        // Course tag
        $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_tag' );
        if( ! empty( $terms_ids ) )
            do_action( 'gamipress_ld_complete_quiz_course_tag_between_grade', $quiz_id, $current_user->ID, $course_id, $score, $terms_ids, $quiz_data );
    }

}
add_action( $quiz_submitted_hook, 'gamipress_ld_complete_quiz_between_grade_listener', 10, 2 );

/**
 * Pass quiz
 *
 * @since 1.0.0
 *
 * @param array $quiz_data array(
 *      'course' => WP_Post,
 *      'quiz' => WP_Post,
 *      'pass' => integer,
 *      'percentage' => integer,
 * )
 * @param WP_User $current_user
 */
function gamipress_ld_pass_quiz_listener( $quiz_data, $current_user ) {

    $quiz_id = gamipress_ld_get_post_id( $quiz_data['quiz'] );
    $course_id = gamipress_ld_get_post_id( $quiz_data['course'] );

    // If user has successfully passed the quiz
    if( $quiz_data['pass'] ) {

        // Pass any quiz
        do_action( 'gamipress_ld_pass_quiz', $quiz_id, $current_user->ID, $course_id, $quiz_data );

        // Pass specific quiz
        do_action( 'gamipress_ld_pass_specific_quiz', $quiz_id, $current_user->ID, $course_id, $quiz_data );

        if( $course_id ) {
            // Pass any quiz of a specific course
            do_action( 'gamipress_ld_pass_quiz_specific_course', $quiz_id, $current_user->ID, $course_id, $quiz_data );

            // Course category
            $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_category' );
            if( ! empty( $terms_ids ) )
                do_action( 'gamipress_ld_pass_quiz_course_category', $quiz_id, $current_user->ID, $course_id, $terms_ids, $quiz_data );

            // Course tag
            $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_tag' );
            if( ! empty( $terms_ids ) )
                do_action( 'gamipress_ld_pass_quiz_course_tag', $quiz_id, $current_user->ID, $course_id, $terms_ids, $quiz_data );
        }
    }

}
add_action( $quiz_submitted_hook, 'gamipress_ld_pass_quiz_listener', 10, 2 );

/**
 * Fail quiz
 *
 * @since 1.0.0
 *
 * @param array $quiz_data array(
 *      'course' => WP_Post,
 *      'quiz' => WP_Post,
 *      'pass' => integer,
 *      'percentage' => integer,
 * )
 * @param WP_User $current_user
 */
function gamipress_ld_fail_quiz_listener( $quiz_data, $current_user ) {

    $quiz_id = gamipress_ld_get_post_id( $quiz_data['quiz'] );
    $course_id = gamipress_ld_get_post_id( $quiz_data['course'] );

    // If user has failed the quiz
    if( ! $quiz_data['pass'] ) {

        // Fail any quiz
        do_action( 'gamipress_ld_fail_quiz', $quiz_id, $current_user->ID, $course_id, $quiz_data );

        // Fail specific quiz
        do_action( 'gamipress_ld_fail_specific_quiz', $quiz_id, $current_user->ID, $course_id, $quiz_data );

        if( $course_id ) {
            // Fail any quiz of a specific course
            do_action( 'gamipress_ld_fail_quiz_specific_course', $quiz_id, $current_user->ID, $course_id, $quiz_data );

            // Course category
            $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_category' );
            if( ! empty( $terms_ids ) )
                do_action( 'gamipress_ld_fail_quiz_course_category', $quiz_id, $current_user->ID, $course_id, $terms_ids, $quiz_data );

            // Course tag
            $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_tag' );
            if( ! empty( $terms_ids ) )
                do_action( 'gamipress_ld_fail_quiz_course_tag', $quiz_id, $current_user->ID, $course_id, $terms_ids, $quiz_data );
        }
    }

}
add_action( $quiz_submitted_hook, 'gamipress_ld_fail_quiz_listener', 10, 2 );

/**
 * Complete topic
 *
 * @since 1.0.0
 *
 * @param array $args array(
 *      'user' => WP_User,
 *      'course' => WP_Post,
 *      'lesson' => WP_Post,
 *      'topic' => WP_Post,
 *      'progress' => array,
 * )
 */
function gamipress_ld_complete_topic( $args ) {

    $user_id = $args['user']->ID;

    $topic_id = gamipress_ld_get_post_id( $args['topic'] );
    $course_id = gamipress_ld_get_post_id( $args['course'] );
    $lesson_id = gamipress_ld_get_post_id( $args['lesson'] );

    // Complete any topic
    do_action( 'gamipress_ld_complete_topic', $topic_id, $user_id, $lesson_id, $course_id, $args );

    // Complete specific topic
    do_action( 'gamipress_ld_complete_specific_topic', $topic_id, $user_id, $lesson_id, $course_id, $args );

    // Topic category
    $terms_ids = gamipress_ld_get_term_ids( $topic_id, 'ld_topic_category' );
    if( ! empty( $terms_ids ) )
        do_action( 'gamipress_ld_complete_topic_category', $topic_id, $user_id, $lesson_id, $course_id, $terms_ids, $args );

    // Topic tag
    $terms_ids = gamipress_ld_get_term_ids( $topic_id, 'ld_topic_tag' );
    if( ! empty( $terms_ids ) )
        do_action( 'gamipress_ld_complete_topic_tag', $topic_id, $user_id, $lesson_id, $course_id, $terms_ids, $args );

    if( $course_id ) {
        // Complete any topic of a specific course
        do_action( 'gamipress_ld_complete_topic_specific_course', $topic_id, $user_id, $lesson_id, $course_id, $args );

        // Course category
        $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_category' );
        if( ! empty( $terms_ids ) )
            do_action( 'gamipress_ld_complete_topic_course_category', $topic_id, $user_id, $lesson_id, $course_id, $terms_ids, $args );

        // Course tag
        $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_tag' );
        if( ! empty( $terms_ids ) )
            do_action( 'gamipress_ld_complete_topic_course_tag', $topic_id, $user_id, $lesson_id, $course_id, $terms_ids, $args );
    }

}
add_action( 'learndash_topic_completed', 'gamipress_ld_complete_topic' );

/**
 * Complete lesson
 *
 * @since 1.0.0
 *
 * @param array $args array(
 *      'user' => WP_User,
 *      'course' => WP_Post,
 *      'lesson' => WP_Post,
 *      'progress' => array,
 * )
 */
function gamipress_ld_complete_lesson( $args ) {

    $user_id = $args['user']->ID;

    $course_id = gamipress_ld_get_post_id( $args['course'] );
    $lesson_id = gamipress_ld_get_post_id( $args['lesson'] );

    // Complete any lesson
    do_action( 'gamipress_ld_complete_lesson', $lesson_id, $user_id, $course_id, $args );

    // Complete specific lesson
    do_action( 'gamipress_ld_complete_specific_lesson', $lesson_id, $user_id, $course_id, $args );

    // Lesson category
    $terms_ids = gamipress_ld_get_term_ids( $lesson_id, 'ld_lesson_category' );
    if( ! empty( $terms_ids ) )
        do_action( 'gamipress_ld_complete_lesson_category', $lesson_id, $user_id, $course_id, $terms_ids, $args );

    // Lesson tag
    $terms_ids = gamipress_ld_get_term_ids( $lesson_id, 'ld_lesson_tag' );
    if( ! empty( $terms_ids ) )
        do_action( 'gamipress_ld_complete_lesson_tag', $lesson_id, $user_id, $course_id, $terms_ids, $args );

    if( $course_id ) {
        // Complete any lesson of a specific course
        do_action( 'gamipress_ld_complete_lesson_specific_course', $lesson_id, $user_id, $course_id, $args );

        // Course category
        $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_category' );
        if( ! empty( $terms_ids ) )
            do_action( 'gamipress_ld_complete_lesson_course_category', $lesson_id, $user_id, $course_id, $terms_ids, $args );

        // Course tag
        $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_tag' );
        if( ! empty( $terms_ids ) )
            do_action( 'gamipress_ld_complete_lesson_course_tag', $lesson_id, $user_id, $course_id, $terms_ids, $args );
    }

}
add_action( 'learndash_lesson_completed', 'gamipress_ld_complete_lesson' );

/**
 * Mark lesson incomplete
 *
 * @since 1.0.0
 *
 * @param int $user_id      User ID.
 * @param int $course_id    Course ID.
 * @param int $lesson_id    Lesson ID.
 */
function gamipress_ld_incomplete_lesson( $user_id, $course_id, $lesson_id ) {

    // Bail if course_id and lesson_id have the same value
    if ( $course_id === $lesson_id ) {
        return;
    }

    // Mark incomplete any lesson
    do_action( 'gamipress_ld_incomplete_lesson', $lesson_id, $user_id, $course_id );

    // Mark incomplete specific lesson
    do_action( 'gamipress_ld_incomplete_specific_lesson', $lesson_id, $user_id, $course_id );

}
add_action( 'learndash_mark_incomplete_process', 'gamipress_ld_incomplete_lesson', 10, 3 );

/**
 * Enroll course
 *
 * @since 1.0.0
 *
 * @param  int  	$user_id
 * @param  int  	$course_id
 * @param  array  	$course_access_list
 * @param  bool  	$remove
 */
function gamipress_ld_enroll_course( $user_id, $course_id, $course_access_list, $remove ) {

    // Bail if hasn't been enrolled
    if ( ! empty( $remove ) ) {
        return;
    }

    // Enroll any course
    do_action( 'gamipress_ld_enroll_course', $course_id, $user_id );

    // Enroll specific course
    do_action( 'gamipress_ld_enroll_specific_course', $course_id, $user_id );

    // Course category
    $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_category' );
    if( ! empty( $terms_ids ) )
        do_action( 'gamipress_ld_enroll_course_category', $course_id, $user_id, $terms_ids );

    // Course tag
    $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_tag' );
    if( ! empty( $terms_ids ) )
        do_action( 'gamipress_ld_enroll_course_tag', $course_id, $user_id, $terms_ids );

}
add_action( 'learndash_update_course_access', 'gamipress_ld_enroll_course', 10, 4 );

/**
 * Complete course
 *
 * @since 1.0.0
 *
 * @param array $args array(
 *      'user' => WP_User,
 *      'course' => WP_Post,
 *      'progress' => array,
 * )
 */
function gamipress_ld_complete_course( $args ) {

    $user_id = $args['user']->ID;

    $course_id = gamipress_ld_get_post_id( $args['course'] );

    // Complete any course
    do_action( 'gamipress_ld_complete_course', $course_id, $user_id, $args );

    // Complete specific course
    do_action( 'gamipress_ld_complete_specific_course', $course_id, $user_id, $args );

    // Course category
    $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_category' );
    if( ! empty( $terms_ids ) )
        do_action( 'gamipress_ld_complete_course_category', $course_id, $user_id, $terms_ids, $args );

    // Course tag
    $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_tag' );
    if( ! empty( $terms_ids ) )
        do_action( 'gamipress_ld_complete_course_tag', $course_id, $user_id, $terms_ids, $args );

}
add_action( 'learndash_course_completed', 'gamipress_ld_complete_course' );

/**
 * Assignment uploaded
 *
 * @since 1.1.3
 *
 * @param int 		$assignment_id 	    Newly created assignment post ID which the assignment is uploaded to
 * @param array 	$assignment_meta    Assignment meta data: array(
 *      'user_id' => int,
 *      'lesson_id' => int,
 *      'course_id' => int
 * )
 */
function gamipress_ld_assignment_upload( $assignment_id, $assignment_meta ) {

    $user_id = $assignment_meta['user_id'];
    $lesson_id = $assignment_meta['lesson_id'];
    $course_id = $assignment_meta['course_id'];

    // Upload an assignment
    do_action( 'gamipress_ld_assignment_upload', $assignment_id, $user_id, $lesson_id, $course_id, $assignment_meta );

    // Upload an assignment to a specific lesson
    do_action( 'gamipress_ld_assignment_upload_specific_lesson', $assignment_id, $user_id, $lesson_id, $course_id, $assignment_meta );

    // Lesson category
    $terms_ids = gamipress_ld_get_term_ids( $lesson_id, 'ld_lesson_category' );
    if( ! empty( $terms_ids ) )
        do_action( 'gamipress_ld_assignment_upload_lesson_category', $assignment_id, $user_id, $lesson_id, $course_id, $terms_ids, $assignment_meta );

    // Lesson tag
    $terms_ids = gamipress_ld_get_term_ids( $lesson_id, 'ld_lesson_tag' );
    if( ! empty( $terms_ids ) )
        do_action( 'gamipress_ld_assignment_upload_lesson_tag', $assignment_id, $user_id, $lesson_id, $course_id, $terms_ids, $assignment_meta );

    // Upload an assignment to a specific course
    do_action( 'gamipress_ld_assignment_upload_specific_course', $assignment_id, $user_id, $lesson_id, $course_id, $assignment_meta );

    // Course category
    $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_category' );
    if( ! empty( $terms_ids ) )
        do_action( 'gamipress_ld_assignment_upload_course_category', $assignment_id, $user_id, $lesson_id, $course_id, $terms_ids, $assignment_meta );

    // Course tag
    $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_tag' );
    if( ! empty( $terms_ids ) )
        do_action( 'gamipress_ld_assignment_upload_course_tag', $assignment_id, $user_id, $lesson_id, $course_id, $terms_ids, $assignment_meta );

}
add_action( 'learndash_assignment_uploaded', 'gamipress_ld_assignment_upload', 10, 2 );

/**
 * Assignment approved
 *
 * @since 1.1.3
 *
 * @param int 		$assignment_id 	    Newly created assignment post ID which the assignment is uploaded to
 */
function gamipress_ld_approve_assignment( $assignment_id ) {

    $assignment = get_post( $assignment_id );

    if( $assignment ) {

        $user_id = $assignment->post_author;
        $lesson_id = get_post_meta( $assignment_id, 'lesson_id', true );
        $course_id = get_post_meta( $assignment_id, 'course_id', true );

        // Approve an assignment
        do_action( 'gamipress_ld_approve_assignment', $assignment_id, $user_id, $lesson_id, $course_id );

        // Approve an assignment of a specific lesson
        do_action( 'gamipress_ld_approve_assignment_specific_lesson', $assignment_id, $user_id, $lesson_id, $course_id );

        // Lesson category
        $terms_ids = gamipress_ld_get_term_ids( $lesson_id, 'ld_lesson_category' );
        if( ! empty( $terms_ids ) )
            do_action( 'gamipress_ld_approve_assignment_lesson_category', $assignment_id, $user_id, $lesson_id, $course_id, $terms_ids );

        // Lesson tag
        $terms_ids = gamipress_ld_get_term_ids( $lesson_id, 'ld_lesson_tag' );
        if( ! empty( $terms_ids ) )
            do_action( 'gamipress_ld_approve_assignment_lesson_tag', $assignment_id, $user_id, $lesson_id, $course_id, $terms_ids );

        // Approve an assignment of a specific course
        do_action( 'gamipress_ld_approve_assignment_specific_course', $assignment_id, $user_id, $lesson_id, $course_id );

        // Course category
        $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_category' );
        if( ! empty( $terms_ids ) )
            do_action( 'gamipress_ld_approve_assignment_course_category', $assignment_id, $user_id, $lesson_id, $course_id, $terms_ids );

        // Course tag
        $terms_ids = gamipress_ld_get_term_ids( $course_id, 'ld_course_tag' );
        if( ! empty( $terms_ids ) )
            do_action( 'gamipress_ld_approve_assignment_course_tag', $assignment_id, $user_id, $lesson_id, $course_id, $terms_ids );

    }

}
add_action( 'learndash_assignment_approved', 'gamipress_ld_approve_assignment' );

/**
 * Join group
 *
 * @since 1.1.3
 *
 * @param int $user_id
 * @param int $group_id
 */
function gamipress_ld_join_group( $user_id, $group_id ) {

    // Join a group
    do_action( 'gamipress_ld_join_group', $group_id, $user_id );

    // Join a specific group
    do_action( 'gamipress_ld_join_specific_group', $group_id, $user_id );

}
add_action( 'ld_added_group_access', 'gamipress_ld_join_group', 10, 2 );
