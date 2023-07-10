<?php
/**
 * Requirements
 *
 * @package GamiPress\Give\Requirements
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add the amount field to the requirement object
 *
 * @since 1.0.0
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_give_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_give_new_donation_min_amount' ) ) {

        // Minimum grade percent
        $requirement['give_amount'] = get_post_meta( $requirement_id, '_gamipress_give_amount', true );

    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_give_requirement_object', 10, 2 );

/**
 * Amount field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_give_requirement_ui_fields( $requirement_id, $post_id ) {

    $amount = absint( get_post_meta( $requirement_id, '_gamipress_give_amount', true ) );
    ?>

    <span class="give-amount"><input type="number" value="<?php echo $amount; ?>" placeholder="100" /></span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_give_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the amount on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_give_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_give_new_donation_min_amount' ) ) {

        // Save the amount field
        update_post_meta( $requirement_id, '_gamipress_give_amount', $requirement['give_amount'] );

    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_give_ajax_update_requirement', 10, 2 );