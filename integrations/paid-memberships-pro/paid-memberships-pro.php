<?php
/**
 * Plugin Name:           GamiPress - Paid Memberships Pro integration
 * Plugin URI:            https://wordpress.org/plugins/gamipress-paid-memberships-pro-integration/
 * Description:           Connect GamiPress with Paid Memberships Pro.
 * Version:               1.0.5
 * Author:                GamiPress
 * Author URI:            https://gamipress.com/
 * Text Domain:           gamipress-paid-memberships-pro-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.2
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               GamiPress\Paid_Memberships_Pro
 * @author                GamiPress
 * @copyright             Copyright (c) GamiPress
 */

final class GamiPress_Integration_Paid_Memberships_Pro {

    /**
     * @var         GamiPress_Integration_Paid_Memberships_Pro $instance The one true GamiPress_Integration_Paid_Memberships_Pro
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      GamiPress_Integration_Paid_Memberships_Pro self::$instance The one true GamiPress_Integration_Paid_Memberships_Pro
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new GamiPress_Integration_Paid_Memberships_Pro();
            self::$instance->constants();
            self::$instance->includes();
            self::$instance->hooks();
            
        }

        return self::$instance;
    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {
        // Plugin version
        define( 'GAMIPRESS_PAID_MEMBERSHIPS_PRO_VER', '1.0.5' );

        // Plugin path
        define( 'GAMIPRESS_PAID_MEMBERSHIPS_PRO_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_PAID_MEMBERSHIPS_PRO_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() ) {

            require_once GAMIPRESS_PAID_MEMBERSHIPS_PRO_DIR . 'includes/admin.php';
            require_once GAMIPRESS_PAID_MEMBERSHIPS_PRO_DIR . 'includes/functions.php';
            require_once GAMIPRESS_PAID_MEMBERSHIPS_PRO_DIR . 'includes/listeners.php';
            require_once GAMIPRESS_PAID_MEMBERSHIPS_PRO_DIR . 'includes/triggers.php';

        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {
        // Setup our activation and deactivation hooks
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        
    }

    /**
     * Activation hook for the plugin.
     *
     * @since  1.0.0
     */
    function activate() {

        if( $this->meets_requirements() ) {

        }

    }

    /**
     * Deactivation hook for the plugin.
     *
     * @since  1.0.0
     */
    function deactivate() {

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( ! class_exists( 'GamiPress' ) )
            return false;

        // Requirements on multisite install
        if( is_multisite() && is_main_site() && function_exists('gamipress_is_network_wide_active') && gamipress_is_network_wide_active() ) {
            // On main site, need to check if integrated plugin is installed on any sub site to load all configuration files
            if( gamipress_is_plugin_active_on_network( 'paid-memberships-pro/paid-memberships-pro.php' ) )
                return true;
        }

        if ( ! function_exists( 'pmpro_init' ) )
            return false;

        return true;

    }

}

/**
 * The main function responsible for returning the one true GamiPress_Integration_Paid_Memberships_Pro instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Integration_Paid_Memberships_Pro The one true GamiPress_Integration_Paid_Memberships_Pro
 */
function GamiPress_PAID_MEMBERSHIPS_PRO() {
    return GamiPress_Integration_Paid_Memberships_Pro::instance();
}
add_action( 'gamipress_pre_init', 'GamiPress_PAID_MEMBERSHIPS_PRO' );
