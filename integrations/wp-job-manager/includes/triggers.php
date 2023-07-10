<?php
/**
 * Triggers
 *
 * @package GamiPress\WP_Job_Manager\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin activity triggers
 *
 * @since   1.0.0
 *
 * @param   array $triggers
 * @return  mixed
 */
function gamipress_wp_job_manager_activity_triggers( $triggers ) {

    $triggers[__( 'WP Job Manager', 'gamipress' )] = array(
        'gamipress_publish_job_listing'                                     => __( 'Publish a job', 'gamipress' ),
        'gamipress_wp_job_manager_publish_job_specific_type'                => __( 'Publish a job of a specific type', 'gamipress' ),
        'gamipress_wp_job_manager_mark_filled'                              => __( 'Mark a job of as filled', 'gamipress' ),
        'gamipress_wp_job_manager_mark_filled_specific_type'                => __( 'Mark a job of a specific type of as filled', 'gamipress' ),
        'gamipress_wp_job_manager_mark_not_filled'                          => __( 'Mark a job of as not filled', 'gamipress' ),
        'gamipress_wp_job_manager_mark_not_filled_specific_type'            => __( 'Mark a job of a specific type of as not filled', 'gamipress' ),
        // Applications
        'gamipress_wp_job_manager_job_application'                          => __( 'Apply to job', 'gamipress' ),
        'gamipress_wp_job_manager_job_application_specific_type'            => __( 'Apply to job of a specific type', 'gamipress' ),
        'gamipress_wp_job_manager_get_job_application'                      => __( 'Receive an application', 'gamipress' ),
        'gamipress_wp_job_manager_get_job_application_specific_type'        => __( 'Receive an application on a job of a specific type', 'gamipress' ),
        'gamipress_wp_job_manager_job_application_hired'                    => __( 'Get hired on a job', 'gamipress' ),
        'gamipress_wp_job_manager_job_application_hired_specific_type'      => __( 'Get hired on a job of a specific type', 'gamipress' ),
        'gamipress_wp_job_manager_job_application_rejected'                 => __( 'Get an application rejected', 'gamipress' ),
        'gamipress_wp_job_manager_job_application_rejected_specific_type'   => __( 'Get an application rejected on a job of a specific type', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_wp_job_manager_activity_triggers' );

/**
 * Build custom activity trigger label
 *
 * @param string    $title
 * @param integer   $requirement_id
 * @param array     $requirement
 *
 * @return string
 */
function gamipress_wp_job_manager_activity_trigger_label( $title, $requirement_id, $requirement ) {

    switch( $requirement['trigger_type'] ) {
        // Type
        case 'gamipress_wp_job_manager_publish_job_specific_type':
        case 'gamipress_wp_job_manager_mark_filled_specific_type':
        case 'gamipress_wp_job_manager_mark_not_filled_specific_type':
        // Applications
        case 'gamipress_wp_job_manager_job_application_specific_type':
        case 'gamipress_wp_job_manager_get_job_application_specific_type':
        case 'gamipress_wp_job_manager_job_application_hired_specific_type':
        case 'gamipress_wp_job_manager_job_application_rejected_specific_type':
            $type_id = ( isset( $requirement['wp_job_manager_type_id'] ) ) ? absint( $requirement['wp_job_manager_type_id'] ) : 0;

            if( $type_id !== 0 ) {

                // Setup the pattern based on trigger type given
                switch( $requirement['trigger_type'] ) {
                    case 'gamipress_wp_job_manager_mark_filled_specific_type':
                        $pattern = __( 'Mark a job of "%s" type as filled', 'gamipress' );
                        break;
                    case 'gamipress_wp_job_manager_mark_not_filled_specific_type':
                        $pattern = __( 'Mark a job of "%s" type as not filled', 'gamipress' );
                        break;
                    case 'gamipress_wp_job_manager_job_application_specific_type':
                        $pattern = __( 'Apply to job of "%s" type', 'gamipress' );
                        break;
                    case 'gamipress_wp_job_manager_get_job_application_specific_type':
                        $pattern = __( 'Receive an application on a job of "%s" type', 'gamipress' );
                        break;
                    case 'gamipress_wp_job_manager_job_application_hired_specific_type':
                        $pattern = __( 'Get hired on a job of "%s" type', 'gamipress' );
                        break;
                    case 'gamipress_wp_job_manager_job_application_rejected_specific_type':
                        $pattern = __( 'Get an application rejected on a job of "%s" type', 'gamipress' );
                        break;
                    default:
                        $pattern = __( 'Publish a job of "%s" type', 'gamipress' );
                        break;
                }

                $type = get_term_by( 'term_id', $type_id, 'job_listing_type' );

                // Return the custom title
                return sprintf( $pattern, $type->name );
            }
            break;

    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_wp_job_manager_activity_trigger_label', 10, 3 );

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
function gamipress_wp_job_manager_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_publish_job_listing': // Internal GamiPress listener
        case 'gamipress_wp_job_manager_publish_job_specific_type':
        case 'gamipress_wp_job_manager_mark_filled':
        case 'gamipress_wp_job_manager_mark_filled_specific_type':
        case 'gamipress_wp_job_manager_mark_not_filled':
        case 'gamipress_wp_job_manager_mark_not_filled_specific_type':
        // Applications
        case 'gamipress_wp_job_manager_job_application':
        case 'gamipress_wp_job_manager_job_application_specific_type':
        case 'gamipress_wp_job_manager_get_job_application':
        case 'gamipress_wp_job_manager_get_job_application_specific_type':
        case 'gamipress_wp_job_manager_job_application_hired':
        case 'gamipress_wp_job_manager_job_application_hired_specific_type':
        case 'gamipress_wp_job_manager_job_application_rejected':
        case 'gamipress_wp_job_manager_job_application_rejected_specific_type':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_wp_job_manager_trigger_get_user_id', 10, 3 );

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
function gamipress_wp_job_manager_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_publish_job_listing': // Internal GamiPress listener
        case 'gamipress_wp_job_manager_mark_filled':
        case 'gamipress_wp_job_manager_mark_not_filled':
            // Add the post ID
            $log_meta['post_id'] = $args[0];
            break;
        case 'gamipress_wp_job_manager_publish_job_specific_type':
        case 'gamipress_wp_job_manager_mark_filled_specific_type':
        case 'gamipress_wp_job_manager_mark_not_filled_specific_type':
            // Add the post and term IDs
            $log_meta['post_id'] = $args[0];
            $log_meta['term_id'] = $args[2];
            break;
        // Applications
        case 'gamipress_wp_job_manager_job_application':
        case 'gamipress_wp_job_manager_job_application_hired':
        case 'gamipress_wp_job_manager_job_application_rejected':
            // Add the post and application IDs
            $log_meta['post_id'] = $args[0];
            $log_meta['application_id'] = $args[2];
            break;
        case 'gamipress_wp_job_manager_job_application_specific_type':
        case 'gamipress_wp_job_manager_job_application_hired_specific_type':
        case 'gamipress_wp_job_manager_job_application_rejected_specific_type':
            // Add the post, term and application IDs
            $log_meta['post_id'] = $args[0];
            $log_meta['term_id'] = $args[2];
            $log_meta['application_id'] = $args[3];
            break;
        case 'gamipress_wp_job_manager_get_job_application':
            // Add the post, candidate and application IDs
            $log_meta['post_id'] = $args[0];
            $log_meta['application_id'] = $args[2];
            $log_meta['candidate_id'] = $args[3];
            break;
        case 'gamipress_wp_job_manager_get_job_application_specific_type':
            // Add the post, candidate and application IDs
            $log_meta['post_id'] = $args[0];
            $log_meta['term_id'] = $args[2];
            $log_meta['application_id'] = $args[3];
            $log_meta['candidate_id'] = $args[4];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_wp_job_manager_log_event_trigger_meta_data', 10, 5 );

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
function gamipress_wp_job_manager_get_user_trigger_count_log_meta( $log_meta, $user_id, $trigger, $since, $site_id, $args ) {

    switch( $trigger ) {
        // Type
        case 'gamipress_wp_job_manager_publish_job_specific_type':
        case 'gamipress_wp_job_manager_mark_filled_specific_type':
        case 'gamipress_wp_job_manager_mark_not_filled_specific_type':
        // Applications
        case 'gamipress_wp_job_manager_job_application_specific_type':
        case 'gamipress_wp_job_manager_get_job_application_specific_type':
        case 'gamipress_wp_job_manager_job_application_hired_specific_type':
        case 'gamipress_wp_job_manager_job_application_rejected_specific_type':
            if( isset( $args[2] ) ) {
                // Add the type ID
                $log_meta['term_id'] = $args[2];
            }

            // $args could be a requirement object
            if( isset( $args['wp_job_manager_type_id'] ) )
                $log_meta['term_id'] = $args['wp_job_manager_type_id'];
            break;
    }

    return $log_meta;

}
add_filter( 'gamipress_get_user_trigger_count_log_meta', 'gamipress_wp_job_manager_get_user_trigger_count_log_meta', 10, 6 );

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
function gamipress_wp_job_manager_log_extra_data_fields( $fields, $log_id, $type ) {

    $prefix = '_gamipress_';

    $log = ct_get_object( $log_id );
    $trigger = $log->trigger_type;

    if( $type !== 'event_trigger' ) {
        return $fields;
    }

    switch( $trigger ) {
        // Type
        case 'gamipress_wp_job_manager_publish_job_specific_type':
        case 'gamipress_wp_job_manager_mark_filled_specific_type':
        case 'gamipress_wp_job_manager_mark_not_filled_specific_type':
        // Applications
        case 'gamipress_wp_job_manager_job_application_specific_type':
        case 'gamipress_wp_job_manager_get_job_application_specific_type':
        case 'gamipress_wp_job_manager_job_application_hired_specific_type':
        case 'gamipress_wp_job_manager_job_application_rejected_specific_type':

            // Get types stored and turn them into an array of options
            $types = get_terms( array(
                'taxonomy' => 'job_listing_type',
                'hide_empty' => false,
            ) );

            $options = array();

            foreach( $types as $type ) {
                $options[$type->term_id] = $type->name;
            }

            $fields[] = array(
                'name' 	            => __( 'Type', 'gamipress' ),
                'desc' 	            => __( 'Job type attached to this log.', 'gamipress' ),
                'id'   	            => $prefix . 'term_id',
                'type' 	            => 'select',
                'options'           => $options
            );
            break;
    }

    return $fields;

}
add_filter( 'gamipress_log_extra_data_fields', 'gamipress_wp_job_manager_log_extra_data_fields', 10, 3 );

/**
 * Extra filter to check duplicated activity
 *
 * @since 1.0.0
 *
 * @param bool 		$return
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return bool					True if user deserves trigger, else false
 */
function gamipress_wp_job_manager_trigger_duplicity_check( $return, $user_id, $trigger, $site_id, $args  ) {

    // If user doesn't deserves trigger, then bail to prevent grant access
    if( ! $return )
        return $return;

    $log_meta = array(
        'type' => 'event_trigger',
        'trigger_type' => $trigger,
    );

    switch ( $trigger ) {
        case 'gamipress_publish_job_listing': // Internal GamiPress listener
        case 'gamipress_wp_job_manager_publish_job_specific_type':
            // Prevent duplicate job publishing
            $log_meta['post_id'] = $args[0];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
    }

    return $return;

}
add_filter( 'gamipress_user_deserves_trigger', 'gamipress_wp_job_manager_trigger_duplicity_check', 10, 5 );