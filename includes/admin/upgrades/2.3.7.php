<?php
/**
 * 2.3.7 Upgrades
 *
 * @package     GamiPress\Admin\Upgrades\2.3.7
 * @since       2.3.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return 2.3.7 as last required upgrade
 *
 * @return string
 */
function gamipress_237_is_last_required_upgrade() {

    return '2.3.7';

}
add_filter( 'gamipress_get_last_required_upgrade', 'gamipress_237_is_last_required_upgrade', 237 );

/**
 * Process 2.3.7 upgrades
 *
 * @param string $stored_version
 *
 * @return string
 */
function gamipress_237_upgrades( $stored_version ) {

    // Already upgrade
    if ( version_compare( $stored_version, '2.3.7', '>=' ) ) {
        return $stored_version;
    }

    // Check if there is something to migrate
    $upgrade_size = gamipress_237_upgrade_size();

    if( $upgrade_size === 0 ) {

        // There is nothing to update, so upgrade
        $stored_version = '2.3.7';

    } else if( is_gamipress_upgrade_completed( 'update_earnings_parent_post_type_meta' ) ) {

        // Migrations are finished, so upgrade
        $stored_version = '2.3.7';

    }

    return $stored_version;

}
add_filter( 'gamipress_process_upgrades', 'gamipress_237_upgrades', 237 );

/**
 * 2.3.7 upgrades notices
 */
function gamipress_237_upgrades_notices() {

    // Bail if GamiPress is active network wide and we are not in main site
    if( gamipress_is_network_wide_active() && ! is_main_site() ) {
        return;
    }

    // Check user permissions
    if ( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    // Already upgraded!
    if( is_gamipress_upgraded_to( '2.3.7' ) ) {
        return;
    }

    $running_upgrade = get_option( 'gamipress_running_upgrade' );

    // Other upgrade already running
    if( $running_upgrade && $running_upgrade !== '2.3.7' ) {
        return;
    }

    if( ! is_gamipress_upgrade_completed( 'update_earnings_parent_post_type_meta' ) ) : ?>

        <div id="gamipress-upgrade-notice" class="updated">

            <?php if( $running_upgrade === '2.3.7' ) : ?>

                <p>
                    <?php _e( 'Upgrading GamiPress database...', 'gamipress' ); ?>
                </p>
                <div class="gamipress-upgrade-progress" data-running-upgrade="2.3.7">
                    <div class="gamipress-upgrade-progress-bar" style="width: 0%;"></div>
                </div>

            <?php else : ?>

                <p>
                    <?php _e( 'GamiPress needs to upgrade the database. <strong>Please backup your database before starting this upgrade.</strong> This upgrade routine will be making changes to the database that are not reversible.', 'gamipress' ); ?>
                </p>
                <p>
                    <a href="javascript:void(0);" onClick="jQuery(this).parent().next('p').slideToggle();" class="button"><?php _e( 'Learn more about this upgrade', 'gamipress' ); ?></a>
                    <a href="javascript:void(0);" onClick="gamipress_start_upgrade('2.3.7')" class="button button-primary"><?php _e( 'Start the upgrade', 'gamipress' ); ?></a>
                </p>
                <p style="display: none;">
                    <?php _e( '<strong>About this upgrade:</strong><br />This is a <strong><em>mandatory</em></strong> update that will update the GamiPress user earnings adding some extra information on earnings related to requirements.', 'gamipress' ); ?>
                    <br>
                    <?php _e( 'Depending on the number of user earnings found, this process could take a while, but there is <strong><em>no danger</em></strong> about lose any data because this process will only <strong><em>append</em></strong> information to old entries, so is <strong><em>100% safe to upgrade</em></strong>.', 'gamipress' ); ?>
                </p>

            <?php endif; ?>

        </div>

        <?php
    endif;

}
add_action( 'admin_notices', 'gamipress_237_upgrades_notices' );

/**
 * Return the number of entries to upgrade
 *
 * @return int
 */
function gamipress_237_upgrade_size() {

    global $wpdb;

    $upgrade_size = 0;

    // Retrieve the count of post upgrade
    if( ! is_gamipress_upgrade_completed( 'update_earnings_parent_post_type_meta' ) && gamipress_database_table_exists( GamiPress()->db->user_earnings ) ) {

        // Setup vars
        $user_earnings      = GamiPress()->db->user_earnings;
        $user_earnings_meta = GamiPress()->db->user_earnings_meta;

        $upgrade_size = absint( $wpdb->get_var(
            "SELECT COUNT(*) 
            FROM {$user_earnings} AS ue 
            LEFT JOIN {$user_earnings_meta} uem ON ( uem.user_earning_id = ue.user_earning_id AND uem.meta_key = '_gamipress_parent_post_type'  ) 
            WHERE ue.post_type IN ( 'step', 'rank-requirement' ) 
            AND uem.meta_value IS NULL"
        ) );

    }

    return $upgrade_size;

}

/**
 * Ajax function to meet the upgrade size
 */
function gamipress_ajax_237_upgrade_info() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Already upgraded
    if ( is_gamipress_upgraded_to( '2.3.7' ) ) {
        wp_send_json_success( array( 'upgraded' => true ) );
    }

    $upgrade_size = gamipress_237_upgrade_size();

    wp_send_json_success( array( 'total' => $upgrade_size ) );

}
add_action( 'wp_ajax_gamipress_237_upgrade_info', 'gamipress_ajax_237_upgrade_info' );

