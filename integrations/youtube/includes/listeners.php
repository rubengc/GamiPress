<?php
/**
 * Listeners
 *
 * @package GamiPress\Youtube\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Watch Youtube video listener
function gamipress_youtube_track_watch_video_listener() {

    $events_triggered   = array();
    $user_id            = ( isset( $_REQUEST['user_id'] ) ? $_REQUEST['user_id'] : get_current_user_id() );
    $video_id           = ( isset( $_REQUEST['video_id'] ) ? $_REQUEST['video_id'] : '' );
    $seconds            = ( isset( $_REQUEST['seconds'] ) ? $_REQUEST['seconds'] : '' );
    $duration           = ( isset( $_REQUEST['duration'] ) ? $_REQUEST['duration'] : '' );
    $post_id            = ( isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : '' );

    if( absint( $user_id ) !== 0 && ! empty( $video_id ) ) {

        // Trigger user watch Youtube video
        do_action( 'gamipress_youtube_watch_video', $video_id, $user_id, $seconds, $duration, $post_id );

        $events_triggered['gamipress_youtube_watch_video'] = array( $video_id, $user_id, $seconds, $duration, $post_id );

        // Trigger user watch specific Youtube video
        do_action( 'gamipress_youtube_watch_specific_video', $video_id, $user_id, $seconds, $duration, $post_id );

        $events_triggered['gamipress_youtube_watch_specific_video'] = array( $video_id, $user_id, $seconds, $duration, $post_id );

    }

    // Return an array of events triggered
    wp_send_json_success( $events_triggered );
    exit;

}
add_action( 'wp_ajax_gamipress_youtube_track_watch_video', 'gamipress_youtube_track_watch_video_listener' );
