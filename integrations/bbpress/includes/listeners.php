<?php
/**
 * Listeners
 *
 * @package GamiPress\bbPress\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// New forum
function gamipress_bbp_new_forum( $forum ) {

    // Create a new forum
    do_action( 'gamipress_bbp_new_forum', $forum['forum_id'], $forum['forum_author'] );

}
add_action( 'bbp_new_forum', 'gamipress_bbp_new_forum', 10 );

// New topic
function gamipress_bbp_new_topic( $topic_id, $forum_id, $anonymous_data, $topic_author ) {

    // Create a new topic
    do_action( 'gamipress_bbp_new_topic', $topic_id, $topic_author, $forum_id );

    // Create a new topic on specific forum
    do_action( 'gamipress_bbp_specific_new_topic', $topic_id, $topic_author, $forum_id );

}
add_action( 'bbp_new_topic', 'gamipress_bbp_new_topic', 10, 4 );

// New reply
function gamipress_bbp_new_reply( $reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author ) {

    // New reply
    do_action( 'gamipress_bbp_new_reply', $reply_id, $reply_author, $topic_id, $forum_id );

    // Reply on a specific topic
    do_action( 'gamipress_bbp_specific_new_reply', $reply_id, $reply_author, $topic_id, $forum_id );

    // Reply on any topic of a specific forum
    do_action( 'gamipress_bbp_specific_forum_reply', $reply_id, $reply_author, $topic_id, $forum_id );

    $topic_author_id = absint( get_post_field( 'post_author', $topic_id ) );

    if( $topic_author_id !== 0 ) {
        // New reply
        do_action( 'gamipress_bbp_get_new_reply', $reply_id, $topic_author_id, $topic_id, $forum_id );

        // Reply on a specific topic
        do_action( 'gamipress_bbp_get_specific_new_reply', $reply_id, $topic_author_id, $topic_id, $forum_id );

        // Reply on any topic of a specific forum
        do_action( 'gamipress_bbp_get_specific_forum_reply', $reply_id, $topic_author_id, $topic_id, $forum_id );
    }

}
add_action( 'bbp_new_reply', 'gamipress_bbp_new_reply', 10, 5 );

// Favorite a topic
function gamipress_bbp_favorite_topic( $user_id, $topic_id ) {

    $forum_id = bbp_get_topic_forum_id( $topic_id );

    $topic_author = get_post_field( 'post_author', $topic_id );

    if( absint( $user_id ) === absint( $topic_author ) ) {
        return;
    }

    // Favorite a topic
    do_action( 'gamipress_bbp_favorite_topic', $topic_id, $user_id, $forum_id );

    // Favorite a specific topic
    do_action( 'gamipress_bbp_specific_favorite_topic', $topic_id, $user_id, $forum_id );

    // Favorite any topic on a specific forum
    do_action( 'gamipress_bbp_specific_forum_favorite_topic', $topic_id, $user_id, $forum_id );

    // Topic author get a new favorite on a topic
    do_action( 'gamipress_bbp_get_favorite_topic', $topic_id, $topic_author, $forum_id );

}
add_action( 'bbp_add_user_favorite', 'gamipress_bbp_favorite_topic', 10, 2 );

// Unfavorite a topic
function gamipress_bbp_unfavorite_topic( $user_id, $topic_id ) {

    $forum_id = bbp_get_topic_forum_id( $topic_id );

    $topic_author = get_post_field( 'post_author', $topic_id );

    if( absint( $user_id ) === absint( $topic_author ) ) {
        return;
    }

    // Favorite a topic
    do_action( 'gamipress_bbp_unfavorite_topic', $topic_id, $user_id, $forum_id );

    // Favorite a specific topic
    do_action( 'gamipress_bbp_specific_unfavorite_topic', $topic_id, $user_id, $forum_id );

    // Favorite any topic on a specific forum
    do_action( 'gamipress_bbp_specific_forum_unfavorite_topic', $topic_id, $user_id, $forum_id );

    // Topic author get a new unfavorite on a topic
    do_action( 'gamipress_bbp_get_unfavorite_topic', $topic_id, $topic_author, $forum_id );

}
add_action( 'bbp_remove_user_favorite', 'gamipress_bbp_unfavorite_topic', 10, 2 );

// Delete forum
function gamipress_bbp_delete_forum( $forum_id ) {

    $user_id = absint( get_post_field( 'post_author', $forum_id ) );

    // Delete a forum
    do_action( 'gamipress_bbp_delete_forum', $forum_id, $user_id );

}
add_action( 'bbp_delete_forum', 'gamipress_bbp_delete_forum' );

// Delete topic
function gamipress_bbp_delete_topic( $topic_id ) {

    $user_id = absint( get_post_field( 'post_author', $topic_id ) );

    $forum_id = bbp_get_topic_forum_id( $topic_id );

    // Delete a topic
    do_action( 'gamipress_bbp_delete_topic', $topic_id, $user_id, $forum_id );

}
add_action( 'bbp_delete_topic', 'gamipress_bbp_delete_topic' );

// Delete reply
function gamipress_bbp_delete_reply( $reply_id ) {

    $user_id = absint( get_post_field( 'post_author', $reply_id ) );

    // Delete a reply
    do_action( 'gamipress_bbp_delete_reply', $reply_id, $user_id );

}
add_action( 'bbp_delete_reply', 'gamipress_bbp_delete_reply' );