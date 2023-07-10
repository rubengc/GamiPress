<?php
/**
 * Rules Engine
 *
 * @package GamiPress\WP_Job_Manager\Rules_Engine
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
function gamipress_wp_job_manager_user_has_access_to_achievement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) )
        return $return;

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

    // If is a specific type trigger, rules engine needs to check the type ID
    if( in_array( $trigger, array(
            'gamipress_wp_job_manager_publish_job_specific_type',
            'gamipress_wp_job_manager_mark_filled_specific_type',
            'gamipress_wp_job_manager_mark_not_filled_specific_type',
            // Applications
            'gamipress_wp_job_manager_job_application_specific_type',
            'gamipress_wp_job_manager_get_job_application_specific_type',
            'gamipress_wp_job_manager_job_application_hired_specific_type',
            'gamipress_wp_job_manager_job_application_rejected_specific_type',
        ) ) ) {

        $type_id = absint( $args[2] );

        $required_type_id = absint( get_post_meta( $requirement_id, '_gamipress_wp_job_manager_type_id', true ) );

        // True if is the correct type ID
        $return = (bool) ( $required_type_id === $type_id );
    }

    // Send back our eligibility
    return $return;
}
add_filter( 'user_has_access_to_achievement', 'gamipress_wp_job_manager_user_has_access_to_achievement', 10, 6 );