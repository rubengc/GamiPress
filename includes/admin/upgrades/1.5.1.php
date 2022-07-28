<?php
/**
 * 1.5.1 Upgrades
 *
 * @package     GamiPress\Admin\Upgrades\1.5.1
 * @since       1.5.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return 1.5.1 as last required upgrade
 *
 * @return string
 */
function gamipress_151_is_last_required_upgrade() {

    return '1.5.1';

}
add_filter( 'gamipress_get_last_required_upgrade', 'gamipress_151_is_last_required_upgrade', 151 );

/**
 * Process 1.5.1 upgrades
 *
 * @param string $stored_version
 *
 * @return string
 */
function gamipress_151_upgrades( $stored_version ) {

    // Already upgrade
    if ( version_compare( $stored_version, '1.5.1', '>=' ) ) {
        return $stored_version;
    }

    // Check if there is something to migrate
    $upgrade_size = gamipress_151_upgrade_size();

    if( $upgrade_size === 0 ) {

        // There is nothing to update, so upgrade
        $stored_version = '1.5.1';

    } else if( is_gamipress_upgrade_completed( 'update_requirements_relationships' ) && is_gamipress_upgrade_completed( 'update_achievements_relationships' ) ) {

        // Migrations are finished, so upgrade
        $stored_version = '1.5.1';

    }

    return $stored_version;

}
add_filter( 'gamipress_process_upgrades', 'gamipress_151_upgrades', 151 );

/**
 * 1.5.1 upgrades notices
 */
function gamipress_151_upgrades_notices() {

    // Bail if GamiPress is active network wide and we are not in main site
    if( gamipress_is_network_wide_active() && ! is_main_site() ) {
        return;
    }

    // Check user permissions
    if ( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    // Already upgraded!
    if( is_gamipress_upgraded_to( '1.5.1' ) ) {
        return;
    }

    $running_upgrade = get_option( 'gamipress_running_upgrade' );

    // Other upgrade already running
    if( $running_upgrade && $running_upgrade !== '1.5.1' ) {
        return;
    }

    if( ! is_gamipress_upgrade_completed( 'update_requirements_relationships' )
        || ! is_gamipress_upgrade_completed( 'update_achievements_relationships' ) ) :
        ?>

        <div id="gamipress-upgrade-notice" class="updated">

            <?php if( $running_upgrade === '1.5.1' ) : ?>

                <p>
                    <?php _e( 'Upgrading GamiPress database...', 'gamipress' ); ?>
                </p>
                <div class="gamipress-upgrade-progress" data-running-upgrade="1.5.1">
                    <div class="gamipress-upgrade-progress-bar" style="width: 0%;"></div>
                </div>

            <?php else : ?>

                <p>
                    <?php _e( 'GamiPress needs to upgrade the database. <strong>Please backup your database before starting this upgrade.</strong> This upgrade routine will be making changes to the database that are not reversible.', 'gamipress' ); ?>
                </p>
                <p>
                    <a href="javascript:void(0);" onClick="jQuery(this).parent().next('p').slideToggle();" class="button"><?php _e( 'Learn more about this upgrade', 'gamipress' ); ?></a>
                    <a href="javascript:void(0);" onClick="gamipress_start_upgrade('1.5.1')" class="button button-primary"><?php _e( 'Start the upgrade', 'gamipress' ); ?></a>
                </p>
                <p style="display: none;">
                    <?php _e( '<strong>About this upgrade:</strong><br />This is a <strong><em>mandatory</em></strong> update that will update all requirements relationships (achievements and their steps, points types and their automatic awards and deducts, ranks and their requirements).', 'gamipress' ); ?>
                    <br>
                    <?php _e( 'On GamiPress 1.5.1, an internal library to handle relationships has been removed to make relationships <strong>WordPress based</strong> instead.', 'gamipress' ); ?>
                    <br>
                    <?php _e( 'Removing this library will improve the <strong>compatibility</strong> of GamiPress with other plugins and themes as well as will <strong>speed up</strong> all relationship related queries making GamiPress much faster!', 'gamipress' ); ?>
                </p>

            <?php endif; ?>

        </div>

        <?php
    endif;

}
add_action( 'admin_notices', 'gamipress_151_upgrades_notices' );

/**
 * Return the number of entries to upgrade
 *
 * @return int
 */
function gamipress_151_upgrade_size() {

    global $wpdb;

    $upgrade_size = 0;

    $p2p  = ( property_exists( $wpdb, 'p2p' ) ? $wpdb->p2p : $wpdb->prefix . 'p2p' );

    // Multisite support
    if( gamipress_is_network_wide_active() ) {
        $p2p = $wpdb->base_prefix . 'p2p';
    }

    // Extra check for new installs
    if( ! gamipress_database_table_exists( $p2p ) ) {
        return 0;
    }

    // Retrieve the count of post upgrade
    if( ! is_gamipress_upgrade_completed( 'update_requirements_relationships' ) ) {

        // Setup vars
        $posts              = GamiPress()->db->posts;
        $requirements_types = gamipress_get_requirement_types_slugs();

        $posts_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$posts} AS p WHERE p.post_type IN ( '" . implode( "', '", $requirements_types ) . "' ) AND p.post_parent = 0" );

        $upgrade_size += absint( $posts_count );

    }

    // Retrieve the count of meta data upgrade
    if( ! is_gamipress_upgrade_completed( 'update_achievements_relationships' ) ) {

        // Setup vars
        $postmeta           = GamiPress()->db->postmeta;

        $meta_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$postmeta} AS pm WHERE pm.post_id NOT IN ( SELECT spm.post_id FROM {$postmeta} AS spm WHERE spm.meta_key = '_gamipress_achievement_post' ) AND pm.meta_key = '_gamipress_trigger_type' AND pm.meta_value = 'specific-achievement'" );

        $upgrade_size += absint( $meta_count );

    }

    return $upgrade_size;

}

