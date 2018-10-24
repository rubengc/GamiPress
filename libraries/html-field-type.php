<?php
/**
 * CMB2 HTML Field Type
 */

if( ! function_exists( 'cmb2_render_html' ) ) :

    /**
     * Render the HTML content
     *
     * @param  array 			$field 			The field data array
     * @param  mixed 			$value 			The stored value for this field
     * @param  integer|string 	$object_id 		The object ID
     * @param  string 			$object_type 	The object type
     * @param  CMB2_Types 	    $field_type 	The field type object
     *
     * @return string       HTML markup for our field
     */
    function cmb2_render_html( $field, $value, $object_id, $object_type, $field_type ) {

        // Parse args
        $attrs = $field_type->parse_args( 'html', array(
            'id'      => $field_type->_id(),
        ) );

        if( isset( $field->args['content_cb'] ) && ! empty( $field->args['content_cb'] ) ) {
            ob_start();
            call_user_func_array( $field->args['content_cb'], array( $field, $object_id, $object_type ) );
            $field->args['content'] = ob_get_clean();
        }

        echo sprintf( '<div %s>%s</div>',
            $field_type->concat_attrs( $attrs ),
            ( isset( $field->args['content'] ) && ! empty( $field->args['content'] ) ? $field->args['content'] : '' )
        );
    }
    add_action( 'cmb2_render_html', 'cmb2_render_html', 10, 5 );

endif;