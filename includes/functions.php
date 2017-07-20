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

    flush_rewrite_rules();
}

/**
 * Utility to execute a shortcode passing an array of args
 *
 * @param string    $shortcode The shortcode to execute
 * @param array     $args      The args to pass to the shortcode
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