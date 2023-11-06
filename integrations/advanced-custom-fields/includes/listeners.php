<?php
/**
 * Listeners
 *
 * @package GamiPress\Advanced_Custom_Fields\Listeners
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
 * @param mixed  $meta_value  Metadata value. Serialized if non-scalar.
 */
function gamipress_acf_user_field_update_listener( $meta_id, $object_id, $meta_key, $meta_value ) {

    global $wpdb;

    $fields_allowed = gamipress_acf_check_acf_user_fields();

    // Bail if not exists ACF user fields
    if ( empty ( $fields_allowed ) ) {
        return;
    }

    // Don't deserve if meta_key is not allowed
    if ( ! array_key_exists( $meta_key, $fields_allowed ) ) {
        return;
    }

    $meta_key = $wpdb->get_var( $wpdb->prepare(
        "SELECT a.ID FROM {$wpdb->prefix}posts AS a WHERE a.post_excerpt = %s",
         $meta_key
    ) );

    $user_id = $object_id;

    // Update any user field with any value
    do_action( 'gamipress_acf_update_any_user_field_any_value', $user_id, $meta_id, $meta_key, $meta_value, $object_id );

    // Update any user field with specific value
    do_action( 'gamipress_acf_update_any_user_field_specific_value', $user_id, $meta_id, $meta_key, $meta_value, $object_id );

    // Update specific user field with any value
    do_action( 'gamipress_acf_update_specific_user_field_any_value', $user_id, $meta_id, $meta_key, $meta_value, $object_id );

    // Update specific user field with specific value
    do_action( 'gamipress_acf_update_specific_user_field_specific_value', $user_id, $meta_id, $meta_key, $meta_value, $object_id );

}
add_action( 'updated_user_meta', 'gamipress_acf_user_field_update_listener', 10, 4 );

/**
 * Post field update listener
 *
 * @since 1.0.0
 *
 * @param int    $meta_id     ID of updated metadata entry.
 * @param int    $object_id   ID of the object metadata is for.
 * @param string $meta_key    Metadata key.
 * @param mixed  $meta_value  Metadata value. Serialized if non-scalar.
 */
function gamipress_acf_post_field_update_listener( $meta_id, $object_id, $meta_key, $meta_value ) {

    global $wpdb;

    $user_id = get_current_user_id(); 

    // Login is required
    if ( $user_id === 0 ) return;

    $fields_allowed = gamipress_acf_check_acf_fields();

    // Bail if not exists ACF fields
    if ( empty ( $fields_allowed ) ) {
        return;
    }

    // Don't deserve if meta_key is not allowed
    if ( ! array_key_exists( $meta_key, $fields_allowed ) ) {
        return;
    }

    $meta_key = $wpdb->get_var( $wpdb->prepare(
        "SELECT a.ID FROM {$wpdb->prefix}posts AS a WHERE a.post_excerpt = %s",
         $meta_key
    ) );
    
    // Update any post field with any value
    do_action( 'gamipress_acf_update_any_post_field_any_value', $user_id, $meta_id, $meta_key, $meta_value, $object_id );

    // Update any post field with specific value
    do_action( 'gamipress_acf_update_any_post_field_specific_value', $user_id, $meta_id, $meta_key, $meta_value, $object_id );

    // Update specific post field with any value
    do_action( 'gamipress_acf_update_specific_post_field_any_value', $user_id, $meta_id, $meta_key, $meta_value, $object_id );

    // Update specific post field with specific value
    do_action( 'gamipress_acf_update_specific_post_field_specific_value', $user_id, $meta_id, $meta_key, $meta_value, $object_id );

}
add_action( 'updated_post_meta', 'gamipress_acf_post_field_update_listener', 10, 4 );