/**
 * Ajax process of 2.3.7 upgrades
 */
function gamipress_ajax_process_237_upgrade() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    global $wpdb;

    // Already upgraded
    if ( is_gamipress_upgraded_to( '2.3.7' ) ) {
        wp_send_json_success( array( 'upgraded' => true ) );
    }

    ignore_user_abort( true );
    set_time_limit( 0 );

    // Add option to meet that upgrade process has been started
    update_option( 'gamipress_running_upgrade', '2.3.7' );

    // --------------------------------------------------------
    // Update user earnings parent_post_type meta
    // --------------------------------------------------------
    // We need to update user earnings "parent_post_type" meta to be able to filter correctly requirements by its parent type in user earnings block

    if( ! is_gamipress_upgrade_completed( 'update_earnings_parent_post_type_meta' ) ) {

        // Setup vars
        $user_earnings      = GamiPress()->db->user_earnings;
        $user_earnings_meta = GamiPress()->db->user_earnings_meta;
        $current            = isset( $_REQUEST['current'] ) ? absint( $_REQUEST['current'] ) : 0;
        $limit              = 50;

        // Retrieve all requirements without parent (ordered by post_id for performance)
        $results = $wpdb->get_results(
                "SELECT ue.user_earning_id, ue.post_id, ue.post_type
            FROM {$user_earnings} AS ue 
            LEFT JOIN {$user_earnings_meta} uem ON ( uem.user_earning_id = ue.user_earning_id AND uem.meta_key = '_gamipress_parent_post_type'  ) 
            WHERE ue.post_type IN ( 'step', 'rank-requirement' ) 
            AND uem.meta_value IS NULL
            ORDER BY ue.post_id ASC
            LIMIT {$limit}"
        );

        $metas = array();
        $meta_key = '_gamipress_parent_post_type';

        // Cache for the parent post types
        $parent_post_types = array();

        foreach( $results as $user_earning ) {

            $meta_value = '';

            if( isset( $parent_post_types[$user_earning->post_id] ) ) {
                // Get the parent post type for this post ID
                $meta_value = $parent_post_types[$user_earning->post_id];
            } else {
                $parent_id = absint( gamipress_get_post_field( 'post_parent', absint( $user_earning->post_id ) ) );

                if( isset( $parent_post_types[$parent_id] ) ) {
                    // Get the parent post type for this parent ID
                    $meta_value = $parent_post_types[$parent_id];
                } else {
                    if( $parent_id !== 0 ) {
                        $meta_value = gamipress_get_post_field( 'post_type', $parent_id );

                        if( $meta_value === 'points-type' ) {
                            $meta_value = gamipress_get_post_field( 'post_name', $parent_id );
                        }
                    }

                    // Cache the parent post type for that post and parent ID
                    $parent_post_types[$user_earning->post_id] = $meta_value;
                    $parent_post_types[$parent_id] = $meta_value;
                }

            }

            if( empty( $meta_value ) ) {
                $meta_value = $user_earning->post_type;
            }

            // Setup the insert value
            $metas[] = $wpdb->prepare( '%d, %s, %s', array( $user_earning->user_earning_id, $meta_key, $meta_value ) );

            $current++;
        }

        $metas = implode( '), (', $metas );

        // Is faster to run a single query to insert all metas instead of insert them one-by-one
        $wpdb->query( "INSERT INTO {$user_earnings_meta} (user_earning_id, meta_key, meta_value) VALUES ({$metas})" );

        $count = absint( $wpdb->get_var(
            "SELECT COUNT(*) 
            FROM {$user_earnings} AS ue 
            LEFT JOIN {$user_earnings_meta} uem ON ( uem.user_earning_id = ue.user_earning_id AND uem.meta_key = '_gamipress_parent_post_type'  ) 
            WHERE ue.post_type IN ( 'step', 'rank-requirement' ) 
            AND uem.meta_value IS NULL"
        ) );

        if( $count === 0 ) {
            gamipress_set_upgrade_complete( 'update_earnings_parent_post_type_meta' );
        }

    }

    // Successfully upgraded
    if( is_gamipress_upgrade_completed( 'update_earnings_parent_post_type_meta' ) ) {

        // Remove option to meet that upgrade process has been finished
        delete_option( 'gamipress_running_upgrade' );

        // Updated stored version
        update_option( 'gamipress_version', '2.3.7' );

        wp_send_json_success( array( 'upgraded' => true ) );

    }

    wp_send_json_success( array( 'current' => $current ) );

}
add_action( 'wp_ajax_gamipress_process_237_upgrade', 'gamipress_ajax_process_237_upgrade' );

function gamipress_ajax_stop_process_237_upgrade() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    $running_upgrade = get_option( 'gamipress_running_upgrade' );

    // Check if is out upgrade
    if( $running_upgrade === '2.3.7' )
        delete_option( 'gamipress_running_upgrade' );

    wp_send_json_success();

}
add_action( 'wp_ajax_gamipress_stop_process_237_upgrade', 'gamipress_ajax_stop_process_237_upgrade' );