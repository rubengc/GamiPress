<?php
/**
 * Listeners
 *
 * @package GamiPress\H5P\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * New user result for complete any content
 *
 * @since  1.0.0
 *
 * @param object    &$data      Has the following properties score,max_score,opened,finished,time
 * @param int       $result_id  Only set if updating result
 * @param int       $content_id Identifier of the H5P Content
 * @param int       $user_id    Identifier of the User
 */
function gamipress_h5p_content_complete( $data, $result_id, $content_id, $user_id ) {

    global $wpdb;

    // Get the library content type
    $content_type = $wpdb->get_var( $wpdb->prepare(
        "SELECT l.name
        FROM {$wpdb->prefix}h5p_contents c
        JOIN {$wpdb->prefix}h5p_libraries l ON l.id = c.library_id
        WHERE c.id = %d",
        $content_id
    ) );

    // Trigger complete any content
    do_action( 'gamipress_h5p_complete_content', $result_id, $user_id, $content_id, $content_type, $data );

    // Trigger complete specific content
    do_action( 'gamipress_h5p_complete_specific_content', $result_id, $user_id, $content_id, $content_type, $data );

    // Trigger complete specific content of type
    do_action( 'gamipress_h5p_complete_specific_content_type', $result_id, $user_id, $content_id, $content_type, $data );

    // At 100% score events
    if( isset( $data['score'] ) && isset( $data['max_score'] ) && $data['score'] >= $data['max_score'] ) {

        // Trigger complete any content at maximum score
        do_action( 'gamipress_h5p_max_complete_content', $result_id, $user_id, $content_id, $content_type, $data );

        // Trigger complete specific content at maximum score
        do_action( 'gamipress_h5p_max_complete_specific_content', $result_id, $user_id, $content_id, $content_type, $data );

        // Trigger complete specific content of type at maximum score
        do_action( 'gamipress_h5p_max_complete_specific_content_type', $result_id, $user_id, $content_id, $content_type, $data );

    }

    // Score events
    if( isset( $data['score'] ) ) {

        // Minimum score events

        // Trigger complete any content with a minimum score
        do_action( 'gamipress_h5p_complete_content_min_score', $result_id, $user_id, $content_id, $content_type, $data['score'], $data );

        // Trigger complete specific content with a minimum score
        do_action( 'gamipress_h5p_complete_specific_content_min_score', $result_id, $user_id, $content_id, $content_type, $data['score'], $data );

        // Trigger complete specific content of type with a minimum score
        do_action( 'gamipress_h5p_complete_specific_content_type_min_score', $result_id, $user_id, $content_id, $content_type, $data['score'], $data );

        // Maximum score events

        // Trigger complete any content with a maximum score
        do_action( 'gamipress_h5p_complete_content_max_score', $result_id, $user_id, $content_id, $content_type, $data['score'], $data );

        // Trigger complete specific content with a maximum score
        do_action( 'gamipress_h5p_complete_specific_content_max_score', $result_id, $user_id, $content_id, $content_type, $data['score'], $data );

        // Trigger complete specific content of type with a maximum score
        do_action( 'gamipress_h5p_complete_specific_content_type_max_score', $result_id, $user_id, $content_id, $content_type, $data['score'], $data );

        // Between score events

        // Trigger complete any content on a range of scores
        do_action( 'gamipress_h5p_complete_content_between_score', $result_id, $user_id, $content_id, $content_type, $data['score'], $data );

        // Trigger complete specific content on a range of scores
        do_action( 'gamipress_h5p_complete_specific_content_between_score', $result_id, $user_id, $content_id, $content_type, $data['score'], $data );

        // Trigger complete specific content of type on a range of scores
        do_action( 'gamipress_h5p_complete_specific_content_type_between_score', $result_id, $user_id, $content_id, $content_type, $data['score'], $data );

        // Minimum percentage score events

        // Trigger complete any content with a minimum percentage
        do_action( 'gamipress_h5p_complete_content_min_percentage', $result_id, $user_id, $content_id, $content_type, $data['score'], $data['max_score'], $data );

        // Trigger complete specific content with a minimum percentage
        do_action( 'gamipress_h5p_complete_specific_content_min_percentage', $result_id, $user_id, $content_id, $content_type, $data['score'], $data['max_score'], $data );

        // Trigger complete specific content of type with a minimum percentage
        do_action( 'gamipress_h5p_complete_specific_content_type_min_percentage', $result_id, $user_id, $content_id, $content_type, $data['score'], $data['max_score'], $data );

        // Maximum percentage score events

        // Trigger complete any content with a maximum percentage
        do_action( 'gamipress_h5p_complete_content_max_percentage', $result_id, $user_id, $content_id, $content_type, $data['score'], $data['max_score'], $data );

        // Trigger complete specific content with a maximum percentage
        do_action( 'gamipress_h5p_complete_specific_content_max_percentage', $result_id, $user_id, $content_id, $content_type, $data['score'], $data['max_score'], $data );
        
        // Trigger complete specific content of type with a maximum percentage
        do_action( 'gamipress_h5p_complete_specific_content_type_max_percentage', $result_id, $user_id, $content_id, $content_type, $data['score'], $data['max_score'], $data );

        // Between percentages score events

        // Trigger complete any content on a range of percentages
        do_action( 'gamipress_h5p_complete_content_between_percentage', $result_id, $user_id, $content_id, $content_type, $data['score'], $data['max_score'], $data );

        // Trigger complete specific content on a range of percentages
        do_action( 'gamipress_h5p_complete_specific_content_between_percentage', $result_id, $user_id, $content_id, $content_type, $data['score'], $data['max_score'], $data );

        // Trigger complete specific content of type on a range of percentages
        do_action( 'gamipress_h5p_complete_specific_content_type_between_percentage', $result_id, $user_id, $content_id, $content_type, $data['score'], $data['max_score'], $data );
        
    }

}
add_action( 'h5p_alter_user_result', 'gamipress_h5p_content_complete', 10, 4 );
