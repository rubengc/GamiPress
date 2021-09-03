<?php
/**
 * Network
 *
 * @package     GamiPress\Network
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.4.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Initializes GamiPress on multisite installs
 *
 * @since 1.4.0
 */
function gamipress_init_multisite() {

    global $wpdb;

    if( is_multisite() ) {

        // Update GamiPress database if network wide active
        if( gamipress_is_network_wide_active() ) {

            // Setup WordPress database tables
            GamiPress()->db->posts                  = $wpdb->base_prefix . 'posts';
            GamiPress()->db->postmeta               = $wpdb->base_prefix . 'postmeta';
            GamiPress()->db->users                  = $wpdb->base_prefix . 'users';
            GamiPress()->db->usermeta               = $wpdb->base_prefix . 'usermeta';

            // Setup GamiPress database tables
            GamiPress()->db->logs 				    = $wpdb->base_prefix . 'gamipress_logs';
            GamiPress()->db->logs_meta 			    = $wpdb->base_prefix . 'gamipress_logs_meta';
            GamiPress()->db->user_earnings 		    = $wpdb->base_prefix . 'gamipress_user_earnings';
            GamiPress()->db->user_earnings_meta     = $wpdb->base_prefix . 'gamipress_user_earnings_meta';

        }

    }

}
add_action( 'gamipress_init', 'gamipress_init_multisite', 1 );

/**
 * Create array of blog ids in network
 *
 * @since  1.0.0
 *
 * @return array Array of blog_ids
 */
function gamipress_get_network_site_ids() {

    global $wpdb;

    if( is_multisite() ) {

        $blog_ids = $wpdb->get_results( "SELECT blog_id FROM " . $wpdb->base_prefix . "blogs" );

        foreach ($blog_ids as $key => $value ) {
            $sites[] = $value->blog_id;
        }

    } else {
        $sites[] = get_current_blog_id();
    }

    return $sites;

}

/**
 * Replace per site queries to root site when GamiPress is active network wide
 *
 * @since 1.4.0
 *
 * @param string    $request
 * @param WP_Query  $wp_query
 *
 * @return string
 */
function gamipress_network_wide_post_request( $request, $wp_query ) {

    global $wpdb;

    // If GamiPress is active network wide and we are not in main site, then filter all queries to our post types
    if(
        gamipress_is_network_wide_active()
        && ! is_main_site()
        && isset( $wp_query->query_vars['post_type'] )
    ) {

        $post_type = $wp_query->query_vars['post_type'];

        if( is_array( $post_type ) ) {
            $post_type = $post_type[0];
        }

        if(
            in_array( $post_type, array( 'points-type', 'achievement-type', 'rank-type' ) )
            || in_array( $post_type, gamipress_get_requirement_types_slugs() )
            || in_array( $post_type, gamipress_get_achievement_types_slugs() )
            || in_array( $post_type, gamipress_get_rank_types_slugs() )
        ) {

            // Replace {prefix}{site}posts to {prefix}posts
            $request = str_replace( $wpdb->posts, "{$wpdb->base_prefix}posts", $request );

            // Replace {prefix}{site}postmeta to {prefix}postmeta
            $request = str_replace( $wpdb->postmeta, "{$wpdb->base_prefix}postmeta", $request );

        }

    }

    return $request;

}
add_filter( 'posts_request', 'gamipress_network_wide_post_request', 10, 2 );

/**
 * Check if GamiPress is network wide active
 *
 * @since 1.4.0
 *
 * @return bool
 */
function gamipress_is_network_wide_active() {

    // Available filter to disable network data centralization
    if( apply_filters( 'gamipress_disable_network_data_centralization', false ) ) {
        GamiPress()->network_wide_active = false;
        return false;
    }

    if( GamiPress()->network_wide_active === null ) {

        if( ! is_multisite() ) {

            // Set to false if not is a multisite install
            GamiPress()->network_wide_active = false;

        } else {

            // Normally the is_plugin_active_for_network() function is only available in the admin area
            if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
                require_once ABSPATH . '/wp-admin/includes/plugin.php';
            }

            GamiPress()->network_wide_active = is_plugin_active_for_network( 'gamipress/gamipress.php' );
        }

    }

    return GamiPress()->network_wide_active;

}

/**
 * Check if a plugin is active on any site of network
 *
 * @since 1.4.8
 *
 * @param string $plugin Path to the main plugin file from plugins directory.
 *
 * @return bool
 */
function gamipress_is_plugin_active_on_network( $plugin ) {

    $active_sites = gamipress_get_plugin_active_sites( $plugin );

    return ! empty( $active_sites );

}

/**
 * Get all sites a plugin is active
 *
 * @since 1.4.8
 *
 * @param string $plugin Path to the main plugin file from plugins directory.
 *
 * @return array
 */
