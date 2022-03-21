<?php
/**
 * Blocks
 *
 * @package     GamiPress\Blocks
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.6.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Select the right "block_categories" filter according to WP version
 *
 * @since 2.1.9
 */
function gamipress_init_block_category() {

    global $wp_version;

    if( version_compare( $wp_version, '5.8', '<' ) ) {
        // WP version is less than 5.8
        add_filter( 'block_categories', 'gamipress_register_block_categories', 10, 2 );
    } else {
        // WP version is 5.8 or higher
        add_filter( 'block_categories_all', 'gamipress_register_block_categories', 10, 2 );
    }

}
add_action( 'gamipress_init', 'gamipress_init_block_category' );

/**
 * Register GamiPress as block category
 *
 * @since 1.6.0
 */
function gamipress_register_block_categories( $categories, $post ) {

    // Register GamiPress as block category
    $categories[] = array(
        'title' => 'GamiPress',
        'slug'  => 'gamipress',
        'icon'  => 'gamipress',
    );

    return $categories;

}

/**
 * GamiPress block icons
 *
 * @since 1.6.1
 */
function gamipress_get_block_icons() {

    $icons = array(
        'gamipress' =>
            '<svg width="24" height="24" viewBox="0 0 167.548 167.548" xmlns="http://www.w3.org/2000/svg" >
                <path d="M 131.973,136.242 H 35.596 L 8.108,57.111 42.507,82.363 c -0.172,0.873 -0.264,1.727 -0.264,2.584 0,7.815 6.359,14.175 14.175,14.175 7.812,0 14.175,-6.359 14.175,-14.175 0,-6.091 -3.797,-11.274 -9.273,-13.255 l 22.465,-47.363 22.396,47.239 c -5.627,1.898 -9.629,7.193 -9.629,13.384 0,7.815 6.359,14.175 14.175,14.175 7.81,0 14.175,-6.359 14.175,-14.175 0,-1.036 -0.121,-2.077 -0.364,-3.103 l 34.984,-24.922 z"/>
            </svg>',
        'rank' =>
            '<svg width="24" height="24" viewBox="0 0 92.275001 92.275002" xmlns="http://www.w3.org/2000/svg" >
                <polygon points="50.1,96.2 76.2,81.3 76.2,66.5 50.1,81.3 24.1,66.5 24.1,81.3" transform="translate(-4.1,-3.9250045)"/>
                <polygon points="50.1,74.5 76.2,59.7 76.2,44.8 50.1,59.7 24.1,44.8 24.1,59.7" transform="translate(-4.1,-3.9250045)"/>
                <path d="M 47,0.67499541 52.6,11.974994 c 0.2,0.3 0.5,0.5 0.8,0.6 l 12.5,1.8 c 0.9,0.1 1.3,1.2 0.6,1.9 l -9,8.8 c -0.3,0.3 -0.4,0.6 -0.3,1 l 2.1,12.5 c 0.2,0.9 -0.8,1.6 -1.6,1.2 l -11.2,-5.9 c -0.3,-0.2 -0.7,-0.2 -1,0 l -11.2,5.9 c -0.8,0.4 -1.7,-0.3 -1.6,-1.2 l 2.2,-12.5 c 0.1,-0.4 -0.1,-0.7 -0.3,-1 l -9.1,-8.8 c -0.7,-0.6 -0.3,-1.7 0.6,-1.9 l 12.5,-1.8 c 0.4,-0.1 0.7,-0.3 0.8,-0.6 L 45,0.67499541 c 0.5,-0.9 1.6,-0.9 2,0 z"/>
            </svg>',
    );

    /**
     * Filter to register custom GamiPress block icons
     *
     * @since 1.6.1
     *
     * @param array $icons Icons already registered
     *
     * @return array
     */
    return apply_filters( 'gamipress_block_icons', $icons );

}

/**
 * Register GamiPress blocks
 *
 * Important: Priority is set to 20 to let every add-on register their shortcodes first
 *
 * @since 1.6.0
 */
