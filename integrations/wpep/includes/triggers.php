<?php
/**
 * Triggers
 *
 * @package GamiPress\WPEP\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register WPEP specific triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_wpep_activity_triggers( $triggers ) {

    $triggers[__( 'WPEP', 'gamipress' )] = array(

        // Assessments
        'gamipress_wpep_complete_assessment'                        => __( 'Complete an assessment', 'gamipress' ),
        'gamipress_wpep_complete_specific_assessment'               => __( 'Complete a specific assessment', 'gamipress' ),
        'gamipress_wpep_fail_assessment'                            => __( 'Fail an assessment', 'gamipress' ),
        'gamipress_wpep_fail_specific_assessment'                   => __( 'Fail a specific assessment', 'gamipress' ),

        // Minimum grade
        'gamipress_wpep_complete_assessment_min_grade'              => __( 'Complete an assessment with a minimum percent grade', 'gamipress' ),
        'gamipress_wpep_complete_specific_assessment_min_grade'     => __( 'Complete a specific assessment with a minimum percent grade', 'gamipress' ),

        // Maximum grade
        'gamipress_wpep_complete_assessment_max_grade'              => __( 'Complete an assessment with a maximum percent grade', 'gamipress' ),
        'gamipress_wpep_complete_specific_assessment_max_grade'     => __( 'Complete a specific assessment with a maximum percent grade', 'gamipress' ),

        // Between grades
        'gamipress_wpep_complete_assessment_between_grade'          => __( 'Complete an assessment on a range of percent grade', 'gamipress' ),
        'gamipress_wpep_complete_specific_assessment_between_grade' => __( 'Complete a specific assessment on a range of percent grade', 'gamipress' ),

        // Lessons
        'gamipress_wpep_complete_lesson'                            => __( 'Complete a lesson', 'gamipress' ),
        'gamipress_wpep_complete_specific_lesson'                   => __( 'Complete a specific lesson', 'gamipress' ),

        // Courses
        'gamipress_wpep_complete_course'                            => __( 'Complete a course', 'gamipress' ),
        'gamipress_wpep_complete_specific_course'                   => __( 'Complete a specific course', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_wpep_activity_triggers' );

/**
 * Register WPEP specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_wpep_specific_activity_triggers( $specific_activity_triggers ) {

    // Post Type Assessment
    $assessment = 'wpep_assessment';
    if( defined( 'WPEP_POST_TYPE_ASSESSMENT' ) )
        $assessment = WPEP_POST_TYPE_ASSESSMENT;

    // Post Type Course
    $course = 'courses';
    if( defined( 'WPEP_POST_TYPE_COURSE' ) )
        $course = WPEP_POST_TYPE_COURSE;

    // Assessments
    $specific_activity_triggers['gamipress_wpep_complete_specific_assessment'] = array( $assessment );
    $specific_activity_triggers['gamipress_wpep_fail_specific_assessment'] = array( $assessment );

    $specific_activity_triggers['gamipress_wpep_complete_specific_assessment_min_grade'] = array( $assessment );

    $specific_activity_triggers['gamipress_wpep_complete_specific_assessment_max_grade'] = array( $assessment );

    $specific_activity_triggers['gamipress_wpep_complete_specific_assessment_between_grade'] = array( $assessment );

    // Lessons
    $specific_activity_triggers['gamipress_wpep_complete_specific_lesson'] = array( 'wpep_lessons' );

    // Courses
    $specific_activity_triggers['gamipress_wpep_complete_specific_course'] = array( $course );

    return $specific_activity_triggers;

}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_wpep_specific_activity_triggers' );

/**
 * Build custom activity trigger label
 *
 * @param string    $title
 * @param integer   $requirement_id
 * @param array     $requirement
 *
 * @return string
 */
function gamipress_wpep_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $score = ( isset( $requirement['wpep_score'] ) ) ? absint( $requirement['wpep_score'] ) : 0;
    $min_score = ( isset( $requirement['wpep_min_score'] ) ) ? absint( $requirement['wpep_min_score'] ) : 0;
    $max_score = ( isset( $requirement['wpep_max_score'] ) ) ? absint( $requirement['wpep_max_score'] ) : 0;

    switch( $requirement['trigger_type'] ) {

        // Minimum grade events
        case 'gamipress_wpep_complete_assessment_min_grade':
            return sprintf( __( 'Completed an assessment with a score of %d or higher', 'gamipress' ), $score );
            break;
        case 'gamipress_wpep_complete_specific_assessment_min_grade':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Complete the assessment %s with a score of %d or higher', 'gamipress' ), get_the_title( $achievement_post_id ), $score );
            break;

        // Maximum grade events
        case 'gamipress_wpep_complete_assessment_max_grade':
            return sprintf( __( 'Completed an assessment with a maximum score of %d', 'gamipress' ), $score );
            break;
        case 'gamipress_wpep_complete_specific_assessment_max_grade':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Complete the assessment %s with a maximum score of %d', 'gamipress' ), get_the_title( $achievement_post_id ), $score );
            break;

        // Between grade events
        case 'gamipress_wpep_complete_assessment_between_grade':
            return sprintf( __( 'Completed an assessment with a score between %d and %d', 'gamipress' ), $min_score, $max_score );
            break;
        case 'gamipress_wpep_complete_specific_assessment_between_grade':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Complete the assessment %s with a score between %d and %d', 'gamipress' ), get_the_title( $achievement_post_id ), $min_score, $max_score );
            break;

    }

    return $title;

}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_wpep_activity_trigger_label', 10, 3 );

