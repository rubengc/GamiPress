<?php
/**
 * Listeners
 *
 * @package GamiPress\SimplePress\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * New topic listener
 *
 * @param array $post
 */
function gamipress_simplepress_new_topic( $post ) {

    if ( $post['action'] !== 'topic' )
        return;

    // Topic details
    $forum_id   = $post['forumid'];
    $topic_id   = $post['topicid'];
    $user_id    = $post['userid'];

    // Trigger new topic
    do_action( 'gamipress_simplepress_new_topic', $topic_id, $user_id, $forum_id );

    // Trigger new topic on specific forum
    do_action( 'gamipress_simplepress_specific_forum_new_topic', $topic_id, $user_id, $forum_id );

}
add_action( 'sph_post_create', 'gamipress_simplepress_new_topic' );

/**
 * Delete topic listener
 *
 * @param stdClass $post
 */
function gamipress_simplepress_delete_topic( $post ) {

    // Topic details
    $forum_id   = $post->forum_id;
    $topic_id   = $post->topic_id;
    $user_id    = $post->user_id;

    // Trigger delete topic
    do_action( 'gamipress_simplepress_delete_topic', $topic_id, $user_id, $forum_id );

    // Trigger delete topic on specific forum
    do_action( 'gamipress_simplepress_specific_forum_delete_topic', $topic_id, $user_id, $forum_id );

}
add_action( 'sph_topic_delete', 'gamipress_simplepress_delete_topic' );

/**
 * New reply listener
 *
 * @param array $post
 */
function gamipress_simplepress_new_reply( $post ) {

    if ( $post['action'] !== 'post' )
        return;

    // Reply details
    $forum_id   = $post['forumid'];
    $topic_id   = $post['topicid'];
    $post_id    = $post['postid'];
    $user_id    = $post['userid'];

    // Trigger new reply
    do_action( 'gamipress_simplepress_new_reply', $post_id, $user_id, $topic_id, $forum_id );

    // Trigger new reply on specific topic
    do_action( 'gamipress_simplepress_specific_topic_new_reply', $post_id, $user_id, $topic_id, $forum_id );

    // Trigger new reply on specific forum
    do_action( 'gamipress_simplepress_specific_forum_new_reply', $post_id, $user_id, $topic_id, $forum_id );

}
add_action( 'sph_post_create', 'gamipress_simplepress_new_reply' );

/**
 * Delete reply listener
 *
 * @param stdClass $post
 */
function gamipress_simplepress_delete_post( $post ) {

    // Reply details
    $forum_id   = $post->forum_id;
    $topic_id   = $post->topic_id;
    $post_id    = $post->post_id;
    $user_id    = $post->user_id;

    // Trigger delete reply
    do_action( 'gamipress_simplepress_delete_reply', $post_id, $user_id, $topic_id, $forum_id );

    // Trigger delete reply on specific topic
    do_action( 'gamipress_simplepress_specific_topic_delete_reply', $post_id, $user_id, $topic_id, $forum_id );

    // Trigger delete reply on specific forum
    do_action( 'gamipress_simplepress_specific_forum_delete_reply', $post_id, $user_id, $topic_id, $forum_id );

}
add_action( 'sph_post_delete', 'gamipress_simplepress_delete_post' );