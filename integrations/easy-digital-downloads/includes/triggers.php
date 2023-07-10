<?php
/**
 * Triggers
 *
 * @package GamiPress\Easy_Digital_Downloads\Triggers
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register plugin activity triggers
 *
 * @param array $triggers
 * @return mixed
 */
function gamipress_edd_activity_triggers( $triggers ) {

    // Easy Digital Downloads
    $triggers[__( 'Easy Digital Downloads', 'gamipress' )] = array(
        'gamipress_publish_download'                => __( 'Publish a new download', 'gamipress' ), // Internal GamiPress listener
        
        'gamipress_edd_lifetime_value'              => __( 'Lifetime value is equal, less or greater than a value', 'gamipress' ),
        
        // Purchase
        'gamipress_edd_new_purchase'                => __( 'Make a new purchase', 'gamipress' ),

        'gamipress_edd_new_download_purchase'       => __( 'Purchase a download', 'gamipress' ),
        'gamipress_edd_specific_download_purchase'  => __( 'Purchase a specific download', 'gamipress' ),
        'gamipress_edd_new_free_download_purchase'  => __( 'Purchase a free download', 'gamipress' ),
        'gamipress_edd_new_paid_download_purchase'  => __( 'Purchase a paid download', 'gamipress' ),
        'gamipress_edd_download_variation_purchase' => __( 'Purchase a specific download variation', 'gamipress' ),

        'gamipress_edd_download_category_purchase'  => __( 'Purchase a download of a specific category', 'gamipress' ),
        'gamipress_edd_download_tag_purchase'       => __( 'Purchase a download of a specific tag', 'gamipress' ),

        'gamipress_edd_new_sale'                    => __( 'Vendor gets a new sale', 'gamipress' ),
        // Refund
        'gamipress_edd_purchase_refund'             => __( 'Refund a purchase', 'gamipress' ),
        'gamipress_edd_download_refund'             => __( 'Refund a download', 'gamipress' ),
        'gamipress_edd_specific_download_refund'    => __( 'Refund a specific download', 'gamipress' ),

        'gamipress_edd_download_variation_refund'   => __( 'Refund a specific download variation', 'gamipress' ),
        'gamipress_edd_download_category_refund'    => __( 'Refund a download of a specific category', 'gamipress' ),
        'gamipress_edd_download_tag_refund'         => __( 'Refund a download of a specific tag', 'gamipress' ),

        'gamipress_edd_user_download_refund'        => __( 'Vendor gets a download refunded', 'gamipress' ),
    );

    // EDD FES
    if( class_exists('EDD_Front_End_Submissions') ) {

        $triggers[__( 'EDD - FrontEnd Submissions', 'gamipress' )] = array(
            'gamipress_edd_approve_download'        => __( 'Vendor get approved a new download', 'gamipress' ),
        );
    }

    // EDD Wish Lists
    if( class_exists('EDD_Wish_Lists') ) {
        $triggers[__( 'EDD - Wish Lists', 'gamipress' )] = array(
            'gamipress_publish_edd_wish_list'           => __( 'Create a new wish list', 'gamipress' ), // Internal GamiPress listener
            'gamipress_edd_add_to_wish_list'            => __( 'Add a download to any wish list', 'gamipress' ),
            'gamipress_edd_add_specific_to_wish_list'   => __( 'Add a specific download to any wish list', 'gamipress' ),
        );
    }

    // EDD Downloads Lists
    if( class_exists('EDD_Downloads_Lists') ) {
        $triggers[__( 'EDD - Downloads Lists', 'gamipress' )] = array(
            'gamipress_edd_wish_download'               => __( 'Add a download to their wishes list', 'gamipress' ),
            'gamipress_edd_wish_specific_download'      => __( 'Add a specific download to their wishes list', 'gamipress' ),
            'gamipress_edd_favorite_download'           => __( 'Add a download to their favorites list', 'gamipress' ),
            'gamipress_edd_favorite_specific_download'  => __( 'Add a specific download to their favorites list', 'gamipress' ),
            'gamipress_edd_like_download'               => __( 'Add a download to their likes list', 'gamipress' ),
            'gamipress_edd_like_specific_download'      => __( 'Add a specific download to their likes list', 'gamipress' ),
            'gamipress_edd_recommend_download'          => __( 'Add a download to their recommendations list', 'gamipress' ),
            'gamipress_edd_recommend_specific_download' => __( 'Add a specific download to their recommendations list', 'gamipress' )
        );
    }

    // EDD Reviews
    if( class_exists('EDD_Reviews') ) {
        $triggers[__( 'EDD - Reviews', 'gamipress' )] = array(
            'gamipress_edd_new_review'          => __( 'Review a download', 'gamipress' ),
            'gamipress_edd_specific_new_review' => __( 'Review a specific download', 'gamipress' ),
            'gamipress_edd_get_review'          => __( 'Vendor gets a review on a download', 'gamipress' ),
            'gamipress_edd_get_specific_review' => __( 'Vendor gets a review on a specific download', 'gamipress' )
        );
    }

    // EDD Download Pages
    if( class_exists('EDD_Download_Pages') ) {
        $download_pages_index = __( 'EDD - Download Pages', 'gamipress' );

        $triggers[$download_pages_index] = array(
            'gamipress_publish_edd_download_page' => __( 'Publish a new download page', 'gamipress' ) // Internal GamiPress listener
        );

        // EDD FrontEnd Submissions + EDD Download Pages
        if( class_exists('EDD_FES') ) {
            $triggers[$download_pages_index]['gamipress_edd_approve_edd_download_page'] = __( 'Vendor get approved a new download page', 'gamipress' );
        }
    }

    // EDD Social Discounts
    if( class_exists('EDD_Social_Discounts') ) {
        $triggers[__( 'EDD - Download Pages', 'gamipress' )] = array(
            'gamipress_edd_share_download'          => __( 'Share a download', 'gamipress' ),
            'gamipress_edd_share_specific_download' => __( 'Share a specific download', 'gamipress' )
        );
    }

    return $triggers;

}
add_filter( 'gamipress_activity_triggers', 'gamipress_edd_activity_triggers' );

