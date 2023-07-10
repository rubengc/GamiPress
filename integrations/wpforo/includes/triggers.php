<?php
/**
 * Triggers
 *
 * @package GamiPress\wpForo\Triggers
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin specific triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_wpforo_activity_triggers( $triggers ) {

    $triggers[__( 'wpForo', 'gamipress' )] = array(

        // New post
        'gamipress_wpforo_new_post' => __( 'Reply to a topic', 'gamipress' ),
        'gamipress_wpforo_specific_topic_new_post' => __( 'Reply to a specific topic', 'gamipress' ),
        'gamipress_wpforo_specific_forum_new_post' => __( 'Reply to a topic on a specific forum', 'gamipress' ),
        // New topic
        'gamipress_wpforo_new_topic' => __( 'Create a new topic', 'gamipress' ),
        'gamipress_wpforo_specific_forum_new_topic' => __( 'Create a new topic on a specific forum', 'gamipress' ),
        // Like post
        'gamipress_wpforo_like_post' => __( 'Like a post', 'gamipress' ),
        'gamipress_wpforo_specific_topic_like_post' => __( 'Like a post on a specific topic', 'gamipress' ),
        'gamipress_wpforo_specific_forum_like_post' => __( 'Like a post on a specific forum', 'gamipress' ),
        'gamipress_wpforo_user_like_post' => __( 'Get a like on a post', 'gamipress' ),
        // Dislike post
        'gamipress_wpforo_dislike_post' => __( 'Dislike a post', 'gamipress' ),
        'gamipress_wpforo_specific_topic_dislike_post' => __( 'Dislike a post on a specific topic', 'gamipress' ),
        'gamipress_wpforo_specific_forum_dislike_post' => __( 'Dislike a post on a specific forum', 'gamipress' ),
        'gamipress_wpforo_user_dislike_post' => __( 'Get a dislike on a post', 'gamipress' ),
        // Vote up a post
        'gamipress_wpforo_vote_up_post' => __( 'Vote up a post', 'gamipress' ),
        'gamipress_wpforo_specific_topic_vote_up_post' => __( 'Vote up a post on a specific topic', 'gamipress' ),
        'gamipress_wpforo_specific_forum_vote_up_post' => __( 'Vote up a post on a specific forum', 'gamipress' ),
        'gamipress_wpforo_user_vote_up_post' => __( 'Get a vote up on a post', 'gamipress' ),
        // Vote down a post
        'gamipress_wpforo_vote_down_post' => __( 'Vote down a post', 'gamipress' ),
        'gamipress_wpforo_specific_topic_vote_down_post' => __( 'Vote down a post on a specific topic', 'gamipress' ),
        'gamipress_wpforo_specific_forum_vote_down_post' => __( 'Vote down a post on a specific forum', 'gamipress' ),
        'gamipress_wpforo_user_vote_down_post' => __( 'Get a vote down on a post', 'gamipress' ),
        // Answer question
        'gamipress_wpforo_answer_question' => __( 'Answer a question', 'gamipress' ),
        'gamipress_wpforo_specific_forum_answer_question' => __( 'Answer a question of a specific forum', 'gamipress' ),
        'gamipress_wpforo_user_answer_question' => __( 'Get a question answered', 'gamipress' ),
        // Delete post
        'gamipress_wpforo_delete_post' => __( 'Delete a reply', 'gamipress' ),
        // Delete topic
        'gamipress_wpforo_delete_topic' => __( 'Delete a topic', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_wpforo_activity_triggers' );


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
function gamipress_wpforo_activity_trigger_label( $title, $requirement_id, $requirement ) {
    
    $forum_id = ( isset( $requirement['wpf_forum'] ) ) ? $requirement['wpf_forum'] : '';
    $topic_id = ( isset( $requirement['wpf_topic'] ) ) ? $requirement['wpf_topic'] : '';

    switch( $requirement['trigger_type'] ) {
      
            case 'gamipress_wpforo_specific_forum_new_post':
                $forum_title = gamipress_wpforo_get_forum_title( $forum_id );
                return sprintf( __( 'Reply on %s forum', 'gamipress' ), $forum_title );
                break;
            case 'gamipress_wpforo_specific_forum_new_topic':
                $forum_title = gamipress_wpforo_get_forum_title( $forum_id );
                return sprintf( __( 'Create a topic on %s forum', 'gamipress' ), $forum_title );
                break;
            case 'gamipress_wpforo_specific_forum_like_post':
                $forum_title = gamipress_wpforo_get_forum_title( $forum_id );
                return sprintf( __( 'Like a post on %s forum', 'gamipress' ), $forum_title );
                break;
            case 'gamipress_wpforo_specific_forum_dislike_post':
                $forum_title = gamipress_wpforo_get_forum_title( $forum_id );
                return sprintf( __( 'Dislike a post on %s forum', 'gamipress' ), $forum_title );
                break;
            case 'gamipress_wpforo_specific_forum_vote_up_post':
                $forum_title = gamipress_wpforo_get_forum_title( $forum_id );
                return sprintf( __( 'Vote up a post on %s forum', 'gamipress' ), $forum_title );
                break;
            case 'gamipress_wpforo_specific_forum_vote_down_post':
                $forum_title = gamipress_wpforo_get_forum_title( $forum_id );
                return sprintf( __( 'Vote down a post on %s forum', 'gamipress' ), $forum_title );
                break;
            case 'gamipress_wpforo_specific_forum_answer_question':
                $forum_title = gamipress_wpforo_get_forum_title( $forum_id );
                return sprintf( __( 'Answer question on %s forum', 'gamipress' ), $forum_title );
                break;
            // Topics
            case 'gamipress_wpforo_specific_topic_new_post':
                $topic_title = gamipress_wpforo_get_topic_title( $topic_id );
                return sprintf( __( 'Reply on %s topic', 'gamipress' ), $topic_title );
                break;
            case 'gamipress_wpforo_specific_topic_like_post':
                $topic_title = gamipress_wpforo_get_topic_title( $topic_id );
                return sprintf( __( 'Like a post on %s topic', 'gamipress' ), $topic_title );
                break;
            case 'gamipress_wpforo_specific_topic_dislike_post':
                $topic_title = gamipress_wpforo_get_topic_title( $topic_id );
                return sprintf( __( 'Dislike a post on %s topic', 'gamipress' ), $topic_title );
                break;
            case 'gamipress_wpforo_specific_topic_vote_up_post':
                $topic_title = gamipress_wpforo_get_topic_title( $topic_id );
                return sprintf( __( 'Vote up a post on %s topic', 'gamipress' ), $topic_title );
                break;
            case 'gamipress_wpforo_specific_topic_vote_down_post':
                $topic_title = gamipress_wpforo_get_topic_title( $topic_id );
                return sprintf( __( 'Vote down a post on %s topic', 'gamipress' ), $topic_title );
                break;
            
          
    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_wpforo_activity_trigger_label', 10, 3 );


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
function gamipress_wpforo_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // New post
        case 'gamipress_wpforo_new_post':
        case 'gamipress_wpforo_specific_topic_new_post':
        case 'gamipress_wpforo_specific_forum_new_post':
        // New topic
        case 'gamipress_wpforo_new_topic':
        case 'gamipress_wpforo_specific_forum_new_topic':
        // Like post
        case 'gamipress_wpforo_like_post':
        case 'gamipress_wpforo_specific_topic_like_post':
        case 'gamipress_wpforo_specific_forum_like_post':
        case 'gamipress_wpforo_user_like_post':
        // Dislike post
        case 'gamipress_wpforo_dislike_post':
        case 'gamipress_wpforo_specific_topic_dislike_post':
        case 'gamipress_wpforo_specific_forum_dislike_post':
        case 'gamipress_wpforo_user_dislike_post':
        // Vote up a post
        case 'gamipress_wpforo_vote_up_post':
        case 'gamipress_wpforo_specific_topic_vote_up_post':
        case 'gamipress_wpforo_specific_forum_vote_up_post':
        case 'gamipress_wpforo_user_vote_up_post':
        // Vote down a post
        case 'gamipress_wpforo_vote_down_post':
        case 'gamipress_wpforo_specific_topic_vote_down_post':
        case 'gamipress_wpforo_specific_forum_vote_down_post':
        case 'gamipress_wpforo_user_vote_down_post':
        // Answer question
        case 'gamipress_wpforo_answer_question':
        case 'gamipress_wpforo_specific_forum_answer_question':
        case 'gamipress_wpforo_user_answer_question':
        // Delete post
        case 'gamipress_wpforo_delete_post':
        // Delete topic
        case 'gamipress_wpforo_delete_topic':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_wpforo_trigger_get_user_id', 10, 3);


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
function gamipress_wpforo_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        // Specific topic events
        // New post
        case 'gamipress_wpforo_specific_topic_new_post':
        // New topic
        case 'gamipress_wpforo_specific_forum_new_topic': // (Unique specific forum event that has specific ID on 2nd argument)
        // Like post
        case 'gamipress_wpforo_specific_topic_like_post':
        // Dislike post
        case 'gamipress_wpforo_specific_topic_dislike_post':
        // Vote up a post
        case 'gamipress_wpforo_specific_topic_vote_up_post':
        // Vote down a post
        case 'gamipress_wpforo_specific_topic_vote_down_post':
            $specific_id = $args[2];
            break;
        // Specific forum events
        // New post
        case 'gamipress_wpforo_specific_forum_new_post':
        // Like post
        case 'gamipress_wpforo_specific_forum_like_post':
        // Dislike post
        case 'gamipress_wpforo_specific_forum_dislike_post':
        // Vote up a post
        case 'gamipress_wpforo_specific_forum_vote_up_post':
        // Vote down a post
        case 'gamipress_wpforo_specific_forum_vote_down_post':
        // Answer question
        case 'gamipress_wpforo_specific_forum_answer_question':
           
            $specific_id = $args[3];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_wpforo_specific_trigger_get_id', 10, 3 );

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
function gamipress_wpforo_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // New post
        case 'gamipress_wpforo_new_post':
        case 'gamipress_wpforo_specific_topic_new_post':
        case 'gamipress_wpforo_specific_forum_new_post':
        // Like post
        case 'gamipress_wpforo_like_post':
        case 'gamipress_wpforo_specific_topic_like_post':
        case 'gamipress_wpforo_specific_forum_like_post':
        case 'gamipress_wpforo_user_like_post':
        // Dislike post
        case 'gamipress_wpforo_dislike_post':
        case 'gamipress_wpforo_specific_topic_dislike_post':
        case 'gamipress_wpforo_specific_forum_dislike_post':
        case 'gamipress_wpforo_user_dislike_post':
        // Vote up a post
        case 'gamipress_wpforo_vote_up_post':
        case 'gamipress_wpforo_specific_topic_vote_up_post':
        case 'gamipress_wpforo_specific_forum_vote_up_post':
        case 'gamipress_wpforo_user_vote_up_post':
        // Vote down a post
        case 'gamipress_wpforo_vote_down_post':
        case 'gamipress_wpforo_specific_topic_vote_down_post':
        case 'gamipress_wpforo_specific_forum_vote_down_post':
        case 'gamipress_wpforo_user_vote_down_post':
        // Delete post
        case 'gamipress_wpforo_delete_post':
            // Add the reply (post), topic and forum IDs
            $log_meta['reply_id'] = $args[0];
            $log_meta['topic_id'] = $args[2];
            $log_meta['forum_id'] = $args[3];
            break;
        // Answer question
        case 'gamipress_wpforo_answer_question':
        case 'gamipress_wpforo_specific_forum_answer_question':
        case 'gamipress_wpforo_user_answer_question':
            // Add the question (topic), answer (post) and forum IDs
            $log_meta['question_id'] = $args[0];
            $log_meta['answer_id'] = $args[2];
            $log_meta['forum_id'] = $args[3];
            break;
        // New topic
        case 'gamipress_wpforo_new_topic':
        case 'gamipress_wpforo_specific_forum_new_topic':
        // Delete topic
        case 'gamipress_wpforo_delete_topic':
            // Add the topic and forum IDs
            $log_meta['topic_id'] = $args[0];
            $log_meta['forum_id'] = $args[2];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_wpforo_log_event_trigger_meta_data', 10, 5 );