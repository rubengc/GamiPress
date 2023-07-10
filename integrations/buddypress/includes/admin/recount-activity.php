<?php
/**
 * Recount Activity
 *
 * @package GamiPress\BuddyPress\Admin\Recount_Activity
 * @since 1.0.6
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add recountable options to the Recount Activity Tool
 *
 * @since 1.0.6
 *
 * @param array $recountable_activity_triggers
 *
 * @return array
 */
function gamipress_bp_recountable_activity_triggers( $recountable_activity_triggers ) {

    // BuddyPress
    $recountable_activity_triggers[__( 'BuddyPress', 'gamipress' )] = array(
        'bp_activated_users' => __( 'Recount activated accounts', 'gamipress' ),
    );

    // BuddyPress Friendships
    if ( gamipress_bp_is_active( 'friends' ) ) {
        $recountable_activity_triggers[__( 'BuddyPress Friendships', 'gamipress' )] = array(
            'bp_friendships'   => __( 'Recount friendships', 'gamipress' ),
        );
    }

    // BuddyPress Messages
    if ( gamipress_bp_is_active( 'messages' ) ) {
        $recountable_activity_triggers[__( 'BuddyPress Messages', 'gamipress' )] = array(
            'bp_messages' => __( 'Recount messages sent/replied', 'gamipress' ),
        );
    }

    // BuddyPress Activity
    if ( gamipress_bp_is_active( 'activity' ) ) {
        $recountable_activity_triggers[__( 'BuddyPress Activity', 'gamipress' )] = array(
            'bp_published_activities'       => __( 'Recount activity stream messages', 'gamipress' ),
            'bp_activity_comments'          => __( 'Recount activity replies', 'gamipress' ),
            'bp_activity_favorites'         => __( 'Recount activity favorites', 'gamipress' ),
        );
    }

    // BuddyPress Groups
    if ( gamipress_bp_is_active( 'groups' ) ) {
        $recountable_activity_triggers[__( 'BuddyPress Groups', 'gamipress' )] = array(
            'bp_group_published_activities'   => __( 'Recount groups activity stream messages', 'gamipress' ),
            'bp_group_joins'                  => __( 'Recount groups joins', 'gamipress' ),
            'bp_group_promotions'             => __( 'Recount groups promotions to moderator/administrator', 'gamipress' ),
        );
    }

    return $recountable_activity_triggers;

}
add_filter( 'gamipress_recountable_activity_triggers', 'gamipress_bp_recountable_activity_triggers' );

/**
 * Recount favorites
 *
 * @since   1.0.6
 * @updated 1.1.7 Added $loop parameter
 *
 * @param array $response
 * @param int   $loop
 *
 * @return array $response
 */
function gamipress_bp_activity_recount_activated_users( $response, $loop ) {

    global $wpdb;

    // Set a limit of 100 users
    $limit = 100;
    $offset = ( $loop !== 0 ? $limit * ( $loop - 1 ) : 0 );

    // Get all stored users count
    $users_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users}" ) );

    // On first loop send an informational text
    if( $loop === 0 && $users_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count );

        // Return early to inform
        return $response;
    }

    // Get all stored users
    $users = $wpdb->get_results( "SELECT * FROM {$wpdb->users} LIMIT {$offset}, {$limit}" );

    foreach( $users as $user ) {

        if( bp_is_user_active( $user->ID ) ) {

            // Trigger activate user action for each user
            do_action( 'gamipress_bp_activate_user', $user, $user->ID );

        }

    }

    $recounted_users = $limit * ( $loop + 1 );

    // Check remaining users
    if( $recounted_users < $users_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining users to finish recount', 'gamipress' ), ( $users_count - $recounted_users ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_bp_activated_users', 'gamipress_bp_activity_recount_activated_users', 10, 2 );

/**
 * Recount friendships
 *
 * @since   1.0.6
 * @updated 1.1.7 Added $loop parameter
 *
 * @param array $response
 * @param int   $loop
 *
 * @return array $response
 */