/**
 * Register Easy Digital Downloads specific activity triggers
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_triggers
 * @return array
 */
function gamipress_edd_specific_activity_triggers( $specific_activity_triggers ) {

    // Purchase
    $specific_activity_triggers['gamipress_edd_specific_download_purchase'] = array( 'download' );
    $specific_activity_triggers['gamipress_edd_download_variation_purchase'] = array( 'download' );
    // Refund
    $specific_activity_triggers['gamipress_edd_specific_download_refund'] = array( 'download' );
    $specific_activity_triggers['gamipress_edd_download_variation_refund'] = array( 'download' );

    // EDD Wish Lists
    if( class_exists('EDD_Wish_Lists') ) {
        $specific_activity_triggers['gamipress_edd_add_specific_to_wish_list'] = array( 'download' );
    }

    // EDD Downloads Lists
    if( class_exists('EDD_Downloads_Lists') ) {
        $specific_activity_triggers['gamipress_edd_wish_specific_download'] = array( 'download' );
        $specific_activity_triggers['gamipress_edd_favorite_specific_download'] = array( 'download' );
        $specific_activity_triggers['gamipress_edd_like_specific_download'] = array( 'download' );
        $specific_activity_triggers['gamipress_edd_recommend_specific_download'] = array( 'download' );
    }

    // EDD Reviews
    if( class_exists('EDD_Reviews') ) {
        $specific_activity_triggers['gamipress_edd_specific_new_review'] = array( 'download' );
        $specific_activity_triggers['gamipress_edd_get_specific_review'] = array( 'download' );
    }

    // EDD Social Discounts
    if( class_exists('EDD_Social_Discounts') ) {
        $specific_activity_triggers['gamipress_edd_share_specific_download'] = array( 'download' );
    }

    return $specific_activity_triggers;
}
add_filter( 'gamipress_specific_activity_triggers', 'gamipress_edd_specific_activity_triggers' );

