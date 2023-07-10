<?php
/**
 * Triggers
 *
 * @package GamiPress\MemberPress\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register MemberPress activity triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_memberpress_activity_triggers( $triggers ) {

    $triggers[__( 'MemberPress', 'gamipress' )] = array(
        'gamipress_memberpress_product_purchase'                    => __( 'Purchase any subscription product', 'gamipress' ),
        'gamipress_memberpress_specific_product_purchase'           => __( 'Purchase a specific subscription product', 'gamipress' ),
        'gamipress_memberpress_lifetime_product_purchase'           => __( 'Purchase any one-time subscription product', 'gamipress' ),
        'gamipress_memberpress_specific_lifetime_product_purchase'  => __( 'Purchase a specific one-time subscription product', 'gamipress' ),
        'gamipress_memberpress_recurring_product_purchase'          => __( 'Purchase any recurring subscription product', 'gamipress' ),
        'gamipress_memberpress_specific_recurring_product_purchase' => __( 'Purchase a specific recurring subscription product', 'gamipress' ),
        'gamipress_memberpress_product_cancelled'                   => __( 'Cancel a subscription product', 'gamipress' ),
        'gamipress_memberpress_specific_product_cancelled'          => __( 'Cancel a specific subscription product', 'gamipress' ),
        'gamipress_memberpress_product_suspended'                   => __( 'Suspend a subscription product', 'gamipress' ),
        'gamipress_memberpress_specific_product_suspended'          => __( 'Suspend a specific subscription product', 'gamipress' ),
    );

    $triggers[__( 'MemberPress Courses', 'gamipress' )] = array(
        'gamipress_memberpress_complete_lesson'                     => __( 'Complete any lesson', 'gamipress' ),
        'gamipress_memberpress_complete_specific_lesson'            => __( 'Complete a specific lesson', 'gamipress' ),
        'gamipress_memberpress_complete_lesson_specific_course'     => __( 'Complete a lesson of a specific course', 'gamipress' ),
        'gamipress_memberpress_start_course'                        => __( 'Start a course', 'gamipress' ),
        'gamipress_memberpress_start_specific_course'               => __( 'Start a specific course', 'gamipress' ),
        'gamipress_memberpress_complete_course'                     => __( 'Complete a course', 'gamipress' ),
        'gamipress_memberpress_complete_specific_course'            => __( 'Complete a specific course', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_memberpress_activity_triggers' );

/**
 * Register MemberPress specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_memberpress_specific_activity_triggers( $specific_activity_triggers ) {

    // Purchase
    $specific_activity_triggers['gamipress_memberpress_specific_product_purchase'] = array( 'memberpressproduct' );
    $specific_activity_triggers['gamipress_memberpress_specific_lifetime_product_purchase'] = array( 'memberpressproduct' );
    $specific_activity_triggers['gamipress_memberpress_specific_recurring_product_purchase'] = array( 'memberpressproduct' );
    $specific_activity_triggers['gamipress_memberpress_specific_product_cancelled'] = array( 'memberpressproduct' );
    $specific_activity_triggers['gamipress_memberpress_specific_product_suspended'] = array( 'memberpressproduct' );
    $specific_activity_triggers['gamipress_memberpress_complete_specific_lesson'] = array( 'mpcs-lesson' );
    $specific_activity_triggers['gamipress_memberpress_complete_lesson_specific_course'] = array( ' mpcs-course' );
    $specific_activity_triggers['gamipress_memberpress_start_specific_course'] = array( ' mpcs-course' );
    $specific_activity_triggers['gamipress_memberpress_complete_specific_course'] = array( ' mpcs-course' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_memberpress_specific_activity_triggers' );

/**
 * Register MemberPress specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_memberpress_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Purchase
    $specific_activity_trigger_labels['gamipress_memberpress_specific_product_purchase'] = __( 'Purchase %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_memberpress_specific_lifetime_product_purchase'] = __( 'Purchase %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_memberpress_specific_recurring_product_purchase'] = __( 'Purchase %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_memberpress_specific_product_cancelled'] = __( 'Cancel %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_memberpress_specific_product_suspended'] = __( 'Suspend %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_memberpress_complete_specific_lesson'] = __( 'Complete %s lesson', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_memberpress_complete_lesson_specific_course'] = __( 'Complete a lesson of %s course', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_memberpress_start_specific_course'] = __( 'Start %s course', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_memberpress_complete_specific_course'] = __( 'Complete %s course', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_memberpress_specific_activity_trigger_label' );

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
function gamipress_memberpress_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Purchase
        case 'gamipress_memberpress_product_purchase':
        case 'gamipress_memberpress_specific_product_purchase':
        case 'gamipress_memberpress_lifetime_product_purchase':
        case 'gamipress_memberpress_specific_lifetime_product_purchase':
        case 'gamipress_memberpress_recurring_product_purchase':
        case 'gamipress_memberpress_specific_recurring_product_purchase':
        case 'gamipress_memberpress_product_cancelled':
        case 'gamipress_memberpress_specific_product_cancelled':
        case 'gamipress_memberpress_product_suspended':
        case 'gamipress_memberpress_specific_product_suspended':
        // Courses
        case 'gamipress_memberpress_complete_lesson':
        case 'gamipress_memberpress_complete_specific_lesson':
        case 'gamipress_memberpress_complete_lesson_specific_course':
        case 'gamipress_memberpress_start_course':
        case 'gamipress_memberpress_start_specific_course':
        case 'gamipress_memberpress_complete_course':
        case 'gamipress_memberpress_complete_specific_course':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_memberpress_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.1
 *
 * @param  integer $specific_id Specific ID to override.
 * @param  string  $trigger     Trigger name.
 * @param  array   $args        Passed trigger args.
 *
 * @return integer              Specific ID.
 */
