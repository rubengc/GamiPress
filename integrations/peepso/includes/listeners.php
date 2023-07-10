<?php
/**
 * Listeners
 *
 * @package GamiPress\PeepSo\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Common user events listener
 *
 * @since 1.0.0
 *
 * @param int $user_id  The user ID
 */
function gamipress_peepso_common_user_listener( $user_id ) {

    // Trigger events depending of current filter since is a common listener
    switch( current_filter() ) {
        case 'peepso_user_after_change_avatar':
            do_action( 'gamipress_peepso_change_avatar', $user_id );
            break;
        case 'peepso_user_after_change_cover':
            do_action( 'gamipress_peepso_change_cover', $user_id );
            break;
    }

}
add_action( 'peepso_user_after_change_avatar', 'gamipress_peepso_common_user_listener' );
add_action( 'peepso_user_after_change_cover', 'gamipress_peepso_common_user_listener' );

/**
 * Common new activities events listener
 *
 * @since 1.0.0
 *
 * @param int $post_id      Post ID attached to the activity (PeepSo activity comments are post too)
 * @param int $activity_id  Activity ID
 */
function gamipress_peepso_common_activity_listener( $post_id, $activity_id ) {

    $user_id = absint( get_post_field( 'post_author', $post_id ) );

    // Trigger events depending of current filter since is a common listener
    switch( current_filter() ) {
        case 'peepso_activity_after_add_post':
            do_action( 'gamipress_peepso_new_activity_post', $activity_id, $user_id, $post_id );
            break;
        case 'peepso_activity_after_add_comment':
            do_action( 'gamipress_peepso_new_activity_comment', $activity_id, $user_id, $post_id );
            break;
    }

}
add_action( 'peepso_activity_after_add_post', 'gamipress_peepso_common_activity_listener', 10, 2 );
add_action( 'peepso_activity_after_add_comment', 'gamipress_peepso_common_activity_listener', 10, 2 );

/**
 * Like listener
 *
 * @since 1.0.5
 *
 * @param stdClass $data
 */
function gamipress_peepso_like_listener( $data ) {

    $action = ( current_filter() === 'peepso_action_like_add' ? 'like' : 'unlike' );

    $user_id = $data->like_user_id;     // id of user adding the like
    $post_id = $data->like_external_id; // id of item being liked; i.e. post_id
    $module_id = $data->like_module_id; // Defines from which module happens the like (0 from user profile, 1 from activity post or comment, etc)
    $type = $data->like_type;           // ???

    /*
     * Modules list:
     * 0 = Profile
     * 1 = Posts/Comments
     * 4 = Photos
     * 5 = Videos
     */
    switch( $module_id ) {
        // Profile
        case 0:

            $user_profile_id = $post_id;

            // Trigger like/unlike an user profile
            do_action( "gamipress_peepso_profile_{$action}", $user_profile_id, $user_id, $module_id, $type );

            // Trigger get a like/unlike on your profile
            do_action( "gamipress_peepso_get_profile_{$action}", $user_id, $user_profile_id, $module_id, $type );

            break;
        // Posts/Comments
        case 1:
        case 4:
        case 5:

            $post_type = get_post_type( $post_id );
            $post_author = get_post_field( 'post_author', $post_id );

            if( $post_type === 'peepso-post' ) {

                // Trigger like/unlike an activity post
                do_action( "gamipress_peepso_post_{$action}", $post_id, $user_id, $module_id, $type );

                // Trigger get a like/unlike on an activity post
                do_action( "gamipress_peepso_get_post_{$action}", $post_id, $post_author, $user_id, $module_id, $type );

            } else if( $post_type === 'peepso-comment' ) {

                // Trigger like/unlike a comment
                do_action( "gamipress_peepso_comment_{$action}", $post_id, $user_id, $module_id, $type );

                // Trigger get a like/unlike on a comment
                do_action( "gamipress_peepso_get_comment_{$action}", $post_id, $post_author, $user_id, $module_id, $type );

            }

            break;
    }

    // Trigger extra actions

    // Like/unlike an activity post with a photo
    if( $module_id === 4 ) {

        if( ! isset( $post_author ) )
            $post_author = get_post_field( 'post_author', $post_id );

        // Trigger like/unlike an activity post with a photo
        do_action( "gamipress_peepso_photo_post_{$action}", $post_id, $user_id, $module_id, $type );

        // Trigger get a like/unlike on an activity post with a photo
        do_action( "gamipress_peepso_get_photo_post_{$action}", $post_id, $post_author, $user_id, $module_id, $type );
    }

    // Like/unlike an activity post with a video
    if( $module_id === 5 ) {

        if( ! isset( $post_author ) )
            $post_author = get_post_field( 'post_author', $post_id );

        // Trigger like/unlike an activity post with a video
        do_action( "gamipress_peepso_video_post_{$action}", $post_id, $user_id, $module_id, $type );

        // Trigger get a like/unlike on an activity post with a video
        do_action( "gamipress_peepso_get_video_post_{$action}", $post_id, $post_author, $user_id, $module_id, $type );

    }

}
add_action( 'peepso_action_like_add', 'gamipress_peepso_like_listener' );
add_action( 'peepso_action_like_remove', 'gamipress_peepso_like_listener' );

/**
 * React listener
 *
 * @since 1.1.1
 *
 * @param stdClass $data
 */
