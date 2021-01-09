<?php
/**
 * GamiPress compatibility with newest WordPress functions
 *
 * @package     GamiPress\Compatibility\WordPress
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.4.9
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( ! function_exists( 'get_post_types_by_support' ) ) :

/**
 * Retrieves a list of post type names that support a specific feature.
 *
 * @since 4.5.0
 *
 * @global array $_wp_post_type_features Post type features
 *
 * @param array|string $feature  Single feature or an array of features the post types should support.
 * @param string       $operator Optional. The logical operation to perform. 'or' means
 *                               only one element from the array needs to match; 'and'
 *                               means all elements must match; 'not' means no elements may
 *                               match. Default 'and'.
 * @return array A list of post type names.
 */
function get_post_types_by_support( $feature, $operator = 'and' ) {
    global $_wp_post_type_features;

    $features = array_fill_keys( (array) $feature, true );

    return array_keys( wp_filter_object_list( $_wp_post_type_features, $features, $operator ) );
}

endif;

if( ! function_exists( 'do_blocks' ) ) :

    /**
     * Parses dynamic blocks out of `post_content` and re-renders them.
     *
     * @since 5.0.0
     *
     * @param string $content Post content.
     * @return string Updated post content.
     */
    function do_blocks( $content ) {
        return $content;
    }

endif;