/**
 * Ajax function to meet the upgrade size
 */
function gamipress_ajax_151_upgrade_info() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Already upgraded
    if ( is_gamipress_upgraded_to( '1.5.1' ) ) {
        wp_send_json_success( array( 'upgraded' => true ) );
    }

    $upgrade_size = gamipress_151_upgrade_size();

    wp_send_json_success( array( 'total' => $upgrade_size ) );

}
add_action( 'wp_ajax_gamipress_151_upgrade_info', 'gamipress_ajax_151_upgrade_info' );

/**
 * Ajax process of 1.5.1 upgrades
 */
function gamipress_ajax_process_151_upgrade() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    global $wpdb;

    // Already upgraded
    if ( is_gamipress_upgraded_to( '1.5.1' ) ) {
        wp_send_json_success( array( 'upgraded' => true ) );
    }

    ignore_user_abort( true );
    set_time_limit( 0 );

    // Add option to meet that upgrade process has been started
    update_option( 'gamipress_running_upgrade', '1.5.1' );

    // Setup common vars
    $posts      = GamiPress()->db->posts;
    $postmeta   = GamiPress()->db->postmeta;
    $current    = isset( $_REQUEST['current'] ) ? absint( $_REQUEST['current'] ) : 0;
    $limit      = 200;

    // --------------------------------------------------------
    // Requirements relationship update
    // --------------------------------------------------------
    // We need to migrate all p2p {requirement-type}-to-* connections to the requirement post_parent field
    // Also, we need to migrate the p2p meta 'order' to the requirement menu_order field

    if( ! is_gamipress_upgrade_completed( 'update_requirements_relationships' ) ) {

        // Migrate from p2p table to posts
        $continue = true;

        // Setup the P2P tables
        $p2p        = ( property_exists( $wpdb, 'p2p' ) ? $wpdb->p2p : $wpdb->prefix . 'p2p' );
        $p2pmeta 	= ( property_exists( $wpdb, 'p2pmeta' ) ? $wpdb->p2pmeta : $wpdb->prefix . 'p2pmeta' );

        // Multisite support
        if( gamipress_is_network_wide_active() ) {
            $p2p        = $wpdb->base_prefix . 'p2p';
            $p2pmeta 	= $wpdb->base_prefix . 'p2pmeta';
        }

        // Extra check for new installs
        if( ! gamipress_database_table_exists( $p2p ) ) {
            gamipress_set_upgrade_complete( 'update_requirements_relationships' );
            $continue = false;
        }

        if( $continue ) {
            // Get our requirement types
            $requirements_types = gamipress_get_requirement_types_slugs();

            // Retrieve all requirements without parent
            $results = $wpdb->get_results( "SELECT p.ID, p.post_type FROM {$posts} AS p WHERE p.post_type IN ( '" . implode( "', '", $requirements_types ) . "' ) AND p.post_parent = 0 LIMIT {$limit}" );

            foreach( $results as $post ) {

                // Get the requirement relationship from the P2P table
                // p2p_from is the requirement ID
                // p2p_to is the parent ID (achievement, points type or rank)
                $p2p_entry = $wpdb->get_row( "SELECT p2p.p2p_id, p2p.p2p_to FROM {$p2p} AS p2p WHERE p2p.p2p_from = {$post->ID} AND p2p.p2p_type LIKE '{$post->post_type}-to-%'" );

                if( $p2p_entry ) {

                    // Setup the vars to update our post
                    $post_parent = $p2p_entry->p2p_to;
                    $menu_order = absint( $wpdb->get_var( "SELECT p2pmeta.meta_value FROM {$p2pmeta} AS p2pmeta WHERE p2pmeta.p2p_id = {$p2p_entry->p2p_id} AND p2pmeta.meta_key = 'order'" ) );

                    // Update the requirement object to meet the new relationships
                    wp_update_post( array(
                        'ID' => $post->ID,
                        'post_parent' => $post_parent,
                        'menu_order' => $menu_order,
                    ) );

                } else {

                    // Delete this requirement since is not well connected
                    wp_delete_post( $post->ID );

                }

                $current++;
            }

            $posts_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$posts} AS p WHERE p.post_type IN ( '" . implode( "', '", $requirements_types ) . "' ) AND p.post_parent = 0" );

            if( absint( $posts_count ) === 0 ) {
                gamipress_set_upgrade_complete( 'update_requirements_relationships' );
            }
        }

    }

    // --------------------------------------------------------
    // Requirements achievements relationship update
    // --------------------------------------------------------
    // For specific-achievement triggers is required to update relationships between {achievement-type}-to-{requirement}
    // So, we need to move specific-achievement connection to _gamipress_achievement_post meta

    if( is_gamipress_upgrade_completed( 'update_requirements_relationships' ) && ! is_gamipress_upgrade_completed( 'update_achievements_relationships' ) ) {

        $continue = true;

        // Extra check for new installs
        if( ! gamipress_database_table_exists( $p2p ) ) {
            gamipress_set_upgrade_complete( 'update_achievements_relationships' );
            $continue = false;
        }

        if( $continue ) {
            // Retrieve all requirements with specific-achievement trigger type and without _gamipress_achievement_post meta
            $results = $wpdb->get_results(
                "SELECT pm.post_id
                 FROM {$postmeta} AS pm
                 WHERE pm.post_id NOT IN ( SELECT spm.post_id FROM {$postmeta} AS spm WHERE spm.meta_key = '_gamipress_achievement_post' )
                  AND pm.meta_key = '_gamipress_trigger_type' AND pm.meta_value = 'specific-achievement'
                  LIMIT {$limit}"
            );

            foreach( $results as $result ) {

                $requirement_type = gamipress_get_post_type( $result->post_id );
                $achievement_type = gamipress_get_post_meta( $result->post_id, '_gamipress_achievement_type' );

                // Get the requirement relationship from the P2P table
                // p2p_from is the achievement ID
                // p2p_to is the requirement ID
                $achievement_id = absint( $wpdb->get_var( "SELECT p2p.p2p_from FROM {$p2p} AS p2p WHERE p2p.p2p_to = {$result->post_id} AND p2p.p2p_type = '{$achievement_type}-to-{$requirement_type}'" ) );

                gamipress_update_post_meta( $result->post_id, '_gamipress_achievement_post', $achievement_id );

                $current++;

            }

            $meta_count = $wpdb->get_var(
                "SELECT COUNT(*)
                 FROM {$postmeta} AS pm
                 WHERE pm.post_id NOT IN ( SELECT spm.post_id FROM {$postmeta} AS spm WHERE spm.meta_key = '_gamipress_achievement_post' )
                  AND pm.meta_key = '_gamipress_trigger_type' AND pm.meta_value = 'specific-achievement'"
            );

            if( absint( $meta_count ) === 0 ) {
                gamipress_set_upgrade_complete( 'update_requirements_relationships' );
            }
        }

    }

    // Successfully upgraded
    if( is_gamipress_upgrade_completed( 'update_requirements_relationships' )
        && is_gamipress_upgrade_completed( 'update_achievements_relationships' ) ) {

        // Remove option to meet that upgrade process has been finished
        delete_option( 'gamipress_running_upgrade' );

        // Updated stored version
        update_option( 'gamipress_version', '1.5.1' );

        wp_send_json_success( array( 'upgraded' => true ) );

    }

    wp_send_json_success( array( 'current' => $current ) );

}
add_action( 'wp_ajax_gamipress_process_151_upgrade', 'gamipress_ajax_process_151_upgrade' );

function gamipress_ajax_stop_process_151_upgrade() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    $running_upgrade = get_option( 'gamipress_running_upgrade' );

    // Check if is out upgrade
    if( $running_upgrade === '1.5.1' )
        delete_option( 'gamipress_running_upgrade' );

    wp_send_json_success();

}
add_action( 'wp_ajax_gamipress_stop_process_151_upgrade', 'gamipress_ajax_stop_process_151_upgrade' );