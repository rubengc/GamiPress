<?php
/**
 * Triggers
 *
 * @package GamiPress\Presto_Player\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin specific triggers
 *
 * @since 1.0.0
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_presto_player_activity_triggers( $triggers ) {

    $triggers[__( 'Presto Player', 'gamipress' )] = array(
        'gamipress_presto_player_watch_video'                           => __( 'Watch any video', 'gamipress' ),
        'gamipress_presto_player_watch_specific_video'                  => __( 'Watch a specific video', 'gamipress' ),
        'gamipress_presto_player_watch_video_min_percent'               => __( 'Watch a minimum percent of any video', 'gamipress' ),
        'gamipress_presto_player_watch_specific_video_min_percent'      => __( 'Watch a minimum percent of a specific video', 'gamipress' ),
        'gamipress_presto_player_watch_video_between_percent'           => __( 'Watch a percent range of any video', 'gamipress' ),
        'gamipress_presto_player_watch_specific_video_between_percent'  => __( 'Watch a percent range of a specific video', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_presto_player_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_presto_player_specific_activity_triggers( $specific_activity_triggers ) {

    $specific_activity_triggers['gamipress_presto_player_watch_specific_video'] = array( 'presto_player_video' );
    $specific_activity_triggers['gamipress_presto_player_watch_specific_video_min_percent'] = array( 'presto_player_video' );
    $specific_activity_triggers['gamipress_presto_player_watch_specific_video_between_percent'] = array( 'presto_player_video' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_presto_player_specific_activity_triggers' );

/**
 * Build custom activity trigger label
 *
 * @since  1.0.0
 *
 * @param string    $title
 * @param integer   $requirement_id
 * @param array     $requirement
 *
 * @return string
 */
