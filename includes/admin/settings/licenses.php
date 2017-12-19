<?php
/**
 * Admin Licenses Settings
 *
 * @package     GamiPress\Admin\Settings\Licenses
 * @since       1.3.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Licenses Settings meta boxes
 *
 * @since  1.1.1
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_settings_licenses_meta_boxes( $meta_boxes ) {

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
add_filter( 'gamipress_settings_licenses_meta_boxes', 'gamipress_settings_licenses_meta_boxes', 9999 );