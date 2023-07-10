<?php
/**
 * Rules Engine
 *
 * @package GamiPress\WooCommerce\Rules_Engine
 * @since 1.1.3
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Checks if given requirement meets the requirements of triggered event
 *
 * @since 1.2.5
 *
 * @param int 	    $requirement_id
 * @param string 	$trigger
 * @param array 	$args
 *
 * @return bool
 */
function gamipress_wc_check_if_meets_requirements( $requirement_id, $trigger, $args ) {

    // Initialize the return value
    $return = true;

    // If is purchase total trigger, rules engine needs to check the condition
    if( $trigger === 'gamipress_wc_new_purchase_total' ) {

        $order_total = floatval( $args[2] );

        $condition = gamipress_get_post_meta( $requirement_id, '_gamipress_wc_purchase_total_condition', true );
        $required_value = floatval( gamipress_get_post_meta( $requirement_id, '_gamipress_wc_purchase_total', true ) );

        // True if purchase total matches the condition
        $return = (bool) ( gamipress_number_condition_matches( $order_total, $required_value, $condition ) );
    }

    // If is product variation trigger, rules engine needs to check the variation ID
    if( $trigger === 'gamipress_wc_product_variation_purchase' || $trigger === 'gamipress_wc_product_variation_refund' ) {

        $variation_id = absint( $args[2] );
        $required_variation_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_wc_variation_id', true ) );

        // True if is the correct variation ID
        $return = (bool) ( $variation_id === $required_variation_id );
    }

    // If is product category trigger, rules engine needs to check the category ID
    if( $trigger === 'gamipress_wc_product_category_purchase' || $trigger === 'gamipress_wc_product_category_refund' ) {

        $category_id = absint( $args[2] );
        $required_category_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_wc_category_id', true ) );

        // True if is the correct category ID
        $return = (bool) ( $required_category_id === $category_id );
    }

    // If is product tag trigger, rules engine needs to check the tag ID
    if( $trigger === 'gamipress_wc_product_tag_purchase' || $trigger === 'gamipress_wc_product_tag_refund' ) {

        $tag_id = absint( $args[2] );
        $required_tag_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_wc_tag_id', true ) );

        // True if is the correct tag ID
        $return = (bool) ( $required_tag_id === $tag_id );
    }

    // If is lifetime value trigger, rules engine needs to check the condition
    if( $trigger === 'gamipress_wc_lifetime_value' ) {

        $lifetime = floatval( $args[0] );

        $condition = gamipress_get_post_meta( $requirement_id, '_gamipress_wc_lifetime_condition', true );
        $required_value = floatval( gamipress_get_post_meta( $requirement_id, '_gamipress_wc_lifetime', true ) );

        // True if lifetime value matches condition
        $return = (bool) ( gamipress_number_condition_matches( $lifetime, $required_value, $condition ) );
    }

    return $return;

}

/**
 * Filter triggered requirements to reduce the number of requirements to check by the awards engine
 *
 * @since 1.2.5
 *
 * @param array 	$triggered_requirements
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_wc_filter_triggered_requirements( $triggered_requirements, $user_id, $trigger, $site_id, $args ) {

    $new_requirements = array();

    foreach( $triggered_requirements as $i => $requirement ) {

        // Skip item
        if( ! gamipress_wc_check_if_meets_requirements( $requirement->ID, $trigger, $args ) ) {
            continue;
        }

        // Keep the requirement on the list of requirements to check by the awards engine
        $new_requirements[] = $requirement;

    }

    return $new_requirements;

}
add_filter( 'gamipress_get_triggered_requirements', 'gamipress_wc_filter_triggered_requirements', 20, 5 );


/**
 * Checks if an user is allowed to work on a given requirement
 *
 * @since  1.1.3
 *
 * @param bool $return          The default return value
 * @param int $user_id          The given user's ID
 * @param int $requirement_id   The given requirement's post ID
 * @param string $trigger       The trigger triggered
 * @param int $site_id          The site id
 * @param array $args           Arguments of this trigger
 *
 * @return bool True if user has access to the requirement, false otherwise
 */
function gamipress_wc_user_has_access_to_achievement( $return = false, $user_id = 0, $requirement_id = 0, $trigger = '', $site_id = 0, $args = array() ) {

    // If we're not working with a requirement, bail here
    if ( ! in_array( get_post_type( $requirement_id ), gamipress_get_requirement_types_slugs() ) )
        return $return;

    // Check if user has access to the achievement ($return will be false if user has exceed the limit or achievement is not published yet)
    if( ! $return )
        return $return;

    // Send back our eligibility
    return gamipress_wc_check_if_meets_requirements( $requirement_id, $trigger, $args );
}
add_filter( 'user_has_access_to_achievement', 'gamipress_wc_user_has_access_to_achievement', 10, 6 );