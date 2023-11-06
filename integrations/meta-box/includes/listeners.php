<?php
/**
 * Listeners
 *
 * @package GamiPress\Meta_Box\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * User field update listener
 *
 * @since 1.0.0
 *
 * @param int    $meta_id     ID of updated metadata entry.
 * @param int    $object_id   ID of the object metadata is for.
 * @param string $meta_key    Metadata key.
 * @param mixed  $_meta_value  Metadata value. Serialized if non-scalar.
 */
function gamipress_meta_box_user_field_update_listener( $meta_id, $object_id, $meta_key, $_meta_value ) {

    global $wpdb;

    $user_id = get_current_user_id();

    // Login is required
    if ( $user_id === 0 ) return;

    if ( ! function_exists( 'rwmb_get_object_fields' ) ) {
        return;
    }

    // Ensure modified meta is a meta box
    $fields_allowed = array_keys( rwmb_get_object_fields( 'user', 'user' ) );

    // Bail if not exists META_BOX user fields
    if ( empty ( $fields_allowed ) ) {
        return;
    }

    // Don't deserve if meta_key is not allowed
    if ( ! in_array( $meta_key, $fields_allowed, true ) ) {
        return false;
    }

    // Update any user field with any value
    do_action( 'gamipress_meta_box_update_any_user_field_any_value', $user_id, $meta_id, $meta_key, $_meta_value, $object_id );

    // Update any user field with specific value
    do_action( 'gamipress_meta_box_update_any_user_field_specific_value', $user_id, $meta_id, $meta_key, $_meta_value, $object_id );

    // Update specific user field with any value
    do_action( 'gamipress_meta_box_update_specific_user_field_any_value', $user_id, $meta_id, $meta_key, $_meta_value, $object_id );

    // Update specific user field with specific value
    do_action( 'gamipress_meta_box_update_specific_user_field_specific_value', $user_id, $meta_id, $meta_key, $_meta_value, $object_id );

}
add_action( 'updated_user_meta', 'gamipress_meta_box_user_field_update_listener', 10, 4 );

/**
 * Post field update listener
 *
 * @since 1.0.0
 *
 * @param int    $meta_id     ID of updated metadata entry.
 * @param int    $object_id   ID of the object metadata is for.
 * @param string $meta_key    Metadata key.
 * @param mixed  $_meta_value  Metadata value. Serialized if non-scalar.
 */
function gamipress_meta_box_post_field_update_listener( $meta_id, $object_id, $meta_key, $_meta_value ) {

    global $wpdb;

    $user_id = get_current_user_id(); 

    // Login is required
    if ( $user_id === 0 ) return;

    if ( ! function_exists( 'rwmb_get_object_fields' ) ) {
        return;
    }
    
    $fields_allowed = array_keys( rwmb_get_object_fields( $object_id ) );

    // Bail if not exists META_BOX fields
    if ( empty ( $fields_allowed ) ) {
        return;
    }

    // Don't deserve if meta_key is not allowed
    if ( ! in_array( $meta_key, $fields_allowed, true ) ) {
        return false;
    }

    // Update any post field with any value
    do_action( 'gamipress_meta_box_update_any_post_field_any_value', $user_id, $meta_id, $meta_key, $_meta_value, $object_id );

    // Update any post field with specific value
    do_action( 'gamipress_meta_box_update_any_post_field_specific_value', $user_id, $meta_id, $meta_key, $_meta_value, $object_id );

    // Update specific post field with any value
    do_action( 'gamipress_meta_box_update_specific_post_field_any_value', $user_id, $meta_id, $meta_key, $_meta_value, $object_id );

    // Update specific post field with specific value
    do_action( 'gamipress_meta_box_update_specific_post_field_specific_value', $user_id, $meta_id, $meta_key, $_meta_value, $object_id );

}
add_action( 'updated_post_meta', 'gamipress_meta_box_post_field_update_listener', 10, 4 );
