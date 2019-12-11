<?php
/**
 * 1.4.7 Upgrades
 *
 * @package     GamiPress\Admin\Upgrades\1.4.7
 * @since       1.4.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return 1.4.7 as last required upgrade
 *
 * @return string
 */
function gamipress_147_is_last_required_upgrade() {

    return '1.4.7';

}
add_filter( 'gamipress_get_last_required_upgrade', 'gamipress_147_is_last_required_upgrade', 147 );

/**
 * Process 1.4.7 upgrades
 *
 * @param string $stored_version
 *
 * @return string
 */
function gamipress_147_upgrades( $stored_version ) {

    // Already upgrade
    if ( version_compare( $stored_version, '1.4.7', '>=' ) ) {
        return $stored_version;
    }

    // Prevent run upgrade until database tables are created
    if( ! gamipress_database_table_has_column( GamiPress()->db->logs, 'trigger_type' ) ) {
        return $stored_version;
    }

    // Check if there is something to migrate
    $upgrade_size = gamipress_147_upgrade_size();

    if( $upgrade_size === 0 ) {

        // There is nothing to update, so upgrade
        $stored_version = '1.4.7';

    } else if( is_gamipress_upgrade_completed( 'update_logs_trigger_type' ) ) {

        // Migrations are finished, so upgrade
        $stored_version = '1.4.7';

    }

    return $stored_version;

}
add_filter( 'gamipress_process_upgrades', 'gamipress_147_upgrades', 147 );

/**
 * 1.4.7 upgrades notices
 */
function gamipress_147_upgrades_notices() {

    // Bail if GamiPress is active network wide and we are not in main site
    if( gamipress_is_network_wide_active() && ! is_main_site() ) {
        return;
    }

    // Check user permissions
    if ( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    // Already upgraded!
    if( is_gamipress_upgraded_to( '1.4.7' ) ) {
        return;
    }

    $running_upgrade = get_option( 'gamipress_running_upgrade' );

    // Other upgrade already running
    if( $running_upgrade && $running_upgrade !== '1.4.7' ) {
        return;
    }

    if( ! is_gamipress_upgrade_completed( 'update_logs_trigger_type' ) ) :
        ?>
        <div id="gamipress-upgrade-notice" class="updated">

            <?php if( $running_upgrade === '1.4.7' ) : ?>

                <p>
                    <?php _e( 'Upgrading GamiPress database...', 'gamipress' ); ?>
                </p>
                <div class="gamipress-upgrade-progress" data-running-upgrade="1.4.7">
                    <div class="gamipress-upgrade-progress-bar" style="width: 0%;"></div>
                </div>

            <?php else : ?>

                <p>
                    <?php _e( 'GamiPress needs to upgrade the database. <strong>Please backup your database before starting this upgrade.</strong> This upgrade routine will be making changes to the database that are not reversible.', 'gamipress' ); ?>
                </p>
                <p>
                    <a href="javascript:void(0);" onClick="jQuery(this).parent().next('p').slideToggle();" class="button"><?php _e( 'Learn more about this upgrade', 'gamipress' ); ?></a>
                    <a href="javascript:void(0);" onClick="gamipress_start_upgrade('1.4.7')" class="button button-primary"><?php _e( 'Start the upgrade', 'gamipress' ); ?></a>
                </p>
                <p style="display: none;">
                    <?php _e( '<strong>About this upgrade:</strong><br />This is a <strong><em>mandatory</em></strong> update that will update all logs with new database table fields. This upgrade provides better performance and scalability.', 'gamipress' ); ?>
                </p>

            <?php endif; ?>

        </div>
        <?php
    endif;

}
add_action( 'admin_notices', 'gamipress_147_upgrades_notices' );

/**
 * Return the number of entries to upgrade
 *
 * @return int
 */
function gamipress_147_upgrade_size() {

    global $wpdb;

    $upgrade_size = 0;

    // Retrieve the count of users earnings to upgrade
    if( ! is_gamipress_upgrade_completed( 'update_logs_trigger_type' ) && gamipress_database_table_has_column( GamiPress()->db->logs, 'trigger_type' ) ) {

        $logs = GamiPress()->db->logs;

        $logs_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$logs} AS l WHERE l.trigger_type = ''" );

        $upgrade_size += absint( $logs_count );

    }

    return $upgrade_size;

}

