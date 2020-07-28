<?php
/**
 * Requirement Functions
 *
 * @package     GamiPress\Requirement_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.5
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Check if post is a registered GamiPress requirement.
 *
 * @since  1.3.0
 *
 * @param  object|int $post Post object or ID.
 * @return bool             True if post is an requirement, otherwise false.
 */
function gamipress_is_requirement( $post = null ) {

    // Assume we are working with a requirement object
    $return = true;

    // If post type is NOT a registered requirement type, it cannot be a requirement
    if ( ! in_array( gamipress_get_post_type( $post ), gamipress_get_requirement_types_slugs() ) ) {
        $return = false;
    }

    // If we pass both previous tests, this is a valid requirement (with filter to override)
    return apply_filters( 'gamipress_is_requirement', $return, $post );
}


/**
 * Get GamiPress Requirement Types
 *
 * Returns a multidimensional array of slug, single name and plural name for all requirement types.
 *
 * @since  1.0.5
 *
 * @return array An array of our registered requirement types
 */
function gamipress_get_requirement_types() {

    return GamiPress()->requirement_types;

}

/**
 * Get GamiPress Requirement Type Slugs
 *
 * @since  1.0.5
 *
 * @return array An array of all our registered requirement type slugs (empty array if none)
 */
function gamipress_get_requirement_types_slugs() {

    // Assume we have no registered requirement types
    $requirement_type_slugs = array();

    // If we do have any requirement types, loop through each and add their slug to our array
    foreach ( GamiPress()->requirement_types as $slug => $data ) {
        $requirement_type_slugs[] = $slug;
    }

    // Finally, return our data
    return $requirement_type_slugs;

}

/**
 * Build a requirement object
 *
 * @since   1.0.5
 * @updated 1.5.1 Added the order key (based on menu_order)
 *
 * @param int $requirement_id
 *
 * @return array
 */
function gamipress_get_requirement_object( $requirement_id = 0 ) {

    $requirement_type = gamipress_get_post_type( $requirement_id );

    // Setup our default requirements array, assume we require nothing
    $requirement = array(
        'ID'               => $requirement_id,
        'title'            => gamipress_get_post_field( 'post_title', $requirement_id ),
        'order'            => gamipress_get_post_field( 'menu_order', $requirement_id ),
        'count'            => absint( gamipress_get_post_meta( $requirement_id, '_gamipress_count' ) ),
        'limit'            => absint( gamipress_get_post_meta( $requirement_id, '_gamipress_limit' ) ),
        'limit_type'       => gamipress_get_post_meta( $requirement_id, '_gamipress_limit_type' ),
        'trigger_type'     => gamipress_get_post_meta( $requirement_id, '_gamipress_trigger_type' ),
        // Points vars
        'points_required'       => absint( gamipress_get_post_meta( $requirement_id, '_gamipress_points_required' ) ),
        'points_type_required'  => gamipress_get_post_meta( $requirement_id, '_gamipress_points_type_required' ),
        // Rank vars
        'rank_required'         => absint( gamipress_get_post_meta( $requirement_id, '_gamipress_rank_required' ) ),
        'rank_type_required'    => gamipress_get_post_meta( $requirement_id, '_gamipress_rank_type_required' ),
        // User role vars
        'user_role_required'    => gamipress_get_post_meta( $requirement_id, '_gamipress_user_role_required' ),
        // Achievement vars
        'achievement_type'          => gamipress_get_post_meta( $requirement_id, '_gamipress_achievement_type' ),
        'achievement_post'          => absint( gamipress_get_post_meta( $requirement_id, '_gamipress_achievement_post' ) ),
        'achievement_post_site_id'  => get_current_blog_id(),
    );

    // Specific points award/deduct data
    if( $requirement_type === 'points-award' || $requirement_type === 'points-deduct' ) {
        $requirement['points']              = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_points' ) );
        $requirement['points_type']         = gamipress_get_post_meta( $requirement_id, '_gamipress_points_type' );
        $requirement['maximum_earnings']    = gamipress_get_post_meta( $requirement_id, '_gamipress_maximum_earnings' );

        if( $requirement['maximum_earnings'] === '' ) {
            $requirement['maximum_earnings'] = 0;
        } else {
            $requirement['maximum_earnings'] = absint( $requirement['maximum_earnings'] );
        }
    }

    // Check the achievement post site ID
    if ( in_array( $requirement['trigger_type'], array_keys( gamipress_get_specific_activity_triggers() ) ) ) {

        if ( $requirement['achievement_post'] > 0  ) {

            $achievement_post_site_id = gamipress_get_post_meta( $requirement_id, '_gamipress_achievement_post_site_id' );

            if( ! empty( $achievement_post_site_id ) ) {
                $requirement['achievement_post_site_id'] = absint( $achievement_post_site_id );
            }
        }

    }

    // Available filter for overriding elsewhere
    return apply_filters( 'gamipress_requirement_object', $requirement, $requirement_id );

}

/**
 * Helper function to query requirements
 *
 * @since 1.8.8
 *
 * @param array $query_args
 *
 * @return array|object|null
 */
function gamipress_query_requirements( $query_args = array() ) {

    $query_args = wp_parse_args( $query_args, array(
        'fields'        => 'all',
        'post_status'   => 'publish',
        'meta_query'    => array(),
    ) );

    global $wpdb;

    $posts      = GamiPress()->db->posts;
    $postmeta   = GamiPress()->db->postmeta;

    // SELECT
    $select = 'SELECT *';

    // Setup fields to return
    if( $query_args['fields'] === 'ids' ) {
        $select = 'SELECT p.ID';
    }

    // FROM
    $from = array( "FROM {$posts} AS p" );

    // WHERE
    $where = array( '1=1' );

    // Post type
    $requirement_types = gamipress_get_requirement_types_slugs();
    $where[] = "p.post_type IN ( '" . implode( "', '", $requirement_types ) . "' )";

    // Post status
    $where[] = "p.post_status = '" . $query_args['post_status'] . "'";

    // Meta query
    if( is_array( $query_args['meta_query'] ) && ! empty( $query_args['meta_query'] ) ) {

        $i = 1;

        foreach ( $query_args['meta_query'] as $meta_key => $meta_value ) {

            $meta_key = sanitize_text_field( $meta_key );

            $from[] = "LEFT JOIN {$postmeta} AS pm{$i} ON ( p.ID = pm{$i}.post_id AND pm{$i}.meta_key = '{$meta_key}' )";

            // Check for array meta values
            if( is_array( $meta_value ) ) {

                foreach( $meta_value as $k => $value ) {
                    $meta_value[$k] = sanitize_text_field( $value );
                }

                $where[] = "pm{$i}.meta_value IN ( '" . implode( "', '", $meta_value ) . "' )";

            } else {
                $meta_value = sanitize_text_field( $meta_value );

                $where[] = "pm{$i}.meta_value = '{$meta_value}'";
            }



            $i++;

        }

    }

    // ORDER BY
    $order_by = array( "p.menu_order ASC" );

    // Setup vars
    $from = implode( ' ', $from );
    $where = "WHERE " . implode( ' AND ', $where );
    $order_by = "ORDER BY " . implode( ', ', $order_by );

    // Get all requirements with this trigger and post assigned
    $results = $wpdb->get_results( "{$select} {$from} {$where} {$order_by}" );

    return $results;

}