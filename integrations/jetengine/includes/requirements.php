<?php
/**
 * Requirements
 *
 * @package GamiPress\JetEngine\Requirements
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
function gamipress_jetengine_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_jetengine_publish_post_specific_type'
            || $requirement['trigger_type'] === 'gamipress_jetengine_update_post_specific_type'
            || $requirement['trigger_type'] === 'gamipress_jetengine_delete_post_specific_type' ) ) {

        // JetEngine post type
        $requirement['jetengine_post_type'] = get_post_meta( $requirement_id, '_gamipress_jetengine_post_type', true );
    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_jetengine_requirement_object', 10, 2 );

/**
 * Custom field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_jetengine_requirement_ui_fields( $requirement_id, $post_id ) {

    $selected = get_post_meta( $requirement_id, '_gamipress_jetengine_post_type', true );

    // Get JetEngine post types
    $post_types_obj = new Jet_Engine_CPT;
    $post_types = $post_types_obj->get_items();
    ?>

    <select class="select-jetengine-post-type">
        <?php foreach( $post_types as $post_type ) : ?>
            <option value="<?php echo $post_type['slug'] ?>" <?php selected( $selected, $post_type['slug'] ); ?>><?php echo $post_type['labels']['name']; ?></option>
        <?php endforeach; ?>
    </select>
    

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_jetengine_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the custom field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_jetengine_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_jetengine_publish_post_specific_type'
            || $requirement['trigger_type'] === 'gamipress_jetengine_update_post_specific_type'
            || $requirement['trigger_type'] === 'gamipress_jetengine_delete_post_specific_type' ) ) {

        // Post type
        update_post_meta( $requirement_id, '_gamipress_jetengine_post_type', $requirement['jetengine_post_type'] );

    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_jetengine_ajax_update_requirement', 10, 2 );