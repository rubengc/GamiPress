<?php
/**
 * Compatibility with CMB2
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Check if should override on admin init action
 *
 * @since 1.0.0
 *
 * @param $ct_table_name
 * @param $object
 */
function ct_cmb2_admin_init() {

    global $ct_registered_tables, $ct_table, $ct_cmb2_override, $pagenow;

    // Setup a custom global to meet that we need to override it
    $ct_cmb2_override = false;

    // Check if is on admin.php
    if( $pagenow !== 'admin.php' ) {
        return;
    }

    // Check if isset page query parameter
    if( ! isset( $_GET['page'] ) ) {
        return;
    }

    foreach( $ct_registered_tables as $ct_registered_table ) {

        // Check if is edit page slug
        if( $_GET['page'] === $ct_registered_table->views->edit->get_slug() ) {
            // Let know to this compatibility module it needs to operate
            $ct_cmb2_override = true;
        }
    }

}
add_action( 'admin_init', 'ct_cmb2_admin_init', 1 );

/**
 * Check if should override on add meta boxes action
 *
 * @since 1.0.0
 *
 * @param $ct_table_name
 * @param $object
 */
function ct_cmb2_add_meta_boxes( $ct_table_name, $object ) {

    global $ct_registered_tables, $ct_table, $ct_cmb2_override;

    // If not is a registered table, return
    if( ! isset( $ct_registered_tables[$ct_table_name] ) ) {
        return;
    }

    // If not object given, return
    if( ! $object ) {
        return;
    }

    $primary_key = $ct_table->db->primary_key;

    // Setup a false post var to allow CMB2 trigger cmb2_override_meta_value hook
    $_REQUEST['post'] = $object->$primary_key;

    // Let know to this compatibility module it needs to operate
    $ct_cmb2_override = true;

    // Fix: CMB2 stop enqueuing their assets so need to add it again
    CMB2_Hookup::enqueue_cmb_css();
    CMB2_Hookup::enqueue_cmb_js();

}
add_action( 'add_meta_boxes', 'ct_cmb2_add_meta_boxes', 10, 2 );

/**
 * On save an object, let it know to CMB2
 *
 * @since 1.0.0
 *
 * @param $object_id
 * @param $object
 */
function ct_cmb2_save_object( $object_id, $object ) {

    global $ct_registered_tables, $ct_table, $ct_cmb2_override;

    // Return if CMB2 not exists
    if( ! class_exists( 'CMB2' ) ) {
        return;
    }

    // Return if user is not allowed
    if ( ! current_user_can( $ct_table->cap->edit_item, $object_id ) ) {
        return;
    }

    // Setup a custom global to meet that we need to override it
    $ct_cmb2_override = true;

    // Loop all registered boxes
    foreach( CMB2_Boxes::get_all() as $cmb ) {

        // Skip meta boxes that do not support this CT_Table
        if( ! in_array( $ct_table->name, $cmb->meta_box['object_types'] ) ) {
            continue;
        }

        // Take a trip to reading railroad – if you pass go collect $200
        $cmb->save_fields( $object_id, 'post', $_POST );
    }

}
add_action( 'ct_save_object', 'ct_cmb2_save_object', 10, 2 );

/**
 * Override the CMB2 field value
 *
 * @since 1.0.0
 *
 * @param $value
 * @param $object_id
 * @param $args
 * @param $field
 *
 * @return mixed|string
 */
function ct_cmb2_override_meta_value( $value, $object_id, $args, $field ) {

    global $ct_registered_tables, $ct_table, $ct_cmb2_override;

    if( ! is_a( $ct_table, 'CT_Table' ) ) {
        return $value;
    }

    if( $ct_cmb2_override !== true ) {
        return $value;
    }

    $object = (array) ct_get_object( $object_id );

    // Check if is a main field
    if( isset( $object[$args['field_id']] ) ) {
        return $object[$args['field_id']];
    }

    // If not is a main field and CT_Table supports meta data, then try to get its value from meta table
    if( in_array( 'meta', $ct_table->supports ) ) {
        return ct_get_object_meta( $object_id, $args['field_id'], ( $args['single'] || $args['repeat'] ) );
    }

    return '';
}
add_filter( 'cmb2_override_meta_value', 'ct_cmb2_override_meta_value', 10, 4 );

/**
 * Override the CMB2 field value save
 *
 * @since 1.0.0
 *
 * @param $check
 * @param $args
 * @param $field_args
 * @param $field
 *
 * @return bool|false|int
 */
function ct_cmb2_override_meta_save( $check, $args, $field_args, $field ) {

    global $ct_registered_tables, $ct_table, $ct_cmb2_override;

    if( $ct_cmb2_override !== true ) {
        return $check;
    }

    $object = (array) ct_get_object( $args['id'] );

    // If not is a main field and CT_Table supports meta data, then try to save the given value to the meta table
    // Note: Main fields are automatically stored by the save method on the CT_Edit_View edit screen
    if( ! isset( $object[$args['field_id']] ) && in_array( 'meta', $ct_table->supports ) ) {

        // Add metadata if not single
        if ( ! $args['single'] ) {
            return ct_add_object_meta( $args['id'], $args['field_id'], $args['value'], false );
        }

        // Delete meta if we have an empty array
        if ( is_array( $args['value'] ) && empty( $args['value'] ) ) {
            return ct_delete_object_meta( $args['id'], $args['field_id'], $field->value );
        }

        // Update metadata
        return ct_update_object_meta( $args['id'], $args['field_id'], $args['value'] );

    }

    return $check;

}
add_filter( 'cmb2_override_meta_save', 'ct_cmb2_override_meta_save', 10, 4 );

/**
 * Override the CMB2 field value remove
 *
 * @since 1.0.0
 *
 * @param $check
 * @param $args
 * @param $field_args
 * @param $field
 *
 * @return bool|false|int
 */
function ct_cmb2_override_meta_remove( $check, $args, $field_args, $field ) {

    global $ct_registered_tables, $ct_table, $ct_cmb2_override, $wpdb;

    if( $ct_cmb2_override !== true ) {
        return $check;
    }

    $object = (array) ct_get_object( $args['id'] );
    
    // If not is a main field and CT_Table supports meta data, then try to remove the given value to the meta table
    // Note: Main fields are automatically managed by the save method on the CT_Edit_View edit screen
    if( ! isset( $object[$args['field_id']] ) && in_array( 'meta', $ct_table->supports ) ) {

        if( $field_args['multiple'] && ! $field_args['repeatable'] ) {
            // Delete multiple entries
            $meta_table_name = $ct_table->meta->db->table_name;
            $primary_key = $ct_table->db->primary_key;

            $where = array(
                'meta_key' => $args['field_id']
            );

            $where[$primary_key] = $args['id'];

            return $wpdb->delete( $meta_table_name, $where );

        } else {
            // Delete single entry
            return ct_delete_object_meta( $args['id'], $args['field_id'], $field->value );
        }

    }

    return $check;

}
add_filter( 'cmb2_override_meta_remove', 'ct_cmb2_override_meta_remove', 10, 4 );
