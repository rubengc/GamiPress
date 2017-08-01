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

	// Points Type
	new_cmb2_box(array(
		'id'           	=> 'points-type-data',
		'title'        	=> __( 'Points Type Data', 'gamipress' ),
		'object_types' 	=> array( 'points-type' ),
		'context'      	=> 'normal',
		'priority'     	=> 'high',
		'fields' 		=> apply_filters( 'gamipress_points_type_data_meta_box_fields', array(
			array(
				'name' 	=> __( 'Singular Name', 'gamipress' ),
				'desc' 	=> __( 'The singular name for this points type.', 'gamipress' ),
				'id'   	=> $prefix . 'singular_name',
				'type' 	=> 'text_medium',
			),
			array(
				'name' 	=> __( 'Plural Name', 'gamipress' ),
				'desc' 	=> __( 'The plural name for this points type.', 'gamipress' ),
				'id'   	=> $prefix . 'plural_name',
				'type' 	=> 'text_medium',
			),
		), $prefix )
	));

	// Achievement Type
	new_cmb2_box(array(
		'id'           	=> 'achievement-type-data',
		'title'        	=> __( 'Achievement Type Data', 'gamipress' ),
		'object_types' 	=> array( 'achievement-type' ),
		'context'      	=> 'normal',
		'priority'     	=> 'high',
		'fields' 		=> apply_filters( 'gamipress_achievement_type_data_meta_box_fields', array(
			array(
				'name' 	=> __( 'Singular Name', 'gamipress' ),
				'desc' 	=> __( 'The singular name for this achievement type.', 'gamipress' ),
				'id'   	=> $prefix . 'singular_name',
				'type' 	=> 'text_medium',
			),
			array(
				'name' 	=> __( 'Plural Name', 'gamipress' ),
				'desc' 	=> __( 'The plural name for this achievement type.', 'gamipress' ),
				'id'   	=> $prefix . 'plural_name',
				'type' 	=> 'text_medium',
			),
		), $prefix )
	));

	// Achievements
	new_cmb2_box( array(
		'id'         	=> 'achievement-data',
		'title'      	=> __( 'Achievement Data', 'gamipress' ),
		'object_types'  => $achievement_types,
		'context'    	=> 'advanced',
		'priority'   	=> 'high',
		'fields' 		=> apply_filters( 'gamipress_achievement_data_meta_box_fields', array(
			array(
				'name' => __( 'Points Awarded', 'gamipress' ),
				'desc' => ' '.__( 'Points awarded for earning this achievement (optional). Leave empty if no points are awarded.', 'gamipress' ),
				'id'   => $prefix . 'points',
				'type' => 'text_small',
			),
            array(
                'name' => __( 'Points Type', 'gamipress' ),
                'desc' => ' '.__( 'Points type to award for earning this achievement (optional).', 'gamipress' ),
                'id'   => $prefix . 'points_type',
                'type' => 'select',
                'options' => $points_types_options
            ),
			array(
				'name'    => __( 'Earned By:', 'gamipress' ),
				'desc'    => __( 'How this achievement can be earned.', 'gamipress' ),
				'id'      => $prefix . 'earned_by',
				'type'    => 'select',
				'options' => apply_filters( 'gamipress_achievement_earned_by', array(
					'triggers' 			=> __( 'Completing Steps', 'gamipress' ),
					'points' 			=> __( 'Minimum Number of Points', 'gamipress' ),
					'admin' 			=> __( 'Admin-awarded Only', 'gamipress' ),
				) )
			),
			array(
				'name' => __( 'Minimum Points Required', 'gamipress' ),
				'desc' => ' '.__( 'Fewest number of points required for earning this achievement.', 'gamipress' ),
				'id'   => $prefix . 'points_required',
				'type' => 'text_small',
			),
            array(
                'name' => __( 'Points Type Required', 'gamipress' ),
                'desc' => ' '.__( 'Points type of points required for earning this achievement (optional).', 'gamipress' ),
                'id'   => $prefix . 'points_type_required',
                'type' => 'select',
                'options' => $points_types_options
            ),
			array(
				'name' => __( 'Sequential Steps', 'gamipress' ),
				'desc' => ' '.__( 'Yes, steps must be completed in order.', 'gamipress' ),
				'id'   => $prefix . 'sequential',
				'type' => 'checkbox',
				'classes' => 'gamipress-switch'
			),
			array(
				'name' => __( 'Show Earners', 'gamipress' ),
				'desc' => ' '.__( 'Yes, display a list of users who have earned this achievement.', 'gamipress' ),
				'id'   => $prefix . 'show_earners',
				'type' => 'checkbox',
				'classes' => 'gamipress-switch'
			),
			array(
				'name' => __( 'Congratulations Text', 'gamipress' ),
				'desc' => __( 'Displayed after achievement is earned.', 'gamipress' ),
				'id'   => $prefix . 'congratulations_text',
				'type' => 'textarea',
			),
			array(
				'name' => __( 'Maximum Earnings', 'gamipress' ),
				'desc' => ' '.__( 'Number of times a user can earn this achievement (leave empty for no maximum).', 'gamipress' ),
				'id'   => $prefix . 'maximum_earnings',
				'type' => 'text_small',
				'default' => '1',
			),
			array(
				'name'    => __( 'Hidden?', 'gamipress' ),
				'desc'    => '',
				'id'      => $prefix . 'hidden',
				'type'    => 'select',
				'options' => array(
					'show' 		=> __( 'Show to User', 'gamipress' ),
					'hidden' 	=> __( 'Hidden to User', 'gamipress' ),
				),
			),
		), $prefix )
	) );

	// Log
	new_cmb2_box(array(
		'id'           	=> 'log-data',
		'title'        	=> __( 'Log Data', 'gamipress' ),
		'object_types' 	=> array( 'gamipress-log' ),
		'context'      	=> 'normal',
		'priority'     	=> 'high',
		'fields' 		=> apply_filters( 'gamipress_log_data_meta_box_fields', array(
			array(
				'name' 	=> __( 'User', 'gamipress' ),
				'desc' 	=> __( 'User assigned to this log.', 'gamipress' ),
				'id'   	=> 'post_author',
				'type' 	=> 'select',
                'options_cb' => 'gamipress_log_post_author_options'
			),
            array(
                'name' 	=> __( 'Type', 'gamipress' ),
                'desc' 	=> __( 'The log type.', 'gamipress' ),
                'id'   	=> $prefix . 'type',
                'type' 	=> 'select',
                'options' 	=> gamipress_get_log_types(),
            ),
			array(
				'name' 	=> __( 'Pattern', 'gamipress' ),
				'desc' 	=> __( 'The log output pattern. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html(),
				'id'   	=> $prefix . 'pattern',
				'type' 	=> 'text',
			),
		), $prefix )
	));

}
add_action( 'cmb2_admin_init', 'gamipress_meta_boxes' );

