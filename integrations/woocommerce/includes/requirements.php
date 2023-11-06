<?php
/**
 * Requirements
 *
 * @package GamiPress\WooCommerce\Requirements
 * @since 1.1.3
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
function gamipress_wc_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wc_new_purchase_total' ) ) {

        // Purchase total
        $requirement['wc_purchase_total_condition'] = gamipress_get_post_meta( $requirement_id, '_gamipress_wc_purchase_total_condition', true );
        $requirement['wc_purchase_total'] = gamipress_get_post_meta( $requirement_id, '_gamipress_wc_purchase_total', true );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wc_product_variation_purchase'
            || $requirement['trigger_type'] === 'gamipress_wc_product_variation_refund' ) ) {
        // Product variation
        $requirement['wc_variation_id'] = gamipress_get_post_meta( $requirement_id, '_gamipress_wc_variation_id', true );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wc_product_category_purchase'
            || $requirement['trigger_type'] === 'gamipress_wc_product_category_refund' ) ) {
        // Category
        $requirement['wc_category_id'] = gamipress_get_post_meta( $requirement_id, '_gamipress_wc_category_id', true );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wc_product_tag_purchase'
            || $requirement['trigger_type'] === 'gamipress_wc_product_tag_refund' ) ) {
        // Category
        $requirement['wc_tag_id'] = gamipress_get_post_meta( $requirement_id, '_gamipress_wc_tag_id', true );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wc_lifetime_value' ) ) {

        // Lifetime value
        $requirement['wc_lifetime_condition'] = gamipress_get_post_meta( $requirement_id, '_gamipress_wc_lifetime_condition', true );
        $requirement['wc_lifetime'] = gamipress_get_post_meta( $requirement_id, '_gamipress_wc_lifetime', true );

    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_wc_requirement_object', 10, 2 );

/**
 * Custom fields on requirements UI
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_wc_requirement_ui_fields( $requirement_id, $post_id ) {

    $number_conditions = gamipress_number_condition_options();

    // Purchase total value
    $purchase_total_condition = gamipress_get_post_meta( $requirement_id, '_gamipress_wc_purchase_total_condition', true );
    $purchase_total = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_wc_purchase_total', true ) );

    ?>
    <span class="wc-purchase-total">
        <select>
            <?php foreach( $number_conditions as $id => $name ) : ?>
                <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $purchase_total_condition, $id ); ?>><?php echo esc_html( $name ); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" value="<?php echo esc_attr( $purchase_total ); ?>" size="7" placeholder="100" />
    </span>

    <?php

    // Product variation select
    $post_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_achievement_post' ) );
    $site_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_achievement_post_site_id' ) );
    $variation_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_wc_variation_id', true ) );
    ?>

    <span class="wc-variation">
        <?php if( $post_id !== 0 && $variation_id !== 0 ) {
            echo gamipress_wc_get_product_variations_dropdown( $post_id, $variation_id, $site_id );
        } ?>
    </span>

    <?php

    // Category select
    $category_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_wc_category_id', true ) );
    $categories = get_terms( array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
    ) );

    ?>

    <span class="wc-category">
        <select>
            <?php foreach( $categories as $category ) : ?>
                <option value="<?php echo $category->term_id; ?>" <?php selected( $category_id, $category->term_id ); ?>><?php echo $category->name; ?></option>
            <?php endforeach; ?>
        </select>
    </span>

    <?php

    // Tags select
    $tag_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_wc_tag_id', true ) );
    $tags = get_terms( array(
        'taxonomy' => 'product_tag',
        'hide_empty' => false,
    ) );

    ?>

    <span class="wc-tag">
        <select>
            <?php foreach( $tags as $tag ) : ?>
                <option value="<?php echo $tag->term_id; ?>" <?php selected( $tag_id, $tag->term_id ); ?>><?php echo $tag->name; ?></option>
            <?php endforeach; ?>
        </select>
    </span>

    <?php

    // Lifetime value
    $lifetime_condition = gamipress_get_post_meta( $requirement_id, '_gamipress_wc_lifetime_condition', true );
    $lifetime = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_wc_lifetime', true ) );

    ?>
    <span class="wc-lifetime">
        <select>
            <?php foreach( $number_conditions as $id => $name ) : ?>
                <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $lifetime_condition, $id ); ?>><?php echo esc_html( $name ); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" value="<?php echo esc_attr( $lifetime ); ?>" size="5" maxlength="5" placeholder="100" />
    </span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_wc_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save custom fields on requirements UI
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_wc_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wc_new_purchase_total' ) ) {

        // Purchase total
        gamipress_update_post_meta( $requirement_id, '_gamipress_wc_purchase_total_condition', $requirement['wc_purchase_total_condition'] );
        gamipress_update_post_meta( $requirement_id, '_gamipress_wc_purchase_total', $requirement['wc_purchase_total'] );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wc_product_variation_purchase'
            || $requirement['trigger_type'] === 'gamipress_wc_product_variation_refund' ) ) {
        // Save the variation field
        gamipress_update_post_meta( $requirement_id, '_gamipress_wc_variation_id', $requirement['wc_variation_id'] );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wc_product_category_purchase'
            || $requirement['trigger_type'] === 'gamipress_wc_product_category_refund' ) ) {
        // Save the category field
        gamipress_update_post_meta( $requirement_id, '_gamipress_wc_category_id', $requirement['wc_category_id'] );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wc_product_tag_purchase'
            || $requirement['trigger_type'] === 'gamipress_wc_product_tag_refund' ) ) {
        // Save the tag field
        gamipress_update_post_meta( $requirement_id, '_gamipress_wc_tag_id', $requirement['wc_tag_id'] );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wc_lifetime_value' ) ) {

        // Lifetime
        gamipress_update_post_meta( $requirement_id, '_gamipress_wc_lifetime_condition', $requirement['wc_lifetime_condition'] );
        gamipress_update_post_meta( $requirement_id, '_gamipress_wc_lifetime', $requirement['wc_lifetime'] );

    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_wc_ajax_update_requirement', 10, 2 );