/**
 * Build custom activity trigger label
 *
 * @param string    $title
 * @param integer   $requirement_id
 * @param array     $requirement
 *
 * @return string
 */
function gamipress_edd_activity_trigger_label( $title, $requirement_id, $requirement ) {

    $lifetime = ( isset( $requirement['edd_lifetime'] ) ) ? absint( $requirement['edd_lifetime'] ) : 0;
    $lifetime_condition = ( isset( $requirement['edd_lifetime_condition'] ) ) ? $requirement['edd_lifetime_condition'] : 'equal';
    $lifetime_conditions = gamipress_edd_get_lifetime_conditions();

    switch( $requirement['trigger_type'] ) {

        // Download variation
        case 'gamipress_edd_download_variation_purchase':
        case 'gamipress_edd_download_variation_refund':
            $variation_id = ( isset( $requirement['edd_variation_id'] ) ) ? absint( $requirement['edd_variation_id'] ) : 0;

            if( $variation_id !== 0 ) {

                // Setup the pattern based on trigger type given
                $pattern = __( 'Purchase %s', 'gamipress' );

                if( $requirement['trigger_type'] === 'gamipress_edd_download_variation_refund' ) {
                    $pattern = __( 'Refund %s', 'gamipress' );
                }

                // Return the custom title
                return sprintf( $pattern, gamipress_edd_get_download_variation_title( $requirement['achievement_post'], $variation_id, $requirement['achievement_post_site_id'] ) );
            }
            break;
        // Category
        case 'gamipress_edd_download_category_purchase':
        case 'gamipress_edd_download_category_refund':
            $category_id = ( isset( $requirement['edd_category_id'] ) ) ? absint( $requirement['edd_category_id'] ) : 0;

            if( $category_id !== 0 ) {

                // Setup the pattern based on trigger type given
                $pattern = __( 'Purchase a download of "%s" category', 'gamipress' );

                if( $requirement['trigger_type'] === 'gamipress_edd_download_category_refund' ) {
                    $pattern = __( 'Refund a download of "%s" category', 'gamipress' );
                }

                $category = get_term_by( 'term_id', $category_id, 'download_category' );

                // Return the custom title
                return sprintf( $pattern, $category->name );
            }
            break;
        // Tag
        case 'gamipress_edd_download_tag_purchase':
        case 'gamipress_edd_download_tag_refund':
            $tag_id = ( isset( $requirement['edd_tag_id'] ) ) ? absint( $requirement['edd_tag_id'] ) : 0;

            if( $tag_id !== 0 ) {

                // Setup the pattern based on trigger type given
                $pattern = __( 'Purchase a download with "%s" tag', 'gamipress' );

                if( $requirement['trigger_type'] === 'gamipress_edd_download_tag_refund' ) {
                    $pattern = __( 'Refund a download with "%s" tag', 'gamipress' );
                }

                $tag = get_term_by( 'term_id', $tag_id, 'download_tag' );

                // Return the custom title
                return sprintf( $pattern, $tag->name );
            }
            break;
        // Lifetime value
        case 'gamipress_edd_lifetime_value':
            // Lifetime value is equal, less or greater than a value
            return sprintf( __( 'lifetime value %s %s', 'gamipress' ), $lifetime_conditions[$lifetime_condition], $lifetime );
            break;

    }

    return $title;
}
add_filter( 'gamipress_activity_trigger_label', 'gamipress_edd_activity_trigger_label', 10, 3 );

