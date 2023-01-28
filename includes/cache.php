<?php
/**
 * GamiPress Cache Class
 *
 * Used to store commonly used query results
 *
 * @package     GamiPress\Cache
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.4.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get a cached element.
 *
 * @since   1.4.7
 * @updated 1.6.1 Added support to return cached value stored in options table
 *
 * @param string    $key        Cache key
 * @param mixed     $default    Default value in case the cache is not found
 * @param bool      $stored     Whatever if the cache has been stored previously in the database or not
 *
 * @return mixed
 */
function gamipress_get_cache( $key = '', $default = null, $stored = true ) {

    if( isset( GamiPress()->cache[$key] ) ) {
        return GamiPress()->cache[$key];
    } else if( $stored ) {

        // If GamiPress is installed network wide, get cache from network options
        if( gamipress_is_network_wide_active() ) {
            $cached = get_site_option( 'gamipress_cache_' . $key );
        } else {
            $cached = get_option( 'gamipress_cache_' . $key );
        }

        // If has been cached on options, then return the cached value
        if( $cached !== false ) {

            GamiPress()->cache[$key] = $cached;

            return GamiPress()->cache[$key];

        }

    }

    return $default;

}

/**
 * Set a cached element.
 *
 * @since   1.4.7
 * @updated 1.6.1 Added $save parameter
 *
 * @param string    $key
 * @param mixed     $value
 * @param bool      $save
 *
 * @return bool
 */
function gamipress_set_cache( $key = '', $value = '', $save = false ) {

    // Just keep value on a floating cache
    // To make it persistent pass $save as true or use gamipress_save_cache() function
    GamiPress()->cache[$key] = $value;

    if( $save === true ) {
        return gamipress_save_cache( $key, $value );
    }

    return true;

}

/**
 * Save a cached element.
 *
 * @since 1.6.1
 *
 * @param string    $key
 * @param mixed     $value
 *
 * @return bool
 */
function gamipress_save_cache( $key = '', $value = '' ) {

    // Allow to make value optional but just if element has been already cached
    if( empty( $value ) && isset( GamiPress()->cache[$key] ) ) {
        $value = GamiPress()->cache[$key];
    }

    // Update the floating cache
    GamiPress()->cache[$key] = $value;

    // If GamiPress is installed network wide, save cache on network options
    if( gamipress_is_network_wide_active() ) {
        return update_site_option( 'gamipress_cache_' . $key, $value );
    } else {
        return update_option( 'gamipress_cache_' . $key, $value, false );
    }

}

/**
 * Delete a cached element.
 *
 * @since 1.6.1
 *
 * @param string    $key
 *
 * @return bool
 */
function gamipress_delete_cache( $key = '' ) {

    if( isset( GamiPress()->cache[$key] ) ) {
        unset( GamiPress()->cache[$key] );
    }

    // If GamiPress is installed network wide, delete cache on network options
    if( gamipress_is_network_wide_active() ) {
        return delete_site_option( 'gamipress_cache_' . $key );
    } else {
        return delete_option( 'gamipress_cache_' . $key );
    }

}

/**
 * Flush the entire GamiPress cache
 *
 * @since 1.9.9.2
 */
function gamipress_flush_cache() {

    global $wpdb;

    if( gamipress_is_network_wide_active() ) {
        // Multi site installs
        $wpdb->query( "DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE 'gamipress_cache_%'");
    } else {
        // Single site installs
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'gamipress_cache_%'" );
    }

}

/**
 * Preview achievement earned email action
 *
 * @since 1.3.0
 */
function gamipress_clear_cache_action() {

    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    // Clear the GamiPress cache
    gamipress_flush_cache();

    wp_redirect( admin_url( 'admin.php?page=gamipress&gamipress-message=cache_cleared' ) );
    exit;

}
add_action( 'gamipress_action_get_clear_cache', 'gamipress_clear_cache_action' );

/**
 * GamiPress admin notices
 *
 * @since 1.5.9
 */
function gamipress_clear_cache_notices() {

    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    if( ! isset( $_GET['gamipress-message'] ) ) {
        return;
    }

    if( $_GET['gamipress-message'] !== 'cache_cleared' ) {
        return;
    }

    ?>

    <div class="notice notice-success is-dismissible gamipress-notice">
        <p>
            <?php _e( '<strong>GamiPress cache</strong> cleared successfully!', 'gamipress' ); ?>
        </p>
    </div>

    <?php

}
add_action( 'admin_notices', 'gamipress_clear_cache_notices' );