<?php
/**
 * View class
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
         * @var string View args
         */
        protected $args = array();

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

        public function add_hooks() {

            // Note: sub-menus need to be registered after parent
            add_action( 'admin_menu', array( $this, 'admin_menu' ), empty( $this->args['parent_slug'] ) ? 10 : 11 );

            add_filter( 'screen_options_show_screen', array( $this, 'show_screen_options' ), 10, 2 );

            add_filter( 'screen_settings', array( $this, 'maybe_screen_settings' ), 10, 2 );

        }

        public function show_screen_options( $show_screen, $screen ) {

            $screen_slug = explode( '_page_', $screen->id );

            if( isset( $screen_slug[1] ) &&  $screen_slug[1] === $this->args['menu_slug'] ) {

                // TODO: Add more checks

                return true;
            }

            return $show_screen;
        }

        /**
         * Check if current screen is own.
         *
         * @param string    $screen_settings    Screen settings.
         * @param WP_Screen $screen             WP_Screen object.
         *
         * @return string   $screen_settings
         */
        public function maybe_screen_settings( $screen_settings, $screen ) {

            $screen_slug = explode( '_page_', $screen->id );

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
         * @param string    $screen_settings    Screen settings.
         * @param WP_Screen $screen             WP_Screen object.
         */
        public function screen_settings( $screen_settings, $screen ) {

        }

        /**
         * Create a new menu
         */
        public function admin_menu() {

            if( ! $this->args['show_in_menu'] ) {

                add_submenu_page( null, $this->args['page_title'], $this->args['menu_title'], $this->args['capability'], $this->args['menu_slug'], array( $this, 'render' ) );

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

        public function get_link() {
            return admin_url( "admin.php?page=" . $this->args['menu_slug'] );
        }

        public function render() {

            do_action( "ct_render_{$this->name}_view", $this );

        }

    }

endif;