/**
 * Register Easy Digital Downloads specific activity triggers labels
 *
 * @since  1.0.0
 *
 * @param  array $specific_activity_trigger_labels
 * @return array
 */
function gamipress_edd_specific_activity_trigger_label( $specific_activity_trigger_labels ) {

    // Purchase
    $specific_activity_trigger_labels['gamipress_edd_specific_download_purchase'] = __( 'Purchase %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_edd_download_variation_purchase'] = __( 'Purchase %s', 'gamipress' );
    // Refund
    $specific_activity_trigger_labels['gamipress_edd_specific_download_refund'] = __( 'Refund %s', 'gamipress' );
    $specific_activity_trigger_labels['gamipress_edd_download_variation_refund'] = __( 'Refund %s', 'gamipress' );

    // EDD Wish Lists
    if( class_exists('EDD_Wish_Lists') ) {
        $specific_activity_trigger_labels['gamipress_edd_add_specific_to_wish_list'] = __( 'Add %s to wish list', 'gamipress' );
    }

    // EDD Downloads Lists
    if( class_exists('EDD_Downloads_Lists') ) {
        $specific_activity_trigger_labels['gamipress_edd_wish_specific_download'] = __( 'Add %s to wish list', 'gamipress' );
        $specific_activity_trigger_labels['gamipress_edd_favorite_specific_download'] = __( 'Favorite %s', 'gamipress' );
        $specific_activity_trigger_labels['gamipress_edd_like_specific_download'] = __( 'Like %s', 'gamipress' );
        $specific_activity_trigger_labels['gamipress_edd_recommend_specific_download'] = __( 'Recommend %s', 'gamipress' );
    }

    // EDD Reviews
    if( class_exists('EDD_Reviews') ) {
        $specific_activity_trigger_labels['gamipress_edd_specific_new_review'] = __( 'Review %s', 'gamipress' );
        $specific_activity_trigger_labels['gamipress_edd_get_specific_review'] = __( 'Vendor gets a review on %s', 'gamipress' );
    }

    // EDD Social Discounts
    if( class_exists('EDD_Social_Discounts') ) {
        $specific_activity_trigger_labels['gamipress_edd_share_specific_download'] = __( 'Share %s', 'gamipress' );
    }

    return $specific_activity_trigger_labels;
}
add_filter( 'gamipress_specific_activity_trigger_label', 'gamipress_edd_specific_activity_trigger_label' );



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
function gamipress_edd_trigger_get_user_id( $user_id, $trigger, $args ) {

    switch ( $trigger ) {
        // Easy Digital Downloads
        case 'gamipress_publish_download': // Internal GamiPress listener
        // Purchase
        case 'gamipress_edd_new_purchase':
        case 'gamipress_edd_new_download_purchase':
        case 'gamipress_edd_specific_download_purchase':
        case 'gamipress_edd_new_free_download_purchase':
        case 'gamipress_edd_new_paid_download_purchase':
        case 'gamipress_edd_download_variation_purchase':
        case 'gamipress_edd_download_category_purchase':
        case 'gamipress_edd_download_tag_purchase':
        case 'gamipress_edd_new_sale':
        // Refund
        case 'gamipress_edd_purchase_refund':
        case 'gamipress_edd_download_refund':
        case 'gamipress_edd_specific_download_refund':
        case 'gamipress_edd_download_variation_refund':
        case 'gamipress_edd_download_category_refund':
        case 'gamipress_edd_download_tag_refund':
        case 'gamipress_edd_user_download_refund':
        // EDD FES
        case 'gamipress_edd_approve_download':
        // EDD Wish Lists
        case 'gamipress_publish_edd_wish_list': // Internal GamiPress listener
        case 'gamipress_edd_add_to_wish_list':
        case 'gamipress_edd_add_specific_to_wish_list':
        // EDD Downloads Lists
        case 'gamipress_edd_wish_download':
        case 'gamipress_edd_wish_specific_download':
        case 'gamipress_edd_favorite_download':
        case 'gamipress_edd_favorite_specific_download':
        case 'gamipress_edd_like_download':
        case 'gamipress_edd_like_specific_download':
        case 'gamipress_edd_recommend_download':
        case 'gamipress_edd_recommend_specific_download':
        // EDD Reviews
        case 'gamipress_edd_new_review':
        case 'gamipress_edd_specific_new_review':
        case 'gamipress_edd_get_review':
        case 'gamipress_edd_get_specific_review':
        // EDD Download Pages
        case 'gamipress_publish_edd_download_page': // Internal GamiPress listener
        case 'gamipress_edd_approve_edd_download_page':
        // EDD Social Discounts
        case 'gamipress_edd_download_social_share':
        case 'gamipress_edd_share_specific_download':
        // EDD Lifetime Value
        case 'gamipress_edd_lifetime_value':
            $user_id = $args[1];
            break;
    }

    return $user_id;

}
add_filter( 'gamipress_trigger_get_user_id', 'gamipress_edd_trigger_get_user_id', 10, 3 );

