<?php
/**
 * Filters
 *
 * @package     GamiPress\Elementor_Forms\Filters
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Available filter to extend the widget form fields
 *
 * @since 1.9.9.2
 *
 * @param array $fields
 * @param array $widget_slug
 *
 * @return array
 */
function gamipress_elementor_forms_setup_widget_fields( $fields, $widget_slug ) {

    // TODO: Deactivated since causes inconsistence fields saving
//    if( ! isset( $_REQUEST['action'] ) ) {
//        return $fields;
//    }
//
//    if( $_REQUEST['action'] !== 'elementor_ajax' ) {
//        return $fields;
//    }

    foreach( $fields as $field_id => $field ) {

        $is_select = (bool) ( in_array( $field['type'], array( 'select', 'advanced_select' ) ) );
        $is_multiple = (bool) ( isset( $field['multiple'] ) && $field['multiple'] );

        // Turn field IDs selects to text fields
        if( strpos( $field_id, 'id' ) !== false && $is_select ) {
            $field['type'] = 'text';
            $is_select = false;
            $field['name'] .= ( $is_multiple ? ' IDs' : ' ID' );

            if( $is_multiple ) {
                unset( $field['multiple'] );
            }
        }

        // Turn post selectors to text fields
        if( isset( $field['classes'] ) && strpos( $field['classes'], 'post-selector' ) && $is_select ) {
            $field['type'] = 'text';
            $is_select = false;
            $field['name'] .= ( $is_multiple ? ' IDs' : ' ID' );

            if( $is_multiple ) {
                unset( $field['multiple'] );
            }
        }

        // Turn user selectors to text fields
        if( isset( $field['classes'] ) && strpos( $field['classes'], 'user-selector' )  && $is_select ) {
            $field['type'] = 'text';
            $is_select = false;
            $field['name'] .= ( $is_multiple ? ' IDs' : ' ID' );

            if( $is_multiple ) {
                unset( $field['multiple'] );
            }

        }

        // Turn select multiple fields to multi checkboxes
        if( $is_select && $is_multiple ) {
            $field['type'] = 'multicheck';
        }

        // Update description to the shortcode one
        if( isset( $field['shortcode_desc'] ) ) {

            if( isset( $field['desc'] ) ) {
                $field['desc'] = $field['shortcode_desc'];
            }

            if( isset( $field['description'] ) ) {
                $field['description'] = $field['shortcode_desc'];
            }
        }

        $fields[$field_id] = $field;

    }

    return $fields;

}
add_filter( 'gamipress_setup_widget_fields', 'gamipress_elementor_forms_setup_widget_fields', 10, 2 );