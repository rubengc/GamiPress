<?php
/**
 * Rules Engine
 *
 * @package GamiPress\BuddyPress\Rules_Engine
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Checks if given requirement meets the requirements of triggered event
 *
 * @since 1.1.2
 *
 * @param int 	    $requirement_id
 * @param string 	$trigger
 * @param array 	$args
 *
 * @return bool
 */
function gamipress_bp_check_if_meets_requirements( $requirement_id, $trigger, $args ) {

    // Initialize the return value
    $return = true;

    // Check if given member type match with specific member type
    if( $trigger === 'gamipress_bp_set_member_type' ) {

        $member_type = $args[1];
        $required_member_type = gamipress_get_post_meta( $requirement_id, '_gamipress_bp_member_type', true );

        $return = (bool) (
            $member_type !== ''
            && $required_member_type !== ''
            && $member_type === $required_member_type );
    }

    if( $trigger === 'gamipress_bp_update_profile_specific_value' ) {

        $field_value = $args[2];
        $required_field_value = gamipress_get_post_meta( $requirement_id, '_gamipress_bp_field_value', true );

        $return = (bool) (
            $field_value !== ''
            && $required_field_value !== ''
            && $field_value === $required_field_value );
    }

    return $return;

}

/**
 * Filter triggered requirements to reduce the number of requirements to check by the awards engine
 *
 * @since 1.1.2
 *
 * @param array 	$triggered_requirements
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_bp_filter_triggered_requirements( $triggered_requirements, $user_id, $trigger, $site_id, $args ) {

    $new_requirements = array();

    foreach( $triggered_requirements as $i => $requirement ) {

        // Skip item
        if( ! gamipress_bp_check_if_meets_requirements( $requirement->ID, $trigger, $args ) ) {
            continue;
        }

        // Keep the requirement on the list of requirements to check by the awards engine
        $new_requirements[] = $requirement;

    }

    return $new_requirements;

}
add_filter( 'gamipress_get_triggered_requirements', 'gamipress_bp_filter_triggered_requirements', 20, 5 );

/**
 * Checks if an user is allowed to work on a given requirement related to a specific requirement
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
function gamipress_bp_user_has_access_to_achievement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( gamipress_get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) ) {
        return $return;
    }

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return ) {
        return $return;
    }

    // Send back our eligibility
    return gamipress_bp_check_if_meets_requirements( $requirement_id, $trigger, $args );
}
add_filter( 'user_has_access_to_achievement', 'gamipress_bp_user_has_access_to_achievement', 10, 6 );

/**
 * Check if user is deserved to get awarded
 *
 * @since 1.0.0
 *
 * @param bool      $return
 * @param int       $user_id
 * @param string    $trigger
 * @param int       $site_id
 * @param array     $args
 *
 * @return bool
 */
function gamipress_bp_block_users_user_deserves_trigger( $return, $user_id, $trigger, $site_id, $args ) {

    $blocked_member_types = gamipress_bp_get_option( 'blocked_member_types' );

    if( ! function_exists( 'bp_get_member_type' ) ) {
        return $return;
    }

    // Check if user member type has been manually blocked
    if( is_array( $blocked_member_types ) ) {

        $user_member_type = bp_get_member_type( $user_id );

        foreach( $blocked_member_types as $blocked_member_type ) {
            // Bail if user member type is blocked
            if( $user_member_type === $blocked_member_type ) {
                return false;
            }
        }

    }

    return $return;
}
add_filter( 'gamipress_user_deserves_trigger', 'gamipress_bp_block_users_user_deserves_trigger', 10, 5 );