<?php
/**
 * CMB2 GamiPress Points Field Type
 *
 * @package GamiPress|CMB2_GamiPress_Points_Field_Type
 */

if( ! function_exists( 'cmb2_render_gamipress_points_field_type' ) ) :

    /**
     * Adds a custom field type for GamiPress points.
     *
     * @param  object       $field          The CMB2_Field type object.
     * @param  string       $value          The saved (and escaped) value.
     * @param  int          $object_id      The current post ID.
     * @param  string       $object_type    The current object type.
     * @param  CMB2_Types   $field_type     The CMB2_Types object.
     *
     * @return void
     */
    function cmb2_render_gamipress_points_field_type( $field, $value, $object_id, $object_type, $field_type ) {

        global $ct_cmb2_override;

        $post_type_meta_key = $field->args['id'] . '_type';

        if( isset( $field->args['points_type_key'] ) ) {
            $post_type_meta_key = $field->args['points_type_key'];
        }

        // Support for CT objects
        if( $ct_cmb2_override === true ) {
            $points_type = ct_get_object_meta( $object_id, $post_type_meta_key, true );
        } else {
            $points_type = get_post_meta( $object_id, $post_type_meta_key, true );
        }

        // Grab our points types as a html
        $points_types_options = '<option value="" class="points-type-placeholder">' . __( 'Choose the Points Type', 'gamipress' ) . '</option>';

        foreach( gamipress_get_points_types() as $slug => $data ) {
            $points_types_options .= '<option value="' . $slug . '" class="' . $slug . '" ' . selected( $points_type, $slug, false ) . '>' . $data['plural_name'] . '</option>';
        } ?>

        <div class="cmb-inline">
            <ul>
                <li>
                    <?php echo $field_type->input( array(
                        'name'  => $field_type->_name(),
                        'id'    => $field_type->_id(),
                        'value' => $value,
                        'desc'  => '',
                        'type' => 'number',
                        'step' => 1,
                        'min' => 0,
                        'class' => 'medium-text',
                        'placeholder' => 0
                    ) ); ?>
                </li>
                <li>
                    <?php echo $field_type->select( array(
                        'name'  => $post_type_meta_key,
                        'id'    => $post_type_meta_key,
                        'value' => $points_type,
                        'desc'  => '',
                        'options' => $points_types_options,
                        'onchange' => 'this.className=this.options[this.selectedIndex].className',
                    ) ); ?>
                </li>
            </ul>
        </div>
        <?php

        echo $field_type->_desc( true );

    }
    add_action( 'cmb2_render_gamipress_points', 'cmb2_render_gamipress_points_field_type', 10, 5 );

    /**
     * After save GamiPress points field type, needs to store the points type value too
     *
     * @param string            $field_id the current field id paramater.
     * @param bool              $updated  Whether the metadata update action occurred.
     * @param string            $action   Action performed. Could be "repeatable", "updated", or "removed".
     * @param CMB2_Field object $field    This field object
     */
    function cmb2_save_gamipress_points_field_type( $field_id, $updated, $action, $field ) {

        global $ct_cmb2_override;

        if( $field->args['type'] !== 'gamipress_points' ) {
            return;
        }

        $post_type_meta_key = $field->args['id'] . '_type';

        if( isset( $field->args['points_type_key'] ) ) {
            $post_type_meta_key = $field->args['points_type_key'];
        }

        if( $ct_cmb2_override === true ) {
            ct_update_object_meta( $field->object_id, $post_type_meta_key, sanitize_text_field( $_REQUEST[$post_type_meta_key] ) );
        } else {
            update_metadata( $field->object_type, $field->object_id, $post_type_meta_key, sanitize_text_field( $_REQUEST[$post_type_meta_key] ) );
        }

    }
    add_action( 'cmb2_save_field', 'cmb2_save_gamipress_points_field_type', 10, 4 );

endif;