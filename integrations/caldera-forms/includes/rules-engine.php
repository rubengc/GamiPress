<?php
/**
 * Rules Engine
 *
 * @package GamiPress\Caldera_Forms\Rules_Engine
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Checks if an user is allowed to work on a given requirement related to a specific form
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
function gamipress_cf_user_has_access_to_achievement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) )
        return $return;

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

    // If is specific form trigger, rules engine needs the attached form
    if( $trigger === 'gamipress_cf_specific_new_form_submission'
        || $trigger === 'gamipress_cf_specific_field_value_submission' ) {

        $form_id = $args[0];
        $required_form_id = get_post_meta( $requirement_id, '_gamipress_caldera_form', true );

        // True if there is a specific form, an attached form and both are equal
        $return = (bool) (
            $form_id
            && $required_form_id
            && $form_id === $required_form_id
        );
    }

    // If is specific field value trigger, rules engine needs the field value
    if( $return && ( $trigger === 'gamipress_cf_field_value_submission'
        || $trigger === 'gamipress_cf_specific_field_value_submission' ) ) {

        $field_name = $args[2];
        $field_value = $args[3];

        $required_field_name = get_post_meta( $requirement_id, '_gamipress_cf_field_name', true );
        $required_field_value = get_post_meta( $requirement_id, '_gamipress_cf_field_value', true );

        // First, check if field name matches the required one
        $return = (bool) ( $field_name === $required_field_name );

        if( $return ) {
            // Check if field value matches the required one (with support for arrays)
            if( is_array( $field_value ) )
                $return = (bool) ( in_array( $required_field_value, $field_value ) );
            else
                $return = (bool) ( $field_value === $required_field_value );
        }
    }

    // Send back our eligibility
    return $return;
}
add_filter( 'user_has_access_to_achievement', 'gamipress_cf_user_has_access_to_achievement', 10, 6 );