/**
 * Ajax function to meet the upgrade size
 */
function gamipress_ajax_147_upgrade_info() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Already upgraded
    if ( is_gamipress_upgraded_to( '1.4.7' ) ) {
        wp_send_json_success( array( 'upgraded' => true ) );
    }

    $upgrade_size = gamipress_147_upgrade_size();

    wp_send_json_success( array( 'total' => $upgrade_size ) );
}
add_action( 'wp_ajax_gamipress_147_upgrade_info', 'gamipress_ajax_147_upgrade_info' );

/**
 * Ajax process of 1.4.7 upgrades
 */
function gamipress_ajax_process_147_upgrade() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    global $wpdb;

    // Already upgraded
    if ( is_gamipress_upgraded_to( '1.4.7' ) ) {
        wp_send_json_success( array( 'upgraded' => true ) );
    }

    ignore_user_abort( true );
    set_time_limit( 0 );

    // Add option to meet that upgrade process has been started
    update_option( 'gamipress_running_upgrade', '1.4.7' );

    $current = isset( $_REQUEST['current'] ) ? absint( $_REQUEST['current'] ) : 0;

    // ----------------------------
    // User earnings update
    // ----------------------------
    if( ! is_gamipress_upgrade_completed( 'update_logs_trigger_type' ) ) {

        // Migrate from log meta _gamipress_trigger_type to trigger field
        $ct_table = ct_setup_table( 'gamipress_logs' );

        $limit = 100;

        $logs = GamiPress()->db->logs;

        // Retrieve all user earnings without title
        $results = $wpdb->get_results( "SELECT l.log_id, l.type FROM {$logs} AS l WHERE l.trigger_type = '' LIMIT {$limit}" );

        foreach( $results as $log ) {

            // Setup the trigger value based on the log type
            switch( $log->type ) {
                case 'event_trigger':
                    $trigger_type = ct_get_object_meta( $log->log_id, '_gamipress_trigger_type', true );

                    if( empty( $trigger_type ) ) {
                        $trigger_type = __( '(no trigger)', 'gamipress' );
                    }
                    break;
                case 'achievement_earn':
                    $trigger_type = 'gamipress_unlock_achievement';
                    break;
                case 'achievement_award':
                    $trigger_type = 'gamipress_award_achievement';
                    break;
                case 'points_earn':
                    $trigger_type = 'gamipress_earn_points';
                    break;
                case 'points_deduct':
                    $trigger_type = 'gamipress_deduct_points';
                    break;
                case 'points_award':
                    $trigger_type = 'gamipress_award_points';
                    break;
                case 'points_revoke':
                    $trigger_type = 'gamipress_revoke_points';
                    break;
                case 'rank_earn':
                    $trigger_type = 'gamipress_unlock_rank';
                    break;
                case 'rank_award':
                    $trigger_type = 'gamipress_award_rank';
                    break;
                default:
                    $trigger_type = __( '(no trigger)', 'gamipress' );
                    break;
            }

            $ct_table->db->update(
                array( 'trigger_type' => $trigger_type ),
                array( 'log_id' => $log->log_id )
            );

            $current++;
        }

        $logs_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$logs} AS l WHERE l.trigger_type = ''" );

        if( absint( $logs_count ) === 0 ) {
            gamipress_set_upgrade_complete( 'update_logs_trigger_type' );
        }

    }

    // Successfully upgraded
    if( is_gamipress_upgrade_completed( 'update_logs_trigger_type' ) ) {

        // Remove option to meet that upgrade process has been finished
        delete_option( 'gamipress_running_upgrade' );

        // Updated stored version
        update_option( 'gamipress_version', '1.4.7' );

        wp_send_json_success( array( 'upgraded' => true ) );
    }

    wp_send_json_success( array( 'current' => $current ) );

}
add_action( 'wp_ajax_gamipress_process_147_upgrade', 'gamipress_ajax_process_147_upgrade' );

function gamipress_ajax_stop_process_147_upgrade() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    $running_upgrade = get_option( 'gamipress_running_upgrade' );

    // Check if is out upgrade
    if( $running_upgrade === '1.4.7' ) {
        delete_option( 'gamipress_running_upgrade' );
    }

    wp_send_json_success();
}
add_action( 'wp_ajax_gamipress_stop_process_147_upgrade', 'gamipress_ajax_stop_process_147_upgrade' );