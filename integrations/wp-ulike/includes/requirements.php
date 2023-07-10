<?php
/**
 * Requirements
 *
 * @package GamiPress\WP_Ulike\Requirements
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add custom fields to the requirement object
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_wp_ulike_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wp_ulike_post_type_like'
            || $requirement['trigger_type'] === 'gamipress_wp_ulike_get_post_type_like' ) ) {

        // Post type
        $requirement['wp_ulike_post_type'] = get_post_meta( $requirement_id, '_gamipress_wp_ulike_post_type', true );

    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_wp_ulike_requirement_object', 10, 2 );

/**
 * Category field on requirements UI
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_wp_ulike_requirement_ui_fields( $requirement_id, $post_id ) {

    $post_types = get_post_types( array( 'public' => true ), 'objects' );
    $post_type = get_post_meta( $requirement_id, '_gamipress_wp_ulike_post_type', true );
    ?>

    <span class="wp-ulike-post-type">
        <select>
            <?php foreach( $post_types as $key => $value ) : ?>
                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $post_type, $key ); ?>><?php echo $value->labels->singular_name; ?></option>
            <?php endforeach; ?>
        </select>
    </span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_wp_ulike_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the custom fields on requirements UI
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_wp_ulike_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wp_ulike_post_type_like'
            || $requirement['trigger_type'] === 'gamipress_wp_ulike_get_post_type_like' ) ) {

        // Post type
        update_post_meta( $requirement_id, '_gamipress_wp_ulike_post_type', $requirement['wp_ulike_post_type'] );

    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_wp_ulike_ajax_update_requirement', 10, 2 );