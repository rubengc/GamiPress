<?php
/**
 * 1.2.8 Upgrades
 *
 * @package     GamiPress\Admin\Upgrades\1.2.8
 * @since       1.2.8
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return 1.2.8 as last required upgrade
 *
 * @return string
 */
function gamipress_128_is_last_required_upgrade() {

    return '1.2.8';

}
add_filter( 'gamipress_get_last_required_upgrade', 'gamipress_128_is_last_required_upgrade', 128 );

/**
 * Process 1.2.8 upgrades
 *
 * @param string $stored_version
 *
 * @return string
 */
function gamipress_128_upgrades( $stored_version ) {

    // Already upgrade
    if ( version_compare( $stored_version, '1.2.8', '>=' ) ) {
        return $stored_version;
    }

    // Prevent run upgrade until database tables are created
    if( ! gamipress_database_table_exists( GamiPress()->db->logs ) || ! gamipress_database_table_exists( GamiPress()->db->user_earnings ) ) {
        return $stored_version;
    }

    // Check if there is something to migrate
    $upgrade_size = gamipress_128_upgrade_size();

    if( $upgrade_size === 0 ) {

        // There is nothing to update, so upgrade
        $stored_version = '1.2.8';

    } else if( is_gamipress_upgrade_completed( 'migrate_user_earnings' ) && is_gamipress_upgrade_completed( 'migrate_logs' ) ) {

        // Migrations are finished, so upgrade
        $stored_version = '1.2.8';

    }

    return $stored_version;

}
add_filter( 'gamipress_process_upgrades', 'gamipress_128_upgrades', 128 );

/**
 * 1.2.8 upgrades notices
 */
function gamipress_128_upgrades_notices() {

    // Bail if GamiPress is active network wide and we are not in main site
    if( gamipress_is_network_wide_active() && ! is_main_site() ) {
        return;
    }

    // Check user permissions
    if ( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    // Already upgraded!
    if( is_gamipress_upgraded_to( '1.2.8' ) ) {
        return;
    }

    // Prevent run upgrade until database tables are created
    if( ! gamipress_database_table_exists( GamiPress()->db->logs ) || ! gamipress_database_table_exists( GamiPress()->db->user_earnings ) ) {
        return;
    }

    $running_upgrade = get_option( 'gamipress_running_upgrade' );

    // Other upgrade already running
    if( $running_upgrade && $running_upgrade !== '1.2.8' ) {
        return;
    }

    if( ! is_gamipress_upgrade_completed( 'migrate_user_earnings' ) || ! is_gamipress_upgrade_completed( 'migrate_logs' ) ) :
        ?>
        <div id="gamipress-upgrade-notice" class="updated">

            <?php if( $running_upgrade === '1.2.8' ) : ?>

                <p>
                    <?php _e( 'Upgrading GamiPress database...', 'gamipress' ); ?>
                </p>
                <div class="gamipress-upgrade-progress" data-running-upgrade="1.2.8">
                    <div class="gamipress-upgrade-progress-bar" style="width: 0%;"></div>
                </div>

            <?php else : ?>

                <p>
                    <?php _e( 'GamiPress needs to upgrade the database. <strong>Please backup your database before starting this upgrade.</strong> This upgrade routine will be making changes to the database that are not reversible.', 'gamipress' ); ?>
                </p>
                <p>
                    <a href="javascript:void(0);" onClick="jQuery(this).parent().next('p').slideToggle();" class="button"><?php _e( 'Learn more about this upgrade', 'gamipress' ); ?></a>
                    <a href="javascript:void(0);" onClick="gamipress_start_upgrade('1.2.8')" class="button button-primary"><?php _e( 'Start the upgrade', 'gamipress' ); ?></a>
                </p>
                <p style="display: none;">
                    <?php _e( '<strong>About this upgrade:</strong><br />This is a <strong><em>mandatory</em></strong> update that will migrate all user earnings and logs with their meta data to a new custom database table. This upgrade provides better performance and scalability.', 'gamipress' ); ?>
                </p>

            <?php endif; ?>

        </div>
        <?php
    endif;

}
add_action( 'admin_notices', 'gamipress_128_upgrades_notices' );

/**
 * Return the number of entries to upgrade
 *
 * @return int
 */
function gamipress_128_upgrade_size() {

    global $wpdb;

    $upgrade_size = 0;

    // Retrieve the count of users earnings to upgrade
    if( ! is_gamipress_upgrade_completed( 'migrate_user_earnings' ) ) {

        $users_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*)
             FROM   $wpdb->usermeta AS u
             WHERE  u.meta_key = %s",
            "_gamipress_achievements"
        ));

        $upgrade_size += absint($users_count);

    }

    // Retrieve the count of logs to upgrade
    if( ! is_gamipress_upgrade_completed( 'migrate_logs' ) ) {

        $posts      = GamiPress()->db->posts;
        $logs_meta 	= GamiPress()->db->logs_meta;

        $logs_count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*)
             FROM {$posts} AS p
             WHERE p.post_type = %s
              AND ID NOT IN (
                SELECT lm.meta_value FROM {$logs_meta} AS lm WHERE lm.meta_key = %s
              )",
            'gamipress-log',
            '_gamipress_legacy_log_id'
        ) );

        $upgrade_size += absint( $logs_count );

    }

    return $upgrade_size;

}

