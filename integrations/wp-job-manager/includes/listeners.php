<?php
/**
 * Listeners
 *
 * @package GamiPress\WP_Job_Manager\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Publish job listener
 *
 * @since 1.0.0
 *
 * @param int       $post_id
 * @param int       $post_author
 * @param WP_Post   $post
 */
function gamipress_wp_job_manager_publish_job_listener( $post_id, $post_author, $post ) {

    $term_ids = gamipress_wp_job_manager_get_post_term_ids( $post_id, 'job_listing_type' );

    foreach( $term_ids as $term_id ) {
        // Trigger publish job of specific type
        do_action( 'gamipress_wp_job_manager_publish_job_specific_type', $post_id, $post_author, $term_id, $post );
    }

}
add_action( 'gamipress_publish_job_listing', 'gamipress_wp_job_manager_publish_job_listener', 10, 3 );

/**
 * Mark as filled listener
 *
 * @since 1.0.0
 *
 * @param string    $action
 * @param int       $post_id
 */
function gamipress_wp_job_manager_dashboard_listener( $action, $post_id ) {

    $post = get_post( $post_id );
    $post_author = $post->post_author;

    switch ( $action ) {
        case 'mark_filled':
            // Trigger mark job as filled
            do_action( 'gamipress_wp_job_manager_mark_filled', $post_id, $post_author );

            $term_ids = gamipress_wp_job_manager_get_post_term_ids( $post_id, 'job_listing_type' );

            foreach( $term_ids as $term_id ) {
                // Trigger mark job of specific type as filled
                do_action( 'gamipress_wp_job_manager_mark_filled_specific_type', $post_id, $post_author, $term_id, $post );
            }
            break;
        case 'mark_not_filled':
            // Trigger mark job as not filled
            do_action( 'gamipress_wp_job_manager_mark_not_filled', $post_id, $post_author );

            $term_ids = gamipress_wp_job_manager_get_post_term_ids( $post_id, 'job_listing_type' );

            foreach( $term_ids as $term_id ) {
                // Trigger mark job of specific type as not filled
                do_action( 'gamipress_wp_job_manager_mark_not_filled_specific_type', $post_id, $post_author, $term_id, $post );
            }
            break;
    }
}
add_action( 'job_manager_my_job_do_action', 'gamipress_wp_job_manager_dashboard_listener', 10, 2 );

/**
 * Apply job listener
 *
 * @since 1.0.0
 *
 * @param int       $application_id
 * @param int       $job_id
 */
function gamipress_wp_job_manager_job_application_listener( $application_id, $job_id ) {

    $user_id = get_current_user_id();

    if( $user_id === 0 ) {
        return;
    }

    $application = get_post( $application_id );

    if( ! $application ) {
        return;
    }

    $job = get_post( $job_id );

    if( ! $job ) {
        return;
    }

    $author_id = $job->post_author;
    $term_ids = gamipress_wp_job_manager_get_post_term_ids( $job->ID, 'job_listing_type' );

    // Trigger apply to job
    do_action( 'gamipress_wp_job_manager_job_application', $job->ID, $user_id, $application->ID, $job );

    foreach( $term_ids as $term_id ) {
        // Trigger apply to job of specific type
        do_action( 'gamipress_wp_job_manager_job_application_specific_type', $job->ID, $user_id, $term_id, $application->ID, $job );
    }

    // Trigger receive application on a job
    do_action( 'gamipress_wp_job_manager_get_job_application', $job->ID, $author_id, $application->ID, $user_id, $job );

    foreach( $term_ids as $term_id ) {
        // Trigger receive application on a job of specific type
        do_action( 'gamipress_wp_job_manager_get_job_application_specific_type', $job->ID, $author_id, $term_id, $application->ID, $user_id, $job );
    }

}
add_action( 'new_job_application', 'gamipress_wp_job_manager_job_application_listener', 10, 2 );

/**
 * Job application status listener
 *
 * @since 1.0.0
 *
 * @param string  $new_status New post status.
 * @param string  $old_status Old post status.
 * @param WP_Post $post       Post object.
 */
function gamipress_wp_job_manager_job_application_status_listener( $new_status, $old_status, $post ) {

    // Bail if not is a job application
    if( $post->post_type !== 'job_application' ) {
        return;
    }

    // Bail if not status change
    if( $new_status === $old_status ) {
        return;
    }

    $job_id = $post->post_parent;
    $application_id = $post->ID;
    $user_id = get_post_meta( $application_id, '_candidate_user_id', true );

    // Trigger job application hired or rejected
    do_action( "gamipress_wp_job_manager_job_application_{$new_status}", $job_id, $user_id, $application_id );

    $term_ids = gamipress_wp_job_manager_get_post_term_ids( $job_id, 'job_listing_type' );

    foreach( $term_ids as $term_id ) {
        // Trigger receive application on a job of specific type
        do_action( "gamipress_wp_job_manager_job_application_{$new_status}_specific_type", $job_id, $user_id, $term_id, $application_id, $post );
    }

}
add_action( 'transition_post_status', 'gamipress_wp_job_manager_job_application_status_listener', 10, 3 );
