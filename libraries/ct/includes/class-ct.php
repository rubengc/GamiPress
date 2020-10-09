<?php
/**
 * Custom Tables main class
 *
 * @since 1.0.0
 *
 * @package      Custom_Tables
 * @author       GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 * @copyright    Copyright (c) GamiPress
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/*
 * Copyright (c) GamiPress (contact@gamipress.com), Ruben Garcia (rubengcdev@gmail.com)
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General
 * Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

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
                self::$instance->includes();
                self::$instance->compatibility();
                self::$instance->hooks();
                self::$instance->load_textdomain();

            }

            return self::$instance;

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

            // CT_Table and CT_Table_Meta classes
            require_once CT_DIR . 'includes/class-ct-table.php';
            require_once CT_DIR . 'includes/class-ct-table-meta.php';
            // Database and schema related classes
            require_once CT_DIR . 'includes/class-ct-database.php';
            require_once CT_DIR . 'includes/class-ct-database-schema.php';
            require_once CT_DIR . 'includes/class-ct-database-schema-updater.php';
            // Rest API
            require_once CT_DIR . 'includes/class-ct-rest-controller.php';
            require_once CT_DIR . 'includes/class-ct-rest-meta-fields.php';
            // CT_Query and CT_List_Table classes
            require_once CT_DIR . 'includes/class-ct-query.php';
            require_once CT_DIR . 'includes/class-ct-list-table.php';
            // Views (List and edit)
            require_once CT_DIR . 'includes/class-ct-view.php';
            require_once CT_DIR . 'includes/class-ct-list-view.php';
            require_once CT_DIR . 'includes/class-ct-edit-view.php';
            // Rest of includes
            require_once CT_DIR . 'includes/functions.php';
            require_once CT_DIR . 'includes/hooks.php';

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

            add_action( 'plugins_loaded', array( $this, 'init' ), 11 );
            add_action( 'after_setup_theme', array( $this, 'init' ), 11 );

        }

        public function init() {

            if ( did_action( 'ct_init' ) ) {
                return;
            }

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