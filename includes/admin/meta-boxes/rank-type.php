<?php
/**
 * Rank Type Meta Boxes
 *
 * @package     GamiPress\Admin\Meta_Boxes\Rank_Type
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.4.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register rank type meta boxes
 *
 * @since 1.0.0
 */
function gamipress_rank_type_meta_boxes() {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_';

    // Rank Type Data
    gamipress_add_meta_box(
        'rank-type-data',
        __( 'Rank Type Data', 'gamipress' ),
        'rank-type',
        array(
            'post_title' => array(
                'name' 	=> __( 'Singular Name', 'gamipress' ),
                'desc' 	=> __( 'The singular name for this rank type.', 'gamipress' ),
                'type' 	=> 'text_medium',
            ),
            $prefix . 'plural_name' => array(
                'name' 	=> __( 'Plural Name', 'gamipress' ),
                'desc' 	=> __( 'The plural name for this rank type.', 'gamipress' ),
                'type' 	=> 'text_medium',
            ),
            'post_name' => array(
                'name' 	=> __( 'Slug', 'gamipress' ),
                'desc' 	=> '<span class="gamipress-permalink hide-if-no-js">' . site_url() . '/<strong class="gamipress-post-name"></strong>/</span><br>' . __( 'Slug is used for internal references, as some shortcode attributes, to completely differentiate this rank type from any other (leave blank to automatically generate one).', 'gamipress' ),
                'type' 	=> 'text_medium',
                'attributes' => array(
                    'maxlength' => 20
                )
            ),
        ),
        array( 'priority' => 'high', )
    );

}
add_action( 'gamipress_init_rank-type_meta_boxes', 'gamipress_rank_type_meta_boxes' );