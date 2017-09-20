<?php
/**
 * Admin Tools Page
 *
 * @package     GamiPress\Admin\Tools
 * @since       1.1.5
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// GamiPress Tools
require_once GAMIPRESS_DIR . 'includes/admin/tools/clean-data.php';
require_once GAMIPRESS_DIR . 'includes/admin/tools/reset-data.php';
require_once GAMIPRESS_DIR . 'includes/admin/tools/import-export-settings.php';
require_once GAMIPRESS_DIR . 'includes/admin/tools/system-info.php';

/**
 * Register tools page.
 *
 * @since  1.1.5
 *
 * @return void
 */
function gamipress_register_tools_page() {

    $tabs = array();
    $boxes = array();

    // Loop tools sections
    foreach( gamipress_get_tools_sections() as $section_id => $section ) {

        $meta_boxes = array();

        /**
         * Filter: gamipress_tools_{$section_id}_meta_boxes
         *
         * @param array $meta_boxes
         *
         * @return array
         */
        $meta_boxes = apply_filters( "gamipress_tools_{$section_id}_meta_boxes", $meta_boxes );

        if( ! empty( $meta_boxes ) ) {

            // Loop tools section meta boxes
            foreach( $meta_boxes as $meta_box_id => $meta_box ) {

                // Only add tools meta box if has fields
                if( isset( $meta_box['fields'] ) && ! empty( $meta_box['fields'] ) ) {

                    // Loop meta box fields
                    foreach( $meta_box['fields'] as $field_id => $field ) {

                        $field['id'] = $field_id;

                        $meta_box['fields'][$field_id] = $field;

                    }

                    $meta_box['id'] = $meta_box_id;

                    $meta_box['display_cb'] = false;
                    $meta_box['admin_menu_hook'] = false;

                    $meta_box['show_on'] = array(
                        'key'   => 'options-page',
                        'value' => array( 'gamipress_tools' ),
                    );

                    $box = new_cmb2_box( $meta_box );

                    $box->object_type( 'options-page' );

                    $boxes[] = $box;

                }
            }

            $tabs[] = array(
                'id'    => $section_id,
                'title' => ( ( isset( $section['icon'] ) ) ? '<i class="dashicons ' . $section['icon'] . '"></i>' : '' ) . $section['title'],
                'desc'  => '',
                'boxes' => array_keys( $meta_boxes ),
            );
        }
    }

    // Create the options page
    new Cmb2_Metatabs_Options( array(
        'key'      => 'gamipress_tools',
        'class'    => 'gamipress-page',
        'title'    => __( 'Tools', 'gamipress' ),
        'topmenu'  => 'gamipress',
        'view_capability' => gamipress_get_manager_capability(),
        'cols'     => 1,
        'boxes'    => $boxes,
        'tabs'     => $tabs,
        'menuargs' => array(
            'menu_title' => __( 'Tools', 'gamipress' ),
        ),
        'savetxt' => false,
        'resettxt' => false,
    ) );

}
add_action( 'cmb2_admin_init', 'gamipress_register_tools_page', 10 );

/**
 * GamiPress registered tools sections
 *
 * @since  1.1.5
 *
 * @return array
 */
function gamipress_get_tools_sections() {

    $gamipress_tools_sections = array(
        'general' => array(
            'title' => __( 'General', 'gamipress' ),
            'icon' => 'dashicons-admin-tools',
        ),
        'import_export' => array(
            'title' => __( 'Import/Export', 'gamipress' ),
            'icon' => 'dashicons-upload',
        ),
        'system' => array(
            'title' => __( 'System', 'gamipress' ),
            'icon' => 'dashicons-performance',
        ),
    );

    return apply_filters( 'gamipress_tools_sections', $gamipress_tools_sections );

}

/**
 * GamiPress Tools Page bottom
 *
 * @since 1.1.5
 *
 * @param string $content   Content to be filtered
 * @param string $page      Current page slug
 *
 * @return mixed string $host if detected, false otherwise
 */
function gamipress_tools_page_bottom( $content, $page ) {

    if( $page !== 'gamipress_tools' ) {
        return $content;
    }

    $content = apply_filters( 'gamipress_tools_page_bottom', $content );

    return $content;
}
add_filter( 'cmb2metatabs_after_form', 'gamipress_tools_page_bottom', 10, 2 );