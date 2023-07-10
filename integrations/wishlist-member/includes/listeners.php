<?php
/**
 * Listeners
 *
 * @package GamiPress\WishList_Member\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Trigger listener
 *
 * @since 1.0.0
 *
 * @param int   $user_id    The user ID
 * @param array $levels_ids Levels added to the user
 */
function gamipress_wishlist_member_common_listener( $user_id, $levels_ids ) {

    $action = str_replace( 'wishlistmember_', '', str_replace( '_user_levels', '', current_filter() ) );

    foreach( $levels_ids as $level_id ) {

        // Trigger add any level
        do_action( "gamipress_wishlist_member_{$action}_level", $level_id, $user_id );

        // Trigger add to specific level
        do_action( "gamipress_wishlist_member_{$action}_specific_level", $level_id, $user_id );

    }

}
add_action( 'wishlistmember_add_user_levels', 'gamipress_wishlist_member_common_listener', 10, 2 );
add_action( 'wishlistmember_remove_user_levels', 'gamipress_wishlist_member_common_listener', 10, 2 );
add_action( 'wishlistmember_approve_user_levels', 'gamipress_wishlist_member_common_listener', 10, 2 );
add_action( 'wishlistmember_unapprove_user_levels', 'gamipress_wishlist_member_common_listener', 10, 2 );
add_action( 'wishlistmember_cancel_user_levels', 'gamipress_wishlist_member_common_listener', 10, 2 );
add_action( 'wishlistmember_uncancel_user_levels', 'gamipress_wishlist_member_common_listener', 10, 2 );