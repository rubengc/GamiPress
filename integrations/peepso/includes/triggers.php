<?php
/**
 * Triggers
 *
 * @package GamiPress\PeepSo\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin specific triggers
 *
 * @since  1.0.0
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_peepso_activity_triggers( $triggers ) {

    $triggers[__( 'PeepSo', 'gamipress' )] = array(
        'gamipress_peepso_change_avatar'        => __( 'Change profile avatar', 'gamipress' ),
        'gamipress_peepso_change_cover'         => __( 'Change cover image', 'gamipress' ),

        'gamipress_peepso_new_activity_post'    => __( 'Write an activity post', 'gamipress' ),
        'gamipress_peepso_new_activity_comment' => __( 'Write a comment on an activity post', 'gamipress' ),
    );

    $triggers[__( 'PeepSo Likes', 'gamipress' )] = array(
        // Like
        'gamipress_peepso_profile_like'         => __( 'Like an user profile', 'gamipress' ),
        'gamipress_peepso_get_profile_like'     => __( 'Get a like on your profile', 'gamipress' ),

        'gamipress_peepso_post_like'            => __( 'Like an activity post', 'gamipress' ),
        'gamipress_peepso_get_post_like'        => __( 'Get a like an activity post', 'gamipress' ),

        'gamipress_peepso_photo_post_like'      => __( 'Like an activity post with a photo', 'gamipress' ),
        'gamipress_peepso_get_photo_post_like'  => __( 'Get a like an activity post with a photo', 'gamipress' ),

        'gamipress_peepso_video_post_like'      => __( 'Like an activity post with a video', 'gamipress' ),
        'gamipress_peepso_get_video_post_like'  => __( 'Get a like an activity post with a video', 'gamipress' ),

        'gamipress_peepso_comment_like'         => __( 'Like a comment', 'gamipress' ),
        'gamipress_peepso_get_comment_like'     => __( 'Get a like a comment', 'gamipress' ),

        // Unlike
        'gamipress_peepso_profile_unlike'       => __( 'Unlike an user profile', 'gamipress' ),
        'gamipress_peepso_get_profile_unlike'   => __( 'Get an unlike on your profile', 'gamipress' ),

        'gamipress_peepso_post_unlike'          => __( 'Unlike an activity post', 'gamipress' ),
        'gamipress_peepso_get_post_unlike'      => __( 'Get an unlike an activity post', 'gamipress' ),

        'gamipress_peepso_photo_post_unlike'    => __( 'Unlike an activity post with a photo', 'gamipress' ),
        'gamipress_peepso_get_photo_post_unlike' => __( 'Get an unlike an activity post with a photo', 'gamipress' ),

        'gamipress_peepso_video_post_unlike'    => __( 'Unlike an activity post with a video', 'gamipress' ),
        'gamipress_peepso_get_video_post_unlike' => __( 'Get an unlike an activity post with a video', 'gamipress' ),

        'gamipress_peepso_comment_unlike'       => __( 'Unlike a comment', 'gamipress' ),
        'gamipress_peepso_get_comment_unlike'   => __( 'Get an unlike a comment', 'gamipress' ),
    );

    $triggers[__( 'PeepSo Reactions', 'gamipress' )] = array(
        // React
        'gamipress_peepso_profile_react'         => __( 'React to an user profile', 'gamipress' ),
        'gamipress_peepso_get_profile_react'     => __( 'Get a reaction on your profile', 'gamipress' ),

        'gamipress_peepso_post_react'            => __( 'React to an activity post', 'gamipress' ),
        'gamipress_peepso_get_post_react'        => __( 'Get a reaction an activity post', 'gamipress' ),

        'gamipress_peepso_photo_post_react'      => __( 'React to an activity post with a photo', 'gamipress' ),
        'gamipress_peepso_get_photo_post_react'  => __( 'Get a reaction an activity post with a photo', 'gamipress' ),

        'gamipress_peepso_video_post_react'      => __( 'React to an activity post with a video', 'gamipress' ),
        'gamipress_peepso_get_video_post_react'  => __( 'Get a reaction an activity post with a video', 'gamipress' ),

        'gamipress_peepso_comment_react'         => __( 'React to a comment', 'gamipress' ),
        'gamipress_peepso_get_comment_react'     => __( 'Get a reaction a comment', 'gamipress' ),

        // Unreact
        'gamipress_peepso_profile_unreact'       => __( 'Remove a reaction on an user profile', 'gamipress' ),
        'gamipress_peepso_get_profile_unreact'   => __( 'Get a reaction removed on your profile', 'gamipress' ),

        'gamipress_peepso_post_unreact'          => __( 'Remove a reaction on an activity post', 'gamipress' ),
        'gamipress_peepso_get_post_unreact'      => __( 'Get a reaction removed on an activity post', 'gamipress' ),

        'gamipress_peepso_photo_post_unreact'     => __( 'Remove a reaction on an activity post with a photo', 'gamipress' ),
        'gamipress_peepso_get_photo_post_unreact' => __( 'Get a reaction removed on an activity post with a photo', 'gamipress' ),

        'gamipress_peepso_video_post_unreact'     => __( 'Remove a reaction on an activity post with a video', 'gamipress' ),
        'gamipress_peepso_get_video_post_unreact' => __( 'Get a reaction removed on an activity post with a video', 'gamipress' ),

        'gamipress_peepso_comment_unreact'       => __( 'Remove a reaction on a comment', 'gamipress' ),
        'gamipress_peepso_get_comment_unreact'   => __( 'Get a reaction removed on a comment', 'gamipress' ),
    );

    // Friends
    if( class_exists( 'PeepSoFriendsPlugin' ) ) {
        $triggers[__( 'PeepSo Friends', 'gamipress' )] = array(
            'gamipress_peepso_friend_request'   => __( 'Send a friend request', 'gamipress' ),
            'gamipress_peepso_friend_add'       => __( 'Add a new friend', 'gamipress' ),
        );
    }

    // Groups
    if( class_exists( 'PeepSoGroupsPlugin' ) ) {
        $triggers[__( 'PeepSo Groups', 'gamipress' )] = array(
            'gamipress_peepso_create_group'         => __( 'Create a group', 'gamipress' ),
            'gamipress_peepso_join_group'           => __( 'Join a group', 'gamipress' ),
            'gamipress_peepso_join_specific_group'  => __( 'Join a specific group', 'gamipress' ),
            'gamipress_peepso_change_group_avatar'  => __( 'Change group avatar', 'gamipress' ),
            'gamipress_peepso_change_group_cover'   => __( 'Change group cover image', 'gamipress' ),
        );
    }

    // Messages
    if( class_exists( 'PeepSoMessagesPlugin' ) ) {
        $triggers[__( 'PeepSo Messages', 'gamipress' )] = array(
            'gamipress_peepso_new_conversation' => __( 'Send a message', 'gamipress' ),
        );
    }

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_peepso_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_peepso_specific_activity_triggers( $specific_activity_triggers ) {

    $group_post_type = ( class_exists( 'PeepSoGroup' ) ? PeepSoGroup::POST_TYPE : 'peepso-group' );

    // Groups
    if( class_exists( 'PeepSoGroupsPlugin' ) ) {
        $specific_activity_triggers['gamipress_peepso_join_specific_group'] = array( $group_post_type );
    }

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_peepso_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_peepso_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_peepso_join_specific_group'] = __( 'Join %s group', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_peepso_specific_activity_trigger_label' );

/**
 * Get user for a PeepSo trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $user_id user ID to override.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 * @return integer          User ID.
 */
