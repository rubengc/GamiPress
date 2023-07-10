<?php
/**
 * Plugin Name:           GamiPress - Fluent Support integration
 * Plugin URI:            https://wordpress.org/plugins/gamipress-fluent-support-integration/
 * Description:           Connect GamiPress with Fluent Support.
 * Version:               1.0.0
 * Author:                GamiPress
 * Author URI:            https://gamipress.com/
 * Text Domain:           gamipress-fluent-support-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.2
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               GamiPress\Fluent_Support
 * @author                GamiPress
 * @copyright             Copyright (c) GamiPress
 */

final class GamiPress_Integration_Fluent_Support {

    /**
     * @var         GamiPress_Integration_Fluent_Support $instance The one true GamiPress_Integration_Fluent_Support
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      GamiPress_Integration_Fluent_Support self::$instance The one true GamiPress_Integration_Fluent_Support
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new GamiPress_Integration_Fluent_Support();
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
        define( 'GAMIPRESS_FLUENT_SUPPORT_VER', '1.0.0' );

        // Plugin path
        define( 'GAMIPRESS_FLUENT_SUPPORT_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_FLUENT_SUPPORT_URL', plugin_dir_url( __FILE__ ) );
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

            require_once GAMIPRESS_FLUENT_SUPPORT_DIR . 'includes/listeners.php';
            require_once GAMIPRESS_FLUENT_SUPPORT_DIR . 'includes/triggers.php';

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

        if ( ! class_exists( 'GamiPress' ) )
            return false;

        // Requirements on multisite install
        if( is_multisite() && is_main_site() && function_exists('gamipress_is_network_wide_active') && gamipress_is_network_wide_active() ) {
            // On main site, need to check if integrated plugin is installed on any sub site to load all configuration files
            if( gamipress_is_plugin_active_on_network( 'fluent-support/fluent-support.php' ) )
                return true;
        }

        if ( ! defined( 'FLUENT_SUPPORT_VERSION' ) )
            return false;

        return true;

    }

}

/**
 * The main function responsible for returning the one true GamiPress_Integration_Fluent_Support instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Integration_Fluent_Support The one true GamiPress_Integration_Fluent_Support
 */
function GamiPress_Integration_Fluent_Support() {
    return GamiPress_Integration_Fluent_Support::instance();
}
add_action( 'gamipress_pre_init', 'GamiPress_Integration_Fluent_Support' );

