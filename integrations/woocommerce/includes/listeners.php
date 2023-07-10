<?php
/**
 * Listeners
 *
 * @package GamiPress\WooCommerce\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// --------------------------------------------
// Listeners for product settings
// --------------------------------------------

/**
 * Award user with points on purchase
 *
 * @param integer $order_id
 */
function gamipress_wc_maybe_award_points_on_purchase( $order_id ) {

    $prefix = '_gamipress_wc_';

    $points_awarded = get_post_meta( $order_id, $prefix . 'points_awarded', true );

    // Bail if points has been already awarded
    if( (bool) $points_awarded ) {
        return;
    }

    $order = wc_get_order( $order_id );

    if( $order ) {

        $items = $order->get_items();

        if ( is_array( $items ) ) {

            // Loop each cart item to check if someone awards points to the user
            foreach ( $items as $item ) {

                $product_id = 0;
                $quantity = 1;

                // Depending of the WooCommerce version, items could be an array or an WC_Order_Item object
                if( class_exists( 'WC_Order_Item' ) && $item instanceof WC_Order_Item ) {
                    $product_id = $item->get_product_id();
                    $quantity = $item->get_quantity();
                } else if( is_array( $item ) && isset( $item['product_id'] ) ) {
                    $product_id = $item['product_id'];
                    $quantity = isset( $item['qty'] ) ? $item['qty'] : $item['quantity'];
                }

                if( $product_id !== 0 && $quantity > 0 ) {

                    // Check if product was setup to award points
                    $user_id = $order->get_user_id();
                    $award_points = (bool) get_post_meta( $product_id, $prefix . 'award_points', true );

                    /**
                     * Filter to check if is available to award points for purchase
                     *
                     * @since 1.1.2
                     *
                     * @param bool                  $award_points   Whatever if should points be awarded to the user or not
                     * @param int                   $user_id        The user ID that will be awarded
                     * @param int                   $product_id     The product ID that user has purchased
                     * @param int                   $order_id       The order ID
                     * @param WC_Order_Item|array   $item           The order item object
                     */
                    $award_points = apply_filters( 'gamipress_wc_award_points_for_purchase', $award_points, $user_id, $product_id, $order_id, $item );

                    if( $award_points ) {

                        $product = wc_get_product( $product_id );

                        // Get the amount of points to award and the points type
                        $points = absint( get_post_meta( $product_id, $prefix . 'points', true ) );
                        $points_type = get_post_meta( $product_id, $prefix . 'points_type', true );

                        // The amount to award if based to the quantity added to the cart
                        $points = absint( $points * $quantity );

                        // Setup the custom reason for the log
                        if( $quantity === 1 ) {
                            $reason =  sprintf(
                                __( '%d %s awarded for purchase of %s', 'gamipress' ),
                                $points,
                                gamipress_get_points_type_plural( $points_type ),
                                $product->get_name()
                            );
                        } else {
                            $reason =  sprintf(
                                __( '%d %s awarded for purchase of %d %s', 'gamipress' ),
                                $points,
                                gamipress_get_points_type_plural( $points_type ),
                                $quantity,
                                $product->get_name()
                            );
                        }

                        // Setup the points award args
                        $args = array(
                            'reason' => $reason,
                            'log_type' => 'points_earn',
                        );

                        // Award the points to the user
                        gamipress_award_points_to_user( $user_id, $points, $points_type, $args );

                        $points_type_data = gamipress_get_points_type( $points_type );

                        // Insert the custom user earning for this purchase
                        gamipress_insert_user_earning( $user_id, array(
                            'title'	        => $reason,
                            'user_id'	    => $user_id,
                            'post_id'	    => $points_type_data['ID'],
                            'post_type' 	=> 'points-type',
                            'points'	    => $points,
                            'points_type'	=> $points_type,
                            'date'	        => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
                        ) );

                    }

                }

            }

        }

    }

    // Set a post meta to meet that points have been awarded
    update_post_meta( $order_id, $prefix . 'points_awarded', '1' );

}
add_action( 'woocommerce_order_status_completed', 'gamipress_wc_maybe_award_points_on_purchase' );

