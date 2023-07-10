<?php
/**
 * Plugin Name:             GamiPress - Youtube integration
 * Plugin URI:              https://wordpress.org/plugins/gamipress-youtube-integration
 * Description:             Connect GamiPress with Youtube.
 * Version:                 1.0.8
 * Author:                  GamiPress
 * Author URI:              https://gamipress.com/
 * Text Domain:             gamipress-youtube-integration
 * Domain Path:             /languages/
 * Requires at least:       4.4
 * Tested up to:            6.1
 * License:                 GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package                 GamiPress\Youtube
 * @author                  GamiPress
 * @copyright               Copyright (c) GamiPress
 */

final class GamiPress_Integration_Youtube {

    /**
     * @var         GamiPress_Integration_Youtube $instance The one true GamiPress_Integration_Youtube
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      object self::$instance The one true GamiPress_Integration_Youtube
     */
    public static function instance() {

        if( !self::$instance ) {

            self::$instance = new GamiPress_Integration_Youtube();
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
        define( 'GAMIPRESS_YOUTUBE_VER', '1.0.8' );

        // Plugin file
        define( 'GAMIPRESS_YOUTUBE_FILE', __FILE__ );

        // Plugin path
        define( 'GAMIPRESS_YOUTUBE_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_YOUTUBE_URL', plugin_dir_url( __FILE__ ) );
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

            require_once GAMIPRESS_YOUTUBE_DIR . 'includes/admin.php';
            require_once GAMIPRESS_YOUTUBE_DIR . 'includes/functions.php';
            require_once GAMIPRESS_YOUTUBE_DIR . 'includes/listeners.php';
            require_once GAMIPRESS_YOUTUBE_DIR . 'includes/requirements.php';
            require_once GAMIPRESS_YOUTUBE_DIR . 'includes/rules-engine.php';
            require_once GAMIPRESS_YOUTUBE_DIR . 'includes/scripts.php';
            require_once GAMIPRESS_YOUTUBE_DIR . 'includes/shortcodes.php';
            require_once GAMIPRESS_YOUTUBE_DIR . 'includes/triggers.php';
            require_once GAMIPRESS_YOUTUBE_DIR . 'includes/widgets.php';

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

        return true;

    }

}

/**
 * The main function responsible for returning the one true GamiPress_Integration_Youtube instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Integration_Youtube The one true GamiPress_Integration_Youtube
 */
function GamiPress_Integration_Youtube() {
    return GamiPress_Integration_Youtube::instance();
}
add_action( 'gamipress_pre_init', 'GamiPress_Integration_Youtube' );
