<?php
/**
 * Functions
 *
 * @package GamiPress\LearnDash\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Retrieves post ID.
 *
 * @since  1.3.0
 *
 * @param mixed $thing
 *
 * @return int|false
 */
function gamipress_ld_get_post_id( $thing ) {

    if( $thing instanceof WP_Post ) {
        return absint( $thing->ID );
    }

    if( is_numeric( $thing ) ) {

        if( absint( $thing ) === 0 ) {
            return false;
        } else {
            return absint( $thing );
        }
    }

    return false;
}

/**
 * Retrieves post term ids for a taxonomy.
 *
 * @since  1.2.8
 *
 * @param  int    $post_id  Post ID.
 * @param  string $taxonomy Taxonomy slug.
 *
 * @return array
 */
function gamipress_ld_get_term_ids( $post_id, $taxonomy ) {

    $terms = get_the_terms( $post_id, $taxonomy );

    return ( empty( $terms ) || is_wp_error( $terms ) ) ? array() : wp_list_pluck( $terms, 'term_id' );

}

/**
 * Helper function to get the terms IDs index
 *
 * @since  1.2.8
 *
 * @param  string $trigger
 *
 * @return integer
 */
function gamipress_ld_get_terms_ids_index( $trigger ) {

    $index = -1;

    switch ( $trigger ) {
        // Course category
        case 'gamipress_ld_enroll_course_category':
        case 'gamipress_ld_complete_course_category':
            // Course tag
        case 'gamipress_ld_enroll_course_tag':
        case 'gamipress_ld_complete_course_tag':
            $index = 2;
            break;
        // Course category
        case 'gamipress_ld_complete_quiz_course_category':
        case 'gamipress_ld_pass_quiz_course_category':
        case 'gamipress_ld_fail_quiz_course_category':
        case 'gamipress_ld_complete_lesson_course_category':
        // Course tag
        case 'gamipress_ld_complete_quiz_course_tag':
        case 'gamipress_ld_pass_quiz_course_tag':
        case 'gamipress_ld_fail_quiz_course_tag':
        case 'gamipress_ld_complete_lesson_course_tag':
        // Lesson taxonomies
        case 'gamipress_ld_complete_lesson_category':
        case 'gamipress_ld_complete_lesson_tag':
            $index = 3;
            break;
        // Course category
        case 'gamipress_ld_complete_quiz_course_category_grade':
        case 'gamipress_ld_complete_quiz_course_category_max_grade':
        case 'gamipress_ld_complete_quiz_course_category_between_grade':
        case 'gamipress_ld_complete_topic_course_category':
        case 'gamipress_ld_assignment_upload_course_category':
        case 'gamipress_ld_approve_assignment_course_category':
        // Course tag
        case 'gamipress_ld_complete_quiz_course_tag_grade':
        case 'gamipress_ld_complete_quiz_course_tag_max_grade':
        case 'gamipress_ld_complete_quiz_course_tag_between_grade':
        case 'gamipress_ld_complete_topic_course_tag':
        case 'gamipress_ld_assignment_upload_course_tag':
        case 'gamipress_ld_approve_assignment_course_tag':
        // Topic taxonomies
        case 'gamipress_ld_complete_topic_category':
        case 'gamipress_ld_complete_topic_tag':
        // Lesson taxonomies
        case 'gamipress_ld_assignment_upload_lesson_category':
        case 'gamipress_ld_approve_assignment_lesson_category':
        case 'gamipress_ld_assignment_upload_lesson_tag':
        case 'gamipress_ld_approve_assignment_lesson_tag':
            $index = 4;
            break;
    }

    return $index;

}

/**
 * Helper function to get the terms IDs index
 *
 * @since  1.2.8
 *
 * @param  string $trigger
 *
 * @return string
 */
function gamipress_ld_get_term_element( $trigger ) {

    $element = '';

    switch ( $trigger ) {
        // Course category
        case 'gamipress_ld_complete_quiz_course_category':
        case 'gamipress_ld_complete_quiz_course_category_grade':
        case 'gamipress_ld_complete_quiz_course_category_max_grade':
        case 'gamipress_ld_complete_quiz_course_category_between_grade':
        case 'gamipress_ld_pass_quiz_course_category':
        case 'gamipress_ld_fail_quiz_course_category':
        case 'gamipress_ld_complete_topic_course_category':
        case 'gamipress_ld_assignment_upload_course_category':
        case 'gamipress_ld_approve_assignment_course_category':
        case 'gamipress_ld_complete_lesson_course_category':
        case 'gamipress_ld_enroll_course_category':
        case 'gamipress_ld_complete_course_category':
        // Course tag
        case 'gamipress_ld_complete_quiz_course_tag':
        case 'gamipress_ld_complete_quiz_course_tag_grade':
        case 'gamipress_ld_complete_quiz_course_tag_max_grade':
        case 'gamipress_ld_complete_quiz_course_tag_between_grade':
        case 'gamipress_ld_pass_quiz_course_tag':
        case 'gamipress_ld_fail_quiz_course_tag':
        case 'gamipress_ld_complete_topic_course_tag':
        case 'gamipress_ld_assignment_upload_course_tag':
        case 'gamipress_ld_approve_assignment_course_tag':
        case 'gamipress_ld_complete_lesson_course_tag':
        case 'gamipress_ld_enroll_course_tag':
        case 'gamipress_ld_complete_course_tag':
            $element = 'course';
            break;
        // Topic taxonomies
        case 'gamipress_ld_complete_topic_category':
        case 'gamipress_ld_complete_topic_tag':
            $element = 'topic';
            break;
        // Lesson taxonomies
        case 'gamipress_ld_assignment_upload_lesson_category':
        case 'gamipress_ld_approve_assignment_lesson_category':
        case 'gamipress_ld_complete_lesson_category':
        case 'gamipress_ld_assignment_upload_lesson_tag':
        case 'gamipress_ld_approve_assignment_lesson_tag':
        case 'gamipress_ld_complete_lesson_tag':
            $element = 'lesson';
            break;
    }

    return $element;

}

/**
 * Helper function to get the terms IDs index
 *
 * @since  1.2.8
 *
 * @param  string $trigger
 *
 * @return string
 */
function gamipress_ld_get_term_taxonomy( $trigger ) {

    $taxonomy = 'category';

    switch ( $trigger ) {
        // Course tag
        case 'gamipress_ld_complete_quiz_course_tag':
        case 'gamipress_ld_complete_quiz_course_tag_grade':
        case 'gamipress_ld_complete_quiz_course_tag_max_grade':
        case 'gamipress_ld_complete_quiz_course_tag_between_grade':
        case 'gamipress_ld_pass_quiz_course_tag':
        case 'gamipress_ld_fail_quiz_course_tag':
        case 'gamipress_ld_complete_topic_course_tag':
        case 'gamipress_ld_assignment_upload_course_tag':
        case 'gamipress_ld_approve_assignment_course_tag':
        case 'gamipress_ld_complete_lesson_course_tag':
        case 'gamipress_ld_enroll_course_tag':
        case 'gamipress_ld_complete_course_tag':
        // Topic tag
        case 'gamipress_ld_complete_topic_tag':
        // Lesson tag
        case 'gamipress_ld_assignment_upload_lesson_tag':
        case 'gamipress_ld_approve_assignment_lesson_tag':
        case 'gamipress_ld_complete_lesson_tag':
            $taxonomy = 'tag';
            break;
    }

    return $taxonomy;

}