/**
 * Deduct user with points on refund
 *
 * @param integer $order_id
 */
function gamipress_wc_maybe_deduct_points_on_refund( $order_id ) {

    $prefix = '_gamipress_wc_';

    $points_awarded = get_post_meta( $order_id, $prefix . 'points_awarded', true );

    // Bail if points has not been awarded
    if( ! (bool) $points_awarded ) {
        return;
    }

    $order = wc_get_order( $order_id );

    if( $order ) {

        $items = $order->get_items();

        if ( is_array( $items ) ) {

            // Loop each cart item to check if someone awards points to the user
            foreach ( $items as $item ) {

                $product_id = 0;
                $quantity = 1;

                // Depending of the WooCommerce version, items could be an array or an WC_Order_Item object
                if( class_exists( 'WC_Order_Item' ) && $item instanceof WC_Order_Item ) {
                    $product_id = $item->get_product_id();
                    $quantity = $item->get_quantity();
                } else if( is_array( $item ) && isset( $item['product_id'] ) ) {
                    $product_id = $item['product_id'];
                    $quantity = isset( $item['qty'] ) ? $item['qty'] : $item['quantity'];
                }

                if( $product_id !== 0 && $quantity > 0 ) {

                    // Check if product was setup to award points
                    $user_id = $order->get_user_id();
                    $award_points = (bool) get_post_meta( $product_id, $prefix . 'award_points', true );

                    /**
                     * Filter to check if is available to award points for purchase
                     *
                     * @since 1.1.2
                     *
                     * @param bool                  $award_points   Whatever if should points be awarded to the user or not
                     * @param int                   $user_id        The user ID that will be awarded
                     * @param int                   $product_id     The product ID that user has purchased
                     * @param int                   $order_id       The order ID
                     * @param WC_Order_Item|array   $item           The order item object
                     */
                    $award_points = apply_filters( 'gamipress_wc_award_points_for_purchase', $award_points, $user_id, $product_id, $order_id, $item );

                    if( $award_points ) {

                        $product = wc_get_product( $product_id );

                        // Get the amount of points to award and the points type
                        $points = absint( get_post_meta( $product_id, $prefix . 'points', true ) );
                        $points_type = get_post_meta( $product_id, $prefix . 'points_type', true );

                        // The amount to award if based to the quantity added to the cart
                        $points = absint( $points * $quantity );

                        // Setup the custom reason for the log
                        if( $quantity === 1 ) {
                            $reason =  sprintf(
                                __( '%d %s deducted for the refund of the purchase of %s', 'gamipress' ),
                                $points,
                                gamipress_get_points_type_plural( $points_type ),
                                $product->get_name()
                            );
                        } else {
                            $reason =  sprintf(
                                __( '%d %s deducted for the refund of the purchase of %d %s', 'gamipress' ),
                                $points,
                                gamipress_get_points_type_plural( $points_type ),
                                $quantity,
                                $product->get_name()
                            );
                        }

                        // Setup the points award args
                        $args = array(
                            'reason' => $reason,
                            'log_type' => 'points_deduct',
                        );

                        // Deduct the points to the user
                        gamipress_deduct_points_to_user( $user_id, $points, $points_type, $args );

                        $points_type_data = gamipress_get_points_type( $points_type );

                        // Insert the custom user earning for this purchase
                        gamipress_insert_user_earning( $user_id, array(
                            'title'	        => $reason,
                            'user_id'	    => $user_id,
                            'post_id'	    => $points_type_data['ID'],
                            'post_type' 	=> 'points-type',
                            'points'	    => $points * -1, // add a negative amount
                            'points_type'	=> $points_type,
                            'date'	        => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
                        ) );

                    }

                }

            }

        }

    }

    // Set a post meta to meet that points have been awarded
    update_post_meta( $order_id, $prefix . 'points_awarded', '' );

}

