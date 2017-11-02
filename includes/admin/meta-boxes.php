<?php
/**
 * Admin Meta Boxes
 *
 * @package     GamiPress\Admin\Meta_Boxes
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Helper function to register custom meta boxes
 *
 * @since  1.0.8
 *
 * @param string 		$id
 * @param string 		$title
 * @param string|array 	$object_types
 * @param array 		$fields
 * @param array 		$args
 */
function gamipress_add_meta_box( $id, $title, $object_types, $fields, $args = array() ) {

	// ID for hooks
	$hook_id = str_replace( '-', '_', $id );

	// First, filter the fields to allow extend it
	$fields = apply_filters( "gamipress_{$hook_id}_fields", $fields );

	foreach( $fields as $field_id => $field ) {

		$fields[$field_id]['id'] = $field_id;

		// Support for group fields
		if( isset( $field['fields'] ) && is_array( $field['fields'] ) ) {

			foreach( $field['fields'] as $group_field_id => $group_field ) {

				$fields[$field_id]['fields'][$group_field_id]['id'] = $group_field_id;

			}

		}

	}

	$args = wp_parse_args( $args, array(
		'vertical_tabs' => false,
		'tabs'      	=> array(),
		'context'      	=> 'normal',
		'priority'     	=> 'default',
	) );

	// Parse tabs
	foreach( $args['tabs'] as $tab_id => $tab ) {

		$args['tabs'][$tab_id]['id'] = $tab_id;

	}

	new_cmb2_box( array(
		'id'           	=> $id,
		'title'        	=> $title,
		'object_types' 	=> ! is_array( $object_types) ? array( $object_types ) : $object_types,
		'tabs'      	=> $args['tabs'],
		'vertical_tabs' => $args['vertical_tabs'],
		'context'      	=> $args['context'],
		'priority'     	=> $args['priority'],
		'classes'		=> 'gamipress-form gamipress-box-form',
		'fields' 		=> $fields
	) );
}

/**
 * Register custom meta boxes used throughout GamiPress
 *
 * @since  1.0.0
 */
