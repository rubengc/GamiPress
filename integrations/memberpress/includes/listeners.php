<?php
/**
 * Listeners
 *
 * @package GamiPress\MemberPress\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Purchase listener
 *
 * @since 1.0.0
 *
 * @param object $event transaction object.
 */
function gamipress_memberpress_purchase_listener( $event ) {

    $subscription = $event->get_data();

    $product_id = intval( $subscription->rec->product_id );
    $user_id = intval( $subscription->rec->user_id );

    // Trigger product purchase
    do_action( 'gamipress_memberpress_product_purchase', $product_id, $user_id );

    // Trigger specific product purchase
    do_action( 'gamipress_memberpress_specific_product_purchase', $product_id, $user_id );

}
add_action( 'mepr-event-non-recurring-transaction-completed', 'gamipress_memberpress_purchase_listener' );
add_action( 'mepr-event-recurring-transaction-completed', 'gamipress_memberpress_purchase_listener' );

/**
 * Lifetime Purchase listener
 *
 * @since 1.0.0
 *
 * @param object $event transaction object.
 */
function gamipress_memberpress_lifetime_purchase_listener( $event ) {

    $subscription = $event->get_data();

    $product_id = intval( $subscription->rec->product_id );
    $user_id = intval( $subscription->rec->user_id );

    // Trigger lifetime product purchase
    do_action( 'gamipress_memberpress_lifetime_product_purchase', $product_id, $user_id );
    do_action( 'gamipress_memberpress_specific_lifetime_product_purchase', $product_id, $user_id );

}
add_action( 'mepr-event-non-recurring-transaction-completed', 'gamipress_memberpress_lifetime_purchase_listener' );

/**
 * Subscription status listener
 *
 * @since 1.0.0
 *
 * @param string $old_status    Old status object.
 * @param string $new_status    New status.
 * @param object $subscription  Subscription object.
 */
function gamipress_memberpress_subscription_status_listener( $old_status, $new_status, $subscription ) {

    $old_status = (string) $old_status;
    $new_status = (string) $new_status;

    if( $old_status === $new_status ) {
        return;
    }

    $product_id = intval( $subscription->rec->product_id );
    $user_id = intval( $subscription->rec->user_id );

    if( $new_status === 'cancelled' ) {
        do_action( 'gamipress_memberpress_product_cancelled', $product_id, $user_id );
        do_action( 'gamipress_memberpress_specific_product_cancelled', $product_id, $user_id );
    } else if( $new_status === 'suspended' ) {
        do_action( 'gamipress_memberpress_product_suspended', $product_id, $user_id );
        do_action( 'gamipress_memberpress_specific_product_suspended', $product_id, $user_id );
    }

}
add_action( 'mepr_subscription_transition_status', 'gamipress_memberpress_subscription_status_listener', 10, 3 );

/**
 * Recurring Purchase listener
 *
 * @since 1.0.0
 *
 * @param object $event transaction object.
 */
function gamipress_memberpress_recurring_purchase_listener( $event ) {

    $subscription = $event->get_data();

    $product_id = intval( $subscription->rec->product_id );
    $user_id = intval( $subscription->rec->user_id );

    do_action( 'gamipress_memberpress_recurring_product_purchase', $product_id, $user_id );
    do_action( 'gamipress_memberpress_specific_recurring_product_purchase', $product_id, $user_id );

}
add_action( 'mepr-event-recurring-transaction-completed', 'gamipress_memberpress_recurring_purchase_listener' );

/**
 * Complete lesson listener
 *
 * @since 1.0.0
 *
 * @param memberpress\courses\models\UserProgress $user_progress
 */
function gamipress_memberpress_complete_lesson_listener( $user_progress ) {

    $lesson_id = $user_progress->lesson_id;
    $course_id = $user_progress->course_id;
    $user_id = $user_progress->user_id;

    do_action( 'gamipress_memberpress_complete_lesson', $lesson_id, $user_id, $course_id );
    do_action( 'gamipress_memberpress_complete_specific_lesson', $lesson_id, $user_id, $course_id );
    do_action( 'gamipress_memberpress_complete_lesson_specific_course', $lesson_id, $user_id, $course_id );

}
add_action( 'mpcs_completed_lesson', 'gamipress_memberpress_complete_lesson_listener' );

/**
 * Start course listener
 *
 * @since 1.0.0
 *
 * @param memberpress\courses\models\UserProgress $user_progress
 */
function gamipress_memberpress_start_course_listener( $user_progress ) {

    $course_id = $user_progress->course_id;
    $user_id = $user_progress->user_id;

    do_action( 'gamipress_memberpress_start_course', $course_id, $user_id );
    do_action( 'gamipress_memberpress_start_specific_course', $course_id, $user_id );

}
add_action( 'mpcs_started_course', 'gamipress_memberpress_start_course_listener' );

/**
 * Complete course listener
 *
 * @since 1.0.0
 *
 * @param memberpress\courses\models\UserProgress $user_progress
 */
function gamipress_memberpress_complete_course_listener( $user_progress ) {

    $course_id = $user_progress->course_id;
    $user_id = $user_progress->user_id;

    do_action( 'gamipress_memberpress_complete_course', $course_id, $user_id );
    do_action( 'gamipress_memberpress_complete_specific_course', $course_id, $user_id );

}
add_action( 'mpcs_completed_course', 'gamipress_memberpress_complete_course_listener' );