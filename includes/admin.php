<?php
/**
 * Admin
 *
 * @package     GamiPress\Admin
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Admin includes
require_once GAMIPRESS_DIR . 'includes/admin/auto-update.php';
require_once GAMIPRESS_DIR . 'includes/admin/contextual-help.php';
require_once GAMIPRESS_DIR . 'includes/admin/dashboard.php';
require_once GAMIPRESS_DIR . 'includes/admin/debug.php';
require_once GAMIPRESS_DIR . 'includes/admin/meta-boxes.php';
require_once GAMIPRESS_DIR . 'includes/admin/notices.php';
require_once GAMIPRESS_DIR . 'includes/admin/plugins.php';
require_once GAMIPRESS_DIR . 'includes/admin/achievements.php';
require_once GAMIPRESS_DIR . 'includes/admin/ranks.php';
require_once GAMIPRESS_DIR . 'includes/admin/requirements.php';
require_once GAMIPRESS_DIR . 'includes/admin/users.php';
require_once GAMIPRESS_DIR . 'includes/admin/upgrades.php';

// Admin pages
require_once GAMIPRESS_DIR . 'includes/admin/pages/dashboard.php';
require_once GAMIPRESS_DIR . 'includes/admin/pages/support.php';
require_once GAMIPRESS_DIR . 'includes/admin/pages/add-ons.php';
require_once GAMIPRESS_DIR . 'includes/admin/pages/assets.php';
require_once GAMIPRESS_DIR . 'includes/admin/pages/licenses.php';
require_once GAMIPRESS_DIR . 'includes/admin/pages/settings.php';
require_once GAMIPRESS_DIR . 'includes/admin/pages/tools.php';

/**
 * Add custom GamiPress body classes
 *
 * @since 1.3.9.5
 *
 * @param string $admin_body_classes
 *
 * @return string
 */
function gamipress_admin_body_class( $admin_body_classes ) {

    global $post_type;

    // Add an extra class to meet that current post type is a gamipress post type
    if(
        in_array( $post_type, array( 'points-type', 'achievement-type', 'rank-type' ) )
        || in_array( $post_type, gamipress_get_achievement_types_slugs() )
        || in_array( $post_type, gamipress_get_rank_types_slugs() )
    ) {
        $admin_body_classes .= ' gamipress-post-type ';
    }

    return $admin_body_classes;

}
add_filter( 'admin_body_class', 'gamipress_admin_body_class' );

/**
 * Add GamiPress menus
 *
 * @since   1.0.0
 * @updated 1.4.0 Added multisite support
 */
function gamipress_admin_menu() {

    // Bail if GamiPress is active network wide and we are not in main site
    if( gamipress_is_network_wide_active() && ! is_main_site() ) {
        return;
    }

    // Set minimum role setting for menus
    $minimum_role = gamipress_get_manager_capability();

    // Achievement types
    $achievement_types = gamipress_get_achievement_types();

    // Achievements menu
    if( ! empty( $achievement_types ) ) {
        add_menu_page( __( 'Achievements', 'gamipress' ), __( 'Achievements', 'gamipress' ), $minimum_role, 'gamipress_achievements', 'gamipress_achievements', 'dashicons-awards', 53 );
    }

    // Rank types
    $rank_types = gamipress_get_rank_types();

    // Achievements menu
    if( ! empty( $rank_types ) ) {
        add_menu_page( __( 'Ranks', 'gamipress' ), __( 'Ranks', 'gamipress' ), $minimum_role, 'gamipress_ranks', 'gamipress_ranks', 'dashicons-rank', 54 );
    }

    // GamiPress menu
    add_menu_page( 'GamiPress', 'GamiPress', $minimum_role, 'gamipress', '', 'dashicons-gamipress', 55 );
    add_submenu_page( 'gamipress', __( 'Dashboard', 'gamipress' ), __( 'Dashboard', 'gamipress' ), $minimum_role, 'gamipress', 'gamipress_dashboard_page' );

}
add_action( 'admin_menu', 'gamipress_admin_menu' );

