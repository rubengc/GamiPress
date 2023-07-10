<?php
/**
 * Triggers
 *
 * @package GamiPress\JetEngine\Triggers
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
function gamipress_jetengine_activity_triggers( $triggers ) {

    $triggers[__( 'JetEngine', 'gamipress' )] = array(

        // Publish
        'gamipress_jetengine_publish_post_any_type' => __( 'Publish a post of any type', 'gamipress' ),
        'gamipress_jetengine_publish_post_specific_type' => __( 'Publish a post of specific type', 'gamipress' ),

        // Update
        'gamipress_jetengine_update_post_any_type'  => __( 'Update a post of any type', 'gamipress' ),
        'gamipress_jetengine_update_post_specific_type'  => __( 'Update a post of specific type', 'gamipress' ),

        // Delete
        'gamipress_jetengine_delete_post_any_type'  => __( 'Delete a post of any type', 'gamipress' ),
        'gamipress_jetengine_delete_post_specific_type'  => __( 'Delete a post of specific type', 'gamipress' ),

    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_jetengine_activity_triggers' );

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
function gamipress_surecart_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $JE_post_type = ( isset( $requirement['jetengine_post_type'] ) ) ? $requirement['jetengine_post_type'] : '';

    // Get the JetEngine post types
    $post_types_obj = new Jet_Engine_CPT;
    $post_types = $post_types_obj->get_items();

    foreach ( $post_types as $post_type ) {
        if ( $post_type['slug'] === $JE_post_type ) {
            $post_type_name = $post_type['labels']['name'];
            break;
        }
    }

    switch( $requirement['trigger_type'] ) {
        case 'gamipress_jetengine_publish_post_specific_type':
            return sprintf( __( 'Publish a post of %s type', 'gamipress-surecart-integration' ), $post_type_name );
            break;
        case 'gamipress_jetengine_update_post_specific_type':
            return sprintf( __( 'Update a post of %s type', 'gamipress-surecart-integration' ), $post_type_name );
            break;
        case 'gamipress_jetengine_delete_post_specific_type':
            return sprintf( __( 'Delete a post of %s type', 'gamipress-surecart-integration' ), $post_type_name );
            break;
        
    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_surecart_activity_trigger_label', 10, 3 );


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
function gamipress_jetengine_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_jetengine_publish_post_any_type':
        case 'gamipress_jetengine_publish_post_specific_type':
        case 'gamipress_jetengine_update_post_any_type':
        case 'gamipress_jetengine_update_post_specific_type':
        case 'gamipress_jetengine_delete_post_any_type':
        case 'gamipress_jetengine_delete_post_specific_type':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}

add_filter( 'gamipress_trigger_get_user_id', 'gamipress_jetengine_trigger_get_user_id', 10, 3);

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.1
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_jetengine_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_jetengine_publish_post_any_type':
        case 'gamipress_jetengine_publish_post_specific_type':
        case 'gamipress_jetengine_update_post_any_type':
        case 'gamipress_jetengine_update_post_specific_type':
        case 'gamipress_jetengine_delete_post_any_type':
        case 'gamipress_jetengine_delete_post_specific_type':
            // Add the post type
            $log_meta['post_type'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_jetengine_log_event_trigger_meta_data', 10, 5 );

