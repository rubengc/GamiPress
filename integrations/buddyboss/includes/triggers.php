<?php
/**
 * Triggers
 *
 * @package GamiPress\BuddyBoss\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register BuddyPress specific triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_buddyboss_activity_triggers( $triggers ) {

    // BuddyBoss
    $triggers[__( 'BuddyBoss', 'gamipress' )] = array(
        // Triggers from BuddyPress
        'gamipress_bp_activate_user' => __( 'Activated account', 'gamipress' ),
        'gamipress_bp_set_member_type' => __( 'Get assigned to a specific profile type', 'gamipress' ),
    );

    // BuddyBoss Follow
    if ( function_exists( 'bp_is_activity_follow_active' ) && bp_is_activity_follow_active() ) {
        $triggers[__( 'BuddyBoss Follow', 'gamipress' )] = array(
            'gamipress_buddyboss_start_following'   => __( 'Start following someone', 'gamipress' ),
            'gamipress_buddyboss_stop_following'    => __( 'Stop following someone', 'gamipress' ),
            'gamipress_buddyboss_get_follower'      => __( 'Get a follower', 'gamipress' ),
            'gamipress_buddyboss_lose_follower'     => __( 'Lose a follower', 'gamipress' ),
        );
    }

    // BuddyBoss Email Invites
    if(  gamipress_bp_is_active( 'invites' ) ) {
        $triggers[__( 'BuddyBoss Email Invites', 'gamipress' )] = array(
            'gamipress_buddyboss_send_email_invite'             => __( 'Send an email invitation', 'gamipress' ),
            'gamipress_buddyboss_email_invited_registered'      => __( 'Register from email invitation', 'gamipress' ),
            'gamipress_buddyboss_get_email_invited_registered'  => __( 'Get an invited user registered', 'gamipress' ),
            'gamipress_buddyboss_email_invited_activated'       => __( 'Account from email invitation gets activated', 'gamipress' ),
            'gamipress_buddyboss_get_email_invited_activated'   => __( 'Get an invited user account activated', 'gamipress' ),
        );
    }

    $triggers = apply_filters( 'gamipress_buddyboss_activity_triggers', $triggers );

    // BuddyBoss Profile
    if ( gamipress_bp_is_active( 'xprofile' ) ) {
        $triggers[__( 'BuddyBoss Profile', 'gamipress' )]['gamipress_buddyboss_profile_progress'] = __( 'Complete a minimum percent of your profile', 'gamipress' );
    }

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_buddyboss_activity_triggers' );

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
function gamipress_buddyboss_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $buddyboss_percentage = ( isset( $requirement['buddyboss_percentage'] ) ) ? $requirement['buddyboss_percentage'] : 0;

    switch( $requirement['trigger_type'] ) {

        case 'gamipress_buddyboss_profile_progress':
            return sprintf( __( 'Complete the %s of your profile', 'gamipress' ), $buddyboss_percentage . '%' );
            break;

    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_buddyboss_activity_trigger_label', 10, 3 );

/**
 * Get activity triggers excluded from activity time limits
 *
 * @since 1.0.0
 *
 * @param array $activities_excluded
 *
 * @return array
 */
function gamipress_buddyboss_activity_triggers_excluded_from_activity_limit( $activities_excluded ) {

    $activities_excluded[] = 'gamipress_buddyboss_profile_progress';

    return $activities_excluded;

}
add_filter( 'gamipress_activity_triggers_excluded_from_activity_limit', 'gamipress_buddyboss_activity_triggers_excluded_from_activity_limit' );

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
function gamipress_buddyboss_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Follow/unfollow
        case 'gamipress_buddyboss_start_following':
        case 'gamipress_buddyboss_stop_following':
        // Email invites
        case 'gamipress_buddyboss_get_email_invited_registered':
        case 'gamipress_buddyboss_get_email_invited_activated':
            $user_id = $args[1];
            break;
        // Get/lose a follower
        case 'gamipress_buddyboss_get_follower':
        case 'gamipress_buddyboss_lose_follower':
        // Email invites
        case 'gamipress_buddyboss_send_email_invite':
        case 'gamipress_buddyboss_email_invited_registered':
        case 'gamipress_buddyboss_email_invited_activated':
        // BuddyBoss Profile
        case 'gamipress_buddyboss_profile_progress':
            $user_id = $args[0];
            break;
    }

    return $user_id;

}

add_filter( 'gamipress_trigger_get_user_id', 'gamipress_buddyboss_trigger_get_user_id', 10, 3);

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.5
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_buddyboss_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Follow/unfollow
        case 'gamipress_buddyboss_start_following':
        case 'gamipress_buddyboss_stop_following':
            // Add the leader ID
            $log_meta['leader_id'] = $args[0];
            break;
        // Get/lose a follower
        case 'gamipress_buddyboss_get_follower':
        case 'gamipress_buddyboss_lose_follower':
            // Add the follower ID
            $log_meta['follower_id'] = $args[1];
            break;
        // Email invites
        case 'gamipress_buddyboss_send_email_invite':
            // Add the invitation ID
            $log_meta['post_id'] = $args[1];
            break;
        case 'gamipress_buddyboss_get_email_invited_registered':
        case 'gamipress_buddyboss_get_email_invited_activated':
        case 'gamipress_buddyboss_email_invited_registered':
        case 'gamipress_buddyboss_email_invited_activated':
            // Add the inviter and invitation ID
            $log_meta['inviter_id'] = $args[1];
            $log_meta['post_id'] = $args[2];
            break;
        // BuddyBoss Profile
        case 'gamipress_buddyboss_profile_progress':
            // Add the percentage completed
            $log_meta['percentage'] = $args[1];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_buddyboss_log_event_trigger_meta_data', 10, 5 );

/**
 * Extra filter to check duplicated activity
 *
 * @since 1.0.2
 *
 * @param bool 		$return
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return bool					True if user deserves trigger, else false
 */
function gamipress_buddyboss_trigger_duplicity_check( $return, $user_id, $trigger, $site_id, $args  ) {

    // If user doesn't deserves trigger, then bail to prevent grant access
    if( ! $return )
        return $return;

    $log_meta = array(
        'type' => 'event_trigger',
        'trigger_type' => $trigger,
    );

    switch ( $trigger ) {
        case 'gamipress_buddyboss_profile_progress':
            // Prevent to reward the user for complete the same percentage
            $log_meta['percentage'] = $args[1];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
    }

    return $return;

}
add_filter( 'gamipress_user_deserves_trigger', 'gamipress_buddyboss_trigger_duplicity_check', 10, 5 );