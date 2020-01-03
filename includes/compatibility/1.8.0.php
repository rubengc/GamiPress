<?php
/**
 * GamiPress 1.8.0 compatibility functions
 *
 * @package     GamiPress\1.8.0
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.8.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Returns array of achievement types a user has earned across a multisite network
 *
 * @deprecated Deprecated since is an unused function
 *
 * @since  1.0.0
 * @param  integer $user_id  The user's ID
 * @return array             An array of post types
 */
function gamipress_get_network_achievement_types_for_user( $user_id ) {

    $blog_id = get_current_blog_id();

    // Assume we have no achievement types
    $all_achievement_types = array();

    // Loop through all active sites
    $sites = gamipress_get_network_site_ids();

    foreach( $sites as $site_blog_id ) {

        // If we're polling a different blog, switch to it
        if ( $blog_id != $site_blog_id ) {
            switch_to_blog( $site_blog_id );
        }

        // Merge earned achievements to our achievement type array
        $achievement_types = gamipress_get_user_earned_achievement_types( $user_id );

        if ( is_array( $achievement_types ) ) {
            $all_achievement_types = array_merge( $achievement_types, $all_achievement_types );
        }

        // If switched to blog, return back to que current blog
        if ( $blog_id != $site_blog_id && is_multisite() ) {
            restore_current_blog();
        }
    }

    // Restore the original blog so the sky doesn't fall
    if ( $blog_id != get_current_blog_id() && is_multisite() ) {
        restore_current_blog();
    }

    // Pare down achievement type list so we return no duplicates
    $achievement_types = array_unique( $all_achievement_types );

    // Return all found achievements
    return $achievement_types;

}