/**
 * Register WPEP specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_wpep_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Assessments
    $specific_activity_trigger_labels['gamipress_wpep_complete_specific_assessment'] = __( 'Complete the assessment %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wpep_fail_specific_assessment'] = __( 'Fail the assessment %s', 'gamipress' );

    // Lessons
    $specific_activity_trigger_labels['gamipress_wpep_complete_specific_lesson'] = __( 'Complete the lesson %s', 'gamipress' );

    // Courses
    $specific_activity_trigger_labels['gamipress_wpep_complete_specific_course'] = __( 'Complete the course %s', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_wpep_specific_activity_trigger_label' );

/**
 * Get WPEP specific activity trigger post title
 *
 * @since  1.0.0
 *
 * @param  string   $post_title
 * @param  integer  $specific_id
 * @param  string   $trigger_type
 * @param  int      $site_id
 * @return string
 */
function gamipress_wpep_specific_activity_trigger_post_title( $post_title, $specific_id, $trigger_type, $site_id ) {

    switch( $trigger_type ) {
        case 'gamipress_wpep_complete_specific_lesson':
            if( absint( $specific_id ) !== 0 ) {
                $lesson = WPEP\Entity\Course::instance()->get_lesson( $specific_id );

                $post_title = ( $lesson ? $lesson->title : '' );
            }
            break;
    }

    return $post_title;

}
add_filter( 'gamipress_specific_activity_trigger_post_title', 'gamipress_wpep_specific_activity_trigger_post_title', 10, 4 );

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
function gamipress_wpep_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Assessments
        case 'gamipress_wpep_complete_assessment':
        case 'gamipress_wpep_complete_specific_assessment':
        case 'gamipress_wpep_fail_assessment':
        case 'gamipress_wpep_fail_specific_assessment':
        // Minimum grade
        case 'gamipress_wpep_complete_assessment_min_grade':
        case 'gamipress_wpep_complete_specific_assessment_min_grade':
        // Maximum grade
        case 'gamipress_wpep_complete_assessment_max_grade':
        case 'gamipress_wpep_complete_specific_assessment_max_grade':
        // Between grades
        case 'gamipress_wpep_complete_assessment_between_grade':
        case 'gamipress_wpep_complete_specific_assessment_between_grade':
        // Lessons
        case 'gamipress_wpep_complete_lesson':
        case 'gamipress_wpep_complete_specific_lesson':
        // Courses
        case 'gamipress_wpep_complete_course':
        case 'gamipress_wpep_complete_specific_course':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_wpep_trigger_get_user_id', 10, 3 );

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
function gamipress_wpep_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        // Assessments
        case 'gamipress_wpep_complete_specific_assessment':
        case 'gamipress_wpep_fail_specific_assessment':
        case 'gamipress_wpep_complete_specific_assessment_min_grade':
        case 'gamipress_wpep_complete_specific_assessment_max_grade':
        case 'gamipress_wpep_complete_specific_assessment_between_grade':
        // Lessons
        case 'gamipress_wpep_complete_specific_lesson':
        // Assessments
        case 'gamipress_wpep_complete_specific_course':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_wpep_specific_trigger_get_id', 10, 3 );

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
function gamipress_wpep_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {

        // Assessments
        case 'gamipress_wpep_complete_assessment':
        case 'gamipress_wpep_complete_specific_assessment':
        case 'gamipress_wpep_fail_assessment':
        case 'gamipress_wpep_fail_specific_assessment':
            // Add the status, assessment, question and answer IDs
            $log_meta['assessment_id'] = $args[0];
            $log_meta['question_id'] = $args[2];
            $log_meta['question_answer_id'] = $args[3];
            $log_meta['status'] = $args[4];
            break;
        // Minimum grade
        case 'gamipress_wpep_complete_assessment_min_grade':
        case 'gamipress_wpep_complete_specific_assessment_min_grade':
        // Maximum grade
        case 'gamipress_wpep_complete_assessment_max_grade':
        case 'gamipress_wpep_complete_specific_assessment_max_grade':
        // Between grades
        case 'gamipress_wpep_complete_assessment_between_grade':
        case 'gamipress_wpep_complete_specific_assessment_between_grade':
            // Add the score, assessment, question and answer IDs
            $log_meta['assessment_id'] = $args[0];
            $log_meta['question_id'] = $args[2];
            $log_meta['question_answer_id'] = $args[3];
            $log_meta['score'] = $args[4];
            break;

        // Lessons
        case 'gamipress_wpep_complete_lesson':
        case 'gamipress_wpep_complete_specific_lesson':
            // Add the lesson and course IDs
            $log_meta['lesson_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            break;

        // Courses
        case 'gamipress_wpep_complete_course':
        case 'gamipress_wpep_complete_specific_course':
            // Add the course ID
            $log_meta['course_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_wpep_log_event_trigger_meta_data', 10, 5 );