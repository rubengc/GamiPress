<?php
/**
 * Listeners
 *
 * @package GamiPress\WP_Courseware\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Complete unit
 *
 * @since 1.0.0
 *
 * @param integer $user_id
 * @param integer $unit_id
 * @param object $parent stdClass{
 *      parent_module_id => integer,
 *      parent_course_id => integer
 * }
 */
function gamipress_wpcw_complete_unit( $user_id, $unit_id, $parent ) {

    $module_id = $parent->parent_module_id;
    $course_id = $parent->parent_course_id;

    // Complete any unit
    do_action( 'gamipress_wpcw_complete_unit', $unit_id, $user_id, $module_id, $course_id );

    // Complete specific unit
    do_action( 'gamipress_wpcw_complete_specific_unit', $unit_id, $user_id, $module_id, $course_id );

}
add_action( 'wpcw_user_completed_unit', 'gamipress_wpcw_complete_unit', 10, 3 );

/**
 * Complete module
 *
 * @since 1.0.0
 *
 * @param integer $user_id
 * @param integer $module_id
 * @param object $parent stdClass // TODO: Need to check in docs
 */
function gamipress_wpcw_complete_module( $user_id, $module_id, $parent ) {

    // Complete any module
    do_action( 'gamipress_wpcw_complete_module', $module_id, $user_id );

    // Complete specific module
    do_action( 'gamipress_wpcw_complete_specific_module', $module_id, $user_id );

}
add_action( 'wpcw_user_completed_module', 'gamipress_wpcw_complete_module', 10, 3 );

/**
 * Complete course
 *
 * @since 1.0.0
 *
 * @param integer $user_id
 * @param integer $unit_id
 * @param object $parent stdClass // TODO: Need to check in docs
 */
function gamipress_wpcw_complete_course( $user_id, $unit_id, $parent ) {

    $course_id = $parent->course_post_id;

    // Complete any course
    do_action( 'gamipress_wpcw_complete_course', $course_id, $user_id );

    // Complete specific course
    do_action( 'gamipress_wpcw_complete_specific_course', $course_id, $user_id );

}
add_action( 'wpcw_user_completed_course', 'gamipress_wpcw_complete_course', 10, 3 );
