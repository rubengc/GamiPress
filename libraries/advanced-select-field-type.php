<?php
/**
 * CMB2 Advanced Select Field Type
 */

if( ! function_exists( 'cmb2_render_advanced_select_field_type' ) ) :

    /**
     * Adds a custom field type for select multiples.
     * @param  object $field             The CMB2_Field type object.
     * @param  string $escaped_value     The saved (and escaped) value.
     * @param  int    $object_id         The current post ID.
     * @param  string $object_type       The current object type.
     * @param  CMB2_Types $field_type_object The CMB2_Types object.
     * @return void
     */
    function cmb2_render_advanced_select_field_type( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

        // Parse args
        $attrs = $field_type_object->parse_args( 'select', array(
            'class'   => 'cmb2_select',
            'name'    => $field_type_object->_name(),
            'id'      => $field_type_object->_id(),
        ) );

        // Support for multiple
        if( $field->args['multiple'] ) {
            $attrs['name'] = $attrs['name'] . '[]';
            $attrs['multiple'] = 'true';
        }

        // Parse options
        $options = '';

        foreach ( $field->options() as $value => $option ) {

            if( is_array( $option ) ) {

                // Options group
                $options .= '<optgroup label="'. $value .'">';

                foreach ( $option as $key => $label ) {
                    $selected = ( is_array( $escaped_value ) && in_array( $key, $escaped_value ) ) || $escaped_value === $key;

                    $options .= sprintf( '<option value="%s"%s>%s</option>',
                        $key,
                        $selected ? 'selected="selected"' : '',
                        $label
                    );
                }

                $options .= '</optgroup>';

            } else {

                $selected = ( is_array( $escaped_value ) && in_array( $value, $escaped_value ) ) || $escaped_value === $value;

                //Single option
                $options .= sprintf( '<option value="%s"%s>%s</option>',
                    $value,
                    $selected ? 'selected="selected"' : '',
                    $option
                );

            }
        }

        echo sprintf( '<select%s>%s</select>%s',
            $field_type_object->concat_attrs( $attrs ),
            $options,
            $field_type_object->_desc( true )
        );

    }
    add_action( 'cmb2_render_advanced_select', 'cmb2_render_advanced_select_field_type', 10, 5 );


    /**
     * Sanitize the selected value.
     */
    function cmb2_sanitize_advanced_select_callback( $override_value, $value ) {

        // Sanitize multiple value
        if ( is_array( $value ) ) {

            foreach ( $value as $key => $saved_value ) {

                $value[$key] = sanitize_text_field( $saved_value );

            }

            return $value;

        }

        return sanitize_text_field( $value );

    }
    add_filter( 'cmb2_sanitize_advanced_select', 'cmb2_sanitize_advanced_select_callback', 10, 2 );

endif;
