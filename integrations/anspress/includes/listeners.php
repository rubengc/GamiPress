<?php
/**
 * Listeners
 *
 * @package GamiPress\AnsPress\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function gamipress_anspress_new_question( $post_id, $post ) {

    $user_id = $post->post_author;

    // Award user for ask a question
    do_action( 'gamipress_anspress_new_question', $post_id, $user_id, $post );

}
add_action( 'ap_after_new_question', 'gamipress_anspress_new_question', 10, 2 );

function gamipress_anspress_new_answer( $post_id, $post ) {

    $user_id = $post->post_author;

    // Award user for answer
    do_action( 'gamipress_anspress_new_answer', $post_id, $user_id, $post );

}
add_action( 'ap_after_new_answer', 'gamipress_anspress_new_answer', 10, 2 );

function gamipress_anspress_best_answer( $post, $question_id ) {

    $answer_id = $post->ID;
    $user_id = $post->post_author;
    $selector_id = get_current_user_id();
    $answer = $post;
    $question = get_post( $question_id );

    // Award answer author for being marked as best
    do_action( 'gamipress_anspress_best_answer', $answer_id, $user_id, $selector_id, $question_id, $answer, $question );

    if( absint( $question->post_author ) === $selector_id ) {
        // Award question author for select a best answer
        do_action( 'gamipress_anspress_select_best_answer', $answer_id, $selector_id, $user_id, $question_id, $answer, $question );
    }

}
add_action( 'ap_select_answer', 'gamipress_anspress_best_answer', 10, 2 );

function gamipress_anspress_vote_up( $post_id ) {

    $post = get_post( $post_id );
    $user_id = absint( $post->post_author );
    $voter_id = get_current_user_id();

    // Bail if user vote himself
    if( $user_id === $voter_id ) {
        return;
    }

    // Award voter for give a vote up
    do_action( 'gamipress_anspress_vote_up', $post_id, $voter_id, $user_id, $post );

    // Award user for receive a vote up
    do_action( 'gamipress_anspress_get_vote_up', $post_id, $user_id, $voter_id, $post );

}
add_action( 'ap_vote_up', 'gamipress_anspress_vote_up' );

function gamipress_anspress_vote_down( $post_id ) {

    $post = get_post( $post_id );
    $user_id = absint( $post->post_author );
    $voter_id = get_current_user_id();

    // Bail if user vote himself
    if( $user_id === $voter_id ) {
        return;
    }

    // Award voter for give a vote down
    do_action( 'gamipress_anspress_vote_down', $post_id, $voter_id, $user_id, $post );

    // Award user for receive a vote down
    do_action( 'gamipress_anspress_get_vote_down', $post_id, $user_id, $voter_id, $post );

}
add_action( 'ap_vote_down', 'gamipress_anspress_vote_down' );

function gamipress_anspress_new_comment( $comment ) {

    $comment_id = $comment->comment_ID;
    $user_id = $comment->user_id;
    $post_id = $comment->comment_post_ID;

    // Award user for comment
    do_action( 'gamipress_anspress_new_comment', $comment_id, $user_id, $post_id, $comment );

}
add_action( 'ap_publish_comment', 'gamipress_anspress_new_comment' );