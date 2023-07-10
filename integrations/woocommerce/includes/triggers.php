<?php
/**
 * Triggers
 *
 * @package GamiPress\WooCommerce\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register WooCommerce activity triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_wc_activity_triggers( $triggers ) {

    $triggers[__( 'WooCommerce', 'gamipress' )] = array(

        'gamipress_publish_product'                         => __( 'Publish a new product', 'gamipress' ), // Internal GamiPress listener

        // Purchase
        'gamipress_wc_new_purchase'                         => __( 'Make a new purchase', 'gamipress' ),
        'gamipress_wc_new_purchase_total'                   => __( 'Complete a purchase where total is equal, less or greater than a value', 'gamipress' ),

        'gamipress_wc_new_product_purchase'                 => __( 'Purchase a product', 'gamipress' ),
        'gamipress_wc_specific_product_purchase'            => __( 'Purchase a specific product', 'gamipress' ),
        'gamipress_wc_product_variation_purchase'           => __( 'Purchase a specific product variation', 'gamipress' ),

        'gamipress_wc_product_category_purchase'            => __( 'Purchase a product of a specific category', 'gamipress' ),
        'gamipress_wc_product_tag_purchase'                 => __( 'Purchase a product of a specific tag', 'gamipress' ),
        'gamipress_wc_lifetime_value'                       => __( 'Lifetime value is equal, less or greater than a value', 'gamipress' ),

        // Reviews
        'gamipress_wc_new_review'                           => __( 'Review a product', 'gamipress' ),
        'gamipress_wc_specific_new_review'                  => __( 'Review a specific product', 'gamipress' ),
        'gamipress_wc_get_review'                           => __( 'Vendor gets a review on a product', 'gamipress' ),
        'gamipress_wc_get_specific_review'                  => __( 'Vendor gets a review on a specific product', 'gamipress' ),

        'gamipress_wc_new_sale'                             => __( 'Vendor gets a new sale', 'gamipress' ),

        // Refund
        'gamipress_wc_purchase_refund'                      => __( 'Refund a purchase', 'gamipress' ),
        'gamipress_wc_product_refund'                       => __( 'Refund a product', 'gamipress' ),
        'gamipress_wc_specific_product_refund'              => __( 'Refund a specific product', 'gamipress' ),
        'gamipress_wc_product_variation_refund'             => __( 'Refund a specific product variation', 'gamipress' ),

        'gamipress_wc_product_category_refund'              => __( 'Refund a product of a specific category', 'gamipress' ),
        'gamipress_wc_product_tag_refund'                   => __( 'Refund a product of a specific tag', 'gamipress' ),

        'gamipress_wc_user_product_refund'                  => __( 'Vendor gets a product refunded', 'gamipress' ),
    );

    $triggers[__( 'WooCommerce Subscriptions', 'gamipress' )] = array(
        // Subscriptions
        'gamipress_wc_subscription_purchase'                => __( 'Purchase a subscription product', 'gamipress' ),
        'gamipress_wc_specific_subscription_purchase'       => __( 'Purchase a specific subscription product', 'gamipress' ),
        'gamipress_wc_subscription_renewal'                 => __( 'Renew a subscription product', 'gamipress' ),
        'gamipress_wc_specific_subscription_renewal'        => __( 'Renew a specific subscription product', 'gamipress' ),
        'gamipress_wc_subscription_cancelled'               => __( 'Cancel a subscription product', 'gamipress' ),
        'gamipress_wc_specific_subscription_cancelled'      => __( 'Cancel a specific subscription product', 'gamipress' ),
        'gamipress_wc_subscription_expired'                 => __( 'Subscription product expires', 'gamipress' ),
        'gamipress_wc_specific_subscription_expired'        => __( 'Specific subscription product expires', 'gamipress' ),
    );

    $triggers[__( 'WooCommerce Memberships', 'gamipress' )] = array(
        // Memberships
        'gamipress_wc_membership_added'                 => __( 'Get added to a membership', 'gamipress' ),
        'gamipress_wc_specific_membership_added'        => __( 'Get added to a specific membership', 'gamipress' ),
        'gamipress_wc_membership_cancelled'             => __( 'Cancel a membership', 'gamipress' ),
        'gamipress_wc_specific_membership_cancelled'    => __( 'Cancel a specific membership', 'gamipress' ),
        'gamipress_wc_membership_expired'               => __( 'Membership expires', 'gamipress' ),
        'gamipress_wc_specific_membership_expired'      => __( 'Specific membership expires', 'gamipress' ),
    );

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_wc_activity_triggers' );

/**
 * Register WooCommerce specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_wc_specific_activity_triggers( $specific_activity_triggers ) {

    // Purchase
    $specific_activity_triggers['gamipress_wc_specific_product_purchase'] = array( 'product' );
    $specific_activity_triggers['gamipress_wc_product_variation_purchase'] = array( 'product' );
    // Reviews
    $specific_activity_triggers['gamipress_wc_specific_new_review'] = array( 'product' );
    $specific_activity_triggers['gamipress_wc_get_specific_review'] = array( 'product' );
    // Refund
    $specific_activity_triggers['gamipress_wc_specific_product_refund'] = array( 'product' );
    $specific_activity_triggers['gamipress_wc_product_variation_refund'] = array( 'product' );

    // Subscriptions
    $specific_activity_triggers['gamipress_wc_specific_subscription_purchase'] = array( 'product' );
    $specific_activity_triggers['gamipress_wc_specific_subscription_renewal'] = array( 'product' );
    $specific_activity_triggers['gamipress_wc_specific_subscription_cancelled'] = array( 'product' );
    $specific_activity_triggers['gamipress_wc_specific_subscription_expired'] = array( 'product' );

    // Memberships
    $specific_activity_triggers['gamipress_wc_specific_membership_added'] = array( 'wc_membership_plan' );
    $specific_activity_triggers['gamipress_wc_specific_membership_cancelled'] = array( 'wc_membership_plan' );
    $specific_activity_triggers['gamipress_wc_specific_membership_expired'] = array( 'wc_membership_plan' );

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_wc_specific_activity_triggers' );

/**
 * Build custom activity trigger label
 *
 * @param string    $title
 * @param integer   $requirement_id
 * @param array     $requirement
 *
 * @return string
 */
