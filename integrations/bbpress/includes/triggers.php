<?php
/**
 * Triggers
 *
 * @package GamiPress\bbPress\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register bbPress specific triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_bbp_activity_triggers( $triggers ) {

    $triggers[__( 'bbPress', 'gamipress' )] = array(
        'gamipress_bbp_new_forum'               => __( 'Create a new forum', 'gamipress' ),

        'gamipress_bbp_new_topic'               => __( 'Create a new topic', 'gamipress' ),
        'gamipress_bbp_specific_new_topic'      => __( 'Create a new topic on a specific forum', 'gamipress' ),

        'gamipress_bbp_new_reply'               => __( 'Reply to a topic', 'gamipress' ),
        'gamipress_bbp_specific_new_reply'      => __( 'Reply to a specific topic', 'gamipress' ),
        'gamipress_bbp_specific_forum_reply'    => __( 'Reply to any topic of a specific forum', 'gamipress' ),

        'gamipress_bbp_get_new_reply'               => __( 'Get a reply in a topic', 'gamipress' ),
        'gamipress_bbp_get_specific_new_reply'      => __( 'Get a reply in a specific topic', 'gamipress' ),
        'gamipress_bbp_get_specific_forum_reply'    => __( 'Get a reply in any topic of a specific forum', 'gamipress' ),

        'gamipress_bbp_favorite_topic'          => __( 'Favorite a topic', 'gamipress' ),
        'gamipress_bbp_specific_favorite_topic' => __( 'Favorite a specific topic', 'gamipress' ),
        'gamipress_bbp_specific_forum_favorite_topic' => __( 'Favorite any topic on a specific forum', 'gamipress' ),
        'gamipress_bbp_get_favorite_topic'      => __( 'Get a new favorite on a topic', 'gamipress' ),

        'gamipress_bbp_unfavorite_topic'          => __( 'Unfavorite a topic', 'gamipress' ),
        'gamipress_bbp_specific_unfavorite_topic' => __( 'Unfavorite a specific topic', 'gamipress' ),
        'gamipress_bbp_specific_forum_unfavorite_topic' => __( 'Unfavorite any topic on a specific forum', 'gamipress' ),
        'gamipress_bbp_get_unfavorite_topic'      => __( 'Lost a new favorite on a topic', 'gamipress' ),

        'gamipress_bbp_delete_forum'            => __( 'Delete a forum', 'gamipress' ),
        'gamipress_bbp_delete_topic'            => __( 'Delete a topic', 'gamipress' ),
        'gamipress_bbp_delete_reply'            => __( 'Delete a reply', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_bbp_activity_triggers' );

/**
 * Register bbPress specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_bbp_specific_activity_triggers( $specific_activity_triggers ) {

    // Support for multisites if bbPress is just active on a subsite
    $forum = ( function_exists( 'bbp_get_forum_post_type' ) ? bbp_get_forum_post_type() : apply_filters( 'bbp_forum_post_type', 'forum' ) );
    $topic = ( function_exists( 'bbp_get_topic_post_type' ) ? bbp_get_topic_post_type() : apply_filters( 'bbp_topic_post_type', 'topic' ) );

    $specific_activity_triggers['gamipress_bbp_specific_new_topic'] = array( $forum );
    $specific_activity_triggers['gamipress_bbp_specific_new_reply'] = array( $topic );
    $specific_activity_triggers['gamipress_bbp_specific_forum_reply'] = array( $forum );
    $specific_activity_triggers['gamipress_bbp_get_specific_new_reply'] = array( $topic );
    $specific_activity_triggers['gamipress_bbp_get_specific_forum_reply'] = array( $forum );
    $specific_activity_triggers['gamipress_bbp_specific_favorite_topic'] = array( $topic );
    $specific_activity_triggers['gamipress_bbp_specific_forum_favorite_topic'] = array( $forum );
    $specific_activity_triggers['gamipress_bbp_specific_unfavorite_topic'] = array( $topic );
    $specific_activity_triggers['gamipress_bbp_specific_forum_unfavorite_topic'] = array( $forum );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_bbp_specific_activity_triggers' );

/**
 * Register bbPress specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_bbp_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_bbp_specific_new_topic'] = __( 'Create a topic on %s forum', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_bbp_specific_new_reply'] = __( 'Reply to %s topic', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_bbp_specific_forum_reply'] = __( 'Reply to any topic on %s forum', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_bbp_get_specific_new_reply'] = __( 'Get a reply in %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_bbp_get_specific_forum_reply'] = __( 'Get a reply in any topic on %s forum', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_bbp_specific_favorite_topic'] = __( 'Favorite %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_bbp_specific_forum_favorite_topic'] = __( 'Favorite any topic on %s forum', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_bbp_specific_unfavorite_topic'] = __( 'Unfavorite %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_bbp_specific_forum_unfavorite_topic'] = __( 'Unfavorite any topic on %s forum', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_bbp_specific_activity_trigger_label' );

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
function gamipress_bbp_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_bbp_new_forum':
        case 'gamipress_bbp_new_topic':
        case 'gamipress_bbp_specific_new_topic':
        case 'gamipress_bbp_new_reply':
        case 'gamipress_bbp_specific_new_reply':
        case 'gamipress_bbp_specific_forum_reply':
        case 'gamipress_bbp_get_new_reply':
        case 'gamipress_bbp_get_specific_new_reply':
        case 'gamipress_bbp_get_specific_forum_reply':
        case 'gamipress_bbp_favorite_topic':
        case 'gamipress_bbp_specific_favorite_topic':
        case 'gamipress_bbp_specific_forum_favorite_topic':
        case 'gamipress_bbp_get_favorite_topic':
        case 'gamipress_bbp_unfavorite_topic':
        case 'gamipress_bbp_specific_unfavorite_topic':
        case 'gamipress_bbp_specific_forum_unfavorite_topic':
        case 'gamipress_bbp_get_unfavorite_topic':
        case 'gamipress_bbp_delete_forum':
        case 'gamipress_bbp_delete_topic':
        case 'gamipress_bbp_delete_reply':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}

add_filter( 'gamipress_trigger_get_user_id', 'gamipress_bbp_trigger_get_user_id', 10, 3);

/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.1
 *
 * @param  integer  $specific_id Specific ID.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          Specific ID.
 */
