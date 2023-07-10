<?php
/**
 * Functions
 *
 * @package GamiPress\WPEP\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_wpep_ajax_get_posts() {

    global $wpdb;

    if( isset( $_REQUEST['post_type'] ) && in_array( 'wpep_lessons', $_REQUEST['post_type'] ) ) {

        // Get the user input
        $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';
        $table = $wpdb->prefix . ( defined( 'WPEP_DB_TABLE_COURSE_SECTION_LESSON' ) ? WPEP_DB_TABLE_COURSE_SECTION_LESSON : 'wpep_section_lesson' );

        // Try to find the lessons
        $lessons = $wpdb->get_results(
            "SELECT *
             FROM {$table}
             " . ( ! empty( $search ) ? "WHERE ( title LIKE '%{$search}%' OR  title LIKE '{$search}%' )" : '' ) . "
             ORDER BY `post_id` ASC, `order` ASC"
        );

        // Build the results array
        $results = array();

        foreach ( $lessons as $lesson ) {

            $section = WPEP\Entity\Course::instance()->get_section_by_lesson_id( $lesson->id );
            $course_title = gamipress_get_post_field( 'post_title', $lesson->post_id );

            // Results should meet same structure like posts
            $results[] = array(
                'ID' => $lesson->id,
                'post_title' => $lesson->title,
                'post_type' => $course_title . ' (' . $section->title . ')',
            );

        }

        // Return our results
        wp_send_json_success( $results );
        die;
    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_wpep_ajax_get_posts', 5 );