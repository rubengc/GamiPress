<?php
/**
 * Rules Engine
 *
 * @package GamiPress\Vimeo\Rules_Engine
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
function gamipress_vimeo_user_has_access_to_achievement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) )
        return $return;

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

    // If is watch specific video trigger, rules engine needs to check the id given
    if( $trigger === 'gamipress_vimeo_watch_specific_video' ) {

        $video_id = $args[0];

        $required_video_id = get_post_meta( $requirement_id, '_gamipress_vimeo_video_id', true );
        $required_video_id = gamipress_vimeo_get_video_id_from_url( $required_video_id );

        // Check if required video id is not empty to prevent award specific video when no ID is given
        $return = ! empty( $required_video_id );

        if( $return ) {
            // True if video id is the required one
            $return = (bool) ( $video_id === $required_video_id );
        }
    }

    // Send back our eligibility
    return $return;
}
add_filter( 'user_has_access_to_achievement', 'gamipress_vimeo_user_has_access_to_achievement', 10, 6 );