<?php
/**
 * Plugin Name:           GamiPress - Thrive Quiz Builder integration
 * Plugin URI:            https://wordpress.org/plugins/gamipress-thrive-quiz-builder-integration/
 * Description:           Connect GamiPress with Thrive Quiz Builder.
 * Version:               1.0.0
 * Author:                GamiPress
 * Author URI:            https://gamipress.com/
 * Text Domain:           gamipress-thrive-quiz-builder-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.2
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               GamiPress\Thrive_Quiz_Builder
 * @author                GamiPress
 * @copyright             Copyright (c) GamiPress
 */

final class GamiPress_Integration_Thrive_Quiz_Builder {

    /**
     * @var         GamiPress_Integration_Thrive_Quiz_Builder $instance The one true GamiPress_Integration_Thrive_Quiz_Builder
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      GamiPress_Integration_Thrive_Quiz_Builder self::$instance The one true GamiPress_Integration_Thrive_Quiz_Builder
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new GamiPress_Integration_Thrive_Quiz_Builder();
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
        define( 'GAMIPRESS_THRIVE_QUIZ_BUILDER_VER', '1.0.0' );

        // Plugin path
        define( 'GAMIPRESS_THRIVE_QUIZ_BUILDER_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_THRIVE_QUIZ_BUILDER_URL', plugin_dir_url( __FILE__ ) );
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

            require_once GAMIPRESS_THRIVE_QUIZ_BUILDER_DIR . 'includes/admin.php';
            require_once GAMIPRESS_THRIVE_QUIZ_BUILDER_DIR . 'includes/functions.php';
            require_once GAMIPRESS_THRIVE_QUIZ_BUILDER_DIR . 'includes/listeners.php';
            require_once GAMIPRESS_THRIVE_QUIZ_BUILDER_DIR . 'includes/requirements.php';
            require_once GAMIPRESS_THRIVE_QUIZ_BUILDER_DIR . 'includes/rules-engine.php';
            require_once GAMIPRESS_THRIVE_QUIZ_BUILDER_DIR . 'includes/scripts.php';
            require_once GAMIPRESS_THRIVE_QUIZ_BUILDER_DIR . 'includes/triggers.php';

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
            if( gamipress_is_plugin_active_on_network( 'thrive-quiz-builder/thrive-quiz-builder.php' ) )
                return true;
        }

        if ( ! class_exists( 'Thrive_Quiz_Builder' ) )
            return false;

        return true;

    }

}

/**
 * The main function responsible for returning the one true GamiPress_Integration_Thrive_Quiz_Builder instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Integration_Thrive_Quiz_Builder The one true GamiPress_Integration_Thrive_Quiz_Builder
 */
function GamiPress_Integration_Thrive_Quiz_Builder() {
    return GamiPress_Integration_Thrive_Quiz_Builder::instance();
}
add_action( 'gamipress_pre_init', 'GamiPress_Integration_Thrive_Quiz_Builder' );