function gamipress_peepso_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_peepso_change_avatar':
        case 'gamipress_peepso_change_cover':
        // Friends
        case 'gamipress_peepso_friend_request':
        case 'gamipress_peepso_friend_add':
            $user_id = $args[0];
            break;
        case 'gamipress_peepso_new_activity_post':
        case 'gamipress_peepso_new_activity_comment':
        // Like
        case 'gamipress_peepso_profile_like':
        case 'gamipress_peepso_get_profile_like':

        case 'gamipress_peepso_post_like':
        case 'gamipress_peepso_get_post_like':

        case 'gamipress_peepso_photo_post_like':
        case 'gamipress_peepso_get_photo_post_like':

        case 'gamipress_peepso_video_post_like':
        case 'gamipress_peepso_get_video_post_like':

        case 'gamipress_peepso_comment_like':
        case 'gamipress_peepso_get_comment_like':
        // Unlike
        case 'gamipress_peepso_profile_unlike':
        case 'gamipress_peepso_get_profile_unlike':

        case 'gamipress_peepso_post_unlike':
        case 'gamipress_peepso_get_post_unlike':

        case 'gamipress_peepso_photo_post_unlike':
        case 'gamipress_peepso_get_photo_post_unlike':

        case 'gamipress_peepso_video_post_unlike':
        case 'gamipress_peepso_get_video_post_unlike':

        case 'gamipress_peepso_comment_unlike':
        case 'gamipress_peepso_get_comment_unlike':
        // React
        case 'gamipress_peepso_profile_react':
        case 'gamipress_peepso_get_profile_react':

        case 'gamipress_peepso_post_react':
        case 'gamipress_peepso_get_post_react':

        case 'gamipress_peepso_photo_post_react':
        case 'gamipress_peepso_get_photo_post_react':

        case 'gamipress_peepso_video_post_react':
        case 'gamipress_peepso_get_video_post_react':

        case 'gamipress_peepso_comment_react':
        case 'gamipress_peepso_get_comment_react':
        // Unreact
        case 'gamipress_peepso_profile_unreact':
        case 'gamipress_peepso_get_profile_unreact':

        case 'gamipress_peepso_post_unreact':
        case 'gamipress_peepso_get_post_unreact':

        case 'gamipress_peepso_photo_post_unreact':
        case 'gamipress_peepso_get_photo_post_unreact':

        case 'gamipress_peepso_video_post_unreact':
        case 'gamipress_peepso_get_video_post_unreact':

        case 'gamipress_peepso_comment_unreact':
        case 'gamipress_peepso_get_comment_unreact':
        // Groups
        case 'gamipress_peepso_create_group':
        case 'gamipress_peepso_join_group':
        case 'gamipress_peepso_join_specific_group':

        case 'gamipress_peepso_change_group_avatar':
        case 'gamipress_peepso_change_group_cover':
        // Messages
        case 'gamipress_peepso_new_conversation':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_peepso_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a PeepSo specific trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer  $specific_id Specific ID.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          Specific ID.
 */
