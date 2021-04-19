<?php
/**
 * Ranks Meta Boxes
 *
 * @package     GamiPress\Admin\Meta_Boxes\Ranks
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.4.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register ranks meta boxes
 *
 * @param string $post_type
 *
 * @since 1.3.1
 */
function gamipress_ranks_meta_boxes( $post_type ) {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_';

    // Grab our rank types slugs
    $rank_types = gamipress_get_rank_types_slugs();

    if( ! in_array( $post_type, $rank_types ) ) {
        return;
    }

    // Rank Data
    gamipress_add_meta_box(
        'rank-data',
        __( 'Rank Data', 'gamipress' ),
        $rank_types,
        array(
            $prefix . 'congratulations_text' => array(
                'name' => __( 'Congratulations Text', 'gamipress' ),
                'desc' => __( 'Displayed after rank is reached.', 'gamipress' ),
                'type' => 'wysiwyg',
            ),
            $prefix . 'unlock_with_points' => array(
                'name' => __( 'Allow reach with points', 'gamipress' ),
                'desc' => __( 'Check this option to allow users to reach this rank by expend an amount of points.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
            $prefix . 'points_to_unlock' => array(
                'name' => __( 'Points to Unlock', 'gamipress' ),
                'desc' => __( 'Amount of points needed to optionally reach this rank by expending them.', 'gamipress' ),
                'type' => 'gamipress_points',
                'points_type_key' => $prefix . 'points_type_to_unlock',
                'default' => '0',
            ),
        ),
        array(
            'context'  => 'advanced',
            'priority' => 'high',
        )
    );

    // Rank Template
    gamipress_add_meta_box(
        'rank-template',
        __( 'Rank Template', 'gamipress' ),
        $rank_types,
        array(
            $prefix . 'show_earners' => array(
                'name' => __( 'Show Earners', 'gamipress' ),
                'desc' => __( 'Check this option to display a list of users who have reached this rank.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
            $prefix . 'maximum_earners' => array(
                'name' => __( 'Maximum Earners', 'gamipress' ),
                'desc' => __( 'Set the maximum number of earners to show (0 for no maximum).', 'gamipress' ),
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                    'step' => '1',
                ),
                'default' => '0'
            ),
            $prefix . 'layout' => array(
                'name'        => __( 'Layout', 'gamipress' ),
                'description' => __( 'Layout to show the rank.', 'gamipress' ),
                'type' 		  => 'radio',
                'options' 	  => gamipress_get_layout_options(),
                'default' 	  => 'left',
                'inline' 	  => true,
                'classes' 	  => 'gamipress-image-options'
            ),
            $prefix . 'align' => array(
                'name'        => __( 'Alignment', 'gamipress' ),
                'description' => __( 'Alignment to show the rank.', 'gamipress' ),
                'type' 		  => 'radio',
                'options' 	  => gamipress_get_alignment_options(),
                'default' 	  => 'none',
                'inline' 	  => true,
                'classes' 	  => 'gamipress-image-options'
            ),
        ),
        array(
            'context'  => 'side',
        )
    );

    // Rank Details
    gamipress_add_meta_box(
        'rank-details',
        __( 'Rank Details', 'gamipress' ),
        $rank_types,
        array(
            'menu_order' => array(
                'name' 	=> __( 'Priority', 'gamipress' ),
                'desc' 	=> __( 'The rank priority defines the order a user can achieve ranks. User will need to get lower priority ranks before get this one.', 'gamipress' ),
                'type' 	=> 'text_medium',
            ),
            $prefix . 'next_rank' => array(
                'content_cb' 	=> 'gamipress_next_rank_content_cb',
                'type' 	=> 'html',
                'classes' => 'gamipress-no-pad'
            ),
            $prefix . 'prev_rank' => array(
                'content_cb' 	=> 'gamipress_prev_rank_content_cb',
                'type' 	=> 'html',
                'classes' => 'gamipress-no-pad'
            ),
        ),
        array(
            'context' => 'side',
            'priority' => 'default',
        )
    );

}
add_action( 'gamipress_init_meta_boxes', 'gamipress_ranks_meta_boxes' );