function gamipress_bp_activity_recount_friendships( $response, $loop ) {

    if ( ! gamipress_bp_is_active( 'friends' ) ) {
        $response['success'] = false;
        $response['response'] = __( 'BuddyPress Friends module is not active.', 'gamipress' );

        return $response;
    }

    global $wpdb;

    // Set a limit of 100 users
    $limit = 100;
    $offset = ( $loop !== 0 ? $limit * ( $loop - 1 ) : 0 );

    $bp = buddypress();

    // Get all accepted friendships count
    $friendships_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$bp->friends->table_name} WHERE is_confirmed = 1" ) );

    // On first loop send an informational text
    if( $loop === 0 && $friendships_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d friendships found, recounting...', 'gamipress' ), $friendships_count );

        // Return early to inform
        return $response;
    }

    // Get all accepted friendships
    $friendships = $wpdb->get_results( "SELECT id, initiator_user_id, friend_user_id FROM {$bp->friends->table_name} WHERE is_confirmed = 1 LIMIT {$offset}, {$limit}" );

    foreach( $friendships as $friendship ) {

        // Trigger friendship accepted action for each friendship
        gamipress_bp_friendship_accepted( $friendship->id, $friendship->initiator_user_id, $friendship->friend_user_id );

    }

    $recounted_friendships = $limit * ( $loop + 1 );

    // Check remaining users
    if( $recounted_friendships < $friendships_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining friendships to finish recount', 'gamipress' ), ( $friendships_count - $recounted_friendships ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_bp_friendships', 'gamipress_bp_activity_recount_friendships', 10, 2 );

/**
 * Recount messages
 *
 * @since   1.0.6
 * @updated 1.1.7 Added $loop parameter
 *
 * @param array $response
 * @param int   $loop
 *
 * @return array $response
 */
function gamipress_bp_activity_recount_messages( $response, $loop ) {

    if ( ! gamipress_bp_is_active( 'messages' ) ) {
        $response['success'] = false;
        $response['response'] = __( 'BuddyPress Messages module is not active.', 'gamipress' );

        return $response;
    }

    global $wpdb;

    // Set a limit of 100 users
    $limit = 100;
    $offset = ( $loop !== 0 ? $limit * ( $loop - 1 ) : 0 );

    $bp = buddypress();

    // Get all messages count
    $messages_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$bp->messages->table_name}" ) );

    // On first loop send an informational text
    if( $loop === 0 && $messages_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d messages found, recounting...', 'gamipress' ), $messages_count );

        // Return early to inform
        return $response;
    }

    // Get all messages
    $messages = $wpdb->get_results( "SELECT * FROM {$bp->messages->table_name} LIMIT {$offset}, {$limit}" );

    foreach( $messages as $message ) {

        // Trigger message sent action for each message
        gamipress_bp_send_message( $message );

    }

    $recounted_messages = $limit * ( $loop + 1 );

    // Check remaining messages
    if( $recounted_messages < $messages_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining messages to finish recount', 'gamipress' ), ( $messages_count - $recounted_messages ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_bp_messages', 'gamipress_bp_activity_recount_messages', 10, 2 );

/**
 * Recount activity
 *
 * @since   1.0.6
 * @updated 1.1.7 Added $loop parameter
 *
 * @param array $response
 * @param int   $loop
 *
 * @return array $response
 */
function gamipress_bp_activity_recount_activities( $response, $loop ) {

    if ( ! gamipress_bp_is_active( 'activity' ) ) {
        $response['success'] = false;
        $response['response'] = __( 'BuddyPress Activity module is not active.', 'gamipress' );

        return $response;
    }

    global $wpdb;

    // Set a limit of 100 users
    $limit = 100;
    $offset = ( $loop !== 0 ? $limit * ( $loop - 1 ) : 0 );

    $bp = buddypress();

    // Get all activities count
    $activities_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$bp->activity->table_name} WHERE type = 'activity_update'" ) );

    // On first loop send an informational text
    if( $loop === 0 && $activities_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d activities found, recounting...', 'gamipress' ), $activities_count );

        // Return early to inform
        return $response;
    }

    // Get all activities
    $activities = $wpdb->get_results( "SELECT * FROM {$bp->activity->table_name} WHERE type = 'activity_update' LIMIT {$offset}, {$limit}" );

    foreach( $activities as $activity ) {

        // Trigger new activity stream action for each activity
        gamipress_bp_publish_activity( $activity->content, $activity->user_id, $activity->id );

    }

    $recounted_activities = $limit * ( $loop + 1 );

    // Check remaining activities
    if( $recounted_activities < $activities_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining activities to finish recount', 'gamipress' ), ( $activities_count - $recounted_activities ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_bp_published_activities', 'gamipress_bp_activity_recount_activities', 10, 2 );

