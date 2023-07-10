<?php
/**
 * Triggers
 *
 * @package GamiPress\Autonami\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin specific triggers
 *
 * @since  1.0.0
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_autonami_activity_triggers( $triggers ) {

    $triggers[__( 'Autonami', 'gamipress' )] = array(
        // Tag added
        'gamipress_autonami_tag_added'            => __( 'Any tag added', 'gamipress' ),
        'gamipress_autonami_specific_tag_added'   => __( 'Specific tag added', 'gamipress' ),
        // List added
        'gamipress_autonami_list_added'            => __( 'Added to any list', 'gamipress' ),
        'gamipress_autonami_specific_list_added'   => __( 'Added to a specific list', 'gamipress' ),
        // Tag removed
        'gamipress_autonami_tag_removed'            => __( 'Any tag removed', 'gamipress' ),
        'gamipress_autonami_specific_tag_removed'   => __( 'Specific tag removed', 'gamipress' ),
        // List removed
        'gamipress_autonami_list_removed'            => __( 'Removed from any list', 'gamipress' ),
        'gamipress_autonami_specific_list_removed'   => __( 'Removed from specific list', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_autonami_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_autonami_specific_activity_triggers( $specific_activity_triggers ) {

    $specific_activity_triggers['gamipress_autonami_specific_tag_added'] = array( 'autonami_tags' );
    $specific_activity_triggers['gamipress_autonami_specific_list_added'] = array( 'autonami_lists' );
    $specific_activity_triggers['gamipress_autonami_specific_tag_removed'] = array( 'autonami_tags' );
    $specific_activity_triggers['gamipress_autonami_specific_list_removed'] = array( 'autonami_lists' );

    return $specific_activity_triggers;

}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_autonami_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_autonami_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_autonami_specific_tag_added'] = __( 'Tag %s added', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_autonami_specific_list_added'] = __( 'Added to %s list', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_autonami_specific_tag_removed'] = __( 'Tag %s removed', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_autonami_specific_list_removed'] = __( 'Removed from %s list', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_autonami_specific_activity_trigger_label' );

/**
 * Get plugin specific activity trigger post title
 *
 * @since  1.0.0
 *
 * @param  string   $post_title
 * @param  integer  $specific_id
 * @param  string   $trigger_type
 *
 * @return string
 */
function gamipress_autonami_specific_activity_trigger_post_title( $post_title, $specific_id, $trigger_type ) {

    switch( $trigger_type ) {
        case 'gamipress_autonami_specific_tag_added':
        case 'gamipress_autonami_specific_tag_removed':
            if( absint( $specific_id ) !== 0 ) {

                // Get the tag title
                $tag_title = gamipress_autonami_get_tag_title( $specific_id );

                $post_title = $tag_title;
            }
            break;
        case 'gamipress_autonami_specific_list_added':
        case 'gamipress_autonami_specific_list_removed':
            if( absint( $specific_id ) !== 0 ) {

                // Get the list title
                $list_title = gamipress_autonami_get_list_title( $specific_id );

                $post_title = $list_title;
            }
            break;
    }

    return $post_title;

}
add_filter( 'gamipress_specific_activity_trigger_post_title', 'gamipress_autonami_specific_activity_trigger_post_title', 10, 3 );

/**
 * Get plugin specific activity trigger permalink
 *
 * @since  1.0.0
 *
 * @param  string   $permalink
 * @param  integer  $specific_id
 * @param  string   $trigger_type
 * @param  integer  $site_id
 *
 * @return string
 */
function gamipress_autonami_specific_activity_trigger_permalink( $permalink, $specific_id, $trigger_type, $site_id ) {

    switch( $trigger_type ) {
        case 'gamipress_autonami_specific_tag_added':
        case 'gamipress_autonami_specific_list_added':
        case 'gamipress_autonami_specific_tag_removed':
        case 'gamipress_autonami_specific_list_removed':
            $permalink = '';
            break;
    }

    return $permalink;

}
add_filter( 'gamipress_specific_activity_trigger_permalink', 'gamipress_autonami_specific_activity_trigger_permalink', 10, 4 );

/**
 * Get user for a autonamin trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $user_id user ID to override.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 * @return integer          User ID.
 */
function gamipress_autonami_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Tag added
        case 'gamipress_autonami_tag_added':
        case 'gamipress_autonami_specific_tag_added':
        // List added
        case 'gamipress_autonami_list_added':
        case 'gamipress_autonami_specific_list_added':
        // Tag removed
        case 'gamipress_autonami_tag_removed':
        case 'gamipress_autonami_specific_tag_removed':
        // List removed
        case 'gamipress_autonami_list_removed':
        case 'gamipress_autonami_specific_list_removed':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_autonami_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a autonamin specific trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer  $specific_id Specific ID.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          Specific ID.
 */
function gamipress_autonami_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        // Tag added
        case 'gamipress_autonami_specific_tag_added':
        // List added
        case 'gamipress_autonami_specific_list_added':
        // Tag added
        case 'gamipress_autonami_specific_tag_removed':
        // List added
        case 'gamipress_autonami_specific_list_removed':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_autonami_specific_trigger_get_id', 10, 3 );

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
function gamipress_autonami_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Tag added
        case 'gamipress_autonami_tag_added':
        case 'gamipress_autonami_specific_tag_added':
        // Tag removed
        case 'gamipress_autonami_tag_removed':
        case 'gamipress_autonami_specific_tag_removed':            
            // Add the tag ID
            $log_meta['tag_id'] = $args[0];
            break;
        // List added
        case 'gamipress_autonami_list_added':
        case 'gamipress_autonami_specific_list_added':
        // List removed
        case 'gamipress_autonami_list_removed':
        case 'gamipress_autonami_specific_list_removed':
            // Add the list ID
            $log_meta['list_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_autonami_log_event_trigger_meta_data', 10, 5 );