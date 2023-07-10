<?php
/**
 * Listeners
 *
 * @package GamiPress\wpDiscuz\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Vote up/down listener
 *
 * @since 1.0.0
 *
 * @param int           $vote       1 on vote up, -1 on vote down
 * @param WP_Comment    $comment    Comment object
 */
function gamipress_wpdiscuz_add_vote_listener( $vote, $comment ) {

    $comment_id = $comment->comment_ID;
    $user_id = get_current_user_id();
    $comment_author_id = gamipress_wpdiscuz_get_commment_user_id( $comment );
    $post_id = absint( $comment->comment_post_ID );

    // Don't trigger this event if voter is not logged in
    if( $user_id === 0 ) return;

    // Don't trigger this event if user has voted himself
    if( $user_id === $comment_author_id ) return;

    if( $vote > 0 ) {

        // Trigger vote up a comment
        do_action( 'gamipress_wpdiscuz_vote_up', $comment_id, $user_id, $comment_author_id, $post_id, $vote, $comment );

        // Trigger get a vote up on any comment (awards to the comment author
        do_action( 'gamipress_wpdiscuz_get_vote_up', $comment_id, $comment_author_id, $user_id, $post_id, $vote, $comment );

    } else {

        // Trigger vote down a comment
        do_action( 'gamipress_wpdiscuz_vote_down', $comment_id, $user_id, $comment_author_id, $post_id, $vote, $comment );

        // Trigger get a vote down on any comment (awards to the comment author
        do_action( 'gamipress_wpdiscuz_get_vote_down', $comment_id, $comment_author_id, $user_id, $post_id, $vote, $comment );

    }

}
add_action( 'wpdiscuz_add_vote', 'gamipress_wpdiscuz_add_vote_listener', 10, 2);

/**
 * Update vote listener
 *
 * @since 1.0.0
 *
 * @param int           $vote           1 on vote up, -1 on vote down
 * @param int           $is_user_voted
 * @param WP_Comment    $comment        Comment object
 */
function gamipress_wpdiscuz_update_vote_listener( $vote, $is_user_voted, $comment ) {

    gamipress_wpdiscuz_add_vote_listener( $vote, $comment );

}
add_action( 'wpdiscuz_update_vote', 'gamipress_wpdiscuz_update_vote_listener', 10, 3);