/**
 * Render a text-only field type for our CMB integration.
 *
 * @since  1.0.0
 * @param  array $field The field data array
 * @param  string $meta The stored meta for this field (which will always be blank)
 * @return string       HTML markup for our field
 */
function gamipress_cmb_render_text_only(  $field, $value, $object_id, $object_type, $field_type ) {
	echo $field->args( 'description' );
}
add_action( 'cmb2_render_text_only', 'gamipress_cmb_render_text_only', 10, 5 );

function gamipress_log_post_author_options( $field ) {
    global $post;

    $post_author =  get_post_field( 'post_author', $post->ID );
    $user = get_userdata( $post_author );

    return array( $post_author => $user->display_name . ' (' . $user->user_login . ')' );
}

// Override the post author field retrieval so CMB2 doesn't look in post-meta.
function cmb2_override_post_author_display( $data, $post_id ) {
	return get_post_field( 'post_author', $post_id );
}


// WordPress will handle the saving for us, so don't save post author to meta.
add_filter( 'cmb2_override_post_author_meta_save', '__return_true' );

// Show log title in edit log screen
function gamipress_admin_log_title_preview( $post ) {
	if( $post->post_type === 'gamipress-log' ) : ?>
	<div class="gamipress-log-title-preview">
		<h1><?php echo get_the_title( $post->ID ); ?></h1>
	</div>
	<?php endif;
}
add_action( 'edit_form_after_title', 'gamipress_admin_log_title_preview' );