/**
 * Get the id for a given specific trigger action.
 *
 * @since  1.0.1
 *
 * integer  $specific_id Specific ID.
 * @param  string  $trigger Trigger name.
 * @param  array   $args    Passed trigger args.
 *
 * @return integer          Specific ID.
 */
function gamipress_edd_specific_trigger_get_id( $specific_id, $trigger = '', $args = array() ) {

    switch ( $trigger ) {
        // EDD Reviews
        case 'gamipress_edd_specific_new_review':
        case 'gamipress_edd_get_specific_review':
            $specific_id = $args[2];
            break;
        // Purchase
        case 'gamipress_edd_specific_download_purchase':
        case 'gamipress_edd_download_variation_purchase':
        // Refund
        case 'gamipress_edd_specific_download_refund':
        case 'gamipress_edd_download_variation_refund':
        // EDD Wish Lists
        case 'gamipress_edd_add_specific_to_wish_list':
        // EDD Downloads Lists
        case 'gamipress_edd_wish_specific_download':
        case 'gamipress_edd_favorite_specific_download':
        case 'gamipress_edd_like_specific_download':
        case 'gamipress_edd_recommend_specific_download':
        // EDD Social Discounts
        case 'gamipress_edd_share_specific_download':
            $specific_id = $args[0];
            break;
    }

    return $specific_id;
}
add_filter( 'gamipress_specific_trigger_get_id', 'gamipress_edd_specific_trigger_get_id', 10, 3 );

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
function gamipress_edd_log_event_trigger_meta_data( $log_meta, $user_id, $trigger, $site_id, $args ) {

    switch ( $trigger ) {
        // Easy Digital Downloads
        case 'gamipress_publish_download': // Internal GamiPress listener
        // EDD FES
        case 'gamipress_edd_approve_download':
        // EDD Social Discounts
        case 'gamipress_edd_download_social_share':
        case 'gamipress_edd_share_specific_download':
            // Add the download ID
            $log_meta['download_id'] = $args[0];
            $log_meta['post_id'] = $args[0]; // Post ID added to make column visible on logs list
            break;

        // Easy Digital Downloads
        case 'gamipress_edd_new_purchase':
        case 'gamipress_edd_purchase_refund':
            // Add the payment ID
            $log_meta['payment_id'] = $args[0];
            break;

        // Easy Digital Downloads
        // Purchase
        case 'gamipress_edd_new_download_purchase':
        case 'gamipress_edd_specific_download_purchase':
        case 'gamipress_edd_new_free_download_purchase':
        case 'gamipress_edd_new_paid_download_purchase':
            // EDD FES
        case 'gamipress_edd_new_sale':
        // Refund
        case 'gamipress_edd_download_refund':
        case 'gamipress_edd_specific_download_refund':
        case 'gamipress_edd_user_download_refund':
            // Add the download and payment IDs
            $log_meta['download_id'] = $args[0];
            $log_meta['post_id'] = $args[0]; // Post ID added to make column visible on logs list
            $log_meta['payment_id'] = $args[2];
            break;

        // Easy Digital Downloads
        case 'gamipress_edd_download_variation_purchase':
        case 'gamipress_edd_download_variation_refund':
            // Add the download, variation and payment IDs
            $log_meta['download_id'] = $args[0];
            $log_meta['post_id'] = $args[0]; // Post ID added to make column visible on logs list
            $log_meta['variation_id'] = $args[2];
            $log_meta['payment_id'] = $args[3];
            break;
        case 'gamipress_edd_download_category_purchase':
        case 'gamipress_edd_download_category_refund':
            // Add the download, category and order IDs
            $log_meta['download_id'] = $args[0];
            $log_meta['post_id'] = $args[0]; // Post ID added to make column visible on logs list
            $log_meta['category_id'] = $args[2];
            $log_meta['payment_id'] = $args[3];
            break;
        case 'gamipress_edd_download_tag_purchase':
        case 'gamipress_edd_download_tag_refund':
            // Add the download, tag and order IDs
            $log_meta['download_id'] = $args[0];
            $log_meta['post_id'] = $args[0]; // Post ID added to make column visible on logs list
            $log_meta['tag_id'] = $args[2];
            $log_meta['payment_id'] = $args[3];
            break;

        // EDD Wish Lists
        case 'gamipress_publish_edd_wish_list': // Internal GamiPress listener
            // Add the download ID
            $log_meta['list_id'] = $args[0];
            break;

        // EDD Download Pages
        case 'gamipress_publish_edd_download_page': // Internal GamiPress listener
        case 'gamipress_edd_approve_edd_download_page':
            // Add the download ID
            $log_meta['download_page_id'] = $args[0];
            break;

        // EDD Wish Lists
        case 'gamipress_edd_add_to_wish_list':
        case 'gamipress_edd_add_specific_to_wish_list':
        // EDD Downloads Lists
        case 'gamipress_edd_wish_download':
        case 'gamipress_edd_wish_specific_download':
        case 'gamipress_edd_favorite_download':
        case 'gamipress_edd_favorite_specific_download':
        case 'gamipress_edd_like_download':
        case 'gamipress_edd_like_specific_download':
        case 'gamipress_edd_recommend_download':
        case 'gamipress_edd_recommend_specific_download':
            // Add the download and list IDs
            $log_meta['download_id'] = $args[0];
            $log_meta['list_id'] = $args[2];
            break;

        // EDD Reviews
        case 'gamipress_edd_new_review':
        case 'gamipress_edd_specific_new_review':
            // Add the download and review IDs
            $log_meta['comment_id'] = $args[0];
            $log_meta['download_id'] = $args[2];
            break;
        case 'gamipress_edd_get_review':
        case 'gamipress_edd_get_specific_review':
            // Add the download, reviewer and review IDs
            $log_meta['comment_id'] = $args[0];
            $log_meta['download_id'] = $args[2];
            $log_meta['reviewer_id'] = $args[3];
            break;
        // EDD Lifetime Value
        case 'gamipress_edd_lifetime_value':
            $log_meta['lifetime_value'] = $args[0];
    }

    return $log_meta;
}
add_filter( 'gamipress_log_event_trigger_meta_data', 'gamipress_edd_log_event_trigger_meta_data', 10, 5 );