// Check order status changes to meet if should award to the user
function gamipress_wc_maybe_award_points_on_order_status_change( $order_id, $from, $to, $order ) {

    if( $from !== 'completed' && $to === 'completed' ) {
        gamipress_wc_maybe_award_points_on_purchase( $order_id );
    }

    if( $from !== 'refunded' && $to === 'refunded' ) {
        gamipress_wc_maybe_deduct_points_on_refund( $order_id );
    }

}
add_action( 'woocommerce_order_status_changed', 'gamipress_wc_maybe_award_points_on_order_status_change', 10, 4 );

// --------------------------------------------
// Common listeners
// --------------------------------------------

// Purchase listener
function gamipress_wc_new_purchase( $order_id ) {

    global $duplicated_order_id;

    $order = wc_get_order( $order_id );

    // Bail if not a valid order
    if( ! $order ) return;

    // Bail if order is not marked as completed
    if ( $order->get_status() !== 'completed' ) {
        return;
    }
    
    if ( empty( $duplicated_order_id ) ) {
        $duplicated_order_id = 0;
    }

    // To avoid points ducplication
    if ( $duplicated_order_id === $order_id ){
        $duplicated_order_id = 0;
        return;
    } else {
        $duplicated_order_id = $order_id;
    }
    
    $user_id = $order->get_user_id();
    $order_total = $order->get_total();

    // Trigger new purchase
    do_action( 'gamipress_wc_new_purchase', $order_id, $user_id );
    do_action( 'gamipress_wc_new_purchase_total', $order_id, $user_id, $order_total );

    $items = $order->get_items();

    if ( is_array( $items ) ) {

        // On purchase, trigger events on each product purchased
        foreach ( $items as $item ) {
            
            $product_id     = 0;
            $variation_id   = 0;
            $quantity       = 1;

            if( class_exists( 'WC_Order_Item' ) && $item instanceof WC_Order_Item ) {

                // WooCommerce >= 3.0.0
                $product_id     = $item->get_product_id();
                $variation_id   = $item->get_variation_id();
                $quantity       = $item->get_quantity();

            } else if( is_array( $item ) && isset( $item['product_id'] ) ) {

                // WooCommerce < 3.0.0
                $product_id     = $item['product_id'];
                $variation_id   = ( isset( $item['variation_id'] ) ? $item['variation_id'] : 0 );
                $quantity       = isset( $item['qty'] ) ? absint( $item['qty'] ) : 1;

            }

            if( $product_id !== 0 ) {

                // Trigger events same times as item quantity
                for ( $i = 0; $i < $quantity; $i++ ) {
                    
                    $vendor_id = absint( get_post_field( 'post_author', $product_id ) );

                    if( $vendor_id !== 0 ) {
                        // Trigger new product sale to award the vendor
                        do_action( 'gamipress_wc_new_sale', $product_id, $vendor_id, $order_id, $quantity );
                    }

                    // Trigger new product purchase
                    do_action( 'gamipress_wc_new_product_purchase', $product_id, $user_id, $order_id, $quantity );
                    
                    // Trigger specific product purchase
                    do_action( 'gamipress_wc_specific_product_purchase', $product_id, $user_id, $order_id, $quantity );

                    if( $variation_id !== 0 ) {
                        // Trigger specific product variation purchase
                        do_action( 'gamipress_wc_product_variation_purchase', $product_id, $user_id, $variation_id, $order_id, $quantity );
                    }

                    // Get an array of categories IDs attached to the product
                    $categories = wc_get_product_term_ids( $product_id, 'product_cat' );

                    if( ! empty( $categories ) ) {

                        foreach( $categories as $category_id ) {
                            // Trigger specific product category purchase (trigger 1 event per category)
                            do_action( 'gamipress_wc_product_category_purchase', $product_id, $user_id, $category_id, $order_id, $quantity );
                        }

                    }

                    // Get an array of tags IDs attached to the product
                    $tags = wc_get_product_term_ids( $product_id, 'product_tag' );

                    if( ! empty( $tags ) ) {

                        foreach( $tags as $tag_id ) {
                            // Trigger specific product tag purchase (trigger 1 event per tag)
                            do_action( 'gamipress_wc_product_tag_purchase', $product_id, $user_id, $tag_id, $order_id, $quantity );
                        }

                    }

                }

            }

        } // end foreach

    } // end if $items is an array

}
add_action( 'woocommerce_payment_complete', 'gamipress_wc_new_purchase' );