function gamipress_memberpress_specific_trigger_get_id( $specific_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Purchase
        case 'gamipress_memberpress_specific_product_purchase':
        case 'gamipress_memberpress_specific_lifetime_product_purchase':
        case 'gamipress_memberpress_specific_recurring_product_purchase':
        case 'gamipress_memberpress_specific_product_cancelled':
        case 'gamipress_memberpress_specific_product_suspended':
        case 'gamipress_memberpress_complete_specific_lesson':
        case 'gamipress_memberpress_start_specific_course':
        case 'gamipress_memberpress_complete_specific_course':
            $specific_id = $args[0];
            break;
        case 'gamipress_memberpress_complete_lesson_specific_course':
            $specific_id = $args[2];
            break;
    }

    return $specific_id;

}

add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_memberpress_specific_trigger_get_id', 10, 3 );

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
function gamipress_memberpress_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Purchase
        case 'gamipress_memberpress_product_purchase':
        case 'gamipress_memberpress_specific_product_purchase':
        case 'gamipress_memberpress_lifetime_product_purchase':
        case 'gamipress_memberpress_specific_lifetime_product_purchase':
        case 'gamipress_memberpress_recurring_product_purchase':
        case 'gamipress_memberpress_specific_recurring_product_purchase':
        case 'gamipress_memberpress_product_cancelled':
        case 'gamipress_memberpress_specific_product_cancelled':
        case 'gamipress_memberpress_product_suspended':
        case 'gamipress_memberpress_specific_product_suspended':
            // Add the product and order IDs
            $log_meta['product_id'] = $args[0];
            $log_meta['post_id'] = $args[0];
            break;
        // Courses
        case 'gamipress_memberpress_complete_lesson':
        case 'gamipress_memberpress_complete_specific_lesson':
            // Add the lesson and course IDs
            $log_meta['lesson_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            $log_meta['post_id'] = $args[0];
            break;
        case 'gamipress_memberpress_complete_lesson_specific_course':
            // Add the lesson and course IDs
            $log_meta['lesson_id'] = $args[0];
            $log_meta['course_id'] = $args[2];
            $log_meta['post_id'] = $args[2];
            break;
        case 'gamipress_memberpress_start_course':
        case 'gamipress_memberpress_start_specific_course':
        case 'gamipress_memberpress_complete_course':
        case 'gamipress_memberpress_complete_specific_course':
            // Add the course ID
            $log_meta['course_id'] = $args[0];
            $log_meta['post_id'] = $args[0];
            break;
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_memberpress_log_event_trigger_meta_data', 10, 5 );