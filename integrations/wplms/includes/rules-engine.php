<?php
/**
 * Rules Engine
 *
 * @package GamiPress\WPLMS\Rules_Engine
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

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
function gamipress_wplms_user_has_access_to_achievement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) )
        return $return;

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

    // If is minimum score trigger, rules engine needs to check the minimum score
    if(
        $trigger === 'gamipress_wplms_complete_course_minimum_mark'
        || $trigger === 'gamipress_wplms_complete_specific_course_minimum_mark'
        || $trigger === 'gamipress_wplms_complete_quiz_minimum_mark'
        || $trigger === 'gamipress_wplms_complete_specific_quiz_minimum_mark'
        || $trigger === 'gamipress_wplms_complete_assignment_minimum_mark'
        || $trigger === 'gamipress_wplms_complete_specific_assignment_minimum_mark'
    ) {

        $score = $args[2];
        $required_score = absint( get_post_meta( $requirement_id, '_gamipress_wplms_score', true ) );

        if( is_numeric( $score ) ) {
            // Numeric score (X)

            $score = absint( $score );

            // True if there is score is bigger than required score
            $return = (bool) ( $score >= $required_score );

        } else if( strpos( $score, '-' ) !== false ) {
            // Score range (X-Y)

            $score_range = explode( '-', $score );

            // True if there is maximum score in range is bigger than required score
            $return = (bool) ( $score_range[1] >= $required_score );
        }

    }

    // Send back our eligibility
    return $return;

}
add_filter( 'user_has_access_to_achievement', 'gamipress_wplms_user_has_access_to_achievement', 10, 6 );