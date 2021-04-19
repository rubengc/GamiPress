<?php
/**
 * Achievements Meta Boxes
 *
 * @package     GamiPress\Admin\Meta_Boxes\Achievements
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.4.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register achievements meta boxes
 *
 * @param string $post_type
 *
 * @since 1.0.0
 */
function gamipress_achievements_meta_boxes( $post_type ) {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_';

    // Grab our achievement types slugs
    $achievement_types = gamipress_get_achievement_types_slugs();

    if( ! in_array( $post_type, $achievement_types ) ) {
        return;
    }

    // Build a rank options (for rank type required field)
    $rank_types_options = array();

    foreach( gamipress_get_rank_types() as $slug => $data ) {
        $rank_types_options[$slug] = $data['singular_name'];
    }

    // Achievement Data
    gamipress_add_meta_box(
        'achievement-data',
        __( 'Achievement Data', 'gamipress' ),
        $achievement_types,
        array(
            $prefix . 'points' => array(
                'name' => __( 'Points Awarded', 'gamipress' ),
                'desc' => __( 'Points awarded for earning this achievement (optional). Leave empty if no points are awarded.', 'gamipress' ),
                'type' => 'gamipress_points',
                'default' => '0',
            ),
            $prefix . 'earned_by' => array(
                'name'    => __( 'Earned By:', 'gamipress' ),
                'desc'    => __( 'How this achievement can be earned.', 'gamipress' ),
                'type'    => 'select',
                'options' => apply_filters( 'gamipress_achievement_earned_by', array(
                    'triggers' 			=> __( 'Completing Steps', 'gamipress' ),
                    'points' 			=> __( 'Minimum Number of Points', 'gamipress' ),
                    'rank' 				=> __( 'Reach a Rank', 'gamipress' ),
                    'admin' 			=> __( 'Admin-awarded Only', 'gamipress' ),
                ) )
            ),
            $prefix . 'points_required' => array(
                'name' => __( 'Minimum Points Required', 'gamipress' ),
                'desc' => __( 'Fewest number of points required for earning this achievement.', 'gamipress' ),
                'type' => 'gamipress_points',
                'points_type_key' => $prefix . 'points_type_required',
                'default' => '0',
            ),
            $prefix . 'rank_type_required' => array(
                'name' => __( 'Rank Type Required', 'gamipress' ),
                'desc' => __( 'Rank Type of the required rank for earning this achievement.', 'gamipress' ),
                'type' => 'select',
                'options' => $rank_types_options
            ),
            $prefix . 'rank_required' => array(
                'name' => __( 'Rank Required', 'gamipress' ),
                'desc' => __( 'Rank required for earning this achievement.', 'gamipress' ),
                'type' => 'select',
                'options_cb' => 'gamipress_options_cb_posts'
            ),
            $prefix . 'congratulations_text' => array(
                'name' => __( 'Congratulations Text', 'gamipress' ),
                'desc' => __( 'Displayed after achievement is earned.', 'gamipress' ),
                'type' => 'wysiwyg',
                'options' => array(
                    'textarea_rows' => 8,
                ),
            ),
            $prefix . 'maximum_earnings' => array(
                'name' => __( 'Maximum Earnings Per User', 'gamipress' ),
                'desc' => __( 'Number of times a user can earn this achievement (set it to 0 for no maximum).', 'gamipress' ),
                'type' => 'text_small',
                'attributes' => array(
                    'type' => 'number'
                ),
                'default' => '1',
            ),
            $prefix . 'global_maximum_earnings' => array(
                'name' => __( 'Global Maximum Earnings', 'gamipress' ),
                'desc' => __( 'Number of times this achievement can be earned globally (set it to 0 for no maximum).', 'gamipress' )
                . '<br>' . '<strong>Note:</strong> This limit decides how many times this achievement can be earned on your site. Setting it to 10, for example, will limit this achievement to only the first 10 users who achieve it.',
                'type' => 'text_small',
                'attributes' => array(
                    'type' => 'number'
                ),
                'default' => '0',
            ),
            $prefix . 'hidden' => array(
                'name'    => __( 'Hidden?', 'gamipress' ),
                'type'    => 'select',
                'options' => array(
                    'show' 		=> __( 'Show to User', 'gamipress' ),
                    'hidden' 	=> __( 'Hidden to User', 'gamipress' ),
                ),
            ),
            $prefix . 'unlock_with_points' => array(
                'name' => __( 'Allow unlock with points', 'gamipress' ),
                'desc' => __( 'Check this option to allow users to unlock this achievement by expend an amount of points.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
            $prefix . 'points_to_unlock' => array(
                'name' => __( 'Points to Unlock', 'gamipress' ),
                'desc' => __( 'Amount of points needed to optionally unlock this achievement by expending them.', 'gamipress' ),
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

    // Achievement Template
    gamipress_add_meta_box(
        'achievement-template',
        __( 'Achievement Template', 'gamipress' ),
        $achievement_types,
        array(
            $prefix . 'show_times_earned' => array(
                'name' => __( 'Show Times Earned', 'gamipress' ),
                'desc' => __( 'Check this option to display the times the user has earned this achievement (only if the achievement can be earned more that 1 time).', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
            $prefix . 'show_global_times_earned' => array(
                'name' => __( 'Show Times Earned By All Users', 'gamipress' ),
                'desc' => __( 'Check this option to display the times that all users have earned this achievement.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
            $prefix . 'show_earners' => array(
                'name' => __( 'Show Earners', 'gamipress' ),
                'desc' => __( 'Check this option to display a list of users who have earned this achievement.', 'gamipress' ),
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
                'description' => __( 'Layout to show the achievement.', 'gamipress' ),
                'type' 		  => 'radio',
                'options' 	  => gamipress_get_layout_options(),
                'default' 	  => 'left',
                'inline' 	  => true,
                'classes' 	  => 'gamipress-image-options'
            ),
            $prefix . 'align' => array(
                'name'        => __( 'Alignment', 'gamipress' ),
                'description' => __( 'Alignment to show the achievement.', 'gamipress' ),
                'type' 		  => 'radio',
                'options' 	  => gamipress_get_alignment_options(),
                'default' 	  => 'none',
                'inline' 	  => true,
                'classes' 	  => 'gamipress-image-options'
            ),
        ),
        array( 'context'  => 'side', )
    );

}
add_action( 'gamipress_init_meta_boxes', 'gamipress_achievements_meta_boxes' );