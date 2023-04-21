<?php
/**
 * View class
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CT_View' ) ) :

    class CT_View {

        /**
         * @var string View name
         */
        protected $name = '';

        /**
         * @var array View args
         */
        protected $args = array();

        /**
         * CT_View constructor.
         *
         * @since 1.0.0
         *
         * @param string    $name
         * @param array     $args
         */
        public function __construct( $name, $args ) {

            $this->name = $name;

            $this->args = wp_parse_args( $args, array(
                'menu_title' => ucfirst( $this->name ),
                'page_title' => ucfirst( $this->name ),
                'menu_slug' => $this->name,
                'parent_slug' => '',
                'show_in_menu' => true,
                'menu_icon' => '',
                'menu_position' => null,
                'capability' => 'manage_options',
            ) );

            $this->add_hooks();

        }

        /**
         * View hooks (called on constructor).
         *
         * @since 1.0.0
         */
        public function add_hooks() {

            add_action( 'admin_init', array( $this, 'admin_init' ) );

            // Note: sub-menus need to be registered after parent
            add_action( 'admin_menu', array( $this, 'admin_menu' ), empty( $this->args['parent_slug'] ) ? 10 : 11 );

            add_filter( 'parent_file', array( $this, 'parent_file' ), 10 );

            add_filter( 'submenu_file', array( $this, 'submenu_file' ), 10, 2 );

            add_action( 'adminmenu', array( $this, 'restore_plugin_page' ), 10, 2 );

            add_filter( 'screen_options_show_screen', array( $this, 'show_screen_options' ), 10, 2 );

            add_filter( 'screen_settings', array( $this, 'maybe_screen_settings' ), 10, 2 );

            add_filter( 'admin_init', array( $this, 'maybe_set_screen_settings' ), 11 );
            add_filter( 'ct-set-screen-option', array( $this, 'set_screen_settings' ), 10, 3 );

        }

        public function show_screen_options( $show_screen, $screen ) {

            if( ! $this->is_current_view() ) {
                return $show_screen;
            }

            $screen_slug = explode( '_page_', $screen->id );

            if( isset( $screen_slug[1] ) &&  $screen_slug[1] === $this->args['menu_slug'] ) {
                return true;
            }

            return $show_screen;

        }

        /**
         * Check if current screen is own.
         *
         * @since 1.0.0
         *
         * @param string    $screen_settings    Screen settings.
         * @param WP_Screen $screen             WP_Screen object.
         *
         * @return string   $screen_settings
         */
        public function maybe_screen_settings( $screen_settings, $screen ) {

            if( ! $this->is_current_view() ) {
                return $screen_settings;
            }

            $screen_slug = explode( '_page_', $screen->id );

            // Check if current screen matches this menu slug
            if( isset( $screen_slug[1] ) &&  $screen_slug[1] === $this->args['menu_slug'] ) {

                global $ct_registered_tables, $ct_table;

                if( ! isset( $ct_registered_tables[$this->name] ) ) {
                    return $screen_settings;
                }

                // Set up global vars
                $ct_table = $ct_registered_tables[$this->name];

                ob_start();
                $this->screen_settings( $screen_settings, $screen );
                $screen_settings .= ob_get_clean();

            }

            return $screen_settings;

        }

        /**
         * Screen settings text displayed in the Screen Options tab.
         *
         * @since 1.0.0
         *
         * @param string    $screen_settings    Screen settings.
         * @param WP_Screen $screen             WP_Screen object.
         */
        public function screen_settings( $screen_settings, $screen ) {
            // Override
        }

        /**
         * Saves view options.
         *
         * Function based on set_screen_options()
         *
         * @since 1.0.0
         *
         * @see set_screen_options()
         */
        function maybe_set_screen_settings() {

            if( ! $this->is_current_view() ) {
                return;
            }

            if ( isset( $_POST['wp_screen_options'] ) && is_array( $_POST['wp_screen_options'] ) ) {
                check_admin_referer( 'screen-options-nonce', 'screenoptionnonce' );

                if ( ! $user = wp_get_current_user() ) {
                    return;
                }

                $option = $_POST['wp_screen_options']['option'];
                $value = $_POST['wp_screen_options']['value'];

                if ( $option != sanitize_key( $option ) ) {
                    return;
                }

                $option = str_replace('-', '_', $option);

                /**
                 * Filters a screen option value before it is set.
                 *
                 * The filter can also be used to modify non-standard [items]_per_page
                 * settings. See the parent function for a full list of standard options.
                 *
                 * Returning false to the filter will skip saving the current option.
                 *
                 * @since 1.0.0
                 *
                 * @see set_screen_options()
                 *
                 * @param bool|int $value  Screen option value. Default false to skip.
                 * @param string   $option The option name.
                 * @param int      $value  The number of rows to use.
                 */
                $value = apply_filters( 'ct-set-screen-option', false, $option, $value );

                if ( false === $value )
                    return;

                update_user_meta( $user->ID, $option, $value );

                $url = remove_query_arg( array( 'pagenum', 'apage', 'paged' ), wp_get_referer() );
                if ( isset( $_POST['mode'] ) ) {
                    $url = add_query_arg( array( 'mode' => $_POST['mode'] ), $url );
                }

                wp_safe_redirect( $url );
                exit;
            }
        }

        /**
         * Screen option value before it is set.
         *
         * The filter can also be used to modify non-standard [items]_per_page
         * settings. See the parent function for a full list of standard options.
         *
         * Returning false to the filter will skip saving the current option.
         *
         * @since 1.0.0
         *
         * @see set_screen_options()
         *
         * @param bool|int $value_to_set    Screen option value to set. Default false to skip.
         * @param string   $option          The option name.
         * @param int      $value           The option value.
         *
         * @return bool|mixed               False to skip or any other value to set as option value
         */
        public function set_screen_settings( $value_to_set, $option, $value ) {

            // Override

            return $value_to_set;

        }

        /**
         * Create a new menu
         *
         * @since 1.0.0
         */
        public function admin_menu() {

            // Override capability by the table capability
            $ct_table = ct_get_table_object( $this->name );

            if( ! empty( $ct_table->capability ) ) {
                $this->args['capability'] = $ct_table->capability;
            }

            if( ! $this->args['show_in_menu'] ) {

                add_submenu_page( '', $this->args['page_title'], $this->args['menu_title'], $this->args['capability'], $this->args['menu_slug'], array( $this, 'render' ) );

            } else {

                if( empty( $this->args['parent_slug'] ) ) {
                    // View menu
                    add_menu_page( $this->args['page_title'], $this->args['menu_title'], $this->args['capability'], $this->args['menu_slug'], array( $this, 'render' ), $this->args['menu_icon'], $this->args['menu_position'] );
                } else {
                    // View sub menu
                    add_submenu_page( $this->args['parent_slug'], $this->args['page_title'], $this->args['menu_title'], $this->args['capability'], $this->args['menu_slug'], array( $this, 'render' ) );
                }

            }

        }

        /**
         * Parent file fix when a view is registered in a submenu
         *
         * @since 1.0.0
         */
        public function parent_file( $parent_file ) {

            global $ct_table, $plugin_page;

            if( ! $this->is_current_view() ) {
                return $parent_file;
            }

            $list_view_args = $ct_table->views->list->args;

            // If not empty parent slug, override actual parent slug
            if( ! empty( $this->args['parent_slug'] ) ) {
                $parent_file = $this->args['parent_slug'];

                if( $this->args['menu_slug'] !== $list_view_args['menu_slug'] ) {
                    // Hack required to make parent file work because get overwritten on get_admin_page_parent() function
                    $plugin_page = null;
                }
            }

            // If we are on an add or edit view and list is displayed on menu, apply the list parent slug
            if( $this->args['menu_slug'] !== $list_view_args['menu_slug'] ) {

                if( $list_view_args['show_in_menu'] && ! empty( $list_view_args['parent_slug'] ) ) {
                    $parent_file = $list_view_args['parent_slug'];

                    // Hack required to make parent file work because get overwritten on get_admin_page_parent() function
                    $plugin_page = null;
                }

            }

            return $parent_file;

        }

        /**
         * Submenu file fix when a view is registered in a submenu
         *
         * @since 1.0.0
         */
        public function submenu_file( $submenu_file, $parent_file ) {

            global $ct_table;

            if( ! $this->is_current_view() ) {
                return $submenu_file;
            }

            $list_view_args = $ct_table->views->list->args;

            // If we are on an add or edit view and list is displayed on menu, then highlight list view
            if( $this->args['menu_slug'] !== $list_view_args['menu_slug'] ) {
                if( $list_view_args['show_in_menu'] && ! empty( $list_view_args['parent_slug'] ) ) {
                    $submenu_file = $list_view_args['menu_slug'];
                }
            }

            return $submenu_file;

        }

        /**
         * Restore global $plugin_page since it was required to get overwritten on parent_file()
         *
         * @since 1.0.0
         */
        public function restore_plugin_page() {

            global $ct_table, $plugin_page;

            if( ! $this->is_current_view() ) {
                return;
            }

            $list_view_args = $ct_table->views->list->args;

            // If we are on an add or edit view and list is displayed on menu, restore plugin page too
            if( $this->args['menu_slug'] !== $list_view_args['menu_slug'] ) {

                // If not empty parent slug then restore plugin page
                if( ! empty( $this->args['parent_slug'] ) ) {
                    $plugin_page = $this->args['menu_slug'];
                }

                if( $list_view_args['show_in_menu'] && ! empty( $list_view_args['parent_slug'] ) ) {
                    $plugin_page = $this->args['menu_slug'];
                }
            }

        }

        public function is_current_view() {

            global $ct_registered_tables, $pagenow;

            if( $pagenow !== 'admin.php' ) {
                return false;
            }

            if( ! isset( $_GET['page'] ) ) {
                return false;
            }

            if( empty( $_GET['page'] ) || $_GET['page'] !== $this->args['menu_slug'] ) {
                return false;
            }

            if( ! isset( $ct_registered_tables[$this->name] ) ) {
                return false;
            }

            return true;
        }

        public function get_slug() {
            return $this->args['menu_slug'];
        }

        public function get_link() {
            return admin_url( "admin.php?page=" . $this->args['menu_slug'] );
        }

        /**
         * View admin init.
         *
         * This function is called on admin_init hook.
         * Includes some checks to determine if the init() function should be called.
         *
         * @since 1.0.0
         */
        public function admin_init() {

            global $ct_registered_tables, $ct_table;

            if( ! $this->is_current_view() ) {
                return;
            }

            // Setup the global CT_Table object for this screen
            $ct_table = $ct_registered_tables[$this->name];

            // Run the init function
            $this->init();

        }

        /**
         * View init.
         *
         * Run redirects here to avoid "headers already sent" error.
         *
         * @since 1.0.0
         */
        public function init() {

            do_action( "ct_init_{$this->name}_view", $this );

        }

        /**
         * View content.
         *
         * @since 1.0.0
         */
        public function render() {

            do_action( "ct_render_{$this->name}_view", $this );

        }

    }

endif;