<?php
/**
 * Custom Tables - Ajax List Table
 *
 * @package      Custom_Tables\Ajax_List_Table
 * @author       GamiPress <contact@gamipress.com>, rubengc <rubengcdev@gamil.com>
 * @copyright    Copyright (c) GamiPress
 */

if ( ! class_exists( 'CT_Ajax_List_Table' ) ) :

    class CT_Ajax_List_Table {

        /**
         * @var         CT_Ajax_List_Table $instance The one true CT_Ajax_List_Table
         * @since       1.0.0
         */
        private static $instance;

        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      CT_Ajax_List_Table self::$instance The one true CT_Ajax_List_Table
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new CT_Ajax_List_Table();
                self::$instance->constants();
                self::$instance->includes();
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
            define( 'CT_AJAX_LIST_TABLE_VER', '1.0.1' );

            // Plugin file
            define( 'CT_AJAX_LIST_TABLE_FILE', __FILE__ );

            // Plugin path
            define( 'CT_AJAX_LIST_TABLE_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'CT_AJAX_LIST_TABLE_URL', plugin_dir_url( __FILE__ ) );
        }

        /**
         * Include plugin files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {

            require_once CT_AJAX_LIST_TABLE_DIR . 'includes/ajax-functions.php';
            require_once CT_AJAX_LIST_TABLE_DIR . 'includes/functions.php';
            require_once CT_AJAX_LIST_TABLE_DIR . 'includes/scripts.php';

        }

    }

    /**
     * The main function responsible for returning the one true CT_Ajax_List_Table instance to functions everywhere
     *
     * @since       1.0.0
     * @return      \CT_Ajax_List_Table The one true CT_Ajax_List_Table
     */
    function CT_Ajax_List_Table() {
        return CT_Ajax_List_Table::instance();
    }
    add_action( 'ct_init', 'CT_Ajax_List_Table' );

endif;
