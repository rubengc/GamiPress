<?php
/**
 * Triggers
 *
 * @package GamiPress\AnsPress\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin activity triggers
 *
 * @since   1.0.0
 *
 * @param   array $triggers
 * @return  mixed
 */
function gamipress_anspress_activity_triggers( $triggers ) {

    $triggers[__( 'AnsPress', 'gamipress' )] = array(
        'gamipress_anspress_new_question'       => __( 'Ask a question', 'gamipress' ),
        'gamipress_anspress_new_answer'         => __( 'Answer a question', 'gamipress' ),
        'gamipress_anspress_best_answer'        => __( 'Get answer selected as best', 'gamipress' ),
        'gamipress_anspress_select_best_answer' => __( 'Select an answer as best', 'gamipress' ),
        'gamipress_anspress_vote_up'            => __( 'Vote up', 'gamipress' ),
        'gamipress_anspress_vote_down'          => __( 'Vote down', 'gamipress' ),
        'gamipress_anspress_get_vote_up'        => __( 'Get a vote up', 'gamipress' ),
        'gamipress_anspress_get_vote_down'      => __( 'Get a vote down', 'gamipress' ),
        'gamipress_anspress_new_comment'        => __( 'Comment on question or answer', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_anspress_activity_triggers' );

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
function gamipress_anspress_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_anspress_new_question':
        case 'gamipress_anspress_new_answer':
        case 'gamipress_anspress_best_answer':
        case 'gamipress_anspress_select_best_answer':
        case 'gamipress_anspress_vote_up':
        case 'gamipress_anspress_vote_down':
        case 'gamipress_anspress_get_vote_up':
        case 'gamipress_anspress_get_vote_down':
        case 'gamipress_anspress_new_comment':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_anspress_trigger_get_user_id', 10, 3 );

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
function gamipress_anspress_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_anspress_new_question':
        case 'gamipress_anspress_new_answer':
            // Add the post ID
            $log_meta['post_id'] = $args[0];
            break;
        case 'gamipress_anspress_best_answer':
        case 'gamipress_anspress_select_best_answer':
            // Add the answer and question IDs
            $log_meta['answer_id'] = $args[0];
            $log_meta['question_id'] = $args[3];
            break;
        case 'gamipress_anspress_vote_up':
        case 'gamipress_anspress_vote_down':
        case 'gamipress_anspress_get_vote_up':
        case 'gamipress_anspress_get_vote_down':
            // Add the post ID
            $log_meta['post_id'] = $args[0];
            break;
        case 'gamipress_anspress_new_comment':
            // Add the comment and post IDs
            $log_meta['comment_id'] = $args[0];
            $log_meta['post_id'] = $args[2];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_anspress_log_event_trigger_meta_data', 10, 5 );

/**
 * Extra filter to check duplicated activity
 *
 * @since 1.0.0
 *
 * @param bool 		$return
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return bool					True if user deserves trigger, else false
 */
function gamipress_anspress_trigger_duplicity_check( $return, $user_id, $trigger, $site_id, $args  ) {

    // If user doesn't deserves trigger, then bail to prevent grant access
    if( ! $return )
        return $return;

    $log_meta = array(
        'type' => 'event_trigger',
        'trigger_type' => $trigger,
    );

    switch ( $trigger ) {
        case 'gamipress_anspress_vote_up':
        case 'gamipress_anspress_vote_down':
        case 'gamipress_anspress_get_vote_up':
        case 'gamipress_anspress_get_vote_down':
            // Prevent duplicate vote up/down on same question/answer
            $log_meta['post_id'] = $args[0];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
    }

    return $return;

}
add_filter( 'gamipress_user_deserves_trigger', 'gamipress_anspress_trigger_duplicity_check', 10, 5 );