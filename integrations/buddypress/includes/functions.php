<?php
/**
 * Functions
 *
 * @package GamiPress\BuddyPress\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Compatibility function to check if a BuddyPress module is active
 *
 * @since 1.2.0
 *
 * @param string $component The component name.
 *
 * @return bool
 */
function gamipress_bp_is_active( $component = '' ) {

    if( function_exists( 'bp_is_active' ) ) {
        return bp_is_active( $component );
    }

    return true;

}

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_bp_ajax_get_posts() {

    global $wpdb;

    if( isset( $_REQUEST['post_type'] ) && in_array( 'bp_groups', $_REQUEST['post_type'] ) ) {

        $results = array();

        // Pull back the search string
        $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : false;

        $bp_groups = groups_get_groups( array(
            'search_terms' => $search,
            'show_hidden' => true,
            'per_page' => 300
        ) );

        if ( ! empty( $bp_groups ) ) {
            foreach ( $bp_groups['groups'] as $group ) {
                // Results should meet same structure like posts
                $results[] = array(
                    'ID' => $group->id,
                    'post_title' => $group->name,
                );
            }
        }

        // Return our results
        wp_send_json_success( $results );
        die;
    } else if ( isset( $_REQUEST['post_type'] ) && in_array( 'bp_fields', $_REQUEST['post_type'] ) ) {
        $results = array();

        // Pull back the search string
        $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';
        $page = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;
        $limit = 20;
        $offset = $limit * ( $page - 1 );
        $prefix = gamipress_bp_get_table_prefix();

        if( ! empty( $search ) ) {
            $profile_fields = $wpdb->get_results( $wpdb->prepare(
                "SELECT * 
                FROM {$prefix}bp_xprofile_fields 
                WHERE name LIKE %s 
                ORDER BY field_order ASC
                LIMIT {$offset}, {$limit}",
                "%{$search}%"
            ) );

            $count = absint( $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$prefix}bp_xprofile_fields WHERE name LIKE %s",
                "%%{$search}%%"
            ) ) );

        } else {
            $profile_fields = $wpdb->get_results(
                "SELECT * 
                FROM {$prefix}bp_xprofile_fields 
                ORDER BY field_order ASC
                LIMIT {$offset}, {$limit}"
            );

            $count = absint( $wpdb->get_var(
                "SELECT COUNT(*) FROM {$prefix}bp_xprofile_fields"
            ) );
        }

        if ( ! empty( $profile_fields ) ) {
            foreach ( $profile_fields as $profile_field ) {

                // Results should meet Select2 structure
                $results[] = array(
                    'ID' => $profile_field->id,
                    'post_title' => $profile_field->name,
                );
        
            }    

        }

        $response = array(
            'results' => $results,
            'more_results' => $count > $limit && $count > $offset,
        );

        // Return our results
        wp_send_json_success( $response );
        die;
    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_bp_ajax_get_posts', 5 );

/**
 * Helper function to get the table prefix
 *
 * @since 1.0.0
 *
 * @return string
 */
function gamipress_bp_get_table_prefix() {

    global $wpdb;

    if( function_exists( 'bp_core_get_table_prefix' ) ) {
        return bp_core_get_table_prefix();
    }

    return $wpdb->prefix;

}

function gamipress_bp_get_profile_field_name( $field_id ) {

    global $wpdb;
    $prefix = gamipress_bp_get_table_prefix();

    $name_field = $wpdb->get_var( $wpdb->prepare(
        "SELECT name 
        FROM {$prefix}bp_xprofile_fields 
        WHERE id = %d",
        $field_id
    ) );

    return $name_field;
}