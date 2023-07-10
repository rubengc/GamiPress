<?php
/**
 * Requirements
 *
 * @package GamiPress\WP_PostRatings\Requirements
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add the rate field to the requirement object
 *
 * @since 1.0.0
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_wp_postratings_requirement_object( $requirement, $requirement_id ) {

    // Specific rate
    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wp_postratings_rate_specific'
            || $requirement['trigger_type'] === 'gamipress_wp_postratings_specific_rate_specific'
            || $requirement['trigger_type'] === 'gamipress_wp_postratings_user_rate_specific'
            || $requirement['trigger_type'] === 'gamipress_wp_postratings_user_specific_rate_specific' ) ) {
        // The rate field
        $requirement['wp_postratings_rate'] = get_post_meta( $requirement_id, '_gamipress_wp_postratings_rate', true );
    }

    // Minimum rate
    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wp_postratings_minimum_rate'
            || $requirement['trigger_type'] === 'gamipress_wp_postratings_specific_minimum_rate'
            || $requirement['trigger_type'] === 'gamipress_wp_postratings_user_minimum_rate'
            || $requirement['trigger_type'] === 'gamipress_wp_postratings_user_specific_minimum_rate' ) ) {
        // The rate field
        $requirement['wp_postratings_rate'] = get_post_meta( $requirement_id, '_gamipress_wp_postratings_rate', true );
    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_wp_postratings_requirement_object', 10, 2 );

/**
 * Rate field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_wp_postratings_requirement_ui_fields( $requirement_id, $post_id ) {

    $value = intval( get_post_meta( $requirement_id, '_gamipress_wp_postratings_rate', true ) ); ?>

    <input type="text" class="input-wp-postratings-rate" value="<?php echo $value; ?>"/>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_wp_postratings_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the rate on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_wp_postratings_ajax_update_requirement( $requirement_id, $requirement ) {

    // Specific rate
    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wp_postratings_rate_specific'
            || $requirement['trigger_type'] === 'gamipress_wp_postratings_specific_rate_specific'
            || $requirement['trigger_type'] === 'gamipress_wp_postratings_user_rate_specific'
            || $requirement['trigger_type'] === 'gamipress_wp_postratings_user_specific_rate_specific' ) ) {
        // Save the rate field
        update_post_meta( $requirement_id, '_gamipress_wp_postratings_rate', $requirement['wp_postratings_rate'] );
    }

    // Minimum rate
    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wp_postratings_minimum_rate'
            || $requirement['trigger_type'] === 'gamipress_wp_postratings_specific_minimum_rate'
            || $requirement['trigger_type'] === 'gamipress_wp_postratings_user_minimum_rate'
            || $requirement['trigger_type'] === 'gamipress_wp_postratings_user_specific_minimum_rate' ) ) {
        // Save the rate field
        update_post_meta( $requirement_id, '_gamipress_wp_postratings_rate', $requirement['wp_postratings_rate'] );
    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_wp_postratings_ajax_update_requirement', 10, 2 );