/**
 * Ajax function to meet the upgrade size
 */
function gamipress_ajax_128_upgrade_info() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Already upgraded
    if ( is_gamipress_upgraded_to( '1.2.8' ) ) {
        wp_send_json_success( array( 'upgraded' => true ) );
    }

    $upgrade_size = gamipress_128_upgrade_size();

    wp_send_json_success( array( 'total' => $upgrade_size ) );
}
add_action( 'wp_ajax_gamipress_128_upgrade_info', 'gamipress_ajax_128_upgrade_info' );

/**
 * Ajax process of 1.2.8 upgrades
 */
function gamipress_ajax_process_128_upgrade() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    global $wpdb;

    $posts      = GamiPress()->db->posts;
    $logs 		= GamiPress()->db->logs;
    $logs_meta 	= GamiPress()->db->logs_meta;

    // Already upgraded
    if ( is_gamipress_upgraded_to( '1.2.8' ) ) {
        wp_send_json_success( array( 'upgraded' => true ) );
    }

    ignore_user_abort( true );
    set_time_limit( 0 );

    // Add option to meet that upgrade process has been started
    update_option( 'gamipress_running_upgrade', '1.2.8' );

    $current = isset( $_REQUEST['current'] ) ? absint( $_REQUEST['current'] ) : 0;

    // ----------------------------
    // User earnings migration
    // ----------------------------
    if( ! is_gamipress_upgrade_completed( 'migrate_user_earnings' ) ) {

        // Migrate from user meta _gamipress_achievements to gamipress_user_earnings table
        $ct_table = ct_setup_table( 'gamipress_user_earnings' );

        // Retrieve all user IDs with the meta _gamipress_achievements
        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT u.user_id, u.meta_value
		     FROM   $wpdb->usermeta AS u
		     WHERE  u.meta_key = %s",
            '_gamipress_achievements'
        ) );

        foreach( $results as $result ) {

            $user_id = $result->user_id;
            $user_earnings = maybe_unserialize( $result->meta_value );

            if( is_array( $user_earnings ) ) {

                foreach( $user_earnings as $user_earning ) {

                    // Skip if not is a user earning
                    if( ! $user_earning || ! is_object( $user_earning ) ) {
                        continue;
                    }

                    $user_earnings_table = GamiPress()->db->user_earnings;

                    $exists = $wpdb->get_var( $wpdb->prepare(
                        "SELECT COUNT(*)
                         FROM {$user_earnings_table}
                        WHERE user_id = %d
                          AND post_id = %d
                          AND date = %s
                        LIMIT 1;",
                        absint( $user_id ),
                        absint( $user_earning->ID ),
                        date( 'Y-m-d H:i:s', $user_earning->date_earned )
                    ) );

                    // Skip if already exists
                    if( absint( $exists ) ) {
                        continue;
                    }


                    // Insert from the user _gamipress_achievements meta to gamipress_user_earnings table
                    $ct_table->db->insert( array(
                        'user_id' => $user_id,
                        'post_id' => $user_earning->ID,
                        'post_type' => $user_earning->post_type,
                        'points' => $user_earning->points,
                        'points_type' => $user_earning->points_type,
                        'date' => date( 'Y-m-d H:i:s', $user_earning->date_earned ),
                    ) );

                }

            }

            $current++;
        }

        gamipress_set_upgrade_complete( 'migrate_user_earnings' );

    }

    // ----------------------------
    // Logs migration
    // ----------------------------
    if( is_gamipress_upgrade_completed( 'migrate_user_earnings' ) && ! is_gamipress_upgrade_completed( 'migrate_logs' ) ) {

        $ct_table = ct_setup_table( 'gamipress_logs' );
        $prefix = '_gamipress_';

        $limit = 50;

        $logs = $wpdb->get_results( $wpdb->prepare(
            "SELECT *
             FROM {$posts}
             WHERE post_type = %s
              AND ID NOT IN (
                SELECT lm.meta_value FROM {$logs_meta} AS lm WHERE lm.meta_key = %s
              )
             ORDER BY ID ASC
            LIMIT %d;",
            'gamipress-log',
            $prefix . 'legacy_log_id',
            $limit
        ) );

        foreach( $logs as $log ) {

            $type = get_post_meta( $log->ID, $prefix . 'type', true );

            $exists = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*)
                 FROM {$logs_meta}
                 WHERE ( meta_key = %s
                  AND meta_value = %s )
                 LIMIT 1;",
                $prefix . 'legacy_log_id',
                $log->ID
            ) );

            // Skip if already exists
            if( absint( $exists ) ) {
                continue;
            }

            // Insert from posts to gamipress_logs table
            $log_id = $ct_table->db->insert( array(
                'title' => $log->post_title,
                'description' => $log->post_excerpt,
                'type' => $type,
                'access' => $log->post_status === 'publish' ? 'public' : 'private',
                'user_id' => $log->post_author,
                'date' => $log->post_date,
            ) );

            if( $log_id ) {

                // Legacy Log ID
                $ct_table->meta->db->insert( array(
                    'log_id' => $log_id,
                    'meta_key' => $prefix . 'legacy_log_id',
                    'meta_value' => $log->ID,
                ) );

                // Update log meta data based on type
                if( $type === 'event_trigger' ) {

                    $trigger = get_post_meta( $log->ID, $prefix . 'trigger_type', true );

                    // Trigger Type
                    $ct_table->meta->db->insert( array(
                        'log_id' => $log_id,
                        'meta_key' => $prefix . 'trigger_type',
                        'meta_value' => $trigger,
                    ) );

                    // Count
                    $ct_table->meta->db->insert( array(
                        'log_id' => $log_id,
                        'meta_key' => $prefix . 'count',
                        'meta_value' => get_post_meta( $log->ID, $prefix . 'count', true ),
                    ) );

                    // If is a specific activity trigger, then add the achievement_post field
                    if( in_array( $trigger, array_keys( gamipress_get_specific_activity_triggers() ) ) ) {

                        // Achievement Attached Post ID
                        $ct_table->meta->db->insert( array(
                            'log_id' => $log_id,
                            'meta_key' => $prefix . 'achievement_post',
                            'meta_value' => get_post_meta( $log->ID, $prefix . 'achievement_post', true ),
                        ) );

                    }

                    // GamiPress Social Share support
                    $social_network = get_post_meta( $log->ID, $prefix . 'social_network', true );

                    if( $social_network ) {

                        $ct_table->meta->db->insert( array(
                            'log_id' => $log_id,
                            'meta_key' => $prefix . 'social_network',
                            'meta_value' => get_post_meta( $log->ID, $prefix . 'social_network', true ),
                        ) );

                    }

                } else if( $type === 'achievement_earn' || $type === 'achievement_award' ) {

                    // Achievement ID
                    $ct_table->meta->db->insert( array(
                        'log_id' => $log_id,
                        'meta_key' => $prefix . 'achievement_id',
                        'meta_value' => get_post_meta( $log->ID, $prefix . 'achievement_id', true ),
                    ) );

                } else if( $type === 'points_award' || $type === 'points_earn' ) {

                    // Points
                    $ct_table->meta->db->insert( array(
                        'log_id' => $log_id,
                        'meta_key' => $prefix . 'points',
                        'meta_value' => get_post_meta( $log->ID, $prefix . 'points', true ),
                    ) );

                    // Points Type
                    $ct_table->meta->db->insert( array(
                        'log_id' => $log_id,
                        'meta_key' => $prefix . 'points_type',
                        'meta_value' => get_post_meta( $log->ID, $prefix . 'points_type', true ),
                    ) );

                    // Total Points
                    $ct_table->meta->db->insert( array(
                        'log_id' => $log_id,
                        'meta_key' => $prefix . 'total_points',
                        'meta_value' => get_post_meta( $log->ID, $prefix . 'total_points', true ),
                    ) );

                    if( $type === 'points_award' ) {

                        // Admin ID
                        $ct_table->meta->db->insert( array(
                            'log_id' => $log_id,
                            'meta_key' => $prefix . 'admin_id',
                            'meta_value' => get_post_meta( $log->ID, $prefix . 'admin_id', true ),
                        ) );

                    }
                }

                $current++;
            }

        }

        $logs_count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*)
             FROM {$posts} AS p
             WHERE p.post_type = %s
              AND ID NOT IN (
                SELECT lm.meta_value FROM {$ct_table->meta->db->table_name} AS lm WHERE lm.meta_key = %s
              )",
            'gamipress-log',
            '_gamipress_legacy_log_id'
        ) );

        if( absint( $logs_count ) === 0 ) {
            gamipress_set_upgrade_complete( 'migrate_logs' );
        }

    }

    // Successfully upgraded
    if( is_gamipress_upgrade_completed( 'migrate_user_earnings' ) && is_gamipress_upgrade_completed( 'migrate_logs' ) ) {

        // Last step, remove all old data
        $wpdb->delete( $wpdb->usermeta, array(
            'meta_key' =>  '_gamipress_achievements'
        ) );

        $wpdb->delete( $posts, array(
            'post_type' =>  'gamipress-log'
        ) );

        // Remove option to meet that upgrade process has been finished
        delete_option( 'gamipress_running_upgrade' );

        // Updated stored version
        update_option( 'gamipress_version', '1.2.8' );

        wp_send_json_success( array( 'upgraded' => true ) );
    }

    wp_send_json_success( array( 'current' => $current ) );

}
add_action( 'wp_ajax_gamipress_process_128_upgrade', 'gamipress_ajax_process_128_upgrade' );

function gamipress_ajax_stop_process_128_upgrade() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    $running_upgrade = get_option( 'gamipress_running_upgrade' );

    // Check if is out upgrade
    if( $running_upgrade === '1.2.8' ) {
        delete_option( 'gamipress_running_upgrade' );
    }

    wp_send_json_success();
}
add_action( 'wp_ajax_gamipress_stop_process_128_upgrade', 'gamipress_ajax_stop_process_128_upgrade' );