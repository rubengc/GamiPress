<?php
/**
 * Functions
 *
 * @package GamiPress\WP_Job_Manager\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Retrieves post term ids for a taxonomy.
 *
 * Taken from WooCommerce wc_get_product_term_ids() function
 *
 * @since  1.0.0
 *
 * @param  int    $post_id  Post ID.
 * @param  string $taxonomy Taxonomy slug.
 *
 * @return array
 */
function gamipress_wp_job_manager_get_post_term_ids( $post_id, $taxonomy ) {
    $terms = get_the_terms( $post_id, $taxonomy );
    return ( empty( $terms ) || is_wp_error( $terms ) ) ? array() : wp_list_pluck( $terms, 'term_id' );
}