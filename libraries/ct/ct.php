<?php
/**
 * Custom Tables main class
 *
 * @since 1.0.0
 *
 * @package      Custom_Tables
 * @author       GamiPress <contact@gamipress.com>, rubengc <rubengcdev@gamil.com>
 * @copyright    Copyright (c) GamiPress
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CT' ) ) :

    final class CT {

        /**
         * @var         CT $instance The one true CT
         * @since       1.0.0
         */
        private static $instance;

        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true CT
         */
        public static function instance() {

            if( ! self::$instance ) {

                self::$instance = new CT();
                self::$instance->constants();
                self::$instance->includes();
                self::$instance->compatibility();
                self::$instance->hooks();
                self::$instance->load_textdomain();

            }

            return self::$instance;

        }

        /**
         * Setup CT constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function constants() {

            // Version
            define( 'CT_VER', '1.0.0' );

            // File
            define( 'CT_FILE', __FILE__ );

            // Path
            define( 'CT_DIR', plugin_dir_path( __FILE__ ) );

            // URL
            define( 'CT_URL', plugin_dir_url( __FILE__ ) );

            // Debug
            define( 'CT_DEBUG', false );

        }

        /**
         * Include CT files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {

            // WP_List_Table dependencies
            if( ! function_exists( 'convert_to_screen' ) ) {
                require_once ABSPATH . 'wp-admin/includes/template.php';
            }

            if( ! function_exists( 'get_column_headers' ) ) {
                require_once ABSPATH . 'wp-admin/includes/screen.php';
            }

            // Includes required WP_List_Table class
            if( ! class_exists( 'WP_List_Table' ) ) {
                require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
            }

            require_once CT_DIR . 'includes/class-ct-table.php';
            require_once CT_DIR . 'includes/class-ct-table-meta.php';
            require_once CT_DIR . 'includes/class-ct-database.php';
            require_once CT_DIR . 'includes/class-ct-database-schema.php';
            require_once CT_DIR . 'includes/class-ct-query.php';
            require_once CT_DIR . 'includes/class-ct-list-table.php';
            require_once CT_DIR . 'includes/class-ct-view.php';
            require_once CT_DIR . 'includes/class-ct-list-view.php';
            require_once CT_DIR . 'includes/class-ct-edit-view.php';

            require_once CT_DIR . 'includes/functions.php';

        }

        /**
         * Include CT compatibility files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function compatibility() {

            require_once CT_DIR . 'compatibility/cmb2.php';

        }

        /**
         * Setup CT hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {

            add_action( 'init', array( $this, 'init' ), 1 );

        }

        public function init() {

            // Setup role caps for CT capabilities
            ct_populate_roles();

            // Trigger CT init hook
            do_action( 'ct_init' );

            if( is_admin() ) {

                // Trigger CT admin init hook
                do_action( 'ct_admin_init' );

            }

        }

        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = CT_DIR . '/languages/';
            $lang_dir = apply_filters( 'ct_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'ct' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'ct', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/ct/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/ct/ folder
                load_textdomain( 'ct', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/ct/languages/ folder
                load_textdomain( 'ct', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'ct', false, $lang_dir );
            }
        }

    }


    /**
     * The main function responsible for returning the one true CT instance to functions everywhere
     *
     * @since       1.0.0
     * @return      \CT The one true CT
     */
    function ct() {
        return CT::instance();
    }

    ct();

endif;