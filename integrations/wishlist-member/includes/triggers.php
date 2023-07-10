<?php
/**
 * Triggers
 *
 * @package GamiPress\WishList_Member\Triggers
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin specific triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_wishlist_member_activity_triggers( $triggers ) {

    $triggers[__( 'WishList Member', 'gamipress' )] = array(

        'gamipress_wishlist_member_add_level'                   => __( 'Get added to any level', 'gamipress' ),
        'gamipress_wishlist_member_add_specific_level'          => __( 'Get added to specific level', 'gamipress' ),

        'gamipress_wishlist_member_remove_level'                => __( 'Get removed from any level', 'gamipress' ),
        'gamipress_wishlist_member_remove_specific_level'       => __( 'Get removed from specific level', 'gamipress' ),

        'gamipress_wishlist_member_approve_level'               => __( 'Get approved on any level', 'gamipress' ),
        'gamipress_wishlist_member_approve_specific_level'      => __( 'Get approved on specific level', 'gamipress' ),

        'gamipress_wishlist_member_unapprove_level'             => __( 'Get unapproved from any level', 'gamipress' ),
        'gamipress_wishlist_member_unapprove_specific_level'    => __( 'Get unapproved from specific level', 'gamipress' ),

        'gamipress_wishlist_member_cancel_level'                => __( 'Get cancelled on any level', 'gamipress' ),
        'gamipress_wishlist_member_cancel_specific_level'       => __( 'Get cancelled on specific level', 'gamipress' ),

        'gamipress_wishlist_member_uncancel_level'              => __( 'Get uncancelled on any level', 'gamipress' ),
        'gamipress_wishlist_member_uncancel_specific_level'     => __( 'Get uncancelled on specific level', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_wishlist_member_activity_triggers' );

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
function gamipress_wishlist_member_activity_trigger_label( $title, $requirement_id, $requirement ) {

    global $WishListMemberInstance;

    $level_id = ( isset( $requirement['wishlist_member_level_id'] ) ) ? $requirement['wishlist_member_level_id'] : '';
    $level_title = '';

    switch( $requirement['trigger_type'] ) {
        case 'gamipress_wishlist_member_add_specific_level':
        case 'gamipress_wishlist_member_remove_specific_level':
        case 'gamipress_wishlist_member_approve_specific_level':
        case 'gamipress_wishlist_member_unapprove_specific_level':
        case 'gamipress_wishlist_member_cancel_specific_level':
        case 'gamipress_wishlist_member_uncancel_specific_level':
            if( function_exists( 'wlmapi_get_levels' ) ) {

                // Get all registered levels
                $levels = wlmapi_get_levels();

                // Check that levels are correctly setup
                if ( is_array( $levels )
                    && isset( $levels['levels'] )
                    && isset( $levels['levels']['level'] )
                    && ! empty( $levels['levels']['level'] ) ) {

                    // Loop levels to add them as options
                    foreach ( $levels['levels']['level'] as $level ) {
                        if( $level['id'] === $level_id ) {
                            $level_title = $level['name'];
                        }
                    }

                }

            }
            break;
    }

    switch( $requirement['trigger_type'] ) {
        case 'gamipress_wishlist_member_add_specific_level':
            return sprintf( __( 'Get added to %s level', 'gamipress' ), $level_title );
            break;
        case 'gamipress_wishlist_member_remove_specific_level':
            return sprintf( __( 'Get removed from %s level', 'gamipress' ), $level_title );
            break;
        case 'gamipress_wishlist_member_approve_specific_level':
            return sprintf( __( 'Get approved on %s level', 'gamipress' ), $level_title );
            break;
        case 'gamipress_wishlist_member_unapprove_specific_level':
            return sprintf( __( 'Get unapproved from %s level', 'gamipress' ), $level_title );
            break;
        case 'gamipress_wishlist_member_cancel_specific_level':
            return sprintf( __( 'Get cancelled on %s level', 'gamipress' ), $level_title );
            break;
        case 'gamipress_wishlist_member_uncancel_specific_level':
            return sprintf( __( 'Get uncancelled on %s level', 'gamipress' ), $level_title );
            break;
    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_wishlist_member_activity_trigger_label', 10, 3 );

/**
 * Get user for a given trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $user_id user ID to override.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          User ID.
 */
function gamipress_wishlist_member_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {

        case 'gamipress_wishlist_member_add_level':
        case 'gamipress_wishlist_member_add_specific_level':

        case 'gamipress_wishlist_member_remove_level':
        case 'gamipress_wishlist_member_remove_specific_level':

        case 'gamipress_wishlist_member_approve_level':
        case 'gamipress_wishlist_member_approve_specific_level':

        case 'gamipress_wishlist_member_unapprove_level':
        case 'gamipress_wishlist_member_unapprove_specific_level':

        case 'gamipress_wishlist_member_cancel_level':
        case 'gamipress_wishlist_member_cancel_specific_level':

        case 'gamipress_wishlist_member_uncancel_level':
        case 'gamipress_wishlist_member_uncancel_specific_level':
            $user_id = $args[1];
            break;

    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_wishlist_member_trigger_get_user_id', 10, 3 );

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
function gamipress_wishlist_member_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_wishlist_member_add_level':
        case 'gamipress_wishlist_member_add_specific_level':

        case 'gamipress_wishlist_member_remove_level':
        case 'gamipress_wishlist_member_remove_specific_level':

        case 'gamipress_wishlist_member_approve_level':
        case 'gamipress_wishlist_member_approve_specific_level':

        case 'gamipress_wishlist_member_unapprove_level':
        case 'gamipress_wishlist_member_unapprove_specific_level':

        case 'gamipress_wishlist_member_cancel_level':
        case 'gamipress_wishlist_member_cancel_specific_level':

        case 'gamipress_wishlist_member_uncancel_level':
        case 'gamipress_wishlist_member_uncancel_specific_level':
            // Add the level ID
            $log_meta['level_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_wishlist_member_log_event_trigger_meta_data', 10, 5 );