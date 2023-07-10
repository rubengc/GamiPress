<?php
/**
 * Triggers
 *
 * @package GamiPress\Advanced_Custom_Fields\Triggers
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
function gamipress_acf_activity_triggers( $triggers ) {

    $triggers[__( 'Advanced Custom Fields', 'gamipress' )] = array(

        // Post fields
        'gamipress_acf_update_any_post_field_any_value' => __( 'Update any post field with any value','gamipress' ),
        'gamipress_acf_update_any_post_field_specific_value' => __( 'Update any post field with specific value','gamipress' ),
        'gamipress_acf_update_specific_post_field_any_value' => __( 'Update specific post field with any value','gamipress' ),
        'gamipress_acf_update_specific_post_field_specific_value' => __( 'Update specific post field with specific value','gamipress' ),
        
        // User fields
        'gamipress_acf_update_any_user_field_any_value' => __( 'Update any user field with any value','gamipress' ),
        'gamipress_acf_update_any_user_field_specific_value' => __( 'Update any user field with specific value','gamipress' ),
        'gamipress_acf_update_specific_user_field_any_value' => __( 'Update specific user field with any value','gamipress' ),
        'gamipress_acf_update_specific_user_field_specific_value' => __( 'Update specific user field with specific value','gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_acf_activity_triggers' );

/**
 * Register plugin specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_acf_specific_activity_triggers( $specific_activity_triggers ) {

    // Post fields
    $specific_activity_triggers['gamipress_acf_update_specific_post_field_any_value'] = array( 'acf_post_fields' );
    $specific_activity_triggers['gamipress_acf_update_specific_post_field_specific_value'] = array( 'acf_post_fields' );
    
    // User fields
    $specific_activity_triggers['gamipress_acf_update_specific_user_field_any_value'] = array( 'acf_user_fields' );
    $specific_activity_triggers['gamipress_acf_update_specific_user_field_specific_value'] = array( 'acf_user_fields' );

    return $specific_activity_triggers;

}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_acf_specific_activity_triggers' );

/**
 * Register plugin specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_acf_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Post fields
    $specific_activity_trigger_labels['gamipress_acf_update_specific_post_field_any_value'] = __( 'Update specific post field %s with any value', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_acf_update_specific_post_field_specific_value'] = __( 'Update specific post field %s with specific value', 'gamipress' );

    // User fields
    $specific_activity_trigger_labels['gamipress_acf_update_specific_user_field_any_value'] = __( 'Update specific user field %s with any value', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_acf_update_specific_user_field_specific_value'] = __( 'Update specific user field %s with specific value', 'gamipress' );
    
    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_acf_specific_activity_trigger_label', 20 );

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
function gamipress_acf_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $field_value = ( isset( $requirement['acf_field_value'] ) ) ? $requirement['acf_field_value'] : '';
    $value_condition = ( isset( $requirement['acf_field_value_condition'] ) ) ? $requirement['acf_field_value_condition'] : '';
    $value_conditions = gamipress_acf_get_value_conditions();

    switch( $requirement['trigger_type'] ) {
        // Post fields
        case 'gamipress_acf_update_any_post_field_specific_value':
            return sprintf( __( 'Update any post field with value that matches with "%s"', 'gamipress' ), $field_value );
            break;
        case 'gamipress_acf_update_specific_post_field_specific_value':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            $acf_post_field_title = gamipress_acf_get_post_field_title( $achievement_post_id );
            return sprintf( __( 'Update post field %s with a value %s %s', 'gamipress' ), $acf_post_field_title, $value_conditions[$value_condition], $field_value );
            break;
    
        // User fields
        case 'gamipress_acf_update_any_user_field_specific_value':
            return sprintf( __( 'Update any user field with value that matches with "%s"', 'gamipress' ), $field_value );
            break;
        case 'gamipress_acf_update_specific_user_field_specific_value':
            $achievement_post_id = absint( $requirement['achievement_post'] );
            $acf_post_field_title = gamipress_acf_get_post_field_title( $achievement_post_id );
            return sprintf( __( 'Update user field %s with a value %s %s', 'gamipress' ), $acf_post_field_title, $value_conditions[$value_condition], $field_value );
            break;            
    
    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_acf_activity_trigger_label', 10, 3 );

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
function gamipress_acf_specific_activity_trigger_post_title( $post_title, $specific_id, $trigger_type ) {
    
    switch( $trigger_type ) {
        case 'gamipress_acf_update_specific_post_field_any_value':    
            
            if( absint( $specific_id ) !== 0 ) {
                // Get the post field title
                $acf_post_field_title = gamipress_acf_get_post_field_title( $specific_id );

                $post_title = $acf_post_field_title;
            }
            break;            

        case 'gamipress_acf_update_specific_user_field_any_value':
            if( absint( $specific_id ) !== 0 ) {
                // Get the user field title
                $acf_user_field_title = gamipress_acf_get_post_field_title( $specific_id );

                $post_title = $acf_user_field_title;
            }
            break;
    }

    return $post_title;

}
add_filter( 'gamipress_specific_activity_trigger_post_title', 'gamipress_acf_specific_activity_trigger_post_title', 10, 3 );

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
function gamipress_acf_specific_activity_trigger_permalink( $permalink, $specific_id, $trigger_type, $site_id ) {
    
    switch( $trigger_type ) {
        case 'gamipress_acf_update_specific_post_field_any_value':
        case 'gamipress_acf_update_specific_user_field_any_value':
            $permalink = '';
            break;
    }

    return $permalink;

}
add_filter( 'gamipress_specific_activity_trigger_permalink', 'gamipress_acf_specific_activity_trigger_permalink', 10, 4 );

/**
 * Get user for an acf trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $user_id user ID to override.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 * @return integer          User ID.
 */
