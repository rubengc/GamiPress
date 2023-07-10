<?php
/**
 * Requirements
 *
 * @package GamiPress\BuddyPress\Requirements
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add the bp fields to the requirement object
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_bp_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] ) ) {

        if( $requirement['trigger_type'] === 'gamipress_bp_set_member_type' ) {

            // The member type
            $requirement['bp_member_type'] = gamipress_get_post_meta( $requirement_id, '_gamipress_bp_member_type', true );

        }

        if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_bp_update_profile_specific_value' ) ) {

        // Value
        $requirement['bp_field_value'] = gamipress_get_post_meta( $requirement_id, '_gamipress_bp_field_value', true );

    }

    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_bp_requirement_object', 10, 2 );

/**
 * Link fields on requirements UI
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_bp_requirement_ui_fields( $requirement_id, $post_id ) {

    if( function_exists( 'bp_get_member_types' ) ) {
        $member_types = bp_get_member_types( array(), 'objects' );
    } else {
        $member_types = array();
    }
    $bp_member_type = gamipress_get_post_meta( $requirement_id, '_gamipress_bp_member_type', true ); ?>

    <select class="select-bp-member-type">
        <?php foreach( $member_types as $member_type => $member_type_obj ) : ?>
            <option value="<?php echo $member_type; ?>" <?php selected( $bp_member_type, $member_type ); ?>><?php echo $member_type_obj->labels['singular_name']; ?></option>
        <?php endforeach; ?>
    </select>

    <?php
    $field_value = gamipress_get_post_meta( $requirement_id, '_gamipress_bp_field_value', true );
    ?>

    <span class="bp-field-value"><input type="text" value="<?php echo $field_value; ?>" placeholder="<?php echo __( 'Field value', 'gamipress' ); ?>" /></span>


    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_bp_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the custom fields on requirements UI
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_bp_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] ) ) {

        if( $requirement['trigger_type'] === 'gamipress_bp_set_member_type' ) {

            // The member type
            update_post_meta( $requirement_id, '_gamipress_bp_member_type', $requirement['bp_member_type'] );
        }

        if( $requirement['trigger_type'] === 'gamipress_bp_update_profile_specific_value' ) {

            // The field value
            update_post_meta( $requirement_id, '_gamipress_bp_field_value', $requirement['bp_field_value'] );
        }

    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_bp_ajax_update_requirement', 10, 2 );