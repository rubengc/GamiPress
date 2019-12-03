<?php
/**
 * Points Types Functions
 *
 * @package     GamiPress\Points_Types_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
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
 * @param string|int|WP_Post    $points_type    The points type
 *
 * @return array|false                          The points type object if is registered, if not return false
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
 * @param int $points_type_id       The points type ID
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
 * @param string|int|WP_Post    $points_type    The points type
 *
 * @return int|false                            The points type ID if is registered, if not return false
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
 * @param string|int|WP_Post    $points_type    The points type
 * @param bool                  $force_return   If set to true, will return "Point" if points type is not registered or singular name is empty
 *
 * @return array|false                          The points type singular name if is registered, if not return false
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
 * @param string|int|WP_Post    $points_type    The points type
 * @param bool                  $force_return   If set to true, will return "Points" if points type is not registered or plural name is empty
 *
 * @return array|false                          The points type plural name if is registered, if not return false
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

/**
 * Return the singular or plural form based on the supplied amount
 *
 * @since  1.5.1
 *
 * @param int                   $amount         The desired amount
 * @param string|int|WP_Post    $points_type    The points type
 * @param bool                  $force_return   If set to true, will return "Point" or "Points" if points type is not registered or plural name is empty
 *
 * @return string                               The points type singular or plural based on the supplied amount
 */
function gamipress_get_points_amount_label( $amount, $points_type, $force_return = false ) {

    // Get the singular or plural label based on points amount
    $label = _n( gamipress_get_points_type_singular( $points_type, $force_return ), gamipress_get_points_type_plural( $points_type, $force_return ), $amount, 'gamipress' );

    /**
     * Points type label position (default after)
     *
     * @since  1.5.1
     *
     * @param string                $label          The points type singular or plural label
     * @param int                   $amount         The desired amount
     * @param string|int|WP_Post    $points_type    The points type
     * @param bool                  $force_return   If set to true, will return "Point" or "Points" if points type is not registered or plural name is empty
     */
    return apply_filters( 'gamipress_get_points_amount_label', $label, $amount, $points_type, $force_return );

}

/**
 * Get the desired points type label position
 *
 * @since  1.5.1
 *
 * @param string|int|WP_Post    $points_type
 *
 * @return string                              The points type plural name if is registered, if not return false
 */
function gamipress_get_points_type_label_position( $points_type ) {

    $label_position = 'after';

    $points_type_id = gamipress_get_points_type_id( $points_type );

    if( $points_type_id ) {

        // Get the points type label position (after or before)
        $label_position = gamipress_get_post_meta( $points_type_id, '_gamipress_label_position' );
    }

    /**
     * Points type label position (default after)
     *
     * @since  1.5.1
     *
     * @param string                $label_position The points type label position (after or before)
     * @param string|int|WP_Post    $points_type    The points type given
     */
    return apply_filters( 'gamipress_get_points_type_label_position', $label_position, $points_type );

}

/**
 * Get the desired points type thousands separator
 *
 * @since  1.5.1
 *
 * @param string|int|WP_Post    $points_type
 *
 * @return string                              The points type plural name if is registered, if not return false
 */
function gamipress_get_points_type_thousands_separator( $points_type ) {

    $thousands_separator = '';

    $points_type_id = gamipress_get_points_type_id( $points_type );

    if( $points_type_id ) {

        // Get the points type thousands separator
        $thousands_separator = gamipress_get_post_meta( $points_type_id, '_gamipress_thousands_separator' );
    }

    /**
     * Points type thousands separator
     *
     * @since  1.5.1
     *
     * @param string                $thousands_separator    The points type thousands separator
     * @param string|int|WP_Post    $points_type            The points type given
     */
    return apply_filters( 'gamipress_get_points_type_thousands_separator', $thousands_separator, $points_type );

}


/**
 * Format an amount based on a points type to append the points type label
 *
 * @since  1.5.1
 *
 * @param int                   $amount         The amount to be formatted
 * @param string|int|WP_Post    $points_type    The points type
 *
 * @return string                               The amount of points formatted using the points type plural or singular
 */
function gamipress_format_points( $amount, $points_type ) {

    $amount = floatval( $amount );

    // Get the singular or plural label based on points amount
    $label = gamipress_get_points_amount_label( $amount, $points_type, true );

    $formatted_amount = gamipress_format_amount( $amount, $points_type );

    // Apply points type settings of label position (after or before)
    $label_position = gamipress_get_points_type_label_position( $points_type );

    if( $label_position === 'before' ) {
        $formatted_amount = $label . ' ' . $formatted_amount;
    } else {
        // Make default after the default label position
        $formatted_amount .= ' ' . $label;
    }

    /**
     * Format points filter
     *
     * @since  1.5.1
     *
     * @param string                $formatted_amount   The formatted amount (with label)
     * @param int                   $amount             The original amount (without any format)
     * @param string|int|WP_Post    $points_type        The points type given
     * @param string                $label_position     The points type label position (after or before)
     */
    return apply_filters( 'gamipress_format_points', $formatted_amount, $amount, $points_type, $label_position );

}

/**
 * Format an amount based on settings
 *
 * @since  1.5.1
 *
 * @param int       $amount The amount to be formatted
 * @param string|int|WP_Post    $points_type    The points type
 *
 * @return string           The amount formatted
 */
function gamipress_format_amount( $amount, $points_type ) {

    $amount = floatval( $amount );

    // Setup the formatting vars
    $decimals = 0;
    $decimals_sep = '';
    $thousands_sep = gamipress_get_points_type_thousands_separator( $points_type );

    // Format the amount
    $formatted_amount = number_format( $amount, $decimals, $decimals_sep, $thousands_sep );

    /**
     * Format amount filter
     *
     * @since  1.5.1
     *
     * @param string                $formatted_amount   The formatted amount
     * @param int                   $amount             The original amount (without any format)
     * @param string|int|WP_Post    $points_type        The points type given
     * @param int                   $decimals           Decimals to apply in format
     * @param string                $decimals_sep       Decimals separator to apply in format
     * @param string                $thousands_sep      Thousands separator to apply in format
     */
    return apply_filters( 'gamipress_format_amount', $formatted_amount, $amount, $points_type, $decimals, $decimals_sep, $thousands_sep );

}