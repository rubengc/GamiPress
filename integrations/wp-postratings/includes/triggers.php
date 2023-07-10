<?php
/**
 * Triggers
 *
 * @package GamiPress\WP_PostRatings\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register WP PostRatings specific triggers
 *
 * @since  1.0.0
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_wp_postratings_activity_triggers( $triggers ) {

    $triggers[__( 'WP PostRatings', 'gamipress' )] = array(
        'gamipress_wp_postratings_rate'                         => __( 'Rate a post', 'gamipress' ),
        'gamipress_wp_postratings_specific_rate'                => __( 'Rate a specific post', 'gamipress' ),
        'gamipress_wp_postratings_user_rate'                    => __( 'Get rates on a post', 'gamipress' ),
        'gamipress_wp_postratings_user_specific_rate'           => __( 'Get rates on a specific post', 'gamipress' ),
        // Specific rate
        'gamipress_wp_postratings_rate_specific'                 => __( 'Rate a post with a specific rating', 'gamipress' ),
        'gamipress_wp_postratings_specific_rate_specific'        => __( 'Rate a specific post with a specific rating', 'gamipress' ),
        'gamipress_wp_postratings_user_rate_specific'            => __( 'Get rates on a post with a specific rating', 'gamipress' ),
        'gamipress_wp_postratings_user_specific_rate_specific'   => __( 'Get rates on a specific post with a specific rating', 'gamipress' ),
        // Minimum rate
        'gamipress_wp_postratings_minimum_rate'                 => __( 'Rate a post with a minimum rating', 'gamipress' ),
        'gamipress_wp_postratings_specific_minimum_rate'        => __( 'Rate a specific post with a minimum rating', 'gamipress' ),
        'gamipress_wp_postratings_user_minimum_rate'            => __( 'Get rates on a post with a minimum rating', 'gamipress' ),
        'gamipress_wp_postratings_user_specific_minimum_rate'   => __( 'Get rates on a specific post with a minimum rating', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_wp_postratings_activity_triggers' );

/**
 * Register WP PostRatings specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_wp_postratings_specific_activity_triggers( $specific_activity_triggers ) {

    // All public post types
    $post_types = get_post_types( array(
        'public' => true
    ) );

    // Remove keys
    $post_types = array_values( $post_types );

    $specific_activity_triggers['gamipress_wp_postratings_specific_rate'] = $post_types;
    $specific_activity_triggers['gamipress_wp_postratings_user_specific_rate'] = $post_types;
    // Specific rate
    $specific_activity_triggers['gamipress_wp_postratings_specific_rate_specific'] = $post_types;
    $specific_activity_triggers['gamipress_wp_postratings_user_specific_rate_specific'] = $post_types;
    // Minimum rate
    $specific_activity_triggers['gamipress_wp_postratings_specific_minimum_rate'] = $post_types;
    $specific_activity_triggers['gamipress_wp_postratings_user_specific_minimum_rate'] = $post_types;

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_wp_postratings_specific_activity_triggers' );

/**
 * Build custom activity trigger label
 *
 * @param string    $title
 * @param integer   $requirement_id
 * @param array     $requirement
 *
 * @return string
 */
