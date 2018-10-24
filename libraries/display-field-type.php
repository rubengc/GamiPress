<?php
/**
 * CMB2 Display Field Type
 */

if( ! function_exists( 'cmb2_render_display' ) ) :

    /**
     * Render a text-only field type
     *
     * @param  array 			$field 			The field data array
     * @param  mixed 			$value 			The stored value for this field
     * @param  integer|string 	$object_id 		The object ID
     * @param  string 			$object_type 	The object type
     * @param  CMB2_Types 	    $field_type 	The field type object
     *
     * @return string       HTML markup for our field
     */
    function cmb2_render_display( $field, $value, $object_id, $object_type, $field_type ) {

        $value = isset( $field->args['value'] ) ? $field->args['value'] : $value;

        // Parse args
        $attrs = $field_type->parse_args( 'display', array(
            'class'   => $field->args['classes'],
            'name'    => $field_type->_name(),
            'id'      => $field_type->_id(),
        ) );

        echo sprintf( '<span%s>%s</span>%s',
            $field_type->concat_attrs( $attrs ),
            $value,
            $field_type->_desc( true )
        );
    }
    add_action( 'cmb2_render_display', 'cmb2_render_display', 10, 5 );

endif;