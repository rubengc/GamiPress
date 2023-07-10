<?php
/**
 * Functions
 *
 * @package GamiPress\Easy_Digital_Downloads\Functions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Award user with points on purchase
 *
 * @param integer $payment_id
 */
function gamipress_edd_maybe_award_points_on_purchase( $payment_id ) {

    $payment = edd_get_payment( $payment_id );

    if( $payment ) {

        $cart_details = $payment->cart_details;

        if ( is_array( $cart_details ) ) {
            // Loop each cart item to check if someone awards points to the user
            foreach ( $cart_details as $index => $item ) {

                // Check if download was setup to award points
                $user_id = $payment->user_id;
                $award_points = (bool) get_post_meta( $item['id'], '_gamipress_edd_award_points', true );

                /**
                 * Filter to check if is available to award points for purchase
                 *
                 * @since 1.1.1
                 *
                 * @param bool  $award_points   Whatever if should points be awarded to the user or not
                 * @param int   $user_id        The user ID that will be awarded
                 * @param int   $download_id    The download ID that user has purchased
                 * @param int   $payment_id     The payment ID
                 * @param array $item           The cart item array
                 */
                $award_points = apply_filters( 'gamipress_edd_award_points_for_purchase', $award_points, $user_id, $item['id'], $payment_id, $item );

                if( $award_points ) {

                    $quantity = absint( $item['quantity'] );

                    // If award points on purchase, then award them
                    $points = absint( get_post_meta( $item['id'], '_gamipress_edd_points', true ) );
                    $points_type = get_post_meta( $item['id'], '_gamipress_edd_points_type', true );

                    // Setup the custom reason for the log
                    if( $quantity === 1 ) {
                        $reason =  sprintf(
                            __( '%d %s awarded for purchase %s', 'gamipress' ),
                            $points,
                            gamipress_get_points_type_plural( $points_type ),
                            $item['name']
                        );
                    } else {
                        $reason =  sprintf(
                            __( '%d %s awarded for purchase %d %s', 'gamipress' ),
                            $points,
                            gamipress_get_points_type_plural( $points_type ),
                            $quantity,
                            $item['name']
                        );
                    }

                    // Setup the points award args
                    $args = array(
                        'reason' => $reason,
                        'log_type' => 'points_earn',
                    );

                    // Award the points to the user
                    gamipress_award_points_to_user( $user_id, $points, $points_type, $args );
                }
            }
        }
    }

}
add_action( 'edd_complete_purchase', 'gamipress_edd_maybe_award_points_on_purchase' );

// Helper function to get download variations dropdown
function gamipress_edd_get_download_variations_dropdown( $download_id, $selected = 0, $site_id = 0 ) {

    if( gamipress_is_network_wide_active() && $site_id !== get_current_blog_id() ) {
        switch_to_blog( $site_id );
        $variations = edd_get_variable_prices( $download_id );
        restore_current_blog();
    } else {
        $variations = edd_get_variable_prices( $download_id );
    }

    if( empty( $variations ) ) {
        return '';
    }

    $output = '<select>';

    foreach( $variations as $variation ) {
        $output .= '<option value="' . $variation['index'] . '" ' . selected( $selected, absint( $variation['index'] ), false ) . '>' . $variation['name'] . ' (' . edd_currency_filter( edd_format_amount( $variation['amount'] ) ) . ')</option>';
    }

    $output .= '</select>';

    return $output;

}

// Helper function to get download variation title
function gamipress_edd_get_download_variation_title( $download_id, $variation_id, $site_id ) {

    if( gamipress_is_network_wide_active() && $site_id !== get_current_blog_id() ) {
        switch_to_blog( $site_id );
        $variations = edd_get_variable_prices( $download_id );
        restore_current_blog();
    } else {
        $variations = edd_get_variable_prices( $download_id );
    }

    $selected_variation = false;

    foreach( $variations as $variation ) {
        if( absint( $variation['index'] ) === absint( $variation_id ) ) {
            $selected_variation = $variation;
            break;
        }
    }

    return get_post_field( 'post_title', $download_id )
    . ( $selected_variation ? ' - ' . $selected_variation['name'] : '' );

}

/**
 * Retrieves download term ids for a taxonomy.
 *
 * Taken from WooCommerce wc_get_product_term_ids() function
 *
 * @since  1.1.2
 *
 * @param  int    $download_id Download ID.
 * @param  string $taxonomy   Taxonomy slug.
 *
 * @return array
 */
function gamipress_edd_get_download_term_ids( $download_id, $taxonomy ) {
    $terms = get_the_terms( $download_id, $taxonomy );
    return ( empty( $terms ) || is_wp_error( $terms ) ) ? array() : wp_list_pluck( $terms, 'term_id' );
}

/**
 * Get lifetime conditions
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_edd_get_lifetime_conditions() {

    return array(
        'equal'             => __( 'equal to', 'gamipress'),
        'not_equal'         => __( 'not equal to', 'gamipress'),
        'less_than'         => __( 'less than', 'gamipress' ),
        'greater_than'      => __( 'greater than', 'gamipress' ),
        'less_or_equal'     => __( 'less or equal to', 'gamipress' ),
        'greater_or_equal'  => __( 'greater or equal to', 'gamipress' ),
    );

}