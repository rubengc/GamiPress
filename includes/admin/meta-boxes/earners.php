<?php
/**
 * Earners Meta Boxes
 *
 * @package     GamiPress\Admin\Meta_Boxes\Earners
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.8.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add earners meta boxes
 *
 * @since  1.8.7
 *
 * @return void
 */
function gamipress_add_earners_meta_box() {

    // Steps
    foreach ( gamipress_get_achievement_types_slugs() as $achievement_type ) {
        add_meta_box( 'gamipress-earners', __( 'Earners', 'gamipress' ), 'gamipress_earners_meta_box', $achievement_type, 'advanced', 'default' );
    }

    // Rank Requirements
    foreach ( gamipress_get_rank_types_slugs() as $rank_type ) {
        add_meta_box( 'gamipress-earners', __( 'Earners', 'gamipress' ), 'gamipress_earners_meta_box', $rank_type, 'advanced', 'default' );
    }

}
add_action( 'add_meta_boxes', 'gamipress_add_earners_meta_box' );

/**
 * Renders the HTML for meta box, refreshes whenever a new point award is added
 *
 * @since 1.0.5
 * @updated 1.3.7 Added $metabox
 *
 * @param WP_Post $post     The current post object.
 * @param array   $metabox  With metabox id, title, callback, and args elements.
 *
 * @return void
 */
function gamipress_earners_meta_box( $post = null, $metabox = array() ) {

    /**
     * Filter to allow set the number of user earnings to show on the earners meta box
     *
     * @since 1.8.7
     *
     * @param int       $items_per_page
     * @param WP_Post   $post     The current post object.
     * @param array     $metabox  With metabox id, title, callback, and args elements.
     *
     * @return int
     */
    $items_per_page = apply_filters( 'gamipress_earners_meta_box_items_per_page', 10, $post, $metabox );

    ct_render_ajax_list_table( 'gamipress_user_earnings',
        array(
            'post_id' => $post->ID,
            'items_per_page' => $items_per_page,
            'is_earners_box' => 1,
        ),
        array(
            'views' => false,
            'search_box' => false
        )
    );

}