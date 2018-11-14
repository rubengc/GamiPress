<?php
/**
 * Blocks
 *
 * @package     GamiPress\Blocks
 * @since       1.6.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register GamiPress as block category
 *
 * @since 1.6.0
 */
function gamipress_register_block_categories( $categories, $post ) {

    // Register GamiPress as block category
    $categories[] = array(
        'title' => __( 'GamiPress', 'gamipress' ),
        'slug'  => 'gamipress',
        'icon'  => 'gamipress',
    );

    return $categories;

}
add_filter( 'block_categories', 'gamipress_register_block_categories', 10, 2 );

/**
 * Register GamiPress blocks
 *
 * Important: Priority is set to 20 to let every add-on register their shortcodes first
 *
 * @since 1.6.0
 */
function gamipress_register_block_types() {

    // Bail if Gutenberg is not enabled
    if( ! function_exists( 'register_block_type' ) )
        return;

    foreach( GamiPress()->shortcodes as $shortcode ) {

        // Block name requires to be a prefix/block-slug
        // So let's turn gamipress_points_types to gamipress/points-types
        $block_name = str_replace( '_', '-', str_replace( 'gamipress_', 'gamipress/', $shortcode->slug ) );

        // Register block and attributes
        register_block_type( $block_name, array(
            'attributes'      => gamipress_get_block_attributes( $shortcode ),
            'render_callback' => $shortcode->output_callback,
        ) );

        // Filter to remove null attributes passed by Gutenberg
        add_filter( "shortcode_atts_{$shortcode->slug}", 'gamipress_remove_null_block_attributes', 10, 4 );

    }

}
add_action( 'init', 'gamipress_register_block_types', 20 );

/**
 * Setup block fields from give shortcode object
 *
 * @since 1.6.0
 *
 * @param GamiPress_Shortcode $shortcode
 *
 * @return array
 */
function gamipress_get_block_fields( $shortcode ) {

    $fields = array();

    foreach( $shortcode->fields as $field_id => $field ) {

        // Parse options callbacks
        if( isset( $field['options_cb'] ) )
            $field['options'] = gamipress_blocks_parse_options_cb( $field );

        // Set the definitive object field
        $fields[$field_id] = $field;
    }

    /**
     * Filter to override specific block fields
     *
     * @since 1.6.0
     *
     * @param array                 $fields
     * @param GamiPress_Shortcode   $shortcode
     *
     * @return array
     */
    $fields = apply_filters( "gamipress_get_{$shortcode->slug}_block_fields", $fields, $shortcode );

    /**
     * Filter to override block fields
     *
     * @since 1.6.0
     *
     * @param array                 $fields
     * @param GamiPress_Shortcode   $shortcode
     *
     * @return array
     */
    return apply_filters( 'gamipress_get_block_fields', $fields, $shortcode );

}

/**
 * Turn select2 fields into 'post' or 'user' field types
 *
 * @since 1.6.0
 *
 * @param array                 $fields
 * @param GamiPress_Shortcode   $shortcode
 *
 * @return array
 */
function gamipress_blocks_selector_fields( $fields, $shortcode ) {

    switch ( $shortcode->slug ) {
        case 'gamipress_achievement':
            // Achievement ID
            $fields['id']['type'] = 'post';
            $fields['id']['post_type'] = gamipress_get_achievement_types_slugs();
            break;
        case 'gamipress_achievements':
            // Include
            $fields['include']['type'] = 'post';
            $fields['include']['post_type'] = gamipress_get_achievement_types_slugs();
            // Exclude
            $fields['exclude']['type'] = 'post';
            $fields['exclude']['post_type'] = gamipress_get_achievement_types_slugs();
            break;
        case 'gamipress_rank':
            // Achievement ID
            $fields['id']['type'] = 'post';
            $fields['id']['post_type'] = gamipress_get_rank_types_slugs();
            break;
        case 'gamipress_ranks':
            // Include
            $fields['include']['type'] = 'post';
            $fields['include']['post_type'] = gamipress_get_rank_types_slugs();
            // Exclude
            $fields['exclude']['type'] = 'post';
            $fields['exclude']['post_type'] = gamipress_get_rank_types_slugs();
            break;
    }

    // TODO: [gamipress_logs] include and exclude (just posible when CT includes WP Rest API endpoints)

    // Common fields to all shortcodes

    // User ID
    if( isset( $fields['user_id'] ) ) {
        // User ID
        $fields['user_id']['type'] = 'user';
    }

    // User ID visibility when current user field is in form
    if( isset( $fields['user_id'] ) && isset( $fields['current_user'] ) ) {
        // Setup as display condition that current_user needs to be false (unchecked)
        $fields['user_id']['conditions'] = array(
            'current_user' => false,
        );
    }

    return $fields;
}
add_filter( 'gamipress_get_block_fields', 'gamipress_blocks_selector_fields', 10, 2 );

