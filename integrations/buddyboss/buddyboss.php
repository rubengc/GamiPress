<?php
/**
 * Plugin Name:           GamiPress - BuddyBoss integration
 * Plugin URI:            https://wordpress.org/plugins/gamipress-buddyboss-integration/
 * Description:           Connect GamiPress with BuddyBoss.
 * Version:               1.3.6
 * Author:                GamiPress
 * Author URI:            https://gamipress.com/
 * Text Domain:           gamipress-buddyboss-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.2
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               GamiPress\BuddyBoss
 * @author                GamiPress
 * @copyright             Copyright (c) GamiPress
 */

final class GamiPress_Integration_BuddyBoss {

    /**
     * @var         GamiPress_Integration_BuddyBoss $instance The one true GamiPress_Integration_BuddyBoss
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      GamiPress_Integration_BuddyBoss self::$instance The one true GamiPress_Integration_BuddyBoss
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new GamiPress_Integration_BuddyBoss();
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
        define( 'GAMIPRESS_BUDDYBOSS_VER', '1.3.6' );

        // Plugin file
        define( 'GAMIPRESS_BUDDYBOSS_FILE', __FILE__ );

        // Plugin path
        define( 'GAMIPRESS_BUDDYBOSS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'GAMIPRESS_BUDDYBOSS_URL', plugin_dir_url( __FILE__ ) );
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

            require_once GAMIPRESS_BUDDYBOSS_DIR . 'includes/listeners.php';
            require_once GAMIPRESS_BUDDYBOSS_DIR . 'includes/requirements.php';
            require_once GAMIPRESS_BUDDYBOSS_DIR . 'includes/rules-engine.php';
            require_once GAMIPRESS_BUDDYBOSS_DIR . 'includes/scripts.php';
            require_once GAMIPRESS_BUDDYBOSS_DIR . 'includes/triggers.php';

            if( ! class_exists( 'GamiPress_BuddyPress' ) ) {
                require_once GAMIPRESS_BUDDYBOSS_DIR . 'includes/community/loader.php';
            }

            if( ! class_exists( 'GamiPress_bbPress' ) ) {
                require_once GAMIPRESS_BUDDYBOSS_DIR . 'includes/forums/loader.php';
            }

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
     * Activation hook for the plugin.
     *
     * @since  1.0.0
     */
    public static function activate() {

        if ( ! class_exists( 'GamiPress' ) )
            return;

        GamiPress_Integration_BuddyBoss::instance();

        global $wpdb;

        // Get stored version
        $stored_version = get_option( 'gamipress_buddyboss_integration_version', '1.0.0' );

        if( function_exists('gamipress_is_network_wide_active') && gamipress_is_network_wide_active() ) {
            $gamipress_settings = get_site_option( 'gamipress_settings', array() );
        } else {
            $gamipress_settings = get_option( 'gamipress_settings', array() );
        }

        // GamiPress BuddyPress 1.1.2 upgrade
        if ( version_compare( $stored_version, '1.1.2', '<' ) ) {

            // Points tab setting
            $points_placement = ( isset( $gamipress_settings['bp_points_placement'] ) ? $gamipress_settings['bp_points_placement'] : '' );

            if( in_array( $points_placement, array( 'tab', 'both' ) ) && ! isset( $gamipress_settings['bp_points_tab'] ) ) {
                $gamipress_settings['bp_points_tab'] = 'on';
            }

            // Achievements tab setting
            $achievements_placement = ( isset( $gamipress_settings['bp_achievements_placement'] ) ? $gamipress_settings['bp_achievements_placement'] : '' );

            if( in_array( $achievements_placement, array( 'tab', 'both' ) ) && ! isset( $gamipress_settings['bp_achievements_tab'] ) ) {
                $gamipress_settings['bp_achievements_tab'] = 'on';
            }


            // Ranks tab setting
            $ranks_placement = ( isset( $gamipress_settings['bp_ranks_placement'] ) ? $gamipress_settings['bp_ranks_placement'] : '' );

            if( in_array( $ranks_placement, array( 'tab', 'both' ) ) && ! isset( $gamipress_settings['bp_ranks_tab'] ) ) {
                $gamipress_settings['bp_ranks_tab'] = 'on';
            }

            // Clone types and order settings
            foreach( array( 'points', 'achievements', 'ranks' ) as $key ) {
                if( ! isset( $gamipress_settings["bp_tab_{$key}_types"] ) ) {
                    $gamipress_settings["bp_tab_{$key}_types"] = $gamipress_settings["bp_members_{$key}_types"];
                    $gamipress_settings["bp_tab_{$key}_types_order"] = $gamipress_settings["bp_members_{$key}_types_order"];
                }
            }

            // Finally, update placement to the new options
            if( ! is_array( $gamipress_settings['bp_points_placement'] ) && in_array( $gamipress_settings['bp_points_placement'], array( 'top', 'both' ) ) ) {
                $gamipress_settings['bp_points_placement'] = array( 'top' );
            } else {
                $gamipress_settings['bp_points_placement'] = array();
            }

            if( ! is_array( $gamipress_settings['bp_achievements_placement'] ) && in_array( $gamipress_settings['bp_achievements_placement'], array( 'top', 'both' ) ) ) {
                $gamipress_settings['bp_achievements_placement'] = array( 'top' );
            } else {
                $gamipress_settings['bp_achievements_placement'] = array();
            }

            if( ! is_array( $gamipress_settings['bp_ranks_placement'] ) && in_array( $gamipress_settings['bp_ranks_placement'], array( 'top', 'both' ) ) ) {
                $gamipress_settings['bp_ranks_placement'] = array( 'top' );
            } else {
                $gamipress_settings['bp_ranks_placement'] = array();
            }

        }

        // Update GamiPress options
        if( function_exists('gamipress_is_network_wide_active') && gamipress_is_network_wide_active() ) {
            update_site_option( 'gamipress_settings', $gamipress_settings );
        } else {
            update_option( 'gamipress_settings', $gamipress_settings );
        }

        // Updated stored version
        update_option( 'gamipress_buddyboss_integration_version', GAMIPRESS_BUDDYBOSS_VER );

    }

    /**
     * Deactivation hook for the plugin.
     *
     * @since  1.0.0
     */
    public static function deactivate() {

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
            if( gamipress_is_plugin_active_on_network( 'buddyboss-platform/bp-loader.php' ) )
                return true;
        }

        if ( ! defined( 'BP_PLATFORM_VERSION' ) )
            return false;

        return true;

    }

}

/**
 * The main function responsible for returning the one true GamiPress_Integration_BuddyBoss instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress_Integration_BuddyBoss The one true GamiPress_Integration_BuddyBoss
 */
function GamiPress_Integration_BuddyBoss() {
    return GamiPress_Integration_BuddyBoss::instance();
}
add_action( 'gamipress_pre_init', 'GamiPress_Integration_BuddyBoss' );

// Setup our activation and deactivation hooks
register_activation_hook( __FILE__, array( 'GamiPress_Integration_BuddyBoss', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'GamiPress_Integration_BuddyBoss', 'deactivate' ) );
