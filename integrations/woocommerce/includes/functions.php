<?php
/**
 * Functions
 *
 * @package GamiPress\WooCommerce\Functions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Helper function to get product variations
function gamipress_wc_get_product_variations( $product_id ) {

    $product = wc_get_product( $product_id );

    // Bail if product doesn't exists
    if( ! $product ) {
        return array();
    }

    // Bail if product is not variable
    if( ! $product->is_type( 'variable' ) ) {
        return array();
    }

    $available_variations = $product->get_available_variations();

    // Bail if there isn't any variations
    if( ! is_array( $available_variations )  ) {
        return array();
    }

    // Return product variations
    return $available_variations;

}

// Helper function to get product variations dropdown
function gamipress_wc_get_product_variations_dropdown( $product_id, $selected = 0, $site_id = 0 ) {

    if( gamipress_is_network_wide_active() && $site_id !== get_current_blog_id() ) {
        switch_to_blog( $site_id );
        $variations = gamipress_wc_get_product_variations( $product_id );
        restore_current_blog();
    } else {
        $variations = gamipress_wc_get_product_variations( $product_id );
    }

    if( empty( $variations ) ) {
        return '';
    }

    $output = '<select>';

    foreach( $variations as $variation ) {

        $attributes = array();

        foreach( $variation['attributes'] as $attribute ) {
            if( ! empty( $attribute ) ) {
                $attributes[] = $attribute;
            }
        }

        $output .= '<option value="' . $variation['variation_id'] . '" ' . selected( $selected, $variation['variation_id'], false ) . '>' . implode( ', ', $attributes ) . ' (#' . $variation['variation_id'] . ')</option>';
    }

    $output .= '</select>';

    return $output;

}

// Helper function to get product variation title
function gamipress_wc_get_product_variation_title( $product_id, $variation_id, $site_id = 0 ) {

    if( gamipress_is_network_wide_active() && $site_id !== get_current_blog_id() ) {
        switch_to_blog( $site_id );
        $variation_attributes = wc_get_product_variation_attributes( $variation_id );
        restore_current_blog();
    } else {
        $variation_attributes = wc_get_product_variation_attributes( $variation_id );
    }

    $attributes = array();

    foreach( $variation_attributes as $attribute ) {
        if( ! empty( $attribute ) ) {
            $attributes[] = $attribute;
        }
    }

    return get_post_field( 'post_title', $product_id )
            . ( ! empty( $attributes ) ? ' (' . implode( ', ', $attributes ) . ')' : '' );

}

