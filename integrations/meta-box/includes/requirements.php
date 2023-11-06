<?php
/**
 * Requirements
 *
 * @package GamiPress\Meta_Box\Requirements
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
function gamipress_meta_box_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_meta_box_update_specific_post_field_any_value'
            || $requirement['trigger_type'] === 'gamipress_meta_box_update_specific_user_field_any_value' ) ) {

        $requirement['mb_field_name'] = get_post_meta( $requirement_id, '_gamipress_meta_box_field_name', true );        
        
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_meta_box_update_any_post_field_specific_value'
            || $requirement['trigger_type'] === 'gamipress_meta_box_update_any_user_field_specific_value' ) ) {
    
        $requirement['mb_field_value'] = get_post_meta( $requirement_id, '_gamipress_meta_box_field_value', true );
        $requirement['mb_field_value_condition'] = get_post_meta( $requirement_id, '_gamipress_meta_box_field_value_condition', true );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_meta_box_update_specific_post_field_specific_value'
            || $requirement['trigger_type'] === 'gamipress_meta_box_update_specific_user_field_specific_value' ) ) {

        $requirement['mb_field_name'] = get_post_meta( $requirement_id, '_gamipress_meta_box_field_name', true );        
        $requirement['mb_field_value'] = get_post_meta( $requirement_id, '_gamipress_meta_box_field_value', true );
        $requirement['mb_field_value_condition'] = get_post_meta( $requirement_id, '_gamipress_meta_box_field_value_condition', true );
    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_meta_box_requirement_object', 10, 2 );

/**
 * Custom field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_meta_box_requirement_ui_fields( $requirement_id, $post_id ) {

    $value_conditions = gamipress_meta_box_get_value_conditions();
    $value_condition = get_post_meta( $requirement_id, '_gamipress_meta_box_field_value_condition', true );
    $field_value = get_post_meta( $requirement_id, '_gamipress_meta_box_field_value', true );
    $field_name = get_post_meta( $requirement_id, '_gamipress_meta_box_field_name', true ); 
    $field_name_user = get_post_meta( $requirement_id, '_gamipress_meta_box_field_name_user', true ); 

    $post_fields = gamipress_meta_box_get_all_post_fields();

    // Meta Box User Meta Extension
    if ( defined( 'MBAIO_DIR' ) || class_exists( 'RWMB_User_Storage' ) ) {

        if ( ! function_exists( 'rwmb_get_object_fields' ) ) {
            return;
        }
        
        $user_fields = rwmb_get_object_fields( 'user', 'user' );
    } 

    ?>

    <span class="mb-field-name">
        <select>
            <?php foreach( $post_fields as $id => $name ) : ?>
                <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $field_name, $id ); ?>><?php echo esc_html( $name['name'] ); ?></option>
            <?php endforeach; ?>
        </select>
    </span>

    <?php
        if ( isset( $user_fields ) ){
    ?>

    <span class="mb-field-name-user">
        <select>
            <?php foreach( $user_fields as $id => $name ) : ?>
                <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $field_name_user, $id ); ?>><?php echo esc_html( $name['name'] ); ?></option>
            <?php endforeach; ?>
        </select>
    </span>

    <?php
        }
    ?>

    <span class="mb-field-value">
        <select>
            <?php foreach( $value_conditions as $id => $name ) : ?>
                <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $value_condition, $id ); ?>><?php echo esc_html( $name ); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" value="<?php echo $field_value; ?>" placeholder="<?php echo __( 'Field value', 'gamipress' ); ?>" />
    </span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_meta_box_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the custom field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_meta_box_ajax_update_requirement( $requirement_id, $requirement ) {
    
    // Specific Field Any Value
    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_meta_box_update_specific_post_field_any_value' ) ) {

        update_post_meta( $requirement_id, '_gamipress_meta_box_field_name', $requirement['mb_field_name'] );
    }

    // Specific Field Any Value
    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_meta_box_update_specific_user_field_any_value' ) ) {

        update_post_meta( $requirement_id, '_gamipress_meta_box_field_name_user', $requirement['mb_field_name_user'] );
    }

    // Any Field Specific Value
    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_meta_box_update_any_post_field_specific_value'
            || $requirement['trigger_type'] === 'gamipress_meta_box_update_any_user_field_specific_value' ) ) {
   
            update_post_meta( $requirement_id, '_gamipress_meta_box_field_value_condition', $requirement['mb_field_value_condition'] );
            update_post_meta( $requirement_id, '_gamipress_meta_box_field_value', $requirement['mb_field_value'] );

            
    }

    // Specific Field Specific Value
    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_meta_box_update_specific_post_field_specific_value' ) ) {

            update_post_meta( $requirement_id, '_gamipress_meta_box_field_value_condition', $requirement['mb_field_value_condition'] );
            update_post_meta( $requirement_id, '_gamipress_meta_box_field_value', $requirement['mb_field_value'] );
            update_post_meta( $requirement_id, '_gamipress_meta_box_field_name', $requirement['mb_field_name'] );
    }

    // Specific Field Specific Value
    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_meta_box_update_specific_user_field_specific_value' ) ) {

            update_post_meta( $requirement_id, '_gamipress_meta_box_field_value_condition', $requirement['mb_field_value_condition'] );
            update_post_meta( $requirement_id, '_gamipress_meta_box_field_value', $requirement['mb_field_value'] );
            update_post_meta( $requirement_id, '_gamipress_meta_box_field_name_user', $requirement['mb_field_name_user'] );
    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_meta_box_ajax_update_requirement', 10, 2 );

/**
 * Get meta box fields objects related to posts and custom types
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_meta_box_get_all_post_fields() {

    if ( ! function_exists( 'rwmb_get_object_fields' ) ) {
        return array();
    }

    $post_fields = rwmb_get_object_fields( 'post' );

    // Get custom types created on Meta Box
    $custom_post_types = get_posts( [
        'post_type' => 'mb-post-type',
        'post_status' => 'publish',
        'numberposts' => -1
    ] );

    if ( !empty ( $custom_post_types ) ) {
        foreach ( $custom_post_types as $custom_type ) {
            $custom_fields = rwmb_get_object_fields( $custom_type->post_name );
            $post_fields = array_merge($post_fields, $custom_fields);
        }
    }

    return $post_fields;

}