/**
 * Moves the Dashboard submenu to the first menu
 * For some reason, WordPress assigns the first submenu item the post types registered under this menu
 *
 * @since 1.0.0
 *
 * @param string $parent_file The parent file.
 *
 * @return string
 */
function gamipress_admin_menu_fix( $parent_file ) {

    global $submenu;

    if( isset( $submenu['gamipress'] ) && is_array( $submenu['gamipress'] ) ) {

        // Loop all the GamiPress submenus
        foreach( $submenu['gamipress'] as $i => $menu ) {

            // Check for the "Dashboard" menu to move it to first position
            if( is_array( $menu ) && isset( $menu[3] ) && $menu[3] === __( 'Dashboard', 'gamipress' ) ) {
                unset( $submenu['gamipress'][$i] );
                array_unshift( $submenu['gamipress'], $menu );
                break;
            }

        }

    }

    return $parent_file;

}
add_filter( 'parent_file', 'gamipress_admin_menu_fix' );

/**
 * Add GamiPress submenus
 *
 * @since 1.0.0
 */
function gamipress_admin_submenu() {

    // Set minimum role setting for menus
    $minimum_role = gamipress_get_manager_capability();

    // GamiPress sub menus
    add_submenu_page( 'gamipress', __( 'Help / Support', 'gamipress' ), __( 'Help / Support', 'gamipress' ), $minimum_role, 'gamipress_help_support', 'gamipress_help_support_page' );
    add_submenu_page( 'gamipress', __( 'Add-ons', 'gamipress' ), __( 'Add-ons', 'gamipress' ), $minimum_role, 'gamipress_add_ons', 'gamipress_add_ons_page' );
    add_submenu_page( 'gamipress', __( 'Assets', 'gamipress' ), __( 'Assets', 'gamipress' ), $minimum_role, 'gamipress_assets', 'gamipress_assets_page' );

}
add_action( 'admin_menu', 'gamipress_admin_submenu', 12 );

/**
 * Add Clear Cache submenu
 *
 * @since 2.1.2
 */
function gamipress_clear_cache_admin_submenu() {

    // Set minimum role setting for menus
    $minimum_role = gamipress_get_manager_capability();

    add_submenu_page( 'gamipress', __( 'Clear Cache', 'gamipress' ), __( 'Clear Cache', 'gamipress' ), $minimum_role, admin_url( 'admin.php?page=gamipress&gamipress-action=clear_cache' ), null );

}
add_action( 'admin_menu', 'gamipress_clear_cache_admin_submenu', 13 );

/**
 * Add Try AutomatorWP submenu
 *
 * @since 2.0.0
 */
function gamipress_try_automatorwp_admin_submenu() {

    if( class_exists( 'AutomatorWP' ) ) {
        return;
    }

    // Set minimum role setting for menus
    $minimum_role = gamipress_get_manager_capability();

    $badge = '<span class="gamipress-admin-menu-badge">' . __( 'New', 'gamipress' ) . '</span>';

    add_submenu_page( 'gamipress', __( 'Try AutomatorWP!', 'gamipress' ), __( 'Try AutomatorWP!', 'gamipress' ) . $badge, $minimum_role, 'https://wordpress.org/plugins/automatorwp/', null );
}
add_action( 'admin_menu', 'gamipress_try_automatorwp_admin_submenu', 9999 );

/**
 * Helper funtion to meet if should show the admin bar menu
 *
 * @since 2.0.3
 */
function gamipress_show_admin_bar_menu() {

    // Bail if GamiPress is active network wide and we are not in main site
    if( gamipress_is_network_wide_active() && ! is_main_site() ) {
        return false;
    }

    // Bail if current user can't manage GamiPress
    if ( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return false;
    }

    // Bail if admin bar menu disabled
    if( (bool) gamipress_get_option( 'disable_admin_bar_menu', false ) ) {
        return false;
    }

    return true;

}

