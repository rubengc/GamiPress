<?php
/**
 * Requirements Meta Boxes
 *
 * @package     GamiPress\Admin\Meta_Boxes\Requirements
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.4.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register requirements meta boxes
 *
 * @param string $post_type
 *
 * @since 1.0.0
 */
function gamipress_requirements_meta_boxes( $post_type ) {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_';

    // Grab our requirement types slugs
    $requirement_types = gamipress_get_requirement_types_slugs();

    if( ! in_array( $post_type, $requirement_types ) ) {
        return;
    }

    // Requirement Data
    gamipress_add_meta_box(
        'requirement-data',
        __( 'Requirement Data', 'gamipress' ),
        $requirement_types,
        array(
            $prefix . 'trigger_type' => array(
                'name' => __( 'Trigger Type', 'gamipress' ),
                'desc' => '',
                'type' => 'advanced_select',
                'options' => gamipress_get_activity_triggers()
            ),
            $prefix . 'count' => array(
                'name' => __( 'Count', 'gamipress' ),
                'desc' => '',
                'type' => 'text_small',
            ),
            $prefix . 'limit' => array(
                'name' => __( 'Limit', 'gamipress' ),
                'desc' => '',
                'type' => 'text_small',
            ),
            $prefix . 'limit_type' => array(
                'name' => __( 'Limit Type', 'gamipress' ),
                'desc' => '',
                'type' => 'select',
                'options' => array(
                    'unlimited' => __( 'Unlimited', 'gamipress' ),
                    'daily'     => __( 'Per Day', 'gamipress' ),
                    'weekly'    => __( 'Per Week', 'gamipress' ),
                    'monthly'   => __( 'Per Month', 'gamipress' ),
                    'yearly'    => __( 'Per Year', 'gamipress' ),
                )
            ),
            $prefix . 'achievement_type' => array(
                'name' => __( 'Achievement Type', 'gamipress' ),
                'desc' => '',
                'type' => 'text',
            ),
            $prefix . 'achievement_post' => array(
                'name' => __( 'Achievement Post', 'gamipress' ),
                'desc' => '',
                'type' => 'text_small',
            ),
            $prefix . 'points' => array(
                'name' => __( 'Points', 'gamipress' ),
                'desc' => '',
                'type' => 'gamipress_points',
            ),
            $prefix . 'maximum_earnings' => array(
                'name' => __( 'Maximum Earnings', 'gamipress' ),
                'desc' => '',
                'type' => 'text_small',
            ),
        ),
        array( 'priority' => 'high' )
    );

}
add_action( 'gamipress_init_meta_boxes', 'gamipress_requirements_meta_boxes' );