<?php
/**
 * Listeners
 *
 * @package GamiPress\Ultimate_Member\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * User account approved
 *
 * @since 1.0.0
 *
 * @param int       $user_id    The user ID
 */
function gamipress_ultimate_member_user_approved( $user_id ) {

    // Trigger account approved
    do_action( 'gamipress_ultimate_member_user_approved', $user_id );

}
add_action( 'um_after_user_is_approved', 'gamipress_ultimate_member_user_approved' );

/**
 * User account inactive
 *
 * @since 1.0.0
 *
 * @param int       $user_id    The user ID
 */
function gamipress_ultimate_member_user_inactive( $user_id ) {

    // Trigger account marked as inactive
    do_action( 'gamipress_ultimate_member_user_inactive', $user_id );

}
add_action( 'um_after_user_is_inactive', 'gamipress_ultimate_member_user_inactive' );

/**
 * Update profile listener
 *
 * @since 1.0.0
 *
 * @param int       $user_id    The user ID
 * @param array     $args       Contains all form fields
 * @param array     $userinfo   Contains all field to be updated
 */
function gamipress_ultimate_member_update_profile( $user_id, $args, $userinfo ) {

    $user_id = absint( $user_id );

    // Prevent award if admin edits another user profile
    if( get_current_user_id() !== $user_id ) {
        return;
    }

    if( isset( $userinfo['description'] ) ) {
        // Trigger update profile description
        do_action( 'gamipress_ultimate_member_update_description', $user_id, $userinfo['description'] );
    }

}
add_action( 'um_after_user_updated', 'gamipress_ultimate_member_update_profile', 10, 3 );

/**
 * User upload listener
 *
 * @since 1.0.0
 *
 * @param int       $user_id    The user ID
 * @param string    $key        The key of item uploaded ('profile_photo'|'cover_photo')
 */
function gamipress_ultimate_member_new_user_upload( $user_id, $key ) {

    // Trigger change profile/cover photo
    // Events triggered are 'gamipress_ultimate_member_change_profile_photo' and 'gamipress_ultimate_member_change_cover_photo'
    do_action( "gamipress_ultimate_member_change_{$key}", $user_id );

}
add_action( 'um_after_upload_db_meta', 'gamipress_ultimate_member_new_user_upload', 10, 2 );

/**
 * User remove profile photo listener
 *
 * @since 1.0.0
 *
 * @param int       $user_id    The user ID
 */
function gamipress_ultimate_member_remove_profile_photo( $user_id ) {

    // Trigger remove profile photo
    do_action( 'gamipress_ultimate_member_remove_profile_photo', $user_id );

}
add_action( 'um_after_remove_profile_photo', 'gamipress_ultimate_member_remove_profile_photo' );

/**
 * User remove cover photo listener
 *
 * @since 1.0.0
 *
 * @param int       $user_id    The user ID
 */
function gamipress_ultimate_member_remove_cover_photo( $user_id ) {

    // Trigger remove cover photo
    do_action( 'gamipress_ultimate_member_remove_cover_photo', $user_id );

}
add_action( 'um_after_remove_cover_photo', 'gamipress_ultimate_member_remove_cover_photo' );

/**
 * Update account listener
 *
 * @since 1.0.0
 *
 * @param int       $user_id    The user ID
 * @param array     $changes    Fields changed
 */
function gamipress_ultimate_member_update_account( $user_id, $changes ) {

    // Trigger update account information
    do_action( 'gamipress_ultimate_member_update_account', $user_id, $changes );

}
add_action( 'um_after_user_account_updated', 'gamipress_ultimate_member_update_account', 10, 2 );