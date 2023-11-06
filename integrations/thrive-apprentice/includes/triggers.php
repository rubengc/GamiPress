<?php
/**
 * Triggers
 *
 * @package GamiPress\Thrive_Apprentice\Triggers
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
function gamipress_thrive_apprentice_activity_triggers( $triggers ) {

    $triggers[__( 'Thrive Apprentice', 'gamipress' )] = array(

        // Lessons
        'gamipress_thrive_apprentice_complete_lesson'             => __( 'Complete any lesson', 'gamipress' ),
        'gamipress_thrive_apprentice_complete_specific_lesson'    => __( 'Complete specific lesson', 'gamipress' ),

        // Modules
        'gamipress_thrive_apprentice_complete_module'             => __( 'Complete any module', 'gamipress' ),
        'gamipress_thrive_apprentice_complete_specific_module'    => __( 'Complete specific module', 'gamipress' ),

        // Courses
        'gamipress_thrive_apprentice_complete_course'             => __( 'Complete any course', 'gamipress' ),
        'gamipress_thrive_apprentice_complete_specific_course'    => __( 'Complete specific course', 'gamipress' ),

    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_thrive_apprentice_activity_triggers' );

/**
 * Register specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_thrive_apprentice_specific_activity_triggers( $specific_activity_triggers ) {
    
    $specific_activity_triggers['gamipress_thrive_apprentice_complete_specific_lesson'] = array( 'tva_lesson' );
    $specific_activity_triggers['gamipress_thrive_apprentice_complete_specific_module'] = array( 'tva_module' );
    $specific_activity_triggers['gamipress_thrive_apprentice_complete_specific_course'] = array( 'tva_courses_posts' );
    
    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_thrive_apprentice_specific_activity_triggers' );

/**
 * Register specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_thrive_apprentice_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    $specific_activity_trigger_labels['gamipress_thrive_apprentice_complete_specific_lesson'] = __( 'Complete %s lesson', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_thrive_apprentice_complete_specific_module'] = __( 'Complete %s module', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_thrive_apprentice_complete_specific_course'] = __( 'Complete %s course', 'gamipress' );
    
    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_thrive_apprentice_specific_activity_trigger_label' );

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
function gamipress_thrive_apprentice_specific_activity_trigger_post_title( $post_title, $specific_id, $trigger_type ) {

    switch( $trigger_type ) {

        case 'gamipress_thrive_apprentice_complete_specific_course':
            if( absint( $specific_id ) !== 0 ) {

                // Get the course title
                $course_title = gamipress_thrive_apprentice_get_course_title( $specific_id );

                $post_title = $course_title;
            }
            break;
    }

    return $post_title;

}
add_filter( 'gamipress_specific_activity_trigger_post_title', 'gamipress_thrive_apprentice_specific_activity_trigger_post_title', 10, 3 );

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
function gamipress_thrive_apprentice_specific_activity_trigger_permalink( $permalink, $specific_id, $trigger_type, $site_id ) {

    switch( $trigger_type ) {
        case 'gamipress_thrive_apprentice_complete_specific_course':
            $permalink = '';
            break;
    }

    return $permalink;

}
add_filter( 'gamipress_specific_activity_trigger_permalink', 'gamipress_thrive_apprentice_specific_activity_trigger_permalink', 10, 4 );


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

function gamipress_thrive_apprentice_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Lessons
        case 'gamipress_thrive_apprentice_complete_lesson':
        case 'gamipress_thrive_apprentice_complete_specific_lesson':
        // Modules
        case 'gamipress_thrive_apprentice_complete_module':
        case 'gamipress_thrive_apprentice_complete_specific_module':
        // Courses
        case 'gamipress_thrive_apprentice_complete_course':
        case 'gamipress_thrive_apprentice_complete_specific_course':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_thrive_apprentice_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $specific_id Specific ID to override.
 * @param  string  $trigger     Trigger name.
 * @param  array   $args        Passed trigger args.
 *
 * @return integer              Specific ID.
 */
function gamipress_thrive_apprentice_specific_trigger_get_id( $specific_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_thrive_apprentice_complete_specific_lesson':
        case 'gamipress_thrive_apprentice_complete_specific_module':
        case 'gamipress_thrive_apprentice_complete_specific_course':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;

}

add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_thrive_apprentice_specific_trigger_get_id', 10, 3 );

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
function gamipress_thrive_apprentice_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {

        // Lessons
        case 'gamipress_thrive_apprentice_complete_lesson':
        case 'gamipress_thrive_apprentice_complete_specific_lesson':
            // Add the lesson ID
            $log_meta['lesson_id'] = $args[0];
            break;

        // Modules
        case 'gamipress_thrive_apprentice_complete_module':
        case 'gamipress_thrive_apprentice_complete_specific_module':
            // Add the module ID
            $log_meta['module_id'] = $args[0];
            break;
        // Courses
        case 'gamipress_thrive_apprentice_complete_course':
        case 'gamipress_thrive_apprentice_complete_specific_course':
            // Add the Course ID
            $log_meta['course_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_thrive_apprentice_log_event_trigger_meta_data', 10, 5 );