<?php
/**
 * GamiPress 1.4.3 compatibility functions
 *
 * @package     GamiPress\1.4.3
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.4.3
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Revoke an achievement from the user
 *
 * @deprecated use gamipress_revoke_achievement_to_user()
 *
 * @since  	1.0.0
 * @updated 1.2.8 Added $earning_id
 *
 * @see gamipress_revoke_achievement_from_user_old()
 *
 * @param  integer $achievement_id The given achievement's post ID
 * @param  integer $user_id        The given user's ID
 * @param  integer $earning_id     The user's earning ID
 *
 * @return void
 */
function gamipress_revoke_achievement_from_user( $achievement_id = 0, $user_id = 0, $earning_id = 0 ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {
        gamipress_revoke_achievement_from_user_old( $achievement_id, $user_id );
        return;
    }

    gamipress_revoke_achievement_to_user( $achievement_id, $user_id, $earning_id );

}