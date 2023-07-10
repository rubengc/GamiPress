<?php
/**
 * Triggers
 *
 * @package GamiPress\Paid_Memberships_Pro\Triggers
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
function gamipress_paid_memberships_pro_activity_triggers( $triggers ) {

    $triggers[__( 'Paid Memberships Pro', 'gamipress' )] = array(

        // Purchase
        'gamipress_pmpro_purchase_membership'           => __( 'Purchase any membership', 'gamipress' ),
        'gamipress_pmpro_purchase_specific_membership'  => __( 'Purchase a specific membership', 'gamipress' ),
        // Renew
        'gamipress_pmpro_renew_membership'              => __( 'Renew any membership', 'gamipress' ),
        'gamipress_pmpro_renew_specific_membership'     => __( 'Renew a specific membership', 'gamipress' ),
        // Cancel
        'gamipress_pmpro_cancel_membership'             => __( 'Cancel any membership', 'gamipress' ),
        'gamipress_pmpro_cancel_specific_membership'    => __( 'Cancel a specific membership', 'gamipress' ),
        // Expired
        'gamipress_pmpro_membership_expired'            => __( 'Get any membership expired', 'gamipress' ),
        'gamipress_pmpro_specific_membership_expired'   => __( 'Get a specific membership expired', 'gamipress' ),

    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_paid_memberships_pro_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_paid_memberships_pro_specific_activity_triggers( $specific_activity_triggers ) {

    $specific_activity_triggers['gamipress_pmpro_purchase_specific_membership'] = array( 'pmpro_membership' );
    $specific_activity_triggers['gamipress_pmpro_renew_specific_membership'] = array( 'pmpro_membership' );
    $specific_activity_triggers['gamipress_pmpro_cancel_specific_membership'] = array( 'pmpro_membership' );
    $specific_activity_triggers['gamipress_pmpro_specific_membership_expired'] = array( 'pmpro_membership' );

    return $specific_activity_triggers;

}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_paid_memberships_pro_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_paid_memberships_pro_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_pmpro_purchase_specific_membership'] = __( 'Purchase %s membership', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_pmpro_renew_specific_membership'] = __( 'Renew %s membership', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_pmpro_cancel_specific_membership'] = __( 'Cancel %s membership', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_pmpro_specific_membership_expired'] = __( 'Get %s membership expired', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_paid_memberships_pro_specific_activity_trigger_label' );

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
function gamipress_paid_memberships_pro_specific_activity_trigger_post_title( $post_title, $specific_id, $trigger_type ) {

    switch( $trigger_type ) {
        case 'gamipress_pmpro_purchase_specific_membership':
        case 'gamipress_pmpro_renew_specific_membership':
        case 'gamipress_pmpro_cancel_specific_membership':
        case 'gamipress_pmpro_specific_membership_expired':
            if( absint( $specific_id ) !== 0 ) {

                // Get the membership title
                $membership_title = gamipress_paid_memberships_pro_get_membership_title( $specific_id );

                $post_title = $membership_title;
            }
            break;
    }

    return $post_title;

}
add_filter( 'gamipress_specific_activity_trigger_post_title', 'gamipress_paid_memberships_pro_specific_activity_trigger_post_title', 10, 3 );

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
function gamipress_paid_memberships_pro_specific_activity_trigger_permalink( $permalink, $specific_id, $trigger_type, $site_id ) {

    switch( $trigger_type ) {
        case 'gamipress_pmpro_purchase_specific_membership':
        case 'gamipress_pmpro_renew_specific_membership':
        case 'gamipress_pmpro_cancel_specific_membership':
        case 'gamipress_pmpro_specific_membership_expired':
            $permalink = '';
            break;
    }

    return $permalink;

}
add_filter( 'gamipress_specific_activity_trigger_permalink', 'gamipress_paid_memberships_pro_specific_activity_trigger_permalink', 10, 4 );

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
function gamipress_paid_memberships_pro_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Purchase
        case 'gamipress_pmpro_purchase_membership':
        case 'gamipress_pmpro_purchase_specific_membership':
        // Renew
        case 'gamipress_pmpro_renew_membership':
        case 'gamipress_pmpro_renew_specific_membership':
        // Cancel
        case 'gamipress_pmpro_cancel_membership':
        case 'gamipress_pmpro_cancel_specific_membership':
        // Expired
        case 'gamipress_pmpro_membership_expired':
        case 'gamipress_pmpro_specific_membership_expired':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_paid_memberships_pro_trigger_get_user_id', 10, 3 );

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
function gamipress_paid_memberships_pro_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_pmpro_purchase_specific_membership':
        case 'gamipress_pmpro_renew_specific_membership':
        case 'gamipress_pmpro_cancel_specific_membership':
        case 'gamipress_pmpro_specific_membership_expired':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_paid_memberships_pro_specific_trigger_get_id', 10, 3 );

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
function gamipress_paid_memberships_pro_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Purchase
        case 'gamipress_pmpro_purchase_membership':
        case 'gamipress_pmpro_purchase_specific_membership':
        // Renew
        case 'gamipress_pmpro_renew_membership':
        case 'gamipress_pmpro_renew_specific_membership':
        // Cancel
        case 'gamipress_pmpro_cancel_membership':
        case 'gamipress_pmpro_cancel_specific_membership':
        // Expired
        case 'gamipress_pmpro_membership_expired':
        case 'gamipress_pmpro_specific_membership_expired':
            // Add the membership ID
            $log_meta['membership_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_paid_memberships_pro_log_event_trigger_meta_data', 10, 5 );