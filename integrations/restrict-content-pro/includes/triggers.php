<?php
/**
 * Triggers
 *
 * @package GamiPress\Restrict_Content_Pro\Triggers
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
function gamipress_restrict_content_pro_activity_triggers( $triggers ) {

    $triggers[__( 'Restrict Content Pro', 'gamipress' )] = array(

        // Purchase
        'gamipress_rcp_free_membership'               => __( 'Get any free membership level', 'gamipress' ),
        'gamipress_rcp_free_specific_membership'      => __( 'Get a specific free membership level', 'gamipress' ),
        // Purchase
        'gamipress_rcp_purchase_membership'           => __( 'Purchase any membership level', 'gamipress' ),
        'gamipress_rcp_purchase_specific_membership'  => __( 'Purchase a specific membership level', 'gamipress' ),
        // Cancel
        'gamipress_rcp_cancel_membership'             => __( 'Cancel any membership level subscription', 'gamipress' ),
        'gamipress_rcp_cancel_specific_membership'    => __( 'Cancel a specific membership level subscription', 'gamipress' ),
        // Expired
        'gamipress_rcp_membership_expired'            => __( 'Get any membership level subscription expired', 'gamipress' ),
        'gamipress_rcp_specific_membership_expired'   => __( 'Get a specific membership level subscription expired', 'gamipress' ),

    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_restrict_content_pro_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_restrict_content_pro_specific_activity_triggers( $specific_activity_triggers ) {

    $specific_activity_triggers['gamipress_rcp_free_specific_membership'] = array( 'rcp_membership' );
    $specific_activity_triggers['gamipress_rcp_purchase_specific_membership'] = array( 'rcp_membership' );
    $specific_activity_triggers['gamipress_rcp_cancel_specific_membership'] = array( 'rcp_membership' );
    $specific_activity_triggers['gamipress_rcp_specific_membership_expired'] = array( 'rcp_membership' );

    return $specific_activity_triggers;

}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_restrict_content_pro_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_restrict_content_pro_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_rcp_free_specific_membership'] = __( 'Get %s membership level', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_rcp_purchase_specific_membership'] = __( 'Purchase %s membership level', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_rcp_cancel_specific_membership'] = __( 'Cancel %s membership level subscription', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_rcp_specific_membership_expired'] = __( 'Get %s membership level subscription expired', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_restrict_content_pro_specific_activity_trigger_label' );

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
function gamipress_restrict_content_pro_specific_activity_trigger_post_title( $post_title, $specific_id, $trigger_type ) {

    switch( $trigger_type ) {
        case 'gamipress_rcp_free_specific_membership':
        case 'gamipress_rcp_purchase_specific_membership':
        case 'gamipress_rcp_cancel_specific_membership':
        case 'gamipress_rcp_specific_membership_expired':
            if( absint( $specific_id ) !== 0 ) {

                // Get the membership title
                $membership_title = gamipress_restrict_content_pro_get_membership_title( $specific_id );

                $post_title = $membership_title;
            }
            break;
    }

    return $post_title;

}
add_filter( 'gamipress_specific_activity_trigger_post_title', 'gamipress_restrict_content_pro_specific_activity_trigger_post_title', 10, 3 );

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
function gamipress_restrict_content_pro_specific_activity_trigger_permalink( $permalink, $specific_id, $trigger_type, $site_id ) {

    switch( $trigger_type ) {
        case 'gamipress_rcp_free_specific_membership':
        case 'gamipress_rcp_purchase_specific_membership':
        case 'gamipress_rcp_cancel_specific_membership':
        case 'gamipress_rcp_specific_membership_expired':
            $permalink = '';
            break;
    }

    return $permalink;

}
add_filter( 'gamipress_specific_activity_trigger_permalink', 'gamipress_restrict_content_pro_specific_activity_trigger_permalink', 10, 4 );

/**
 * Get user for a h5pn trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $user_id user ID to override.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 * @return integer          User ID.
 */
function gamipress_restrict_content_pro_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Free
        case 'gamipress_rcp_free_membership':
        case 'gamipress_rcp_free_specific_membership':
        // Purchase
        case 'gamipress_rcp_purchase_membership':
        case 'gamipress_rcp_purchase_specific_membership':
        // Cancel
        case 'gamipress_rcp_cancel_membership':
        case 'gamipress_rcp_cancel_specific_membership':
        // Expired
        case 'gamipress_rcp_membership_expired':
        case 'gamipress_rcp_specific_membership_expired':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_restrict_content_pro_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a h5pn specific trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer  $specific_id Specific ID.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          Specific ID.
 */
function gamipress_restrict_content_pro_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_rcp_free_specific_membership':
        case 'gamipress_rcp_purchase_specific_membership':
        case 'gamipress_rcp_cancel_specific_membership':
        case 'gamipress_rcp_specific_membership_expired':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_restrict_content_pro_specific_trigger_get_id', 10, 3 );

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
function gamipress_restrict_content_pro_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Free
        case 'gamipress_rcp_free_membership':
        case 'gamipress_rcp_free_specific_membership':
        // Purchase
        case 'gamipress_rcp_purchase_membership':
        case 'gamipress_rcp_purchase_specific_membership':
        // Cancel
        case 'gamipress_rcp_cancel_membership':
        case 'gamipress_rcp_cancel_specific_membership':
        // Expired
        case 'gamipress_rcp_membership_expired':
        case 'gamipress_rcp_specific_membership_expired':
            // Add the membership ID
            $log_meta['membership_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_restrict_content_pro_log_event_trigger_meta_data', 10, 5 );