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
function cmb2_render_select_with_groups_field_type( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
    $output = '<select class="widefat" name="' . $field->args['_name'] . '" id="' . $field->args['_id'] . '"';
    foreach ( $field->args['attributes'] as $attribute => $value ) {
        $output .= " $attribute=\"$value\"";
    }
    $output .= ' />';

    foreach ( $field->options() as $group_label => $group ) {
        $output .= '<optgroup label="'. $group_label .'">';
        foreach ( $group as $key => $label ) {
            $output .= $field_type_object->select_option( array(
                'label'		=> $label,
                'value'		=> $key,
                'checked'	=> $escaped_value == $key
            ));
        }
        $output .= '</optgroup>';
    }

    $output .= '</select>';
    $output .= $field_type_object->_desc( true );

    echo $output; // WPCS: XSS ok.
}
add_action( 'cmb2_render_select_with_groups', 'cmb2_render_select_with_groups_field_type', 10, 5 );