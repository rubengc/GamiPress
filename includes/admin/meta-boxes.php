<?php
/**
 * Admin Meta Boxes
 *
 * @package     GamiPress\Admin\Meta_Boxes
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_DIR . 'includes/admin/meta-boxes/achievement-type.php';
require_once GAMIPRESS_DIR . 'includes/admin/meta-boxes/achievements.php';
require_once GAMIPRESS_DIR . 'includes/admin/meta-boxes/points-type.php';
require_once GAMIPRESS_DIR . 'includes/admin/meta-boxes/rank-type.php';
require_once GAMIPRESS_DIR . 'includes/admin/meta-boxes/ranks.php';
require_once GAMIPRESS_DIR . 'includes/admin/meta-boxes/requirements.php';
require_once GAMIPRESS_DIR . 'includes/admin/meta-boxes/logs.php';

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

	// First, filter fields to allow extend it
	$fields = apply_filters( "gamipress_{$hook_id}_fields", $fields, $args );

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

	// Filter tabs to allow extend it
	$tabs = apply_filters( "gamipress_{$hook_id}_tabs", $args['tabs'], $fields, $args );

	// Parse tabs
	foreach( $tabs as $tab_id => $tab ) {

		$tabs[$tab_id]['id'] = $tab_id;

	}

	new_cmb2_box( array(
		'id'           	=> $id,
		'title'        	=> $title,
		'object_types' 	=> ! is_array( $object_types) ? array( $object_types ) : $object_types,
		'tabs'      	=> $tabs,
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
function gamipress_init_meta_boxes() {

	global $post, $pagenow, $ct_table;

    if( ! in_array( $pagenow, array( 'post-new.php', 'post.php', 'admin.php' ) ) ) {
        return;
    }

    $post_type = '';

    // Get post type from global post
    if( $post && $post->post_type ) {
        $post_type = $post->post_type;
    }

    // On post.php post ID is on GET parameters
    if( empty( $post_type ) && isset( $_GET['post'] ) ) {
        $post_type = gamipress_get_post_field( 'post_type', $_GET['post'] );
    }

    // On post-new.php and sometimes on post.php post type is on GET or POST parameters
    if( empty( $post_type ) && isset( $_REQUEST['post_type'] ) ) {
        $post_type = $_REQUEST['post_type'];
    }

    // Check if there is a CT view
    if( empty( $post_type ) && $pagenow === 'admin.php' && $ct_table ) {
        $post_type = $ct_table->name;
    }

    // Check if there is the edit GamiPress logs screen
    if( empty( $post_type ) && $pagenow === 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] === 'edit_gamipress_logs' ) {
        $post_type = 'gamipress_logs';
    }

    /**
     * Hook to register meta boxes
     *
     * @since 1.4.7
     *
     * @param string $post_type
     */
	do_action( 'gamipress_init_meta_boxes', $post_type );

    /**
     * Hook to register meta boxes
     *
     * @since 1.4.7
     */
	do_action( "gamipress_init_{$post_type}_meta_boxes" );

}
add_action( 'cmb2_admin_init', 'gamipress_init_meta_boxes' );

function gamipress_remove_meta_boxes() {

	remove_meta_box( 'slugdiv', 'points-type', 'normal' );
	remove_meta_box( 'slugdiv', 'achievement-type', 'normal' );
	remove_meta_box( 'slugdiv', 'rank-type', 'normal' );

}
add_action( 'admin_menu', 'gamipress_remove_meta_boxes' );

// Options callback for select2 fields assigned to posts
function gamipress_options_cb_posts( $field ) {

	$value = $field->escaped_value;
	$options = array();

	if( ! empty( $value ) ) {
		if( ! is_array( $value ) ) {
			$value = array( $value );
		}

		foreach( $value as $post_id ) {
			$options[$post_id] = get_post_field( 'post_title', $post_id );
		}
	}

	return $options;

}

// Options callback for select2 fields assigned to users
function gamipress_options_cb_users( $field ) {

	$value = $field->escaped_value;
	$options = array();

	if( ! empty( $value ) ) {
		if( ! is_array( $value ) ) {
			$value = array( $value );
		}

		foreach( $value as $user_id ) {
			$user_data = get_userdata($user_id);

			$options[$user_id] = $user_data->user_login;
		}
	}

	return $options;

}

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
 * Helper function to avoid errors with default param
 */
function gamipress_site_name_default_cb( $field_args, $field ) {

	return get_bloginfo( 'name' );

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

function gamipress_cmb2_override_menu_order_display( $data, $post_id ) {

	// Code has been moved to gamipress_get_rank_priority() on 1.3.9

	return gamipress_get_rank_priority( $post_id );

}
add_filter( 'cmb2_override_menu_order_meta_value', 'gamipress_cmb2_override_menu_order_display', 10, 2 );

/*
 * WP will handle the saving for us, so don't save title/content to meta.
 */
add_filter( 'cmb2_override_post_title_meta_save', '__return_true' );
add_filter( 'cmb2_override_post_name_meta_save', '__return_true' );
add_filter( 'cmb2_override_menu_order_meta_save', '__return_true' );

// Options callback to return achievement types as options
function gamipress_options_cb_achievement_types( $field ) {

	// Setup a custom array of achievement types
	$options = array( 'all' => __( 'All', 'gamipress' ) );

	foreach ( gamipress_get_achievement_types() as $slug => $data ) {
		$options[$slug] = $data['plural_name'];
	}

	return $options;

}

// Options callback to return points types as options
function gamipress_options_cb_points_types( $field ) {

	// Setup a custom array of points types
	$options = array( 'all' => __( 'All', 'gamipress' ) );

	foreach ( gamipress_get_points_types() as $slug => $data ) {
		$options[$slug] = $data['plural_name'];
	}

	return $options;

}

// Options callback to return rank types as options
function gamipress_options_cb_rank_types( $field ) {

	// Setup a custom array of achievement types
	$options = array( 'all' => __( 'All', 'gamipress' ) );

	foreach ( gamipress_get_rank_types() as $slug => $data ) {
		$options[$slug] = $data['plural_name'];
	}

	return $options;

}

// Options callback to return log types as options
function gamipress_options_cb_log_types( $field ) {

	// Setup a custom array of log types
	$options = array_merge( array( 'all' => __( 'All', 'gamipress' ) ), gamipress_get_log_types() );

	return $options;

}