/**
 * Add GamiPress admin bar menu
 *
 * @since 1.5.1
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function gamipress_admin_bar_menu( $wp_admin_bar ) {

    // Bail if admin bar menu disabled
    if( ! gamipress_show_admin_bar_menu() ) {
        return;
    }

    // GamiPress
    $wp_admin_bar->add_node( array(
        'id'    => 'gamipress',
        'title'	=>	'<span class="ab-icon"></span>' . 'GamiPress',
        'href'   => admin_url( 'admin.php?page=gamipress' ),
        'meta'  => array( 'class' => 'gamipress' ),
    ) );

    // Dashboard Group
    $wp_admin_bar->add_group( array(
        'id'     => 'gamipress-dashboard-group',
        'parent' => 'gamipress',
    ) );

    // Dashboard
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-dashboard',
        'title'  => __( 'Dashboard', 'gamipress' ),
        'parent' => 'gamipress-dashboard-group',
        'href'   => admin_url( 'admin.php?page=gamipress' )
    ) );

    // Check registered points types
    $points_types = gamipress_get_points_types();

    // - Points Types
    $wp_admin_bar->add_node( array(
        'id'     => 'points-types',
        'title'  => __( 'Points Types', 'gamipress' ),
        'parent' => ( ! empty( $points_types ) ? 'points-group' : 'gamipress' ),
        'href'   => admin_url( 'edit.php?post_type=points-type' )
    ) );

    if( ! empty( $points_types ) ) {

        // Points Group
        $wp_admin_bar->add_group( array(
            'id'     => 'points-group',
            'parent' => 'gamipress',
        ) );

        foreach( $points_types as $points_type => $data ) {

            // - - Achievements
            $wp_admin_bar->add_node( array(
                'id'     => 'points-' . $points_type,
                'title'  => $data['plural_name'],
                'parent' => 'points-types',
                'href'   => get_edit_post_link( $data['ID'] )
            ) );

        }

    }

    // Check registered achievement types
    $achievement_types = gamipress_get_achievement_types();

    // - Achievement Types
    $wp_admin_bar->add_node( array(
        'id'     => 'achievement-types',
        'title'  => __( 'Achievement Types', 'gamipress' ),
        'parent' => ( ! empty( $achievement_types ) ? 'achievements-group' : 'gamipress' ),
        'href'   => admin_url( 'edit.php?post_type=achievement-type' )
    ) );

    if( ! empty( $achievement_types ) ) {

        // Achievements Group
        $wp_admin_bar->add_group( array(
            'id'     => 'achievements-group',
            'parent' => 'gamipress',
        ) );

        foreach( $achievement_types as $achievement_type => $data ) {

            // - - Achievements
            $wp_admin_bar->add_node( array(
                'id'     => 'achievement-' . $achievement_type,
                'title'  => $data['plural_name'],
                'parent' => 'achievement-types',
                'href'   => admin_url( 'edit.php?post_type=' . $achievement_type )
            ) );

        }

    }

    // Check registered rank types
    $rank_types = gamipress_get_rank_types();

    // - Rank Types
    $wp_admin_bar->add_node( array(
        'id'     => 'rank-types',
        'title'  => __( 'Rank Types', 'gamipress' ),
        'parent' => ( ! empty( $rank_types ) ? 'ranks-group' : 'gamipress' ),
        'href'   => admin_url( 'edit.php?post_type=rank-type' )
    ) );

    if( ! empty( $rank_types ) ) {

        // Achievements Group
        $wp_admin_bar->add_group( array(
            'id'     => 'ranks-group',
            'parent' => 'gamipress',
        ) );

        foreach( $rank_types as $rank_type => $data ) {

            // - - Achievements
            $wp_admin_bar->add_node( array(
                'id'     => 'rank-' . $rank_type,
                'title'  => $data['plural_name'],
                'parent' => 'rank-types',
                'href'   => admin_url( 'edit.php?post_type=' . $rank_type )
            ) );

        }

    }

}
add_action( 'admin_bar_menu', 'gamipress_admin_bar_menu', 100 );

/**
 * Add GamiPress admin bar custom tables menu
 *
 * @since 1.5.4
 */
