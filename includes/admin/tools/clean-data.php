<?php
/**
 * Clean Data Tool
 *
 * @package     GamiPress\Admin\Tools\Clean_Data
 * @since       1.1.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Clean Data Tool meta boxes
 *
 * @since  1.1.7
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_clean_data_tool_meta_boxes( $meta_boxes ) {

    $meta_boxes['clean-data'] = array(
        'title' => __( 'Clean Data', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_clean_data_tool_fields', array(
            'clean_data_actions' => array(
                'desc' => __( 'Occasionally, as a consequence of the deletion of data, there may be entries that are not related to any other, like a step without an achievement.', 'gamipress' )
                    . '<br>' . __( 'This tool will search this data to completely remove it from your server.', 'gamipress' ),
                'type' => 'multi_buttons',
                'buttons' => array(
                    'search_data_to_clean' => array(
                        'label' => __( 'Search data to clean', 'gamipress' ),
                    ),
                    'clean_data' => array(
                        'label' => __( 'Proceed with the cleanup', 'gamipress' ),
                        'button' => 'primary'
                    )
                ),
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_tools_general_meta_boxes', 'gamipress_clean_data_tool_meta_boxes' );

/**
 * AJAX handler for the search data to clean action
 *
 * @since 1.1.5
 */
function gamipress_ajax_search_data_to_clean() {

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    $requirements = gamipress_get_unassigned_requirements();
    $found_results = count( $requirements );
    $data = array(
        'found_results' => $found_results,
        'message' => ''
    );

    // Return a success message
    if( $requirements && $found_results > 0 ) {
        $data['message'] = sprintf( _n( '%s entry found.', '%s entries found.', $found_results, 'gamipress' ), $found_results );
    } else {
        $data['message'] = __( 'No data to clean.', 'gamipress' );
    }

    $data['message'] = '<span>' . $data['message'] . '</span>';

    wp_send_json_success( $data );
}
add_action( 'wp_ajax_gamipress_search_data_to_clean', 'gamipress_ajax_search_data_to_clean' );

/**
 * AJAX handler for the clean data tool
 *
 * @since 1.1.5
 */
function gamipress_ajax_clean_data_tool() {

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    $requirements = gamipress_get_unassigned_requirements();

    foreach( $requirements as $requirement ) {
        wp_delete_post( $requirement['ID'] );
    }

    // Return a success message
    wp_send_json_success( __( 'Cleanup process has been done successfully.', 'gamipress' ) );
}
add_action( 'wp_ajax_gamipress_clean_data_tool', 'gamipress_ajax_clean_data_tool' );