function gamipress_presto_player_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $percent = ( isset( $requirement['presto_player_percent'] ) ) ? absint( $requirement['presto_player_percent'] ) : 0;
    $min_percent = ( isset( $requirement['presto_player_min_percent'] ) ) ? absint( $requirement['presto_player_min_percent'] ) : 0;
    $max_percent = ( isset( $requirement['presto_player_max_percent'] ) ) ? absint( $requirement['presto_player_max_percent'] ) : 0;

    switch( $requirement['trigger_type'] ) {
        // Specific percent
        case 'gamipress_presto_player_watch_video_min_percent':
            return sprintf( __( 'Watch a %s of any video', 'gamipress' ), $percent . '%' );
            break;
        // Specific percent on a specific video
        case 'gamipress_presto_player_watch_specific_video_min_percent':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            $title = gamipress_get_specific_activity_trigger_post_title( $achievement_post_id, $requirement['trigger_type'], $requirement['achievement_post_site_id'] );
            return sprintf( __( 'Watch a %s of %s video', 'gamipress' ), $percent . '%', $title );
            break;
        // Between percent
        case 'gamipress_presto_player_watch_video_between_percent':
            return sprintf( __( 'Watch a percent between %s and %s of any video', 'gamipress' ), $min_percent . '%', $max_percent . '%' );
            break;
        // Between percent on a specific video
        case 'gamipress_presto_player_watch_specific_video_between_percent':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            $title = gamipress_get_specific_activity_trigger_post_title( $achievement_post_id, $requirement['trigger_type'], $requirement['achievement_post_site_id'] );
            return sprintf( __( 'Watch a percent between %s and %s of %s video', 'gamipress' ), $min_percent . '%', $max_percent . '%', $title );
            break;
    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_presto_player_activity_trigger_label', 10, 3 );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_presto_player_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_presto_player_watch_specific_video'] = __( 'Watch %s video', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_presto_player_watch_specific_video_min_percent'] = __( 'Watch a minimum percent of %s video', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_presto_player_watch_specific_video_between_percent'] = __( 'Watch a range percent of %s video', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_presto_player_specific_activity_trigger_label' );

/**
 * Get plugin specific activity trigger post title
 *
 * @since  1.0.6
 *
 * @param  string   $post_title
 * @param  integer  $specific_id
 * @param  string   $trigger_type
 * @param  string   $site_id
 * @return string
 */
function gamipress_presto_player_specific_activity_trigger_post_title( $post_title, $specific_id, $trigger_type, $site_id ) {

    global $wpdb;

    switch( $trigger_type ) {
        case 'gamipress_presto_player_watch_specific_video':
        case 'gamipress_presto_player_watch_specific_video_min_percent':
        case 'gamipress_presto_player_watch_specific_video_between_percent':
            if( absint( $specific_id ) !== 0 ) {

                if( gamipress_is_network_wide_active() && $site_id !== get_current_blog_id() ) {
                    // Switch to site
                    switch_to_blog( $site_id );

                    $post_title = $wpdb->get_var( $wpdb->prepare(
                        "SELECT v.title FROM {$wpdb->prefix}presto_player_videos AS v WHERE v.id = %s",
                        "$specific_id"
                    ) );

                    // Restore current site
                    restore_current_blog();
                } else {
                    $post_title = $wpdb->get_var( $wpdb->prepare(
                        "SELECT v.title FROM {$wpdb->prefix}presto_player_videos AS v WHERE v.id = %s",
                        "$specific_id"
                    ) );
                }


            }
            break;
    }

    return $post_title;

}
add_filter( 'gamipress_specific_activity_trigger_post_title', 'gamipress_presto_player_specific_activity_trigger_post_title', 10, 4 );

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
function gamipress_presto_player_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_presto_player_watch_video':
        case 'gamipress_presto_player_watch_specific_video':
        case 'gamipress_presto_player_watch_video_min_percent':
        case 'gamipress_presto_player_watch_specific_video_min_percent':
        case 'gamipress_presto_player_watch_video_between_percent':
        case 'gamipress_presto_player_watch_specific_video_between_percent':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_presto_player_trigger_get_user_id', 10, 3);


/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer  $specific_id Specific ID.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          Specific ID.
 */
function gamipress_presto_player_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_presto_player_watch_specific_video':
        case 'gamipress_presto_player_watch_specific_video_min_percent':
        case 'gamipress_presto_player_watch_specific_video_between_percent':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_presto_player_specific_trigger_get_id', 10, 3 );

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
function gamipress_presto_player_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_presto_player_watch_video':
        case 'gamipress_presto_player_watch_specific_video':
        case 'gamipress_presto_player_watch_video_min_percent':
        case 'gamipress_presto_player_watch_specific_video_min_percent':
        case 'gamipress_presto_player_watch_video_between_percent':
        case 'gamipress_presto_player_watch_specific_video_between_percent':
            // Add the video ID and percent
            $log_meta['video_id'] = $args[0];
            $log_meta['percent'] = $args[2];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_presto_player_log_event_trigger_meta_data', 10, 5 );

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
function gamipress_presto_player_log_extra_data_fields( $fields, $log_id, $type ) {

    $prefix = '_gamipress_';

    $log = ct_get_object( $log_id );
    $trigger = $log->trigger_type;

    if( $type !== 'event_trigger' ) {
        return $fields;
    }

    switch( $trigger ) {
        case 'gamipress_presto_player_watch_video_min_percent':
        case 'gamipress_presto_player_watch_specific_video_min_percent':
        case 'gamipress_presto_player_watch_video_between_percent':
        case 'gamipress_presto_player_watch_specific_video_between_percent':
            $fields[] = array(
                'name' 	            => __( 'Percent watched', 'gamipress' ),
                'desc' 	            => __( 'Percent watched on this video by the user.', 'gamipress' ),
                'id'   	            => $prefix . 'percent',
                'type' 	            => 'text',
            );
            break;
    }

    return $fields;

}
add_filter( 'gamipress_log_extra_data_fields', 'gamipress_presto_player_log_extra_data_fields', 10, 3 );

/**
 * Override the meta data to filter the logs count
 *
 * @since   1.0.0
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
function gamipress_presto_player_get_user_trigger_count_log_meta( $log_meta, $user_id, $trigger, $since, $site_id, $args ) {

    switch( $trigger ) {
        case 'gamipress_presto_player_watch_video_min_percent':
        case 'gamipress_presto_player_watch_specific_video_min_percent':
            $percent = 0;

            if( isset( $args[2] ) ) {
                $percent = $args[2];
            }

            // $args could be a requirement object
            if( isset( $args['presto_player_percent'] ) ) {
                $percent = $args['presto_player_percent'];
            }

            $log_meta['percent'] = array(
                'key' => 'percent',
                'value' => (int) $percent,
                'compare' => '>=',
                'type' => 'integer',
            );
            break;
        case 'gamipress_presto_player_watch_video_between_percent':
        case 'gamipress_presto_player_watch_specific_video_between_percent':

            if( isset( $args[2] ) ) {
                // Add the percent
                $percent = $args[2];

                $log_meta['score'] = array(
                    'key' => 'percent',
                    'value' => $percent,
                    'compare' => '>=',
                    'type' => 'integer',
                );
            }

            // $args could be a requirement object
            if( isset( $args['presto_player_min_percent'] ) ) {
                $min_percent = $args['presto_player_min_percent'];

                $log_meta['score'] = array(
                    'key' => 'percent',
                    'value' => $min_percent,
                    'compare' => '>=',
                    'type' => 'integer',
                );
            }

            // $args could be a requirement object
            if( isset( $args['presto_player_max_percent'] ) ) {
                $max_percent = $args['presto_player_max_percent'];

                $log_meta['score'] = array(
                    'key' => 'percent',
                    'value' => $max_percent,
                    'compare' => '<=',
                    'type' => 'integer',
                );
            }
            break;
    }

    return $log_meta;

}
add_filter( 'gamipress_get_user_trigger_count_log_meta', 'gamipress_presto_player_get_user_trigger_count_log_meta', 10, 6 );