function gamipress_peepso_react_listener( $data ) {

    $action = ( current_filter() === 'peepso_action_react_add' ? 'react' : 'unreact' );

    $user_id = $data->react_user_id;     // id of adding the react
    $post_id = $data->react_external_id; // id of item being reactd; i.e. post_id
    $module_id = $data->react_module_id; // Defines from which module happens the react (0 from user profile, 1 from activity post or comment, etc)

    /*
     * Modules list:
     * 0 = Profile
     * 1 = Posts/Comments
     * 4 = Photos
     * 5 = Videos
     */
    switch( $module_id ) {
        // Profile
        case 0:

            $user_profile_id = $post_id;

            // Trigger react/unreact an user profile
            do_action( "gamipress_peepso_profile_{$action}", $user_profile_id, $user_id, $module_id );

            // Trigger get a react/unreact on your profile
            do_action( "gamipress_peepso_get_profile_{$action}", $user_id, $user_profile_id, $module_id );

            break;
        // Posts/Comments
        case 1:
        case 4:
        case 5:

            $post_type = get_post_type( $post_id );
            $post_author = get_post_field( 'post_author', $post_id );

            if( $post_type === 'peepso-post' ) {

                // Trigger react/unreact an activity post
                do_action( "gamipress_peepso_post_{$action}", $post_id, $user_id, $module_id );

                // Trigger get a react/unreact on an activity post
                do_action( "gamipress_peepso_get_post_{$action}", $post_id, $post_author, $user_id, $module_id );

            } else if( $post_type === 'peepso-comment' ) {

                // Trigger react/unreact a comment
                do_action( "gamipress_peepso_comment_{$action}", $post_id, $user_id, $module_id );

                // Trigger get a react/unreact on a comment
                do_action( "gamipress_peepso_get_comment_{$action}", $post_id, $post_author, $user_id, $module_id );

            }

            break;
    }

    // Trigger extra actions

    // React/unreact an activity post with a photo
    if( $module_id === 4 ) {

        if( ! isset( $post_author ) )
            $post_author = get_post_field( 'post_author', $post_id );

        // Trigger like/unlike an activity post with a photo
        do_action( "gamipress_peepso_photo_post_{$action}", $post_id, $user_id, $module_id );

        // Trigger get a like/unlike on an activity post with a photo
        do_action( "gamipress_peepso_get_photo_post_{$action}", $post_id, $post_author, $user_id, $module_id );
    }

    // React/unreact an activity post with a video
    if( $module_id === 5 ) {

        if( ! isset( $post_author ) )
            $post_author = get_post_field( 'post_author', $post_id );

        // Trigger like/unlike an activity post with a video
        do_action( "gamipress_peepso_video_post_{$action}", $post_id, $user_id, $module_id );

        // Trigger get a like/unlike on an activity post with a video
        do_action( "gamipress_peepso_get_video_post_{$action}", $post_id, $post_author, $user_id, $module_id );

    }

}
add_action( 'peepso_action_react_add', 'gamipress_peepso_react_listener' );
add_action( 'peepso_action_react_remove', 'gamipress_peepso_react_listener' );

/**
 * Common friendship events listener
 *
 * @since 1.0.0
 *
 * @param int $from_user_id Form user ID
 * @param int $to_user_id   To user ID
 */
function gamipress_peepso_common_friends_listener( $from_user_id, $to_user_id ) {

    // Trigger events depending of current filter since is a common listener
    switch( current_filter() ) {
        case 'peepso_friends_requests_after_add':
            do_action( 'gamipress_peepso_friend_request', $from_user_id, $to_user_id );
            break;
        case 'peepso_friends_requests_after_accept':
            do_action( 'gamipress_peepso_friend_add', $from_user_id, $to_user_id );
            break;
    }

}
add_action( 'peepso_friends_requests_after_add', 'gamipress_peepso_common_friends_listener', 10, 2 );
add_action( 'peepso_friends_requests_after_accept', 'gamipress_peepso_common_friends_listener', 10, 2 );

/**
 * Create a group listener
 *
 * @since 1.0.0
 *
 * @param PeepSoGroup $group The group object
 */
function gamipress_peepso_create_group( $group ) {

    do_action( 'gamipress_peepso_create_group', $group->id, $group->owner_id );

}
add_action( 'peepso_action_group_create', 'gamipress_peepso_create_group' );

/**
 * Join a group listener
 *
 * @since 1.0.0
 *
 * @param int $group_id The group ID
 * @param int $user_id  The user ID
 */
function gamipress_peepso_join_group( $group_id, $user_id ) {

    // Trigger join a group
    do_action( 'gamipress_peepso_join_group', $group_id, $user_id );

    // Trigger join a specific group
    do_action( 'gamipress_peepso_join_specific_group', $group_id, $user_id );

}
add_action( 'peepso_action_group_user_join', 'gamipress_peepso_join_group', 10, 2 );

/**
 * Common groups events listener
 *
 * @since 1.0.0
 *
 * @param int $group_id The group ID
 */
function gamipress_peepso_common_groups_listener( $group_id ) {

    $user_id = get_current_user_id();

    // Trigger events depending of current filter since is a common listener
    switch( current_filter() ) {
        case 'peepso_groups_after_change_avatar':
            do_action( 'gamipress_peepso_change_group_avatar', $group_id, $user_id );
            break;
        case 'peepso_groups_after_change_cover':
            do_action( 'gamipress_peepso_change_group_cover', $group_id, $user_id );
            break;
    }

}
add_action( 'peepso_groups_after_change_avatar', 'gamipress_peepso_common_groups_listener' );
add_action( 'peepso_groups_after_change_cover', 'gamipress_peepso_common_groups_listener' );

/**
 * Send a message listener
 *
 * @since 1.0.0
 *
 * @param int $message_id The message ID
 */
function gamipress_peepso_new_conversation( $message_id ) {

    $user_id = absint( get_post_field( 'post_author', $message_id ) );

    // Trigger send a message
    do_action( 'gamipress_peepso_new_conversation', $message_id, $user_id );

}
add_action( 'peepso_messages_new_conversation', 'gamipress_peepso_new_conversation' );