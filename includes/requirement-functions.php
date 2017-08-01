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
    } elseif ( in_array( $requirement['trigger_type'], array_keys( gamipress_get_specific_activity_triggers() ) ) ) {
        $achievement_post = absint( get_post_meta( $requirement_id, '_gamipress_achievement_post', true ) );

        if ( 0 < $achievement_post ) {
            $requirements[ 'achievement_post' ] = $achievement_post;
        }
    }

    // Available filter for overriding elsewhere
    return apply_filters( 'gamipress_requirement_object', $requirement, $requirement_id );
}