<?php
/**
 * CMB2 Select Multiple Custom Field Type
 * @package CMB2 Select Multiple Field Type
 */

/**
 * Adds a custom field type for select multiples.
 * @param  object $field             The CMB2_Field type object.
 * @param  string $value             The saved (and escaped) value.
 * @param  int    $object_id         The current post ID.
 * @param  string $object_type       The current object type.
 * @param  object $field_type_object The CMB2_Types object.
 * @return void
 */
function cmb2_render_size_field_type( $field, $value, $object_id, $object_type, $field_type ) {

    // make sure we specify each part of the value we need.
    $value = wp_parse_args( $value, array(
        'width' => 100,
        'height' => 100,
    ) ); ?>

    <div class="cmb-inline">
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

    return;
}
add_filter( 'cmb2_sanitize_size', 'cmb2_sanitize_size_callback', 10, 2 );
