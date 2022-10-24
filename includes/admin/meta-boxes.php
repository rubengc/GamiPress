<?php
/**
 * Admin Meta Boxes
 *
 * @package     GamiPress\Admin\Meta_Boxes
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
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
require_once GAMIPRESS_DIR . 'includes/admin/meta-boxes/requirements-ui.php';
require_once GAMIPRESS_DIR . 'includes/admin/meta-boxes/earners.php';
require_once GAMIPRESS_DIR . 'includes/admin/meta-boxes/logs.php';
require_once GAMIPRESS_DIR . 'includes/admin/meta-boxes/log-extra-data-ui.php';

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

    /**
     * Filter box fields to allow extend it
     *
     * @since  1.0.8
     *
     * @param array $fields Box fields
     * @param array $args   Box args
     *
     * @return array
     */
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

    /**
     * Filter box tabs to allow extend it
     *
     * @since  1.0.8
     *
     * @param array $tabs   Box tabs
     * @param array $fields Box fields
     * @param array $args   Box args
     *
     * @return array
     */
	$tabs = apply_filters( "gamipress_{$hook_id}_tabs", $args['tabs'], $fields, $args );

	// Parse tabs
	foreach( $tabs as $tab_id => $tab ) {

		$tabs[$tab_id]['id'] = $tab_id;

	}

	// Setup the final box arguments
	$box = array(
        'id'           	=> $id,
        'title'        	=> $title,
        'object_types' 	=> ! is_array( $object_types) ? array( $object_types ) : $object_types,
        'tabs'      	=> $tabs,
        'vertical_tabs' => $args['vertical_tabs'],
        'context'      	=> $args['context'],
        'priority'     	=> $args['priority'],
        'classes'		=> 'gamipress-form gamipress-box-form',
        'fields' 		=> $fields
    );

    /**
     * Filter the final box args that will be passed to CMB2
     *
     * @since  1.6.9
     *
     * @param array 		$box            Final box args
     * @param string 		$id             Box id
     * @param string 		$title          Box title
     * @param string|array 	$object_types   Object types where box will appear
     * @param array 		$fields         Box fields
     * @param array 		$tabs           Box tabs
     * @param array 		$args           Box args
     */
    apply_filters( "gamipress_{$hook_id}_box", $box, $id, $title, $object_types, $fields, $tabs, $args );

    // Instance the CMB2 box
	new_cmb2_box( $box );
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
    if( $post && $post->post_type )
        $post_type = $post->post_type;

    // On post.php post ID is on GET parameters
    if( empty( $post_type ) && isset( $_GET['post'] ) ) {
        $post_type = gamipress_get_post_field( 'post_type', $_GET['post'] );
    }

    // On post-new.php and sometimes on post.php post type is on GET or POST parameters
    if( empty( $post_type ) && isset( $_REQUEST['post_type'] ) ) {
        $post_type = sanitize_text_field( $_REQUEST['post_type'] );
    }

    // Check if there is a CT view
    if( empty( $post_type ) && $pagenow === 'admin.php' && $ct_table ) {
        $post_type = $ct_table->name;
    }

    // Check if there is the edit GamiPress logs screen
    if( $pagenow === 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] === 'edit_gamipress_logs' ) {
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
			$options[$post_id] = get_post_field( 'post_title', $post_id ) . ' (#' . $post_id . ')';
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

// Options callback for WordPress roles
function gamipress_options_cb_roles( $field ) {

    $options = array();

    $editable_roles = get_editable_roles();

    $field->args['excluded_roles'] = ( isset( $field->args['excluded_roles'] ) ? $field->args['excluded_roles'] : array() );

    // Ensure excluded roles as array
    if( ! is_array( $field->args['excluded_roles'] ) )
        $field->args['excluded_roles'] = array( $field->args['excluded_roles'] );

    foreach ( $editable_roles as $role => $details ) {

        // Skip excluded roles
        if( in_array( $role, $field->args['excluded_roles'] ) )
            continue;

        $options[$role] = translate_user_role( $details['name'] );

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

    global $ct_cmb2_override;

    // Prevent to override on custom tables
    if( $ct_cmb2_override === true ) {
        return $data;
    }

	if( get_post_field( 'post_status', $post_id ) === 'auto-draft' ) {
		return '';
	}

	return get_post_field( 'post_title', $post_id );

}
add_filter( 'cmb2_override_post_title_meta_value', 'gamipress_cmb2_override_post_title_display', 10, 2 );

function gamipress_cmb2_override_post_name_display( $data, $post_id ) {

    global $ct_cmb2_override;

    // Prevent to override on custom tables
    if( $ct_cmb2_override === true ) {
        return $data;
    }

	return get_post_field( 'post_name', $post_id );

}
add_filter( 'cmb2_override_post_name_meta_value', 'gamipress_cmb2_override_post_name_display', 10, 2 );

function gamipress_cmb2_override_menu_order_display( $data, $post_id ) {

    global $ct_cmb2_override;

    // Prevent to override on custom tables
    if( $ct_cmb2_override === true ) {
        return $data;
    }

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

    // Custom option none label
    $field->args['option_none_label'] = ( isset( $field->args['option_none_label'] ) ? $field->args['option_none_label'] : __( 'Choose an achievement type', 'gamipress' ) );

    $options = gamipress_options_cb_init_options( $field );

	foreach ( gamipress_get_achievement_types() as $slug => $data ) {
		$options[$slug] = $data['plural_name'];
	}

	return $options;

}

// Options callback to return points types as options
function gamipress_options_cb_points_types( $field ) {

	// Setup a custom array of points types

    // Custom option none label
    $field->args['option_none_label'] = ( isset( $field->args['option_none_label'] ) ? $field->args['option_none_label'] : __( 'Choose a points type', 'gamipress' ) );

    $options = gamipress_options_cb_init_options( $field );

	foreach ( gamipress_get_points_types() as $slug => $data ) {
		$options[$slug] = $data['plural_name'];
	}

	return $options;

}

// Options callback to return rank types as options
function gamipress_options_cb_rank_types( $field ) {

	// Setup a custom array of rank types

    // Custom option none label
    $field->args['option_none_label'] = ( isset( $field->args['option_none_label'] ) ? $field->args['option_none_label'] : __( 'Choose a rank type', 'gamipress' ) );

    $options = gamipress_options_cb_init_options( $field );

	foreach ( gamipress_get_rank_types() as $slug => $data ) {
		$options[$slug] = $data['plural_name'];
	}

	return $options;

}

// Options callback to return log types as options
function gamipress_options_cb_log_types( $field ) {

	// Setup a custom array of log types

    // Custom option none label
    $field->args['option_none_label'] = ( isset( $field->args['option_none_label'] ) ? $field->args['option_none_label'] : __( 'Choose a log type', 'gamipress' ) );

    $options = gamipress_options_cb_init_options( $field );

    // Prepend the log types as options
    $options = array_merge( $options, gamipress_get_log_types() );

	return $options;

}

// Options callback to return the points label position options using the current points type label
function gamipress_options_cb_points_label_position( $field ) {

	$plural = gamipress_get_points_type_plural( $field->object_id, true );

	return array(
		'after' => sprintf( __( 'After (10 %s)', 'gamipress' ), $plural ),
		'before' => sprintf( __( 'Before (%s 10)', 'gamipress' ), $plural ),
	);

}

// Common callback for options_cb functions that support custom args
function gamipress_options_cb_init_options( $field ) {

    $options = array();

    // Default args

    // option_none = false
    $field->args['option_none'] = ( isset( $field->args['option_none'] ) ? $field->args['option_none'] : false );
    // option_none_label = __( 'Choose an option', 'gamipress' )
    $field->args['option_none_label'] = ( isset( $field->args['option_none_label'] ) ? $field->args['option_none_label'] : __( 'Choose an option', 'gamipress' ) );

    // option_all = true
    $field->args['option_all'] = ( isset( $field->args['option_all'] ) ? $field->args['option_all'] : true );
    // option_all_label = __( 'All', 'gamipress' )
    $field->args['option_all_label'] = ( isset( $field->args['option_all_label'] ) ? $field->args['option_all_label'] : __( 'All', 'gamipress' ) );

    // Check if option_none is set to true (by default option_none is set to false)
    if( $field->args['option_none'] ) $options[''] = $field->args['option_none_label'];

    // Check if option_all is set to true (by default option_all is set to true)
    if( $field->args['option_all'] ) $options['all'] = $field->args['option_all_label'];

    return $options;

}
