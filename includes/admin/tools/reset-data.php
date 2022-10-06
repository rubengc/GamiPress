<?php
/**
 * Reset Data Tool
 *
 * @package     GamiPress\Admin\Tools\Reset_Data
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
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
        'title' => gamipress_dashicon( 'trash' ) . __( 'Reset Data', 'gamipress' ),
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
                    'points_deducts' => __( 'Points Deductions', 'gamipress' ),
                    'rank_types' => __( 'Rank Types', 'gamipress' ),
                    'ranks' => __( 'Ranks', 'gamipress' ),
                    'rank_requirements' => __( 'Rank Requirements', 'gamipress' ),
                    'logs' => __( 'Logs', 'gamipress' ),
                    'earnings' => __( 'Users Earnings', 'gamipress' ),
                    'earned_points' => __( 'Users Earned Points', 'gamipress' ),
                    'earned_achievements' => __( 'Users Earned Achievements', 'gamipress' ),
                    'earned_ranks' => __( 'Users Earned Ranks', 'gamipress' ),
                ),
            ),
            'reset_data' => array(
                'label' => __( 'Reset Data', 'gamipress' ),
                'desc' => '<span style="color: color: #a00;">' . __( '<strong>Important!</strong> Just use this tool on a test environment or if really you know what are you doing.', 'gamipress' ) . '</span>',
                'type' => 'button',
                'button' => 'danger gamipress-button-danger'
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
 * @since   1.1.5
 * @updated 1.4.0 Added user earnings reset
 */
function gamipress_ajax_reset_data_tool() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    // Check parameters received
    if( ! isset( $_POST['items'] ) || empty( $_POST['items'] ) ) {
        wp_send_json_error( __( 'No items selected.', 'gamipress' ) );
    }

    ignore_user_abort( true );

    if ( ! gamipress_is_function_disabled( 'set_time_limit' ) ) {
        set_time_limit( 0 );
    }

    global $wpdb;

    // Setup db table names
    $posts          = GamiPress()->db->posts;
    $user_earnings  = GamiPress()->db->user_earnings;

    // Setup vars
    $achievement_types = gamipress_get_achievement_types();
    $points_types = gamipress_get_points_types();
    $rank_types = gamipress_get_rank_types();

    foreach( $_POST['items'] as $item ) {

        switch( $item ) {
            case 'achievement_types':

                $wpdb->delete( $posts, array(
                    'post_type' => 'achievement-type'
                ) );

                break;
            case 'achievements':

                $wpdb->delete( $posts, array(
                    'post_type' => array_keys( $achievement_types )
                ) );

                break;
            case 'steps':

                $wpdb->delete( $posts, array(
                    'post_type' => 'step'
                ) );

                break;
            case 'points_types':

                $wpdb->delete( $posts, array(
                    'post_type' => 'points-type'
                ) );

                break;
            case 'points_awards':

                $wpdb->delete( $posts, array(
                    'post_type' => 'points-award'
                ) );

                break;
            case 'points_deducts':

                $wpdb->delete( $posts, array(
                    'post_type' => 'points-deduct'
                ) );

                break;
            case 'rank_types':

                $wpdb->delete( $posts, array(
                    'post_type' => 'rank-type'
                ) );

                break;
            case 'ranks':

                $wpdb->delete( $posts, array(
                    'post_type' => array_keys( $rank_types )
                ) );

                break;
            case 'rank_requirements':

                $wpdb->delete( $posts, array(
                    'post_type' => 'rank-requirement'
                ) );

                break;
            case 'logs':

                $logs 		= GamiPress()->db->logs;
                $logs_meta 	= GamiPress()->db->logs_meta;

                // Reset from gamipress_logs table
                $wpdb->query( "DELETE FROM {$logs} WHERE 1=1" );

                // Reset from gamipress_logs_meta table
                $wpdb->query( "DELETE FROM {$logs_meta} WHERE 1=1" );

                break;
            case 'earnings':
            case 'earned_points':
            case 'earned_achievements':
            case 'earned_ranks':
                break;
            default:
                do_action( 'gamipress_reset_data_tool_reset', $item );
                break;
        }

        // User earnings
        if(
            in_array( 'earnings', $_POST['items'] )
            || in_array( 'earned_points', $_POST['items'] )
            || in_array( 'earned_achievements', $_POST['items'] )
            || in_array( 'earned_ranks', $_POST['items'] )
        ) {

            // For points and ranks, we need to reset all users meta
            if( in_array( 'earned_points', $_POST['items'] ) || in_array( 'earned_ranks', $_POST['items'] ) ) {

                // Get all stored users
                $users = $wpdb->get_results( "SELECT {$wpdb->users}.ID FROM {$wpdb->users}" );

                foreach( $users as $user ) {

                    // Reset user earned points
                    if( in_array( 'earned_points', $_POST['items'] ) ) {

                        foreach( $points_types as $points_type => $points_type_data ) {
                            // Reset user's points total
                            gamipress_update_user_meta( $user->ID, "_gamipress_{$points_type}_points", 0 );
                        }

                    }

                    // Reset user earned ranks
                    if( in_array( 'earned_ranks', $_POST['items'] ) ) {

                        foreach( $rank_types as $rank_type => $rank_type_data ) {
                            // Reset user's ranks
                            gamipress_delete_user_meta( $user->ID, "_gamipress_{$rank_type}_rank" );
                        }

                    }
                }

            }

            if( in_array( 'earned_points', $_POST['items'] ) ) {
                // Reset all points awards and deducts gamipress_user_earnings table
                $wpdb->query( "DELETE FROM {$user_earnings} WHERE post_type IN ( 'points-award', 'points-deduct' )" );

            }

            if( in_array( 'earned_achievements', $_POST['items'] ) ) {
                // Reset all achievements and steps from gamipress_user_earnings table
                $wpdb->query( "DELETE FROM {$user_earnings} WHERE post_type IN ( 'step', '" . implode( "', '", array_keys( $achievement_types ) ) . "' )" );

            }

            if( in_array( 'earned_ranks', $_POST['items'] ) ) {
                // Reset all ranks and rank requirements from gamipress_user_earnings table
                $wpdb->query( "DELETE FROM {$user_earnings} WHERE post_type IN ( 'rank-requirement', '" . implode( "', '", array_keys( $rank_types ) ) . "' )" );

            }

            if( in_array( 'earnings', $_POST['items'] ) ) {
                // Reset all the gamipress_user_earnings table
                $wpdb->query( "DELETE FROM {$user_earnings} WHERE 1=1" );
            }
        }

    }

    // Flush the GamiPress cache
    gamipress_flush_cache();

    // Return a success message
    wp_send_json_success( __( 'Data has been reset successfully.', 'gamipress' ) );
}
add_action( 'wp_ajax_gamipress_reset_data_tool', 'gamipress_ajax_reset_data_tool' );