// Refund purchase listener
function gamipress_wc_purchase_refund( $order_id ) {

    $order = wc_get_order( $order_id );
    $user_id = $order->get_user_id();

    if( $order ) {
        // Trigger purchase refund
        do_action( 'gamipress_wc_purchase_refund', $order_id, $user_id );

        $items = $order->get_items();

        if ( is_array( $items ) ) {

            // On purchase, trigger events on each product purchased
            foreach ( $items as $item ) {

                $product_id     = 0;
                $variation_id   = 0;
                $quantity       = 1;

                if( class_exists( 'WC_Order_Item' ) && $item instanceof WC_Order_Item ) {

                    // WooCommerce >= 3.0.0
                    $product_id     = $item->get_product_id();
                    $variation_id   = $item->get_variation_id();
                    $quantity       = $item->get_quantity();

                } else if( is_array( $item ) && isset( $item['product_id'] ) ) {

                    // WooCommerce < 3.0.0
                    $product_id     = $item['product_id'];
                    $variation_id   = ( isset( $item['variation_id'] ) ? $item['variation_id'] : 0 );
                    $quantity       = isset( $item['qty'] ) ? absint( $item['qty'] ) : 1;

                }

                if( $product_id !== 0 ) {

                    // Trigger events same times as item quantity
                    for ( $i = 0; $i < $quantity; $i++ ) {

                        $vendor_id = absint( get_post_field( 'post_author', $product_id ) );

                        if( $vendor_id !== 0 ) {
                            // Trigger product refunded to award the vendor
                            do_action( 'gamipress_wc_user_product_refund', $product_id, $vendor_id, $order_id, $quantity );
                        }

                        // Trigger product refund
                        do_action( 'gamipress_wc_product_refund', $product_id, $user_id, $order_id, $quantity );

                        // Trigger specific product refund
                        do_action( 'gamipress_wc_specific_product_refund', $product_id, $user_id, $order_id, $quantity );

                        if( $variation_id !== 0 ) {

                            // Trigger specific product variation refund
                            do_action( 'gamipress_wc_product_variation_refund', $product_id, $user_id, $variation_id, $order_id, $quantity );

                        }

                        // Get an array of categories IDs attached to the product
                        $categories = wc_get_product_term_ids( $product_id, 'product_cat' );

                        if( ! empty( $categories ) ) {

                            foreach( $categories as $category_id ) {
                                // Trigger specific product category refund (trigger 1 event per category)
                                do_action( 'gamipress_wc_product_category_refund', $product_id, $user_id, $category_id, $order_id, $quantity );
                            }

                        }

                        // Get an array of tags IDs attached to the product
                        $tags = wc_get_product_term_ids( $product_id, 'product_tag' );

                        if( ! empty( $tags ) ) {

                            foreach( $tags as $tag_id ) {
                                // Trigger specific product tag refund (trigger 1 event per tag)
                                do_action( 'gamipress_wc_product_tag_refund', $product_id, $user_id, $tag_id, $order_id, $quantity );
                            }

                        }

                    }

                }

            } // end foreach

        } // end if $items is an array

    } // end if $order

}

// Check order status changes to meet if should award to the user
function gamipress_wc_check_order_status_change( $order_id, $from, $to, $order ) {

    if( $from !== 'completed' && $to === 'completed' ) {
        gamipress_wc_new_purchase( $order_id );
    }

    if( $from !== 'refunded' && $to === 'refunded' ) {
        gamipress_wc_purchase_refund( $order_id );
    }

}
add_action( 'woocommerce_order_status_changed', 'gamipress_wc_check_order_status_change', 10, 4 );

