<?php
/**
 * Import/Export Setup Tool
 *
 * @package     GamiPress\Admin\Tools\Import_Export_Setup
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.7.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Import/Export Setup Tool meta boxes
 *
 * @since  1.7.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_import_export_setup_tool_meta_boxes( $meta_boxes ) {

    // Setup vars
    $points_types = gamipress_get_points_types();
    $achievement_types = gamipress_get_achievement_types();
    $rank_types = gamipress_get_rank_types();

    $options = array();

    // Points types options
    if( count( $points_types ) ) $options['all-points-types'] = __( 'All Points Types', 'gamipress' );

    foreach( $points_types as $slug => $data ) {
        $options[$slug . '-points-type'] = '<strong>' . $data['plural_name'] . '</strong>';
        $options[$slug . '-points-awards'] = __( 'Points Awards', 'gamipress' );
        $options[$slug . '-points-deducts'] = __( 'Points Deductions', 'gamipress' );
    }

    // Achievement types options
    if( count( $achievement_types ) ) $options['all-achievement-types'] = __( 'All Achievement Types', 'gamipress' );

    foreach( $achievement_types as $slug => $data ) {
        $options[$slug . '-achievement-type'] = '<strong>' . $data['singular_name'] . '</strong>';
        $options[$slug . '-achievements'] = __( 'Achievements', 'gamipress' );
        $options[$slug . '-steps'] = __( 'Steps', 'gamipress' );
    }

    // Rank types options
    if( count( $rank_types ) ) $options['all-rank-types'] = __( 'All Rank Types', 'gamipress' );

    foreach( $rank_types as $slug => $data ) {
        $options[$slug . '-rank-type'] = '<strong>' . $data['singular_name'] . '</strong>';
        $options[$slug . '-ranks'] = __( 'Ranks', 'gamipress' );
        $options[$slug . '-rank-requirements'] = __( 'Requirements', 'gamipress' );
    }

    $meta_boxes['import-export-setup'] = array(
        'title' => gamipress_dashicon( 'admin-generic' ) . __( 'Setup', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_import_export_setup_tool_fields', array(
            'export_setup_options' => array(
                'label'     => __( 'Export Setup', 'gamipress' ),
                'desc'      => __( 'Choose the stored setup you want to export.', 'gamipress' ),
                'type'      => 'multicheck',
                'classes' => 'gamipress-switch gamipress-all-types-multicheck',
                'options' => $options,
            ),
            'export_setup' => array(
                'label'     => __( 'Export Setup', 'gamipress' ),
                'desc'      => __( 'Export elements setup on this site as a file to easily import those elements to another site.', 'gamipress' ),
                'type'      => 'button',
                'button'    => 'primary',
                'icon'      => 'dashicons-download',
                'action'    => 'export_setup'
            ),
            'import_setup_file' => array(
                'type'          => 'text',
                'attributes'    => array( 'type' => 'file' )
            ),
            'import_setup' => array(
                'label'     => __( 'Import Setup', 'gamipress' ),
                'type'      => 'button',
                'button'    => 'primary',
                'icon'      => 'dashicons-upload',
            ),
        ) ),
        'vertical_tabs' => true,
        'tabs' => apply_filters( 'gamipress_import_export_setup_tool_tabs', array(
            'export_points' => array(
                'icon' => 'dashicons-download',
                'title' => __( 'Export', 'gamipress' ),
                'fields' => array(
                    'export_setup_options',
                    'export_setup',
                ),
            ),
            'import_points' => array(
                'icon' => 'dashicons-upload',
                'title' => __( 'Import', 'gamipress' ),
                'fields' => array(
                    'import_setup_file',
                    'import_setup',
                ),
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_tools_import_export_meta_boxes', 'gamipress_import_export_setup_tool_meta_boxes' );

/**
 * Export Setup action
 *
 * @since 1.7.0
 */
