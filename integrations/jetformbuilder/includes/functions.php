<?php
/**
 * Functions
 *
 * @package     GamiPress\JetFormBuilder\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get form fields values
 *
 * @since 1.0.0
 *
 * @param Jet_Form_Builder\Form_Handler $form_handler
 *
 * @return array
 */
function gamipress_jetformbuilder_get_form_fields_values( $form_handler ) {

    $form_fields = array();

    $all_fields = $form_handler->action_handler->request_data;

    foreach ( $all_fields as $field_key => $field_value ) {
        if ( empty( $field_value ) ) {
            continue;
        }

        if ( substr( $field_key, 0, 2 ) === '__' ) {
            continue;
        }
        
        $form_fields[$field_key] = $field_value;
    }

    return $form_fields;

}