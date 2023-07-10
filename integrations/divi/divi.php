<?php
/**
 * Plugin Name:           GamiPress - Divi integration
 * Plugin URI:            https://wordpress.org/plugins/gamipress-divi-integration/
 * Description:           Connect GamiPress with Divi.
 * Version:               1.0.0
 * Author:                GamiPress
 * Author URI:            https://gamipress.com/
 * Text Domain:           gamipress-divi-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.2
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               GamiPress\Divi
 * @author                GamiPress
 * @copyright             Copyright (c) GamiPress
 */

final class GamiPress_Integration_Divi {

    /**
     * @var         GamiPress_Integration_Divi $instance The one true GamiPress_Integration_Divi
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      GamiPress_Integration_Divi self::$instance The one true GamiPress_Integration_Divi
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new GamiPress_Integration_Divi();
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
        define( 'GAMIPRESS_DIVI_VER', '1.0.0' );

        // Plugin path
        define( 'GAMIPRESS_DIVI_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_DIVI_URL', plugin_dir_url( __FILE__ ) );
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

            require_once GAMIPRESS_DIVI_DIR . 'includes/admin.php';
            require_once GAMIPRESS_DIVI_DIR . 'includes/listeners.php';
            require_once GAMIPRESS_DIVI_DIR . 'includes/requirements.php';
            require_once GAMIPRESS_DIVI_DIR . 'includes/rules-engine.php';
            require_once GAMIPRESS_DIVI_DIR . 'includes/scripts.php';
            require_once GAMIPRESS_DIVI_DIR . 'includes/triggers.php';

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

        $theme = wp_get_theme();

        if ( $theme->get_template() !== 'Divi' ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true GamiPress_Integration_Divi instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Integration_Divi The one true GamiPress_Integration_Divi
 */
function GamiPress_Integration_Divi() {
    return GamiPress_Integration_Divi::instance();
}
add_action( 'gamipress_pre_init', 'GamiPress_Integration_Divi' );
