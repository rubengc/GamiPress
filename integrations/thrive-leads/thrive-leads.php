<?php
/**
 * Plugin Name:           GamiPress - Thrive Leads integration
 * Plugin URI:            https://wordpress.org/plugins/gamipress-thrive-leads-integration/
 * Description:           Connect GamiPress with Thrive Leads.
 * Version:               1.0.0
 * Author:                GamiPress
 * Author URI:            https://gamipress.com/
 * Text Domain:           gamipress-thrive-leads-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.2
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               GamiPress\Thrive_Leads
 * @author                GamiPress
 * @copyright             Copyright (c) GamiPress
 */

final class GamiPress_Integration_Thrive_Leads {

    /**
     * @var         GamiPress_Integration_Thrive_Leads $instance The one true GamiPress_Integration_Thrive_Leads
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      GamiPress_Integration_Thrive_Leads self::$instance The one true GamiPress_Integration_Thrive_Leads
     */
    public static function instance() {

        if( !self::$instance ) {

            self::$instance = new GamiPress_Integration_Thrive_Leads();
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
        define( 'GAMIPRESS_THRIVE_LEADS_VER', '1.0.6' );

        // Plugin path
        define( 'GAMIPRESS_THRIVE_LEADS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_THRIVE_LEADS_URL', plugin_dir_url( __FILE__ ) );

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

            require_once GAMIPRESS_THRIVE_LEADS_DIR . 'includes/admin.php';
            require_once GAMIPRESS_THRIVE_LEADS_DIR . 'includes/functions.php';
            require_once GAMIPRESS_THRIVE_LEADS_DIR . 'includes/listeners.php';
            require_once GAMIPRESS_THRIVE_LEADS_DIR . 'includes/triggers.php';

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

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( ! class_exists( 'GamiPress' ) ) {
            return false;
        }

        // Requirements on multisite install
        if( is_multisite() && gamipress_is_network_wide_active() && is_main_site() ) {

            // On main site, need to check if integrated plugin is installed on any sub site to load all configuration files
            if( gamipress_is_plugin_active_on_network( 'thrive-leads/thrive-leads.php' ) ) {
                return true;
            }

        }

        if ( ! defined( 'TVE_LEADS_PATH' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true GamiPress_Integration_Thrive_Leads instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Integration_Thrive_Leads The one true GamiPress_Integration_Thrive_Leads
 */
function GamiPress_Integration_Thrive_Leads() {
    return GamiPress_Integration_Thrive_Leads::instance();
}
add_action( 'gamipress_pre_init', 'GamiPress_Integration_Thrive_Leads' );