function gamipress_bbp_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_bbp_specific_new_topic':
        case 'gamipress_bbp_specific_new_reply':
        case 'gamipress_bbp_get_specific_new_reply':      
        case 'gamipress_bbp_specific_forum_favorite_topic':
        case 'gamipress_bbp_specific_forum_unfavorite_topic':
            $specific_id = $args[2];
            break;
        case 'gamipress_bbp_specific_forum_reply':
        case 'gamipress_bbp_get_specific_forum_reply':
            $specific_id = $args[3];
            break;
        case 'gamipress_bbp_specific_favorite_topic':
        case 'gamipress_bbp_specific_unfavorite_topic':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_bbp_specific_trigger_get_id', 10, 3 );

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.1
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_bbp_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_bbp_new_forum':
        case 'gamipress_bbp_delete_forum':
            // Add the forum ID
            $log_meta['forum_id'] = $args[0];
            break;
        case 'gamipress_bbp_new_topic':
        case 'gamipress_bbp_specific_new_topic':
            // Add the topic and forum ID
            $log_meta['topic_id'] = $args[0];
            $log_meta['forum_id'] = $args[2];
            break;
        case 'gamipress_bbp_new_reply':
        case 'gamipress_bbp_specific_new_reply':
        case 'gamipress_bbp_specific_forum_reply':
        case 'gamipress_bbp_get_new_reply':
        case 'gamipress_bbp_get_specific_new_reply':
        case 'gamipress_bbp_get_specific_forum_reply':
            // Add the reply, topic and forum IDs
            $log_meta['reply_id'] = $args[0];
            $log_meta['topic_id'] = $args[2];
            $log_meta['forum_id'] = $args[3];
            break;
        case 'gamipress_bbp_favorite_topic':
        case 'gamipress_bbp_specific_favorite_topic':
        case 'gamipress_bbp_specific_forum_favorite_topic':
        case 'gamipress_bbp_get_favorite_topic':
        case 'gamipress_bbp_unfavorite_topic':
        case 'gamipress_bbp_specific_unfavorite_topic':
        case 'gamipress_bbp_specific_forum_unfavorite_topic':
        case 'gamipress_bbp_get_unfavorite_topic':
        case 'gamipress_bbp_delete_topic':
            // Add the topic and forum IDs
            $log_meta['topic_id'] = $args[0];
            $log_meta['forum_id'] = $args[2];
            break;
        case 'gamipress_bbp_delete_reply':
            // Add the reply ID
            $log_meta['reply_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_bbp_log_event_trigger_meta_data', 10, 5 );

/**
 * Extra filter to check duplicated activity
 *
 * @since 1.0.1
 *
 * @param bool 		$return
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return bool					True if user deserves trigger, else false
 */
function gamipress_bbp_trigger_duplicity_check( $return, $user_id, $trigger, $site_id, $args  ) {

     // If user doesn't deserves trigger, then bail to prevent grant access
    if( ! $return )
        return $return;

    $log_meta = array(
        'type' => 'event_trigger',
        'trigger_type' => $trigger,
    );

    switch ( $trigger ) {
        case 'gamipress_bbp_new_forum':
            // User can not create same forum more times, so check it
            $log_meta['forum_id'] = $args[0];
            $return = (bool) ( gamipress_get_user_log_count( $user_id, $log_meta ) === 0 );
            break;
        case 'gamipress_bbp_new_topic':
        case 'gamipress_bbp_specific_new_topic':
            // User can not create same topic more times, so check it
            $log_meta['topic_id'] = $args[0];
            $return = (bool) ( gamipress_get_user_log_count( $user_id, $log_meta ) === 0 );
            break;
        case 'gamipress_bbp_new_reply':
        case 'gamipress_bbp_specific_new_reply':
        case 'gamipress_bbp_specific_forum_reply':
        case 'gamipress_bbp_get_new_reply':
        case 'gamipress_bbp_get_specific_new_reply':
        case 'gamipress_bbp_get_specific_forum_reply':
            // User can not create same reply more times, so check it
            $log_meta['reply_id'] = $args[0];
            $return = (bool) ( gamipress_get_user_log_count( $user_id, $log_meta ) === 0 );
            break;
    }

    return $return;

}
add_filter( 'gamipress_user_deserves_trigger', 'gamipress_bbp_trigger_duplicity_check', 10, 5 );