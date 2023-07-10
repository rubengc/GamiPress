<?php
/**
 * Triggers
 *
 * @package GamiPress\Sensei\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Sensei specific triggers
 *
 * @since  1.0.0
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_sensei_activity_triggers( $triggers ) {

    $triggers[__( 'Sensei', 'gamipress' )] = array(

        'gamipress_sensei_complete_quiz' => __( 'Complete a quiz', 'gamipress' ),
        'gamipress_sensei_complete_specific_quiz' => __( 'Complete a specific quiz', 'gamipress' ),

        'gamipress_sensei_complete_quiz_grade' => __( 'Complete a quiz with a minimum percent grade', 'gamipress' ),
        'gamipress_sensei_complete_specific_quiz_grade' => __( 'Complete a specific quiz with a minimum percent grade', 'gamipress' ),

        'gamipress_sensei_complete_quiz_max_grade' => __( 'Complete a quiz with a maximum percent grade', 'gamipress' ),
        'gamipress_sensei_complete_specific_quiz_max_grade' => __( 'Complete a specific quiz with a maximum percent grade', 'gamipress' ),

        'gamipress_sensei_pass_quiz' => __( 'Successfully pass a quiz', 'gamipress' ),
        'gamipress_sensei_pass_specific_quiz' => __( 'Successfully pass a specific quiz', 'gamipress' ),

        'gamipress_sensei_fail_quiz' => __( 'Fail a quiz', 'gamipress' ),
        'gamipress_sensei_fail_specific_quiz' => __( 'Fail a specific quiz', 'gamipress' ),

        'gamipress_sensei_complete_lesson' => __( 'Complete a lesson', 'gamipress' ),
        'gamipress_sensei_complete_specific_lesson' => __( 'Complete a specific lesson', 'gamipress' ),

        'gamipress_sensei_start_course' => __( 'Enroll in a course', 'gamipress' ),
        'gamipress_sensei_complete_course' => __( 'Complete a course', 'gamipress' ),
        'gamipress_sensei_complete_specific_course' => __( 'Complete a specific course', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_sensei_activity_triggers' );

/**
 * Register Sensei specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_sensei_specific_activity_triggers( $specific_activity_triggers ) {

    $specific_activity_triggers['gamipress_sensei_complete_specific_quiz'] = array( 'quiz' );
    $specific_activity_triggers['gamipress_sensei_complete_specific_quiz_grade'] = array( 'quiz' );
    $specific_activity_triggers['gamipress_sensei_complete_specific_quiz_max_grade'] = array( 'quiz' );
    $specific_activity_triggers['gamipress_sensei_pass_specific_quiz'] = array( 'quiz' );
    $specific_activity_triggers['gamipress_sensei_fail_specific_quiz'] = array( 'quiz' );
    $specific_activity_triggers['gamipress_sensei_complete_specific_lesson'] = array( 'lesson' );
    $specific_activity_triggers['gamipress_sensei_complete_specific_course'] = array( 'course' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_sensei_specific_activity_triggers' );

/**
 * Build custom activity trigger label
 *
 * @since  1.0.0
 *
 * @param string    $title
 * @param integer   $requirement_id
 * @param array     $requirement
 *
 * @return string
 */
function gamipress_sensei_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $minimum_score = ( isset( $requirement['sensei_score'] ) ) ? absint( $requirement['sensei_score'] ) : 0;

    switch( $requirement['trigger_type'] ) {
        case 'gamipress_sensei_complete_quiz_grade':
            return sprintf( __( 'Completed a quiz with a score of %d or higher', 'gamipress' ), $minimum_score );
            break;
        case 'gamipress_sensei_complete_specific_quiz_grade':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Complete the quiz %s with a score of %d or higher', 'gamipress' ), get_the_title( $achievement_post_id ), $minimum_score );
            break;
        case 'gamipress_sensei_complete_quiz_max_grade':
            return sprintf( __( 'Completed a quiz with a maximum score of %d', 'gamipress' ), $minimum_score );
            break;
        case 'gamipress_sensei_complete_specific_quiz_max_grade':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Complete the quiz %s with a maximum score of %d', 'gamipress' ), get_the_title( $achievement_post_id ), $minimum_score );
            break;
    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_sensei_activity_trigger_label', 10, 3 );

/**
 * Register Sensei specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_sensei_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_sensei_complete_specific_quiz'] = __( 'Complete the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_sensei_pass_specific_quiz'] = __( 'Pass the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_sensei_fail_specific_quiz'] = __( 'Fail the quiz %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_sensei_complete_specific_lesson'] = __( 'Complete the lesson %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_sensei_complete_specific_course'] = __( 'Complete the course %s', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_sensei_specific_activity_trigger_label' );

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
function gamipress_sensei_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_sensei_complete_quiz':
        case 'gamipress_sensei_complete_specific_quiz':
        case 'gamipress_sensei_complete_quiz_grade':
        case 'gamipress_sensei_complete_specific_quiz_grade':
        case 'gamipress_sensei_complete_quiz_max_grade':
        case 'gamipress_sensei_complete_specific_quiz_max_grade':
        case 'gamipress_sensei_pass_quiz':
        case 'gamipress_sensei_pass_specific_quiz':
        case 'gamipress_sensei_fail_quiz':
        case 'gamipress_sensei_fail_specific_quiz':
        case 'gamipress_sensei_complete_lesson':
        case 'gamipress_sensei_complete_specific_lesson':
        case 'gamipress_sensei_start_course':
        case 'gamipress_sensei_complete_course':
        case 'gamipress_sensei_complete_specific_course':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_sensei_trigger_get_user_id', 10, 3 );

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
function gamipress_sensei_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_sensei_complete_specific_quiz':
        case 'gamipress_sensei_complete_specific_quiz_grade':
        case 'gamipress_sensei_complete_specific_quiz_max_grade':
        case 'gamipress_sensei_pass_specific_quiz':
        case 'gamipress_sensei_fail_specific_quiz':
        case 'gamipress_sensei_complete_specific_lesson':
        case 'gamipress_sensei_complete_specific_course':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_sensei_specific_trigger_get_id', 10, 3 );

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
function gamipress_sensei_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_sensei_complete_quiz':
        case 'gamipress_sensei_complete_specific_quiz':
        case 'gamipress_sensei_pass_quiz':
        case 'gamipress_sensei_pass_specific_quiz':
        case 'gamipress_sensei_fail_quiz':
        case 'gamipress_sensei_fail_specific_quiz':
            // Add the quiz and course IDs
            $log_meta['quiz_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            break;
        case 'gamipress_sensei_complete_quiz_grade':
        case 'gamipress_sensei_complete_specific_quiz_grade':
        case 'gamipress_sensei_complete_quiz_max_grade':
        case 'gamipress_sensei_complete_specific_quiz_max_grade':
            // Add the quiz and course IDs
            $log_meta['quiz_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            $log_meta['score'] = $args[3];
            break;
        case 'gamipress_sensei_complete_lesson':
        case 'gamipress_sensei_complete_specific_lesson':
            // Add the lesson and course IDs
            $log_meta['lesson_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            break;
        case 'gamipress_sensei_start_course':
        case 'gamipress_sensei_complete_course':
        case 'gamipress_sensei_complete_specific_course':
            // Add the course ID
            $log_meta['course_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_sensei_log_event_trigger_meta_data', 10, 5 );