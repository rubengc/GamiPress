<?php
/**
 * Admin Licenses Page
 *
 * @package     GamiPress\Admin\Licenses
 * @since       1.3.9.3
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Licenses Settings meta boxes
 *
 * @since   1.1.1
 * @updated 1.3.9.3 Filter changed to gamipress_licenses_meta_boxes
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_licenses_meta_boxes( $meta_boxes ) {

    // Get our add-ons
    $plugins = gamipress_plugins_api();

    // Loop settings section meta boxes
    foreach( $meta_boxes as $meta_box_id => $meta_box ) {

        // Only add settings meta box if has fields
        if( isset( $meta_box['fields'] ) && ! empty( $meta_box['fields'] ) ) {

            // Loop meta box fields
            foreach( $meta_box['fields'] as $field_id => $field ) {

                // Update edd_license fields with default parameters to the GamiPress server
                if( $field['type'] === 'edd_license' ) {

                    // if not server provider, then add GamiPress server
                    if( ! isset( $field['server'] ) ) {
                        $field['server'] = 'https://gamipress.com/edd-sl-api';
                    }

                    // Check if is a GamiPress hosted plugin
                    if( $field['server'] === 'https://gamipress.com/edd-sl-api' ) {

                        // Renew link
                        $field['renew_license_link'] = 'https://gamipress.com/renew-a-license';

                        // Before field row hook to render some extra information
                        $field['before_row'] = 'gamipress_license_field_before';

                        // Try to find the plugin thumbnail from plugins API
                        if ( ! is_wp_error( $plugins )
                            && isset( $field['file'] )
                            && ! isset( $field['thumbnail'] ) ) {

                            foreach ( $plugins as $plugin ) {

                                $slug = basename( $field['file'], '.php' );

                                if( $slug === $plugin->info->slug ) {
                                    $field['thumbnail'] = $plugin->info->thumbnail;

                                    // Thumbnail found so exit loop
                                    break;
                                }

                            }

                        }

                    }

                    // Update the field definition
                    $meta_boxes[$meta_box_id]['fields'][$field_id] = $field;
                }
            }

        }

    }

    return $meta_boxes;

}
add_filter( 'gamipress_licenses_meta_boxes', 'gamipress_licenses_meta_boxes', 9999 );

/**
 * Register licenses page.
 *
 * @since  1.3.9.3
 *
 * @return void
 */
function gamipress_register_licenses_page() {

    $tabs = array();
    $boxes = array();

    $meta_boxes = array();

    // TODO: Keep for backward compatibility with gamipress_settings_licenses_meta_boxes filter
    $meta_boxes = apply_filters( "gamipress_settings_licenses_meta_boxes", $meta_boxes );

    /**
     * Filter: gamipress_licenses_{$section_id}_meta_boxes
     *
     * @param array $meta_boxes
     *
     * @return array
     */
    $meta_boxes = apply_filters( "gamipress_licenses_meta_boxes", $meta_boxes );

    if( ! empty( $meta_boxes ) ) {

        // Loop licenses section meta boxes
        foreach( $meta_boxes as $meta_box_id => $meta_box ) {

            // Check meta box tabs
            if( isset( $meta_box['tabs'] ) && ! empty( $meta_box['tabs'] ) ) {

                // Loop meta box tabs
                foreach( $meta_box['tabs'] as $tab_id => $tab ) {

                    $tab['id'] = $tab_id;

                    $meta_box['tabs'][$tab_id] = $tab;

                }

            }

            // Only add licenses meta box if has fields
            if( isset( $meta_box['fields'] ) && ! empty( $meta_box['fields'] ) ) {

                // Loop meta box fields
                foreach( $meta_box['fields'] as $field_id => $field ) {

                    $field['id'] = $field_id;

                    // Support for group fields
                    if( isset( $field['fields'] ) && is_array( $field['fields'] ) ) {

                        foreach( $field['fields'] as $group_field_id => $group_field ) {

                            $field['fields'][$group_field_id]['id'] = $group_field_id;

                        }

                    }

                    $meta_box['fields'][$field_id] = $field;

                }

                $meta_box['id'] = $meta_box_id;

                $meta_box['display_cb'] = false;
                $meta_box['admin_menu_hook'] = false;

                $meta_box['show_on'] = array(
                    'key'   => 'options-page',
                    'value' => array( 'gamipress_licenses' ),
                );

                $box = new_cmb2_box( $meta_box );

                $box->object_type( 'options-page' );

                $boxes[] = $box;

            }
        }
    }

    // Create the options page
    new Cmb2_Metatabs_Options( array(
        'key'      => 'gamipress_settings',
        'class'    => 'gamipress-page',
        'title'    => __( 'Licenses', 'gamipress' ),
        'topmenu'  => 'gamipress',
        'view_capability' => gamipress_get_manager_capability(),
        'cols'     => 1,
        'boxes'    => $boxes,
        //'tabs'     => $tabs,
        'menuargs' => array(
            'menu_title' => __( 'Licenses', 'gamipress' ),
            'menu_slug'  => 'gamipress_licenses',
        ),
        'savetxt' => __( 'Save Changes', 'gamipress' ),
        'resettxt' => false,
    ) );

}
add_action( 'cmb2_admin_init', 'gamipress_register_licenses_page' );

/**
 * License field thumbnail.
 *
 * @since  1.1.1
 *
 * @param  array        $field_args Current field args
 * @param  CMB2_Field   $field      Current field object
 */
function gamipress_license_field_before( $field_args, $field ) {

    if( isset( $field_args['thumbnail'] ) && ! empty( $field_args['thumbnail'] ) ) : ?>

        <div class="gamipress-license-thumbnail">
            <img src="<?php echo $field_args['thumbnail']; ?>" alt="<?php echo $field_args['item_name']; ?>">
        </div>

    <?php endif;

}
