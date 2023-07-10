<?php
/**
 * Requirements
 *
 * @package GamiPress\Easy_Digital_Downloads\Requirements
 * @since 1.1.2
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
function gamipress_edd_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_edd_download_variation_purchase'
            || $requirement['trigger_type'] === 'gamipress_edd_download_variation_refund' ) ) {
        // Download variation
        $requirement['edd_variation_id'] = gamipress_get_post_meta( $requirement_id, '_gamipress_edd_variation_id', true );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_edd_download_category_purchase'
            || $requirement['trigger_type'] === 'gamipress_edd_download_category_refund' ) ) {
        // Category
        $requirement['edd_category_id'] = gamipress_get_post_meta( $requirement_id, '_gamipress_edd_category_id', true );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_edd_download_tag_purchase'
            || $requirement['trigger_type'] === 'gamipress_edd_download_tag_refund' ) ) {
        // Category
        $requirement['edd_tag_id'] = gamipress_get_post_meta( $requirement_id, '_gamipress_edd_tag_id', true );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_edd_lifetime_value' ) ) {

        // Lifetime value
        $requirement['edd_lifetime_condition'] = get_post_meta( $requirement_id, '_gamipress_edd_lifetime_condition', true );
        $requirement['edd_lifetime'] = get_post_meta( $requirement_id, '_gamipress_edd_lifetime', true );

    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_edd_requirement_object', 10, 2 );

/**
 * Custom fields on requirements UI
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_edd_requirement_ui_fields( $requirement_id, $post_id ) {

    // Download variation select
    $post_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_achievement_post' ) );
    $site_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_achievement_post_site_id' ) );
    $variation_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_edd_variation_id', true ) );

    ?>

    <span class="edd-variation">
        <?php if( $post_id !== 0 && $variation_id !== 0 ) {
            echo gamipress_edd_get_download_variations_dropdown( $post_id, $variation_id, $site_id );
        } ?>
    </span>

    <?php

    // Category select
    $category_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_edd_category_id', true ) );
    $categories = get_terms( array(
        'taxonomy' => 'download_category',
        'hide_empty' => false,
    ) );

    ?>

    <span class="edd-category">
        <select>
            <?php foreach( $categories as $category ) : ?>
                <option value="<?php echo $category->term_id; ?>" <?php selected( $category_id, $category->term_id ); ?>><?php echo $category->name; ?></option>
            <?php endforeach; ?>
        </select>
    </span>

    <?php

    // Tags select
    $tag_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_edd_tag_id', true ) );
    $tags = get_terms( array(
        'taxonomy' => 'download_tag',
        'hide_empty' => false,
    ) );

    ?>

    <span class="edd-tag">
        <select>
            <?php foreach( $tags as $tag ) : ?>
                <option value="<?php echo $tag->term_id; ?>" <?php selected( $tag_id, $tag->term_id ); ?>><?php echo $tag->name; ?></option>
            <?php endforeach; ?>
        </select>
    </span>

    <?php

    // Lifetime value
    $lifetime_conditions = gamipress_edd_get_lifetime_conditions();
    $lifetime_condition = get_post_meta( $requirement_id, '_gamipress_edd_lifetime_condition', true );
    $lifetime = absint( get_post_meta( $requirement_id, '_gamipress_edd_lifetime', true ) );

    ?>
    <span class="edd-lifetime">
        <select>
            <?php foreach( $lifetime_conditions as $id => $name ) : ?>
                <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $lifetime_condition, $id ); ?>><?php echo esc_html( $name ); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" value="<?php echo esc_attr( $lifetime ); ?>" size="3" maxlength="3" placeholder="100" />
    </span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_edd_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save custom fields on requirements UI
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_edd_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_edd_download_variation_purchase'
            || $requirement['trigger_type'] === 'gamipress_edd_download_variation_refund' ) ) {
        // Save the variation field
        gamipress_update_post_meta( $requirement_id, '_gamipress_edd_variation_id', $requirement['edd_variation_id'] );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_edd_download_category_purchase'
            || $requirement['trigger_type'] === 'gamipress_edd_download_category_refund' ) ) {
        // Save the category field
        gamipress_update_post_meta( $requirement_id, '_gamipress_edd_category_id', $requirement['edd_category_id'] );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_edd_download_tag_purchase'
            || $requirement['trigger_type'] === 'gamipress_edd_download_tag_refund' ) ) {
        // Save the tag field
        gamipress_update_post_meta( $requirement_id, '_gamipress_edd_tag_id', $requirement['edd_tag_id'] );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_edd_lifetime_value' ) ) {

        // Lifetime
        update_post_meta( $requirement_id, '_gamipress_edd_lifetime_condition', $requirement['edd_lifetime_condition'] );
        update_post_meta( $requirement_id, '_gamipress_edd_lifetime', $requirement['edd_lifetime'] );

    }

}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_edd_ajax_update_requirement', 10, 2 );