function gamipress_admin_bar_custom_tables_menu( $wp_admin_bar ) {

    // Bail if admin bar menu disabled
    if( ! gamipress_show_admin_bar_menu() ) {
        return;
    }

    // User Earnings
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-user-earnings',
        'title'  => __( 'User Earnings', 'gamipress' ),
        'parent' => 'gamipress',
        'href'   => admin_url( 'admin.php?page=gamipress_user_earnings' )
    ) );

    // Logs
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-logs',
        'title'  => __( 'Logs', 'gamipress' ),
        'parent' => 'gamipress',
        'href'   => admin_url( 'admin.php?page=gamipress_logs' )
    ) );

}
add_action( 'admin_bar_menu', 'gamipress_admin_bar_custom_tables_menu', 150 );

/**
 * Add GamiPress admin bar menu
 *
 * @since 1.5.1
 *
 * @param object $wp_admin_bar The WordPress toolbar object
 */
function gamipress_admin_bar_submenu( $wp_admin_bar ) {

    // Bail if admin bar menu disabled
    if( ! gamipress_show_admin_bar_menu() ) {
        return;
    }

    // Help / Support
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-support',
        'title'  => __( 'Help / Support', 'gamipress' ),
        'parent' => 'gamipress',
        'href'   => admin_url( 'admin.php?page=gamipress_help_support' )
    ) );

    // Add-ons
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-add-ons',
        'title'  => __( 'Add-ons', 'gamipress' ),
        'parent' => 'gamipress',
        'href'   => admin_url( 'admin.php?page=gamipress_add_ons' )
    ) );

    // Assets
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-assets',
        'title'  => __( 'Assets', 'gamipress' ),
        'parent' => 'gamipress',
        'href'   => admin_url( 'admin.php?page=gamipress_assets' )
    ) );

    // Licenses
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-licenses',
        'title'  => __( 'Licenses', 'gamipress' ),
        'parent' => 'gamipress',
        'href'   => admin_url( 'admin.php?page=gamipress_licenses' )
    ) );

    // Tools
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-tools',
        'title'  => __( 'Tools', 'gamipress' ),
        'parent' => 'gamipress',
        'href'   => admin_url( 'admin.php?page=gamipress_tools' )
    ) );

    // Settings
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-settings',
        'title'  => __( 'Settings', 'gamipress' ),
        'parent' => 'gamipress',
        'href'   => admin_url( 'admin.php?page=gamipress_settings' )
    ) );

    // Clear cache
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-clear-cache',
        'title'  => __( 'Clear Cache', 'gamipress' ),
        'parent' => 'gamipress',
        'href'   => admin_url( 'admin.php?page=gamipress&gamipress-action=clear_cache' )
    ) );

}
add_action( 'admin_bar_menu', 'gamipress_admin_bar_submenu', 999 );

/**
 * Add Try AutomatorWP admin bar submenu
 *
 * @since 2.0.0
 *
 * @param object $wp_admin_bar The WordPress toolbar object
 */
function gamipress_try_automatorwp_admin_bar_submenu( $wp_admin_bar ) {

    // Bail if admin bar menu disabled
    if( ! gamipress_show_admin_bar_menu() ) {
        return;
    }

    if( class_exists( 'AutomatorWP' ) ) {
        return;
    }

    $badge = '<span class="gamipress-admin-menu-badge">' . __( 'New', 'gamipress' ) . '</span>';

    // Try AutomatorWP
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-try-automatorwp',
        'title'  => __( 'Try AutomatorWP!', 'gamipress' ) . $badge,
        'parent' => 'gamipress',
        'href'   => 'https://wordpress.org/plugins/automatorwp/'
    ) );

}
add_action( 'admin_bar_menu', 'gamipress_try_automatorwp_admin_bar_submenu', 999 );

/**
 * Register our custom columns
 *
 * @since   1.0.6
 * @updated 1.3.9.5 Added the thumbnail column
 *
 * @param $posts_columns
 * @param $post_type
 *
 * @return mixed
 */
