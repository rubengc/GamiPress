<?php
/**
 * Functions
 *
 * @package GamiPress\Formidable_Forms\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_frm_ajax_get_posts() {

    if( isset( $_REQUEST['post_type'] ) && in_array( 'formidable_form', $_REQUEST['post_type'] ) ) {
        $results = array();

        $forms = FrmForm::getAll();

        foreach ( $forms as $form ) {

            // Results should meet same structure like posts
            $results[] = array(
                'ID' => $form->id,
                'post_title' => $form->name,
            );

        }

        // Return our results
        wp_send_json_success( $results );
        die;
    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_frm_ajax_get_posts', 5 );