<?php
/**
 * CMB2 Size Field Type
 *
 * @package GamiPress|CMB2_Size_Field_Type
 */

if( ! function_exists( 'cmb2_render_size_field_type' ) ) :

    /**
     * Adds a custom field type for dimension sizes.
     *
     * @param  object       $field          The CMB2_Field type object.
     * @param  string       $value          The saved (and escaped) value.
     * @param  int          $object_id      The current post ID.
     * @param  string       $object_type    The current object type.
     * @param  CMB2_Types   $field_type     The CMB2_Types object.
     *
     * @return void
     */
    function cmb2_render_size_field_type( $field, $value, $object_id, $object_type, $field_type ) {

        // Make sure we specify each part of the value we need.
        $value = wp_parse_args( $value, array(
            'width' => 100,
            'height' => 100,
        ) ); ?>

        <div class="cmb-inline">
            <ul>
                <li>
                    <label for="<?php echo $field_type->_id( '_width' ); ?>"><?php _e( 'Max Width' ); ?></label>
                    <?php echo $field_type->input( array(
                        'name'  => $field_type->_name( '[width]' ),
                        'id'    => $field_type->_id( '_width' ),
                        'value' => $value['width'],
                        'desc'  => '',
                        'type' => 'number',
                        'step' => 1,
                        'min' => 0,
                        'class' => 'small-text',
                    ) ); ?>
                </li>
                <li>
                    <label for="<?php echo $field_type->_id( '_height' ); ?>"><?php _e( 'Max Height' ); ?></label>
                    <?php echo $field_type->input( array(
                        'name'  => $field_type->_name( '[height]' ),
                        'id'    => $field_type->_id( '_height' ),
                        'value' => $value['height'],
                        'desc'  => '',
                        'type' => 'number',
                        'step' => 1,
                        'min' => 0,
                        'class' => 'small-text',
                    ) ); ?>
                </li>
            </ul>
        </div>
        <?php

        echo $field_type->_desc( true );
    }
    add_action( 'cmb2_render_size', 'cmb2_render_size_field_type', 10, 5 );


    /**
     * Sanitize the selected value.
     */
    function cmb2_sanitize_size_callback( $override_value, $value ) {

        if ( is_array( $value ) ) {

            foreach ( $value as $key => $saved_value ) {
                $value[$key] = sanitize_text_field( $saved_value );
            }

            return $value;
        }

        return $override_value;
    }
    add_filter( 'cmb2_sanitize_size', 'cmb2_sanitize_size_callback', 10, 2 );

endif;
