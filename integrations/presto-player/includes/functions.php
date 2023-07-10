<?php
/**
 * Functions
 *
 * @package GamiPress\Presto_Player\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_presto_player_ajax_get_posts() {

    global $wpdb;

    if( isset( $_REQUEST['post_type'] ) && in_array( 'presto_player_video', $_REQUEST['post_type'] ) ) {

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

                $site_videos = $wpdb->get_results( $wpdb->prepare(
                    "SELECT v.id AS ID, v.title AS post_title
                    FROM   {$wpdb->prefix}presto_player_videos AS v
                    WHERE  v.title LIKE %s",
                    "%%{$search}%%"
                ) );

                foreach ( $site_videos as $video ) {

                    // Results should meet same structure like posts
                    $results[] = array(
                        'ID' => $video->ID,
                        'post_title' => $video->post_title,
                        'site_id' => $site_id,
                        'site_name' => $site_name,
                    );

                }

                // Restore current site
                restore_current_blog();

            }
        } else {

            $results = $wpdb->get_results( $wpdb->prepare(
                "SELECT v.id AS ID, v.title AS post_title
                    FROM   {$wpdb->prefix}presto_player_videos AS v
                    WHERE  v.title LIKE %s",
                "%%{$search}%%"
            ) );

        }

        // Return our results
        wp_send_json_success( $results );
        die;
    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_presto_player_ajax_get_posts', 5 );