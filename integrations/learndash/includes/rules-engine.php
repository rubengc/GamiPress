<?php
/**
 * Rules Engine
 *
 * @package GamiPress\LearnDash\Rules_Engine
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Checks if given requirement meets the requirements of triggered event
 *
 * @since 1.2.5
 *
 * @param int 	    $requirement_id
 * @param string 	$trigger
 * @param array 	$args
 *
 * @return bool
 */
function gamipress_ld_check_if_meets_requirements( $requirement_id, $trigger, $args ) {

    // Initialize the return value
    $return = true;

    // If is minimum score trigger, rules engine needs to check the minimum score
    if( $trigger === 'gamipress_ld_complete_quiz_grade'
        || $trigger === 'gamipress_ld_complete_specific_quiz_grade'
        || $trigger === 'gamipress_ld_complete_quiz_specific_course_grade'
        || $trigger === 'gamipress_ld_complete_quiz_course_category_grade'
        || $trigger === 'gamipress_ld_complete_quiz_course_tag_grade' ) {

        $score = absint( $args[3] );

        $required_score = absint( get_post_meta( $requirement_id, '_gamipress_ld_score', true ) );

        // True if there is score is bigger than required score
        $return = (bool) ( $score >= $required_score );

    }

    // If is maximum score trigger, rules engine needs to check the maximum score
    if( $trigger === 'gamipress_ld_complete_quiz_max_grade'
        || $trigger === 'gamipress_ld_complete_specific_quiz_max_grade'
        || $trigger === 'gamipress_ld_complete_quiz_specific_course_max_grade'
        || $trigger === 'gamipress_ld_complete_quiz_course_category_max_grade'
        || $trigger === 'gamipress_ld_complete_quiz_course_tag_max_grade' ) {

        $score = absint( $args[3] );

        $required_score = absint( get_post_meta( $requirement_id, '_gamipress_ld_score', true ) );

        // True if there is score is lower than required score
        $return = (bool) ( $score <= $required_score );

    }

    // If is between score trigger, rules engine needs to check the minimum and maximum score allowed
    if( $trigger === 'gamipress_ld_complete_quiz_between_grade'
        || $trigger === 'gamipress_ld_complete_specific_quiz_between_grade'
        || $trigger === 'gamipress_ld_complete_quiz_specific_course_between_grade'
        || $trigger === 'gamipress_ld_complete_quiz_course_category_between_grade'
        || $trigger === 'gamipress_ld_complete_quiz_course_tag_between_grade' ) {

        $score = absint( $args[3] );

        $min_score = absint( get_post_meta( $requirement_id, '_gamipress_ld_min_score', true ) );
        $max_score = absint( get_post_meta( $requirement_id, '_gamipress_ld_max_score', true ) );

        // True if there is score is bigger than min score and lower than max score
        $return = (bool) ( $score >= $min_score && $score <= $max_score );

    }

    // If everything done, check if there is a specific term
    if ( $return ) {

        // Check if trigger requires a specific term
        $terms_ids_index = gamipress_ld_get_terms_ids_index( $trigger );

        if( $terms_ids_index !== -1 ) {
            // Add the terms IDs from $args
            $terms_ids = $args[$terms_ids_index];

            // If terms IDs exists, then check them against the requirement term ID
            if( is_array( $terms_ids ) && count( $terms_ids ) > 0 ) {
                $element = gamipress_ld_get_term_element( $trigger );
                $taxonomy = gamipress_ld_get_term_taxonomy( $trigger );

                $required_term_id = absint( get_post_meta( $requirement_id, '_gamipress_ld_' . $element . '_' . $taxonomy . '_id', true ) );

                // Turn all IDs into integers
                $terms_ids = array_map( 'absint', $terms_ids );

                // True if required term id is in the terms ids passed
                $return = in_array( $required_term_id, $terms_ids );
            }

        }

    }

    return $return;

}

/**
 * Filter triggered requirements to reduce the number of requirements to check by the awards engine
 *
 * @since 1.2.5
 *
 * @param array 	$triggered_requirements
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_ld_filter_triggered_requirements( $triggered_requirements, $user_id, $trigger, $site_id, $args ) {

    $new_requirements = array();

    foreach( $triggered_requirements as $i => $requirement ) {

        // Skip item
        if( ! gamipress_ld_check_if_meets_requirements( $requirement->ID, $trigger, $args ) ) {
            continue;
        }

        // Keep the requirement on the list of requirements to check by the awards engine
        $new_requirements[] = $requirement;

    }

    return $new_requirements;

}
add_filter( 'gamipress_get_triggered_requirements', 'gamipress_ld_filter_triggered_requirements', 20, 5 );

/**
 * Checks if a user is allowed to work on a given requirement related to a minimum of score
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
function gamipress_ld_user_has_access_to_achievement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) )
        return $return;

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

    // Send back our eligibility
    return gamipress_ld_check_if_meets_requirements( $requirement_id, $trigger, $args );

}
add_filter( 'user_has_access_to_achievement', 'gamipress_ld_user_has_access_to_achievement', 10, 6 );