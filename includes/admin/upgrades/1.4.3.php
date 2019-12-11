<?php
/**
 * 1.4.3 Upgrades
 *
 * @package     GamiPress\Admin\Upgrades\1.4.3
 * @since       1.4.3
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return 1.4.3 as last required upgrade
 *
 * @return string
 */
function gamipress_143_is_last_required_upgrade() {

    return '1.4.3';

}
add_filter( 'gamipress_get_last_required_upgrade', 'gamipress_143_is_last_required_upgrade', 143 );

/**
 * Process 1.4.3 upgrades
 *
 * @param string $stored_version
 *
 * @return string
 */
function gamipress_143_upgrades( $stored_version ) {

    // Already upgrade
    if ( version_compare( $stored_version, '1.4.3', '>=' ) ) {
        return $stored_version;
    }

    // Prevent run upgrade until database tables are created
    if( ! gamipress_database_table_exists( 'gamipress_user_earnings' ) ) {
        return $stored_version;
    }

    // Check if there is something to migrate
    $upgrade_size = gamipress_143_upgrade_size();

    if( $upgrade_size === 0 ) {

        // There is nothing to update, so upgrade
        $stored_version = '1.4.3';

    } else if( is_gamipress_upgrade_completed( 'update_user_earnings' ) ) {

        // Migrations are finished, so upgrade
        $stored_version = '1.4.3';

    }

    return $stored_version;

}
add_filter( 'gamipress_process_upgrades', 'gamipress_143_upgrades', 143 );

/**
 * 1.4.3 upgrades notices
 */
function gamipress_143_upgrades_notices() {

    // Bail if GamiPress is active network wide and we are not in main site
    if( gamipress_is_network_wide_active() && ! is_main_site() ) {
        return;
    }

    // Check user permissions
    if ( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    // Already upgraded!
    if( is_gamipress_upgraded_to( '1.4.3' ) ) {
        return;
    }

    $running_upgrade = get_option( 'gamipress_running_upgrade' );

    // Other upgrade already running
    if( $running_upgrade && $running_upgrade !== '1.4.3' ) {
        return;
    }

    if( ! is_gamipress_upgrade_completed( 'update_user_earnings' ) ) :
        ?>
        <div id="gamipress-upgrade-notice" class="updated">

            <?php if( $running_upgrade === '1.4.3' ) : ?>

                <p>
                    <?php _e( 'Upgrading GamiPress database...', 'gamipress' ); ?>
                </p>
                <div class="gamipress-upgrade-progress" data-running-upgrade="1.4.3">
                    <div class="gamipress-upgrade-progress-bar" style="width: 0%;"></div>
                </div>

            <?php else : ?>

                <p>
                    <?php _e( 'GamiPress needs to upgrade the database. <strong>Please backup your database before starting this upgrade.</strong> This upgrade routine will be making changes to the database that are not reversible.', 'gamipress' ); ?>
                </p>
                <p>
                    <a href="javascript:void(0);" onClick="jQuery(this).parent().next('p').slideToggle();" class="button"><?php _e( 'Learn more about this upgrade', 'gamipress' ); ?></a>
                    <a href="javascript:void(0);" onClick="gamipress_start_upgrade('1.4.3')" class="button button-primary"><?php _e( 'Start the upgrade', 'gamipress' ); ?></a>
                </p>
                <p style="display: none;">
                    <?php _e( '<strong>About this upgrade:</strong><br />This is a <strong><em>mandatory</em></strong> update that will update all user earnings with new database table fields. This upgrade provides better performance and scalability.', 'gamipress' ); ?>
                </p>

            <?php endif; ?>

        </div>
        <?php
    endif;

}
add_action( 'admin_notices', 'gamipress_143_upgrades_notices' );

/**
 * Return the number of entries to upgrade
 *
 * @return int
 */
function gamipress_143_upgrade_size() {

    global $wpdb;

    $upgrade_size = 0;

    // Retrieve the count of users earnings to upgrade
    if( ! is_gamipress_upgrade_completed( 'update_user_earnings' ) ) {

        $user_earnings = GamiPress()->db->user_earnings;

        $earnings_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$user_earnings} AS e WHERE e.title = ''" );

        $upgrade_size += absint( $earnings_count );

    }

    return $upgrade_size;

}

/**
 * Ajax function to meet the upgrade size
 */
function gamipress_ajax_143_upgrade_info() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Already upgraded
    if ( is_gamipress_upgraded_to( '1.4.3' ) ) {
        wp_send_json_success( array( 'upgraded' => true ) );
    }

    $upgrade_size = gamipress_143_upgrade_size();

    wp_send_json_success( array( 'total' => $upgrade_size ) );
}
add_action( 'wp_ajax_gamipress_143_upgrade_info', 'gamipress_ajax_143_upgrade_info' );

/**
 * Ajax process of 1.4.3 upgrades
 */
function gamipress_ajax_process_143_upgrade() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    global $wpdb;

    // Already upgraded
    if ( is_gamipress_upgraded_to( '1.4.3' ) ) {
        wp_send_json_success( array( 'upgraded' => true ) );
    }

    ignore_user_abort( true );
    set_time_limit( 0 );

    // Add option to meet that upgrade process has been started
    update_option( 'gamipress_running_upgrade', '1.4.3' );

    $current = isset( $_REQUEST['current'] ) ? absint( $_REQUEST['current'] ) : 0;

    // ----------------------------
    // User earnings update
    // ----------------------------
    if( ! is_gamipress_upgrade_completed( 'update_user_earnings' ) ) {

        // Update gamipress_user_earnings table title field
        $ct_table = ct_setup_table( 'gamipress_user_earnings' );

        $limit = 100;

        $user_earnings = GamiPress()->db->user_earnings;

        // Retrieve all user earnings without title
        $results = $wpdb->get_results( "SELECT * FROM {$user_earnings} AS e WHERE e.title = '' LIMIT {$limit}" );

        foreach( $results as $user_earning ) {

            // Set the new title using WordPress (no title)
            $title = __( '(no title)' );

            if( gamipress_post_exists( $user_earning->post_id ) ) {
                $post_title = gamipress_get_post_field( 'post_title', $user_earning->post_id );

                if( ! empty( $post_title ) ) {
                    $title = $post_title;
                }
            }

            $ct_table->db->update(
                array( 'title' => $title ),
                array( 'user_earning_id' => $user_earning->user_earning_id )
            );

            $current++;
        }

        $earnings_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$user_earnings} AS e WHERE e.title = ''" );

        if( absint( $earnings_count ) === 0 ) {
            gamipress_set_upgrade_complete( 'update_user_earnings' );
        }

    }

    // Successfully upgraded
    if( is_gamipress_upgrade_completed( 'update_user_earnings' ) ) {

        // Remove option to meet that upgrade process has been finished
        delete_option( 'gamipress_running_upgrade' );

        // Updated stored version
        update_option( 'gamipress_version', '1.4.3' );

        wp_send_json_success( array( 'upgraded' => true ) );
    }

    wp_send_json_success( array( 'current' => $current ) );

}
add_action( 'wp_ajax_gamipress_process_143_upgrade', 'gamipress_ajax_process_143_upgrade' );

function gamipress_ajax_stop_process_143_upgrade() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    $running_upgrade = get_option( 'gamipress_running_upgrade' );

    // Check if is out upgrade
    if( $running_upgrade === '1.4.3' ) {
        delete_option( 'gamipress_running_upgrade' );
    }

    wp_send_json_success();
}
add_action( 'wp_ajax_gamipress_stop_process_143_upgrade', 'gamipress_ajax_stop_process_143_upgrade' );