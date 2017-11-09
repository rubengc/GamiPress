<?php
/**
 * Reset Data Tool
 *
 * @package     GamiPress\Admin\Tools\Reset_Data
 * @since       1.1.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Reset Data Tool meta boxes
 *
 * @since  1.1.7
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_reset_data_tool_meta_boxes( $meta_boxes ) {

    $meta_boxes['reset-data'] = array(
        'title' => __( 'Reset Data', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_reset_data_tool_fields', array(
            'data_to_reset' => array(
                'desc' => __( 'Choose the stored data you want to reset.', 'gamipress' ),
                'type' => 'multicheck',
                'classes' => 'gamipress-switch',
                'options' => array(
                    'achievement_types' => __( 'Achievement Types', 'gamipress' ),
                    'achievements' => __( 'Achievements', 'gamipress' ),
                    'steps' => __( 'Steps', 'gamipress' ),
                    'points_types' => __( 'Points Types', 'gamipress' ),
                    'points_awards' => __( 'Points Awards', 'gamipress' ),
                    'rank_types' => __( 'Rank Types', 'gamipress' ),
                    'ranks' => __( 'Ranks', 'gamipress' ),
                    'rank_requirements' => __( 'Rank Requirements', 'gamipress' ),
                    'logs' => __( 'Logs', 'gamipress' ),
                ),
            ),
            'reset_data' => array(
                'label' => __( 'Reset Data', 'gamipress' ),
                'desc' => __( '<strong>Important!</strong> Just use this tool on a test environment or if really you know what are you doing.', 'gamipress' ),
                'type' => 'button',
                'button' => 'primary'
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_tools_general_meta_boxes', 'gamipress_reset_data_tool_meta_boxes', 9999 );

/**
 * GamiPress after tools page
 *
 * @since 1.1.7
 *
 * @param string $content   Content to be filtered
 *
 * @return mixed string $host if detected, false otherwise
 */
function gamipress_reset_data_tool_page_bottom( $content ) {

    ob_start();
    ?>
    <!-- Reset Data Dialog -->
    <div id="reset-data-dialog" title="Are you sure you want to delete this data?" style="display: none;">
        <span><?php _e( 'This action will delete all of the entries you selected. It will be completely <strong>irrecoverable</strong>.', 'gamipress' ); ?></span>
        <br><br>
        <span><?php _e( 'Please, confirm the data that will be removed:', 'gamipress' ); ?></span>
        <div id="reset-data-reminder"></div>
    </div>
    <?php
    $content .= ob_get_clean();

    return $content;
}
add_filter( 'gamipress_tools_page_bottom', 'gamipress_reset_data_tool_page_bottom' );

/**
 * AJAX handler for the reset data tool
 *
 * @since 1.1.5
 */
function gamipress_ajax_reset_data_tool() {

    // Check parameters received
    if( ! isset( $_POST['items'] ) || empty( $_POST['items'] ) ) {
        wp_send_json_error( __( 'No items selected.', 'gamipress' ) );
    }

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    global $wpdb;

    foreach( $_POST['items'] as $item ) {

        switch( $item ) {
            case 'achievement_types':

                $wpdb->delete( $wpdb->posts, array(
                    'post_type' => 'achievement-type'
                ) );

                break;
            case 'achievements':

                $wpdb->delete( $wpdb->posts, array(
                    'post_type' => gamipress_get_achievement_types_slugs()
                ) );

                break;
            case 'steps':

                $wpdb->delete( $wpdb->posts, array(
                    'post_type' => 'step'
                ) );

                break;
            case 'points_types':

                $wpdb->delete( $wpdb->posts, array(
                    'post_type' => 'points-type'
                ) );

                break;
            case 'points_awards':

                $wpdb->delete( $wpdb->posts, array(
                    'post_type' => 'points-award'
                ) );

                break;
            case 'rank_types':

                $wpdb->delete( $wpdb->posts, array(
                    'post_type' => 'rank-type'
                ) );

                break;
            case 'ranks':

                $wpdb->delete( $wpdb->posts, array(
                    'post_type' => gamipress_get_rank_types_slugs()
                ) );

                break;
            case 'rank_requirements':

                $wpdb->delete( $wpdb->posts, array(
                    'post_type' => 'rank-requirement'
                ) );

                break;
            case 'logs':

                if( is_gamipress_upgraded_to( '1.2.8' ) ) {
                    $ct_table = ct_setup_table( 'gamipress_logs' );

                    // Reset from gamipress_logs table
                    $wpdb->delete( $ct_table->db->table_name, array(
                        '1' => 1
                    ) );

                } else {

                    // Reset from old gamipress-log CPT
                    $wpdb->delete( $wpdb->posts, array(
                        'post_type' => 'gamipress-log'
                    ) );

                }
                break;
            default:
                do_action( 'gamipress_reset_data_tool_reset', $item );
                break;
        }

    }

    // Return a success message
    wp_send_json_success( __( 'Data has been reset successfully.', 'gamipress' ) );
}
add_action( 'wp_ajax_gamipress_reset_data_tool', 'gamipress_ajax_reset_data_tool' );