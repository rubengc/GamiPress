<?php
/**
 * Requirements
 *
 * @package GamiPress\Divi\Requirements
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add the custom field to the requirement object
 *
 * @since 1.0.0
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_divi_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_divi_specific_new_form_submission'
            || $requirement['trigger_type'] === 'gamipress_divi_specific_field_value_submission' ) ) {

        // Form name
        $requirement['divi_form_id'] = get_post_meta( $requirement_id, '_gamipress_divi_form_id', true );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_divi_field_value_submission'
            || $requirement['trigger_type'] === 'gamipress_divi_specific_field_value_submission' ) ) {

        // Field name and value
        $requirement['divi_field_name'] = get_post_meta( $requirement_id, '_gamipress_divi_field_name', true );
        $requirement['divi_field_value'] = get_post_meta( $requirement_id, '_gamipress_divi_field_value', true );
    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_divi_requirement_object', 10, 2 );

/**
 * Custom field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_divi_requirement_ui_fields( $requirement_id, $post_id ) {

    $form_id = get_post_meta( $requirement_id, '_gamipress_divi_form_id', true );
    $field_name = get_post_meta( $requirement_id, '_gamipress_divi_field_name', true );
    $field_value = get_post_meta( $requirement_id, '_gamipress_divi_field_value', true );
    ?>

    <span class="divi-form-id"><input type="text" value="<?php echo $form_id; ?>" placeholder="<?php echo __( 'Form ID', 'gamipress' ); ?>" /></span>
    <span class="divi-field-name"><input type="text" value="<?php echo $field_name; ?>" placeholder="<?php echo __( 'Field ID', 'gamipress' ); ?>" /></span>
    <span class="divi-field-value"><input type="text" value="<?php echo $field_value; ?>" placeholder="<?php echo __( 'Field value', 'gamipress' ); ?>" /></span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_divi_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the custom field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_divi_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_divi_specific_new_form_submission'
            || $requirement['trigger_type'] === 'gamipress_divi_specific_field_value_submission' ) ) {

        // Form name
        update_post_meta( $requirement_id, '_gamipress_divi_form_id', $requirement['divi_form_id'] );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_divi_field_value_submission'
            || $requirement['trigger_type'] === 'gamipress_divi_specific_field_value_submission' ) ) {

        // Field name and value
        update_post_meta( $requirement_id, '_gamipress_divi_field_name', $requirement['divi_field_name'] );
        update_post_meta( $requirement_id, '_gamipress_divi_field_value', $requirement['divi_field_value'] );
    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_divi_ajax_update_requirement', 10, 2 );