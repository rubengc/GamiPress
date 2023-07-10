<?php
/**
 * Plugin Name:           GamiPress - WP-Polls integration
 * Plugin URI:            https://wordpress.org/plugins/gamipress-wp-polls-integration/
 * Description:           Connect GamiPress with WP-Polls.
 * Version:               1.0.0
 * Author:                GamiPress
 * Author URI:            https://gamipress.com/
 * Text Domain:           gamipress-wp-polls-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.2
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               GamiPress\WP_Polls
 * @author                GamiPress
 * @copyright             Copyright (c) GamiPress
 */

final class GamiPress_Integration_WP_Polls {

    /**
     * @var         GamiPress_Integration_WP_Polls $instance The one true GamiPress_Integration_WP_Polls
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      object self::$instance The one true GamiPress_Integration_WP_Polls
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new GamiPress_Integration_WP_Polls();
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
        define( 'GAMIPRESS_WP_POLLS_VER', '1.0.0' );

        // Plugin path
        define( 'GAMIPRESS_WP_POLLS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_WP_POLLS_URL', plugin_dir_url( __FILE__ ) );
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

            require_once GAMIPRESS_WP_POLLS_DIR . 'includes/admin.php';
            require_once GAMIPRESS_WP_POLLS_DIR . 'includes/functions.php';
            require_once GAMIPRESS_WP_POLLS_DIR . 'includes/listeners.php';
            require_once GAMIPRESS_WP_POLLS_DIR . 'includes/triggers.php';

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

        if ( ! class_exists( 'GamiPress' ) ) {
            return false;
        }

        // Multisite feature requires GamiPress 1.4.8
        if( is_multisite() ) {

            // Requirements on multisite install
            if( is_main_site() && function_exists('gamipress_is_network_wide_active') && gamipress_is_network_wide_active() ) {

                // On main site, need to check if integrated plugin is installed on any sub site to load all configuration files
                if( gamipress_is_plugin_active_on_network( 'wp-polls/wp-polls.php' ) ) {
                    return true;
                }

            }

        }

        if ( ! function_exists( 'vote_poll' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true GamiPress_Integration_WP_Polls instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Integration_WP_Polls The one true GamiPress_Integration_WP_Polls
 */
function GamiPress_Integration_WP_Polls() {
    return GamiPress_Integration_WP_Polls::instance();
}
add_action( 'gamipress_pre_init', 'GamiPress_Integration_WP_Polls' );
