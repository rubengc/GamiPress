<?php
/**
 * Ajax Functions
 *
 * @package GamiPress\WP_Courseware\Ajax_Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_wpcw_ajax_get_posts() {

    global $wpdb;

    if( isset( $_REQUEST['post_type'] ) ) {

        $override = false;
        $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

        // Check if we are looking for WP Courseware units
        // TODO: Deprecated, Units are now CPT posts with post_type as 'course_unit'
//        if( in_array( 'wpcw_units', $_REQUEST['post_type'] ) ) {
//
//            $override = true;
//
//            $units = $wpdb->get_results( $wpdb->prepare(
//                "SELECT unit_id, unit_title
//                  FROM {$wpdb->prefix}wpcw_units
//                  WHERE  1=1
//                   AND unit_title LIKE %s",
//                "%%{$search}%%"
//            ) );
//
//            $results = array();
//
//            foreach ( $units as $unit ) {
//
//                // Results should meet same structure like posts
//                $results[] = array(
//                    'ID' => $unit->unit_id,
//                    'post_title' => $unit->unit_title,
//                );
//
//            }
//
//        }

        // Check if we are looking for WP Courseware modules
        if( in_array( 'wpcw_modules', $_REQUEST['post_type'] ) ) {

            $override = true;

            $modules = $wpdb->get_results( $wpdb->prepare(
                "SELECT module_id, module_title
                  FROM {$wpdb->prefix}wpcw_modules
                  WHERE  1=1
                   AND module_title LIKE %s",
                "%%{$search}%%"
            ) );

            $results = array();

            foreach ( $modules as $module ) {

                // Results should meet same structure like posts
                $results[] = array(
                    'ID' => $module->module_id,
                    'post_title' => $module->module_title,
                );

            }

        }

        // Check if we are looking for WP Courseware courses
        if( in_array( 'wpcw_courses', $_REQUEST['post_type'] ) ) {

            $override = true;

            $courses = $wpdb->get_results( $wpdb->prepare(
                "SELECT course_id, course_title
                  FROM {$wpdb->prefix}wpcw_courses
                  WHERE  1=1
                   AND course_title LIKE %s",
                "%%{$search}%%"
            ) );

            $results = array();

            foreach ( $courses as $course ) {

                // Results should meet same structure like posts
                $results[] = array(
                    'ID' => $course->course_id,
                    'post_title' => $course->course_title,
                );

            }

        }

        if( $override ) {
            // Return our results
            wp_send_json_success( $results );
            die;
        }
    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_wpcw_ajax_get_posts', 5 );