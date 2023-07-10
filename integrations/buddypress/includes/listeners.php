<?php
/**
 * Listeners
 *
 * @package GamiPress\BuddyPress\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/* ----------------------------------------
 * BuddyPress
 ------------------------------------------ */

// Activated account
function gamipress_bp_activate_user( $user_id, $key, $user ) {

    do_action( 'gamipress_bp_activate_user', $user, $user_id );

}
add_action( 'bp_core_activated_user', 'gamipress_bp_activate_user', 10, 3 );

// Set member type
function gamipress_bp_set_member_type( $user_id, $member_type, $append ) {

    do_action( 'gamipress_bp_set_member_type', $user_id, $member_type, $append );

}
add_action( 'bp_set_member_type', 'gamipress_bp_set_member_type', 10, 3 );

/* ----------------------------------------
 * BuddyPress Profile
 ------------------------------------------ */

// Change profile avatar
function gamipress_bp_upload_avatar( $user_id = 0 ) {

    if ( empty( $user_id ) ) {
        $user_id = bp_displayed_user_id();
    }

    // BuddyPress filter for the user ID when a user has uploaded a new avatar.
    $user_id = apply_filters( 'bp_xprofile_new_avatar_user_id', $user_id );

    do_action( 'gamipress_bp_upload_avatar', $user_id );

}
add_action( 'xprofile_avatar_uploaded', 'gamipress_bp_upload_avatar' );

// Change cover image
function gamipress_bp_upload_cover_image( $user_id ) {

    do_action( 'gamipress_bp_upload_cover_image', $user_id );

}
add_action( 'xprofile_cover_image_uploaded', 'gamipress_bp_upload_cover_image' );

// Update profile information
function gamipress_bp_update_profile( $user_id, $posted_field_ids, $errors, $old_values, $new_values ) {

    do_action( 'gamipress_bp_update_profile', $user_id );

    // Loop all updated fields
    foreach( $new_values as $field_id => $field ) {

        $old_value = $old_values[$field_id]['value'];
        $field_value = $field['value'];

        // Skip not updated fields
        if( $field_value === $old_value ) {
            continue;
        }

        $field = xprofile_get_field( $field_id );

        do_action( 'gamipress_bp_update_profile_any_value', $user_id, $field_id );
        do_action( 'gamipress_bp_update_profile_specific_value', $user_id, $field_id, $field_value );
    
    }

}
add_action( 'xprofile_updated_profile', 'gamipress_bp_update_profile', 10, 5 );

/* ----------------------------------------
 * BuddyPress Friendships
 ------------------------------------------ */

// Send a friendship request
function gamipress_bp_friendship_request( $friendship_id, $initiator_user_id, $friend_user_id ) {

    do_action( 'gamipress_bp_friendship_request', $friendship_id, $initiator_user_id );

}
add_action( 'friends_friendship_requested', 'gamipress_bp_friendship_request', 10, 3 );

// Accept a friendship
function gamipress_bp_friendship_accepted( $friendship_id, $initiator_user_id, $friend_user_id ) {

    // Accept a friendship
    do_action( 'gamipress_bp_friendship_accepted', $friendship_id, $friend_user_id );

    // Get a friendship accepted
    do_action( 'gamipress_bp_get_friendship_accepted', $friendship_id, $initiator_user_id );

}
add_action( 'friends_friendship_accepted', 'gamipress_bp_friendship_accepted', 10, 3 );

// Reject a friendship
function gamipress_bp_friendship_rejected( $friendship_id, $friendship ) {

    $initiator_user_id = $friendship->initiator_user_id;
    $friend_user_id = $friendship->friend_user_id;

    // Reject a friendship
    do_action( 'gamipress_bp_friendship_rejected', $friendship_id, $friend_user_id );

    // Get a friendship rejected
    do_action( 'gamipress_bp_get_friendship_rejected', $friendship_id, $initiator_user_id );

}
add_action( 'friends_friendship_rejected', 'gamipress_bp_friendship_rejected', 10, 2 );

// Remove a friendship
function gamipress_bp_friendship_deleted( $friendship_id, $initiator_user_id, $friend_user_id ) {

    // Remove a friendship
    do_action( 'gamipress_bp_friendship_deleted', $friendship_id, $friend_user_id );

    // Get a friendship removed
    do_action( 'gamipress_bp_get_friendship_deleted', $friendship_id, $initiator_user_id );

}
add_action( 'friends_friendship_deleted', 'gamipress_bp_friendship_deleted', 10, 3 );