/**
 * Recount activity comments
 *
 * @since   1.0.6
 * @updated 1.1.7 Added $loop parameter
 *
 * @param array $response
 * @param int   $loop
 *
 * @return array $response
 */
function gamipress_bp_activity_recount_activity_comments( $response, $loop ) {

    if ( ! gamipress_bp_is_active( 'activity' ) ) {
        $response['success'] = false;
        $response['response'] = __( 'BuddyPress Activity module is not active.', 'gamipress' );

        return $response;
    }

    global $wpdb;

    // Set a limit of 100 users
    $limit = 100;
    $offset = ( $loop !== 0 ? $limit * ( $loop - 1 ) : 0 );

    $bp = buddypress();

    // Get all activity comments count
    $activities_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$bp->activity->table_name} WHERE type = 'activity_comment'" ) );

    // On first loop send an informational text
    if( $loop === 0 && $activities_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d activities found, recounting...', 'gamipress' ), $activities_count );

        // Return early to inform
        return $response;
    }

    // Get all activity comments
    $activities = $wpdb->get_results( "SELECT * FROM {$bp->activity->table_name} WHERE type = 'activity_comment' LIMIT {$offset}, {$limit}" );

    foreach( $activities as $activity ) {

        // Trigger new activity stream action for each activity
        gamipress_bp_new_activity_comment( $activity->id, $activity, $activity );

    }

    $recounted_activities = $limit * ( $loop + 1 );

    // Check remaining activities
    if( $recounted_activities < $activities_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining activities to finish recount', 'gamipress' ), ( $activities_count - $recounted_activities ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_bp_activity_comments', 'gamipress_bp_activity_recount_activity_comments', 10, 2 );

/**
 * Recount activity favorites
 *
 * @since   1.0.6
 * @updated 1.1.7 Added $loop parameter
 *
 * @param array $response
 * @param int   $loop
 *
 * @return array $response
 */
function gamipress_bp_activity_recount_activity_favorites( $response, $loop ) {

    if ( ! gamipress_bp_is_active( 'activity' ) ) {
        $response['success'] = false;
        $response['response'] = __( 'BuddyPress Activity module is not active.', 'gamipress' );

        return $response;
    }

    global $wpdb;

    // Set a limit of 100 users
    $limit = 100;
    $offset = ( $loop !== 0 ? $limit * ( $loop - 1 ) : 0 );

    // Get all stored users count
    $users_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users}" ) );

    // On first loop send an informational text
    if( $loop === 0 && $users_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count );

        // Return early to inform
        return $response;
    }

    // Get all stored users
    $users = $wpdb->get_results( "SELECT ID FROM {$wpdb->users} LIMIT {$offset}, {$limit}" );

    foreach( $users as $user ) {

        $user_favorites = bp_activity_get_user_favorites( $user->ID );

        foreach( $user_favorites as $activity_id ) {

            // Trigger new activity favorite action for each activity
            gamipress_bp_favorite_activity( $activity_id, $user->ID );

        }
    }

    $recounted_users = $limit * ( $loop + 1 );

    // Check remaining users
    if( $recounted_users < $users_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining users to finish recount', 'gamipress' ), ( $users_count - $recounted_users ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_bp_activity_favorites', 'gamipress_bp_activity_recount_activity_favorites', 10, 2 );

/**
 * Recount group activity
 *
 * @since   1.0.6
 * @updated 1.1.7 Added $loop parameter
 *
 * @param array $response
 * @param int   $loop
 *
 * @return array $response
 */
function gamipress_bp_activity_recount_activity_group_published_activities( $response, $loop ) {

    if ( ! gamipress_bp_is_active( 'groups' ) ) {
        $response['success'] = false;
        $response['response'] = __( 'BuddyPress Groups module is not active.', 'gamipress' );

        return $response;
    }

    global $wpdb;

    // Set a limit of 10 groups
    $limit = 10;
    $offset = ( $loop !== 0 ? $limit * ( $loop - 1 ) : 0 );

    $bp = buddypress();

    // Get all groups count
    $groups_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$bp->groups->table_name}" ) );

    // On first loop send an informational text
    if( $loop === 0 && $groups_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d groups found, recounting...', 'gamipress' ), $groups_count );

        // Return early to inform
        return $response;
    }

    // Get all groups
    $groups = $wpdb->get_results( "SELECT id FROM {$bp->groups->table_name} LIMIT {$offset}, {$limit}" );

    foreach( $groups as $group ) {

        // Get all activities
        $activities = $wpdb->get_results( "SELECT * FROM {$bp->activity->table_name} WHERE type = 'activity_update' AND item_id = {$group->id}" );

        foreach( $activities as $activity ) {

            // Trigger new group activity stream action for each activity
            gamipress_bp_group_publish_activity( $activity->content, $activity->user_id, $group->id, $activity->id );

        }

    }

    $recounted_groups = $limit * ( $loop + 1 );

    // Check remaining groups
    if( $recounted_groups < $groups_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining groups to finish recount', 'gamipress' ), ( $groups_count - $recounted_groups ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_bp_group_published_activities', 'gamipress_bp_activity_recount_activity_group_published_activities', 10, 2 );

