<?php
/**
 * Functions
 *
 * @package GamiPress\Thrive_Quiz_Builder\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_thrive_quiz_builder_ajax_get_posts() {

    global $wpdb;

    $results = array();

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    if( isset( $_REQUEST['post_type'] ) && in_array( 'percentage_quizzes', $_REQUEST['post_type'] ) ) {

        // Get the quizzes
        $quizzes = gamipress_thrive_quiz_builder_get_percentage_quizzes();

        foreach ( $quizzes as $quiz ) {

            // Results should meet same structure like posts
            $results[] = array(
                'ID' => $quiz->ID,
                'post_title' => $quiz->post_title,
            );

        }

        // Return our results
        wp_send_json_success( $results );
        die;

    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_thrive_quiz_builder_ajax_get_posts', 5 );

/**
 * Get percentage conditions
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_thrive_quiz_builder_get_percentage_conditions() {

    return array(
        'equal'             => __( 'equal to', 'gamipress'),
        'not_equal'         => __( 'not equal to', 'gamipress'),
        'less_than'         => __( 'less than', 'gamipress' ),
        'greater_than'      => __( 'greater than', 'gamipress' ),
        'less_or_equal'     => __( 'less or equal to', 'gamipress' ),
        'greater_or_equal'  => __( 'greater or equal to', 'gamipress' ),
    );

}

/**
 * Get predefined quiz types
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_thrive_quiz_builder_get_quiz_types() {

    return array(
        ''              => __( 'Choose a quiz type', 'gamipress' ),
        'number'        => __( 'Number', 'gamipress' ),
        'percentage'    => __( 'Percentage', 'gamipress' ),
        'personality'   => __( 'Personality', 'gamipress' ),
        'right_wrong'   => __( 'Right/Wrong', 'gamipress' ),
        'survey'        => __( 'Survey', 'gamipress' )
    );

}

/**
 * Get quizzes with percentage type
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_thrive_quiz_builder_get_percentage_quizzes( ) {

    $quizzes = get_posts( array(
        'numberposts' => -1,
        'post_type' => 'tqb_quiz',
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'tqb_quiz_type',
                'value' => 'percentage',
                'compare' => '=',
            ),
            array(
                'key' => 'tqb_quiz_type',
                'value' => 's:10:"percentage";', 
                'compare' => 'LIKE',
            ),
        )
    ) );

    return $quizzes;
}

/**
 * Get the percentage quiz title
 *
 * @since 1.0.0
 *
 * @param int $quiz_id   Quiz ID
 */
function gamipress_thrive_quiz_builder_get_percentage_quiz_title( $quiz_id ){

    $percentage_quiz = get_post( $quiz_id );

    return ( $percentage_quiz ? $percentage_quiz->post_title : '' );

}