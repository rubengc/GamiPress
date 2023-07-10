<?php
/**
 * Functions
 *
 * @package GamiPress\WS_Form\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_ws_form_ajax_get_posts() {

    global $wpdb;

    if( isset( $_REQUEST['post_type'] ) && in_array( 'ws_form', $_REQUEST['post_type'] ) ) {

        // Pull back the search string
        $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';
        $results = array();

        if( gamipress_is_network_wide_active() ) {

            // Look for results on all sites on a multisite install

            foreach( gamipress_get_network_site_ids() as $site_id ) {

                // Switch to site
                switch_to_blog( $site_id );

                // Get the current site name to append it to results
                $site_name = get_bloginfo( 'name' );

                // Get the forms
                $forms = wsf_form_get_all( $published = false, $order_by = 'label' );

                foreach( $forms as $form ) {

                    if( $search && ( strpos( strtolower( $form['label'] ), $search) === false) ) {
                        continue;
                    }

                    // Results should meet the Select2 structure
                    $results[] = array(
                        'ID' => $form['id'],
                        'post_title' => $form['label'],
                        'site_id' => $site_id,
                        'site_name' => $site_name,
                    );

                }

                // Restore current site
                restore_current_blog();

            }
        } else {

            // Get the forms
            $forms = wsf_form_get_all( $published = false, $order_by = 'label' );

            foreach( $forms as $form ) {

                if( $search && ( strpos( strtolower( $form['label'] ), $search) === false) ) {
                    continue;
                }

                // Results should meet the Select2 structure
                $results[] = array(
                    'ID' => $form['id'],
                    'post_title' => $form['label'],
                );

            }

        }

        // Return our results
        wp_send_json_success( $results );
        die;
    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_ws_form_ajax_get_posts', 5 );