/**
 * Override the meta data to filter the logs count
 *
 * @since   1.1.4
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
function gamipress_edd_get_user_trigger_count_log_meta( $log_meta, $user_id, $trigger, $since, $site_id, $args ) {

    switch( $trigger ) {
        // Variation
        case 'gamipress_edd_download_variation_purchase':
        case 'gamipress_edd_download_variation_refund':
            if( isset( $args[0] ) && isset( $args[2] ) ) {
                // Add the download and variation IDs
                $log_meta['download_id'] = absint( $args[0] );
                $log_meta['variation_id'] = absint( $args[2] );
            }

            // $args could be a requirement object
            if( isset( $args['edd_variation_id'] ) && isset( $args['achievement_post'] ) ) {
                // Add the download and variation IDs
                $log_meta['download_id'] = absint( $args['achievement_post'] );
                $log_meta['variation_id'] = absint( $args['edd_variation_id'] );
            }
            break;
        // Category
        case 'gamipress_edd_download_category_purchase':
        case 'gamipress_edd_download_category_refund':
            if( isset( $args[2] ) ) {
                // Add the download category ID
                $log_meta['category_id'] = $args[2];
            }

            // $args could be a requirement object
            if( isset( $args['edd_category_id'] ) ) {
                // Add the download category ID
                $log_meta['category_id'] = $args['edd_category_id'];
            }
            break;
        // Tag
        case 'gamipress_edd_download_tag_purchase':
        case 'gamipress_edd_download_tag_refund':
            if( isset( $args[2] ) ) {
                // Add the download tag ID
                $log_meta['tag_id'] = $args[2];
            }

            // $args could be a requirement object
            if( isset( $args['edd_tag_id'] ) ) {
                // Add the download tag ID
                $log_meta['tag_id'] = $args['edd_tag_id'];
            }
            break;
    }

    return $log_meta;

}
add_filter( 'gamipress_get_user_trigger_count_log_meta', 'gamipress_edd_get_user_trigger_count_log_meta', 10, 6 );

/**
 * Extra data fields
 *
 * @since 1.1.2
 *
 * @param array     $fields
 * @param int       $log_id
 * @param string    $type
 *
 * @return array
 */
