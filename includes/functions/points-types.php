<?php
/**
 * Points Types Functions
 *
 * @package     GamiPress\Points_Types_Functions
 * @since       1.4.6
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get registered points types
 *
 * Returns a multidimensional array of slug, single name and plural name for all points types.
 *
 * @since  1.0.0
 *
 * @return array An array of our registered points types
 */
function gamipress_get_points_types() {

    return GamiPress()->points_types;

}

/**
 * Get registered points type slugs
 *
 * @since  1.0.0
 *
 * @return array An array of all our registered points type slugs (empty array if none)
 */
function gamipress_get_points_types_slugs() {

    // Assume we have no registered points types
    $points_type_slugs = array();

    // If we do have any points types, loop through each and add their slug to our array
    foreach ( GamiPress()->points_types as $slug => $data ) {
        $points_type_slugs[] = $slug;
    }

    // Finally, return our data
    return $points_type_slugs;

}

/**
 * Get the desired points type
 *
 * @since  1.4.6
 *
 * @param string|int|WP_Post    $points_type
 *
 * @return array|false                              The points type object if is registered, if not return false
 */
function gamipress_get_points_type( $points_type ) {

    // Check if is an WP_Post
    if( $points_type instanceof WP_Post ) {

        if( $points_type->post_type === 'points-type' ) {
            // If WP_Post given is an points type post, try to find it by ID
            $points_type = $points_type->ID;
        } else {
            // Else the WP_Post given is an points post, so the points type is on post_type field
            $points_type = $points_type->post_type;
        }

    }

    if( gettype( $points_type ) === 'string' && isset( GamiPress()->points_types[$points_type] ) ) {
        return GamiPress()->points_types[$points_type];
    }

    if( is_numeric( $points_type ) ) {
        return gamipress_get_points_type_by_id( $points_type );
    }

    // Point type can not be found
    return false;

}

/**
 * Get the desired points type by ID
 *
 * @since  1.4.6
 *
 * @param int $points_type_id
 *
 * @return array|false              The points type object if is registered, if not return false
 */
function gamipress_get_points_type_by_id( $points_type_id ) {

    $points_type_id = absint( $points_type_id );

    // Bail if wrong ID given
    if( $points_type_id === 0 ) {
        return false;
    }

    // Loop all registered points types to find what matches the given ID
    foreach ( GamiPress()->points_types as $slug => $data ) {
        if( absint( $data['ID'] ) === $points_type_id ) {
            return $data;
        }
    }

    // Point type can not be found
    return false;

}

/**
 * Get the desired points type ID
 *
 * @since  1.4.6
 *
 * @param string|int|WP_Post    $points_type
 *
 * @return array|false                              The points type ID if is registered, if not return false
 */
function gamipress_get_points_type_id( $points_type ) {

    $points_type = gamipress_get_points_type( $points_type );

    if( $points_type ) {
        return $points_type['ID'];
    }

    return false;

}

/**
 * Get the desired points type singular name
 *
 * @since  1.4.6
 *
 * @param string|int|WP_Post    $points_type
 * @param bool                  $force_return       If set to true, will return "Point" if points type is not registered or singular name is empty
 *
 * @return array|false                              The points type singular name if is registered, if not return false
 */
function gamipress_get_points_type_singular( $points_type, $force_return = false ) {

    $points_type = gamipress_get_points_type( $points_type );

    if( $points_type ) {

        // If force return and points type singular name is empty, return "Point" as singular name
        if( $force_return && empty( $points_type['singular_name'] ) ) {
            return __( 'Point', 'gamipress' );
        }

        // Return the points type singular name
        return $points_type['singular_name'];
    }

    return $force_return ? __( 'Point', 'gamipress' ) : false;

}

/**
 * Get the desired points type plural name
 *
 * @since  1.4.6
 *
 * @param string|int|WP_Post    $points_type
 * @param bool                  $force_return       If set to true, will return "Points" if points type is not registered or plural name is empty
 *
 * @return array|false                              The points type plural name if is registered, if not return false
 */
function gamipress_get_points_type_plural( $points_type, $force_return = false ) {

    $points_type = gamipress_get_points_type( $points_type );

    if( $points_type ) {

        // If force return and points type plural name is empty, return "Points" as plural name
        if( $force_return && empty( $points_type['plural_name'] ) ) {
            return __( 'Points', 'gamipress' );
        }

        // Return the points type singular name
        return $points_type['plural_name'];
    }

    return $force_return ? __( 'Points', 'gamipress' ) : false;

}