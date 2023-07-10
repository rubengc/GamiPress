<?php
/**
 * Triggers
 *
 * @package GamiPress\Thrive_Quiz_Builder\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin activity triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_thrive_quiz_builder_activity_triggers( $triggers ) {

    $triggers[__( 'Thrive Quiz Builder', 'gamipress' )] = array(

        // Quiz
        'gamipress_thrive_quiz_builder_complete_quiz'             => __( 'Complete any quiz', 'gamipress' ),
        'gamipress_thrive_quiz_builder_complete_specific_quiz'    => __( 'Complete specific quiz', 'gamipress' ),

        // Quiz Type
        'gamipress_thrive_quiz_builder_complete_quiz_type'    => __( 'Complete quiz of specific type', 'gamipress' ),

        // Percentage Quiz
        'gamipress_thrive_quiz_builder_complete_percentage_quiz'             => __( 'Complete any quiz with a grade percentage greater than, less than or equal to a specific percentage', 'gamipress' ),
        'gamipress_thrive_quiz_builder_complete_specific_percentage_quiz'    => __( 'Complete specific quiz with a grade percentage greater than, less than or equal to a specific percentage', 'gamipress' ),

        // Share result
        'gamipress_thrive_quiz_builder_share_result'             => __( 'Share result of any quiz', 'gamipress' ),
        'gamipress_thrive_quiz_builder_share_specific_result'   => __( 'Share result of specific quiz', 'gamipress' ),

    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_thrive_quiz_builder_activity_triggers' );

/**
 * Register specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_thrive_quiz_builder_specific_activity_triggers( $specific_activity_triggers ) {
    
    $specific_activity_triggers['gamipress_thrive_quiz_builder_complete_specific_quiz'] = array( 'tqb_quiz' );
    $specific_activity_triggers['gamipress_thrive_quiz_builder_complete_specific_percentage_quiz'] = array( 'percentage_quizzes' );
    $specific_activity_triggers['gamipress_thrive_quiz_builder_share_specific_result'] = array( 'tqb_quiz' );
    
    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_thrive_quiz_builder_specific_activity_triggers' );

/**
 * Register specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_thrive_quiz_builder_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_thrive_quiz_builder_complete_specific_quiz'] = __( 'Complete %s quiz', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_thrive_quiz_builder_complete_specific_percentage_quiz'] = __( 'Complete %s percentage quiz', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_thrive_quiz_builder_complete_specific_result'] = __( 'Share result of %s quiz', 'gamipress' );
    
    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_thrive_quiz_builder_specific_activity_trigger_label' );

/**
 * Build custom activity trigger label
 *
 * @param string    $title
 * @param integer   $requirement_id
 * @param array     $requirement
 *
 * @return string
 */
function gamipress_thrive_quiz_builder_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $percentage = ( isset( $requirement['thrive_quiz_builder_percentage'] ) ) ? absint( $requirement['thrive_quiz_builder_percentage'] ) : 0;
    $percentage_condition = ( isset( $requirement['thrive_quiz_builder_percentage_condition'] ) ) ? $requirement['thrive_quiz_builder_percentage_condition'] : 'equal';
    $percentage_conditions = gamipress_thrive_quiz_builder_get_percentage_conditions();

    $quiz_types = gamipress_thrive_quiz_builder_get_quiz_types();
    $quiz_type = ( isset( $requirement['thrive_quiz_builder_quiz_type'] ) ) ? $requirement['thrive_quiz_builder_quiz_type'] : '';

    switch( $requirement['trigger_type'] ) {
        // Quiz type
        case 'gamipress_thrive_quiz_builder_complete_quiz_type':
            if( $quiz_type === '' ) {
                return __( 'Completed a quiz of a specific type', 'gamipress' );
            } else {
                return sprintf( __( 'Completed a quiz of %s type', 'gamipress' ), $quiz_types[$quiz_type] );

            }
            break;
        // Any quiz percentage
        case 'gamipress_thrive_quiz_builder_complete_percentage_quiz':
            return sprintf( __( 'Complete any quiz with a grade percentage %s %s', 'gamipress' ), $percentage_conditions[$percentage_condition], $percentage . '%' );
            break;
        // Specific quiz percentage
        case 'gamipress_thrive_quiz_builder_complete_specific_percentage_quiz':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            $achievement_post_site_id = absint( $requirement['achievement_post_site_id'] );
            $achievement_post_title = gamipress_get_specific_activity_trigger_post_title( $achievement_post_id, $requirement['trigger_type'], $achievement_post_site_id );

            return sprintf( __( 'Complete %s quiz with a grade percentage %s %s', 'gamipress' ), $achievement_post_title, $percentage_conditions[$percentage_condition], $percentage . '%' );
            break;

    }

    return $title;

}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_thrive_quiz_builder_activity_trigger_label', 10, 3 );


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

function gamipress_thrive_quiz_builder_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Quiz
        case 'gamipress_thrive_quiz_builder_complete_quiz':
        case 'gamipress_thrive_quiz_builder_complete_specific_quiz':
        // Share
        case 'gamipress_thrive_quiz_builder_share_result':
        case 'gamipress_thrive_quiz_builder_share_specific_result':
        // Quiz Type
        case 'gamipress_thrive_quiz_builder_complete_quiz_type':
        // Percentage quiz
        case 'gamipress_thrive_quiz_builder_complete_percentage_quiz':
        case 'gamipress_thrive_quiz_builder_complete_specific_percentage_quiz':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_thrive_quiz_builder_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $specific_id Specific ID to override.
 * @param  string  $trigger     Trigger name.
 * @param  array   $args        Passed trigger args.
 *
 * @return integer              Specific ID.
 */
function gamipress_thrive_quiz_builder_specific_trigger_get_id( $specific_id, $trigger, $args ) {
    
    switch ( $trigger ) {
        case 'gamipress_thrive_quiz_builder_complete_specific_quiz':
        case 'gamipress_thrive_quiz_builder_share_specific_result':
        case 'gamipress_thrive_quiz_builder_complete_specific_percentage_quiz':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;

}

add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_thrive_quiz_builder_specific_trigger_get_id', 10, 3 );

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.2
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_thrive_quiz_builder_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {

        // Quiz
        case 'gamipress_thrive_quiz_builder_complete_quiz':
        case 'gamipress_thrive_quiz_builder_complete_specific_quiz':
            // Add the quiz ID
            $log_meta['quiz_id'] = $args[0];
            break;
        // Quiz Type
        case 'gamipress_thrive_quiz_builder_complete_quiz_type':
            // Add the quiz ID and type
            $log_meta['quiz_id'] = $args[0];
            $log_meta['quiz_type'] = $args[2];
            break;
        // Percentage quiz
        case 'gamipress_thrive_quiz_builder_complete_percentage_quiz':
        case 'gamipress_thrive_quiz_builder_complete_specific_percentage_quiz':
            // Add the quiz ID
            $log_meta['quiz_id'] = $args[0];
            $log_meta['percentage'] = $args[2];
            break;
        // Share
        case 'gamipress_thrive_quiz_builder_share_result':
        case 'gamipress_thrive_quiz_builder_share_specific_result':
            // Add the quiz ID
            $log_meta['quiz_id'] = $args[2];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_thrive_quiz_builder_log_event_trigger_meta_data', 10, 5 );