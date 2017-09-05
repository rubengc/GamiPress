<?php
/**
 * Admin Plugins
 *
 * @package     GamiPress\Admin\Plugins
 * @since       1.1.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Plugins row action links
 *
 * @since 1.1.0
 *
 * @param array     $links  Array of plugin action links
 * @param string    $file   Plugin file
 *
 * @return array    $links
 */
function gamipress_plugin_action_links( $links, $file ) {
    if ( $file != 'gamipress/gamipress.php' )
        return $links;

    $settings_link = '<a href="' . admin_url( 'admin.php?page=gamipress_settings' ) . '">' . esc_html__( 'Settings', 'gamipress' ) . '</a>';

    array_unshift( $links, $settings_link );

    return $links;
}
add_filter( 'plugin_action_links', 'gamipress_plugin_action_links', 10, 2 );


/**
 * Plugin row meta links
 *
 * @since 1.1.0
 *
 * @param array     $input  Array of plugin meta links
 * @param string    $file   Plugin file
 *
 * @return array    $input
 */
function gamipress_plugin_row_meta( $input, $file ) {
    if ( $file != 'gamipress/gamipress.php' )
        return $input;

    $link = esc_url( add_query_arg( array(
            'utm_source'   => 'plugins-page',
            'utm_medium'   => 'plugin-row',
            'utm_campaign' => 'admin',
        ), 'https://gamipress.com/add-ons/' )
    );

    $links = array(
        '<a href="' . $link . '">' . esc_html__( 'Add-ons', 'gamipress' ) . '</a>',
    );

    $input = array_merge( $input, $links );

    return $input;
}
add_filter( 'plugin_row_meta', 'gamipress_plugin_row_meta', 10, 2 );
