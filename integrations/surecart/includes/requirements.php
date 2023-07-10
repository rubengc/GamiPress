<?php
/**
 * Requirements
 *
 * @package GamiPress\SureCart\Requirements
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
function gamipress_surecart_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_surecart_specific_product_purchase') ) {
        // Field form
        $requirement['surecart_product'] = get_post_meta( $requirement_id, '_gamipress_surecart_product', true );
    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_surecart_requirement_object', 10, 2 );

/**
 * Form field on requirements UI
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_surecart_requirement_ui_fields( $requirement_id, $post_id ) {

    // Get the products
    $products = SureCart\Models\Product::get();
    $selected = get_post_meta( $requirement_id, '_gamipress_surecart_product', true ); ?>

    <select class="select-surecart-product">
        <?php foreach( $products as $product ) : ?>
            <option value="<?php echo $product->id; ?>" <?php selected( $selected, $product->id ); ?>><?php echo $product->name; ?></option>
        <?php endforeach; ?>
    </select>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_surecart_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the form on requirements UI
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_surecart_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_surecart_specific_product_purchase' ) ) {

        // Field form
        update_post_meta( $requirement_id, '_gamipress_surecart_product', $requirement['surecart_product'] );
    }

}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_surecart_ajax_update_requirement', 10, 2 );