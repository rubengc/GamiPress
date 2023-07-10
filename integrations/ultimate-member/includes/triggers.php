<?php
/**
 * Triggers
 *
 * @package GamiPress\Ultimate_Member\Triggers
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
function gamipress_ultimate_member_activity_triggers( $triggers ) {

    $triggers[__( 'Ultimate Member', 'gamipress' )] = array(
        'gamipress_ultimate_member_user_approved'           => __( 'Account approved', 'gamipress' ),
        'gamipress_ultimate_member_user_inactive'           => __( 'Account marked as inactive', 'gamipress' ),
        'gamipress_ultimate_member_change_profile_photo'    => __( 'Change profile photo', 'gamipress' ),
        'gamipress_ultimate_member_change_cover_photo'      => __( 'Change cover photo', 'gamipress' ),
        'gamipress_ultimate_member_remove_profile_photo'    => __( 'Remove profile photo', 'gamipress' ),
        'gamipress_ultimate_member_remove_cover_photo'      => __( 'Remove cover photo', 'gamipress' ),
        'gamipress_ultimate_member_update_description'      => __( 'Update profile description', 'gamipress' ),
        'gamipress_ultimate_member_update_account'          => __( 'Update account information', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_ultimate_member_activity_triggers' );

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
function gamipress_ultimate_member_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_ultimate_member_user_approved':
        case 'gamipress_ultimate_member_user_inactive':
        case 'gamipress_ultimate_member_change_profile_photo':
        case 'gamipress_ultimate_member_change_cover_photo':
        case 'gamipress_ultimate_member_remove_profile_photo':
        case 'gamipress_ultimate_member_remove_cover_photo':
        case 'gamipress_ultimate_member_update_description':
        case 'gamipress_ultimate_member_update_account':
            $user_id = $args[0];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_ultimate_member_trigger_get_user_id', 10, 3 );