function gamipress_meta_boxes() {
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_gamipress_';

    // Grab our points types as an array
    $points_types_options = array(
        '' => 'Default'
    );

    foreach( gamipress_get_points_types() as $slug => $data ) {
        $points_types_options[$slug] = $data['plural_name'];
    }

	// Grab our achievement types as an array
	$achievement_types = gamipress_get_achievement_types_slugs();

	// Grab our requirement types as an array
	$requirement_types = gamipress_get_requirement_types_slugs();

	// Check if points awards are public
	$public_points_awards = apply_filters( 'gamipress_public_points_awards', false );

	// Points Type
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
				'desc' 	=>  (( $public_points_awards ) ? '<span class="gamipress-permalink hide-if-no-js">' . site_url() . '/<strong class="gamipress-post-name"></strong>/</span><br>' : '' ) . __( 'Slug is used for internal references, as some shortcode attributes, to completely differentiate this points type from any other (leave blank to automatically generate one).', 'gamipress' ),
				'type' 	=> 'text_medium',
				'attributes' => array(
					'maxlength' => 20
				)
			),
		),
		array( 'priority' => 'high', )
	);

	// Achievement Type
	gamipress_add_meta_box(
		'achievement-type-data',
		__( 'Achievement Type Data', 'gamipress' ),
		'achievement-type',
		array(
			'post_title' => array(
				'name' 	=> __( 'Singular Name', 'gamipress' ),
				'desc' 	=> __( 'The singular name for this achievement type.', 'gamipress' ),
				'type' 	=> 'text_medium',
			),
			$prefix . 'plural_name' => array(
				'name' 	=> __( 'Plural Name', 'gamipress' ),
				'desc' 	=> __( 'The plural name for this achievement type.', 'gamipress' ),
				'type' 	=> 'text_medium',
			),
			'post_name' => array(
				'name' 	=> __( 'Slug', 'gamipress' ),
				'desc' 	=> '<span class="gamipress-permalink hide-if-no-js">' . site_url() . '/<strong class="gamipress-post-name"></strong>/</span><br>' . __( 'Slug is used for internal references, as some shortcode attributes, to completely differentiate this achievement type from any other (leave blank to automatically generate one).', 'gamipress' ),
				'type' 	=> 'text_medium',
				'attributes' => array(
					'maxlength' => 20
				)
			),
		),
		array( 'priority' => 'high', )
	);

	// Achievements
	gamipress_add_meta_box(
		'achievement-data',
		__( 'Achievement Data', 'gamipress' ),
		array_diff( $achievement_types, array( 'step', 'points-award' ) ),
		array(
			$prefix . 'points' => array(
				'name' => __( 'Points Awarded', 'gamipress' ),
				'desc' => __( 'Points awarded for earning this achievement (optional). Leave empty if no points are awarded.', 'gamipress' ),
				'type' => 'text_small',
			),
			$prefix . 'points_type' => array(
				'name' => __( 'Points Type', 'gamipress' ),
				'desc' => __( 'Points type to award for earning this achievement (optional).', 'gamipress' ),
				'type' => 'select',
				'options' => $points_types_options
			),
			$prefix . 'earned_by' => array(
				'name'    => __( 'Earned By:', 'gamipress' ),
				'desc'    => __( 'How this achievement can be earned.', 'gamipress' ),
				'type'    => 'select',
				'options' => apply_filters( 'gamipress_achievement_earned_by', array(
					'triggers' 			=> __( 'Completing Steps', 'gamipress' ),
					'points' 			=> __( 'Minimum Number of Points', 'gamipress' ),
					'admin' 			=> __( 'Admin-awarded Only', 'gamipress' ),
				) )
			),
			$prefix . 'points_required' => array(
				'name' => __( 'Minimum Points Required', 'gamipress' ),
				'desc' => __( 'Fewest number of points required for earning this achievement.', 'gamipress' ),
				'type' => 'text_small',
			),
			$prefix . 'points_type_required' => array(
				'name' => __( 'Points Type Required', 'gamipress' ),
				'desc' => __( 'Points type of points required for earning this achievement (optional).', 'gamipress' ),
				'type' => 'select',
				'options' => $points_types_options
			),
			$prefix . 'sequential' => array(
				'name' => __( 'Sequential Steps', 'gamipress' ),
				'desc' => __( 'Yes, steps must be completed in order.', 'gamipress' ),
				'type' => 'checkbox',
				'classes' => 'gamipress-switch'
			),
			$prefix . 'show_earners' => array(
				'name' => __( 'Show Earners', 'gamipress' ),
				'desc' => __( 'Yes, display a list of users who have earned this achievement.', 'gamipress' ),
				'type' => 'checkbox',
				'classes' => 'gamipress-switch'
			),
			$prefix . 'congratulations_text' => array(
				'name' => __( 'Congratulations Text', 'gamipress' ),
				'desc' => __( 'Displayed after achievement is earned.', 'gamipress' ),
				'type' => 'textarea',
			),
			$prefix . 'maximum_earnings' => array(
				'name' => __( 'Maximum Earnings', 'gamipress' ),
				'desc' => __( 'Number of times a user can earn this achievement (leave empty for no maximum).', 'gamipress' ),
				'type' => 'text_small',
				'default' => '1',
			),
			$prefix . 'hidden' => array(
				'name'    => __( 'Hidden?', 'gamipress' ),
				'type'    => 'select',
				'options' => array(
					'show' 		=> __( 'Show to User', 'gamipress' ),
					'hidden' 	=> __( 'Hidden to User', 'gamipress' ),
				),
			),
		),
		array(
			'context'  => 'advanced',
			'priority' => 'high',
		)
	);

	// Requirements
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
				'type' => 'text_small',
			),
			$prefix . 'points_type' => array(
				'name' => __( 'Points Type', 'gamipress' ),
				'desc' => '',
				'type' => 'select',
				'options' => $points_types_options
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
add_action( 'cmb2_admin_init', 'gamipress_meta_boxes' );

function gamipress_remove_meta_boxes() {

	remove_meta_box( 'slugdiv', 'points-type', 'normal' );
	remove_meta_box( 'slugdiv', 'achievement-type', 'normal' );

}
add_action( 'admin_menu', 'gamipress_remove_meta_boxes' );

/**
 * Helper function to enable a checkbox when value was not stored
 */
function gamipress_cmb2_checkbox_enabled_by_default( $field_args, $field ) {

	if( get_post_field( 'post_status', $field->object_id ) === 'auto-draft' ) {
		return '1';
	}

	return '';

}

/**
 * Override the title/content field retrieval so CMB2 doesn't look in post-meta.
 */
function gamipress_cmb2_override_post_title_display( $data, $post_id ) {

	if( get_post_field( 'post_status', $post_id ) === 'auto-draft' ) {
		return '';
	}

	return get_post_field( 'post_title', $post_id );

}
add_filter( 'cmb2_override_post_title_meta_value', 'gamipress_cmb2_override_post_title_display', 10, 2 );

function gamipress_cmb2_override_post_name_display( $data, $post_id ) {

	return get_post_field( 'post_name', $post_id );

}
add_filter( 'cmb2_override_post_name_meta_value', 'gamipress_cmb2_override_post_name_display', 10, 2 );

/*
 * WP will handle the saving for us, so don't save title/content to meta.
 */
add_filter( 'cmb2_override_post_title_meta_save', '__return_true' );
add_filter( 'cmb2_override_post_name_meta_save', '__return_true' );
