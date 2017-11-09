<?php
/**
 * Functions
 *
 * @package     GamiPress\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register GamiPress types and flush rewrite rules.
 *
 * @since 1.0.0
 */
function gamipress_flush_rewrite_rules() {

    gamipress_register_post_types();
    gamipress_register_points_types();
    gamipress_register_achievement_types();
    gamipress_register_rank_types();

    flush_rewrite_rules();

}

/**
 * Utility to execute a shortcode passing an array of args
 *
 * @param string    $shortcode The shortcode to execute
 * @param array     $args      The args to pass to the shortcode
 *
 * @return string   $output    Output from the shortcode execution with the given args
 */
function gamipress_do_shortcode( $shortcode, $args ) {
    $shortcode_args = '';

    foreach( $args as $arg => $value ) {
        if( is_array( $value ) ) {
            $value = str_replace( '"', '\'', json_encode( $value ) );
            $value = str_replace( '[', '{', $value );
            $value = str_replace( ']', '}', $value );
        }

        $shortcode_args .= sprintf( ' %s="%s"', $arg, $value);
    }

    return do_shortcode( sprintf( '[%s %s]', $shortcode, $shortcode_args ) );
}

/**
 * Create array of blog ids in the network if multisite setting is on
 *
 * @since  1.0.0
 *
 * @return array Array of blog_ids
 */
function gamipress_get_network_site_ids() {

    global $wpdb;

    if( is_multisite() && (bool) gamipress_get_option( 'ms_show_all_achievements', false ) ) {
        $blog_ids = $wpdb->get_results( "SELECT blog_id FROM " . $wpdb->base_prefix . "blogs" );
        foreach ($blog_ids as $key => $value ) {
            $sites[] = $value->blog_id;
        }
    } else {
        $sites[] = get_current_blog_id();
    }

    return $sites;

}