function gamipress_wp_postratings_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $minimum_score = ( isset( $requirement['wp_postratings_rate'] ) ) ? intval( $requirement['wp_postratings_rate'] ) : 0;

    switch( $requirement['trigger_type'] ) {
        // Specific rate
        case 'gamipress_wp_postratings_rate_specific':
            return sprintf( __( 'Rate a post with a rating of %d', 'gamipress' ), $minimum_score );
            break;
        case 'gamipress_wp_postratings_specific_rate_specific':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Rate %s with a rating of %d', 'gamipress' ), get_the_title( $achievement_post_id ), $minimum_score );
            break;
        case 'gamipress_wp_postratings_user_rate_specific':
            return sprintf( __( 'Get a rate on a post with a rating of %d', 'gamipress' ), $minimum_score );
            break;
        case 'gamipress_wp_postratings_user_specific_rate_specific':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Get a rate on %s with a rating of %d', 'gamipress' ), get_the_title( $achievement_post_id ), $minimum_score );
            break;
        // Minimum rate
        case 'gamipress_wp_postratings_minimum_rate':
            return sprintf( __( 'Rate a post with a rating of %d or higher', 'gamipress' ), $minimum_score );
            break;
        case 'gamipress_wp_postratings_specific_minimum_rate':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Rate %s with a rating of %d or higher', 'gamipress' ), get_the_title( $achievement_post_id ), $minimum_score );
            break;
        case 'gamipress_wp_postratings_user_minimum_rate':
            return sprintf( __( 'Get a rate on a post with a rating of %d or higher', 'gamipress' ), $minimum_score );
            break;
        case 'gamipress_wp_postratings_user_specific_minimum_rate':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            return sprintf( __( 'Get a rate on %s with a rating of %d or higher', 'gamipress' ), get_the_title( $achievement_post_id ), $minimum_score );
            break;
    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_wp_postratings_activity_trigger_label', 10, 3 );

/**
 * Register WP PostRatings specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_wp_postratings_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_wp_postratings_specific_rate'] = __( 'Rate %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wp_postratings_user_specific_rate'] = __( 'Get a rate on %s', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_wp_postratings_specific_activity_trigger_label' );

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
function gamipress_wp_postratings_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_wp_postratings_rate':
        case 'gamipress_wp_postratings_specific_rate':
        case 'gamipress_wp_postratings_user_rate':
        case 'gamipress_wp_postratings_user_specific_rate':
        // Specific rate
        case 'gamipress_wp_postratings_rate_specific':
        case 'gamipress_wp_postratings_specific_rate_specific':
        case 'gamipress_wp_postratings_user_rate_specific':
        case 'gamipress_wp_postratings_user_specific_rate_specific':
        // Minimum rate
        case 'gamipress_wp_postratings_minimum_rate':
        case 'gamipress_wp_postratings_specific_minimum_rate':
        case 'gamipress_wp_postratings_user_minimum_rate':
        case 'gamipress_wp_postratings_user_specific_minimum_rate':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_wp_postratings_trigger_get_user_id', 10, 3);


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
function gamipress_wp_postratings_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_wp_postratings_specific_rate':
        case 'gamipress_wp_postratings_user_specific_rate':
        // Specific rate
        case 'gamipress_wp_postratings_specific_rate_specific':
        case 'gamipress_wp_postratings_user_specific_rate_specific':
        // Minimum rate
        case 'gamipress_wp_postratings_specific_minimum_rate':
        case 'gamipress_wp_postratings_user_specific_minimum_rate':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_wp_postratings_specific_trigger_get_id', 10, 3 );

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
function gamipress_wp_postratings_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_wp_postratings_rate':
        case 'gamipress_wp_postratings_specific_rate':
        // Specific rate
        case 'gamipress_wp_postratings_rate_specific':
        case 'gamipress_wp_postratings_specific_rate_specific':
        // Minimum rate
        case 'gamipress_wp_postratings_minimum_rate':
        case 'gamipress_wp_postratings_specific_minimum_rate':
            // Add the post ID rate and rating value
            $log_meta['post_id'] = $args[0];
            $log_meta['rating_value'] = $args[2];
            break;
        case 'gamipress_wp_postratings_user_rate':
        case 'gamipress_wp_postratings_user_specific_rate':
        // Specific rate
        case 'gamipress_wp_postratings_user_rate_specific':
        case 'gamipress_wp_postratings_user_specific_rate_specific':
        // Minimum rate
        case 'gamipress_wp_postratings_user_minimum_rate':
        case 'gamipress_wp_postratings_user_specific_minimum_rate':
            // Add the post ID rate and rating value
            $log_meta['post_id'] = $args[0];
            $log_meta['rating_value'] = $args[3];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_wp_postratings_log_event_trigger_meta_data', 10, 5 );