function gamipress_acf_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Posts
        case 'gamipress_acf_update_any_post_field_any_value':
        case 'gamipress_acf_update_specific_post_field_any_value':
        case 'gamipress_acf_update_any_post_field_specific_value':
        case 'gamipress_acf_update_specific_post_field_specific_value':
        // Users
        case 'gamipress_acf_update_any_user_field_any_value':
        case 'gamipress_acf_update_any_user_field_specific_value':
        case 'gamipress_acf_update_specific_user_field_any_value':
        case 'gamipress_acf_update_specific_user_field_specific_value':            
            $user_id = $args[0];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_acf_trigger_get_user_id', 10, 3 );

/**
 * Get the id for an acf specific trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer  $specific_id Specific ID.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          Specific ID.
 */
function gamipress_acf_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    global $wpdb;

    switch ( $trigger ) {
        // Posts
        case 'gamipress_acf_update_specific_post_field_any_value':
        case 'gamipress_acf_update_specific_post_field_specific_value':            
            $specific_id = $args[2];
            break;
        // Users
        case 'gamipress_acf_update_specific_user_field_any_value':
        case 'gamipress_acf_update_specific_user_field_specific_value':          
            $specific_id = $args[2];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_acf_specific_trigger_get_id', 10, 3 );

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
function gamipress_acf_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Posts
        case 'gamipress_acf_update_any_post_field_any_value':
        case 'gamipress_acf_update_specific_post_field_any_value':
        case 'gamipress_acf_update_any_post_field_specific_value':
        case 'gamipress_acf_update_specific_post_field_specific_value':            
        // Users
        case 'gamipress_acf_update_any_user_field_any_value':
        case 'gamipress_acf_update_any_user_field_specific_value':
        case 'gamipress_acf_update_specific_user_field_any_value':
        case 'gamipress_acf_update_specific_user_field_specific_value':            
            // Add the user ID, field name and value
            $log_meta['user_id'] = $args[0];
            $log_meta['field_name'] = $args[2];
            $log_meta['field_value'] = $args[3];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_acf_log_event_trigger_meta_data', 10, 5 );