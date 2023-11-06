<?php
/**
 * Functions
 *
 * @package GamiPress\armember\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_armember_ajax_get_posts() {

    global $wpdb;

    $results = array();

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? sanitize_text_field( $_REQUEST['q'] ) : '';
    $search = $wpdb->esc_like( $search );

    if( isset( $_REQUEST['post_type'] ) && in_array( 'armember_membership', $_REQUEST['post_type'] ) ) {

        // Get the membership plans
        $memberships = gamipress_armember_get_plan();

        foreach ( $memberships as $plan ) {

            // Results should meet same structure like posts
            $results[] = array(
                'ID' => $plan['id'],
                'post_title' => $plan['name'],
            );

        }

        // Return our results
        wp_send_json_success( $results );
        die;

    } 

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_armember_ajax_get_posts', 5 );

/**
 * Get Membership plan
 *
 * @since 1.0.0
 *
 * @param int $plan_id
 *
 * @return string|null
 */
function gamipress_armember_get_plan( ) {

    $membership_plans = array();

    if( class_exists( 'ARM_subscription_plans' ) ) {
		$obj_plans = new \ARM_subscription_plans();
	} else {
		$obj_plans = new \ARM_subscription_plans_Lite();
	}

    $all_plans = $obj_plans->arm_get_all_subscription_plans( 'arm_subscription_plan_id, arm_subscription_plan_name' );

    foreach ( $all_plans as $plan ){
    
        $membership_plans[] = array(
            'id' => $plan['arm_subscription_plan_id'],
            'name' => $plan['arm_subscription_plan_name'],
        );
        
    }

    return $membership_plans; 

}

// Get the membership plan title
function gamipress_armember_get_plan_title( $plan_id ) {

    // Empty title if no ID provided
    if( absint( $plan_id ) === 0 ) {
        return '';
    }

    if( class_exists( 'ARM_subscription_plans' ) ) {
		$obj_plans = new \ARM_subscription_plans();
	} else {
		$obj_plans = new \ARM_subscription_plans_Lite();
	}
    
    $plan_name = $obj_plans->arm_get_plan_name_by_id( $plan_id );

    return $plan_name;

}

