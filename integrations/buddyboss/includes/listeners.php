<?php
/**
 * Listeners
 *
 * @package GamiPress\BuddyBoss\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/* ----------------------------------------
 * BuddyBoss Profile
 ------------------------------------------ */

// Profile progress listener
function gamipress_buddyboss_profile_progress( $progress_details ) {

    if( $progress_details['total_fields'] === 0 ) {
        return $progress_details;
    }

    $user_id = get_current_user_id();
    $percentage = round( ( $progress_details['completed_fields'] * 100 ) / $progress_details['total_fields'] );

    do_action( 'gamipress_buddyboss_profile_progress', $user_id, $percentage );

    return $progress_details;

}
add_filter( 'xprofile_pc_user_progress', 'gamipress_buddyboss_profile_progress' );

// Forces to trigger profile progress hook (for installs where widget is not set)
function gamipress_buddyboss_trigger_profile_progress() {

    if( ! function_exists( 'bp_xprofile_get_user_progress' ) ) {
        return;
    }

    if ( is_active_widget( false, false, 'bp_xprofile_profile_completion_widget', true ) ) {
        return;
    }

    if ( is_active_widget( false, false, 'BP_Xprofile_Profile_Completion_Widget', true ) ) {
        return;
    }

    bp_xprofile_get_user_progress( array(), array( 'profile_photo', 'cover_photo' ) );

}
add_action( 'xprofile_avatar_uploaded', 'gamipress_buddyboss_trigger_profile_progress', 9999 );
add_action( 'xprofile_cover_image_uploaded', 'gamipress_buddyboss_trigger_profile_progress', 9999 );
add_action( 'xprofile_updated_profile', 'gamipress_buddyboss_trigger_profile_progress', 9999 );

/* ----------------------------------------
 * BuddyBoss Follow
 ------------------------------------------ */

/**
 * Start following listener
 *
 * @since 1.0.0
 *
 * @param object $follow Follow object.
 */
function gamipress_buddyboss_start_following_listener( $follow ) {

    do_action( 'gamipress_buddyboss_start_following', $follow->leader_id, $follow->follower_id );

    do_action( 'gamipress_buddyboss_get_follower', $follow->leader_id, $follow->follower_id );

}
add_action( 'bp_start_following', 'gamipress_buddyboss_start_following_listener' );

/**
 * Stop following listener
 *
 * @since 1.0.0
 *
 * @param object $follow Follow object.
 */
function gamipress_buddyboss_stop_following_listener( $follow ) {

    do_action( 'gamipress_buddyboss_stop_following', $follow->leader_id, $follow->follower_id );

    do_action( 'gamipress_buddyboss_lose_follower', $follow->leader_id, $follow->follower_id );

}
add_action( 'bp_stop_following', 'gamipress_buddyboss_stop_following_listener' );

/**
 * Email invite listener
 *
 * @since 1.0.0
 *
 * @param int $user_id Inviter user id.
 * @param int $post_id Invitation post id.
 */
function gamipress_buddyboss_send_email_invite_listener( $user_id, $post_id ) {

    // Trigger send an invitation event
    do_action( 'gamipress_buddyboss_send_email_invite', $user_id, $post_id );

}
add_action( 'bp_member_invite_submit', 'gamipress_buddyboss_send_email_invite_listener', 10, 2 );

/**
 * Invited user registered
 *
 * @since 1.0.0
 *
 * @param int $user_id      Invitee user id.
 * @param int $inviter_id   Inviter user id.
 * @param int $post_id      Invitation post id.
 */
function gamipress_buddyboss_email_invited_registered_listener( $user_id, $inviter_id, $post_id ) {

    // Trigger invited user gets registered (award invited user)
    do_action( 'gamipress_buddyboss_email_invited_registered', $user_id, $inviter_id, $post_id );

    // Trigger get an invited user registered (award user that sent the invitation)
    do_action( 'gamipress_buddyboss_get_email_invited_registered', $user_id, $inviter_id, $post_id );

}
add_action( 'bp_invites_member_invite_mark_register_user', 'gamipress_buddyboss_email_invited_registered_listener', 10, 3 );

/**
 * Invited user activated
 *
 * @since 1.0.0
 *
 * @param int $user_id      Invitee user id.
 * @param int $inviter_id   Inviter user id.
 * @param int $post_id      Invitation post id.
 */
function gamipress_buddyboss_email_invited_activated_listener( $user_id, $inviter_id, $post_id ) {

    // Trigger invited user account gets activated (award invited user)
    do_action( 'gamipress_buddyboss_email_invited_activated', $user_id, $inviter_id, $post_id );

    // Trigger get an invited user account activated (award user that sent the invitation)
    do_action( 'gamipress_buddyboss_get_email_invited_activated', $user_id, $inviter_id, $post_id );

}
add_action( 'bp_invites_member_invite_activate_user', 'gamipress_buddyboss_email_invited_activated_listener', 10, 3 );
