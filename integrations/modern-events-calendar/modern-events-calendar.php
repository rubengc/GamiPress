<?php
/**
 * Plugin Name:           GamiPress - Modern Events Calendar integration
 * Plugin URI:            https://wordpress.org/plugins/gamipress-modern-events-calendar-integration/
 * Description:           Connect GamiPress with Modern Events Calendar.
 * Version:               1.0.0
 * Author:                GamiPress
 * Author URI:            https://gamipress.com/
 * Text Domain:           gamipress-modern-events-calendar-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.2
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               GamiPress\Modern_Events_Calendar
 * @author                GamiPress
 * @copyright             Copyright (c) GamiPress
 */

final class GamiPress_Integration_Modern_Events_Calendar {

    /**
     * @var         GamiPress_Integration_Modern_Events_Calendar $instance The one true GamiPress_Integration_Modern_Events_Calendar
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      GamiPress_Integration_Modern_Events_Calendar self::$instance The one true GamiPress_Integration_Modern_Events_Calendar
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new GamiPress_Integration_Modern_Events_Calendar();
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
        define( 'GAMIPRESS_MODERN_EVENTS_CALENDAR_VER', '1.0.0' );

        // Plugin path
        define( 'GAMIPRESS_MODERN_EVENTS_CALENDAR_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_MODERN_EVENTS_CALENDAR_URL', plugin_dir_url( __FILE__ ) );
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

            require_once GAMIPRESS_MODERN_EVENTS_CALENDAR_DIR . 'includes/listeners.php';
            require_once GAMIPRESS_MODERN_EVENTS_CALENDAR_DIR . 'includes/triggers.php';

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
            if( gamipress_is_plugin_active_on_network( 'modern-events-calendar-lite/modern-events-calendar-lite.php' )
                || gamipress_is_plugin_active_on_network( 'modern-events-calendar/modern-events-calendar.php' ) )
                return true;
        }

        if ( ! class_exists( 'MEC' ) )
            return false;

        return true;

    }

}

/**
 * The main function responsible for returning the one true GamiPress_Integration_Modern_Events_Calendar instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Integration_Modern_Events_Calendar The one true GamiPress_Integration_Modern_Events_Calendar
 */
function GamiPress_Integration_Modern_Events_Calendar() {
    return GamiPress_Integration_Modern_Events_Calendar::instance();
}
add_action( 'gamipress_pre_init', 'GamiPress_Integration_Modern_Events_Calendar' );

