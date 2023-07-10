<?php
/**
 * Rules Engine
 *
 * @package GamiPress\wpForo\Rules_Engine
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

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
/*
function gamipress_wpforo_filter_triggered_requirements( $triggered_requirements, $user_id, $trigger, $site_id, $args ) {

    $new_requirements = array();

    foreach( $triggered_requirements as $i => $requirement ) {

        // Skip item
        if( ! gamipress_wpforo_check_if_meets_requirements( $requirement->ID, $trigger, $args ) ) {
            continue;
        }

        // Keep the requirement on the list of requirements to check by the awards engine
        $new_requirements[] = $requirement;

    }

    return $new_requirements;

}
add_filter( 'gamipress_get_triggered_requirements', 'gamipress_wpforo_filter_triggered_requirements', 20, 5 );
*/
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
function gamipress_wpforo_user_has_access_to_achievement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) )
        return $return;

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

    // Send back our eligibility
    return gamipress_wpforo_check_if_meets_requirements( $requirement_id, $trigger, $args );

}
add_filter( 'user_has_access_to_achievement', 'gamipress_wpforo_user_has_access_to_achievement', 10, 6 );

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
function gamipress_wpforo_check_if_meets_requirements( $requirement_id, $trigger, $args ) {

    // Initialize the return value
    $return = true;

    // Check forum ID
    if( $trigger === 'gamipress_wpforo_specific_forum_new_post'
    || $trigger === 'gamipress_wpforo_specific_forum_like_post'
    || $trigger === 'gamipress_wpforo_specific_forum_dislike_post'
    || $trigger === 'gamipress_wpforo_specific_forum_vote_up_post'
    || $trigger === 'gamipress_wpforo_specific_forum_vote_down_post'
    || $trigger === 'gamipress_wpforo_specific_forum_answer_question' ) {

        $forum_id = $args[3];

        $required_forum_id = get_post_meta( $requirement_id, '_gamipress_wpforo_forum', true );

        // Check if forum ID matches the required one
        $return = (bool) ( $forum_id === $required_forum_id );
    }

    // Check forum ID when a topic is created in specific forum
    if( $trigger === 'gamipress_wpforo_specific_forum_new_topic' ) {

        $forum_id = $args[2];

        $required_forum_id = get_post_meta( $requirement_id, '_gamipress_wpforo_forum', true );

        // Check if forum ID matches the required one
        $return = (bool) ( $forum_id === $required_forum_id );
    }

    // Check topic ID
    if( $trigger === 'gamipress_wpforo_specific_topic_new_post'
    || $trigger === 'gamipress_wpforo_specific_topic_like_post'
    || $trigger === 'gamipress_wpforo_specific_topic_dislike_post'
    || $trigger === 'gamipress_wpforo_specific_topic_vote_up_post'
    || $trigger === 'gamipress_wpforo_specific_topic_vote_down_post' ) {

        $topic_id = $args[2];

        $required_topic_id = get_post_meta( $requirement_id, '_gamipress_wpforo_topic', true );

        // Check if topic ID matches the required one
        $return = (bool) ( $topic_id === $required_topic_id );
    }

    return $return;

}