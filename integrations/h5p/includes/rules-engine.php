<?php
/**
 * Rules Engine
 *
 * @package GamiPress\H5P\Rules_Engine
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
function gamipress_h5p_check_if_meets_requirements( $requirement_id, $trigger, $args ) {

    // Initialize the return value
    $return = true;

    // If is specific content type trigger, rules engine needs to check the content type
    if( $trigger === 'gamipress_h5p_complete_specific_content_type'
        || $trigger === 'gamipress_h5p_max_complete_specific_content_type'
        || $trigger === 'gamipress_h5p_complete_specific_content_type_min_score'
        || $trigger === 'gamipress_h5p_complete_specific_content_type_max_score'
        || $trigger === 'gamipress_h5p_complete_specific_content_type_between_score'
        || $trigger === 'gamipress_h5p_complete_specific_content_type_min_percentage'
        || $trigger === 'gamipress_h5p_complete_specific_content_type_max_percentage' ) {

        $content_type = $args[3];

        $required_content_type = get_post_meta( $requirement_id, '_gamipress_h5p_content_type', true );

        // True if content type is the required content type
        $return = (bool) ( $content_type === $required_content_type );
    }

    // If is minimum score trigger, rules engine needs to check the minimum score
    if( $return && ( $trigger === 'gamipress_h5p_complete_content_min_score'
            || $trigger === 'gamipress_h5p_complete_specific_content_min_score'
            || $trigger === 'gamipress_h5p_complete_specific_content_type_min_score' ) ) {

        $score = absint( $args[4] );

        $required_score = absint( get_post_meta( $requirement_id, '_gamipress_h5p_score', true ) );

        // True if there is score is bigger than required score
        $return = (bool) ( $score >= $required_score );
    }

    // If is maximum score trigger, rules engine needs to check the maximum score
    if( $return && ( $trigger === 'gamipress_h5p_complete_content_max_score'
            || $trigger === 'gamipress_h5p_complete_specific_content_max_score'
            || $trigger === 'gamipress_h5p_complete_specific_content_type_max_score' ) ) {

        $score = absint( $args[4] );

        $required_score = absint( get_post_meta( $requirement_id, '_gamipress_h5p_score', true ) );

        // True if there is score is lower than required score
        $return = (bool) ( $score <= $required_score );
    }

    // If is between score trigger, rules engine needs to check if score is between range of scores
    if( $return && ( $trigger === 'gamipress_h5p_complete_content_between_score'
            || $trigger === 'gamipress_h5p_complete_specific_content_between_score'
            || $trigger === 'gamipress_h5p_complete_specific_content_type_between_score' ) ) {

        $score = absint( $args[4] );

        $min_score = absint( get_post_meta( $requirement_id, '_gamipress_h5p_min_score', true ) );
        $max_score = absint( get_post_meta( $requirement_id, '_gamipress_h5p_max_score', true ) );

        // True if there is score is bigger than min score and lower than max score
        $return = (bool) ( $score >= $min_score && $score <= $max_score );
    }

    // If is minimum percentage trigger, rules engine needs to check the minimum percentage
    if( $return && ( $trigger === 'gamipress_h5p_complete_content_min_percentage'
            || $trigger === 'gamipress_h5p_complete_specific_content_min_percentage'
            || $trigger === 'gamipress_h5p_complete_specific_content_type_min_percentage' ) ) {

        $score = absint( $args[4] );
        $max_score = absint( $args[5] );
        $percentage = ( $score / $max_score ) * 100;

        $required_percentage = absint( get_post_meta( $requirement_id, '_gamipress_h5p_percentage', true ) );

        // True if there is percentage is bigger than required percentage
        $return = (bool) ( $percentage >= $required_percentage );
    }

    // If is maximum percentage trigger, rules engine needs to check the minimum percentage
    if( $return && ( $trigger === 'gamipress_h5p_complete_content_max_percentage'
            || $trigger === 'gamipress_h5p_complete_specific_content_max_percentage'
            || $trigger === 'gamipress_h5p_complete_specific_content_type_max_percentage' ) ) {

        $score = absint( $args[4] );
        $max_score = absint( $args[5] );
        $percentage = ( $score / $max_score ) * 100;

        $required_percentage = absint( get_post_meta( $requirement_id, '_gamipress_h5p_percentage', true ) );

        // True if there is percentage is bigger than required percentage
        $return = (bool) ( $percentage <= $required_percentage );
    }

    // If is between percentage trigger, rules engine needs to check the minimum and maximum percentage
    if( $return && ( $trigger === 'gamipress_h5p_complete_content_between_percentage'
            || $trigger === 'gamipress_h5p_complete_specific_content_between_percentage'
            || $trigger === 'gamipress_h5p_complete_specific_content_type_between_percentage' ) ) {

        $score = absint( $args[4] );
        $max_score = absint( $args[5] );
        $percentage = ( $score / $max_score ) * 100;

        $min_percentage = absint( get_post_meta( $requirement_id, '_gamipress_h5p_min_percentage', true ) );
        $max_percentage = absint( get_post_meta( $requirement_id, '_gamipress_h5p_max_percentage', true ) );

        // True if there is score is bigger than min percentage and lower than max percentage
        $return = (bool) ( $percentage >= $min_percentage && $percentage <= $max_percentage );
    }

    return $return;

}

/**
 * Filter triggered requirements to reduce the number of requirements to check by the awards engine
 *
 * @since 1.0.6
 *
 * @param array 	$triggered_requirements
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_h5p_filter_triggered_requirements( $triggered_requirements, $user_id, $trigger, $site_id, $args ) {

    $new_requirements = array();

    foreach( $triggered_requirements as $i => $requirement ) {

        // Skip item
        if ( ! gamipress_h5p_check_if_meets_requirements( $requirement->ID, $trigger, $args ) ) {
            continue;
        }

        // Keep the requirement on the list of requirements to check by the awards engine
        $new_requirements[] = $requirement;

    }

    return $new_requirements;

}
add_filter( 'gamipress_get_triggered_requirements', 'gamipress_h5p_filter_triggered_requirements', 20, 5 );

/**
 * Checks if an user is allowed to work on a given requirement related to a content type
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
function gamipress_h5p_user_has_access_to_achievement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) ) {
        return $return;
    }

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if ( ! $return ) {
        return $return;
    }

    // Send back our eligibility
    return gamipress_h5p_check_if_meets_requirements( $requirement_id, $trigger, $args );

}
add_filter( 'user_has_access_to_achievement', 'gamipress_h5p_user_has_access_to_achievement', 10, 6 );