function gamipress_ajax_export_setup_tool() {

    global $wpdb;

    $postmeta = GamiPress()->db->postmeta;

    $items = $_POST['items'];

    // Check parameters received
    if( ! isset( $items ) || empty( $items ) ) {
        wp_send_json_error( __( 'No items selected.', 'gamipress' ) );
    }

    // Check parameters received
    if( ! is_array( $items ) ) {
        wp_send_json_error( __( 'No items selected.', 'gamipress' ) );
    }

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    ignore_user_abort( true );

    if ( ! gamipress_is_function_disabled( 'set_time_limit' ) ) {
        set_time_limit( 0 );
    }

    // Setup vars
    $setup = array();
    $posts_to_export = array();
    $points_types = gamipress_get_points_types();
    $achievement_types = gamipress_get_achievement_types();
    $rank_types = gamipress_get_rank_types();
    $points_types_slugs = gamipress_get_points_types_slugs();
    $achievement_types_slugs = gamipress_get_achievement_types_slugs();
    $rank_types_slugs = gamipress_get_rank_types_slugs();
    $types = array_merge( $points_types, $achievement_types, $rank_types );
    $types_slugs = array_merge( $points_types_slugs, $achievement_types_slugs, $rank_types_slugs );
    $suffixes = array(
        '-points-type',
            '-points-awards',
            '-points-deducts',
        '-achievement-type',
            '-achievements',
                '-steps',
        '-rank-type',
            '-ranks',
                '-rank-requirements',
    );

    // Common arg to query requirements
    $query_args = array(
        'post_status'       => 'any',
        'orderby'			=> 'menu_order',
        'order'				=> 'ASC',
        'posts_per_page'    => -1,
        'suppress_filters'  => false,
    );

    foreach( $items as $item_to_export ) {

        foreach( $suffixes as $suffix ) {

            // If option does not ends with one of suffixes then is not correct
            if( ! gamipress_ends_with( $item_to_export, $suffix ) ) {
                continue;
            }

            // Remove the suffix to get the type
            $type = str_replace( $suffix, '', $item_to_export );

            // Check if is a registered type option
            if( ! in_array( $type, $types_slugs ) ) {
                continue;
            }

            switch( $suffix ) {
                case '-points-type':
                case '-achievement-type':
                case '-rank-type':
                    // Append type to the posts to export array
                    $posts_to_export[] = gamipress_get_post( $types[$type]['ID'] );
                    break;
                case '-points-awards':
                case '-points-deducts':

                    // Remove first "-"
                    $post_type = substr( $suffix, 1 );
                    // Remove last "s"
                    $post_type = rtrim( $post_type, 's' );

                    $posts = get_posts( array_merge( $query_args, array(
                        'post_type'         => $post_type,
                        'post_parent'     	=> $types[$type]['ID'],
                    ) ) );

                    // Bail if not posts found
                    if( empty( $posts ) ) break;

                    // Merge posts to posts to export
                    $posts_to_export = array_merge( $posts_to_export, $posts );

                    break;
                case '-steps':
                case '-rank-requirements':

                    // Remove first "-"
                    $post_type = substr( $suffix, 1 );
                    // Remove last "s"
                    $post_type = rtrim( $post_type, 's' );

                    $parents = get_posts( array_merge( $query_args, array(
                        'post_type' => $type,
                    ) ) );

                    $parents = wp_list_pluck( $parents, 'ID' );

                    // Bail if not parents found
                    if( empty( $parents ) ) break;

                    $posts = get_posts( array_merge( $query_args, array(
                        'post_type'         => $post_type,
                        'post_parent__in'   => $parents,
                    ) ) );

                    // Bail if not posts found
                    if( empty( $posts ) ) break;

                    // Merge posts to posts to export
                    $posts_to_export = array_merge( $posts_to_export, $posts );

                    break;
                case '-achievements':
                case '-ranks':

                    $posts = get_posts( array_merge( $query_args, array(
                        'post_type' => $type,
                    ) ) );

                    // Bail if not posts found
                    if( empty( $posts ) ) break;

                    // Merge posts to posts to export
                    $posts_to_export = array_merge( $posts_to_export, $posts );

                    break;
            }

        }

    }

    foreach( $posts_to_export as $post ) {

        if( $post instanceof WP_Post ) {
            $post = $post->to_array();
        }

        if( is_array( $post ) && isset( $post['ID'] ) && absint( $post['ID'] ) !== 0 ) {

            $post_id = $post['ID'];
            $post['meta'] = array();

            // Get all post metas
            $post_metas = $wpdb->get_results( "SELECT meta_key, meta_value FROM {$postmeta} WHERE post_id={$post_id}" );

            foreach( $post_metas as $meta ) {

                $meta_key = $meta->meta_key;
                $meta_value = $meta->meta_value;

                // Turn thumbnail ID into a thumbnail URL for the import process
                if( $meta_key === '_thumbnail_id' && absint( $meta_value ) !== 0 ) {
                    $meta_value = wp_get_attachment_image_url( $meta_value, 'full' );
                }

                /**
                 * Filter meta value to allow custom processing
                 *
                 * @since 1.7.0
                 *
                 * @param mixed $meta_value
                 * @param string $meta_key
                 * @param array $post
                 *
                 * @return mixed
                 */
                $post['meta'][$meta_key] = apply_filters( 'gamipress_export_setup_tool_post_meta_value', $meta_value, $meta_key, $post );
            }

            /**
             * Filter to append custom data to post to export
             *
             * @since 1.7.0
             *
             * @param array $post
             *
             * @return array
             */
            $post = apply_filters( 'gamipress_export_setup_tool_post', $post );


            $setup[] = $post;
        }
    }

    wp_send_json_success( array(
        'setup_raw' => $setup,
        'setup' => json_encode( $setup ),
        'message' => __( 'Setup export process has been done successfully.', 'gamipress' ),
    ) );

}
add_action( 'wp_ajax_gamipress_export_setup_tool', 'gamipress_ajax_export_setup_tool' );