/* ----------------------------------------
 * BuddyPress Messages
 ------------------------------------------ */

// Send/Reply to a private message
function gamipress_bp_send_message( $message ) {

    do_action( 'gamipress_bp_send_message', $message, $message->sender_id );

}
add_action( 'messages_message_sent', 'gamipress_bp_send_message' );

/* ----------------------------------------
 * BuddyPress Activity
 ------------------------------------------ */

// Write an activity stream message
function gamipress_bp_publish_activity( $content, $user_id, $activity_id ) {

    // Trigger write activity stream message
    do_action( 'gamipress_bp_publish_activity', $activity_id, $user_id );

}
add_action( 'bp_activity_posted_update', 'gamipress_bp_publish_activity', 10, 3 );

// Activity delete
function gamipress_bp_delete_activity( $args ) {

    if( ! isset( $args['id'] ) ) {
        return;
    }

    if( class_exists( 'BP_Activity_Activity' ) ) {
        $activity = new BP_Activity_Activity( $args['id'] );

        if( ! $activity ) {
            return;
        }

        if( $activity->component === 'groups' ) {

            $group_id  = $activity->item_id;

            // Trigger delete group activity stream message
            do_action( 'gamipress_bp_group_delete_activity', $activity->id, $activity->user_id, $group_id );
            do_action( 'gamipress_bp_specific_group_delete_activity', $activity->id, $activity->user_id, $group_id );
        } else {
            // Trigger delete activity stream message
            do_action( 'gamipress_bp_delete_activity', $activity->id, $activity->user_id );
        }
    }

}
add_action( 'bp_before_activity_delete', 'gamipress_bp_delete_activity', 10, 1 );

// Reply to an item in an activity stream
function gamipress_bp_new_activity_comment( $comment_id, $args, $activity ) {

    $comment_parent = $args['parent_id'];
    $comment_parent_data = new BP_Activity_Activity( $comment_parent );
    
    $user_id = bp_loggedin_user_id();

    // Check if user is not the author
    if ( $comment_parent_data->user_id === $user_id ) {
        return;
    }
    
    do_action( 'gamipress_bp_new_activity_comment', $comment_id, $user_id );
    do_action( 'gamipress_bp_get_new_activity_comment', $comment_id, $comment_parent_data->user_id, $user_id );
}
add_action( 'bp_activity_comment_posted', 'gamipress_bp_new_activity_comment', 10, 3 );

// Delete a reply from an item in an activity stream
function gamipress_bp_delete_activity_comment( $activity_id, $comment_id ) {

    $user_id = bp_loggedin_user_id();

    do_action( 'gamipress_bp_delete_activity_comment', $comment_id, $user_id );
}
add_action( 'bp_activity_delete_comment', 'gamipress_bp_delete_activity_comment', 10, 2 );

// Favorite an activity stream item
function gamipress_bp_favorite_activity( $activity_id, $user_id ) {

    if( class_exists( 'BP_Activity_Activity' ) ) {
        $activity = new BP_Activity_Activity( $activity_id );

        // Check if user is not the author
        if ( $activity->user_id !== $user_id ) {
            do_action( 'gamipress_bp_favorite_activity', $activity_id, $user_id );
       
            do_action( 'gamipress_bp_user_favorite_activity', $activity_id, $activity->user_id, $user_id );
        }
    }

}
add_action( 'bp_activity_add_user_favorite', 'gamipress_bp_favorite_activity', 10, 2 );

// Remove a favorite from an activity stream item
function gamipress_bp_remove_favorite_activity( $activity_id, $user_id ) {

    if( class_exists( 'BP_Activity_Activity' ) ) {
        $activity = new BP_Activity_Activity( $activity_id );

        if ( $activity->user_id !== $user_id ) {
            do_action( 'gamipress_bp_remove_favorite_activity', $activity_id, $user_id );
            
            do_action( 'gamipress_bp_user_remove_favorite_activity', $activity_id, $activity->user_id, $user_id );
        }
    }

}
add_action( 'bp_activity_remove_user_favorite', 'gamipress_bp_remove_favorite_activity', 10, 2 );

/* ----------------------------------------
 * BuddyPress Groups
 ------------------------------------------ */