function gamipress_posts_columns( $posts_columns, $post_type ) {

    if( ! in_array( $post_type, array( 'points-type', 'achievement-type', 'rank-type' ) ) ) {
        return $posts_columns;
    }

    // Switch Title to Singular Name
    $posts_columns['title'] = __( 'Singular Name', 'gamipress' );

    // Prepend the thumbnail column
    $chunks                 = array_chunk( $posts_columns, 1, true );
    $chunks[0]['thumbnail'] = __( 'Image', 'gamipress' );

    $posts_columns = call_user_func_array( 'array_merge', $chunks );

    // Try to place our column before date column
    $pos = array_search( 'date', array_keys( $posts_columns ) );

    if ( ! is_int( $pos ) ) {
        $pos = 1;
    }

    // Place our column in our desired position
    $chunks                     = array_chunk( $posts_columns, $pos, true );
    $chunks[0]['plural_name']   = __( 'Plural Name', 'gamipress' );
    $chunks[0]['post_name']     = __( 'Slug', 'gamipress' );

    return call_user_func_array( 'array_merge', $chunks );
}
add_filter( 'manage_posts_columns', 'gamipress_posts_columns', 10, 2 );

/**
 * Output for our custom columns
 *
 * @since   1.0.6
 * @updated 1.3.9.5 Added the thumbnail column output
 *
 * @param $column_name
 * @param $post_id
 */
function gamipress_posts_custom_columns( $column_name, $post_id ) {

    if( ! in_array( gamipress_get_post_type( $post_id ), array( 'points-type', 'achievement-type', 'rank-type' ) ) ) {
        return;
    }

    switch( $column_name ) {
        case 'thumbnail':
            $can_edit_post = current_user_can( 'edit_post', $post_id );

            if( $can_edit_post ) {
                printf(
                    '<a href="%s" aria-label="%s">%s</a>',
                    get_edit_post_link( $post_id ),
                    /* translators: %s: post title */
                    esc_attr( sprintf( __( '&#8220;%s&#8221; (Edit)' ), get_post_field( 'post_title', $post_id ) ) ),
                    get_the_post_thumbnail( $post_id, array( 32, 32 ) )
                );
            } else {
                echo get_the_post_thumbnail( $post_id, array( 32, 32 ) );
            }

            break;
        case 'plural_name':
            echo esc_html( gamipress_get_post_meta( $post_id, '_gamipress_plural_name' ) );
            break;
        case 'post_name':
            echo esc_html( get_post_field( 'post_name', $post_id ) );
            break;
    }
}
add_action( 'manage_posts_custom_column', 'gamipress_posts_custom_columns', 10, 2 );

/**
 * On delete our post, remove relationships
 *
 * @since 1.1.5
 *
 * @param integer $post_id
 */
