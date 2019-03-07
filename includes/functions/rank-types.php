<?php
/**
 * Rank Types Functions
 *
 * @package     GamiPress\Rank_Types_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.4.6
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get registered rank types
 *
 * Returns a multidimensional array of slug, single name and plural name for all rank types.
 *
 * @since  1.3.1
 *
 * @return array An array of our registered rank types
 */
function gamipress_get_rank_types() {

    return GamiPress()->rank_types;

}

/**
 * Get registered rank type slugs
 *
 * @since  1.3.1
 *
 * @return array An array of all our registered rank type slugs (empty array if none)
 */
function gamipress_get_rank_types_slugs() {

    // Assume we have no registered rank types
    $rank_type_slugs = array();

    // If we do have any rank types, loop through each and add their slug to our array
    foreach ( GamiPress()->rank_types as $slug => $data ) {
        $rank_type_slugs[] = $slug;
    }

    // Finally, return our data
    return $rank_type_slugs;

}

/**
 * Get the desired rank type
 *
 * @since  1.4.6
 *
 * @param string|int|WP_Post    $rank_type
 *
 * @return array|false                              The rank type object if is registered, if not return false
 */
function gamipress_get_rank_type( $rank_type ) {

    // Check if is an WP_Post
    if( $rank_type instanceof WP_Post ) {

        if( $rank_type->post_type === 'rank-type' ) {
            // If WP_Post given is an rank type post, try to find it by ID
            $rank_type = $rank_type->ID;
        } else {
            // Else the WP_Post given is an rank post, so the rank type is on post_type field
            $rank_type = $rank_type->post_type;
        }

    }

    if( gettype( $rank_type ) === 'string' && isset( GamiPress()->rank_types[$rank_type] ) ) {
        return GamiPress()->rank_types[$rank_type];
    }

    if( is_numeric( $rank_type ) ) {
        return gamipress_get_rank_type_by_id( $rank_type );
    }

    // Rank type can not be found
    return false;

}

/**
 * Get the desired rank type by ID
 *
 * @since  1.4.6
 *
 * @param int $rank_type_id
 *
 * @return array|false              The rank type object if is registered, if not return false
 */
function gamipress_get_rank_type_by_id( $rank_type_id ) {

    $rank_type_id = absint( $rank_type_id );

    // Bail if wrong ID given
    if( $rank_type_id === 0 ) {
        return false;
    }

    // Loop all registered rank types to find what matches the given ID
    foreach ( GamiPress()->rank_types as $slug => $data ) {
        if( absint( $data['ID'] ) === $rank_type_id ) {
            return $data;
        }
    }

    // Rank type can not be found
    return false;

}

/**
 * Get the desired rank type ID
 *
 * @since  1.4.6
 *
 * @param string|int|WP_Post    $rank_type
 *
 * @return int|false                                The rank type ID if is registered, if not return false
 */
function gamipress_get_rank_type_id( $rank_type ) {

    $rank_type = gamipress_get_rank_type( $rank_type );

    if( $rank_type ) {
        return $rank_type['ID'];
    }

    return false;

}

/**
 * Get the desired rank type singular name
 *
 * @since  1.3.1
 * @updated 1.4.6 Full rewrite of the function and added the parameter $force return
 *
 * @param string|int|WP_Post    $rank_type
 * @param bool                  $force_return       If set to true, will return "Rank" if rank type is not registered or singular name is empty
 *
 * @return array|false                              The rank type singular name if is registered, if not return false
 */
function gamipress_get_rank_type_singular( $rank_type, $force_return = false ) {

    $rank_type = gamipress_get_rank_type( $rank_type );

    if( $rank_type ) {

        // If force return and rank type singular name is empty, return "Rank" as singular name
        if( $force_return && empty( $rank_type['singular_name'] ) ) {
            return __( 'Rank', 'gamipress' );
        }

        // Return the rank type singular name
        return $rank_type['singular_name'];
    }

    return $force_return ? __( 'Rank', 'gamipress' ) : false;

}

/**
 * Get the desired rank type plural name
 *
 * @since   1.3.1
 * @updated 1.4.6 Full rewrite of the function and added the parameter $force return
 *
 * @param string|int|WP_Post    $rank_type
 * @param bool                  $force_return       If set to true, will return "Ranks" if rank type is not registered or plural name is empty
 *
 * @return array|false                              The rank type plural name if is registered, if not return false
 */
function gamipress_get_rank_type_plural( $rank_type, $force_return = false ) {

    $rank_type = gamipress_get_rank_type( $rank_type );

    if( $rank_type ) {

        // If force return and rank type plural name is empty, return "Ranks" as plural name
        if( $force_return && empty( $rank_type['plural_name'] ) ) {
            return __( 'Ranks', 'gamipress' );
        }

        // Return the rank type singular name
        return $rank_type['plural_name'];
    }

    return $force_return ? __( 'Ranks', 'gamipress' ) : false;

}