// Write a group activity stream message
function gamipress_bp_group_publish_activity( $content, $user_id, $group_id, $activity_id ) {

    do_action( 'gamipress_bp_group_publish_activity', $activity_id, $user_id, $group_id );
    do_action( 'gamipress_bp_specific_group_publish_activity', $activity_id, $user_id, $group_id );

}
add_action( 'bp_groups_posted_update', 'gamipress_bp_group_publish_activity', 10, 4 );

// Create a group
function gamipress_bp_new_group( $new_group_id ) {

    // User id is handle automatically on gamipress_bp_trigger_get_user_id
    do_action( 'gamipress_bp_new_group', $new_group_id );

}
add_action( 'groups_group_create_complete', 'gamipress_bp_new_group' );

// Join a group and join a specific group
function gamipress_bp_join_group( $group_id, $user_id ) {

    // Join a group
    do_action( 'gamipress_bp_join_group', $group_id, $user_id );

    // Join a specific group
    do_action( 'gamipress_bp_join_specific_group', $group_id, $user_id );

}
add_action( 'groups_join_group', 'gamipress_bp_join_group', 10, 2 );

// Join a group and leave a specific group
function gamipress_bp_leave_group( $group_id, $user_id ) {

    // Leave a group
    do_action( 'gamipress_bp_leave_group', $group_id, $user_id );

    // Leave a specific group
    do_action( 'gamipress_bp_leave_specific_group', $group_id, $user_id );

}
add_action( 'groups_leave_group', 'gamipress_bp_leave_group', 10, 2 );

// Request join a private group
function gamipress_bp_request_join_private_group( $user_id, $admins, $group_id, $request_id ) {

    // Get request join to a private group
    do_action( 'gamipress_bp_request_join_private_group', $group_id, $user_id );

    // Get request join to a specific private group
    do_action( 'gamipress_bp_request_join_specific_private_group', $group_id, $user_id );

}
add_action( 'groups_membership_requested', 'gamipress_bp_request_join_private_group', 10, 4 );

// Join a private group and join a specific private group
// Note: User is not really joining to a private group, he gets accepted
function gamipress_bp_join_private_group( $user_id, $group_id, $inviter_id ) {

    // Get accepted on a private group
    do_action( 'gamipress_bp_join_private_group', $group_id, $user_id, $inviter_id );

    // Get accepted on a specific private group
    do_action( 'gamipress_bp_join_specific_private_group', $group_id, $user_id, $inviter_id );

}
add_action( 'groups_accept_invite', 'gamipress_bp_join_private_group', 10, 3 );

// Join a private group and join a specific private group
function gamipress_bp_membership_accepted( $user_id, $group_id, $accepted ) {

    // Bail if not accepted
    if( ! $accepted ) {
        return;
    }

    // Try to get the inviter
    $invites = groups_get_invites( array(
        'user_id' => $user_id,
        'item_id' => $group_id,
    ) );

    $inviter_id = 0;

    if ( $invites ) {
        $inviter_id = current( $invites )->inviter_id;
    }

    // Get accepted on a private group
    do_action( 'gamipress_bp_join_private_group', $group_id, $user_id, $inviter_id );

    // Get accepted on a specific private group
    do_action( 'gamipress_bp_join_specific_private_group', $group_id, $user_id, $inviter_id );

}
add_action( 'groups_membership_accepted', 'gamipress_bp_membership_accepted', 10, 3 );

// Invite someone to join a group
// Note: $args['user_id'] is invited user, $args['inviter_id'] or bp_loggedin_user_id() is the user to award
function gamipress_bp_invite_user( $args ) {

    $user_id = bp_loggedin_user_id();

    do_action( 'gamipress_bp_invite_user', $args['group_id'], $user_id );
    do_action( 'gamipress_bp_invite_user_specific_group', $args['group_id'], $user_id );

}
add_action( 'groups_invite_user', 'gamipress_bp_invite_user' );

// Promoted to group moderator/administrator
function gamipress_bp_promote_member( $group_id, $user_id, $status ) {

    do_action( 'gamipress_bp_promote_member', $group_id, $user_id );
    do_action( 'gamipress_bp_promote_member_specific_group', $group_id, $user_id );

}
add_action( 'groups_promote_member', 'gamipress_bp_promote_member', 10, 3 );

// Promote another group member to moderator/administrator
function gamipress_bp_promoted_member( $user_id, $group_id ) {

    do_action( 'gamipress_bp_promoted_member', $group_id, $user_id );
    do_action( 'gamipress_bp_promoted_member_specific_group', $group_id, $user_id );

}
add_action( 'groups_promoted_member', 'gamipress_bp_promoted_member', 10, 2 );