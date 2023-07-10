<?php
/**
 * Requirements
 *
 * @package GamiPress\Advanced_Custom_Fields\Requirements
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
function gamipress_acf_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_acf_update_any_post_field_specific_value'
            || $requirement['trigger_type'] === 'gamipress_acf_update_any_user_field_specific_value'
            || $requirement['trigger_type'] === 'gamipress_acf_update_specific_post_field_specific_value'
            || $requirement['trigger_type'] === 'gamipress_acf_update_specific_user_field_specific_value' ) ) {

        $requirement['acf_field_value'] = get_post_meta( $requirement_id, '_gamipress_acf_field_value', true );
        $requirement['acf_field_value_condition'] = get_post_meta( $requirement_id, '_gamipress_acf_field_value_condition', true );
    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_acf_requirement_object', 10, 2 );

/**
 * Custom field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_acf_requirement_ui_fields( $requirement_id, $post_id ) {

    $value_conditions = gamipress_acf_get_value_conditions();
    $value_condition = get_post_meta( $requirement_id, '_gamipress_acf_field_value_condition', true );
    $field_value = get_post_meta( $requirement_id, '_gamipress_acf_field_value', true );
    ?>

    <span class="acf-field-value">
        <select>
            <?php foreach( $value_conditions as $id => $name ) : ?>
                <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $value_condition, $id ); ?>><?php echo esc_html( $name ); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" value="<?php echo $field_value; ?>" placeholder="<?php echo __( 'Field value', 'gamipress' ); ?>" />
    </span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_acf_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the custom field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_acf_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_acf_update_any_post_field_specific_value'
            || $requirement['trigger_type'] === 'gamipress_acf_update_any_user_field_specific_value'
            || $requirement['trigger_type'] === 'gamipress_acf_update_specific_post_field_specific_value'
            || $requirement['trigger_type'] === 'gamipress_acf_update_specific_user_field_specific_value' ) ) {

        update_post_meta( $requirement_id, '_gamipress_acf_field_value_condition', $requirement['acf_field_value_condition'] );
        update_post_meta( $requirement_id, '_gamipress_acf_field_value', $requirement['acf_field_value'] );
    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_acf_ajax_update_requirement', 10, 2 );