<?php
/**
 * Functions
 *
 * @package GamiPress\Groundhogg\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_groundhogg_ajax_get_posts() {

    global $wpdb;

    if( isset( $_REQUEST['post_type'] ) && in_array( 'groundhogg_tags', $_REQUEST['post_type'] ) ) {

        $results = array();

        // Pull back the search string
        $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

        // Get the tags
        $tags = Groundhogg\Plugin::$instance->dbs->get_db( 'tags' )->search( $search );

        foreach ( $tags as $tag ) {

            // Results should meet same structure like posts
            $results[] = array(
                'ID' => $tag->tag_id,
                'post_title' => $tag->tag_name,
            );

        }

        // Return our results
        wp_send_json_success( $results );
        die;

    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_groundhogg_ajax_get_posts', 5 );

// Get the tag title
function gamipress_groundhogg_get_tag_title( $tag_id ) {

    if( absint( $tag_id ) === 0 ) {
        return '';
    }

    $tag = Groundhogg\Plugin::$instance->dbs->get_db( 'tags' )->get( $tag_id );

    return ( $tag ? $tag->tag_name : '' );

}