function gamipress_register_block_types() {

    // Bail if Gutenberg is not enabled
    if( ! function_exists( 'register_block_type' ) ) {
        return;
    }

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
        if( isset( $field['options_cb'] ) ) {
            $field['options'] = gamipress_blocks_parse_options_cb( $field );
        }

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
        case 'gamipress_inline_achievement':
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
        case 'gamipress_inline_rank':
            // Rank ID
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
        case 'gamipress_points':
        case 'gamipress_user_points':
        case 'gamipress_site_points':
            // Period start and end visibility
            $fields['period_start']['conditions'] = array( 'period' => 'custom' );
            $fields['period_end']['conditions'] = array( 'period' => 'custom' );
            // Inline conditions
            $inline_condition = array(
                'field_id' => 'inline',
                'value' => true,
                'compare' => '!=',
            );
            $fields['columns']['conditions'] = array( $inline_condition );
            $fields['layout']['conditions'] = array( $inline_condition );
            $fields['align']['conditions'] = array( $inline_condition );
            break;
        case 'gamipress_points_types':
            // Points Types attributes
            $fields['toggle']['conditions'] = array( 'awards' => true, 'deducts' => true, 'relation' => 'OR' );
            $fields['heading']['conditions'] = array( 'awards' => true, 'deducts' => true, 'relation' => 'OR' );
            $fields['heading_size']['conditions'] = array( 'heading' => true );
            break;
    }

    // Common attributes
    switch ( $shortcode->slug ) {
        case 'gamipress_achievement':
        case 'gamipress_achievements':
        case 'gamipress_last_achievements_earned':
            // Achievement common attributes
            $fields['title_size']['conditions'] = array( 'title' => true );
            $fields['thumbnail_size']['conditions'] = array( 'thumbnail' => true );
            $fields['points_awarded_thumbnail']['conditions'] = array( 'points_awarded' => true );
            $fields['toggle']['conditions'] = array( 'steps' => true );
            $fields['heading']['conditions'] = array( 'steps' => true );
            $fields['heading_size']['conditions'] = array( 'steps' => true, 'heading' => true );
            $fields['earners_limit']['conditions'] = array( 'earners' => true );
            break;
        case 'gamipress_inline_achievement':
        case 'gamipress_inline_last_achievements_earned':
            // Inline achievement common attributes
            $fields['thumbnail_size']['conditions'] = array( 'thumbnail' => true );
            break;
        case 'gamipress_rank':
        case 'gamipress_ranks':
        case 'gamipress_user_rank':
            // Rank common attributes
            $fields['title_size']['conditions'] = array( 'title' => true );
            $fields['thumbnail_size']['conditions'] = array( 'thumbnail' => true );
            $fields['toggle']['conditions'] = array( 'requirements' => true );
            $fields['heading']['conditions'] = array( 'requirements' => true );
            $fields['heading_size']['conditions'] = array( 'requirements' => true, 'heading' => true );
            $fields['earners_limit']['conditions'] = array( 'earners' => true );
            break;
        case 'gamipress_inline_rank':
        case 'gamipress_inline_user_rank':
            // Inline rank common attributes
            $fields['thumbnail_size']['conditions'] = array( 'thumbnail' => true );
            break;
        case 'gamipress_points':
        case 'gamipress_user_points':
        case 'gamipress_site_points':
        case 'gamipress_points_types':
            // Points common attributes
            $fields['thumbnail_size']['conditions'] = array( 'thumbnail' => true );
            break;
    }

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
        if( $value === null ) {
            $out[$name] = $pairs[$name];
        }

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
 * Filter block per-rendering to setup the renderer for error messages
 *
 * @since 1.7.6
 *
 * @param string $pre_render The pre-rendered content. Default null.
 * @param array  $block      The block being rendered.
 *
 * @return string
 */
function gamipress_pre_render_block( $pre_render, $block ) {

    global $gamipress_renderer;

    // Setup renderer
    $gamipress_renderer = 'block';

    return $pre_render;
}
add_filter( 'pre_render_block', 'gamipress_pre_render_block', 10, 2 );

/**
 * Filter block rendering to reset the renderer for error messages
 *
 * @since 1.7.6
 *
 * @param string $block_content The block content about to be appended.
 * @param array  $block         The full block, including name and attributes.
 *
 * @return string
 */
function gamipress_render_block( $block_content, $block ) {

    global $gamipress_renderer;

    // Reset renderer
    $gamipress_renderer = false;

    return $block_content;
}
add_filter( 'render_block', 'gamipress_render_block', 10, 2 );

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