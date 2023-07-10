<?php
/**
 * Triggers
 *
 * @package GamiPress\LearnDash\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register LearnDash specific triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_ld_activity_triggers( $triggers ) {

    $triggers[__( 'LearnDash', 'gamipress' )] = array(

        // Quizzes
        'gamipress_ld_complete_quiz'                    => __( 'Complete a quiz', 'gamipress' ),
        'gamipress_ld_complete_specific_quiz'           => __( 'Complete a specific quiz', 'gamipress' ),
        'gamipress_ld_complete_quiz_specific_course'    => __( 'Complete a quiz of a specific course', 'gamipress' ),
            // Course taxonomies
            'gamipress_ld_complete_quiz_course_category'    => __( 'Complete a quiz of a course of a category', 'gamipress' ),
            'gamipress_ld_complete_quiz_course_tag'         => __( 'Complete a quiz of a course of a tag', 'gamipress' ),

        // Minimum grade
        'gamipress_ld_complete_quiz_grade'                  => __( 'Complete a quiz with a minimum percent grade', 'gamipress' ),
        'gamipress_ld_complete_specific_quiz_grade'         => __( 'Complete a specific quiz with a minimum percent grade', 'gamipress' ),
        'gamipress_ld_complete_quiz_specific_course_grade'  => __( 'Complete a quiz of a specific course with a minimum percent grade', 'gamipress' ),
            // Course taxonomies
            'gamipress_ld_complete_quiz_course_category_grade'  => __( 'Complete a quiz of a course of a category with a minimum percent grade', 'gamipress' ),
            'gamipress_ld_complete_quiz_course_tag_grade'       => __( 'Complete a quiz of a course of a tag with a minimum percent grade', 'gamipress' ),

        // Maximum grade
        'gamipress_ld_complete_quiz_max_grade'                  => __( 'Complete a quiz with a maximum percent grade', 'gamipress' ),
        'gamipress_ld_complete_specific_quiz_max_grade'         => __( 'Complete a specific quiz with a maximum percent grade', 'gamipress' ),
        'gamipress_ld_complete_quiz_specific_course_max_grade'  => __( 'Complete a quiz of a specific course with a maximum percent grade', 'gamipress' ),
            // Course taxonomies
            'gamipress_ld_complete_quiz_course_category_max_grade'  => __( 'Complete a quiz of a course of a category with a maximum percent grade', 'gamipress' ),
            'gamipress_ld_complete_quiz_course_tag_max_grade'       => __( 'Complete a quiz of a course of a tag with a maximum percent grade', 'gamipress' ),

        // Between grades
        'gamipress_ld_complete_quiz_between_grade'                  => __( 'Complete a quiz on a range of percent grade', 'gamipress' ),
        'gamipress_ld_complete_specific_quiz_between_grade'         => __( 'Complete a specific quiz on a range of percent grade', 'gamipress' ),
        'gamipress_ld_complete_quiz_specific_course_between_grade'  => __( 'Complete a quiz of a specific course on a range of percent grade', 'gamipress' ),
            // Course taxonomies
            'gamipress_ld_complete_quiz_course_category_between_grade'  => __( 'Complete a quiz of a course of a category on a range of percent grade', 'gamipress' ),
            'gamipress_ld_complete_quiz_course_tag_between_grade'  => __( 'Complete a quiz of a course of a tag on a range of percent grade', 'gamipress' ),

        // Pass
        'gamipress_ld_pass_quiz'                    => __( 'Successfully pass a quiz', 'gamipress' ),
        'gamipress_ld_pass_specific_quiz'           => __( 'Successfully pass a specific quiz', 'gamipress' ),
        'gamipress_ld_pass_quiz_specific_course'    => __( 'Successfully pass a quiz of a specific course', 'gamipress' ),
            // Course taxonomies
            'gamipress_ld_pass_quiz_course_category'    => __( 'Successfully pass a quiz of a course of a category', 'gamipress' ),
            'gamipress_ld_pass_quiz_course_tag'    => __( 'Successfully pass a quiz of a course of a tag', 'gamipress' ),

        // Fail
        'gamipress_ld_fail_quiz'                    => __( 'Fail a quiz', 'gamipress' ),
        'gamipress_ld_fail_specific_quiz'           => __( 'Fail a specific quiz', 'gamipress' ),
        'gamipress_ld_fail_quiz_specific_course'    => __( 'Fail a quiz of a specific course', 'gamipress' ),
            // Course taxonomies
            'gamipress_ld_fail_quiz_course_category'    => __( 'Fail a quiz of a course of a category', 'gamipress' ),
            'gamipress_ld_fail_quiz_course_tag'    => __( 'Fail a quiz of a course of a tag', 'gamipress' ),

        // Topics
        'gamipress_ld_complete_topic'                   => __( 'Complete a topic', 'gamipress' ),
        'gamipress_ld_complete_specific_topic'          => __( 'Complete a specific topic', 'gamipress' ),
            // Topic taxonomies
            'gamipress_ld_complete_topic_category'          => __( 'Complete a topic of a category', 'gamipress' ),
            'gamipress_ld_complete_topic_tag'          => __( 'Complete a topic of a tag', 'gamipress' ),
        'gamipress_ld_complete_topic_specific_course'   => __( 'Complete a topic of a specific course', 'gamipress' ),
            // Course taxonomies
            'gamipress_ld_complete_topic_course_category'   => __( 'Complete a topic of a course of a category', 'gamipress' ),
            'gamipress_ld_complete_topic_course_tag'   => __( 'Complete a topic of a course of a tag', 'gamipress' ),

        // Assignments
        'gamipress_ld_assignment_upload'                    => __( 'Upload an assignment', 'gamipress' ),
        'gamipress_ld_assignment_upload_specific_lesson'    => __( 'Upload an assignment to a specific lesson', 'gamipress' ),
            // Lesson taxonomies
            'gamipress_ld_assignment_upload_lesson_category'    => __( 'Upload an assignment to a lesson of a category', 'gamipress' ),
            'gamipress_ld_assignment_upload_lesson_tag'    => __( 'Upload an assignment to a lesson of a tag', 'gamipress' ),
        'gamipress_ld_assignment_upload_specific_course'    => __( 'Upload an assignment to a specific course', 'gamipress' ),
            // Course taxonomies
            'gamipress_ld_assignment_upload_course_category'    => __( 'Upload an assignment to a course of a category', 'gamipress' ),
            'gamipress_ld_assignment_upload_course_tag'    => __( 'Upload an assignment to a course of a tag', 'gamipress' ),
        'gamipress_ld_approve_assignment'                   => __( 'Approve an assignment', 'gamipress' ),
        'gamipress_ld_approve_assignment_specific_lesson'   => __( 'Approve an assignment of a specific lesson', 'gamipress' ),
            // Lesson taxonomies
            'gamipress_ld_approve_assignment_lesson_category'   => __( 'Approve an assignment of a lesson of a category', 'gamipress' ),
            'gamipress_ld_approve_assignment_lesson_tag'   => __( 'Approve an assignment of a lesson of a tag', 'gamipress' ),
        'gamipress_ld_approve_assignment_specific_course'   => __( 'Approve an assignment of a specific course', 'gamipress' ),
            // Course taxonomies
            'gamipress_ld_approve_assignment_course_category'   => __( 'Approve an assignment of a course of a category', 'gamipress' ),
            'gamipress_ld_approve_assignment_course_tag'   => __( 'Approve an assignment of a course of a tag', 'gamipress' ),

        // Lessons
        'gamipress_ld_complete_lesson'                  => __( 'Complete a lesson', 'gamipress' ),
        'gamipress_ld_complete_specific_lesson'         => __( 'Complete a specific lesson', 'gamipress' ),
        'gamipress_ld_incomplete_lesson'                  => __( 'Mark as incomplete a lesson', 'gamipress' ),
        'gamipress_ld_incomplete_specific_lesson'         => __( 'Mark as incomplete a specific lesson', 'gamipress' ),

            // Lesson taxonomies
            'gamipress_ld_complete_lesson_category'         => __( 'Complete a lesson of a category', 'gamipress' ),
            'gamipress_ld_complete_lesson_tag'         => __( 'Complete a lesson of a tag', 'gamipress' ),
        'gamipress_ld_complete_lesson_specific_course'  => __( 'Complete a lesson of a specific course', 'gamipress' ),
            // Course taxonomies
            'gamipress_ld_complete_lesson_course_category'  => __( 'Complete a lesson of a course of a category', 'gamipress' ),
            'gamipress_ld_complete_lesson_course_tag'  => __( 'Complete a lesson of a course of a tag', 'gamipress' ),

        // Courses
        'gamipress_ld_enroll_course'            => __( 'Enroll in a course', 'gamipress' ),
        'gamipress_ld_enroll_specific_course'   => __( 'Enroll in a specific course', 'gamipress' ),
            // Course taxonomies
            'gamipress_ld_enroll_course_category'   => __( 'Enroll in a course of a category', 'gamipress' ),
            'gamipress_ld_enroll_course_tag'   => __( 'Enroll in a course of a tag', 'gamipress' ),
        'gamipress_ld_complete_course'          => __( 'Complete a course', 'gamipress' ),
        'gamipress_ld_complete_specific_course' => __( 'Complete a specific course', 'gamipress' ),
            // Course taxonomies
            'gamipress_ld_complete_course_category' => __( 'Complete a course of a category', 'gamipress' ),
            'gamipress_ld_complete_course_tag' => __( 'Complete a course of a tag', 'gamipress' ),

        // Groups
        'gamipress_ld_join_group'            => __( 'Join any group', 'gamipress' ),
        'gamipress_ld_join_specific_group'   => __( 'Join a specific group', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_ld_activity_triggers' );

/**
 * Register LearnDash specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_ld_specific_activity_triggers( $specific_activity_triggers ) {

    // Quizzes
    $specific_activity_triggers['gamipress_ld_complete_specific_quiz'] = array( 'sfwd-quiz' );
    $specific_activity_triggers['gamipress_ld_complete_quiz_specific_course'] = array( 'sfwd-courses' );

    $specific_activity_triggers['gamipress_ld_complete_specific_quiz_grade'] = array( 'sfwd-quiz' );
    $specific_activity_triggers['gamipress_ld_complete_quiz_specific_course_grade'] = array( 'sfwd-courses' );

    $specific_activity_triggers['gamipress_ld_complete_specific_quiz_max_grade'] = array( 'sfwd-quiz' );
    $specific_activity_triggers['gamipress_ld_complete_quiz_specific_course_max_grade'] = array( 'sfwd-courses' );

    $specific_activity_triggers['gamipress_ld_complete_specific_quiz_between_grade'] = array( 'sfwd-quiz' );
    $specific_activity_triggers['gamipress_ld_complete_quiz_specific_course_between_grade'] = array( 'sfwd-courses' );

    $specific_activity_triggers['gamipress_ld_pass_specific_quiz'] = array( 'sfwd-quiz' );
    $specific_activity_triggers['gamipress_ld_pass_quiz_specific_course'] = array( 'sfwd-courses' );

    $specific_activity_triggers['gamipress_ld_fail_specific_quiz'] = array( 'sfwd-quiz' );
    $specific_activity_triggers['gamipress_ld_fail_quiz_specific_course'] = array( 'sfwd-courses' );

    // Topics
    $specific_activity_triggers['gamipress_ld_complete_specific_topic'] = array( 'sfwd-topic' );
    $specific_activity_triggers['gamipress_ld_complete_topic_specific_course'] = array( 'sfwd-courses' );

    // Assignments
    $specific_activity_triggers['gamipress_ld_assignment_upload_specific_lesson'] = array( 'sfwd-lessons' );
    $specific_activity_triggers['gamipress_ld_assignment_upload_specific_course'] = array( 'sfwd-courses' );

    $specific_activity_triggers['gamipress_ld_approve_assignment_specific_lesson'] = array( 'sfwd-lessons' );
    $specific_activity_triggers['gamipress_ld_approve_assignment_specific_course'] = array( 'sfwd-courses' );

    // Lessons
    $specific_activity_triggers['gamipress_ld_complete_specific_lesson'] = array( 'sfwd-lessons' );
    $specific_activity_triggers['gamipress_ld_incomplete_specific_lesson'] = array( 'sfwd-lessons' );
    $specific_activity_triggers['gamipress_ld_complete_lesson_specific_course'] = array( 'sfwd-courses' );

    // Courses
    $specific_activity_triggers['gamipress_ld_enroll_specific_course'] = array( 'sfwd-courses' );
    $specific_activity_triggers['gamipress_ld_complete_specific_course'] = array( 'sfwd-courses' );

    // Groups
    $specific_activity_triggers['gamipress_ld_join_specific_group'] = array( 'groups' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_ld_specific_activity_triggers' );

/**
 * Build custom activity trigger label
 *
 * @param string    $title
 * @param integer   $requirement_id
 * @param array     $requirement
 *
 * @return string
 */
