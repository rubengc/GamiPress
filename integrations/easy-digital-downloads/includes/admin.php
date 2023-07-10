<?php
/**
 * Admin
 *
 * @package GamiPress\Easy_Digital_Downloads\Admin
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_EDD_DIR . 'includes/admin/recount-activity.php';

function gamipress_edd_meta_boxes() {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_edd_';

    // Grab our points types as an array
    $points_types_options = array(
        '' => 'Default'
    );

    foreach( gamipress_get_points_types() as $slug => $data ) {
        $points_types_options[$slug] = $data['plural_name'];
    }

    // Download Points
    new_cmb2_box(array(
        'id'           	=> 'gamipress-edd-download-points',
        'title'        	=> __( 'Award points', 'gamipress' ),
        'object_types' 	=> array( 'download' ),
        'context'      	=> 'side',
        'priority'     	=> 'default',
        'fields' 		=> apply_filters( 'gamipress_edd_download_points_defaults_meta_box_fields', array(
            array(
                'name' 	=> __( 'Award points to users that purchase this download?', 'gamipress' ),
                'desc' 	=> '',
                'id'   	=> $prefix . 'award_points',
                'type' 	=> 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            array(
                'name' => __( 'Points', 'gamipress' ),
                'desc' => ' '.__( 'Points awarded for purchasing this download.', 'gamipress' ),
                'id'   => $prefix . 'points',
                'type' => 'text_small',
            ),
            array(
                'name' => __( 'Points Type', 'gamipress' ),
                'desc' => ' '.__( 'Points type to award for purchasing this download.', 'gamipress' ),
                'id'   => $prefix . 'points_type',
                'type' => 'select',
                'options' => $points_types_options
            ),
        ), $prefix )
    ));

}
add_action( 'cmb2_admin_init', 'gamipress_edd_meta_boxes' );

/**
 * Easy Digital Downloads automatic updates
 *
 * @since  1.0.1
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_edd_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress'] = __( 'Easy Digital Downloads integration', 'gamipress' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_edd_automatic_updates' );