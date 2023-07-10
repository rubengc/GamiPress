<?php
/**
 * Listeners
 *
 * @package GamiPress\Thrive_Quiz_Builder\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Quiz listener
 *
 * @since 1.0.0
 *
 * @param WP_Post $quiz   Quiz data
 * @param array $user   User data
 */
function gamipress_thrive_quiz_builder_complete_quiz_listener( $quiz, $user ) {

    $user_id = get_current_user_id();

    // Complete any quiz
    do_action( 'gamipress_thrive_quiz_builder_complete_quiz', absint( $quiz->ID ), $user_id );

    // Complete specific quiz
    do_action( 'gamipress_thrive_quiz_builder_complete_specific_quiz', absint( $quiz->ID ), $user_id );

    // Complete a quiz of a type
    do_action( 'gamipress_thrive_quiz_builder_complete_quiz_type', absint( $quiz->ID ), $user_id, $quiz->type );

    // Remove '%' character and cast to int
    $percentage = (int) rtrim( $user['points'], "%" );

    // Complete any percentage quiz
    do_action( 'gamipress_thrive_quiz_builder_complete_percentage_quiz', absint( $quiz->ID ), $user_id, $percentage );

    // Complete specific percentage quiz
    do_action( 'gamipress_thrive_quiz_builder_complete_specific_percentage_quiz', absint( $quiz->ID ), $user_id, $percentage );

}
add_action( 'tqb_quiz_completed', 'gamipress_thrive_quiz_builder_complete_quiz_listener', 10, 2 );


/**
 * Share result listener
 *
 * @since 1.0.0
 *
 * @param array $post   Quiz post data
 */
function gamipress_thrive_quiz_builder_share_result( $post ) {

    $user_id = get_current_user_id();

    // Complete any course
    do_action( 'gamipress_thrive_quiz_builder_share_result', absint( $post['page_id'] ), $user_id, absint( $post['quiz_id'] ) );

    // Complete specific course
    do_action( 'gamipress_thrive_quiz_builder_share_specific_result', absint( $post['page_id'] ), $user_id, absint( $post['quiz_id'] ) );

}
add_action( 'tqb_register_social_media_conversion', 'gamipress_thrive_quiz_builder_share_result' );

