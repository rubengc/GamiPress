<?php
/**
 * Triggers
 *
 * @package GamiPress\WP_User_Manager\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin activity triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_wp_user_manager_activity_triggers( $triggers ) {

    // WP User Manager
    $triggers[__( 'WP User Manager', 'gamipress' )] = array(
        'gamipress_wp_user_manager_login'                   => __( 'Log in to the website', 'gamipress' ),
        'gamipress_wp_user_manager_register'                => __( 'Register through a form', 'gamipress' ),
        'gamipress_wp_user_manager_register_specific_form'  => __( 'Register through a specific form', 'gamipress' ),
        'gamipress_wp_user_manager_user_approved'           => __( 'Get account approved', 'gamipress' ),
        'gamipress_wp_user_manager_user_rejected'           => __( 'Get account rejected', 'gamipress' ),
        'gamipress_wp_user_manager_user_verified'           => __( 'Verify email address', 'gamipress' ),
        'gamipress_wp_user_manager_change_avatar'           => __( 'Change profile avatar', 'gamipress' ),
        'gamipress_wp_user_manager_remove_avatar'           => __( 'Remove profile avatar', 'gamipress' ),
        'gamipress_wp_user_manager_change_cover'            => __( 'Change profile cover', 'gamipress' ),
        'gamipress_wp_user_manager_remove_cover'            => __( 'Remove profile cover', 'gamipress' ),
        'gamipress_wp_user_manager_change_description'      => __( 'Change profile description', 'gamipress' ),
        // Groups
        'gamipress_wp_user_manager_join_group'              => __( 'Join any group', 'gamipress' ),
        'gamipress_wp_user_manager_join_specific_group'     => __( 'Join a specific group', 'gamipress' ),
        'gamipress_wp_user_manager_leave_group'             => __( 'Leave any group', 'gamipress' ),
        'gamipress_wp_user_manager_leave_specific_group'    => __( 'Leave a specific group', 'gamipress' ),
        'gamipress_wp_user_manager_accepted_group'          => __( 'Get accepted in any group', 'gamipress' ),
        'gamipress_wp_user_manager_accepted_specific_group' => __( 'Get accepted in a specific group', 'gamipress' ),
        'gamipress_wp_user_manager_rejected_group'          => __( 'Get rejected from any group', 'gamipress' ),
        'gamipress_wp_user_manager_rejected_specific_group' => __( 'Get rejected from a specific group', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_wp_user_manager_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_wp_user_manager_specific_activity_triggers( $specific_activity_triggers ) {

    $specific_activity_triggers['gamipress_wp_user_manager_register_specific_form'] = array( 'wp_user_manager_form' );
    // Groups
    $specific_activity_triggers['gamipress_wp_user_manager_join_specific_group'] = array( 'wpum_group' );
    $specific_activity_triggers['gamipress_wp_user_manager_leave_specific_group'] = array( 'wpum_group' );
    $specific_activity_triggers['gamipress_wp_user_manager_accepted_specific_group'] = array( 'wpum_group' );
    $specific_activity_triggers['gamipress_wp_user_manager_rejected_specific_group'] = array( 'wpum_group' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_wp_user_manager_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_wp_user_manager_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_wp_user_manager_register_specific_form'] = __( 'Register through %s form', 'gamipress' );
    // Groups
    $specific_activity_trigger_labels['gamipress_wp_user_manager_join_specific_group'] = __( 'Join %s group', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wp_user_manager_leave_specific_group'] = __( 'Leave %s group', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wp_user_manager_accepted_specific_group'] = __( 'Get accepted in %s group', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wp_user_manager_rejected_specific_group'] = __( 'Get rejected from %s group', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_wp_user_manager_specific_activity_trigger_label' );

/**
 * Get plugin specific activity trigger post title
 *
 * @since  1.0.0
 *
 * @param  string   $post_title
 * @param  integer  $specific_id
 * @param  string   $trigger_type
 * @return string
 */
function gamipress_wp_user_manager_specific_activity_trigger_post_title( $post_title, $specific_id, $trigger_type ) {

    global $wpdb;

    switch( $trigger_type ) {
        case 'gamipress_wp_user_manager_register_specific_form':
            if( absint( $specific_id ) !== 0 ) {
                $post_title = $wpdb->get_var( $wpdb->prepare(
                    "SELECT name FROM {$wpdb->prefix}wpum_registration_forms WHERE id = %s",
                    $specific_id
                ) );
            }
            break;
    }

    return $post_title;

}
add_filter( 'gamipress_specific_activity_trigger_post_title', 'gamipress_wp_user_manager_specific_activity_trigger_post_title', 10, 3 );

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
function gamipress_wp_user_manager_specific_activity_trigger_permalink( $permalink, $specific_id, $trigger_type, $site_id ) {

    switch( $trigger_type ) {
        case 'gamipress_wp_user_manager_register_specific_form':
            $permalink = '';
            break;
    }

    return $permalink;

}
add_filter( 'gamipress_specific_activity_trigger_permalink', 'gamipress_wp_user_manager_specific_activity_trigger_permalink', 10, 4 );

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
function gamipress_wp_user_manager_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_wp_user_manager_login':
        case 'gamipress_wp_user_manager_user_approved':
        case 'gamipress_wp_user_manager_user_rejected':
        case 'gamipress_wp_user_manager_user_verified':
        case 'gamipress_wp_user_manager_change_avatar':
        case 'gamipress_wp_user_manager_remove_avatar':
        case 'gamipress_wp_user_manager_change_cover':
        case 'gamipress_wp_user_manager_remove_cover':
        case 'gamipress_wp_user_manager_change_description':
        $user_id = $args[0];
            break;
        case 'gamipress_wp_user_manager_register':
        case 'gamipress_wp_user_manager_register_specific_form':
        // Groups
        case 'gamipress_wp_user_manager_join_group':
        case 'gamipress_wp_user_manager_join_specific_group':
        case 'gamipress_wp_user_manager_leave_group':
        case 'gamipress_wp_user_manager_leave_specific_group':
        case 'gamipress_wp_user_manager_accepted_group':
        case 'gamipress_wp_user_manager_accepted_specific_group':
        case 'gamipress_wp_user_manager_rejected_group':
        case 'gamipress_wp_user_manager_rejected_specific_group':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_wp_user_manager_trigger_get_user_id', 10, 3 );

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
function gamipress_wp_user_manager_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        case 'gamipress_wp_user_manager_register_specific_form':
        // Groups
        case 'gamipress_wp_user_manager_join_specific_group':
        case 'gamipress_wp_user_manager_leave_specific_group':
        case 'gamipress_wp_user_manager_accepted_specific_group':
        case 'gamipress_wp_user_manager_rejected_specific_group':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_wp_user_manager_specific_trigger_get_id', 10, 3 );

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.2
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_wp_user_manager_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_wp_user_manager_register':
        case 'gamipress_wp_user_manager_register_specific_form':
            // Add the form ID
            $log_meta['form_id'] = $args[0];
            break;
        // Groups
        case 'gamipress_wp_user_manager_join_group':
        case 'gamipress_wp_user_manager_join_specific_group':
        case 'gamipress_wp_user_manager_leave_group':
        case 'gamipress_wp_user_manager_leave_specific_group':
        case 'gamipress_wp_user_manager_accepted_group':
        case 'gamipress_wp_user_manager_accepted_specific_group':
        case 'gamipress_wp_user_manager_rejected_group':
        case 'gamipress_wp_user_manager_rejected_specific_group':
            // Add the post ID
            $log_meta['post_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_wp_user_manager_log_event_trigger_meta_data', 10, 5 );