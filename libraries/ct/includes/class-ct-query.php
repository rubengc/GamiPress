<?php
/**
 * Query class
 *
 * Based on WP_Query class
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CT_Query' ) ) :

    class CT_Query {

        /**
         * Query vars set by the user
         *
         * @since 1.0.0
         * @access public
         * @var array
         */
        public $query;

        /**
         * Query vars, after parsing
         *
         * @since 1.0.0
         * @access public
         * @var array
         */
        public $query_vars = array();

        /**
         * Holds the data for a single object that is queried.
         *
         * Holds the contents of a post, page, category, attachment.
         *
         * @since 1.0.0
         * @access public
         * @var object|array
         */
        public $queried_object;

        /**
         * The ID of the queried object.
         *
         * @since 1.0.0
         * @access public
         * @var int
         */
        public $queried_object_id;

        /**
         * Get post database query.
         *
         * @since 1.0.0
         * @access public
         * @var string
         */
        public $request;

        /**
         * List of posts.
         *
         * @since 1.0.0
         * @access public
         * @var array
         */
        public $results;

        /**
         * The amount of posts for the current query.
         *
         * @since 1.0.0
         * @access public
         * @var int
         */
        public $result_count = 0;

        /**
         * Index of the current item in the loop.
         *
         * @since 1.0.0
         * @access public
         * @var int
         */
        public $current_result = -1;

        /**
         * Whether the loop has started and the caller is in the loop.
         *
         * @since 1.0.0
         * @access public
         * @var bool
         */
        public $in_the_loop = false;

        /**
         * The current post.
         *
         * @since 1.0.0
         * @access public
         * @var WP_Post
         */
        public $result;

        /**
         * The amount of found posts for the current query.
         *
         * If limit clause was not used, equals $post_count.
         *
         * @since 1.0.0
         * @access public
         * @var int
         */
        public $found_results = 0;

        /**
         * The amount of pages.
         *
         * @since 1.0.0
         * @access public
         * @var int
         */
        public $max_num_pages = 0;


        public function __construct( $query = '' ) {

            $this->init();

            $this->query = $this->query_vars = wp_parse_args( $query );

        }

        public function init() {

            unset($this->results);
            unset($this->query);
            $this->query_vars = array();
            unset($this->queried_object);
            unset($this->queried_object_id);
            $this->result_count = 0;
            $this->current_result = -1;
            $this->in_the_loop = false;
            unset( $this->request );
            unset( $this->result );
            $this->found_results = 0;
            $this->max_num_pages = 0;

        }

        public function set( $query_var, $value ) {
            $this->query_vars[$query_var] = $value;
        }

        public function get_results() {

            global $wpdb, $ct_table;

            $this->parse_query();

            /**
             * Fires after the query variable object is created, but before the actual query is run.
             *
             * Note: If using conditional tags, use the method versions within the passed instance
             * (e.g. $this->is_main_query() instead of is_main_query()). This is because the functions
             * like is_main_query() test against the global $wp_query instance, not the passed one.
             *
             * @since 1.0.0
             *
             * @param CT_Query &$this The CT_Query instance (passed by reference).
             */
            do_action_ref_array( 'ct_pre_get_results', array( &$this ) );

            // Shorthand.
            $q = &$this->query_vars;

            // Fill again in case pre_get_posts unset some vars.
            $q = $this->fill_query_vars($q);

            // Suppress filters
            if ( ! isset($q['suppress_filters']) )
                $q['suppress_filters'] = false;

            if ( empty( $q['items_per_page'] ) ) {
                $q['items_per_page'] = get_option( 'items_per_page', 20 );
            }

            // Pagination
            if ( ! isset( $q['nopaging'] ) ) {
                if ( $q['items_per_page'] == -1 ) {
                    $q['nopaging'] = true;
                } else {
                    $q['nopaging'] = false;
                }
            }

            // Items per page
            $q['items_per_page'] = (int) $q['items_per_page'];

            if ( $q['items_per_page'] < -1 )
                $q['items_per_page'] = abs($q['items_per_page']);
            elseif ( $q['items_per_page'] == 0 )
                $q['items_per_page'] = 1;

            // page
            if ( isset($q['page']) ) {
                $q['page'] = trim($q['page'], '/');
                $q['page'] = absint($q['page']);
            }

            // If true, forcibly turns off SQL_CALC_FOUND_ROWS even when limits are present.
            if ( isset($q['no_found_rows']) ) {
                $q['no_found_rows'] = (bool) $q['no_found_rows'];
            } else {
                $q['no_found_rows'] = false;
            }

            switch ( $q['fields'] ) {
                case 'ids':
                    $fields = "{$ct_table->db->table_name}.{$ct_table->db->primary_key}";
                    break;
                default:
                    $fields = "{$ct_table->db->table_name}.*";
            }

            // First let's clear some variables
            $distinct = '';
            $where = '';
            $limits = '';
            $join = '';
            $search = '';
            $groupby = '';
            $orderby = '';
            $page = 1;

            // If a search pattern is specified, load the posts that match.
            if ( strlen( $q['s'] ) ) {
                $search = $this->parse_search( $q );
            }

            if ( ! $q['suppress_filters'] ) {
                /**
                 * Filters the search SQL that is used in the WHERE clause of WP_Query.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $search Search SQL for WHERE clause.
                 * @param WP_Query $this   The current WP_Query object.
                 */
                $search = apply_filters_ref_array( 'ct_query_search', array( $search, &$this ) );
            }

            $where .= $search;

            // Rand order by
            $rand = ( isset( $q['orderby'] ) && 'rand' === $q['orderby'] );
            if ( ! isset( $q['order'] ) ) {
                $q['order'] = $rand ? '' : 'DESC';
            } else {
                $q['order'] = $rand ? '' : $this->parse_order( $q['order'] );
            }

            // Order by.
            if ( empty( $q['orderby'] ) ) {
                /*
                 * Boolean false or empty array blanks out ORDER BY,
                 * while leaving the value unset or otherwise empty sets the default.
                 */
                if ( isset( $q['orderby'] ) && ( is_array( $q['orderby'] ) || false === $q['orderby'] ) ) {
                    $orderby = '';
                } else {
                    // Default order by primary key
                    $orderby = "{$ct_table->db->table_name}.{$ct_table->db->primary_key} " . $q['order'];
                }
            } elseif ( 'none' == $q['orderby'] ) {
                $orderby = '';
            } else {
                $orderby_array = array();
                if ( is_array( $q['orderby'] ) ) {
                    foreach ( $q['orderby'] as $_orderby => $order ) {
                        $orderby = addslashes_gpc( urldecode( $_orderby ) );

                        $orderby_array[] = $orderby . ' ' . $this->parse_order( $order );
                    }
                    $orderby = implode( ', ', $orderby_array );

                } else {
                    $q['orderby'] = urldecode( $q['orderby'] );
                    $q['orderby'] = addslashes_gpc( $q['orderby'] );

                    foreach ( explode( ' ', $q['orderby'] ) as $i => $orderby ) {
                        $orderby_array[] = $orderby;
                    }

                    $orderby = implode( ' ' . $q['order'] . ', ', $orderby_array );

                    if ( empty( $orderby ) ) {
                        $orderby = "{$ct_table->db->table_name}.{$ct_table->db->primary_key} " . $q['order'];
                    } elseif ( ! empty( $q['order'] ) ) {
                        $orderby .= " {$q['order']}";
                    }
                }
            }

            /*
             * Apply filters on where and join prior to paging so that any
             * manipulations to them are reflected in the paging by day queries.
             */
            if ( !$q['suppress_filters'] ) {
                /**
                 * Filters the WHERE clause of the query.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $where The WHERE clause of the query.
                 * @param WP_Query &$this The WP_Query instance (passed by reference).
                 */
                $where = apply_filters_ref_array( 'ct_query_where', array( $where, &$this ) );

                /**
                 * Filters the JOIN clause of the query.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $where The JOIN clause of the query.
                 * @param WP_Query &$this The WP_Query instance (passed by reference).
                 */
                $join = apply_filters_ref_array( 'ct_query_join', array( $join, &$this ) );
            }

            // Paging
            if ( empty($q['nopaging']) ) {
                $page = absint($q['paged']);
                if ( !$page )
                    $page = 1;

                // If 'offset' is provided, it takes precedence over 'paged'.
                if ( isset( $q['offset'] ) && is_numeric( $q['offset'] ) ) {
                    $q['offset'] = absint( $q['offset'] );
                    $pgstrt = $q['offset'] . ', ';
                } else {
                    $pgstrt = absint( ( $page - 1 ) * $q['items_per_page'] ) . ', ';
                }
                $limits = 'LIMIT ' . $pgstrt . $q['items_per_page'];
            }

            $pieces = array( 'where', 'groupby', 'join', 'orderby', 'distinct', 'fields', 'limits' );

            /*
             * Apply post-paging filters on where and join. Only plugins that
             * manipulate paging queries should use these hooks.
             */
            if ( !$q['suppress_filters'] ) {
                /**
                 * Filters the WHERE clause of the query.
                 *
                 * Specifically for manipulating paging queries.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $where The WHERE clause of the query.
                 * @param WP_Query &$this The WP_Query instance (passed by reference).
                 */
                $where = apply_filters_ref_array( 'ct_query_where_paged', array( $where, &$this ) );

                /**
                 * Filters the GROUP BY clause of the query.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $groupby The GROUP BY clause of the query.
                 * @param WP_Query &$this   The WP_Query instance (passed by reference).
                 */
                $groupby = apply_filters_ref_array( 'ct_query_groupby', array( $groupby, &$this ) );

                /**
                 * Filters the JOIN clause of the query.
                 *
                 * Specifically for manipulating paging queries.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $join  The JOIN clause of the query.
                 * @param WP_Query &$this The WP_Query instance (passed by reference).
                 */
                $join = apply_filters_ref_array( 'ct_query_join_paged', array( $join, &$this ) );

                /**
                 * Filters the ORDER BY clause of the query.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $orderby The ORDER BY clause of the query.
                 * @param WP_Query &$this   The WP_Query instance (passed by reference).
                 */
                $orderby = apply_filters_ref_array( 'ct_query_orderby', array( $orderby, &$this ) );

                /**
                 * Filters the DISTINCT clause of the query.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $distinct The DISTINCT clause of the query.
                 * @param WP_Query &$this    The WP_Query instance (passed by reference).
                 */
                $distinct = apply_filters_ref_array( 'ct_query_distinct', array( $distinct, &$this ) );

                /**
                 * Filters the LIMIT clause of the query.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $limits The LIMIT clause of the query.
                 * @param WP_Query &$this  The WP_Query instance (passed by reference).
                 */
                $limits = apply_filters_ref_array( 'ct_querylimits', array( $limits, &$this ) );

                /**
                 * Filters the SELECT clause of the query.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $fields The SELECT clause of the query.
                 * @param WP_Query &$this  The WP_Query instance (passed by reference).
                 */
                $fields = apply_filters_ref_array( 'ct_query_fields', array( $fields, &$this ) );

                /**
                 * Filters all query clauses at once, for convenience.
                 *
                 * Covers the WHERE, GROUP BY, JOIN, ORDER BY, DISTINCT,
                 * fields (SELECT), and LIMITS clauses.
                 *
                 * @since 1.0.0
                 *
                 * @param array    $clauses The list of clauses for the query.
                 * @param WP_Query &$this   The WP_Query instance (passed by reference).
                 */
                $clauses = (array) apply_filters_ref_array( 'ct_query_clauses', array( compact( $pieces ), &$this ) );

                $where = isset( $clauses[ 'where' ] ) ? $clauses[ 'where' ] : '';
                $groupby = isset( $clauses[ 'groupby' ] ) ? $clauses[ 'groupby' ] : '';
                $join = isset( $clauses[ 'join' ] ) ? $clauses[ 'join' ] : '';
                $orderby = isset( $clauses[ 'orderby' ] ) ? $clauses[ 'orderby' ] : '';
                $distinct = isset( $clauses[ 'distinct' ] ) ? $clauses[ 'distinct' ] : '';
                $fields = isset( $clauses[ 'fields' ] ) ? $clauses[ 'fields' ] : '';
                $limits = isset( $clauses[ 'limits' ] ) ? $clauses[ 'limits' ] : '';
            }

            /**
             * Fires to announce the query's current selection parameters.
             *
             * For use by caching plugins.
             *
             * @since 1.0.0
             *
             * @param string $selection The assembled selection query.
             */
            do_action( 'ct_query_selection', $where . $groupby . $orderby . $limits . $join );

            /*
             * Filters again for the benefit of caching plugins.
             * Regular plugins should use the hooks above.
             */
            if ( !$q['suppress_filters'] ) {
                /**
                 * Filters the WHERE clause of the query.
                 *
                 * For use by caching plugins.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $where The WHERE clause of the query.
                 * @param WP_Query &$this The WP_Query instance (passed by reference).
                 */
                $where = apply_filters_ref_array( 'ct_query_where_request', array( $where, &$this ) );

                /**
                 * Filters the GROUP BY clause of the query.
                 *
                 * For use by caching plugins.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $groupby The GROUP BY clause of the query.
                 * @param WP_Query &$this   The WP_Query instance (passed by reference).
                 */
                $groupby = apply_filters_ref_array( 'ct_query_groupby_request', array( $groupby, &$this ) );

                /**
                 * Filters the JOIN clause of the query.
                 *
                 * For use by caching plugins.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $join  The JOIN clause of the query.
                 * @param WP_Query &$this The WP_Query instance (passed by reference).
                 */
                $join = apply_filters_ref_array( 'ct_query_join_request', array( $join, &$this ) );

                /**
                 * Filters the ORDER BY clause of the query.
                 *
                 * For use by caching plugins.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $orderby The ORDER BY clause of the query.
                 * @param WP_Query &$this   The WP_Query instance (passed by reference).
                 */
                $orderby = apply_filters_ref_array( 'ct_query_orderby_request', array( $orderby, &$this ) );

                /**
                 * Filters the DISTINCT clause of the query.
                 *
                 * For use by caching plugins.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $distinct The DISTINCT clause of the query.
                 * @param WP_Query &$this    The WP_Query instance (passed by reference).
                 */
                $distinct = apply_filters_ref_array( 'ct_query_distinct_request', array( $distinct, &$this ) );

                /**
                 * Filters the SELECT clause of the query.
                 *
                 * For use by caching plugins.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $fields The SELECT clause of the query.
                 * @param WP_Query &$this  The WP_Query instance (passed by reference).
                 */
                $fields = apply_filters_ref_array( 'ct_query_fields_request', array( $fields, &$this ) );

                /**
                 * Filters the LIMIT clause of the query.
                 *
                 * For use by caching plugins.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $limits The LIMIT clause of the query.
                 * @param WP_Query &$this  The WP_Query instance (passed by reference).
                 */
                $limits = apply_filters_ref_array( 'ct_query_limits_request', array( $limits, &$this ) );

                /**
                 * Filters all query clauses at once, for convenience.
                 *
                 * For use by caching plugins.
                 *
                 * Covers the WHERE, GROUP BY, JOIN, ORDER BY, DISTINCT,
                 * fields (SELECT), and LIMITS clauses.
                 *
                 * @since 1.0.0
                 *
                 * @param array    $pieces The pieces of the query.
                 * @param WP_Query &$this  The WP_Query instance (passed by reference).
                 */
                $clauses = (array) apply_filters_ref_array( 'ct_query_clauses_request', array( compact( $pieces ), &$this ) );

                $where = isset( $clauses[ 'where' ] ) ? $clauses[ 'where' ] : '';
                $groupby = isset( $clauses[ 'groupby' ] ) ? $clauses[ 'groupby' ] : '';
                $join = isset( $clauses[ 'join' ] ) ? $clauses[ 'join' ] : '';
                $orderby = isset( $clauses[ 'orderby' ] ) ? $clauses[ 'orderby' ] : '';
                $distinct = isset( $clauses[ 'distinct' ] ) ? $clauses[ 'distinct' ] : '';
                $fields = isset( $clauses[ 'fields' ] ) ? $clauses[ 'fields' ] : '';
                $limits = isset( $clauses[ 'limits' ] ) ? $clauses[ 'limits' ] : '';
            }

            if ( ! empty($groupby) )
                $groupby = 'GROUP BY ' . $groupby;
            if ( !empty( $orderby ) )
                $orderby = 'ORDER BY ' . $orderby;

            $found_rows = '';
            if ( !$q['no_found_rows'] && !empty($limits) )
                $found_rows = 'SQL_CALC_FOUND_ROWS';

            $this->request = $old_request = "SELECT $found_rows $distinct $fields FROM {$ct_table->db->table_name} $join WHERE 1=1 $where $groupby $orderby $limits";

            if ( !$q['suppress_filters'] ) {
                /**
                 * Filters the completed SQL query before sending.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $request The complete SQL query.
                 * @param WP_Query &$this   The WP_Query instance (passed by reference).
                 */
                $this->request = apply_filters_ref_array( 'ct_query_request', array( $this->request, &$this ) );
            }

            /**
             * Filters the posts array before the query takes place.
             *
             * Return a non-null value to bypass WordPress's default post queries.
             *
             * Filtering functions that require pagination information are encouraged to set
             * the `found_posts` and `max_num_pages` properties of the WP_Query object,
             * passed to the filter by reference. If WP_Query does not perform a database
             * query, it will not have enough information to generate these values itself.
             *
             * @since 1.0.0
             *
             * @param array|null $posts Return an array of post data to short-circuit WP's query,
             *                          or null to allow WP to run its normal queries.
             * @param WP_Query   $this  The WP_Query instance, passed by reference.
             */
            $this->results = apply_filters_ref_array( 'ct_results_pre_query', array( null, &$this ) );

            if ( 'ids' == $q['fields'] ) {
                if ( null === $this->results ) {
                    $this->results = $wpdb->get_col( $this->request );
                }

                $this->results = array_map( 'intval', $this->results );
                $this->result_count = count( $this->results );
                $this->set_found_results( $q, $limits );

                return $this->results;
            }

            if ( null === $this->results ) {
                $split_the_query = ( $old_request == $this->request && "{$ct_table->db->table_name}.*" == $fields && !empty( $limits ) && $q['items_per_page'] < 500 );

                /**
                 * Filters whether to split the query.
                 *
                 * Splitting the query will cause it to fetch just the IDs of the found posts
                 * (and then individually fetch each post by ID), rather than fetching every
                 * complete row at once. One massive result vs. many small results.
                 *
                 * @since 1.0.0
                 *
                 * @param bool     $split_the_query Whether or not to split the query.
                 * @param WP_Query $this            The WP_Query instance.
                 */
                $split_the_query = apply_filters( 'split_the_query', $split_the_query, $this );

                if ( $split_the_query ) {
                    // First get the IDs and then fill in the objects

                    $this->request = "SELECT $found_rows $distinct {$ct_table->db->table_name}.{$ct_table->db->primary_key} FROM {$ct_table->db->table_name} $join WHERE 1=1 $where $groupby $orderby $limits";

                    /**
                     * Filters the Post IDs SQL request before sending.
                     *
                     * @since 1.0.0
                     *
                     * @param string   $request The post ID request.
                     * @param WP_Query $this    The WP_Query instance.
                     */
                    $this->request = apply_filters( 'ct_query_request_ids', $this->request, $this );

                    $ids = $wpdb->get_col( $this->request );

                    if ( $ids ) {
                        $this->results = $ids;
                        $this->set_found_results( $q, $limits );
                        // TODO: Add caching utility
                        //_prime_post_caches( $ids, $q['update_post_term_cache'], $q['update_post_meta_cache'] );
                    } else {
                        $this->results = array();
                    }
                } else {
                    $this->results = $wpdb->get_results( $this->request );
                    $this->set_found_results( $q, $limits );
                }
            }

            // Convert to objects.
            if ( $this->results ) {
                $this->results = array_map( 'ct_get_object', $this->results );
            }

            if ( ! $q['suppress_filters'] ) {
                /**
                 * Filters the raw post results array, prior to status checks.
                 *
                 * @since 1.0.0
                 *
                 * @param array    $posts The post results array.
                 * @param WP_Query &$this The WP_Query instance (passed by reference).
                 */
                $this->results = apply_filters_ref_array( 'ct_query_results', array( $this->results, &$this ) );
            }

            if ( ! $q['suppress_filters'] ) {
                /**
                 * Filters the array of retrieved posts after they've been fetched and
                 * internally processed.
                 *
                 * @since 1.0.0
                 *
                 * @param array    $posts The array of retrieved posts.
                 * @param WP_Query &$this The WP_Query instance (passed by reference).
                 */
                $this->results = apply_filters_ref_array( 'ct_the_results', array( $this->results, &$this ) );
            }

            // Ensure that any posts added/modified via one of the filters above are
            // of the type WP_Post and are filtered.
            if ( $this->results ) {
                $this->result_count = count( $this->results );

                $this->results = array_map( 'ct_get_object', $this->results );

                //if ( $q['cache_results'] )
                    //update_post_caches($this->posts, $post_type, $q['update_post_term_cache'], $q['update_post_meta_cache']);

                //$this->post = reset( $this->posts );
            } else {
                $this->result_count = 0;
                $this->results = array();
            }

            return $this->results;

        }

        public function parse_query( $query = '' ) {

            if ( ! empty( $query ) ) {
                $this->init();
                $this->query = $this->query_vars = wp_parse_args( $query );
            } elseif ( ! isset( $this->query ) ) {
                $this->query = $this->query_vars;
            }

            $this->query_vars = $this->fill_query_vars($this->query_vars);

            $qv = &$this->query_vars;

            $qv['paged'] = absint($qv['paged']);

            // Fairly insane upper bound for search string lengths.
            if ( ! is_scalar( $qv['s'] ) || ( ! empty( $qv['s'] ) && strlen( $qv['s'] ) > 1600 ) ) {
                $qv['s'] = '';
            }

            /**
             * Fires after the main query vars have been parsed.
             *
             * @since 1.0.0
             *
             * @param WP_Query &$this The WP_Query instance (passed by reference).
             */
            do_action_ref_array( 'ct_parse_query', array( &$this ) );

        }

        /**
         * Fills in the query variables, which do not exist within the parameter.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $array Defined query variables.
         * @return array Complete query variables with undefined ones filled in empty.
         */
        public function fill_query_vars($array) {
            $keys = array(
              'paged'
            , 's'
            , 'sentence'
            , 'fields'
            );

            foreach ( $keys as $key ) {
                if ( ! isset($array[$key]) )
                    $array[$key] = '';
            }

            return $array;
        }

        /**
         * Generate SQL for the WHERE clause based on passed search terms.
         *
         * @since 1.0.0
         *
         * @param array $q Query variables.
         * @return string WHERE clause.
         */
        protected function parse_search( &$q ) {
            global $wpdb, $ct_table;

            $search = '';

            $search_fields = apply_filters( "ct_query_{$ct_table->name}_search_fields", array() );

            if( empty( $search_fields ) ) {
                return $search;
            }

            // added slashes screw with quote grouping when done early, so done later
            $q['s'] = stripslashes( $q['s'] );

            if ( empty( $_GET['s'] ) )
                $q['s'] = urldecode( $q['s'] );
            // there are no line breaks in <input /> fields
            $q['s'] = str_replace( array( "\r", "\n" ), '', $q['s'] );
            $q['search_terms_count'] = 1;
            if ( ! empty( $q['sentence'] ) ) {
                $q['search_terms'] = array( $q['s'] );
            } else {
                if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $q['s'], $matches ) ) {
                    $q['search_terms_count'] = count( $matches[0] );
                    $q['search_terms'] = $this->parse_search_terms( $matches[0] );
                    // if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence
                    if ( empty( $q['search_terms'] ) || count( $q['search_terms'] ) > 9 )
                        $q['search_terms'] = array( $q['s'] );
                } else {
                    $q['search_terms'] = array( $q['s'] );
                }
            }

            $n = ! empty( $q['exact'] ) ? '' : '%';
            $searchand = '';
            $q['search_orderby_title'] = array();

            /**
             * Filters the prefix that indicates that a search term should be excluded from results.
             *
             * @since 1.0.0
             *
             * @param string $exclusion_prefix The prefix. Default '-'. Returning
             *                                 an empty value disables exclusions.
             */
            $exclusion_prefix = apply_filters( 'ct_query_search_exclusion_prefix', '-' );

            foreach ( $q['search_terms'] as $term ) {
                // If there is an $exclusion_prefix, terms prefixed with it should be excluded.
                $exclude = $exclusion_prefix && ( $exclusion_prefix === substr( $term, 0, 1 ) );
                if ( $exclude ) {
                    $like_op  = 'NOT LIKE';
                    $andor_op = 'AND';
                    $term     = substr( $term, 1 );
                } else {
                    $like_op  = 'LIKE';
                    $andor_op = 'OR';
                }

                if ( $n && ! $exclude ) {
                    $like = '%' . $wpdb->esc_like( $term ) . '%';
                    //$q['search_orderby_title'][] = $wpdb->prepare( "{$wpdb->posts}.post_title LIKE %s", $like );
                }

                $like = $n . $wpdb->esc_like( $term ) . $n;

                $search_fields_where = array();
                $search_fields_args = array();

                foreach( $search_fields as $search_field ) {
                    $search_fields_where[] = "{$ct_table->db->table_name}.$search_field $like_op %s";
                    $search_fields_args[] = $like;
                }

                $search_where = "(" . implode( ") $andor_op (", $search_fields_where ) . ")";

                $search .= $wpdb->prepare( "{$searchand}($search_where)", $search_fields_args );

                //$search .= $wpdb->prepare( "{$searchand}(({$wpdb->posts}.post_title $like_op %s) $andor_op ({$wpdb->posts}.post_excerpt $like_op %s) $andor_op ({$wpdb->posts}.post_content $like_op %s))", $like, $like, $like );

                $searchand = ' AND ';
            }

            if ( ! empty( $search ) ) {
                $search = " AND ({$search}) ";
            }

            return $search;
        }

        /**
         * Check if the terms are suitable for searching.
         *
         * Uses an array of stopwords (terms) that are excluded from the separate
         * term matching when searching for posts. The list of English stopwords is
         * the approximate search engines list, and is translatable.
         *
         * @since 1.0.0
         *
         * @param array $terms Terms to check.
         * @return array Terms that are not stopwords.
         */
        protected function parse_search_terms( $terms ) {
            $strtolower = function_exists( 'mb_strtolower' ) ? 'mb_strtolower' : 'strtolower';
            $checked = array();

            $stopwords = $this->get_search_stopwords();

            foreach ( $terms as $term ) {
                // keep before/after spaces when term is for exact match
                if ( preg_match( '/^".+"$/', $term ) )
                    $term = trim( $term, "\"'" );
                else
                    $term = trim( $term, "\"' " );

                // Avoid single A-Z and single dashes.
                if ( ! $term || ( 1 === strlen( $term ) && preg_match( '/^[a-z\-]$/i', $term ) ) )
                    continue;

                if ( in_array( call_user_func( $strtolower, $term ), $stopwords, true ) )
                    continue;

                $checked[] = $term;
            }

            return $checked;
        }

        /**
         * Retrieve stopwords used when parsing search terms.
         *
         * @since 1.0.0
         *
         * @return array Stopwords.
         */
        protected function get_search_stopwords() {
            if ( isset( $this->stopwords ) )
                return $this->stopwords;

            /* translators: This is a comma-separated list of very common words that should be excluded from a search,
             * like a, an, and the. These are usually called "stopwords". You should not simply translate these individual
             * words into your language. Instead, look for and provide commonly accepted stopwords in your language.
             */
            $words = explode( ',', _x( 'about,an,are,as,at,be,by,com,for,from,how,in,is,it,of,on,or,that,the,this,to,was,what,when,where,who,will,with,www',
                'Comma-separated list of search stopwords in your language' ) );

            $stopwords = array();
            foreach ( $words as $word ) {
                $word = trim( $word, "\r\n\t " );
                if ( $word )
                    $stopwords[] = $word;
            }

            /**
             * Filters stopwords used when parsing search terms.
             *
             * @since 1.0.0
             *
             * @param array $stopwords Stopwords.
             */
            $this->stopwords = apply_filters( 'ct_search_stopwords', $stopwords );
            return $this->stopwords;
        }

        /**
         * Parse an 'order' query variable and cast it to ASC or DESC as necessary.
         *
         * @since 1.0.0
         * @access protected
         *
         * @param string $order The 'order' query variable.
         * @return string The sanitized 'order' query variable.
         */
        protected function parse_order( $order ) {
            if ( ! is_string( $order ) || empty( $order ) ) {
                return 'DESC';
            }

            if ( 'ASC' === strtoupper( $order ) ) {
                return 'ASC';
            } else {
                return 'DESC';
            }
        }

        /**
         * Set up the amount of found posts and the number of pages (if limit clause was used)
         * for the current query.
         *
         * @since 1.0.0
         * @access private
         *
         * @param array  $q      Query variables.
         * @param string $limits LIMIT clauses of the query.
         */
        private function set_found_results( $q, $limits ) {
            global $wpdb;
            // Bail if posts is an empty array. Continue if posts is an empty string,
            // null, or false to accommodate caching plugins that fill posts later.
            if ( $q['no_found_rows'] || ( is_array( $this->results ) && ! $this->results ) )
                return;

            if ( ! empty( $limits ) ) {
                /**
                 * Filters the query to run for retrieving the found posts.
                 *
                 * @since 1.0.0
                 *
                 * @param string   $found_posts The query to run to find the found posts.
                 * @param WP_Query &$this       The WP_Query instance (passed by reference).
                 */
                $this->found_results = $wpdb->get_var( apply_filters_ref_array( 'ct_found_results_query', array( 'SELECT FOUND_ROWS()', &$this ) ) );
            } else {
                $this->found_results = count( $this->results );
            }

            /**
             * Filters the number of found posts for the query.
             *
             * @since 1.0.0
             *
             * @param int      $found_posts The number of posts found.
             * @param WP_Query &$this       The WP_Query instance (passed by reference).
             */
            $this->found_results = apply_filters_ref_array( 'ct_found_results', array( $this->found_results, &$this ) );

            if ( ! empty( $limits ) )
                $this->max_num_pages = ceil( $this->found_results / $q['items_per_page'] );
        }

        /**
         * Sets up the query by parsing query string.
         *
         * @since 1.0.0
         *
         * @param string|array $query URL query string or array of query arguments.
         * @return array List of results.
         */
        public function query( $query ) {
            $this->init();
            $this->query = $this->query_vars = wp_parse_args( $query );
            return $this->get_results();
        }

    }

endif;