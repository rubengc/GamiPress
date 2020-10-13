<?php
/**
 * Rest Controller class
 *
 * Based on WP_REST_Posts_Controller class
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Core class to access posts via the REST API.
 *
 * @since 1.0.0
 *
 * @see WP_REST_Controller
 */
class CT_REST_Controller extends WP_REST_Controller {

    /**
     * Table name.
     *
     * @since 1.0.0
     * @var string
     */
    protected $name;

    /**
     * Table Meta table object.
     *
     * @since 1.0.0
     * @access public
     * @var CT_Table $table
     */
    public $table;

    /**
     * Instance of a post meta fields object.
     *
     * @since 1.0.0
     * @var CT_REST_Meta_Fields
     */
    protected $meta;

    /**
     * Constructor.
     *
     * @since 1.0.0
     *
     * @param string $name Custom table name.
     */
    public function __construct( $name ) {
        $this->name = $name;
        $this->namespace = 'wp/v2';
        $this->table = ct_get_table_object( $name );
        $this->rest_base = ! empty( $this->table->rest_base ) ? $this->table->rest_base : $this->name;

        if( in_array( 'meta', $this->table->supports ) ) {
            $this->meta = new CT_REST_Meta_Fields( $name );
        }
    }

    /**
     * Registers the routes for the objects of the controller.
     *
     * @since 1.0.0
     *
     * @see register_rest_route()
     */
    public function register_routes() {

        register_rest_route( $this->namespace, '/' . $this->rest_base, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_items' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'create_item' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
                'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
            'args' => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the object.' ),
                    'type'        => 'integer',
                ),
            ),
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_item' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
                'args'                => array(
                    'context'  => $this->get_context_param( array( 'default' => 'view' ) ),
                ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_item' ),
                'permission_callback' => array( $this, 'update_item_permissions_check' ),
                'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_item' ),
                'permission_callback' => array( $this, 'delete_item_permissions_check' ),
                'args'                => array(
                    // TODO: There is no support for trash functionality, so let's remove it temporally
                    /*'force' => array(
                        'type'        => 'boolean',
                        'default'     => false,
                        'description' => __( 'Whether to bypass trash and force deletion.' ),
                    ),*/
                ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );
    }

    /**
     * Checks if a given request has access to read posts.
     *
     * @since 1.0.0
     *
     * @param  WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_items_permissions_check( $request ) {

        if ( 'edit' === $request['context'] && ! current_user_can( $this->table->cap->edit_items ) ) {
            return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to edit items of this type.' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Retrieves a collection of posts.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_items( $request ) {

        // Ensure a search string is set in case the orderby is set to 'relevance'.
        if ( ! empty( $request['orderby'] ) && 'relevance' === $request['orderby'] && empty( $request['search'] ) ) {
            return new WP_Error( 'rest_no_search_term_defined', __( 'You need to define a search term to order by relevance.' ), array( 'status' => 400 ) );
        }

        // Ensure an include parameter is set in case the orderby is set to 'include'.
        if ( ! empty( $request['orderby'] ) && 'include' === $request['orderby'] && empty( $request['include'] ) ) {
            return new WP_Error( 'rest_orderby_include_missing_include', __( 'You need to define an include parameter to order by include.' ), array( 'status' => 400 ) );
        }

        // Retrieve the list of registered collection query parameters.
        $registered = $this->get_collection_params();
        $args = array();

        $ct_table = ct_setup_table( $this->name );

        /*
         * This array defines mappings between public API query parameters whose
         * values are accepted as-passed, and their internal CT_Query parameter
         * name equivalents (some are the same). Only values which are also
         * present in $registered will be set.
         */
        $parameter_mappings = array(
            // WP_REST_Controller fields
            'page'           => 'paged',
            'search'         => 's',
            // CT_REST_Controller fields
            'offset'         => 'offset',
            'order'          => 'order',
            'orderby'        => 'orderby',
        );

        /**
         * Filter parameters mappings for the rest controller.
         *
         * The dynamic part of the filter `$this->name` refers to the custom table name.
         *
         * @since 1.0.0
         *
         * @param array             $parameter_mappings Array of parameters to map.
         * @param CT_Table          $ct_table           Table object.
         * @param WP_REST_Request   $request            The request given.
         */
        $parameter_mappings = apply_filters( "ct_rest_{$this->name}_parameter_mappings", $parameter_mappings, $ct_table, $request );

        /*
         * For each known parameter which is both registered and present in the request,
         * set the parameter's value on the query $args.
         */
        foreach ( $parameter_mappings as $api_param => $wp_param ) {
            if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
                $args[ $wp_param ] = $request[ $api_param ];
            }
        }

        // Ensure our per_page parameter overrides any provided items_per_page filter.
        if ( isset( $registered['per_page'] ) ) {
            $args['items_per_page'] = $request['per_page'];
        }

        /**
         * Filters the query arguments for a request.
         *
         * Enables adding extra arguments or setting defaults for a post collection request.
         *
         * @since 1.0.0
         *
         * @link https://developer.wordpress.org/reference/classes/wp_query/
         *
         * @param array           $args    Key value array of query var to query value.
         * @param WP_REST_Request $request The request used.
         */
        $args = apply_filters( "ct_rest_{$this->name}_query", $args, $request );
        $query_args = $this->prepare_items_query( $args, $request );

        $ct_query  = new CT_Query();
        $query_result = $ct_query->query( $query_args );

        $items = array();

        foreach ( $query_result as $item ) {
            if ( ! $this->check_read_permission( $item ) ) {
                continue;
            }

            $data    = $this->prepare_item_for_response( $item, $request );
            $items[] = $this->prepare_response_for_collection( $data );
        }

        $page = (int) $query_args['paged'];
        $total_items = $ct_query->found_results;

        if ( $total_items < 1 ) {
            // Out-of-bounds, run the query again without LIMIT for total count.
            unset( $query_args['paged'] );

            $count_query = new CT_Query();
            $count_query->query( $query_args );
            $total_items = $count_query->found_results;
        }

        $max_pages = ceil( $total_items / (int) $ct_query->query_vars['items_per_page'] );

        if ( $page > $max_pages && $total_items > 0 ) {
            ct_reset_setup_table();

            return new WP_Error( 'rest_item_invalid_page_number', __( 'The page number requested is larger than the number of pages available.' ), array( 'status' => 400 ) );
        }

        $response = rest_ensure_response( $items );

        $response->header( 'X-WP-Total', (int) $total_items );
        $response->header( 'X-WP-TotalPages', (int) $max_pages );

        $request_params = $request->get_query_params();
        $base = add_query_arg( $request_params, rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ) );

        if ( $page > 1 ) {
            $prev_page = $page - 1;

            if ( $prev_page > $max_pages ) {
                $prev_page = $max_pages;
            }

            $prev_link = add_query_arg( 'page', $prev_page, $base );
            $response->link_header( 'prev', $prev_link );
        }
        if ( $max_pages > $page ) {
            $next_page = $page + 1;
            $next_link = add_query_arg( 'page', $next_page, $base );

            $response->link_header( 'next', $next_link );
        }

        ct_reset_setup_table();

        return $response;
    }

    /**
     * Get the object, if the ID is valid.
     *
     * @since 4.7.2
     *
     * @param int $id Supplied ID.
     * @return WP_Post|WP_Error Post object if ID is valid, WP_Error otherwise.
     */
    protected function get_object( $id ) {
        $error = new WP_Error( 'rest_item_invalid_id', __( 'Invalid item ID.' ), array( 'status' => 404 ) );
        if ( (int) $id <= 0 ) {
            return $error;
        }

        ct_setup_table( $this->name );

        $object = ct_get_object( (int) $id );
        $primary_key = $this->table->db->primary_key;

        ct_reset_setup_table();

        if ( empty( $object ) || empty( $object->$primary_key ) ) {
            return $error;
        }

        return $object;
    }

    /**
     * Checks if a given request has access to read a post.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return bool|WP_Error True if the request has read access for the item, WP_Error object otherwise.
     */
    public function get_item_permissions_check( $request ) {
        $object = $this->get_object( $request['id'] );
        if ( is_wp_error( $object ) ) {
            return $object;
        }

        if ( 'edit' === $request['context'] && $object && ! $this->check_update_permission( $object ) ) {
            return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to edit this item.' ), array( 'status' => rest_authorization_required_code() ) );
        }

        if ( $object ) {
            return $this->check_read_permission( $object );
        }

        return true;
    }

    /**
     * Retrieves a single post.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_item( $request ) {
        $object = $this->get_object( $request['id'] );
        if ( is_wp_error( $object ) ) {
            return $object;
        }

        $data     = $this->prepare_item_for_response( $object, $request );
        $response = rest_ensure_response( $data );

        return $response;
    }

    /**
     * Checks if a given request has access to create a post.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
     */
    public function create_item_permissions_check( $request ) {
        if ( ! empty( $request['id'] ) ) {
            return new WP_Error( 'rest_item_exists', __( 'Cannot create existing item.' ), array( 'status' => 400 ) );
        }

        if ( ! current_user_can( $this->table->cap->create_items ) ) {
            return new WP_Error( 'rest_cannot_create', __( 'Sorry, you are not allowed to create items as this user.' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Creates a single post.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function create_item( $request ) {
        if ( ! empty( $request['id'] ) ) {
            return new WP_Error( 'rest_item_exists', __( 'Cannot create existing item.' ), array( 'status' => 400 ) );
        }

        ct_setup_table( $this->name );

        $prepared_object = $this->prepare_item_for_database( $request );

        if ( is_wp_error( $prepared_object ) ) {
            return $prepared_object;
        }

        $object_id = ct_insert_object( wp_slash( (array) $prepared_object ), true );

        if ( is_wp_error( $object_id ) ) {

            if ( 'db_insert_error' === $object_id->get_error_code() ) {
                $object_id->add_data( array( 'status' => 500 ) );
            } else {
                $object_id->add_data( array( 'status' => 400 ) );
            }

            return $object_id;
        }

        $object = ct_get_object( $object_id );

        /**
         * Fires after a single object is created or updated via the REST API.
         *
         * The dynamic portion of the hook name, `$this->name`, refers to the post type slug.
         *
         * @since 1.0.0
         *
         * @param WP_Post         $object   Inserted or updated object.
         * @param WP_REST_Request $request  Request object.
         * @param bool            $creating True when creating a post, false when updating.
         */
        do_action( "ct_rest_insert_{$this->name}", $object, $request, true );

        $schema = $this->get_item_schema();

        if ( in_array( 'meta', $this->table->supports ) && ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {

            $meta_update = $this->meta->update_value( $request['meta'], $object_id );

            if ( is_wp_error( $meta_update ) ) {
                return $meta_update;
            }

        }

        $object = ct_get_object( $object_id );
        $fields_update = $this->update_additional_fields_for_object( $object, $request );

        if ( is_wp_error( $fields_update ) ) {
            return $fields_update;
        }

        $request->set_param( 'context', 'edit' );

        /**
         * Fires after a single object is completely created or updated via the REST API.
         *
         * The dynamic portion of the hook name, `$this->name`, refers to the custom table name.
         *
         * @since 1.0.0
         *
         * @param WP_Post         $object   Inserted or updated object.
         * @param WP_REST_Request $request  Request object.
         * @param bool            $creating True when creating a post, false when updating.
         */
        do_action( "ct_rest_after_insert_{$this->name}", $object, $request, true );

        $response = $this->prepare_item_for_response( $object, $request );
        $response = rest_ensure_response( $response );

        $response->set_status( 201 );
        $response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $object_id ) ) );

        ct_reset_setup_table();

        return $response;
    }

    /**
     * Checks if a given request has access to update a post.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has access to update the item, WP_Error object otherwise.
     */
    public function update_item_permissions_check( $request ) {
        $object = $this->get_object( $request['id'] );
        if ( is_wp_error( $object ) ) {
            return $object;
        }

        if ( $object && ! $this->check_update_permission( $object ) ) {
            return new WP_Error( 'rest_cannot_edit', __( 'Sorry, you are not allowed to edit this item.' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Updates a single post.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function update_item( $request ) {
        $valid_check = $this->get_object( $request['id'] );
        if ( is_wp_error( $valid_check ) ) {
            return $valid_check;
        }

        ct_setup_table( $this->name );

        $object = $this->prepare_item_for_database( $request );

        if ( is_wp_error( $object ) ) {
            return $object;
        }

        // Convert the object to an array, otherwise ct_update_object will expect non-escaped input.
        $object_id = ct_update_object( wp_slash( (array) $object ), true );

        if ( is_wp_error( $object_id ) ) {
            if ( 'db_update_error' === $object_id->get_error_code() ) {
                $object_id->add_data( array( 'status' => 500 ) );
            } else {
                $object_id->add_data( array( 'status' => 400 ) );
            }
            return $object_id;
        }

        $object = ct_get_object( $object_id );

        /**
         * Fires after a single object is created or updated via the REST API.
         *
         * The dynamic portion of the hook name, `$this->name`, refers to the post type slug.
         *
         * @since 1.0.0
         *
         * @param WP_Post         $object   Inserted or updated object.
         * @param WP_REST_Request $request  Request object.
         * @param bool            $creating True when creating a post, false when updating.
         */
        do_action( "ct_rest_insert_{$this->name}", $object, $request, false );

        $schema = $this->get_item_schema();

        if ( in_array( 'meta', $this->table->supports ) && ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {

            $meta_update = $this->meta->update_value( $request['meta'], $object_id );

            if ( is_wp_error( $meta_update ) ) {
                return $meta_update;
            }

        }

        $object = ct_get_object( $object_id );
        $fields_update = $this->update_additional_fields_for_object( $object, $request );

        if ( is_wp_error( $fields_update ) ) {
            return $fields_update;
        }

        $request->set_param( 'context', 'edit' );

        /**
         * Fires after a single object is completely created or updated via the REST API.
         *
         * The dynamic portion of the hook name, `$this->name`, refers to the custom table name.
         *
         * @since 1.0.0
         *
         * @param WP_Post         $object   Inserted or updated object.
         * @param WP_REST_Request $request  Request object.
         * @param bool            $creating True when creating a post, false when updating.
         */
        do_action( "ct_rest_after_insert_{$this->name}", $object, $request, false );

        $response = $this->prepare_item_for_response( $object, $request );

        ct_reset_setup_table();

        return rest_ensure_response( $response );
    }

    /**
     * Checks if a given request has access to delete a post.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has access to delete the item, WP_Error object otherwise.
     */
    public function delete_item_permissions_check( $request ) {
        $object = $this->get_object( $request['id'] );
        if ( is_wp_error( $object ) ) {
            return $object;
        }

        if ( $object && ! $this->check_delete_permission( $object ) ) {
            return new WP_Error( 'rest_cannot_delete', __( 'Sorry, you are not allowed to delete this item.' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    /**
     * Deletes a single item.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function delete_item( $request ) {
        $object = $this->get_object( $request['id'] );
        if ( is_wp_error( $object ) ) {
            return $object;
        }

        $id    = $request['id'];
        $force = (bool) $request['force'];

        $supports_trash = ( EMPTY_TRASH_DAYS > 0 );

        /**
         * Filters whether a post is trashable.
         *
         * The dynamic portion of the hook name, `$this->name`, refers to the custom table name.
         *
         * Pass false to disable trash support for the post.
         *
         * @since 1.0.0
         *
         * @param bool    $supports_trash Whether the post type support trashing.
         * @param WP_Post $post           The Post object being considered for trashing support.
         */
        $supports_trash = apply_filters( "ct_rest_{$this->name}_trashable", $supports_trash, $object );

        if ( ! $this->check_delete_permission( $object ) ) {
            return new WP_Error( 'rest_user_cannot_delete_post', __( 'Sorry, you are not allowed to delete this post.' ), array( 'status' => rest_authorization_required_code() ) );
        }

        $request->set_param( 'context', 'edit' );

        // TODO: There is no support for trash functionality, so let's force deletion
        $force = true;

        // If we're forcing, then delete permanently.
        if ( $force ) {
            $previous = $this->prepare_item_for_response( $object, $request );
            $result = ct_delete_object( $id, true );
            $response = new WP_REST_Response();
            $response->set_data( array( 'deleted' => true, 'previous' => $previous->get_data() ) );
        } else {
            // If we don't support trashing for this type, error out.
            if ( ! $supports_trash ) {
                /* translators: %s: force=true */
                return new WP_Error( 'rest_trash_not_supported', sprintf( __( "The post does not support trashing. Set '%s' to delete." ), 'force=true' ), array( 'status' => 501 ) );
            }

            // Otherwise, only trash if we haven't already.
            //if ( 'trash' === $post->post_status ) {
                //return new WP_Error( 'rest_already_trashed', __( 'The post has already been deleted.' ), array( 'status' => 410 ) );
            //}

            // (Note that internally this falls through to `wp_delete_post` if the trash is disabled.)
            //$result = wp_trash_post( $id );
            $object = ct_get_object( $id );
            $response = $this->prepare_item_for_response( $object, $request );
        }

        if ( ! $result ) {
            return new WP_Error( 'rest_cannot_delete', __( 'The item cannot be deleted.' ), array( 'status' => 500 ) );
        }

        /**
         * Fires immediately after a single post is deleted or trashed via the REST API.
         *
         * They dynamic portion of the hook name, `$this->name`, refers to the custom table name.
         *
         * @since 1.0.0
         *
         * @param object           $post     The deleted or trashed post.
         * @param WP_REST_Response $response The response data.
         * @param WP_REST_Request  $request  The request sent to the API.
         */
        do_action( "ct_rest_delete_{$this->name}", $object, $response, $request );

        return $response;
    }

    /**
     * Determines the allowed query_vars for a get_items() response and prepares
     * them for CT_Query.
     *
     * @since 1.0.0
     *
     * @param array           $prepared_args Optional. Prepared CT_Query arguments. Default empty array.
     * @param WP_REST_Request $request       Optional. Full details about the request.
     * @return array Items query arguments.
     */
    protected function prepare_items_query( $prepared_args = array(), $request = null ) {

        $ct_table = $this->table;
        $query_args = array();

        foreach ( $prepared_args as $key => $value ) {
            /**
             * Filters the query_vars used in get_items() for the constructed query.
             *
             * The dynamic portion of the hook name, `$key`, refers to the query_var key.
             *
             * @since 1.0.0
             *
             * @param string $value The query_var value.
             */
            $query_args[ $key ] = apply_filters( "ct_rest_query_var-{$key}", $value ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
        }

        // Map to proper CT_Query orderby param.
        if ( isset( $query_args['orderby'] ) && isset( $request['orderby'] ) ) {
            $orderby_mappings = array();

            /**
             * Filter orderby parameters mappings for the rest controller.
             *
             * The dynamic part of the filter `$this->name` refers to the custom table for the controller.
             *
             * @since 1.0.0
             *
             * @param array             $orderby_mappings   Array of parameters to map (for the orderby clause).
             * @param CT_Table          $ct_table           Table object.
             * @param array             $prepared_args      Prepared CT_Query arguments. Default empty array.
             * @param WP_REST_Request   $request            The request given.
             */
            $orderby_mappings = apply_filters( "ct_rest_{$this->name}_orderby_mappings", $orderby_mappings, $ct_table, $prepared_args, $request );

            if ( isset( $orderby_mappings[ $request['orderby'] ] ) ) {
                $query_args['orderby'] = $orderby_mappings[ $request['orderby'] ];
            }
        }

        return $query_args;
    }

    /**
     * Checks the post_date_gmt or modified_gmt and prepare any post or
     * modified date for single post output.
     *
     * @since 1.0.0
     *
     * @param string      $date_gmt GMT publication time.
     * @param string|null $date     Optional. Local publication time. Default null.
     * @return string|null ISO8601/RFC3339 formatted datetime.
     */
    protected function prepare_date_response( $date_gmt, $date = null ) {
        // Use the date if passed.
        if ( isset( $date ) ) {
            return mysql_to_rfc3339( $date );
        }

        // Return null if $date_gmt is empty/zeros.
        if ( '0000-00-00 00:00:00' === $date_gmt ) {
            return null;
        }

        // Return the formatted datetime.
        return mysql_to_rfc3339( $date_gmt );
    }

    /**
     * Prepares a single post for create or update.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Request object.
     * @return stdClass|WP_Error Post object or WP_Error.
     */
    protected function prepare_item_for_database( $request ) {
        $prepared_object = new stdClass;
        $primary_key = $this->table->db->primary_key;
        $table_fields = $this->table->db->schema->fields;

        // Parse object primary key as ID
        if( isset( $request[$primary_key] ) ) {
            $request['id'] = $request[$primary_key];
        }

        // Object ID.
        if ( isset( $request['id'] ) ) {
            $existing_object = $this->get_object( $request['id'] );
            if ( is_wp_error( $existing_object ) ) {
                return $existing_object;
            }

            $prepared_object->$primary_key = $existing_object->$primary_key;
        }

        $schema = $this->get_item_schema();

        if( isset( $schema['properties'] ) && is_array( $schema['properties'] ) ) {
            foreach( $schema['properties'] as $field => $field_args ) {

                // Check if field is on request and also if is a table field
                if( isset( $request[$field] ) && isset( $table_fields[$field] ) ) {

                    $value = $request[$field];

                    /**
                     * Filters a post before it is inserted via the REST API.
                     *
                     * The dynamic portion of the hook name, `$this->name`, refers to the custom table name.
                     *
                     * @since 1.0.0
                     *
                     * @param mixed             $value      The field value given.
                     * @param string            $field      The field name.
                     * @param WP_REST_Request   $request    Request object.
                     *
                     * @return mixed|WP_Error   Return the field value sanitized or a WP_Error if for some reason field value is not correct
                     */
                    $value = apply_filters( "ct_rest_{$this->name}_sanitize_field_value", $value, $field, $request );

                    // Bail if value filtered returns an error
                    if( is_wp_error( $value ) ) {
                        return $value;
                    }

                    $prepared_object->$field = $request[$field];

                }

            }
        }

        /**
         * Filters an object before it is inserted via the REST API.
         *
         * The dynamic portion of the hook name, `$this->name`, refers to the custom table name.
         *
         * @since 1.0.0
         *
         * @param stdClass        $prepared_post An object representing a single post prepared
         *                                       for inserting or updating the database.
         * @param WP_REST_Request $request       Request object.
         */
        return apply_filters( "ct_rest_pre_insert_{$this->name}", $prepared_object, $request );

    }

    /**
     * Checks if an item can be read.
     *
     * Correctly handles posts with the inherit status.
     *
     * @since 1.0.0
     *
     * @param object $item Item object.
     * @return bool Whether the item can be read.
     */
    public function check_read_permission( $item ) {
        $primary_key = $this->table->db->primary_key;

        // Is the item readable?
        if ( current_user_can( $this->table->cap->read_item, $item->$primary_key ) ) {
            return true;
        }

        return false;
    }

    /**
     * Checks if an item can be edited.
     *
     * @since 1.0.0
     *
     * @param object $item Item object.
     * @return bool Whether the item can be edited.
     */
    protected function check_update_permission( $item ) {
        $primary_key = $this->table->db->primary_key;

        // Is the item editable?
        if ( current_user_can( $this->table->cap->edit_item, $item->$primary_key ) ) {
            return true;
        }

        return false;
    }

    /**
     * Checks if an item can be created.
     *
     * @since 1.0.0
     *
     * @param object $item Item object.
     * @return bool Whether the item can be created.
     */
    protected function check_create_permission( $item ) {
        return current_user_can( $this->table->cap->create_items );
    }

    /**
     * Checks if an item can be deleted.
     *
     * @since 1.0.0
     *
     * @param object $item Item object.
     * @return bool Whether the item can be deleted.
     */
    protected function check_delete_permission( $item ) {
        $primary_key = $this->table->db->primary_key;

        return current_user_can( $this->table->cap->delete_item, $item->$primary_key );
    }

    /**
     * Prepares a single post output for response.
     *
     * @since 1.0.0
     *
     * @param stdClass        $object   Object.
     * @param WP_REST_Request $request  Request object.
     * @return WP_REST_Response Response object.
     */
    public function prepare_item_for_response( $object, $request ) {

        $fields = $this->get_fields_for_response( $request );
        $primary_key = $this->table->db->primary_key;
        $data = array();

        foreach( $fields as $field ) {

            $value = isset( $object->$field ) ? $object->$field : '';

            /**
             * Filters the object field value for a response.
             *
             * The dynamic portion of the hook name, `$this->name`, refers to the custom table name.
             *
             * @since 1.0.0
             *
             * @param mixed             $value      The field value.
             * @param string            $field      The field key.
             * @param stdClass          $object     Object.
             * @param WP_REST_Request   $request    Request object.
             * @param array             $fields     Fields defined to being returned.
             */
            $data[$field] = apply_filters( "ct_rest_prepare_{$this->name}_field_value", $value, $field, $object, $request, $fields );
        }

        if ( in_array( 'meta', $this->table->supports ) && in_array( 'meta', $fields, true ) ) {
            $data['meta'] = $this->meta->get_value( $object->$primary_key, $request );
        }

        $context = ! empty( $request['context'] ) ? $request['context'] : 'view';
        $data    = $this->add_additional_fields_to_object( $data, $request );
        $data    = $this->filter_response_by_context( $data, $context );

        // Wrap the data in a response object.
        $response = rest_ensure_response( $data );

        /**
         * Filters the object data for a response.
         *
         * The dynamic portion of the hook name, `$this->name`, refers to the custom table name.
         *
         * @since 1.0.0
         *
         * @param WP_REST_Response $response The response object.
         * @param stdClass         $object   Object.
         * @param WP_REST_Request  $request  Request object.
         */
        return apply_filters( "ct_rest_prepare_{$this->name}", $response, $object, $request );
    }

    /**
     * Retrieves the post's schema, conforming to JSON Schema.
     *
     * @since 1.0.0
     *
     * @return array Item schema data.
     */
    public function get_item_schema() {

        $schema = array(
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => $this->name,
            'type'       => 'object',
            // Properties are the fields that will be returned through rest request.
            'properties' => array(
                // id is common to all registered tables
                'id' => array(
                    'description' => __( 'Unique identifier for the object.' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit', 'embed' ),
                ),
            ),
        );

        // Add meta property if table has support for it
        if( in_array( 'meta', $this->table->supports ) ) {
            $schema['properties']['meta'] = $this->meta->get_field_schema();
        }

        /**
         * Filter item schema for the rest controller.
         *
         * The dynamic part of the filter `$this->name` refers to the custom table name.
         *
         * @since 1.0.0
         *
         * @param array $schema
         */
        $schema = apply_filters( "ct_rest_{$this->name}_schema", $schema );

        return $this->add_additional_fields_schema( $schema );
    }

    /**
     * Retrieves the query params for the posts collection.
     *
     * @since 1.0.0
     *
     * @return array Collection parameters.
     */
    public function get_collection_params() {

        $query_params = parent::get_collection_params();

        $query_params['context']['default'] = 'view';

        $ct_table = $this->table;

        // Offset
        $query_params['offset'] = array(
            'description'        => __( 'Offset the result set by a specific number of items.' ),
            'type'               => 'integer',
        );

        // Order
        $query_params['order'] = array(
            'description'        => __( 'Order sort attribute ascending or descending.' ),
            'type'               => 'string',
            'default'            => 'desc',
            'enum'               => array( 'asc', 'desc' ),
        );

        // Order By
        $query_params['orderby'] = array(
            'description'        => __( 'Sort collection by object attribute.' ),
            'type'               => 'string',
            'default'            => $ct_table->db->primary_key,
            'enum'               => array_merge(
                // Allow order by table fields
                array_keys( $ct_table->db->schema->fields ),
                // Allow order by custom order by clauses
                array( 'include', 'relevance'  )
            ),
        );

        /**
         * Filter collection parameters for the rest controller.
         *
         * The dynamic part of the filter `$this->name` refers to the custom table for the controller.
         *
         * This filter registers the collection parameter, but does not map the
         * collection parameter to an internal CT_Query parameter. Use the
         * `ct_rest_{$this->name}_query` filter to set CT_Query parameters.
         *
         * @since 1.0.0
         *
         * @param array     $query_params   JSON Schema-formatted collection parameters.
         * @param CT_Table  $ct_table       Table object.
         */
        return apply_filters( "ct_rest_{$this->name}_collection_params", $query_params, $ct_table );
    }
}
