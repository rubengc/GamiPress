<?php
/**
 * Rules Engine
 *
 * @package GamiPress\Tutor\Rules_Engine
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
function gamipress_tutor_user_has_access_to_achievement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) )
        return $return;

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

    // If is category trigger, rules engine needs to check if field name and values matches required ones
    if( $return && ( $trigger === 'gamipress_tutor_complete_quiz_course_category'
            || $trigger === 'gamipress_tutor_pass_quiz_course_category'
            || $trigger === 'gamipress_tutor_fail_quiz_course_category' ) ) {

        $course_category = $args[4];
        $required_course_category = get_post_meta( $requirement_id, '_gamipress_tutor_category', true );

        // Check if category matches the required one (with support for arrays)
        if( is_array( $course_category ) )
            $return = (bool) ( in_array( $required_course_category, $course_category ) );
        else
            $return = (bool) ( $course_category === $required_course_category );
        
    }

    // If is category trigger, rules engine needs to check if field name and values matches required ones
    if( $return && ( $trigger === 'gamipress_tutor_complete_lesson_course_category' ) ) {

        $course_category = $args[3];
        $required_course_category = get_post_meta( $requirement_id, '_gamipress_tutor_category', true );

        // Check if category matches the required one (with support for arrays)
        if( is_array( $course_category ) )
            $return = (bool) ( in_array( $required_course_category, $course_category ) );
        else
            $return = (bool) ( $course_category === $required_course_category );
        
    }

    // If is category trigger, rules engine needs to check if field name and values matches required ones
    if( $return && ( $trigger === 'gamipress_tutor_complete_course_category'
            || $trigger === 'gamipress_tutor_enroll_course_category' ) ) {

        $course_category = $args[2];
        $required_course_category = get_post_meta( $requirement_id, '_gamipress_tutor_category', true );

        // Check if category matches the required one (with support for arrays)
        if( is_array( $course_category ) )
            $return = (bool) ( in_array( $required_course_category, $course_category ) );
        else
            $return = (bool) ( $course_category === $required_course_category );
        
    }

    // Send back our eligibility
    return $return;

}
add_filter( 'user_has_access_to_achievement', 'gamipress_tutor_user_has_access_to_achievement', 10, 6 );