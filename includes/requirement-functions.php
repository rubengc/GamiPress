<?php
/**
 * Requirement Functions
 *
 * @package     GamiPress\Requirement_Functions
 * @since       1.0.5
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

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
 * @since  1.0.5
 *
 * @param int $requirement_id
 *
 * @return array
 */
function gamipress_get_requirement_object( $requirement_id = 0 ) {

    $requirement_type = get_post_type( $requirement_id );

    // Setup our default requirements array, assume we require nothing
    $requirement = array(
        'count'            => absint( get_post_meta( $requirement_id, '_gamipress_count', true ) ),
        'limit'            => absint( get_post_meta( $requirement_id, '_gamipress_limit', true ) ),
        'limit_type'       => get_post_meta( $requirement_id, '_gamipress_limit_type', true ),
        'trigger_type'     => get_post_meta( $requirement_id, '_gamipress_trigger_type', true ),
        'achievement_type' => get_post_meta( $requirement_id, '_gamipress_achievement_type', true ),
        'achievement_post' => ''
    );

    // Specific points award data
    if( $requirement_type === 'points-award' ) {
        $requirement['points']      = absint( get_post_meta( $requirement_id, '_gamipress_points', true ) );
        $requirement['points_type'] = absint( get_post_meta( $requirement_id, '_gamipress_points_type', true ) );
    }

    // If the requirement requires a specific achievement
    if ( ! empty( $requirement['achievement_type'] ) ) {
        $connected_activities = @get_posts( array(
            'post_type'        => $requirement['achievement_type'],
            'posts_per_page'   => 1,
            'suppress_filters' => false,
            'connected_type'   => $requirement['achievement_type'] . '-to-' . $requirement_type,
            'connected_to'     => $requirement_id
        ));

        if ( ! empty( $connected_activities ) ) {
            $requirements['achievement_post'] = $connected_activities[0]->ID;
        }
    } else if ( in_array( $requirement['trigger_type'], array_keys( gamipress_get_specific_activity_triggers() ) ) ) {
        $achievement_post = absint( get_post_meta( $requirement_id, '_gamipress_achievement_post', true ) );

        if ( $achievement_post > 0  ) {
            $requirement['achievement_post'] = $achievement_post;
        }
    }

    // Available filter for overriding elsewhere
    return apply_filters( 'gamipress_requirement_object', $requirement, $requirement_id );
}

/**
 * Get the the ID of a P2P connection (p2p_id) to a given requirement ID (p2p_from)
 *
 * @since  1.0.6
 *
 * @param  integer $requirement_id The given post ID
 *
 * @return integer           The resulting connection
 */
function gamipress_get_requirement_connection_id( $requirement_id = 0 ) {

    global $wpdb;

    $p2p_id = $wpdb->get_var( $wpdb->prepare( "SELECT p2p_id FROM $wpdb->p2p WHERE p2p_from = %d ", $requirement_id ) );

    return $p2p_id;

}

/**
 * Get the the ID of a P2P connected object (p2p_to) to a given requirement ID (p2p_from)
 *
 * @since  1.0.6
 *
 * @param  integer $requirement_id The given post ID
 *
 * @return integer           The resulting connected post ID
 */
function gamipress_get_requirement_connected_id( $requirement_id = 0 ) {

    global $wpdb;

    $p2p_to = $wpdb->get_var( $wpdb->prepare( "SELECT p2p_to FROM $wpdb->p2p WHERE p2p_from = %d ", $requirement_id ) );

    return $p2p_to;

}

/**
 * Get requirements that by some way have been unassigned from their achievement (normally it happens if achievement has been removed but not the requirements)
 *
 * @since  1.0.6
 *
 * @return array|bool                  Array of the requirements, or false if none
 */
function gamipress_get_unassigned_requirements() {

    global $wpdb;

    $requirements = $wpdb->get_results( "
        SELECT p.ID
        FROM {$wpdb->posts} AS p
        LEFT JOIN {$wpdb->p2p} AS p2p
        ON p2p.p2p_from = p.ID
        WHERE p.post_type IN( 'points-award', 'step' )
        AND p2p.p2p_from IS NULL
    ", ARRAY_A );

    // If it has results, return them, otherwise return false
    if ( ! empty( $requirements ) )
        return $requirements;
    else
        return false;

}