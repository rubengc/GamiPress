<?php
/**
 * Rules Engine
 *
 * @package GamiPress\Meta_Box\Rules_Engine
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Checks if an user is allowed to work on a given requirement
 *
 * @since  1.0.0
 *
 * @param bool $return          The default return value
 * @param int $user_id          The given user's ID
 * @param int $requirement_id   The given requirement's post ID
 * @param string $trigger       The trigger triggered
 * @param int $site_id          The site id
 * @param array $args           Arguments of this trigger
 *
 * @return bool True if user has access to the requirement, false otherwise
 */
function gamipress_meta_box_user_has_access_to_achievement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) )
        return $return;

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

    // Rules engine needs to check if field name matches required ones
    if( $return && ( $trigger === 'gamipress_meta_box_update_specific_post_field_any_value'
        || $trigger === 'gamipress_meta_box_update_specific_user_field_any_value' ) ) {

        $return = gamipress_meta_box_fields_conditions( $trigger, $requirement_id, $args );
        
    }

    // Rules engine needs to check if value matches required ones
    if( $return && ( $trigger === 'gamipress_meta_box_update_any_post_field_specific_value'
            || $trigger === 'gamipress_meta_box_update_any_user_field_specific_value' ) ) {

        $field_value = $args[3];
 
        $condition = gamipress_get_post_meta( $requirement_id, '_gamipress_meta_box_field_value_condition', true );
        $required_field_value = gamipress_get_post_meta( $requirement_id, '_gamipress_meta_box_field_value', true );
        $return = (bool) ( gamipress_meta_box_condition_matches( $field_value, $required_field_value, $condition ) );
 
    }

    // Rules engine needs to check if field name and values matches required ones
    if( $return && ( $trigger === 'gamipress_meta_box_update_specific_post_field_specific_value'
            || $trigger === 'gamipress_meta_box_update_specific_user_field_specific_value' ) ) {

            $return = gamipress_meta_box_fields_values_conditions( $trigger, $requirement_id, $args );
 
    }

    // Send back our eligibility
    return $return;

}
add_filter( 'user_has_access_to_achievement', 'gamipress_meta_box_user_has_access_to_achievement', 10, 6 );

/**
 * Checks if field name matches condition
 *
 * @since  1.0.0
 *
 * @param string $trigger       The trigger triggered
 * @param int $requirement_id   The given requirement's post ID
 * @param array $args           Arguments of this trigger
 *
 * @return bool True if field name matches condition, false otherwise
 */
function gamipress_meta_box_fields_conditions( $trigger, $requirement_id, $args ) {

    $field_name = $args[2];

    if ( $trigger === 'gamipress_meta_box_update_specific_post_field_any_value' ) {
        $required_field_name = gamipress_get_post_meta( $requirement_id, '_gamipress_meta_box_field_name', true );
    }

    if ( $trigger === 'gamipress_meta_box_update_specific_user_field_any_value' ) {
        $required_field_name = gamipress_get_post_meta( $requirement_id, '_gamipress_meta_box_field_name_user', true );
    }

    // First, check if field name matches the required one
    $return = (bool) ( $field_name === $required_field_name );

    if( $return ) {
        // Check if field name matches the required one (with support for arrays)
        if( is_array( $field_name ) )
            $return = (bool) ( in_array( $required_field_name, $field_name ) );
        else
            $return = (bool) ( $field_name === $required_field_name );
    }

    return $return;

}

/**
 * Checks if field name and value match condition
 *
 * @since  1.0.0
 *
 * @param string $trigger       The trigger triggered
 * @param int $requirement_id   The given requirement's post ID
 * @param array $args           Arguments of this trigger
 *
 * @return bool True if field name and value match condition, false otherwise
 */
function gamipress_meta_box_fields_values_conditions( $trigger, $requirement_id, $args ) {

    $field_value = $args[3];
    $field_name = $args[2];

    if ( $trigger === 'gamipress_meta_box_update_specific_post_field_specific_value' ) {
        $required_field_name = gamipress_get_post_meta( $requirement_id, '_gamipress_meta_box_field_name', true );
    }

    if ( $trigger === 'gamipress_meta_box_update_specific_user_field_specific_value' ) {
        $required_field_name = gamipress_get_post_meta( $requirement_id, '_gamipress_meta_box_field_name_user', true );
    }

    $condition = gamipress_get_post_meta( $requirement_id, '_gamipress_meta_box_field_value_condition', true );
    $required_field_value = gamipress_get_post_meta( $requirement_id, '_gamipress_meta_box_field_value', true );

    // First, check if field name matches the required one
    $return = (bool) ( $field_name === $required_field_name );

    if( $return ) {
        // Check if field value matches the required one (with support for arrays)
        if( is_array( $field_name ) )
            $return = (bool) ( in_array( $required_field_name, $field_name ) );
        else
            $return = (bool) ( $field_name === $required_field_name );
    }

    if ( $return ) {
            $return = (bool) ( gamipress_meta_box_condition_matches( $field_value, $required_field_value, $condition ) );
    }

    return $return;

}