/**
 * Recount group joins
 *
 * @since   1.0.6
 * @updated 1.1.7 Added $loop parameter
 *
 * @param array $response
 * @param int   $loop
 *
 * @return array $response
 */
function gamipress_bp_activity_recount_activity_group_joins( $response, $loop ) {

    if ( ! gamipress_bp_is_active( 'groups' ) ) {
        $response['success'] = false;
        $response['response'] = __( 'BuddyPress Groups module is not active.', 'gamipress' );

        return $response;
    }

    global $wpdb;

    // Set a limit of 10 groups
    $limit = 10;
    $offset = ( $loop !== 0 ? $limit * ( $loop - 1 ) : 0 );

    $bp = buddypress();

    // Get all groups count
    $groups_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$bp->groups->table_name}" ) );

    // On first loop send an informational text
    if( $loop === 0 && $groups_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d groups found, recounting...', 'gamipress' ), $groups_count );

        // Return early to inform
        return $response;
    }

    // Get all groups
    $groups = $wpdb->get_results( "SELECT id FROM {$bp->groups->table_name} LIMIT {$offset}, {$limit}" );

    foreach( $groups as $group ) {

        // Get all activities
        $activities = $wpdb->get_results( "SELECT * FROM {$bp->activity->table_name} WHERE type = 'joined_group' AND item_id = {$group->id}" );

        foreach( $activities as $activity ) {

            // Trigger new group join action for each activity
            gamipress_bp_join_group( $activity->user_id, $group->id );

        }

    }

    $recounted_groups = $limit * ( $loop + 1 );

    // Check remaining groups
    if( $recounted_groups < $groups_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining groups to finish recount', 'gamipress' ), ( $groups_count - $recounted_groups ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_bp_group_joins', 'gamipress_bp_activity_recount_activity_group_joins', 10, 2 );

/**
 * Recount group promotions
 *
 * @since   1.0.6
 * @updated 1.1.7 Added $loop parameter
 *
 * @param array $response
 * @param int   $loop
 *
 * @return array $response
 */
function gamipress_bp_activity_recount_activity_group_promotions( $response, $loop ) {

    if ( ! gamipress_bp_is_active( 'groups' ) ) {
        $response['success'] = false;
        $response['response'] = __( 'BuddyPress Groups module is not active.', 'gamipress' );

        return $response;
    }

    global $wpdb;

    // Set a limit of 10 groups
    $limit = 10;
    $offset = ( $loop !== 0 ? $limit * ( $loop - 1 ) : 0 );

    $bp = buddypress();

    // Get all groups count
    $groups_members_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$bp->groups->table_name_members}" ) );

    // On first loop send an informational text
    if( $loop === 0 && $groups_members_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d group members found, recounting...', 'gamipress' ), $groups_members_count );

        // Return early to inform
        return $response;
    }

    // Get all groups
    $groups_members = $wpdb->get_results( "SELECT * FROM {$bp->groups->table_name_members} LIMIT {$offset}, {$limit}" );

    foreach( $groups_members as $group_member ) {

        if( $group_member->is_admin || $group_member->is_mod ) {
            // Trigger new promoted user action for each group member mod/admin
            gamipress_bp_promoted_member( $group_member->group_id, $group_member->user_id );
        }
    }

    $recounted_group_members = $limit * ( $loop + 1 );

    // Check remaining group members
    if( $recounted_group_members < $groups_members_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining group members to finish recount', 'gamipress' ), ( $groups_members_count - $recounted_group_members ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_bp_group_promotions', 'gamipress_bp_activity_recount_activity_group_promotions', 10, 2 );