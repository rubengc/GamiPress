<?php
/**
 * Table class
 *
 * Based on WP_Post_Type class
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CT_Table' ) ) :

    class CT_Table {

        /**
         * Table key.
         *
         * @since 1.0.0
         * @access public
         * @var string $name
         */
        public $name;

        /**
         * Table database.
         *
         * @since 1.0.0
         * @access public
         * @var CT_DataBase $db
         */
        public $db;

        /**
         * Table ciews.
         *
         * @since 1.0.0
         * @access public
         * @var stdClass $views
         */
        public $views;

        /**
         * Table Meta (if supports contains 'meta').
         *
         * @since 1.0.0
         * @access public
         * @var CT_Table_Meta $meta
         */
        public $meta;

        /**
         * Table key.
         *
         * @since 1.0.0
         * @access public
         * @var string $name
         */
        public $singular;

        /**
         * Table key.
         *
         * @since 1.0.0
         * @access public
         * @var string $name
         */
        public $plural;

        /**
         * Name of the post type shown in the menu. Usually plural.
         *
         * @since 1.0.0
         * @access public
         * @var string $label
         */
        public $label;

        /**
         * Labels object for this post type.
         *
         * If not set, post labels are inherited for non-hierarchical types
         * and page labels for hierarchical ones.
         *
         * @see get_post_type_labels()
         *
         * @since 1.0.0
         * @access public
         * @var object $labels
         */
        public $labels;

        /**
         * Whether to exclude posts with this post type from front end search
         * results.
         *
         * Default is the opposite value of $public.
         *
         * @since 1.0.0
         * @access public
         * @var bool $exclude_from_search
         */
        public $exclude_from_search = null;

        /**
         * Whether queries can be performed on the front end for the post type as part of `parse_request()`.
         *
         * Endpoints would include:
         * - `?post_type={post_type_key}`
         * - `?{post_type_key}={single_post_slug}`
         * - `?{post_type_query_var}={single_post_slug}`
         *
         * Default is the value of $public.
         *
         * @since 1.0.0
         * @access public
         * @var bool $publicly_queryable
         */
        public $publicly_queryable = null;

        /**
         * Whether to generate and allow a UI for managing this post type in the admin.
         *
         * Default is the value of $public.
         *
         * @since 1.0.0
         * @access public
         * @var bool $show_ui
         */
        public $show_ui = null;

        /**
         * Where to show the post type in the admin menu.
         *
         * To work, $show_ui must be true. If true, the post type is shown in its own top level menu. If false, no menu is
         * shown. If a string of an existing top level menu (eg. 'tools.php' or 'edit.php?post_type=page'), the post type
         * will be placed as a sub-menu of that.
         *
         * Default is the value of $show_ui.
         *
         * @since 1.0.0
         * @access public
         * @var bool $show_in_menu
         */
        public $show_in_menu = null;

        /**
         * Makes this post type available for selection in navigation menus.
         *
         * Default is the value $public.
         *
         * @since 1.0.0
         * @access public
         * @var bool $show_in_nav_menus
         */
        public $show_in_nav_menus = null;

        /**
         * Makes this post type available via the admin bar.
         *
         * Default is the value of $show_in_menu.
         *
         * @since 1.0.0
         * @access public
         * @var bool $show_in_admin_bar
         */
        public $show_in_admin_bar = null;

        /**
         * The position in the menu order the post type should appear.
         *
         * To work, $show_in_menu must be true. Default null (at the bottom).
         *
         * @since 1.0.0
         * @access public
         * @var int $menu_position
         */
        public $menu_position = null;

        /**
         * The URL to the icon to be used for this menu.
         *
         * Pass a base64-encoded SVG using a data URI, which will be colored to match the color scheme.
         * This should begin with 'data:image/svg+xml;base64,'. Pass the name of a Dashicons helper class
         * to use a font icon, e.g. 'dashicons-chart-pie'. Pass 'none' to leave div.wp-menu-image empty
         * so an icon can be added via CSS.
         *
         * Defaults to use the posts icon.
         *
         * @since 1.0.0
         * @access public
         * @var string $menu_icon
         */
        public $menu_icon = null;

        /**
         * The string to use to build the read, edit, and delete capabilities.
         *
         * May be passed as an array to allow for alternative plurals when using
         * this argument as a base to construct the capabilities, e.g.
         * array( 'story', 'stories' ). Default 'post'.
         *
         * @since 1.0.0
         * @access public
         * @var string $capability_type
         */
        public $capability_type = 'post';

        /**
         * Whether to use the internal default meta capability handling.
         *
         * Default false.
         *
         * @since 1.0.0
         * @access public
         * @var bool $map_meta_cap
         */
        public $map_meta_cap = false;

        /**
         * Provide a callback function that sets up the meta boxes for the edit form.
         *
         * Do `remove_meta_box()` and `add_meta_box()` calls in the callback. Default null.
         *
         * @since 1.0.0
         * @access public
         * @var string $register_meta_box_cb
         */
        public $register_meta_box_cb = null;

        /**
         * An array of taxonomy identifiers that will be registered for the post type.
         *
         * Taxonomies can be registered later with `register_taxonomy()` or `register_taxonomy_for_object_type()`.
         *
         * Default empty array.
         *
         * @since 1.0.0
         * @access public
         * @var array $taxonomies
         */
        public $taxonomies = array();

        /**
         * Whether there should be post type archives, or if a string, the archive slug to use.
         *
         * Will generate the proper rewrite rules if $rewrite is enabled. Default false.
         *
         * @since 1.0.0
         * @access public
         * @var bool|string $has_archive
         */
        public $has_archive = false;

        /**
         * Sets the query_var key for this post type.
         *
         * Defaults to $post_type key. If false, a post type cannot be loaded at `?{query_var}={post_slug}`.
         * If specified as a string, the query `?{query_var_string}={post_slug}` will be valid.
         *
         * @since 1.0.0
         * @access public
         * @var string|bool $query_var
         */
        public $query_var;

        /**
         * Whether to allow this post type to be exported.
         *
         * Default true.
         *
         * @since 1.0.0
         * @access public
         * @var bool $can_export
         */
        public $can_export = true;

        /**
         * Whether to delete posts of this type when deleting a user.
         *
         * If true, posts of this type belonging to the user will be moved to trash when then user is deleted.
         * If false, posts of this type belonging to the user will *not* be trashed or deleted.
         * If not set (the default), posts are trashed if post_type_supports( 'author' ).
         * Otherwise posts are not trashed or deleted. Default null.
         *
         * @since 1.0.0
         * @access public
         * @var bool $delete_with_user
         */
        public $delete_with_user = null;

        /**
         * Whether this table is a native or "built-in" post_type.
         *
         * Default false.
         *
         * @since 1.0.0
         * @access public
         * @var bool $_builtin
         */
        public $_builtin = false;

        /**
         * URL segment to use for edit link of this table.
         *
         * Default 'post.php?post=%d'.
         *
         * @since 1.0.0
         * @access public
         * @var string $_edit_link
         */
        public $_edit_link = 'post.php?post=%d';

        /**
         * Table capabilities.
         *
         * @since 1.0.0
         * @access public
         * @var object $cap
         */
        public $cap;

        /**
         * Table capability to access.
         *
         * @since 1.0.0
         * @access public
         * @var string $capability
         */
        public $capability;

        /**
         * Triggers the handling of rewrites for this post type.
         *
         * Defaults to true, using $post_type as slug.
         *
         * @since 1.0.0
         * @access public
         * @var array|false $rewrite
         */
        public $rewrite;

        /**
         * The features supported by the post type.
         *
         * @since 1.0.0
         * @access public
         * @var array|bool $supports
         */
        public $supports;

        /**
         * Whether this post type should appear in the REST API.
         *
         * Default false. If true, standard endpoints will be registered with
         * respect to $rest_base and $rest_controller_class.
         *
         * @since 4.7.4
         * @access public
         * @var bool $show_in_rest
         */
        public $show_in_rest;

        /**
         * The base path for this post type's REST API endpoints.
         *
         * @since 4.7.4
         * @access public
         * @var string|bool $rest_base
         */
        public $rest_base;

        /**
         * The controller for this post type's REST API endpoints.
         *
         * Custom controllers must extend WP_REST_Controller.
         *
         * @since 4.7.4
         * @access public
         * @var string|bool $rest_controller_class
         */
        public $rest_controller_class;

        /**
         * Constructor.
         *
         * Will populate object properties from the provided arguments and assign other
         * default properties based on that information.
         *
         * @since 1.0.0
         * @access public
         *
         * @see ct_register_table()
         *
         * @param string       $name Table key.
         * @param array|string $args      Optional. Array or string of arguments for registering a post type.
         *                                Default empty array.
         */
        public function __construct( $name, $args = array() ) {

            // Table name
            $this->name = $name;

            $this->set_props( $args );

        }

        /**
         * Sets table properties.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array|string $args Array or string of arguments for registering a post type.
         */
        public function set_props( $args ) {
            $args = wp_parse_args( $args );

            /**
             * Filters the arguments for registering a table.
             *
             * @since 1.0.0
             *
             * @param array  $args      Array of arguments for registering a post type.
             * @param string $post_type Table key.
             */
            $args = apply_filters( 'ct_register_table_args', $args, $this->name );

            $has_edit_link = ! empty( $args['_edit_link'] );

            // Args prefixed with an underscore are reserved for internal use.
            $defaults = array(
                'singular'              => $this->name,
                'plural'                => $this->name . 's',
                'labels'                => array(),
                'description'           => '',
                'group'                 => '',
                'public'                => false,
                'hierarchical'          => false,
                'exclude_from_search'   => null,
                'publicly_queryable'    => null,
                'show_ui'               => null,
                'show_in_menu'          => null,
                'show_in_nav_menus'     => null,
                'show_in_admin_bar'     => null,
                'menu_position'         => null,
                'menu_icon'             => null,
                'capability_type'       => 'item',
                'capabilities'          => array(),
                'map_meta_cap'          => null,
                'supports'              => array(),
                'register_meta_box_cb'  => null,
                //'taxonomies'            => array(),
                'has_archive'           => false,
                'rewrite'               => true,
                'query_var'             => true,
                'can_export'            => true,
                'delete_with_user'      => null,
                //'_builtin'              => false,
                //'_edit_link'            => 'post.php?post=%d',
                // Rest defaults
                'show_in_rest'          => false,
                'rest_base'             => false,
                'rest_controller_class' => false,
                // Database defaults
                'primary_key' => '',
                'version' => 1,
                'global' => false,
                //'schema' => '',
                'engine' => 'InnoDB',
                // Shortcuts
                'capability' => '',
            );

            $args = array_merge( $defaults, $args );

            $args['name'] = $this->name;

            if ( empty( $args['group'] ) ) {
                $args['group'] = strtok( $this->name, '_');
            }

            // If not set, default to the setting for public.
            if ( null === $args['publicly_queryable'] ) {
                $args['publicly_queryable'] = $args['public'];
            }

            // If not set, default to the setting for public.
            if ( null === $args['show_ui'] ) {
                $args['show_ui'] = $args['public'];
            }

            // If not set, default to the setting for show_ui.
            if ( null === $args['show_in_menu'] || ! $args['show_ui'] ) {
                $args['show_in_menu'] = $args['show_ui'];
            }

            // If not set, default to the whether the full UI is shown.
            if ( null === $args['show_in_admin_bar'] ) {
                $args['show_in_admin_bar'] = (bool) $args['show_in_menu'];
            }

            // If not set, default to the setting for public.
            if ( null === $args['show_in_nav_menus'] ) {
                $args['show_in_nav_menus'] = $args['public'];
            }

            // If not set, default to true if not public, false if public.
            if ( null === $args['exclude_from_search'] ) {
                $args['exclude_from_search'] = ! $args['public'];
            }

            // Back compat with quirky handling in version 3.0. #14122.
            if ( empty( $args['capabilities'] ) && null === $args['map_meta_cap'] && in_array( $args['capability_type'], array( 'post', 'page' ) ) ) {
                $args['map_meta_cap'] = true;
            }

            // If not set, default to false.
            if ( null === $args['map_meta_cap'] ) {
                $args['map_meta_cap'] = false;
            }

            // If there's no specified edit link and no UI, remove the edit link.
            if ( ! $args['show_ui'] && ! $has_edit_link ) {
                $args['_edit_link'] = '';
            }

            $this->cap = ct_get_table_capabilities( (object) $args );
            $this->capability = $args['capability'];

            unset( $args['capabilities'] );

            if ( is_array( $args['capability_type'] ) ) {
                $args['capability_type'] = $args['capability_type'][0];
            }

            if ( false !== $args['query_var'] ) {
                if ( true === $args['query_var'] ) {
                    $args['query_var'] = $this->name;
                } else {
                    $args['query_var'] = sanitize_title_with_dashes( $args['query_var'] );
                }
            }

            if ( false !== $args['rewrite'] && ( is_admin() || '' != get_option( 'permalink_structure' ) ) ) {
                if ( ! is_array( $args['rewrite'] ) ) {
                    $args['rewrite'] = array();
                }
                if ( empty( $args['rewrite']['slug'] ) ) {
                    $args['rewrite']['slug'] = $this->name;
                }
                if ( ! isset( $args['rewrite']['with_front'] ) ) {
                    $args['rewrite']['with_front'] = true;
                }
                if ( ! isset( $args['rewrite']['pages'] ) ) {
                    $args['rewrite']['pages'] = true;
                }
                if ( ! isset( $args['rewrite']['feeds'] ) || ! $args['has_archive'] ) {
                    $args['rewrite']['feeds'] = (bool) $args['has_archive'];
                }
                if ( ! isset( $args['rewrite']['ep_mask'] ) ) {
                    if ( isset( $args['permalink_epmask'] ) ) {
                        $args['rewrite']['ep_mask'] = $args['permalink_epmask'];
                    } else {
                        $args['rewrite']['ep_mask'] = EP_PERMALINK;
                    }
                }
            }

            foreach ( $args as $property_name => $property_value ) {
                $this->$property_name = $property_value;
            }

            $this->singular  = $args['singular'];
            $this->plural  = $args['plural'];

            $labels = (array) ct_get_table_labels( $this );

            // Custom defined labels overrides default
            if( isset( $args['labels'] ) && is_array( $args['labels'] ) ) {
                $labels = wp_parse_args( $args['labels'], $labels );
            }

            $this->labels = (object) $labels;

            $this->label  = $this->labels->name;

            // Table database

            if( isset( $args['db'] ) ) {
                if( is_array( $args['db'] ) ) {
                    // Table as array of args to pass to CT_DataBase
                    $this->db = new CT_DataBase( $this->name, $args['db'] );
                } else if( $args['db'] instanceof CT_DataBase || is_subclass_of( $args['db'], 'CT_DataBase' ) ) {
                    // Table as custom object
                    $this->db = $args['db'];
                }
            } else {
                // Default database initialization
                $this->db = new CT_DataBase( $this->name, $args );
            }

            // Views (list, add, edit)

            $views_defaults = array(
                'list' => array(
                    'page_title'    => $this->labels->plural_name,
                    'menu_title'    => $this->labels->all_items,
                    'menu_slug'     => $this->name,
                    'parent_slug'   => $this->name,
                    'show_in_menu'  => $this->show_ui,

                    // Specific view args
                    'per_page'      => 20,
                    'columns'       => array(),
                ),
                'add' => array(
                    'page_title'    => $this->labels->add_new,
                    'menu_title'    => $this->labels->add_new,
                    'menu_slug'     => 'add_' . $this->name,
                    'parent_slug'   => $this->name,
                    'show_in_menu'  => $this->show_ui,

                    // Specific view args
                    'columns'       => 2,
                ),
                'edit' => array(
                    'page_title'    => $this->labels->edit_item,
                    'menu_title'    => $this->labels->edit_item,
                    'menu_slug'     => 'edit_' . $this->name,
                    'parent_slug'   => '',
                    'show_in_menu'  => false,

                    // Specific view args
                    'columns'       => 2,
                ),
            );

            if( isset( $args['views'] ) && is_array( $args['views'] ) ) {

                $views = array();

                // Ensure default views (list, add, edit) are in
                foreach( $views_defaults as $view => $view_args ) {
                    if( ! isset( $args['views'][$view] ) ) {
                        $args['views'][$view] = $view_args;
                    }
                }

                foreach( $args['views'] as $view => $view_args ) {

                    if( is_array( $view_args ) ) {

                        // Parse default view args
                        if( isset( $views_defaults[$view] ) ) {
                            $view_args = wp_parse_args( $view_args, $views_defaults[$view] );
                        }

                        // View as array of args to pass to CT_View
                        switch( $view ) {
                            case 'list':
                                $views[$view] = new CT_List_View( $this->name, $view_args );
                                break;
                            case 'add':
                                $views[$view] = new CT_Edit_View( $this->name, $view_args );
                                break;
                            case 'edit':
                                $views[$view] = new CT_Edit_View( $this->name, $view_args );
                                break;
                            default:
                                $views[$view] = new CT_View( $this->name, $view_args );
                                break;
                        }
                    } else if( $view_args instanceof CT_View || is_subclass_of( $view_args, 'CT_View' ) ) {
                        // View as custom object
                        $views[$view] = $view_args;
                    }
                }

                // Ensure to add all default views
                foreach( array( 'list', 'add', 'edit' ) as $view ) {
                    if( ! isset( $views[$view] ) ) {
                        $views[$view] = false;
                    }
                }

                $this->views = (object) $views;

            } else {
                // Default views initialization
                $this->views = (object) array(
                    'list' => new CT_List_View( $this->name, $views_defaults['list'] ),
                    'add' => new CT_Edit_View( $this->name, $views_defaults['add'] ),
                    'edit' => new CT_Edit_View( $this->name, $views_defaults['edit'] ),
                );
            }

            // Meta data
            if( in_array( 'meta', $this->supports ) ) {
                $this->meta = new CT_Table_Meta( $this );
            }
        }

    }

endif;