function gamipress_peepso_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_peepso_join_specific_group':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_peepso_specific_trigger_get_id', 10, 3 );

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
function gamipress_peepso_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_peepso_new_activity_post':
        case 'gamipress_peepso_new_activity_comment':
            // Add the activity and post IDs
            $log_meta['activity_id'] = $args[0];
            $log_meta['post_id'] = $args[2];
            break;
        // Like/Unlike
        case 'gamipress_peepso_get_profile_like':
        case 'gamipress_peepso_get_profile_unlike':
            // Add the liker ID
            $log_meta['liker_id'] = $args[0];
            break;
        case 'gamipress_peepso_profile_like':
        case 'gamipress_peepso_post_like':
        case 'gamipress_peepso_photo_post_like':
        case 'gamipress_peepso_video_post_like':
        case 'gamipress_peepso_comment_like':

        case 'gamipress_peepso_profile_unlike':
        case 'gamipress_peepso_post_unlike':
        case 'gamipress_peepso_photo_post_unlike':
        case 'gamipress_peepso_video_post_unlike':
        case 'gamipress_peepso_comment_unlike':
            // Add the liker ID
            $log_meta['liker_id'] = $args[1];
            break;
        case 'gamipress_peepso_get_post_like':
        case 'gamipress_peepso_get_photo_post_like':
        case 'gamipress_peepso_get_video_post_like':
        case 'gamipress_peepso_get_comment_like':

        case 'gamipress_peepso_get_post_unlike':
        case 'gamipress_peepso_get_photo_post_unlike':
        case 'gamipress_peepso_get_video_post_unlike':
        case 'gamipress_peepso_get_comment_unlike':
            // Add the liker ID
            $log_meta['liker_id'] = $args[2];
            break;
        // React/Unreact
        case 'gamipress_peepso_get_profile_react':
        case 'gamipress_peepso_get_profile_unreact':
            // Add the reacter ID
            $log_meta['reacter_id'] = $args[0];
            break;
        case 'gamipress_peepso_profile_react':
        case 'gamipress_peepso_post_react':
        case 'gamipress_peepso_photo_post_react':
        case 'gamipress_peepso_video_post_react':
        case 'gamipress_peepso_comment_react':

        case 'gamipress_peepso_profile_unreact':
        case 'gamipress_peepso_post_unreact':
        case 'gamipress_peepso_photo_post_unreact':
        case 'gamipress_peepso_video_post_unreact':
        case 'gamipress_peepso_comment_unreact':
            // Add the reacter ID
            $log_meta['reacter_id'] = $args[1];
            break;
        case 'gamipress_peepso_get_post_react':
        case 'gamipress_peepso_get_photo_post_react':
        case 'gamipress_peepso_get_video_post_react':
        case 'gamipress_peepso_get_comment_react':
        case 'gamipress_peepso_get_post_unreact':
        case 'gamipress_peepso_get_photo_post_unreact':
        case 'gamipress_peepso_get_video_post_unreact':
        case 'gamipress_peepso_get_comment_unreact':
            // Add the reacter ID
            $log_meta['reacter_id'] = $args[2];
            break;
        // Friends
        case 'gamipress_peepso_friend_request':
        case 'gamipress_peepso_friend_add':
            // Add the friend ID
            $log_meta['friend_id'] = $args[1];
            break;
        // Groups
        case 'gamipress_peepso_create_group':
        case 'gamipress_peepso_join_group':
        case 'gamipress_peepso_join_specific_group':
        case 'gamipress_peepso_change_group_avatar':
        case 'gamipress_peepso_change_group_cover':
            // Add the group ID
            $log_meta['group_id'] = $args[0];
            break;
        case 'gamipress_peepso_new_conversation':
            // Add the message ID
            $log_meta['message_id'] = $args[0];
            break;

    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_peepso_log_event_trigger_meta_data', 10, 5 );