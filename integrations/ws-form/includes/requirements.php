<?php
/**
 * Requirements
 *
 * @package GamiPress\WS_Form\Requirements
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add the custom field to the requirement object
 *
 * @since 1.0.0
 *
 * @param array $requirement
 * @param int   $requirement_id
 *
 * @return array
 */
function gamipress_ws_form_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ws_form_field_value_submission'
            || $requirement['trigger_type'] === 'gamipress_ws_form_specific_field_value_submission' ) ) {

        // Field name and value
        $requirement['ws_form_field_name'] = get_post_meta( $requirement_id, '_gamipress_ws_form_field_name', true );
        $requirement['ws_form_field_value'] = get_post_meta( $requirement_id, '_gamipress_ws_form_field_value', true );
    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_ws_form_requirement_object', 10, 2 );

/**
 * Custom field on requirements UI
 *
 * @since 1.0.0
 *
 * @param int $requirement_id
 * @param int $post_id
 */
function gamipress_ws_form_requirement_ui_fields( $requirement_id, $post_id ) {

    $field_name = get_post_meta( $requirement_id, '_gamipress_ws_form_field_name', true );
    $field_value = get_post_meta( $requirement_id, '_gamipress_ws_form_field_value', true );
    ?>

    <span class="ws-form-field-name"><input type="text" value="<?php echo esc_attr( $field_name ); ?>" placeholder="<?php echo esc_attr( __( 'Field ID', 'gamipress' ) ); ?>" /></span>
    <span class="ws-form-field-value"><input type="text" value="<?php echo esc_attr( $field_value ); ?>" placeholder="<?php echo esc_attr( __( 'Field value', 'gamipress' ) ); ?>" /></span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_ws_form_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the custom field on requirements UI
 *
 * @since 1.0.0
 *
 * @param int   $requirement_id
 * @param array $requirement
 */
function gamipress_ws_form_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ws_form_field_value_submission'
            || $requirement['trigger_type'] === 'gamipress_ws_form_specific_field_value_submission' ) ) {

        // Field name and value
        update_post_meta( $requirement_id, '_gamipress_ws_form_field_name', sanitize_text_field( $requirement['ws_form_field_name'] ) );
        update_post_meta( $requirement_id, '_gamipress_ws_form_field_value', sanitize_text_field( $requirement['ws_form_field_value'] ) );
    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_ws_form_ajax_update_requirement', 10, 2 );