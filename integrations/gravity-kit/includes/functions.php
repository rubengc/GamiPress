<?php
/**
 * Functions
 *
 * @package GamiPress\Gravity_Kit\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_gravity_kit_ajax_get_posts() {

    global $wpdb;

    if( isset( $_REQUEST['post_type'] ) && in_array( 'gravity_form', $_REQUEST['post_type'] ) ) {

        // Pull back the search string
        $search = isset( $_REQUEST['q'] ) ? wpdb::esc_like( $_REQUEST['q'] ) : '';
        $results = array();

        if( gamipress_is_network_wide_active() ) {

            // Look for results on all sites on a multisite install

            foreach( gamipress_get_network_site_ids() as $site_id ) {

                // Switch to site
                switch_to_blog( $site_id );

                // Get the current site name to append it to results
                $site_name = get_bloginfo( 'name' );

                $site_forms = $wpdb->get_results( $wpdb->prepare(
                    "SELECT p.id AS ID, p.title AS post_title
                    FROM   {$wpdb->prefix}gf_form AS p
                    WHERE  p.title LIKE %s",
                    "%%{$search}%%"
                ) );

                foreach ( $site_forms as $form ) {

                    // Results should meet same structure like posts
                    $results[] = array(
                        'ID' => $form->ID,
                        'post_title' => $form->post_title,
                        'site_id' => $site_id,
                        'site_name' => $site_name,
                    );

                }

                // Restore current site
                restore_current_blog();

            }
        } else {

            $results = $wpdb->get_results( $wpdb->prepare(
                "SELECT p.id AS ID, p.title AS post_title
                    FROM   {$wpdb->prefix}gf_form AS p
                    WHERE  p.title LIKE %s",
                "%%{$search}%%"
            ) );

        }



        // Return our results
        wp_send_json_success( $results );
        die;
    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_gravity_kit_ajax_get_posts', 5 );

/**
 * Get entry form
 *
 * @since 1.0.0
 *
 * @param int       $entry_id      The entry ID
 *
 * @return int
 */
function gamipress_gravity_kit_get_entry_form ( $entry_id ) {

    global $wpdb;

	$form_id = $wpdb->get_var( $wpdb->prepare( "SELECT form_id from {$wpdb->prefix}gf_entry WHERE id=%d", $entry_id ) );

    return $form_id;

}

/**
 * Get entry user
 *
 * @since 1.0.0
 *
 * @param int       $entry_id      The entry ID
 *
 * @return int|NULL
 */
function gamipress_gravity_kit_get_entry_user ( $entry_id ) {

    global $wpdb;

	$user_id = $wpdb->get_var( $wpdb->prepare( "SELECT created_by from {$wpdb->prefix}gf_entry WHERE id=%d", $entry_id ) );

    return $user_id;

}