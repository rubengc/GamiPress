<?php
/**
 * Listeners
 *
 * @package GamiPress\Thrive_Apprentice\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Complete lesson listener
 *
 * @since 1.0.0
 *
 * @param array $lesson_details   Lesson data
 * @param array $user_details     User data
 */
function gamipress_thrive_apprentice_complete_lesson( $lesson_details, $user_details ) {

    $user_id = get_current_user_id();

    // Complete any lesson
    do_action( 'gamipress_thrive_apprentice_complete_lesson', absint( $lesson_details['lesson_id'] ), $user_id );

    // Complete specific lesson
    do_action( 'gamipress_thrive_apprentice_complete_specific_lesson', absint( $lesson_details['lesson_id'] ), $user_id );

}
add_action( 'thrive_apprentice_lesson_complete', 'gamipress_thrive_apprentice_complete_lesson', 10, 2 );

/**
 * Complete module listener
 *
 * @since 1.0.0
 *
 * @param array $module_details   Module data
 * @param array $user_details     User data
 */
function gamipress_thrive_apprentice_complete_module( $module_details, $user_details ) {

    $user_id = get_current_user_id();

    // Complete any module
    do_action( 'gamipress_thrive_apprentice_complete_module', absint( $module_details['module_id'] ), $user_id );

    // Complete specific module
    do_action( 'gamipress_thrive_apprentice_complete_specific_module', absint( $module_details['module_id'] ), $user_id );

}
add_action( 'thrive_apprentice_module_finish', 'gamipress_thrive_apprentice_complete_module', 10, 2 );

/**
 * Complete course listener
 *
 * @since 1.0.0
 *
 * @param array $course_details   Course data
 * @param array $user_details     User data
 */
function gamipress_thrive_apprentice_complete_course( $course_details, $user_details ) {

    $user_id = get_current_user_id();

    // Complete any course
    do_action( 'gamipress_thrive_apprentice_complete_course', absint( $course_details['course_id'] ), $user_id );

    // Complete specific course
    do_action( 'gamipress_thrive_apprentice_complete_specific_course', absint( $course_details['course_id'] ), $user_id );

}
add_action( 'thrive_apprentice_course_finish', 'gamipress_thrive_apprentice_complete_course', 10, 2 );