function gamipress_ld_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $score = ( isset( $requirement['ld_score'] ) ) ? absint( $requirement['ld_score'] ) : 0;
    $min_score = ( isset( $requirement['ld_min_score'] ) ) ? absint( $requirement['ld_min_score'] ) : 0;
    $max_score = ( isset( $requirement['ld_max_score'] ) ) ? absint( $requirement['ld_max_score'] ) : 0;

    switch( $requirement['trigger_type'] ) {

        // Minimum grade events
        case 'gamipress_ld_complete_quiz_grade':
            return sprintf( __( 'Completed a quiz with a score of %d or higher', 'gamipress' ), $score );
            break;
        case 'gamipress_ld_complete_specific_quiz_grade':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Complete the quiz %s with a score of %d or higher', 'gamipress' ), get_the_title( $achievement_post_id ), $score );
            break;
        case 'gamipress_ld_complete_quiz_specific_course_grade':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Complete a quiz of the course %s with a score of %d or higher', 'gamipress' ), get_the_title( $achievement_post_id ), $score );
            break;

        // Maximum grade events
        case 'gamipress_ld_complete_quiz_max_grade':
            return sprintf( __( 'Completed a quiz with a maximum score of %d', 'gamipress' ), $score );
            break;
        case 'gamipress_ld_complete_specific_quiz_max_grade':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Complete the quiz %s with a maximum score of %d', 'gamipress' ), get_the_title( $achievement_post_id ), $score );
            break;
        case 'gamipress_ld_complete_quiz_specific_course_max_grade':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Complete a quiz of the course %s with a maximum score of %d', 'gamipress' ), get_the_title( $achievement_post_id ), $score );
            break;

        // Between grade events
        case 'gamipress_ld_complete_quiz_between_grade':
            return sprintf( __( 'Completed a quiz with a score between %d and %d', 'gamipress' ), $min_score, $max_score );
            break;
        case 'gamipress_ld_complete_specific_quiz_between_grade':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Complete the quiz %s with a score between %d and %d', 'gamipress' ), get_the_title( $achievement_post_id ), $min_score, $max_score );
            break;
        case 'gamipress_ld_complete_quiz_specific_course_between_grade':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Complete a quiz of the course %s with a score between %d and %d', 'gamipress' ), get_the_title( $achievement_post_id ), $min_score, $max_score );
            break;

        // Course category
        case 'gamipress_ld_complete_quiz_course_category':
            return sprintf( __( 'Complete a quiz of a course of "%s" category', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'category' ) );
            break;
        case 'gamipress_ld_complete_quiz_course_category_grade':
            return sprintf( __( 'Complete a quiz of a course of "%s" category with a score of %d or higher', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'category' ), $score );
            break;
        case 'gamipress_ld_complete_quiz_course_category_max_grade':
            return sprintf( __( 'Complete a quiz of a course of "%s" category with a maximum score of %d', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'category' ), $score );
            break;
        case 'gamipress_ld_complete_quiz_course_category_between_grade':
            return sprintf( __( 'Complete a quiz of a course of "%s" category with a score between %d and %d', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'category' ), $min_score, $max_score );
            break;
        case 'gamipress_ld_pass_quiz_course_category':
            return sprintf( __( 'Pass a quiz of a course of "%s" category', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'category' ) );
            break;
        case 'gamipress_ld_fail_quiz_course_category':
            return sprintf( __( 'Fail a quiz of a course of "%s" category', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'category' ) );
            break;
        case 'gamipress_ld_complete_topic_course_category':
            return sprintf( __( 'Complete a topic of a course of "%s" category', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'category' ) );
            break;
        case 'gamipress_ld_assignment_upload_course_category':
            return sprintf( __( 'Upload an assignment to a course of "%s" category', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'category' ) );
            break;
        case 'gamipress_ld_approve_assignment_course_category':
            return sprintf( __( 'Approve an assignment of a course of "%s" category', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'category' ) );
            break;
        case 'gamipress_ld_complete_lesson_course_category':
            return sprintf( __( 'Complete a lesson of a course of "%s" category', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'category' ) );
            break;
        case 'gamipress_ld_enroll_course_category':
            return sprintf( __( 'Enroll in a course of "%s" category', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'category' ) );
            break;
        case 'gamipress_ld_complete_course_category':
            return sprintf( __( 'Complete a course of "%s" category', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'category' ) );
            break;

        // Course tag
        case 'gamipress_ld_complete_quiz_course_tag':
            return sprintf( __( 'Complete a quiz of a course of "%s" tag', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'tag' ) );
            break;
        case 'gamipress_ld_complete_quiz_course_tag_grade':
            return sprintf( __( 'Complete a quiz of a course of "%s" tag with a score of %d or higher', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'tag' ), $score );
            break;
        case 'gamipress_ld_complete_quiz_course_tag_max_grade':
            return sprintf( __( 'Complete a quiz of a course of "%s" tag with a maximum score of %d', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'tag' ), $score );
            break;
        case 'gamipress_ld_complete_quiz_course_tag_between_grade':
            return sprintf( __( 'Complete a quiz of a course of "%s" tag with a score between %d and %d', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'tag' ), $min_score, $max_score );
            break;
        case 'gamipress_ld_pass_quiz_course_tag':
            return sprintf( __( 'Pass a quiz of a course of "%s" tag', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'tag' ) );
            break;
        case 'gamipress_ld_fail_quiz_course_tag':
            return sprintf( __( 'Fail a quiz of a course of "%s" tag', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'tag' ) );
            break;
        case 'gamipress_ld_complete_topic_course_tag':
            return sprintf( __( 'Complete a topic of a course of "%s" tag', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'tag' ) );
            break;
        case 'gamipress_ld_assignment_upload_course_tag':
            return sprintf( __( 'Upload an assignment to a course of "%s" tag', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'tag' ) );
            break;
        case 'gamipress_ld_approve_assignment_course_tag':
            return sprintf( __( 'Approve an assignment of a course of "%s" tag', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'tag' ) );
            break;
        case 'gamipress_ld_complete_lesson_course_tag':
            return sprintf( __( 'Complete a lesson of a course of "%s" tag', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'tag' ) );
            break;
        case 'gamipress_ld_enroll_course_tag':
            return sprintf( __( 'Enroll in a course of "%s" tag', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'tag' ) );
            break;
        case 'gamipress_ld_complete_course_tag':
            return sprintf( __( 'Complete a course of "%s" tag', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'course', 'tag' ) );
            break;

        // Topic category
        case 'gamipress_ld_complete_topic_category':
            return sprintf( __( 'Complete a topic of "%s" category', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'topic', 'category' ) );
            break;

        // Topic tag
        case 'gamipress_ld_complete_topic_tag':
            return sprintf( __( 'Complete a topic of "%s" tag', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'topic', 'tag' ) );
            break;

        // Lesson category
        case 'gamipress_ld_assignment_upload_lesson_category':
            return sprintf( __( 'Upload an assignment to a lesson of "%s" category', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'lesson', 'category' ) );
            break;
        case 'gamipress_ld_approve_assignment_lesson_category':
            return sprintf( __( 'Approve an assignment of a lesson of "%s" category', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'lesson', 'category' ) );
            break;
        case 'gamipress_ld_complete_lesson_category':
            return sprintf( __( 'Complete a lesson of "%s" category', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'lesson', 'category' ) );
            break;

        // Lesson tag
        case 'gamipress_ld_assignment_upload_lesson_tag':
            return sprintf( __( 'Upload an assignment to a lesson of "%s" tag', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'lesson', 'tag' ) );
            break;
        case 'gamipress_ld_approve_assignment_lesson_tag':
            return sprintf( __( 'Approve an assignment of a lesson of "%s" tag', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'lesson', 'tag' ) );
            break;
        case 'gamipress_ld_complete_lesson_tag':
            return sprintf( __( 'Complete a lesson of "%s" tag', 'gamipress' ), gamipress_ld_get_term_name( $requirement, 'lesson', 'tag' ) );
            break;

    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_ld_activity_trigger_label', 10, 3 );

/**
 * Helper function to get a term name
 *
 * @param array $requirement
 * @param string $element
 * @param string $taxonomy
 *
 * @return string
 */
function gamipress_ld_get_term_name( $requirement, $element = 'course', $taxonomy = 'category' ) {

    $term_id = ( isset( $requirement['ld_' . $element . '_' . $taxonomy . '_id'] ) ) ? absint( $requirement['ld_' . $element . '_' . $taxonomy . '_id'] ) : 0;

    if( $term_id !== 0 ) {

        $term = get_term_by( 'term_id', $term_id, 'ld_' . $element . '_' . $taxonomy );

        // Return the term name
        return $term->name;
    }

    return __( 'any', 'gamipress' );

}

/**
 * Register LearnDash specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_ld_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Quizzes
    $specific_activity_trigger_labels['gamipress_ld_complete_specific_quiz'] = __( 'Complete the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_ld_complete_quiz_specific_course'] = __( 'Complete any quiz of the course %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_ld_pass_specific_quiz'] = __( 'Pass the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_ld_pass_quiz_specific_course'] = __( 'Pass a quiz of the course %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_ld_fail_specific_quiz'] = __( 'Fail the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_ld_fail_quiz_specific_course'] = __( 'Fail a quiz of the course %s', 'gamipress' );

    // Topics
    $specific_activity_trigger_labels['gamipress_ld_complete_specific_topic'] = __( 'Complete the topic %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_ld_complete_topic_specific_course'] = __( 'Complete a topic of the course %s', 'gamipress' );

    // Assignments
    $specific_activity_trigger_labels['gamipress_ld_assignment_upload_specific_lesson'] = __( 'Upload an assignment to the lesson %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_ld_assignment_upload_specific_course'] = __( 'Upload an assignment to the course %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_ld_approve_assignment_specific_lesson'] = __( 'Approve an assignment of the lesson %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_ld_approve_assignment_specific_course'] = __( 'Approve an assignment of the course %s', 'gamipress' );

    // Lessons
    $specific_activity_trigger_labels['gamipress_ld_complete_specific_lesson'] = __( 'Complete the lesson %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_ld_incomplete_specific_lesson'] = __( 'Mark as incomplete the lesson %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_ld_complete_lesson_specific_course'] = __( 'Complete a lesson of the course %s', 'gamipress' );

    // Courses
    $specific_activity_trigger_labels['gamipress_ld_enroll_specific_course'] = __( 'Enroll in course %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_ld_complete_specific_course'] = __( 'Complete the course %s', 'gamipress' );

    // Groups
    $specific_activity_trigger_labels['gamipress_ld_join_specific_group'] = __( 'Join %s group', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_ld_specific_activity_trigger_label' );

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
function gamipress_ld_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Quizzes
        case 'gamipress_ld_complete_quiz':
        case 'gamipress_ld_complete_specific_quiz':
        case 'gamipress_ld_complete_quiz_specific_course':
        case 'gamipress_ld_complete_quiz_grade':
        case 'gamipress_ld_complete_specific_quiz_grade':
        case 'gamipress_ld_complete_quiz_specific_course_grade':
        case 'gamipress_ld_complete_quiz_max_grade':
        case 'gamipress_ld_complete_specific_quiz_max_grade':
        case 'gamipress_ld_complete_quiz_specific_course_max_grade':
        case 'gamipress_ld_complete_quiz_between_grade':
        case 'gamipress_ld_complete_specific_quiz_between_grade':
        case 'gamipress_ld_complete_quiz_specific_course_between_grade':
        case 'gamipress_ld_pass_quiz':
        case 'gamipress_ld_pass_specific_quiz':
        case 'gamipress_ld_pass_quiz_specific_course':
        case 'gamipress_ld_fail_quiz':
        case 'gamipress_ld_fail_specific_quiz':
        case 'gamipress_ld_fail_quiz_specific_course':

        // Topics
        case 'gamipress_ld_complete_topic':
        case 'gamipress_ld_complete_specific_topic':
        case 'gamipress_ld_complete_topic_specific_course':

        // Assignments
        case 'gamipress_ld_assignment_upload':
        case 'gamipress_ld_assignment_upload_specific_lesson':
        case 'gamipress_ld_assignment_upload_specific_course':
        case 'gamipress_ld_approve_assignment':
        case 'gamipress_ld_approve_assignment_specific_lesson':
        case 'gamipress_ld_approve_assignment_specific_course':

        // Lessons
        case 'gamipress_ld_complete_lesson':
        case 'gamipress_ld_complete_specific_lesson':
        case 'gamipress_ld_incomplete_lesson':
        case 'gamipress_ld_incomplete_specific_lesson':
        case 'gamipress_ld_complete_lesson_specific_course':

        // Courses
        case 'gamipress_ld_enroll_course':
        case 'gamipress_ld_enroll_specific_course':
        case 'gamipress_ld_complete_course':
        case 'gamipress_ld_complete_specific_course':

        // Groups
        case 'gamipress_ld_join_group':
        case 'gamipress_ld_join_specific_group':

        // Course category
        case 'gamipress_ld_complete_quiz_course_category':
        case 'gamipress_ld_complete_quiz_course_category_grade':
        case 'gamipress_ld_complete_quiz_course_category_max_grade':
        case 'gamipress_ld_complete_quiz_course_category_between_grade':
        case 'gamipress_ld_pass_quiz_course_category':
        case 'gamipress_ld_fail_quiz_course_category':
        case 'gamipress_ld_complete_topic_course_category':
        case 'gamipress_ld_assignment_upload_course_category':
        case 'gamipress_ld_approve_assignment_course_category':
        case 'gamipress_ld_complete_lesson_course_category':
        case 'gamipress_ld_enroll_course_category':
        case 'gamipress_ld_complete_course_category':

        // Course tag
        case 'gamipress_ld_complete_quiz_course_tag':
        case 'gamipress_ld_complete_quiz_course_tag_grade':
        case 'gamipress_ld_complete_quiz_course_tag_max_grade':
        case 'gamipress_ld_complete_quiz_course_tag_between_grade':
        case 'gamipress_ld_pass_quiz_course_tag':
        case 'gamipress_ld_fail_quiz_course_tag':
        case 'gamipress_ld_complete_topic_course_tag':
        case 'gamipress_ld_assignment_upload_course_tag':
        case 'gamipress_ld_approve_assignment_course_tag':
        case 'gamipress_ld_complete_lesson_course_tag':
        case 'gamipress_ld_enroll_course_tag':
        case 'gamipress_ld_complete_course_tag':

        // Topic taxonomies
        case 'gamipress_ld_complete_topic_category':
        case 'gamipress_ld_complete_topic_tag':

        // Lesson taxonomies
        case 'gamipress_ld_assignment_upload_lesson_category':
        case 'gamipress_ld_approve_assignment_lesson_category':
        case 'gamipress_ld_complete_lesson_category':
        case 'gamipress_ld_assignment_upload_lesson_tag':
        case 'gamipress_ld_approve_assignment_lesson_tag':
        case 'gamipress_ld_complete_lesson_tag':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_ld_trigger_get_user_id', 10, 3 );

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
function gamipress_ld_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_ld_complete_specific_quiz':
        case 'gamipress_ld_complete_specific_quiz_grade':
        case 'gamipress_ld_complete_specific_quiz_max_grade':
        case 'gamipress_ld_complete_specific_quiz_between_grade':
        case 'gamipress_ld_pass_specific_quiz':
        case 'gamipress_ld_fail_specific_quiz':
        case 'gamipress_ld_complete_specific_topic':
        case 'gamipress_ld_complete_specific_lesson':
        case 'gamipress_ld_incomplete_specific_lesson':
        case 'gamipress_ld_enroll_specific_course':
        case 'gamipress_ld_complete_specific_course':
        case 'gamipress_ld_join_specific_group':
            $specific_id = $args[0];
            break;
        case 'gamipress_ld_complete_quiz_specific_course':
        case 'gamipress_ld_complete_quiz_specific_course_grade':
        case 'gamipress_ld_complete_quiz_specific_course_max_grade':
        case 'gamipress_ld_complete_quiz_specific_course_between_grade':
        case 'gamipress_ld_pass_quiz_specific_course':
        case 'gamipress_ld_fail_quiz_specific_course':
        case 'gamipress_ld_assignment_upload_specific_lesson':
        case 'gamipress_ld_approve_assignment_specific_lesson':
        case 'gamipress_ld_complete_lesson_specific_course':
            $specific_id = $args[2];
            break;
        case 'gamipress_ld_complete_topic_specific_course':
        case 'gamipress_ld_assignment_upload_specific_course':
        case 'gamipress_ld_approve_assignment_specific_course':
            $specific_id = $args[3];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_ld_specific_trigger_get_id', 10, 3 );

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
function gamipress_ld_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {

        // Quizzes
        case 'gamipress_ld_complete_quiz':
        case 'gamipress_ld_complete_specific_quiz':
        case 'gamipress_ld_complete_quiz_specific_course':
            // Course taxonomies
            case 'gamipress_ld_complete_quiz_course_category':
            case 'gamipress_ld_complete_quiz_course_tag':
        case 'gamipress_ld_pass_quiz':
        case 'gamipress_ld_pass_specific_quiz':
        case 'gamipress_ld_pass_quiz_specific_course':
            // Course taxonomies
            case 'gamipress_ld_pass_quiz_course_category':
            case 'gamipress_ld_pass_quiz_course_tag':
        case 'gamipress_ld_fail_quiz':
        case 'gamipress_ld_fail_specific_quiz':
        case 'gamipress_ld_fail_quiz_specific_course':
            // Course taxonomies
            case 'gamipress_ld_fail_quiz_course_category':
            case 'gamipress_ld_fail_quiz_course_tag':
            // Add the quiz and course IDs
            $log_meta['quiz_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            break;
        case 'gamipress_ld_complete_quiz_grade':
        case 'gamipress_ld_complete_specific_quiz_grade':
        case 'gamipress_ld_complete_quiz_specific_course_grade':
        case 'gamipress_ld_complete_quiz_max_grade':
        case 'gamipress_ld_complete_specific_quiz_max_grade':
        case 'gamipress_ld_complete_quiz_specific_course_max_grade':
        case 'gamipress_ld_complete_quiz_between_grade':
        case 'gamipress_ld_complete_specific_quiz_between_grade':
        case 'gamipress_ld_complete_quiz_specific_course_between_grade':
            // Course taxonomies
            case 'gamipress_ld_complete_quiz_course_category_grade':
            case 'gamipress_ld_complete_quiz_course_category_max_grade':
            case 'gamipress_ld_complete_quiz_course_category_between_grade':
            case 'gamipress_ld_complete_quiz_course_tag_grade':
            case 'gamipress_ld_complete_quiz_course_tag_max_grade':
            case 'gamipress_ld_complete_quiz_course_tag_between_grade':
            // Add the quiz and course IDs
            $log_meta['quiz_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            $log_meta['score'] = $args[3];
            break;

        // Topics
        case 'gamipress_ld_complete_topic':
        case 'gamipress_ld_complete_specific_topic':
            // Topic taxonomies
            case 'gamipress_ld_complete_topic_category':
            case 'gamipress_ld_complete_topic_tag':
        case 'gamipress_ld_complete_topic_specific_course':
            // Course taxonomies
            case 'gamipress_ld_complete_topic_course_category':
            case 'gamipress_ld_complete_topic_course_tag':
            // Add the topic, lesson and course IDs
            $log_meta['topic_id'] = $args[0];
            $log_meta['lesson_id'] = $args[2];
            $log_meta['course_id'] = $args[3];
            break;

        // Assignments
        case 'gamipress_ld_assignment_upload':
        case 'gamipress_ld_assignment_upload_specific_lesson':
            // Lesson taxonomies
            case 'gamipress_ld_assignment_upload_lesson_category':
            case 'gamipress_ld_assignment_upload_lesson_tag':
        case 'gamipress_ld_assignment_upload_specific_course':
            // Course taxonomies
            case 'gamipress_ld_assignment_upload_course_category':
            case 'gamipress_ld_assignment_upload_course_tag':
        case 'gamipress_ld_approve_assignment':
        case 'gamipress_ld_approve_assignment_specific_lesson':
            // Lesson taxonomies
            case 'gamipress_ld_approve_assignment_lesson_category':
            case 'gamipress_ld_approve_assignment_lesson_tag':
        case 'gamipress_ld_approve_assignment_specific_course':
            // Course taxonomies
            case 'gamipress_ld_approve_assignment_course_category':
            case 'gamipress_ld_approve_assignment_course_tag':
            // Add the assignment, lesson and course IDs
            $log_meta['assignment_id'] = $args[0];
            $log_meta['lesson_id'] = $args[2];
            $log_meta['course_id'] = $args[3];
            break;

        // Lessons
        case 'gamipress_ld_complete_lesson':
        case 'gamipress_ld_complete_specific_lesson':
        case 'gamipress_ld_incomplete_lesson':
        case 'gamipress_ld_incomplete_specific_lesson':
            // Lesson taxonomies
            case 'gamipress_ld_complete_lesson_category':
            case 'gamipress_ld_complete_lesson_tag':
        case 'gamipress_ld_complete_lesson_specific_course':
            // Course taxonimies
            case 'gamipress_ld_complete_lesson_course_category':
            case 'gamipress_ld_complete_lesson_course_tag':
            // Add the lesson and course IDs
            $log_meta['lesson_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            break;

        // Courses
        case 'gamipress_ld_enroll_course':
        case 'gamipress_ld_enroll_specific_course':
            // Course taxonomies
            case 'gamipress_ld_enroll_course_category':
            case 'gamipress_ld_enroll_course_tag':
        case 'gamipress_ld_complete_course':
        case 'gamipress_ld_complete_specific_course':
            // Course taxonomies
            case 'gamipress_ld_complete_course_category':
            case 'gamipress_ld_complete_course_tag':
            // Add the course ID
            $log_meta['course_id'] = $args[0];
            break;

        // Groups
        case 'gamipress_ld_join_group':
        case 'gamipress_ld_join_specific_group':
            // Add the group ID
            $log_meta['group_id'] = $args[0];
            break;
    }

    // Terms IDs
    $terms_ids_index = gamipress_ld_get_terms_ids_index( $trigger );

    if( $terms_ids_index !== -1 ) {
        // Add the terms IDs
        $log_meta['terms_ids'] = $args[$terms_ids_index];
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_ld_log_event_trigger_meta_data', 10, 5 );

/**
 * Extra data fields
 *
 * @since 1.1.2
 *
 * @param array     $fields
 * @param int       $log_id
 * @param string    $type
 *
 * @return array
 */
function gamipress_ld_log_extra_data_fields( $fields, $log_id, $type ) {

    $prefix = '_gamipress_';

    $log = ct_get_object( $log_id );
    $trigger = $log->trigger_type;

    if( $type !== 'event_trigger' ) {
        return $fields;
    }

    switch( $trigger ) {
        // Quizzes
        case 'gamipress_ld_complete_quiz_grade':
        case 'gamipress_ld_complete_specific_quiz_grade':
        case 'gamipress_ld_complete_quiz_specific_course_grade':
        case 'gamipress_ld_complete_quiz_max_grade':
        case 'gamipress_ld_complete_specific_quiz_max_grade':
        case 'gamipress_ld_complete_quiz_specific_course_max_grade':
        case 'gamipress_ld_complete_quiz_between_grade':
        case 'gamipress_ld_complete_specific_quiz_between_grade':
        case 'gamipress_ld_complete_quiz_specific_course_between_grade':
            // Course taxonomies
            case 'gamipress_ld_complete_quiz_course_category_grade':
            case 'gamipress_ld_complete_quiz_course_category_max_grade':
            case 'gamipress_ld_complete_quiz_course_category_between_grade':
            case 'gamipress_ld_complete_quiz_course_tag_grade':
            case 'gamipress_ld_complete_quiz_course_tag_max_grade':
            case 'gamipress_ld_complete_quiz_course_tag_between_grade':
            $fields[] = array(
                'name' 	            => __( 'Grade of completion', 'gamipress' ),
                'desc' 	            => __( 'Grade of completion the user got on complete this quiz.', 'gamipress' ),
                'id'   	            => $prefix . 'score',
                'type' 	            => 'text',
            );
            break;
    }

    return $fields;

}
add_filter( 'gamipress_log_extra_data_fields', 'gamipress_ld_log_extra_data_fields', 10, 3 );

/**
 * Override the meta data to filter the logs count
 *
 * @since   1.2.3
 *
 * @param  array    $log_meta       The meta data to filter the logs count
 * @param  int      $user_id        The given user's ID
 * @param  string   $trigger        The given trigger we're checking
 * @param  int      $since 	        The since timestamp where retrieve the logs
 * @param  int      $site_id        The desired Site ID to check
 * @param  array    $args           The triggered args or requirement object
 *
 * @return array                    The meta data to filter the logs count
 */
function gamipress_ld_get_user_trigger_count_log_meta( $log_meta, $user_id, $trigger, $since, $site_id, $args ) {

    switch( $trigger ) {
        // Quizzes
        case 'gamipress_ld_complete_quiz_grade':
        case 'gamipress_ld_complete_specific_quiz_grade':
        case 'gamipress_ld_complete_quiz_specific_course_grade':
            // Course taxonomies
            case 'gamipress_ld_complete_quiz_course_category_grade':
            case 'gamipress_ld_complete_quiz_course_tag_grade':

            $score = 0;

            if( isset( $args[3] ) ) {
                // Add the score
                $score = $args[3];
            }

            // $args could be a requirement object
            if( isset( $args['ld_score'] ) ) {
                // Add the score
                $score = $args['ld_score'];
            }

            $log_meta['score'] = array(
                'key' => 'score',
                'value' => (int) $score,
                'compare' => '>=',
                'type' => 'integer',
            );
            break;
        case 'gamipress_ld_complete_quiz_max_grade':
        case 'gamipress_ld_complete_specific_quiz_max_grade':
        case 'gamipress_ld_complete_quiz_specific_course_max_grade':
            // Course taxonomies
            case 'gamipress_ld_complete_quiz_course_category_max_grade':
            case 'gamipress_ld_complete_quiz_course_tag_max_grade':

            $score = 0;

            if( isset( $args[3] ) ) {
                // Add the score
                $score = $args[3];
            }

            // $args could be a requirement object
            if( isset( $args['ld_score'] ) ) {
                // Add the score
                $score = $args['ld_score'];
            }

            $log_meta['score'] = array(
                'key' => 'score',
                'value' => $score,
                'compare' => '<=',
                'type' => 'integer',
            );
            break;
        case 'gamipress_ld_complete_quiz_between_grade':
        case 'gamipress_ld_complete_specific_quiz_between_grade':
        case 'gamipress_ld_complete_quiz_specific_course_between_grade':
            // Course taxonomies
            case 'gamipress_ld_complete_quiz_course_category_between_grade':
            case 'gamipress_ld_complete_quiz_course_tag_between_grade':
            if( isset( $args[3] ) ) {
                // Add the score
                $score = $args[3];

                $log_meta['score'] = array(
                    'key' => 'score',
                    'value' => $score,
                    'compare' => '>=',
                    'type' => 'integer',
                );
            }

            // $args could be a requirement object
            if( isset( $args['ld_min_score'] ) ) {
                // Add the score
                $min_score = $args['ld_min_score'];

                $log_meta['score'] = array(
                    'key' => 'score',
                    'value' => $min_score,
                    'compare' => '>=',
                    'type' => 'integer',
                );
            }

            // $args could be a requirement object
            if( isset( $args['ld_max_score'] ) ) {
                // Add the score
                $max_score = $args['ld_max_score'];

                $log_meta['score'] = array(
                    'key' => 'score',
                    'value' => $max_score,
                    'compare' => '<=',
                    'type' => 'integer',
                );
            }
            break;
    }

    return $log_meta;

}
add_filter( 'gamipress_get_user_trigger_count_log_meta', 'gamipress_ld_get_user_trigger_count_log_meta', 10, 6 );