<?php
/**
 * Triggers
 *
 * @package GamiPress\Meta_Box\Triggers
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
function gamipress_meta_box_activity_triggers( $triggers ) {

    $triggers[__( 'Meta Box', 'gamipress' )] = array(

        // Post fields
        'gamipress_meta_box_update_any_post_field_any_value' => __( 'Update any post field with any value','gamipress' ),
        'gamipress_meta_box_update_any_post_field_specific_value' => __( 'Update any post field with specific value','gamipress' ),
        'gamipress_meta_box_update_specific_post_field_any_value' => __( 'Update specific post field with any value','gamipress' ),
        'gamipress_meta_box_update_specific_post_field_specific_value' => __( 'Update specific post field with specific value','gamipress' ),

    );
        
    // User fields
    // Meta Box User Meta Extension
    if ( defined( 'MBAIO_DIR' ) || class_exists( 'RWMB_User_Storage' ) ) {

        $triggers[__( 'Meta Box Users', 'gamipress' )] = array(
            'gamipress_meta_box_update_any_user_field_any_value' => __( 'Update any user field with any value','gamipress' ),
            'gamipress_meta_box_update_any_user_field_specific_value' => __( 'Update any user field with specific value','gamipress' ),
            'gamipress_meta_box_update_specific_user_field_any_value' => __( 'Update specific user field with any value','gamipress' ),
            'gamipress_meta_box_update_specific_user_field_specific_value' => __( 'Update specific user field with specific value','gamipress' ),
        );

    }

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_meta_box_activity_triggers' );

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
function gamipress_meta_box_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $field_name = ( isset( $requirement['mb_field_name'] ) ) ? $requirement['mb_field_name'] : '';
    $field_name_user = ( isset( $requirement['mb_field_name_user'] ) ) ? $requirement['mb_field_name_user'] : '';
    $field_value = ( isset( $requirement['mb_field_value'] ) ) ? $requirement['mb_field_value'] : '';
    $value_condition = ( isset( $requirement['mb_field_value_condition'] ) ) ? $requirement['mb_field_value_condition'] : '';
    $value_conditions = gamipress_meta_box_get_value_conditions();

    switch( $requirement['trigger_type'] ) {
        // Post fields
        case 'gamipress_meta_box_update_specific_post_field_any_value':
            return sprintf( __( 'Update post field "%s" with any value', 'gamipress' ), $field_name );
            break;
        case 'gamipress_meta_box_update_any_post_field_specific_value':
            return sprintf( __( 'Update any post field with value that matches with "%s"', 'gamipress' ), $field_value );
            break;
        case 'gamipress_meta_box_update_specific_post_field_specific_value':
            return sprintf( __( 'Update post field "%s" with a value %s "%s"', 'gamipress' ), $field_name, $value_conditions[$value_condition], $field_value );
            break;
    
        // User fields
        case 'gamipress_meta_box_update_specific_user_field_any_value':
            return sprintf( __( 'Update user field "%s" with any value', 'gamipress' ), $field_name_user );
            break;
        case 'gamipress_meta_box_update_any_user_field_specific_value':
            return sprintf( __( 'Update any user field with value that matches with "%s"', 'gamipress' ), $field_value );
            break;
        case 'gamipress_meta_box_update_specific_user_field_specific_value':
            return sprintf( __( 'Update user field "%s" with a value %s "%s"', 'gamipress' ), $field_name_user, $value_conditions[$value_condition], $field_value );
            break;            
    
    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_meta_box_activity_trigger_label', 10, 3 );

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
function gamipress_meta_box_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Posts
        case 'gamipress_meta_box_update_any_post_field_any_value':
        case 'gamipress_meta_box_update_specific_post_field_any_value':
        case 'gamipress_meta_box_update_any_post_field_specific_value':
        case 'gamipress_meta_box_update_specific_post_field_specific_value':
        // Users
        case 'gamipress_meta_box_update_any_user_field_any_value':
        case 'gamipress_meta_box_update_any_user_field_specific_value':
        case 'gamipress_meta_box_update_specific_user_field_any_value':
        case 'gamipress_meta_box_update_specific_user_field_specific_value':            
            $user_id = $args[0];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_meta_box_trigger_get_user_id', 10, 3 );

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
function gamipress_meta_box_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        // Posts
        case 'gamipress_meta_box_update_specific_post_field_any_value':
        case 'gamipress_meta_box_update_specific_post_field_specific_value':
            $specific_id = $args[2];
            break;
        case 'gamipress_meta_box_update_any_post_field_specific_value':            
            $specific_id = $args[2];
            break;
        // Users
        case 'gamipress_meta_box_update_specific_user_field_any_value':
        case 'gamipress_meta_box_update_specific_user_field_specific_value':
            $specific_id = $args[2];
            break;
        case 'gamipress_meta_box_update_any_user_field_specific_value':          
            $specific_id = $args[2];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_meta_box_specific_trigger_get_id', 10, 3 );

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
function gamipress_meta_box_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Posts
        case 'gamipress_meta_box_update_any_post_field_any_value':
        case 'gamipress_meta_box_update_specific_post_field_any_value':
        case 'gamipress_meta_box_update_any_post_field_specific_value':
        case 'gamipress_meta_box_update_specific_post_field_specific_value':            
        // Users
        case 'gamipress_meta_box_update_any_user_field_any_value':
        case 'gamipress_meta_box_update_any_user_field_specific_value':
        case 'gamipress_meta_box_update_specific_user_field_any_value':
        case 'gamipress_meta_box_update_specific_user_field_specific_value':            
            // Add the user ID, field name and value
            $log_meta['user_id'] = $args[0];
            $log_meta['field_name'] = $args[2];
            $log_meta['field_value'] = $args[3];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_meta_box_log_event_trigger_meta_data', 10, 5 );