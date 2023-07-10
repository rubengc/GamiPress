<?php
/**
 * Triggers
 *
 * @package GamiPress\Download_Manager\Triggers
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
function gamipress_download_manager_activity_triggers( $triggers ) {

    $triggers[__( 'Download Manager', 'gamipress' )] = array(

        'gamipress_download_manager_any_download'       => __( 'Download any file', 'gamipress' ),
        'gamipress_download_manager_specific_download'  => __( 'Download specific file', 'gamipress' ),
        
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_download_manager_activity_triggers' );

/**
 * Register Download Manager specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_download_manager_specific_activity_triggers( $specific_activity_triggers ) {

    $specific_activity_triggers['gamipress_download_manager_specific_download'] = array( 'wpdmpro' );
    
    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_download_manager_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_download_manager_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_download_manager_specific_download'] = __( 'Download %s file', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_download_manager_specific_activity_trigger_label' );

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
function gamipress_download_manager_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {

        case 'gamipress_download_manager_any_download':
        case 'gamipress_download_manager_specific_download':
            $user_id = $args[1];
            break;

    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_download_manager_trigger_get_user_id', 10, 3 );

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
function gamipress_download_manager_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {

        case 'gamipress_download_manager_specific_download':
            $specific_id = $args[0];
            break;

    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_download_manager_specific_trigger_get_id', 10, 3 );

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
function gamipress_download_manager_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {

        case 'gamipress_download_manager_any_download':
        case 'gamipress_download_manager_specific_download':
            $log_meta['package_id'] = $args[0];
            break;

    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_download_manager_log_event_trigger_meta_data', 10, 5 );