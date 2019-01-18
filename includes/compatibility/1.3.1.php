<?php
/**
 * GamiPress 1.3.1 compatibility functions
 *
 * @package     GamiPress\1.3.1
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.3.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Helper function tests that we're in the main loop
 *
 * @deprecated use gamipress_is_single_achievement()
 *
 * @since  1.0.0
 *
 * @param  bool|integer $id The page id
 *
 * @return boolean     A boolean determining if the function is in the main loop
 */
function gamipress_is_main_loop( $id = false ) {

    $slugs = gamipress_get_achievement_types_slugs();
    // only run our filters on the gamipress singular pages
    if ( is_admin() || empty( $slugs ) || ! is_singular( $slugs ) )
        return false;
    // w/o id, we're only checking template context
    if ( ! $id )
        return true;

    // Checks several variables to be sure we're in the main loop (and won't effect things like post pagination titles)
    return ( ( $GLOBALS['post']->ID == $id ) && in_the_loop() && empty( $GLOBALS['gamipress_reformat_content'] ) );

}

/**
 * Return a user's points
 *
 * @deprecated use gamipress_get_user_points()
 *
 * @since  1.0.0
 * @param  int   $user_id      The given user's ID
 * @return integer  $user_points  The user's current points
 */
function gamipress_get_users_points( $user_id = 0, $points_type = '' ) {
    return gamipress_get_user_points( $user_id, $points_type );
}

/**
 * Posts a log entry when a user earns points
 *
 * @deprecated use gamipress_update_user_points()
 *
 * @since  1.0.0
 *
 * @param  integer $user_id        The given user's ID
 * @param  integer $new_points     The new points the user is being awarded
 * @param  integer $admin_id       If being awarded by an admin, the admin's user ID
 * @param  integer $achievement_id The achievement that generated the points, if applicable
 * @param  string  $points_type    The points type
 *
 * @return integer                 The user's updated points total
 */
function gamipress_update_users_points( $user_id = 0, $new_points = 0, $admin_id = 0, $achievement_id = null, $points_type = '' ) {
    return gamipress_update_user_points( $user_id, $new_points, $admin_id, $achievement_id, $points_type );
}