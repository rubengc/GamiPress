<?php
/**
 * Functions
 *
 * @package GamiPress\Thrive_Apprentice\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_thrive_apprentice_ajax_get_posts() {

    global $wpdb;

    $results = array();

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    if( isset( $_REQUEST['post_type'] ) && in_array( 'tva_courses_posts', $_REQUEST['post_type'] ) ) {

        // Get the services
        $courses = get_terms(array(
            'taxonomy' => 'tva_courses',
            'hide_empty' => false,
        ));

        foreach ( $courses as $course ) {

            // Results should meet same structure like posts
            $results[] = array(
                'ID' => $course->term_id,
                'post_title' => $course->name,
            );

        }

        // Return our results
        wp_send_json_success( $results );
        die;

    }


}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_thrive_apprentice_ajax_get_posts', 5 );

// Get the service title
function gamipress_thrive_apprentice_get_course_title( $course_id ) {

    $course_id = absint( $course_id );

    if( $course_id === 0 ) return '';

    $course_title = get_term_by('id', $course_id, 'tva_courses');

    return $course_title->name;

}
