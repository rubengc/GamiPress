<?php
/**
 * Listeners
 *
 * @package GamiPress\WP_User_Manager\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Login listener
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param WP_User $user
 */
function gamipress_wp_user_manager_login( $user_id, $user ) {

    // Trigger user login
    do_action( 'gamipress_wp_user_manager_login', $user_id );

}
add_action( 'wpum_after_login', 'gamipress_wp_user_manager_login', 10, 2 );

/**
 * Register listener
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param array $values
 * @param WPUM_Registration_Form $form
 */
function gamipress_wp_user_manager_register( $user_id, $values, $form ) {

    $form_id = $form->get_ID();

    // Trigger register through form
    do_action( 'gamipress_wp_user_manager_register', $form_id, $user_id );

    // Trigger register through specific form
    do_action( 'gamipress_wp_user_manager_register_specific_form', $form_id, $user_id );

}
add_action( 'wpum_after_registration', 'gamipress_wp_user_manager_register', 10, 3 );

/**
 * User approved listener
 *
 * @since 1.0.0
 *
 * @param int $user_id
 */
function gamipress_wp_user_manager_user_approved( $user_id ) {

    // Trigger user approval
    do_action( 'gamipress_wp_user_manager_user_approved', $user_id );

}
add_action( 'wpumuv_after_user_approval', 'gamipress_wp_user_manager_user_approved', 10, 1 );

/**
 * User rejected listener
 *
 * @since 1.0.0
 *
 * @param int $user_id
 */
function gamipress_wp_user_manager_user_rejected( $user_id ) {

    // Trigger user rejection
    do_action( 'gamipress_wp_user_manager_user_rejected', $user_id );

}
add_action( 'wpumuv_before_user_rejection', 'gamipress_wp_user_manager_user_rejected', 10, 1 );

/**
 * User verified listener
 *
 * @since 1.0.0
 *
 * @param int $user_id
 */
function gamipress_wp_user_manager_user_verified( $user_id ) {

    // Trigger user verification
    do_action( 'gamipress_wp_user_manager_user_verified', $user_id );

}
add_action( 'wpumuv_after_user_verification', 'gamipress_wp_user_manager_user_verified', 10, 1 );

/**
 * Change avatar listener
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param string $image_url
 */
function gamipress_wp_user_manager_change_avatar( $user_id, $image_url ) {

    // Trigger change avatar
    do_action( 'gamipress_wp_user_manager_change_avatar', $user_id );

}
add_action( 'wpum_user_update_change_avatar', 'gamipress_wp_user_manager_change_avatar', 10, 2 );

/**
 * Remove avatar listener
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param string $image_url
 */
function gamipress_wp_user_manager_remove_avatar( $user_id, $image_url ) {

    // Trigger remove avatar
    do_action( 'gamipress_wp_user_manager_remove_avatar', $user_id );

}
add_action( 'wpum_user_update_remove_avatar', 'gamipress_wp_user_manager_remove_avatar', 10, 2 );

/**
 * Change cover listener
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param string $image_url
 */
function gamipress_wp_user_manager_change_cover( $user_id, $image_url ) {

    // Trigger change cover
    do_action( 'gamipress_wp_user_manager_change_cover', $user_id );

}
add_action( 'wpum_user_update_change_cover', 'gamipress_wp_user_manager_change_cover', 10, 2 );

/**
 * Remove cover listener
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param string $image_url
 */
function gamipress_wp_user_manager_remove_cover( $user_id, $image_url ) {

    // Trigger remove cover
    do_action( 'gamipress_wp_user_manager_remove_cover', $user_id );

}
add_action( 'wpum_user_update_remove_cover', 'gamipress_wp_user_manager_remove_cover', 10, 2 );

/**
 * Change description listener
 *
 * @since 1.0.0
 *
 * @param WPUM_Form_Profile $form
 * @param array $values
 * @param int $user_id
 */
function gamipress_wp_user_manager_change_description( $form, $values, $user_id ) {

    // Bail if description doesn't gets updated
    if ( ! isset( $values['account']['user_description'] ) ) {
        return;
    }

    // Trigger change description
    do_action( 'gamipress_wp_user_manager_change_description', $user_id );

}
add_action( 'wpum_before_user_update', 'gamipress_wp_user_manager_change_description', 10, 3 );

/**
 * Join group listener
 *
 * @since 1.0.0
 *
 * @param int $group_id
 * @param int $user_id
 * @param string $privacy_method
 */
function gamipress_wp_user_manager_join_group( $group_id, $user_id, $privacy_method ) {

    // Trigger join a group
    do_action( 'gamipress_wp_user_manager_join_group', $group_id, $user_id );

    // Trigger join a specific group
    do_action( 'gamipress_wp_user_manager_join_specific_group', $group_id, $user_id );

}
add_action( 'wpumgp_after_member_join', 'gamipress_wp_user_manager_join_group', 10, 3 );

/**
 * Leave group listener
 *
 * @since 1.0.0
 *
 * @param int $group_id
 * @param int $user_id
 * @param string $privacy_method
 */
function gamipress_wp_user_manager_leave_group( $group_id, $user_id, $privacy_method ) {

    // Trigger leave a group
    do_action( 'gamipress_wp_user_manager_leave_group', $group_id, $user_id );

    // Trigger leave a specific group
    do_action( 'gamipress_wp_user_manager_leave_specific_group', $group_id, $user_id );

}
add_action( 'wpumgp_after_member_leave', 'gamipress_wp_user_manager_leave_group', 10, 3 );

/**
 * Accepted group listener
 *
 * @since 1.0.0
 *
 * @param int $group_id
 * @param int $user_id
 */
function gamipress_wp_user_manager_accepted_group( $group_id, $user_id ) {

    // Trigger get accepted in a group
    do_action( 'gamipress_wp_user_manager_accepted_group', $group_id, $user_id );

    // Trigger get accepted in a specific group
    do_action( 'gamipress_wp_user_manager_accepted_specific_group', $group_id, $user_id );

}
add_action( 'wpumgp_after_membership_approved', 'gamipress_wp_user_manager_accepted_group', 10, 2 );

/**
 * Rejected group listener
 *
 * @since 1.0.0
 *
 * @param int $group_id
 * @param int $user_id
 */
function gamipress_wp_user_manager_rejected_group( $group_id, $user_id ) {

    // Trigger get rejected from a group
    do_action( 'gamipress_wp_user_manager_rejected_group', $group_id, $user_id );

    // Trigger get rejected from a specific group
    do_action( 'gamipress_wp_user_manager_rejected_specific_group', $group_id, $user_id );

}
add_action( 'wpumgp_after_membership_rejected', 'gamipress_wp_user_manager_rejected_group', 10, 2 );