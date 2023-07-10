<?php
/**
 * Plugin Name:           GamiPress - Upsell Plugin integration
 * Plugin URI:            https://wordpress.org/plugins/gamipress-upsell-plugin-integration/
 * Description:           Connect GamiPress with Upsell Plugin.
 * Version:               1.0.0
 * Author:                GamiPress
 * Author URI:            https://gamipress.com/
 * Text Domain:           gamipress-upsell-plugin-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.2
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               GamiPress\Upsell_Plugin
 * @author                GamiPress
 * @copyright             Copyright (c) GamiPress
 */

final class GamiPress_Integration_Upsell_Plugin {

    /**
     * @var         GamiPress_Integration_Upsell_Plugin $instance The one true GamiPress_Integration_Upsell_Plugin
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      GamiPress_Integration_Upsell_Plugin self::$instance The one true GamiPress_Integration_Upsell_Plugin
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new GamiPress_Integration_Upsell_Plugin();
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
        define( 'GAMIPRESS_UPSELL_PLUGIN_VER', '1.0.0' );

        // Plugin path
        define( 'GAMIPRESS_UPSELL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_UPSELL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
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

            require_once GAMIPRESS_UPSELL_PLUGIN_DIR . 'includes/admin.php';
            require_once GAMIPRESS_UPSELL_PLUGIN_DIR . 'includes/listeners.php';
            require_once GAMIPRESS_UPSELL_PLUGIN_DIR . 'includes/triggers.php';

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
            if( gamipress_is_plugin_active_on_network( 'upsell/plugin.php' ) )
                return true;
        }

        if ( ! function_exists( 'upsell' ) )
            return false;

        return true;

    }

}

/**
 * The main function responsible for returning the one true GamiPress_Integration_Upsell_Plugin instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Integration_Upsell_Plugin The one true GamiPress_Integration_Upsell_Plugin
 */
function GamiPress_UPSELL_PLUGIN() {
    return GamiPress_Integration_Upsell_Plugin::instance();
}
add_action( 'gamipress_pre_init', 'GamiPress_UPSELL_PLUGIN' );
