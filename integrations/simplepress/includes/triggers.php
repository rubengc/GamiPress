<?php
/**
 * Triggers
 *
 * @package GamiPress\SimpePress\Triggers
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
function gamipress_simplepress_activity_triggers( $triggers ) {

    $triggers[__( 'Simple:Press', 'gamipress' )] = array(
        // New topic
        'gamipress_simplepress_new_topic' => __( 'Create a new topic', 'gamipress' ),
        'gamipress_simplepress_specific_forum_new_topic' => __( 'Create a new topic on a specific forum', 'gamipress' ),
        // New Reply
        'gamipress_simplepress_new_reply' => __( 'Reply to a topic', 'gamipress' ),
        'gamipress_simplepress_specific_topic_new_reply' => __( 'Reply to a specific topic', 'gamipress' ),
        'gamipress_simplepress_specific_forum_new_reply' => __( 'Reply to a topic on a specific forum', 'gamipress' ),
        // Delete topic
        'gamipress_simplepress_delete_topic' => __( 'Delete a topic', 'gamipress' ),
        'gamipress_simplepress_specific_forum_delete_topic' => __( 'Delete a topic on a specific forum', 'gamipress' ),
        // Delete Reply
        'gamipress_simplepress_delete_reply' => __( 'Delete a reply', 'gamipress' ),
        'gamipress_simplepress_specific_topic_delete_reply' => __( 'Delete a reply of a specific topic', 'gamipress' ),
        'gamipress_simplepress_specific_forum_delete_reply' => __( 'Delete a reply of a specific forum', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_simplepress_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_simplepress_specific_activity_triggers( $specific_activity_triggers ) {

    // New topic
    $specific_activity_triggers['gamipress_simplepress_specific_forum_new_topic'] = array( 'simplepress_forum' );
    // New Reply
    $specific_activity_triggers['gamipress_simplepress_specific_topic_new_reply'] = array( 'simplepress_topic' );
    $specific_activity_triggers['gamipress_simplepress_specific_forum_new_reply'] = array( 'simplepress_forum' );
    // Delete topic
    $specific_activity_triggers['gamipress_simplepress_specific_forum_delete_topic'] = array( 'simplepress_forum' );
    // Delete Reply
    $specific_activity_triggers['gamipress_simplepress_specific_topic_delete_reply'] = array( 'simplepress_topic' );
    $specific_activity_triggers['gamipress_simplepress_specific_forum_delete_reply'] = array( 'simplepress_forum' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_simplepress_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_simplepress_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // New topic
    $specific_activity_trigger_labels['gamipress_simplepress_specific_forum_new_topic'] = __( 'Create a topic on %s forum', 'gamipress' );
    // New Reply
    $specific_activity_trigger_labels['gamipress_simplepress_specific_topic_new_reply'] = __( 'Reply on %s topic', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_simplepress_specific_forum_new_reply'] = __( 'Reply on %s forum', 'gamipress' );
    // Delete topic
    $specific_activity_trigger_labels['gamipress_simplepress_specific_forum_delete_topic'] = __( 'Delete a topic on %s forum', 'gamipress' );
    // Delete Reply
    $specific_activity_trigger_labels['gamipress_simplepress_specific_topic_delete_reply'] = __( 'Delete a reply on %s topic', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_simplepress_specific_forum_delete_reply'] = __( 'Delete a reply on %s forum', 'gamipress' );



    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_simplepress_specific_activity_trigger_label' );

/**
 * Get plugin specific activity trigger post title
 *
 * @since  1.0.0
 *
 * @param  string   $post_title
 * @param  int      $specific_id
 * @param  string   $trigger_type
 * @param  int      $site_id
 * @return string
 */
function gamipress_simplepress_specific_activity_trigger_post_title( $post_title, $specific_id, $trigger_type, $site_id ) {

    global $wpdb;

    switch( $trigger_type ) {
        // Forum title
        case 'gamipress_simplepress_specific_forum_new_topic':
        case 'gamipress_simplepress_specific_forum_new_reply':
        case 'gamipress_simplepress_specific_forum_delete_topic':
        case 'gamipress_simplepress_specific_forum_delete_reply':
            if( absint( $specific_id ) !== 0 ) {
                $post_title = gamipress_simplepress_get_forum_title( $specific_id );
            }
            break;
        // Topic title
        case 'gamipress_simplepress_specific_topic_new_reply':
        case 'gamipress_simplepress_specific_topic_delete_reply':
            if( absint( $specific_id ) !== 0 ) {
                $post_title = gamipress_simplepress_get_topic_title( $specific_id );
            }
            break;
    }

    return $post_title;

}
add_filter( 'gamipress_specific_activity_trigger_post_title', 'gamipress_simplepress_specific_activity_trigger_post_title', 10, 4 );

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
function gamipress_simplepress_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // New topic
        case 'gamipress_simplepress_new_topic':
        case 'gamipress_simplepress_specific_forum_new_topic':
        // New Reply
        case 'gamipress_simplepress_new_reply':
        case 'gamipress_simplepress_specific_topic_new_reply':
        case 'gamipress_simplepress_specific_forum_new_reply':
        // Delete topic
        case 'gamipress_simplepress_delete_topic':
        case 'gamipress_simplepress_specific_forum_delete_topic':
        // Delete Reply
        case 'gamipress_simplepress_delete_reply':
        case 'gamipress_simplepress_specific_topic_delete_reply':
        case 'gamipress_simplepress_specific_forum_delete_reply':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_simplepress_trigger_get_user_id', 10, 3);


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
function gamipress_simplepress_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        // New topic
        case 'gamipress_simplepress_specific_forum_new_topic':
        // New Reply
        case 'gamipress_simplepress_specific_topic_new_reply':
        case 'gamipress_simplepress_specific_forum_new_reply':
        // Delete topic
        case 'gamipress_simplepress_specific_forum_delete_topic':
        // Delete Reply
        case 'gamipress_simplepress_specific_topic_delete_reply':
        case 'gamipress_simplepress_specific_forum_delete_reply':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_simplepress_specific_trigger_get_id', 10, 3 );

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
function gamipress_simplepress_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // New topic
        case 'gamipress_simplepress_new_topic':
        case 'gamipress_simplepress_specific_forum_new_topic':
        // Delete topic
        case 'gamipress_simplepress_delete_topic':
        case 'gamipress_simplepress_specific_forum_delete_topic':
            // Add the topic and forum IDs
            $log_meta['topic_id'] = $args[0];
            $log_meta['forum_id'] = $args[2];
            break;
        // New Reply
        case 'gamipress_simplepress_new_reply':
        case 'gamipress_simplepress_specific_topic_new_reply':
        case 'gamipress_simplepress_specific_forum_new_reply':
        // Delete Reply
        case 'gamipress_simplepress_delete_reply':
        case 'gamipress_simplepress_specific_topic_delete_reply':
        case 'gamipress_simplepress_specific_forum_delete_reply':
            // Add the reply, topic and forum IDs
            $log_meta['reply_id'] = $args[0];
            $log_meta['topic_id'] = $args[2];
            $log_meta['forum_id'] = $args[3];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_simplepress_log_event_trigger_meta_data', 10, 5 );