/**
 * AJAX handler for the import setup tool
 *
 * @since 1.7.0
 */
function gamipress_ajax_import_setup_tool() {

    // Check parameters received
    if( ! isset( $_FILES['file'] ) ) {
        wp_send_json_error( __( 'No setup to import.', 'gamipress' ) );
    }

    $import_file = $_FILES['file']['tmp_name'];

    if( empty( $import_file ) ) {
        wp_send_json_error( __( 'Can not retrieve the file to import, check server file permissions.', 'gamipress' ) );
    }

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    // Retrieve the setup from the file and convert the json object to an array
    $setup = json_decode( file_get_contents( $import_file ), true ) ;

    if( ! is_array( $setup ) || empty( $setup ) ) {
        wp_send_json_error( __( 'Empty setup, so nothing to import.', 'gamipress' ) );
    }

    ignore_user_abort( true );

    if ( ! gamipress_is_function_disabled( 'set_time_limit' ) ) {
        set_time_limit( 0 );
    }

    // Setup vars
    $user_id = get_current_user_id();
    $processed = array();

    // Make a first loop to insert all posts given
    foreach( $setup as $original_post ) {

        // Since post will be modified, make a copy
        $post = $original_post;

        /**
         * Filter post that will being imported
         *
         * @since 1.7.0
         *
         * @param array $post
         *
         * @return array
         */
        $post = apply_filters( 'gamipress_import_setup_tool_post', $post );

        // Remote post ID
        $post_id = $post['ID'];

        $defaults = array(
            'ID'                    => 0,
            'post_author'           => $user_id,
            'post_content'          => '',
            'post_content_filtered' => '',
            'post_title'            => '',
            'post_excerpt'          => '',
            'post_status'           => 'draft',
            'post_type'             => 'post',
            'comment_status'        => '',
            'ping_status'           => '',
            'post_password'         => '',
            'to_ping'               => '',
            'pinged'                => '',
            'post_parent'           => 0,
            'menu_order'            => 0,
            'guid'                  => '',
            'import_id'             => 0,
            'context'               => '',
        );

        $post_data = wp_parse_args( $post, $defaults );

        unset( $post_data['ID'] );

        // Local post ID
        $local_post_id = wp_insert_post( $post_data );

        // Update post ID for following filters
        $post['ID'] = $local_post_id;

        // Add post inserted to processed posts array
        $processed[$post_id] = $local_post_id;

        // Process post metas
        if( isset( $post['meta'] ) && is_array( $post['meta'] ) ) {

            foreach( $post['meta'] as $meta_key => $meta_value ) {

                // Check if thumbnail ID is a external URL and import the external file to this install
                if( $meta_key === '_thumbnail_id' && ! empty( $meta_value ) && ! is_numeric( $meta_value ) ) {

                    $meta_value = gamipress_import_attachment( $meta_value );

                }

                /**
                 * Filter meta value to allow custom processing
                 *
                 * @since 1.7.0
                 *
                 * @param mixed $meta_value
                 * @param string $meta_key
                 * @param array $post
                 *
                 * @return mixed
                 */
                $meta_value = apply_filters( 'gamipress_import_setup_tool_post_meta_value', $meta_value, $meta_key, $post );

                // Skip meta if there is any error
                if( is_wp_error( $meta_value ) ) continue;

                $meta_value = maybe_unserialize( $meta_value );

                update_post_meta( $post['ID'], $meta_key, $meta_value, true );
            }

        }

    }

    // Second loop to update external post parents to local posts
    foreach( $setup as $post ) {

        // Bail if this post hasn't been processed
        if( ! isset( $processed[$post['ID']] ) ) continue;

        // Check if post parent have been processed too
        if( isset( $post['post_parent'] ) && absint( $post['post_parent'] ) !== 0 && isset( $processed[$post['post_parent']] ) ) {
            // Update post parent
            wp_update_post( array(
                'ID' => $processed[$post['ID']],
                'post_parent' => $processed[$post['post_parent']],
            ) );
        }
    }

    // Flush the GamiPress cache
    gamipress_flush_cache();

    // Return a success message
    wp_send_json_success( __( 'Setup has been imported successfully.', 'gamipress' ) );

}
add_action( 'wp_ajax_gamipress_import_setup_tool', 'gamipress_ajax_import_setup_tool' );