function gamipress_edd_log_extra_data_fields( $fields, $log_id, $type ) {

    $prefix = '_gamipress_';

    $log = ct_get_object( $log_id );
    $trigger = $log->trigger_type;

    if( $type !== 'event_trigger' ) {
        return $fields;
    }

    switch( $trigger ) {
        // Category
        case 'gamipress_edd_download_category_purchase':
        case 'gamipress_edd_download_category_refund':

            // Get categories stored and turn them into an array of options
            $categories = get_terms( array(
                'taxonomy' => 'download_category',
                'hide_empty' => false,
            ) );

            $options = array();

            foreach( $categories as $category ) {
                $options[$category->term_id] = $category->name;
            }

            $fields[] = array(
                'name' 	            => __( 'Categories', 'gamipress' ),
                'desc' 	            => __( 'Categories attached to this log.', 'gamipress' ),
                'id'   	            => $prefix . 'category_id',
                'type' 	            => 'select',
                'options'           => $options
            );
            break;
        // Tag
        case 'gamipress_edd_download_tag_purchase':
        case 'gamipress_edd_download_tag_refund':

            // Get tags stored and turn them into an array of options
            $tags = get_terms( array(
                'taxonomy' => 'download_tag',
                'hide_empty' => false,
            ) );

            $options = array();

            foreach( $tags as $tag ) {
                $options[$tag->term_id] = $tag->name;
            }

            $fields[] = array(
                'name' 	            => __( 'Tags', 'gamipress' ),
                'desc' 	            => __( 'Tags attached to this log.', 'gamipress' ),
                'id'   	            => $prefix . 'tag_id',
                'type' 	            => 'select',
                'options'           => $options
            );
            break;
    }

    return $fields;

}
add_filter( 'gamipress_log_extra_data_fields', 'gamipress_edd_log_extra_data_fields', 10, 3 );

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
function gamipress_edd_trigger_duplicity_check( $return, $user_id, $trigger, $site_id, $args  ) {

    // If user doesn't deserves trigger, then bail to prevent grant access
    if( ! $return )
        return $return;

    $log_meta = array(
        'type' => 'event_trigger',
        'trigger_type' => $trigger,
    );

    switch ( $trigger ) {
        // Easy Digital Downloads
        case 'gamipress_publish_download': // Internal GamiPress listener
            // User can not create same download more times, so check it
            $log_meta['post_id'] = gamipress_get_event_arg( $args, 'post_id', 0 );
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        case 'gamipress_edd_new_purchase':
        case 'gamipress_edd_purchase_refund':
            // User can not place or refund same payment ID more times, so check it
            $log_meta['payment_id'] = $args[0];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        // Easy Digital Downloads
        // Purchase
        case 'gamipress_edd_new_download_purchase':
        case 'gamipress_edd_specific_download_purchase':
        case 'gamipress_edd_new_free_download_purchase':
        case 'gamipress_edd_new_paid_download_purchase':
        // EDD FES
        case 'gamipress_edd_new_sale':
        // Refund
        case 'gamipress_edd_download_refund':
        case 'gamipress_edd_specific_download_refund':
        case 'gamipress_edd_user_download_refund':

            $quantity = isset( $args[3] ) ? $args[3] : 1;

            // Don't perform duplicity check if quantity is higher than 1
            if( $quantity > 1 ) {
                break;
            }

            // User can not place same download and payment IDs more times, so check it
            $log_meta['download_id'] = $args[0];
            $log_meta['payment_id'] = $args[2];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        // Easy Digital Downloads
        // Variation
        case 'gamipress_edd_download_variation_purchase':
        case 'gamipress_edd_download_variation_refund':

            $quantity = isset( $args[4] ) ? $args[4] : 1;

            // Don't perform duplicity check if quantity is higher than 1
            if( $quantity > 1 ) {
                break;
            }

            // User can not place same download and payment IDs more times, so check it
            $log_meta['download_id'] = $args[0];
            $log_meta['payment_id'] = $args[3];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        // Category
        case 'gamipress_edd_download_category_purchase':
        case 'gamipress_edd_download_category_refund':

            $quantity = isset( $args[4] ) ? $args[4] : 1;

            // Don't perform duplicity check if quantity is higher than 1
            if( $quantity > 1 ) {
                break;
            }

            // User can not place same download, category and payment IDs more times, so check it
            $log_meta['download_id'] = $args[0];
            $log_meta['category_id'] = $args[2];
            $log_meta['payment_id'] = $args[3];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        // Tag
        case 'gamipress_edd_download_tag_purchase':
        case 'gamipress_edd_download_tag_refund':

            $quantity = isset( $args[4] ) ? $args[4] : 1;

            // Don't perform duplicity check if quantity is higher than 1
            if( $quantity > 1 ) {
                break;
            }

            // User can not place same download, tag and payment IDs more times, so check it
            $log_meta['download_id'] = $args[0];
            $log_meta['tag_id'] = $args[2];
            $log_meta['payment_id'] = $args[3];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        // EDD Wish Lists
        case 'gamipress_publish_edd_wish_list': // Internal GamiPress listener
            // User can not create same list more times, so check it
            $log_meta['list_id'] = $args[0];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        // EDD Download Pages
        case 'gamipress_publish_edd_download_page': // Internal GamiPress listener
            // User can not create same download page more times, so check it
            $log_meta['download_page_id'] = $args[0];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
        // EDD Reviews
        case 'gamipress_edd_new_review':
        case 'gamipress_edd_specific_new_review':
        case 'gamipress_edd_get_review':
        case 'gamipress_edd_get_specific_review':
            // User can not create same comment more times, so check it
            $log_meta['comment_id'] = $args[0];
            $return = (bool) ( gamipress_get_user_last_log( $user_id, $log_meta ) === false );
            break;
    }

    return $return;

}
add_filter( 'gamipress_user_deserves_trigger', 'gamipress_edd_trigger_duplicity_check', 10, 5 );