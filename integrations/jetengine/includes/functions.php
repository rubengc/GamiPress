<?php
/**
 * Functions
 *
 * @package     GamiPress\JetEngine\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Check JetEngine post type
 *
 * @since 1.0.0
 * 
 * @param object $post  Post data
 *
 * @return bool
 */
function gamipress_jetengine_check_type( $post ) {

    $array_types = array();

    // Get JetEngine post types
    $post_types_obj = new Jet_Engine_CPT;
    $post_types = $post_types_obj->get_items();

    foreach( $post_types as $post_type ) {
        $array_types[] = $post_type['slug'];
    }

    // Bail if the post is not a JetEngine type
    if ( !in_array( $post->post_type, $array_types ) ){
        return false;
    }

    return true;
}