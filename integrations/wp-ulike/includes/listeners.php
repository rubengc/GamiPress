<?php
/**
 * Listeners
 *
 * @package GamiPress\WP_Ulike\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Common WP Ulike listener (for like and unlike)
function gamipress_wp_ulike_common_listener( $id, $key, $user_id, $status ) {

    $author_id = 0;

    switch ( $key ) {
        case '_liked':
        case '_topicliked': // bbPress
            $author_id 	= get_post_field( 'post_author', $id );
            break;
        case '_commentliked':
            $comment = get_comment( $id );
            $author_id 	= $comment->user_id;
            break;
        case '_activityliked': // BuddyPress
            $author_id 	= gamipress_wp_ulike_get_bp_author_id( $id );
            break;
    }

    // Prevent authors like/unlike himself
    if ( absint( $user_id ) === absint( $author_id ) ) {
        return;
    }
    
    if( $status === 'like' ) {
        gamipress_wp_ulike_like( $id, $key, $user_id, $author_id );
    } else {
        gamipress_wp_ulike_unlike( $id, $key, $user_id, $author_id );
    }

}
add_action( 'wp_ulike_after_process', 'gamipress_wp_ulike_common_listener', 10, 4 );

function gamipress_wp_ulike_like( $id, $key, $user_id, $author_id ) {
    
    $post_type = gamipress_get_post_type( $id );

    // Trigger like anything
    do_action( 'gamipress_wp_ulike_like', $id, $user_id );

    switch ( $key ) {
        case '_liked':
            // Trigger post like
            do_action( 'gamipress_wp_ulike_post_like', $id, $user_id );
            do_action( 'gamipress_wp_ulike_post_type_like', $id, $user_id, $post_type );
            break;
        case '_commentliked':
            // Trigger comment like
            do_action( 'gamipress_wp_ulike_comment_like', $id, $user_id );
            break;
        case '_topicliked': // bbPress
            // Trigger bbPress topic like
            do_action( 'gamipress_wp_ulike_topic_like', $id, $user_id );
            break;
        case '_activityliked': // BuddyPress
            // Trigger BuddyPress activity like
            do_action( 'gamipress_wp_ulike_activity_like', $id, $user_id );
            break;
    }

    if( $author_id ) {

        // Trigger get a like anywhere (author is awarded one, user is the user that liked)
        do_action( 'gamipress_wp_ulike_get_like', $id, $author_id, $user_id );

        switch ( $key ) {
            case '_liked':
                // Trigger get a post like
                do_action( 'gamipress_wp_ulike_get_post_like', $id, $author_id, $user_id );
                do_action( 'gamipress_wp_ulike_get_post_type_like', $id, $author_id, $user_id, $post_type );
                break;
            case '_commentliked':
                // Trigger get a comment like
                do_action( 'gamipress_wp_ulike_get_comment_like', $id, $author_id, $user_id );
                break;
            case '_topicliked': // bbPress
                // Trigger get a bbPress topic like
                do_action( 'gamipress_wp_ulike_get_topic_like', $id, $author_id, $user_id );
                break;
            case '_activityliked': // BuddyPress
                // Trigger get a BuddyPress activity like
                do_action( 'gamipress_wp_ulike_get_activity_like', $id, $author_id, $user_id );
                break;
        }

    }

}

function gamipress_wp_ulike_unlike( $id, $key, $user_id, $author_id ) {

    // Trigger unlike anything
    do_action( 'gamipress_wp_ulike_unlike', $id, $user_id );

    switch ( $key ) {
        case '_liked':
            // Trigger post unlike
            do_action( 'gamipress_wp_ulike_post_unlike', $id, $user_id );
            break;
        case '_commentliked':
            // Trigger comment unlike
            do_action( 'gamipress_wp_ulike_comment_unlike', $id, $user_id );
            break;
        case '_topicliked': // bbPress
            // Trigger bbPress topic unlike
            do_action( 'gamipress_wp_ulike_topic_unlike', $id, $user_id );
            break;
        case '_activityliked': // BuddyPress
            // Trigger BuddyPress activity unlike
            do_action( 'gamipress_wp_ulike_activity_unlike', $id, $user_id );
            break;
    }

    if( $author_id ) {

        // Trigger get a unlike anywhere (author is awarded one, user is the user that unliked)
        do_action( 'gamipress_wp_ulike_get_unlike', $id, $author_id, $user_id );

        switch ( $key ) {
            case '_liked':
                // Trigger get a post unlike
                do_action( 'gamipress_wp_ulike_get_post_unlike', $id, $author_id, $user_id );
                break;
            case '_commentliked':
                // Trigger get a comment unlike
                do_action( 'gamipress_wp_ulike_get_comment_unlike', $id, $author_id, $user_id );
                break;
            case '_topicliked': // bbPress
                // Trigger get a bbPress topic unlike
                do_action( 'gamipress_wp_ulike_get_topic_unlike', $id, $author_id, $user_id );
                break;
            case '_activityliked': // BuddyPress
                // Trigger get a BuddyPress activity unlike
                do_action( 'gamipress_wp_ulike_get_activity_unlike', $id, $author_id, $user_id );
                break;
        }

    }

}

// Get activity author
function gamipress_wp_ulike_get_bp_author_id( $activity_id ) {

    $activity = bp_activity_get_specific( array( 'activity_ids' => $activity_id, 'display_comments'  => true ) );

    if( isset( $activity['activities'][0] ) ) {
        return $activity['activities'][0]->user_id;
    } else {
        return 0;
    }

}