/**
 * Setup block attributes from give shortcode object
 *
 * @since 1.6.0
 *
 * @param GamiPress_Shortcode $shortcode
 *
 * @return array
 */
function gamipress_get_block_attributes( $shortcode ) {

    $attributes = array();

    // Turn fields into attributes
    foreach( $shortcode->fields as $field_id => $field ) {

        if( $field['type'] === 'checkbox' ) {

            // Checkboxes as boolean
            $attributes[$field_id] = array(
                'type' => 'boolean',
            );

        } else {

            // String is the default type
            $attributes[$field_id] = array(
                'type' => 'string',
            );

        }

    }

    /**
     * Filter to override specific block attributes
     *
     * @since 1.6.0
     *
     * @param array                 $attributes
     * @param GamiPress_Shortcode   $shortcode
     *
     * @return array
     */
    $attributes = apply_filters( "gamipress_gutenberg_blocks_get_{$shortcode->slug}_block_attributes", $attributes, $shortcode );

    /**
     * Filter to override block attributes
     *
     * @since 1.6.0
     *
     * @param array                 $attributes
     * @param GamiPress_Shortcode   $shortcode
     *
     * @return array
     */
    return apply_filters( 'gamipress_gutenberg_blocks_get_block_attributes', $attributes, $shortcode );

}

/**
 * Removes null attributes passed by Gutenberg
 *
 * @since 1.6.0
 *
 * @param array  $out       The output array of shortcode attributes
 * @param array  $pairs     The supported attributes and their defaults
 * @param array  $atts      The user defined shortcode attributes
 * @param string $shortcode The shortcode name
 *
 * @return array
 */
function gamipress_remove_null_block_attributes( $out, $pairs, $atts, $shortcode ) {

    // Bail if shortcode is not registered
    if( ! isset( GamiPress()->shortcodes[$shortcode] ) )
        return $out;

    // Setup the shortcode object
    $shortcode_obj = GamiPress()->shortcodes[$shortcode];

    foreach( $out as $name => $value ) {

        // Gutenberg pass default values and empty as null
        // So let's force default value instead of null
        if( $value === null )
            $out[$name] = $pairs[$name];

        if( isset( $shortcode_obj->fields[$name] ) ) {
            $field = $shortcode_obj->fields[$name];

            if( $field['type'] === 'checkbox' && is_bool( $out[$name] ) ) {

                // Turn booleans into yes|no
                $out[$name] = ( $out[$name] === true ? 'yes' : 'no' );

            }
        }
    }

    return $out;

}

/**
 * Simulates CMB2 options_cb processing
 *
 * @since 1.6.0
 *
 * @param array $field
 *
 * @return array
 */
function gamipress_blocks_parse_options_cb( $field ) {

    $field_options = ( isset( $field['options'] ) ? (array) $field['options'] : array() );

    if ( isset( $field['options_cb'] ) && is_callable( $field['options_cb'] ) ) {
        $options = call_user_func( $field['options_cb'], gamipress_blocks_cmb_field_object( $field ) );

        if ( $options && is_array( $options ) ) {
            $field_options = $options + $field_options;
        }
    }

    return $field_options;

}

/**
 * Builds a false CMB2 field object
 *
 * @since 1.6.0
 *
 * @param array $field
 *
 * @return stdClass
 */
function gamipress_blocks_cmb_field_object( $field ) {
    return (object) array(
        'args' => $field,
        'value' => '',
        'escaped_value' => '',
    );
}