<?php
/**
 * Requirements
 *
 * @package GamiPress\WP_Job_Manager\Requirements
 * @since   1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add custom fields to the requirement object
 *
 * @since 1.0.0
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_wp_job_manager_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && in_array( $requirement['trigger_type'], array(
            'gamipress_wp_job_manager_publish_job_specific_type',
            'gamipress_wp_job_manager_mark_filled_specific_type',
            'gamipress_wp_job_manager_mark_not_filled_specific_type',
            // Applications
            'gamipress_wp_job_manager_job_application_specific_type',
            'gamipress_wp_job_manager_get_job_application_specific_type',
            'gamipress_wp_job_manager_job_application_hired_specific_type',
            'gamipress_wp_job_manager_job_application_rejected_specific_type',
        ) ) ) {
        // Type
        $requirement['wp_job_manager_type_id'] = gamipress_get_post_meta( $requirement_id, '_gamipress_wp_job_manager_type_id', true );
    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_wp_job_manager_requirement_object', 10, 2 );

/**
 * Custom fields on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_wp_job_manager_requirement_ui_fields( $requirement_id, $post_id ) {

    // Type select
    $type_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_wp_job_manager_type_id', true ) );
    $types = get_terms( array(
        'taxonomy' => 'job_listing_type',
        'hide_empty' => false,
    ) );

    ?>

    <span class="wp-job-manager-type">
        <select>
            <?php foreach( $types as $type ) : ?>
                <option value="<?php echo $type->term_id; ?>" <?php selected( $type_id, $type->term_id ); ?>><?php echo $type->name; ?></option>
            <?php endforeach; ?>
        </select>
    </span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_wp_job_manager_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save custom fields on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_wp_job_manager_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && in_array( $requirement['trigger_type'], array(
            'gamipress_wp_job_manager_publish_job_specific_type',
            'gamipress_wp_job_manager_mark_filled_specific_type',
            'gamipress_wp_job_manager_mark_not_filled_specific_type',
            // Applications
            'gamipress_wp_job_manager_job_application_specific_type',
            'gamipress_wp_job_manager_get_job_application_specific_type',
            'gamipress_wp_job_manager_job_application_hired_specific_type',
            'gamipress_wp_job_manager_job_application_rejected_specific_type',
        ) ) ) {
        // Save the type field
        gamipress_update_post_meta( $requirement_id, '_gamipress_wp_job_manager_type_id', $requirement['wp_job_manager_type_id'] );
    }

}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_wp_job_manager_ajax_update_requirement', 10, 2 );