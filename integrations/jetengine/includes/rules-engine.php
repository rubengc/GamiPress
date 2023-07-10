<?php
/**
 * Rules Engine
 *
 * @package GamiPress\JetEngine\Rules_Engine
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
function gamipress_jetengine_user_has_access_to_achievement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) )
        return $return;

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

    // If is between score trigger, rules engine needs to check if field name and values matches required ones
    if( $return && ( $trigger === 'gamipress_jetengine_publish_post_specific_type'
            || $trigger === 'gamipress_jetengine_update_post_specific_type'
            || $trigger === 'gamipress_jetengine_delete_post_specific_type' ) ) {

        $post_type = $args[0];

        $required_post_type = get_post_meta( $requirement_id, '_gamipress_jetengine_post_type', true );

        // First, check if field name matches the required one
        $return = (bool) ( $post_type === $required_post_type );

        if( $return ) {
            // Check if post type matches the required one (with support for arrays)
            if( is_array( $post_type ) )
                $return = (bool) ( in_array( $required_post_type, $post_type ) );
            else
                $return = (bool) ( $post_type === $required_post_type );
        }
    }

    // Send back our eligibility
    return $return;

}
add_filter( 'user_has_access_to_achievement', 'gamipress_jetengine_user_has_access_to_achievement', 10, 6 );