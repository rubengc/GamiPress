<?php
/**
 * Listeners
 *
 * @package GamiPress\CoursePress\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Pass module listener
/**
 * Complete module listener
 *
 * @since 1.0.0
 *
 * @param int       $student_id
 * @param int       $module_id
 * @param string    $module_title
 * @param int       $unit_id
 * @param int       $course_id
 */
function gamipress_coursepress_complete_module( $student_id, $module_id, $module_title, $unit_id, $course_id ) {

    // Complete any module
    do_action( 'gamipress_coursepress_complete_unit', $module_id, $student_id, $unit_id, $course_id );

    // Complete a specific module
    do_action( 'gamipress_coursepress_complete_specific_unit', $module_id, $student_id, $unit_id, $course_id );

    // Complete any module of a specific unit
    do_action( 'gamipress_coursepress_complete_unit_specific_unit', $module_id, $student_id, $unit_id, $course_id );

    // Complete any module of a specific course
    do_action( 'gamipress_coursepress_complete_unit_specific_course', $module_id, $student_id, $unit_id, $course_id );

}
add_action( 'coursepress_student_module_passed', 'gamipress_coursepress_complete_module', 10, 5 );

/**
 * Complete unit listener
 *
 * @since 1.0.0
 *
 * @param int       $student_id
 * @param int       $unit_id
 * @param string    $unit_title
 * @param int       $course_id
 */
function gamipress_coursepress_complete_unit( $student_id, $unit_id, $unit_title, $course_id ) {

    // Complete any unit
    do_action( 'gamipress_coursepress_complete_unit', $unit_id, $student_id, $course_id );

    // Complete a specific unit
    do_action( 'gamipress_coursepress_complete_specific_unit', $unit_id, $student_id, $course_id );

    // Complete any unit of a specific course
    do_action( 'gamipress_coursepress_complete_unit_specific_course', $unit_id, $student_id, $course_id );

}
add_action( 'coursepress_student_unit_completed', 'gamipress_coursepress_complete_unit', 10, 4 );

/**
 * Complete course listener
 *
 * @since 1.0.0
 *
 * @param int       $student_id
 * @param int       $course_id
 * @param string    $course_title
 */
function gamipress_coursepress_complete_course( $student_id, $course_id, $course_title ) {

    // Complete any course
    do_action( 'gamipress_coursepress_complete_course', $course_id, $student_id );

    // Complete a specific course
    do_action( 'gamipress_coursepress_complete_specific_course', $course_id, $student_id );

}

add_action( 'coursepress_student_course_completed', 'gamipress_coursepress_complete_course', 10, 3 );