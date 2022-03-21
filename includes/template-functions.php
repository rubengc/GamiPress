<?php
/**
 * Template Functions
 *
 * @package     GamiPress\Template_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Render an achievement
 *
 * @since  1.0.0
 *
 * @param  integer  $achievement    The Achievement's ID
 * @param  array    $template_args  Template args
 *
 * @return string                   The Achievement's output
 */
function gamipress_render_achievement( $achievement = 0, $template_args = array() ) {

    global $post, $gamipress_template_args;

    // Initialize GamiPress template args global
    $gamipress_template_args = wp_parse_args( $template_args, gamipress_achievement_shortcode_defaults() );

    // If we were given an ID, get the post
    if ( is_numeric( $achievement ) ) {
        $post = gamipress_get_post( $achievement );
    } else {
        $post = $achievement;
    }

    setup_postdata( $post );

    // Enqueue assets
    if( ! (bool) gamipress_get_option( 'disable_css', false ) )
        wp_enqueue_style( 'gamipress-css' );

    if( ! (bool) gamipress_get_option( 'disable_js', false ) )
        wp_enqueue_script( 'gamipress-js' );

    // Try to load achievement-{post_type}.php, if not exists then load achievement.php
    ob_start();
    gamipress_get_template_part( 'achievement', $post->post_type );
    $output = ob_get_clean();

    $output = apply_filters( 'gamipress_render_achievement', $output, $post->ID, $post );

    wp_reset_postdata();

    // Return our filterable markup
    return $output;

}

/**
 * Render a rank
 *
 * @since  1.0.0
 *
 * @param  integer  $rank           The Rank's ID
 * @param  array    $template_args  Template args
 *
 * @return string                   The Achievement's output
 */
function gamipress_render_rank( $rank = 0, $template_args = array() ) {

    global $post, $gamipress_template_args;

    // Initialize GamiPress template args global
    $gamipress_template_args = wp_parse_args( $template_args, gamipress_rank_shortcode_defaults() );

    // If we were given an ID, get the post
    if ( is_numeric( $rank ) ) {
        $post = gamipress_get_post( $rank );
    } else {
        $post = $rank;
    }

    setup_postdata( $post );

    // Enqueue assets
    if( ! (bool) gamipress_get_option( 'disable_css', false ) ) {
        wp_enqueue_style( 'gamipress-css' );
    }

    if( ! (bool) gamipress_get_option( 'disable_js', false ) ) {
        wp_enqueue_script( 'gamipress-js' );
    }

    // Try to load rank-{type}.php, if not exists then load rank.php
    ob_start();
    gamipress_get_template_part( 'rank', $post->post_type );
    $output = ob_get_clean();

    $output = apply_filters( 'gamipress_render_rank', $output, $post->ID, $post );

    wp_reset_postdata();

    // Return our filterable markup
    return $output;

}

/**
 * Retrieves a template part
 *
 * Taken from Easy Digital Downloads
 *
 * @since 1.0.0
 *
 * @param string $slug
 * @param string $name Optional. Default null
 * @param bool   $load
 *
 * @return string
 */
function gamipress_get_template_part( $slug, $name = null, $load = true ) {

    // Execute code for this part
    do_action( 'get_template_part_' . $slug, $slug, $name );

    $load_template = apply_filters( 'gamipress_allow_template_part_' . $slug . '_' . $name, true );
    if ( false === $load_template ) {
        return '';
    }

    // Setup possible parts
    $templates = array();
    if ( isset( $name ) )
        $templates[] = $slug . '-' . $name . '.php';
    $templates[] = $slug . '.php';

    // Allow template parts to be filtered
    $templates = apply_filters( 'gamipress_get_template_part', $templates, $slug, $name );

    // Return the part that is found
    return gamipress_locate_template( $templates, $load, false );

}

/**
 * Retrieve the name of the highest priority template file that exists.
 *
 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that themes which
 * inherit from a parent theme can just overload one file. If the template is
 * not found in either of those, it looks in the theme-compat folder last.
 *
 * Taken from Easy Digital Downloads
 *
 * @since 1.0.0
 *
 * @param string|array  $template_names Template file(s) to search for, in order.
 * @param bool          $load           If true the template file will be loaded if it is found.
 * @param bool          $require_once   Whether to require_once or require. Default true. Has no effect if $load is false.
 *
 * @return string The template filename if one is located.
 */
function gamipress_locate_template( $template_names, $load = false, $require_once = true ) {

    // No file found yet
    $located = false;

    // Try to find a template file
    foreach ( (array) $template_names as $template_name ) {

        // Continue if template is empty
        if ( empty( $template_name ) )
            continue;

        // Trim off any slashes from the template name
        $template_name = ltrim( $template_name, '/' );

        // try locating this template file by looping through the template paths
        foreach( gamipress_get_theme_template_paths() as $template_path ) {

            if( file_exists( $template_path . $template_name ) ) {
                $located = $template_path . $template_name;
                break;
            }
        }

        if( $located ) {
            break;
        }
    }

    if ( ( true == $load ) && ! empty( $located ) )
        load_template( $located, $require_once );

    return $located;

}

/**
 * Returns a list of paths to check for template locations
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_get_theme_template_paths() {

    $file_paths = array();

    // Lets add-ons register template paths
    $file_paths = apply_filters( 'gamipress_template_paths', $file_paths );

    // Prepend theme template paths
    array_unshift(
        $file_paths,
        trailingslashit( get_stylesheet_directory() ) . 'gamipress/',
        trailingslashit( get_template_directory() ) . 'gamipress/'
    );

    // GamiPress template path in last position
    $file_paths[] =  GAMIPRESS_DIR . 'templates/';

    return array_map( 'trailingslashit', $file_paths );

}

/**
 * Helper function to parse inline shortcode outputs
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_parse_inline_output( $output ) {

    // Remove line breaks
    $output = str_replace( "\r", "", $output );
    $output = str_replace( "\n", "", $output );

    // Remove tabs
    $output = str_replace( "\t", "", $output );

    // Remove double spaces
    $output = str_replace( "  ", "", $output );

    // Remove spaces between tags
    $output = str_replace( "> <", "><", $output );


    return $output;
}