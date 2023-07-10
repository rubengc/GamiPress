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
 * Retrieves post term ids for a taxonomy.
 *
 * @since  1.0.6
 *
 * @param  int    $post_id  Post ID.
 *
 * @return array
 */
function gamipress_tutor_get_term_ids( $course_id ) {

    $terms = get_the_terms( $course_id, 'course-category' );

    return ( empty( $terms ) || is_wp_error( $terms ) ) ? array() : wp_list_pluck( $terms, 'term_id' );

}