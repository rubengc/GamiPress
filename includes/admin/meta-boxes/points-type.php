<?php
/**
 * Points Type Meta Boxes
 *
 * @package     GamiPress\Admin\Meta_Boxes\Points_Type
 * @since       1.4.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register points type meta boxes
 *
 * @since 1.0.0
 */
function gamipress_points_type_meta_boxes() {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_';

    // Check if points types are public
    $public_points_type = apply_filters( 'gamipress_public_points_type', false );

    // Points Type Data
    gamipress_add_meta_box(
        'points-type-data',
        __( 'Points Type Data', 'gamipress' ),
        'points-type',
        array(
            'post_title' => array(
                'name' 	=> __( 'Singular Name', 'gamipress' ),
                'desc' 	=> __( 'The singular name for this points type.', 'gamipress' ),
                'type' 	=> 'text_medium',
            ),
            $prefix . 'plural_name' => array(
                'name' 	=> __( 'Plural Name', 'gamipress' ),
                'desc' 	=> __( 'The plural name for this points type.', 'gamipress' ),
                'type' 	=> 'text_medium',
            ),
            'post_name' => array(
                'name' 	=> __( 'Slug', 'gamipress' ),
                'desc' 	=>  (( $public_points_type ) ? '<span class="gamipress-permalink hide-if-no-js">' . site_url() . '/<strong class="gamipress-post-name"></strong>/</span><br>' : '' ) . __( 'Slug is used for internal references, as some shortcode attributes, to completely differentiate this points type from any other (leave blank to automatically generate one).', 'gamipress' ),
                'type' 	=> 'text_medium',
                'attributes' => array(
                    'maxlength' => 20
                )
            ),
        ),
        array( 'priority' => 'high', )
    );

}
add_action( 'gamipress_init_points-type_meta_boxes', 'gamipress_points_type_meta_boxes' );