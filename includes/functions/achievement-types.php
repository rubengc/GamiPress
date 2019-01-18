<?php
/**
 * Achievement Types Functions
 *
 * @package     GamiPress\Achievement_Types_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.4.6
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get registered achievement types
 *
 * Returns a multidimensional array of slug, single name and plural name for all achievement types.
 *
 * @since  1.0.0
 *
 * @return array An array of our registered achievement types
 */
function gamipress_get_achievement_types() {

    return GamiPress()->achievement_types;

}

/**
 * Get registered achievement type slugs
 *
 * @since  1.0.0
 *
 * @return array An array of all our registered achievement type slugs (empty array if none)
 */
function gamipress_get_achievement_types_slugs() {

    // Assume we have no registered achievement types
    $achievement_type_slugs = array();

    // If we do have any achievement types, loop through each and add their slug to our array
    foreach ( GamiPress()->achievement_types as $slug => $data ) {
        $achievement_type_slugs[] = $slug;
    }

    // Finally, return our data
    return $achievement_type_slugs;

}

/**
 * Get the desired achievement type
 *
 * @since  1.4.6
 *
 * @param string|int|WP_Post    $achievement_type
 *
 * @return array|false                              The achievement type object if is registered, if not return false
 */
function gamipress_get_achievement_type( $achievement_type ) {

    // Check if is an WP_Post
    if( $achievement_type instanceof WP_Post ) {

        if( $achievement_type->post_type === 'achievement-type' ) {
            // If WP_Post given is an achievement type post, try to find it by ID
            $achievement_type = $achievement_type->ID;
        } else {
            // Else the WP_Post given is an achievement post, so the achievement type is on post_type field
            $achievement_type = $achievement_type->post_type;
        }

    }

    if( gettype( $achievement_type ) === 'string' && isset( GamiPress()->achievement_types[$achievement_type] ) ) {
        return GamiPress()->achievement_types[$achievement_type];
    }

    if( is_numeric( $achievement_type ) ) {
        return gamipress_get_achievement_type_by_id( $achievement_type );
    }

    // Achievement type can not be found
    return false;

}

/**
 * Get the desired achievement type by ID
 *
 * @since  1.4.6
 *
 * @param int $achievement_type_id
 *
 * @return array|false              The achievement type object if is registered, if not return false
 */
function gamipress_get_achievement_type_by_id( $achievement_type_id ) {

    $achievement_type_id = absint( $achievement_type_id );

    // Bail if wrong ID given
    if( $achievement_type_id === 0 ) {
        return false;
    }

    // Loop all registered achievement types to find what matches the given ID
    foreach ( GamiPress()->achievement_types as $slug => $data ) {
        if( absint( $data['ID'] ) === $achievement_type_id ) {
            return $data;
        }
    }

    // Achievement type can not be found
    return false;

}

/**
 * Get the desired achievement type ID
 *
 * @since  1.4.6
 *
 * @param string|int|WP_Post    $achievement_type
 *
 * @return array|false                              The achievement type ID if is registered, if not return false
 */
function gamipress_get_achievement_type_id( $achievement_type ) {

    $achievement_type = gamipress_get_achievement_type( $achievement_type );

    if( $achievement_type ) {
        return $achievement_type['ID'];
    }

    return false;

}

/**
 * Get the desired achievement type singular name
 *
 * @since  1.4.6
 *
 * @param string|int|WP_Post    $achievement_type
 * @param bool                  $force_return       If set to true, will return "Achievement" if achievement type is not registered or singular name is empty
 *
 * @return array|false                              The achievement type singular name if is registered, if not return false
 */
function gamipress_get_achievement_type_singular( $achievement_type, $force_return = false ) {

    $achievement_type = gamipress_get_achievement_type( $achievement_type );

    if( $achievement_type ) {

        // If force return and achievement type singular name is empty, return "Achievement" as singular name
        if( $force_return && empty( $achievement_type['singular_name'] ) ) {
            return __( 'Achievement', 'gamipress' );
        }

        // Return the achievement type singular name
        return $achievement_type['singular_name'];
    }

    return $force_return ? __( 'Achievement', 'gamipress' ) : false;

}

/**
 * Get the desired achievement type plural name
 *
 * @since  1.4.6
 *
 * @param string|int|WP_Post    $achievement_type
 * @param bool                  $force_return       If set to true, will return "Achievements" if achievement type is not registered or plural name is empty
 *
 * @return array|false                              The achievement type plural name if is registered, if not return false
 */
function gamipress_get_achievement_type_plural( $achievement_type, $force_return = false ) {

    $achievement_type = gamipress_get_achievement_type( $achievement_type );

    if( $achievement_type ) {

        // If force return and achievement type plural name is empty, return "Achievements" as plural name
        if( $force_return && empty( $achievement_type['plural_name'] ) ) {
            return __( 'Achievements', 'gamipress' );
        }

        // Return the achievement type singular name
        return $achievement_type['plural_name'];
    }

    return $force_return ? __( 'Achievements', 'gamipress' ) : false;

}