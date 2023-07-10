<?php
/**
 * Rules Engine
 *
 * @package GamiPress\Forminator\Rules_Engine
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Checks if given requirement meets the requirements of triggered event
 *
 * @since 1.0.0
 *
 * @param int 	    $requirement_id
 * @param string 	$trigger
 * @param array 	$args
 *
 * @return bool
 */
function gamipress_forminator_check_if_meets_requirements( $requirement_id, $trigger, $args ) {

    // Initialize the return value
    $return = true;

    // If is between score trigger, rules engine needs to check if field name and values matches required ones
    if( $return && ( $trigger === 'gamipress_forminator_form_field_value_submission'
            || $trigger === 'gamipress_forminator_specific_form_field_value_submission'
            || $trigger === 'gamipress_forminator_poll_field_value_submission'
            || $trigger === 'gamipress_forminator_specific_poll_field_value_submission'
            || $trigger === 'gamipress_forminator_quiz_field_value_submission'
            || $trigger === 'gamipress_forminator_specific_quiz_field_value_submission' ) ) {

        $field_name = $args[2];
        $field_value = $args[3];

        $required_field_name = get_post_meta( $requirement_id, '_gamipress_forminator_field_name', true );
        $required_field_value = get_post_meta( $requirement_id, '_gamipress_forminator_field_value', true );

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

    return $return;

}

/**
 * Filter triggered requirements to reduce the number of requirements to check by the awards engine
 *
 * @since 1.0.0
 *
 * @param array 	$triggered_requirements
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_forminator_filter_triggered_requirements( $triggered_requirements, $user_id, $trigger, $site_id, $args ) {

    $new_requirements = array();

    foreach( $triggered_requirements as $i => $requirement ) {

        // Skip item
        if( ! gamipress_forminator_check_if_meets_requirements( $requirement->ID, $trigger, $args ) ) {
            continue;
        }

        // Keep the requirement on the list of requirements to check by the awards engine
        $new_requirements[] = $requirement;

    }

    return $new_requirements;

}
add_filter( 'gamipress_get_triggered_requirements', 'gamipress_forminator_filter_triggered_requirements', 20, 5 );

/**
 * Checks if an user is allowed to work on a given requirement related to a minimum of score
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
function gamipress_forminator_user_has_access_to_achievement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) )
        return $return;

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

    // Send back our eligibility
    return gamipress_forminator_check_if_meets_requirements( $requirement_id, $trigger, $args );

}
add_filter( 'user_has_access_to_achievement', 'gamipress_forminator_user_has_access_to_achievement', 10, 6 );