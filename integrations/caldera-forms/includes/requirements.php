<?php
/**
 * Requirements
 *
 * @package GamiPress\Caldera_Forms\Requirements
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add the form field to the requirement object
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_cf_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_cf_specific_new_form_submission'
            || $requirement['trigger_type'] === 'gamipress_cf_specific_field_value_submission' ) ) {
        // Field form
        $requirement['caldera_form'] = get_post_meta( $requirement_id, '_gamipress_caldera_form', true );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_cf_field_value_submission'
            || $requirement['trigger_type'] === 'gamipress_cf_specific_field_value_submission' ) ) {

        // Field name and value
        $requirement['cf_field_name'] = get_post_meta( $requirement_id, '_gamipress_cf_field_name', true );
        $requirement['cf_field_value'] = get_post_meta( $requirement_id, '_gamipress_cf_field_value', true );
    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_cf_requirement_object', 10, 2 );

/**
 * Form field on requirements UI
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_cf_requirement_ui_fields( $requirement_id, $post_id ) {

    $forms = Caldera_Forms::get_forms();
    $selected = get_post_meta( $requirement_id, '_gamipress_caldera_form', true ); ?>

    <select class="select-caldera-form">
        <?php foreach( $forms as $form ) : ?>
            <option value="<?php echo $form['ID']; ?>" <?php selected( $selected, $form['ID'] ); ?>><?php echo $form['name']; ?></option>
        <?php endforeach; ?>
    </select>

    <?php

    $field_name = get_post_meta( $requirement_id, '_gamipress_cf_field_name', true );
    $field_value = get_post_meta( $requirement_id, '_gamipress_cf_field_value', true );
    ?>

    <span class="cf-field-name"><input type="text" value="<?php echo $field_name; ?>" placeholder="<?php echo __( 'Field name', 'gamipress' ); ?>" /></span>
    <span class="cf-field-value"><input type="text" value="<?php echo $field_value; ?>" placeholder="<?php echo __( 'Field value', 'gamipress' ); ?>" /></span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_cf_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the form on requirements UI
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_cf_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_cf_specific_new_form_submission'
            || $requirement['trigger_type'] === 'gamipress_cf_specific_field_value_submission' ) ) {

        // Field form
        update_post_meta( $requirement_id, '_gamipress_caldera_form', $requirement['caldera_form'] );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_cf_field_value_submission'
            || $requirement['trigger_type'] === 'gamipress_cf_specific_field_value_submission' ) ) {

        // Field name and value
        update_post_meta( $requirement_id, '_gamipress_cf_field_name', $requirement['cf_field_name'] );
        update_post_meta( $requirement_id, '_gamipress_cf_field_value', $requirement['cf_field_value'] );
    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_cf_ajax_update_requirement', 10, 2 );