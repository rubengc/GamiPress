<?php
/**
 * Triggers
 *
 * @package GamiPress\Youtube\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin specific triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_youtube_activity_triggers( $triggers ) {

    $triggers[__( 'Youtube', 'gamipress' )] = array(
        'gamipress_youtube_watch_video'                    => __( 'Watch any video', 'gamipress' ),
        'gamipress_youtube_watch_specific_video'           => __( 'Watch a specific video', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_youtube_activity_triggers' );

/**
 * Get user for a given trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $user_id user ID to override.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 * @return integer          User ID.
 */
function gamipress_youtube_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_youtube_watch_video':
        case 'gamipress_youtube_watch_specific_video':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_youtube_trigger_get_user_id', 10, 3 );

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.0
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_youtube_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_youtube_watch_video':
        case 'gamipress_youtube_watch_specific_video':
            // Add the video ID, seconds, duration and the post ID watched
            $log_meta['video_id'] = $args[0];
            $log_meta['seconds'] = $args[2];
            $log_meta['duration'] = $args[3];
            $log_meta['post_id'] = $args[4];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_youtube_log_event_trigger_meta_data', 10, 5 );

/**
 * Extra data fields
 *
 * @since 1.0.0
 *
 * @param array     $fields
 * @param int       $log_id
 * @param string    $type
 *
 * @return array
 */
function gamipress_youtube_log_extra_data_fields( $fields, $log_id, $type ) {

    $prefix = '_gamipress_';

    // TODO: To remove, added just to backward compatibility
    if( ! is_gamipress_upgraded_to( '1.4.7' ) ) {
        $trigger = ct_get_object_meta( $log_id, '_gamipress_trigger_type', true );
    } else {
        $log = ct_get_object( $log_id );
        $trigger = $log->trigger_type;
    }

    if( $type !== 'event_trigger' ) {
        return $fields;
    }

    switch( $trigger ) {
        case 'gamipress_youtube_watch_video':
        case 'gamipress_youtube_watch_specific_video':

            $video_id = ct_get_object_meta( $log_id, $prefix . 'video_id', true );

            $url = 'https://www.youtube.com/embed/' . $video_id;

            $fields[] = array(
                'name' 	            => __( 'Video', 'gamipress' ),
                'desc' 	            => __( 'Video watched by the user.', 'gamipress' ),
                'id'   	            => $prefix . 'video_id',
                'type' 	            => 'html',
                'content'           => '<iframe id="youtube-player" type="text/html" src="' . $url . '" width="640" height="360" frameborder="0"></iframe>'
            );
            break;
    }

    return $fields;

}
add_filter( 'gamipress_log_extra_data_fields', 'gamipress_youtube_log_extra_data_fields', 10, 3 );

/**
 * Override the meta data to filter the logs count
 *
 * @since   1.0.4
 *
 * @param  array    $log_meta       The meta data to filter the logs count
 * @param  int      $user_id        The given user's ID
 * @param  string   $trigger        The given trigger we're checking
 * @param  int      $since 	        The since timestamp where retrieve the logs
 * @param  int      $site_id        The desired Site ID to check
 * @param  array    $args           The triggered args or requirement object
 *
 * @return array                    The meta data to filter the logs count
 */
function gamipress_youtube_get_user_trigger_count_log_meta( $log_meta, $user_id, $trigger, $since, $site_id, $args ) {

    switch( $trigger ) {
        case 'gamipress_youtube_watch_specific_video':
            // Add the video ID
            if( isset( $args[0] ))
                $log_meta['video_id'] = $args[0];

            // $args could be a requirement object
            if( isset( $args['youtube_video_id'] ) )
                $log_meta['video_id'] = $args['youtube_video_id'];
            break;
    }

    return $log_meta;

}
add_filter( 'gamipress_get_user_trigger_count_log_meta', 'gamipress_youtube_get_user_trigger_count_log_meta', 10, 6 );