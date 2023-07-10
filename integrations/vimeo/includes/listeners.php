<?php
/**
 * Listeners
 *
 * @package GamiPress\Vimeo\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Watch Vimeo video listener
function gamipress_vimeo_track_watch_video_listener() {

    $events_triggered   = array();
    $user_id            = ( isset( $_REQUEST['user_id'] ) ? $_REQUEST['user_id'] : get_current_user_id() );
    $video_id           = ( isset( $_REQUEST['video_id'] ) ? $_REQUEST['video_id'] : '' );
    $seconds            = ( isset( $_REQUEST['seconds'] ) ? $_REQUEST['seconds'] : '' );
    $duration           = ( isset( $_REQUEST['duration'] ) ? $_REQUEST['duration'] : '' );
    $post_id            = ( isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : '' );

    if( absint( $user_id ) !== 0 && ! empty( $video_id ) ) {

        // Trigger user watch Vimeo video
        do_action( 'gamipress_vimeo_watch_video', $video_id, $user_id, $seconds, $duration, $post_id );

        $events_triggered['gamipress_vimeo_watch_video'] = array( $video_id, $user_id, $seconds, $duration, $post_id );

        // Trigger user watch specific Vimeo video
        do_action( 'gamipress_vimeo_watch_specific_video', $video_id, $user_id, $seconds, $duration, $post_id );

        $events_triggered['gamipress_vimeo_watch_specific_video'] = array( $video_id, $user_id, $seconds, $duration, $post_id );

    }

    // Return an array of events triggered
    wp_send_json_success( $events_triggered );
    exit;

}
add_action( 'wp_ajax_gamipress_vimeo_track_watch_video', 'gamipress_vimeo_track_watch_video_listener' );