// Review listener
function gamipress_wc_approved_review_listener( $comment_ID, $comment ) {

    // Enforce array for both hooks (wp_insert_comment uses object, comment_{status}_comment uses array)
    if ( is_object( $comment ) ) {
        $comment = get_object_vars( $comment );
    }

    $comment_ID = absint( $comment_ID );
    $user_id = absint( $comment[ 'user_id' ] );
    $post_id = absint( $comment[ 'comment_post_ID' ] );
    $post = get_post( $post_id );

    // Check if comment is a review
    // In some release, WooCommerce stop to set the comment_type as review and now reviews are based on assigned post ID post_type
    //if ( $comment[ 'comment_type' ] !== 'review' ) {
    if ( $post->post_type !== 'product' ) {
        return;
    }

    // Check if review is approved
    if ( 1 !== (int) $comment[ 'comment_approved' ] ) {
        return;
    }

    // Trigger review actions
    do_action( 'gamipress_wc_new_review', $comment_ID, $user_id, $post_id, $comment );
    do_action( 'gamipress_wc_specific_new_review', $comment_ID, $user_id, $post_id, $comment );

    if ( absint( $post->post_author ) !== 0 ) {
        // Trigger get review actions to product author
        do_action( 'gamipress_wc_get_review', $comment_ID, absint( $post->post_author ), $post_id, $user_id, $comment );
        do_action( 'gamipress_wc_get_specific_review', $comment_ID, absint( $post->post_author ), $post_id, $user_id, $comment );
    }

}
add_action( 'comment_approved_', 'gamipress_wc_approved_review_listener', 10, 2 );
add_action( 'comment_approved_review', 'gamipress_wc_approved_review_listener', 10, 2 );
add_action( 'wp_insert_comment', 'gamipress_wc_approved_review_listener', 10, 2 );

/**
 * Purchase subscription listener
 *
 * @since 1.0.0
 *
 * @param WC_Subscription $subscription
 */
function gamipress_wc_subscription_purchase_listener( $subscription ) {

    $items = $subscription->get_items();

    // Bail if no items purchased
    if ( ! is_array( $items ) ) {
        return;
    }

    $user_id = $subscription->get_user_id();

    // Loop all items to trigger events on each one purchased
    foreach ( $items as $item ) {

        $product_id     = $item->get_product_id();
        $quantity       = $item->get_quantity();

        // Skip items not assigned to a product
        if( $product_id === 0 ) {
            continue;
        }

        // Trigger events same times as item quantity
        for ( $i = 0; $i < $quantity; $i++ ) {

            // Any product subscription purchase
            do_action( 'gamipress_wc_subscription_purchase', $product_id, $user_id, $subscription->get_id() );

            // Specific product subscription purchase
            do_action( 'gamipress_wc_specific_subscription_purchase', $product_id, $user_id, $subscription->get_id() );


        } // End for of quantities

    } // End foreach of items


}
add_action( 'woocommerce_subscription_payment_complete', 'gamipress_wc_subscription_purchase_listener' );

/**
 * Subscription renewal listener
 *
 * @since 1.0.0
 *
 * @param int $order_id Order ID
 */

function gamipress_wc_subscription_renewal_listener( $order_id ) {

    $order = wc_get_order( $order_id );
    $items = $order->get_items();
    
    // Bail if no items purchased
    if ( ! is_array( $items ) ) {
        return;
    }

    $user_id = $order->get_user_id();

    // Loop all items to trigger events on each one purchased
    foreach ( $items as $item ) {

        $product_id     = $item->get_product_id();
        $quantity       = $item->get_quantity();

        // Skip items not assigned to a product
        if( $product_id === 0 ) {
            continue;
        }
        
        // Trigger events same times as item quantity
        for ( $i = 0; $i < $quantity; $i++ ) {

            // Any product subscription renewal
            do_action( 'gamipress_wc_subscription_renewal', $product_id, $user_id, $order_id );

            // Specific product subscription renewal
            do_action( 'gamipress_wc_specific_subscription_renewal', $product_id, $user_id, $order_id );


        } // End for of quantities

    } // End foreach of items

}
add_action( 'woocommerce_renewal_order_payment_complete', 'gamipress_wc_subscription_renewal_listener' );

/**
 * Subscription status updated listener
 *
 * @since 1.0.0
 *
 * @param WC_Subscription $subscription
 */
