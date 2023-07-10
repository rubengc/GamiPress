<?php
/**
 * Requirements
 *
 * @package GamiPress\BuddyBoss\Requirements
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add the custom fields to the requirement object
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_buddyboss_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] ) ) {

        if( $requirement['trigger_type'] === 'gamipress_buddyboss_profile_progress' ) {

            // The percentage
            $requirement['buddyboss_percentage'] = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_buddyboss_percentage', true ) );

        }
    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_buddyboss_requirement_object', 10, 2 );

/**
 * Link fields on requirements UI
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_buddyboss_requirement_ui_fields( $requirement_id, $post_id ) {

    $buddyboss_percentage = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_buddyboss_percentage', true ) ); ?>

    <span class="buddyboss-percentage"><?php echo __( 'percentage required', 'gamipress' ); ?> <input type="number" class="input-buddyboss-percentage" value="<?php echo $buddyboss_percentage; ?>" placeholder="0" min="0" max="100" step="1">%</span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_buddyboss_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the custom fields on requirements UI
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_buddyboss_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] ) ) {

        if( $requirement['trigger_type'] === 'gamipress_buddyboss_profile_progress' ) {

            // The percentage
            update_post_meta( $requirement_id, '_gamipress_buddyboss_percentage', absint( $requirement['buddyboss_percentage'] ) );
        }

    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_buddyboss_ajax_update_requirement', 10, 2 );