function gamipress_wc_activity_trigger_label( $title, $requirement_id, $requirement ) {

    switch( $requirement['trigger_type'] ) {

        // Product variation
        case 'gamipress_wc_product_variation_purchase':
        case 'gamipress_wc_product_variation_refund':
            $variation_id = ( isset( $requirement['wc_variation_id'] ) ) ? absint( $requirement['wc_variation_id'] ) : 0;

            if( $variation_id !== 0 ) {

                // Setup the pattern based on trigger type given
                $pattern = __( 'Purchase %s', 'gamipress' );

                if( $requirement['trigger_type'] === 'gamipress_wc_product_variation_refund' ) {
                    $pattern = __( 'Refund %s', 'gamipress' );
                }

                // Return the custom title
                return sprintf( $pattern, gamipress_wc_get_product_variation_title( $requirement['achievement_post'], $variation_id, $requirement['achievement_post_site_id'] ) );
            }
            break;
        // Category
        case 'gamipress_wc_product_category_purchase':
        case 'gamipress_wc_product_category_refund':
            $category_id = ( isset( $requirement['wc_category_id'] ) ) ? absint( $requirement['wc_category_id'] ) : 0;

            if( $category_id !== 0 ) {

                // Setup the pattern based on trigger type given
                $pattern = __( 'Purchase a product of "%s" category', 'gamipress' );

                if( $requirement['trigger_type'] === 'gamipress_wc_product_variation_refund' ) {
                    $pattern = __( 'Refund a product of "%s" category', 'gamipress' );
                }

                $category = get_term_by( 'term_id', $category_id, 'product_cat' );

                // Return the custom title
                return sprintf( $pattern, $category->name );
            }
            break;
        // Tag
        case 'gamipress_wc_product_tag_purchase':
        case 'gamipress_wc_product_tag_refund':
            $tag_id = ( isset( $requirement['wc_tag_id'] ) ) ? absint( $requirement['wc_tag_id'] ) : 0;

            if( $tag_id !== 0 ) {

                // Setup the pattern based on trigger type given
                $pattern = __( 'Purchase a product with "%s" tag', 'gamipress' );

                if( $requirement['trigger_type'] === 'gamipress_wc_product_variation_refund' ) {
                    $pattern = __( 'Refund a product with "%s" tag', 'gamipress' );
                }

                $tag = get_term_by( 'term_id', $tag_id, 'product_tag' );

                // Return the custom title
                return sprintf( $pattern, $tag->name );
            }
            break;

        case 'gamipress_wc_new_purchase_total':
            $purchase_total = ( isset( $requirement['wc_purchase_total'] ) ) ? floatval( $requirement['wc_purchase_total'] ) : 0;
            $condition = ( isset( $requirement['wc_purchase_total_condition'] ) ) ? $requirement['wc_purchase_total_condition'] : 'equal';
            $conditions = gamipress_number_condition_options();

            // Complete a purchase where total is equal, less or greater than a value
            return sprintf( __( 'Complete a purchase where total is %s %s', 'gamipress' ), $conditions[$condition], $purchase_total );
        case 'gamipress_wc_lifetime_value':
            $lifetime = ( isset( $requirement['wc_lifetime'] ) ) ? floatval( $requirement['wc_lifetime'] ) : 0;
            $condition = ( isset( $requirement['wc_lifetime_condition'] ) ) ? $requirement['wc_lifetime_condition'] : 'equal';
            $conditions = gamipress_number_condition_options();

            // Lifetime value is equal, less or greater than a value
            return sprintf( __( 'Lifetime value %s %s', 'gamipress' ), $conditions[$condition], $lifetime );
            break;

    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_wc_activity_trigger_label', 10, 3 );

/**
 * Register WooCommerce specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_wc_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Purchase
    $specific_activity_trigger_labels['gamipress_wc_specific_product_purchase'] = __( 'Purchase %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wc_product_variation_purchase'] = __( 'Purchase %s', 'gamipress' );
    // Reviews
    $specific_activity_trigger_labels['gamipress_wc_specific_new_review'] = __( 'Review %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wc_get_specific_review'] = __( 'Vendor gets a review on %s', 'gamipress' );
    // Refund
    $specific_activity_trigger_labels['gamipress_wc_specific_product_refund'] = __( 'Refund %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wc_product_variation_refund'] = __( 'Refund %s', 'gamipress' );

    // Subscriptions
    $specific_activity_trigger_labels['gamipress_wc_specific_subscription_purchase'] = __( 'Purchase %s subscription', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wc_specific_subscription_renewal'] = __( 'Renew %s subscription', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wc_specific_subscription_cancelled'] = __( 'Cancel %s subscription', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wc_specific_subscription_expired'] = __( '%s subscription expires', 'gamipress' );

    // Memberships
    $specific_activity_trigger_labels['gamipress_wc_specific_membership_added'] = __( 'Get added to %s membership', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wc_specific_membership_cancelled'] = __( 'Cancel %s membership', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_wc_specific_membership_expires'] = __( '%s membership expires', 'gamipress' );

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_wc_specific_activity_trigger_label' );


/**
 * Get user for a given trigger action.
 *
 * @since  1.0.0
 *
 * @param  integer $user_id user ID to override.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 * @return integer          User ID.
 */
function gamipress_wc_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        case 'gamipress_publish_product': // Internal GamiPress listener
        // Purchase
        case 'gamipress_wc_new_purchase':
        case 'gamipress_wc_new_purchase_total':
        case 'gamipress_wc_new_product_purchase':
        case 'gamipress_wc_specific_product_purchase':
        case 'gamipress_wc_product_variation_purchase':
        case 'gamipress_wc_product_category_purchase':
        case 'gamipress_wc_product_tag_purchase':
        case 'gamipress_wc_lifetime_value':
        // Reviews
        case 'gamipress_wc_new_review':
        case 'gamipress_wc_specific_new_review':
        case 'gamipress_wc_get_review':
        case 'gamipress_wc_get_specific_review':
        case 'gamipress_wc_new_sale':
        // Refund
        case 'gamipress_wc_purchase_refund':
        case 'gamipress_wc_product_refund':
        case 'gamipress_wc_specific_product_refund':
        case 'gamipress_wc_product_variation_refund':
        case 'gamipress_wc_product_category_refund':
        case 'gamipress_wc_product_tag_refund':
        case 'gamipress_wc_user_product_refund':
        // Subscriptions
        case 'gamipress_wc_subscription_purchase':
        case 'gamipress_wc_specific_subscription_purchase':
        case 'gamipress_wc_subscription_renewal':
        case 'gamipress_wc_specific_subscription_renewal':
        case 'gamipress_wc_subscription_cancelled':
        case 'gamipress_wc_specific_subscription_cancelled':
        case 'gamipress_wc_subscription_expired':
        case 'gamipress_wc_specific_subscription_expired':
        // Memberships
        case 'gamipress_wc_membership_added':
        case 'gamipress_wc_specific_membership_added':
        case 'gamipress_wc_membership_cancelled':
        case 'gamipress_wc_specific_membership_cancelled':
        case 'gamipress_wc_membership_expired':
        case 'gamipress_wc_specific_membership_expired':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_wc_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.1
 *
 * @param  integer $specific_id Specific ID to override.
 * @param  string  $trigger     Trigger name.
 * @param  array   $args        Passed trigger args.
 *
 * @return integer              Specific ID.
 */
function gamipress_wc_specific_trigger_get_id( $specific_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Purchase
        case 'gamipress_wc_specific_product_purchase':
        case 'gamipress_wc_product_variation_purchase':
        case 'gamipress_wc_specific_subscription_purchase':
        case 'gamipress_wc_specific_subscription_expired':
        // Refund
        case 'gamipress_wc_specific_product_refund':
        case 'gamipress_wc_product_variation_refund':
        // Memberships
        case 'gamipress_wc_specific_membership_added':
        case 'gamipress_wc_specific_membership_cancelled':
        case 'gamipress_wc_specific_membership_expired':
        
            $specific_id = $args[0];
            break;
        // Reviews
        case 'gamipress_wc_specific_new_review':
        case 'gamipress_wc_get_specific_review':
        // Subscriptions
        case 'gamipress_wc_specific_subscription_renewal':
        case 'gamipress_wc_specific_subscription_cancelled':
        case 'gamipress_wc_specific_subscription_purchase':
        case 'gamipress_wc_specific_subscription_expired':
        
            $specific_id = $args[0];
            break;
    }

    return $specific_id;

}

add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_wc_specific_trigger_get_id', 10, 3 );

/**
 * Extended meta data for event trigger logging
 *
 * @since 1.0.2
 *
 * @param array 	$log_meta
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return array
 */
function gamipress_wc_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        case 'gamipress_publish_product': // Internal GamiPress listener
            // Add the product ID
            $log_meta['product_id'] = $args[0];
            $log_meta['post_id'] = $args[0]; // Post ID added to make column visible on logs list
            break;
        // Purchase
        case 'gamipress_wc_new_product_purchase':
        case 'gamipress_wc_specific_product_purchase':
        case 'gamipress_wc_new_sale':
        // Refund
        case 'gamipress_wc_product_refund':
        case 'gamipress_wc_specific_product_refund':
        case 'gamipress_wc_user_product_refund':
        // Subscriptions
        case 'gamipress_wc_subscription_purchase':
        case 'gamipress_wc_specific_subscription_purchase':
        case 'gamipress_wc_subscription_renewal':
        case 'gamipress_wc_specific_subscription_renewal':
        case 'gamipress_wc_subscription_cancelled':
        case 'gamipress_wc_specific_subscription_cancelled':
        case 'gamipress_wc_subscription_expired':
        case 'gamipress_wc_specific_subscription_expired':
            // Add the product and order IDs
            $log_meta['product_id'] = $args[0];
            $log_meta['post_id'] = $args[0]; // Post ID added to make column visible on logs list
            $log_meta['order_id'] = $args[2];
            break;
        case 'gamipress_wc_product_variation_purchase':
        case 'gamipress_wc_product_variation_refund':
            // Add the product, variation and order IDs
            $log_meta['product_id'] = $args[0];
            $log_meta['post_id'] = $args[0]; // Post ID added to make column visible on logs list
            $log_meta['variation_id'] = $args[2];
            $log_meta['order_id'] = $args[3];
            break;
        case 'gamipress_wc_product_category_purchase':
        case 'gamipress_wc_product_category_refund':
            // Add the product, category and order IDs
            $log_meta['product_id'] = $args[0];
            $log_meta['post_id'] = $args[0]; // Post ID added to make column visible on logs list
            $log_meta['category_id'] = $args[2];
            $log_meta['order_id'] = $args[3];
            break;
        case 'gamipress_wc_product_tag_purchase':
        case 'gamipress_wc_product_tag_refund':
            // Add the product, tag and order IDs
            $log_meta['product_id'] = $args[0];
            $log_meta['post_id'] = $args[0]; // Post ID added to make column visible on logs list
            $log_meta['tag_id'] = $args[2];
            $log_meta['order_id'] = $args[3];
            break;
        case 'gamipress_wc_new_purchase':
        case 'gamipress_wc_purchase_refund':
            // Add the order ID
            $log_meta['order_id'] = $args[0];
            break;
        // Purchase total
        case 'gamipress_wc_new_purchase_total':
            $log_meta['order_id'] = $args[0];
            $log_meta['purchase_total'] = $args[2];
            break;
        // Reviews
        case 'gamipress_wc_new_review':
        case 'gamipress_wc_specific_new_review':
            // Add the comment and product IDs
            $log_meta['comment_id'] = $args[0];
            $log_meta['product_id'] = $args[2];
            break;
        case 'gamipress_wc_get_review':
        case 'gamipress_wc_get_specific_review':
            // Add the comment, reviewer and product IDs
            $log_meta['comment_id'] = $args[0];
            $log_meta['product_id'] = $args[2];
            $log_meta['reviewer_id'] = $args[3];
            break;
        // Memberships
        case 'gamipress_wc_membership_added':
        case 'gamipress_wc_specific_membership_added':
        case 'gamipress_wc_membership_cancelled':
        case 'gamipress_wc_specific_membership_cancelled':
        case 'gamipress_wc_membership_expired':
        case 'gamipress_wc_specific_membership_expired':
            // Add the membership and order IDs
            $log_meta['post_id'] = $args[0]; // Post ID added to make column visible on logs list
            $log_meta['order_id'] = $args[2];
            break;
        // Lifetime
        case 'gamipress_wc_lifetime_value':
            $log_meta['lifetime_value'] = $args[0];


    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_wc_log_event_trigger_meta_data', 10, 5 );

/**
 * Override the meta data to filter the logs count
 *
 * @since   1.1.5
 *
 * @param  array    $log_meta       The meta data to filter the logs count
 * @param  int      $user_id        The given user's ID
 * @param  string   $trigger        The given trigger we're checking
 * @param  int      $since 	        The since timestamp where retrieve the logs
 * @param  int      $site_id        The desired Site ID to check
 * @param  array    $args           The triggered args or requirement object
 *
 * @return array                    The meta data to filter the logs count
 */
function gamipress_wc_get_user_trigger_count_log_meta( $log_meta, $user_id, $trigger, $since, $site_id, $args ) {

    switch( $trigger ) {
        // Variation
        case 'gamipress_wc_product_variation_purchase':
        case 'gamipress_wc_product_variation_refund':
            if( isset( $args[0] ) && isset( $args[2] ) ) {
                // Add the product and variation IDs
                $log_meta['product_id'] = absint( $args[0] );
                $log_meta['variation_id'] = absint( $args[2] );
            }

            // $args could be a requirement object
            if( isset( $args['wc_variation_id'] ) && isset( $args['achievement_post'] ) ) {
                // Add the product and variation IDs
                $log_meta['product_id'] = absint( $args['achievement_post'] );
                $log_meta['variation_id'] = absint( $args['wc_variation_id'] );
            }
            break;
        // Category
        case 'gamipress_wc_product_category_purchase':
        case 'gamipress_wc_product_category_refund':
            if( isset( $args[2] ) ) {
                // Add the product category ID
                $log_meta['category_id'] = $args[2];
            }

            // $args could be a requirement object
            if( isset( $args['wc_category_id'] ) ) {
                // Add the product category ID
                $log_meta['category_id'] = $args['wc_category_id'];
            }
            break;
        // Tag
        case 'gamipress_wc_product_tag_purchase':
        case 'gamipress_wc_product_tag_refund':
            if( isset( $args[2] ) ) {
                // Add the product tag ID
                $log_meta['tag_id'] = $args[2];
            }

            // $args could be a requirement object
            if( isset( $args['wc_tag_id'] ) ) {
                // Add the product tag ID
                $log_meta['tag_id'] = $args['wc_tag_id'];
            }
            break;
    }

    return $log_meta;

}
add_filter( 'gamipress_get_user_trigger_count_log_meta', 'gamipress_wc_get_user_trigger_count_log_meta', 10, 6 );

/**
 * Extra data fields
 *
 * @since 1.1.3
 *
 * @param array     $fields
 * @param int       $log_id
 * @param string    $type
 *
 * @return array
 */
function gamipress_wc_log_extra_data_fields( $fields, $log_id, $type ) {

    $prefix = '_gamipress_';

    $log = ct_get_object( $log_id );
    $trigger = $log->trigger_type;

    if( $type !== 'event_trigger' ) {
        return $fields;
    }

    switch( $trigger ) {
        // Product variation
        case 'gamipress_wc_product_variation_purchase':
        case 'gamipress_wc_product_variation_refund':

            // TODO: Important! call to wc_get_product() causes a value lost on all extra data fields

            $variation_id = ct_get_object_meta( $log_id, $prefix . 'variation_id', true );

            $variation_attributes = wc_get_product_variation_attributes( $variation_id );
            $attributes = array();

            foreach( $variation_attributes as $attribute ) {
                if( ! empty( $attribute ) ) {
                    $attributes[] = $attribute;
                }
            }

            $fields[] = array(
                'name' 	    => __( 'Variation', 'gamipress' ),
                'desc' 	    => __( 'Variation attached to this log.', 'gamipress' ),
                'id'   	    => $prefix . 'variation_id',
                'type' 	    => 'select',
                'options'   => array(
                    $variation_id => ( ! empty( $attributes ) ? implode( ', ', $attributes ) . ' (#' . $variation_id . ')' : '' )
                )
            );
        break;
        // Category
        case 'gamipress_wc_product_category_purchase':
        case 'gamipress_wc_product_category_refund':

            // Get categories stored and turn them into an array of options
            $categories = get_terms( array(
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
            ) );

            $options = array();

            foreach( $categories as $category ) {
                $options[$category->term_id] = $category->name;
            }

                $fields[] = array(
                'name' 	    => __( 'Category', 'gamipress' ),
                'desc' 	    => __( 'Category attached to this log.', 'gamipress' ),
                'id'   	    => $prefix . 'category_id',
                'type' 	    => 'select',
                'options'   => $options,
            );
            break;
        // Tag
        case 'gamipress_wc_product_tag_purchase':
        case 'gamipress_wc_product_tag_refund':

            // Get tags stored and turn them into an array of options
            $tags = get_terms( array(
                'taxonomy' => 'product_tag',
                'hide_empty' => false,
            ) );

            $options = array();

            foreach( $tags as $tag ) {
                $options[$tag->term_id] = $tag->name;
            }

            $fields[] = array(
                'name' 	    => __( 'Tag', 'gamipress' ),
                'desc' 	    => __( 'Tag attached to this log.', 'gamipress' ),
                'id'   	    => $prefix . 'tag_id',
                'type' 	    => 'select',
                'options'   => $options,
            );
            break;
    }

    return $fields;

}
add_filter( 'gamipress_log_extra_data_fields', 'gamipress_wc_log_extra_data_fields', 10, 3 );

/**
 * Extra filter to check duplicated activity
 *
 * @since 1.0.2
 *
 * @param bool 		$return
 * @param integer 	$user_id
 * @param string 	$trigger
 * @param integer 	$site_id
 * @param array 	$args
 *
 * @return bool					True if user deserves trigger, else false
 */
function gamipress_wc_trigger_duplicity_check( $return, $user_id, $trigger, $site_id, $args  ) {

    // If user doesn't deserves trigger, then bail to prevent grant access
    if( ! $return )
        return $return;

    $log_meta = array(
        'type' => 'event_trigger',
        'trigger_type' => $trigger,
    );

    switch ( $trigger ) {
        case 'gamipress_publish_product': // Internal GamiPress listener
            // User can not create same product more times, so check it
            $log_meta['post_id'] = gamipress_get_event_arg( $args, 'post_id', 0 );
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        case 'gamipress_wc_new_review':
        case 'gamipress_wc_specific_new_review':
        case 'gamipress_wc_get_review':
        case 'gamipress_wc_get_specific_review':
            // User can not create same comment more times, so check it
            $log_meta['comment_id'] = $args[0];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        // Purchase
        case 'gamipress_wc_new_product_purchase':
        case 'gamipress_wc_specific_product_purchase':
        case 'gamipress_wc_new_sale':
        // Refund
        case 'gamipress_wc_product_refund':
        case 'gamipress_wc_specific_product_refund':
        case 'gamipress_wc_user_product_refund':
        // Subscriptions
        case 'gamipress_wc_subscription_purchase':
        case 'gamipress_wc_specific_subscription_purchase':
        case 'gamipress_wc_subscription_renewal':
        case 'gamipress_wc_specific_subscription_renewal':
        case 'gamipress_wc_subscription_cancelled':
        case 'gamipress_wc_specific_subscription_cancelled':
        case 'gamipress_wc_subscription_expired':
        case 'gamipress_wc_specific_subscription_expired':

            $quantity = isset( $args[3] ) ? $args[3] : 1;

            // Don't perform duplicity check if quantity is higher than 1
             if( $quantity > 1 ) {
                break;
            }
 
            // User can not place same product and order IDs more times, so check it
            $log_meta['product_id'] = $args[0];
            $log_meta['order_id'] = $args[2];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        // Variation
        case 'gamipress_wc_product_variation_purchase':
        case 'gamipress_wc_product_variation_refund':

            $quantity = isset( $args[4] ) ? $args[4] : 1;

            // Don't perform duplicity check if quantity is higher than 1
            if( $quantity > 1 ) {
                break;
            }

            // User can not place same product and order ID more times, so check it
            $log_meta['product_id'] = $args[0];
            $log_meta['variation_id'] = $args[2];
            $log_meta['order_id'] = $args[3];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        // Category
        case 'gamipress_wc_product_category_purchase':
        case 'gamipress_wc_product_category_refund':

            $quantity = isset( $args[4] ) ? $args[4] : 1;

            // Don't perform duplicity check if quantity is higher than 1
            if( $quantity > 1 ) {
                break;
            }

            // User can not place same product, category and order ID more times, so check it
            $log_meta['product_id'] = $args[0];
            $log_meta['category_id'] = $args[2];
            $log_meta['order_id'] = $args[3];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        // Tag
        case 'gamipress_wc_product_tag_purchase':
        case 'gamipress_wc_product_tag_refund':

            $quantity = isset( $args[4] ) ? $args[4] : 1;

            // Don't perform duplicity check if quantity is higher than 1
            if( $quantity > 1 ) {
                break;
            }

            // User can not place same product, tag and order ID more times, so check it
            $log_meta['product_id'] = $args[0];
            $log_meta['tag_id'] = $args[2];
            $log_meta['order_id'] = $args[3];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        case 'gamipress_wc_new_purchase':
        case 'gamipress_wc_purchase_refund':
            // User can not place or refund same order ID more times, so check it
            $log_meta['order_id'] = $args[0];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
    }

    return $return;

}
add_filter( 'gamipress_user_deserves_trigger', 'gamipress_wc_trigger_duplicity_check', 10, 5 );