function gamipress_get_plugin_active_sites( $plugin ) {

    // Bail if not is a multisite install
    if( ! is_multisite() ) {
        return array( get_current_blog_id() );
    }

    // If plugin is active network wide, return all sites
    if( is_plugin_active_for_network( $plugin ) ) {
        return gamipress_get_network_site_ids();
    }

    $sites_active = array();

    // Store a copy of the original ID for later
    $blog_id = get_current_blog_id();

    // Loop through all sites
    foreach( gamipress_get_network_site_ids() as $site_blog_id ) {

        // If we're polling a different blog, switch to it
        if ( $blog_id != $site_blog_id ) {
            switch_to_blog( $site_blog_id );
        }

        if( is_plugin_active( $plugin ) ) {
            $sites_active[] = $site_blog_id;
        }

        // Restore the original blog
        if ( $blog_id != $site_blog_id && is_multisite() ) {
            restore_current_blog();
        }

    }

    // Restore the original blog so the sky doesn't fall
    if ( $blog_id != get_current_blog_id() && is_multisite() ) {
        restore_current_blog();
    }

    return $sites_active;

}

/**
 * On save settings, also update GamiPress site option to make it network wide accessible
 *
 * @since 1.4.0
 *
 * @param string    $override
 * @param mixed     $options
 * @param CMB2      $cmb
 *
 * @return mixed
 */
function gamipress_save_network_settings( $override, $options, $cmb ) {

    if( gamipress_is_network_wide_active() ) {
        // Set options as network option
        update_site_option( 'gamipress_settings', $options );
    }

    return $override;

}
add_action( 'cmb2_override_option_save_gamipress_settings', 'gamipress_save_network_settings', 10, 3 );

/**
 * Filter the post edit link to return edit link from main site
 *
 * @since 1.4.0
 *
 * @param string $link    The edit link.
 * @param int    $post_id Post ID.
 * @param string $context The link context. If set to 'display' then ampersands are encoded.
 *
 * @return string
 */
function gamipress_main_site_edit_post_link( $link, $post_id, $context ) {

    if( ! gamipress_is_network_wide_active() || is_main_site() ) {
        return $link;
    }

    // Try to get the post type of current site
    $post_type = get_post_type( $post_id );

    if( ! $post_type ) {
        // If this post not exists on current site, then get his post type from main site
        $post_type = gamipress_get_post_type( $post_id );
    }

    if(
        in_array( $post_type, array( 'points-type', 'achievement-type', 'rank-type' ) )
        || in_array( $post_type, gamipress_get_requirement_types_slugs() )
        || in_array( $post_type, gamipress_get_achievement_types_slugs() )
        || in_array( $post_type, gamipress_get_rank_types_slugs() )
    ) {

        $post_type_object = get_post_type_object( $post_type );

        if ( 'display' == $context )
            $action = '&amp;action=edit';
        else
            $action = '&action=edit';

        $link = get_admin_url( get_main_site_id(), sprintf( $post_type_object->_edit_link . $action, $post_id ) );
    }

    return $link;

}
add_filter( 'get_edit_post_link', 'gamipress_main_site_edit_post_link', 10, 3 );

/**
 * Override CMB2 can save function to avoid issues on multisite installs
 *
 * @since 1.4.9
 *
 * @param bool      $can_save
 * @param object    $cmb
 *
 * @return bool
 */
function gamipress_network_can_save_meta_boxes( $can_save, $cmb ) {

    global $post;

   if( is_multisite() && $post ) {

       // Custom can save check
       $can_save = (
           $cmb->prop( 'save_fields' )
           // check nonce
           && isset( $_POST[ $cmb->nonce() ] )
           && wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() )
           // check if autosave
           && ! ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
           // get the metabox types & compare it to this type
           && ( in_array( $post->post_type, $cmb->box_types() ) )
           // Don't do updates during a switch-to-blog instance.
           //&& ! ( is_multisite() && ms_is_switched() ) // Removed, ms_is_switched() always returns true
       );

   }

    return $can_save;

}
add_filter( 'cmb2_can_save', 'gamipress_network_can_save_meta_boxes', 10, 2 );

/**
 * Force to clean switched sites global to avoid issues with other plugins (since wp_restore_blog() doesn't cleans it)
 *
 * @since 1.5.7
 */
function gamipress_fix_network_wp_insert_post() {

    if ( is_multisite() && ms_is_switched() ) {
        // Force to clean switched sites global
        $GLOBALS['_wp_switched_stack'] = array();
    }

}
add_action( 'wp_insert_post', 'gamipress_fix_network_wp_insert_post', 9 );

/**
 * Switch to main site
 *
 * @since   1.6.3
 * @updated 2.1.0 Update the $gamipress_original_blog_id global var
 *
 * @return int The blog ID before switch to main site
 */
function gamipress_switch_to_main_site() {

    global $gamipress_original_blog_id;

    $gamipress_original_blog_id = get_current_blog_id();

    // Switch to main site if not already on main site
    if( ! is_main_site() ) {
        switch_to_blog( get_main_site_id() );
    }

    return $gamipress_original_blog_id;

}

/**
 * Switch to main site just if GamiPress is network wide active
 *
 * @since 1.6.3
 *
 * @return int The blog ID before switch to main site
 */
function gamipress_switch_to_main_site_if_network_wide_active() {

    // Switch to main site if GamiPress is network wide active
    if( gamipress_is_network_wide_active() ) {
        return gamipress_switch_to_main_site();
    }

    return get_current_blog_id();

}

/**
 * Get the original site ID even if GamiPress has switched to main site
 *
 * @since 2.1.0
 *
 * @return int The blog ID before switch to main site
 */
function gamipress_get_original_site_id() {

    global $gamipress_original_blog_id;

    return ( $gamipress_original_blog_id ? $gamipress_original_blog_id : get_current_blog_id() );

}