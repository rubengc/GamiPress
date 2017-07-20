<?php
/**
 * Admin
 *
 * @package     GamiPress\Admin
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_DIR . 'includes/admin/settings.php';
require_once GAMIPRESS_DIR . 'includes/admin/support.php';
require_once GAMIPRESS_DIR . 'includes/admin/meta-boxes.php';
require_once GAMIPRESS_DIR . 'includes/admin/points-awards-ui.php';
require_once GAMIPRESS_DIR . 'includes/admin/steps-ui.php';
require_once GAMIPRESS_DIR . 'includes/admin/log-extra-data-ui.php';

/**
 * Create GamiPress Settings menus
 */
function gamipress_admin_menu() {

    // Set minimum role setting for menus
    $minimum_role = gamipress_get_manager_capability();

    // Achievement types
    $achievement_types = gamipress_get_achievement_types();

    // Achievements menu
    if( ! empty( $achievement_types ) ) {
        add_menu_page( __( 'Achivements', 'gamipress' ), __( 'Achievements', 'gamipress' ), $minimum_role, 'gamipress_achievements', 'gamipress_achievements', 'dashicons-awards', 54 );
    }

    // GamiPress menu
    //add_menu_page( 'GamiPress', 'GamiPress', $minimum_role, 'gamipress', 'gamipress_settings', 'dashicons-awards', 55 );
    add_menu_page( 'GamiPress', 'GamiPress', $minimum_role, 'gamipress', 'gamipress_settings', 'dashicons-gamipress', 55 );

    // GamiPress sub menu
    add_submenu_page( 'gamipress', __( 'GamiPress Settings', 'gamipress' ), __( 'Settings', 'gamipress' ), $minimum_role, 'gamipress_settings', 'gamipress_settings_page' );
    add_submenu_page( 'gamipress', __( 'Help / Support', 'gamipress' ), __( 'Help / Support', 'gamipress' ), $minimum_role, 'gamipress_sub_help_support', 'gamipress_help_support_page' );

}
add_action( 'admin_menu', 'gamipress_admin_menu' );