function gamipress_on_delete_post( $post_id ) {
    global $wpdb;

    $post_type = gamipress_get_post_type( $post_id );

    $is_achievement = gamipress_is_achievement( $post_id );
    $is_rank = gamipress_is_rank( $post_id );

    $delete_user_earnings = ( in_array( $post_type, gamipress_get_requirement_types_slugs() ) );

    if( $post_type === 'achievement-type' || $post_type === 'rank-type' ) {
        // Remove all achievements of this achievement type or if is a rank type, remove all ranks of this rank type

        $dependent_type = get_post_field( 'post_name', $post_id );

        $posts = GamiPress()->db->posts;

        $dependents = $wpdb->get_results( $wpdb->prepare( "SELECT p.ID FROM {$posts} AS p WHERE  p.post_type = %s", $dependent_type ) );

        foreach( $dependents as $dependent ) {
            // Remove the achievement or rank of this type
            wp_delete_post( $dependent->ID );
        }
    } else if( $post_type === 'points-type' ) {

        // Get assigned points awards
        $points_awards = gamipress_get_points_type_points_awards( $post_id, 'any' );

        if( $points_awards ) {

            foreach( $points_awards as $points_award ) {
                // Remove the points award
                wp_delete_post( $points_award->ID );
            }

        }

        // Get assigned points deducts
        $points_deducts = gamipress_get_points_type_points_deducts( $post_id, 'any' );

        if( $points_deducts ) {

            foreach( $points_deducts as $points_deduct ) {
                // Remove the points deduct
                wp_delete_post( $points_deduct->ID );
            }

        }

    } else if( $is_achievement || $is_rank ) {
        // Remove steps/rank requirements assigned

        // Force to delete user earnings
        $delete_user_earnings = true;

        if( $is_rank ) {
            $requirement_type = 'rank-requirement';
        } else if ( $is_achievement ) {
            $requirement_type = 'step';
        }

        if( ! isset( $requirement_type ) ) {
            return;
        }

        // Get assigned requirements
        $assigned_requirements = gamipress_get_assigned_requirements( $post_id, $requirement_type, 'any' );

        if( $assigned_requirements ) {

            foreach( $assigned_requirements as $requirement ) {
                // Remove the requirement
                wp_delete_post( $requirement->ID );
            }

        }
    }

    // Delete assigned user earnings
    if( $delete_user_earnings ) {

        $user_earnings 		= GamiPress()->db->user_earnings;
        $user_earnings_meta = GamiPress()->db->user_earnings_meta;

        // Delete all user's earnings
        $wpdb->query( "DELETE ue FROM {$user_earnings} AS ue WHERE ue.post_id = {$post_id}" );
        // Delete orphaned user earnings metas
        $wpdb->query( "DELETE uem FROM {$user_earnings_meta} uem LEFT JOIN {$user_earnings} ue ON ue.user_earning_id = uem.user_earning_id WHERE ue.user_earning_id IS NULL" );
    }

}
add_action( 'delete_post', 'gamipress_on_delete_post' );

/**
 * Processes all GamiPress actions sent via POST and GET by looking for the 'gamipress-action' request and running do_action() to call the function
 *
 * @since 1.1.5
 */
function gamipress_process_actions() {

    // $_REQUEST
    if ( isset( $_REQUEST['gamipress-action'] ) ) {
        do_action( 'gamipress_action_request_' . $_REQUEST['gamipress-action'], $_REQUEST );
    }

    // $_POST
    if ( isset( $_POST['gamipress-action'] ) ) {
        do_action( 'gamipress_action_post_' . $_POST['gamipress-action'], $_POST );
    }

    // $_GET
    if ( isset( $_GET['gamipress-action'] ) ) {
        do_action( 'gamipress_action_get_' . $_GET['gamipress-action'], $_GET );
    }

}
add_action( 'admin_init', 'gamipress_process_actions' );

/**
 * Add custom footer text to the admin dashboard
 *
 * @since	    1.3.8.1
 *
 * @param       string $footer_text The existing footer text
 *
 * @return      string
 */
function gamipress_admin_footer_text( $footer_text ) {

    global $typenow;

    if (
        $typenow === 'points-type'
        || $typenow === 'achievement-type'
        || $typenow === 'rank-type'
        || in_array( $typenow, gamipress_get_achievement_types_slugs() )
        || in_array( $typenow, gamipress_get_rank_types_slugs() )
        || ( isset( $_GET['page'] ) && (
                $_GET['page'] === 'gamipress_settings'
                || $_GET['page'] === 'gamipress_logs'
                || $_GET['page'] === 'edit_gamipress_logs'
                || $_GET['page'] === 'gamipress_add_ons'
                || $_GET['page'] === 'gamipress_assets'
                || $_GET['page'] === 'gamipress_help_support'
                || $_GET['page'] === 'gamipress_tools'
            )
        )
    ) {

        $gamipress_footer_text = sprintf( __( 'Thank you for using <a href="%1$s" target="_blank">GamiPress</a>! Please leave us a <a href="%2$s" target="_blank">%3$s</a> rating on WordPress.org', 'gamipress' ),
            'https://gamipress.com',
            'https://wordpress.org/support/plugin/gamipress/reviews/?rate=5#new-post',
            '&#9733;&#9733;&#9733;&#9733;&#9733;'
        );

        return str_replace( '</span>', '', $footer_text ) . ' | ' . $gamipress_footer_text . '</span>';

    } else {

        return $footer_text;

    }

}
add_filter( 'admin_footer_text', 'gamipress_admin_footer_text' );