function gamipress_wc_subscription_status_updated_listener( $subscription ) {

    $status = false;

    if( $subscription->has_status( array( 'cancelled' ) ) ) {
        $status = 'cancelled';
    } else if( $subscription->has_status( array( 'expired' ) ) ) {
        $status = 'expired';
    }

    if( $status === false ) {
        return;
    }

    $items = $subscription->get_items();

    // Bail if no items purchased
    if ( ! is_array( $items ) ) {
        return;
    }

    $user_id = $subscription->get_user_id();

    // Loop all items to trigger events on each one purchased
    foreach ( $items as $item ) {

        $product_id     = $item->get_product_id();
        $quantity       = $item->get_quantity();

        // Skip items not assigned to a product
        if( $product_id === 0 ) {
            continue;
        }

        // Trigger events same times as item quantity
        for ( $i = 0; $i < $quantity; $i++ ) {

            // Any product subscription cancelled or expired
            do_action( "gamipress_wc_subscription_{$status}", $product_id, $user_id, $subscription->get_id() );

            // Specific product subscription cancelled or expired
            do_action( "gamipress_wc_specific_subscription_{$status}", $product_id, $user_id, $subscription->get_id() );


        } // End for of quantities

    } // End foreach of items

}
add_action( 'woocommerce_subscription_status_updated', 'gamipress_wc_subscription_status_updated_listener' );

/**
 * Membership created listener
 *
 * @since 1.0.0
 *
 * @param $membership_plan
 * @param array $data
 */
function gamipress_wc_membership_added_listener( $membership_plan, $data ) {

    $user_id = absint( $data['user_id'] );

    // Bail if not user provided
    if( $user_id === 0 ) {
        return;
    }

    // Get the order ID
    $order_id = 0;
    $access_method = get_post_meta( $membership_plan->id, '_access_method', true );

    if ( $access_method === 'purchase' ) {
        $order_id = get_post_meta( $data['user_membership_id'], '_order_id', true );
    }

    // Any membership added
    do_action( 'gamipress_wc_membership_added', $membership_plan->id, $user_id, $order_id );

    // Specific membership added
    do_action( 'gamipress_wc_specific_membership_added', $membership_plan->id, $user_id, $order_id );

}
add_action( 'wc_memberships_user_membership_saved', 'gamipress_wc_membership_added_listener', 10, 2 );

/**
 * Membership status changed listener
 *
 * @since 1.0.0
 *
 * @param $membership_plan
 * @param string $old_status
 * @param string $new_status
 */
function gamipress_wc_membership_status_changed_listener( $membership_plan, $old_status, $new_status ) {

    $user_id = absint( $membership_plan->user_id );

    // Bail if not user provided
    if( $user_id === 0 ) {
        return;
    }

    if( $old_status === $new_status ) {
        return;
    }

    // Get the order ID
    $order_id = 0;
    $access_method = get_post_meta( $membership_plan->plan_id, '_access_method', true );

    if ( $access_method === 'purchase' ) {
        $order_id = get_post_meta( $membership_plan->post->ID, '_order_id', true );
    }

    // Any membership cancelled/expired
    do_action( "gamipress_wc_membership_{$new_status}", $membership_plan->id, $user_id, $order_id );

    // Specific membership cancelled/expired
    do_action( "gamipress_wc_specific_membership_{$new_status}", $membership_plan->id, $user_id, $order_id );

}
add_action( 'wc_memberships_user_membership_status_changed', 'gamipress_wc_membership_status_changed_listener', 10, 3 );

/**
 * Trigger listener
 *
 * @since 1.0.0
 *
 * @param int $order_id The order ID
 */
function gamipress_wc_lifetime_value( $order_id ) {

    $order = wc_get_order( $order_id );
    $user_id = $order->get_user_id();

    //Bail if not user
    if ( $user_id === 0 ){
        return;
    }

    $lifetime_value = wc_get_customer_total_spent( $user_id );

    // Trigger lifetime value
    do_action( 'gamipress_wc_lifetime_value', $lifetime_value, $user_id );

}
add_action( 'woocommerce_order_status_completed', 'gamipress_wc_lifetime_value', 10, 1 );