<?php
/**
 * List Table class
 *
 * Based on WP_Posts_List_Table class
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CT_List_Table' ) ) :

    class CT_List_Table extends WP_List_Table {

        /**
         * Get things started
         *
         * @access public
         * @since  1.0.0
         *
         * @param array $args Optional. Arbitrary display and query arguments to pass through
         *                    the list table. Default empty array.
         */
        public function __construct( $args = array() ) {
            global $ct_table;

            parent::__construct( array(
                'singular' => $ct_table->labels->singular_name,
                'plural' => $ct_table->labels->plural_name,
                'screen' => convert_to_screen( $ct_table->labels->plural_name )
            ) );
        }

        /**
         * Show the search field
         *
         * @since 1.0.0
         *
         * @param string $text Label for the search box
         * @param string $input_id ID of the search box
         *
         * @return void
         */
        public function search_box( $text, $input_id ) {
            if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
                return;

            $input_id = $input_id . '-search-input';

            if ( ! empty( $_REQUEST['orderby'] ) )
                echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
            if ( ! empty( $_REQUEST['order'] ) )
                echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
            ?>
            <p class="search-box">
                <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
                <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
                <?php submit_button( $text, 'button', false, false, array( 'ID' => 'search-submit' ) ); ?>
            </p>
            <?php
        }

        /**
         * Retrieve the view types
         *
         * @access public
         * @since 1.0.0
         *
         * @return array $views All the views available
         */
        public function get_views() {

            global $wpdb, $ct_table;

            /**
             * Utility filter to generate views based on a given field
             *
             * @since 1.0.0
             *
             * @param string $field_id The field id to generate the views
             *
             * @return string
             */
            $field_id = apply_filters( "ct_list_{$ct_table->name}_views_field", '' );

            /**
             * Labels for the filed id
             *
             * @since 1.0.0
             *
             * @param array $field_labels   Field labels in format array( 'field_value' => 'Field Label' )
             *                              Field values that are not listed here won't get displayed
             *
             * @return array
             */
            $field_labels = apply_filters( "ct_list_{$ct_table->name}_views_field_labels", array() );

            $views = array();

            // Check if field ID and labels has been passed and also is field is registered on the table (not as meta)
            if( ! empty( $field_id ) && ! empty( $field_labels ) && isset( $ct_table->db->schema->fields[$field_id] ) ) {

                // Get the number of entries per each different field value
                $results = $wpdb->get_results( "SELECT {$field_id}, COUNT( * ) AS num_entries FROM {$ct_table->db->table_name} GROUP BY {$field_id}", ARRAY_A );
                $counts  = array();

                // Loop them to build the counts array
                foreach( $results as $result ) {
                    $counts[$result[$field_id]] = absint( $result['num_entries'] );
                }

                $list_link = ct_get_list_link( $ct_table->name );
                $current = isset( $_GET[$field_id] ) ? $_GET[$field_id] : '';

                // Setup the 'All' view
                $all_count =  absint( $wpdb->get_var( "SELECT COUNT( * ) FROM {$ct_table->db->table_name}" ) );
                $views['all'] = '<a href="' . $list_link . '" class="' . ( empty( $current ) ? 'current' : '' ) . '">' . __( 'All', 'ct' ) . ' <span class="count">(' . $all_count . ')</span></a>';

                foreach( $counts as $value => $count ) {

                    // Skip fields that are not intended to being displayed
                    if( ! isset( $field_labels[$value] ) ) {
                        continue;
                    }

                    $label = $field_labels[$value];
                    $url = $list_link . '&' . $field_id . '=' . $value;

                    $views[$value] = '<a href="' . $url . '" class="' . ( $current === $value ? 'current' : '' ) . '">' . $label . ' <span class="count">(' . $count . ')</span>' . '</a>';
                }

            }

            /**
             * Available filter to past custom views
             *
             * @since 1.0.0
             *
             * @param array $views  An array of views links.
             *                      Array format: array( 'link_id' => 'link' )
             *                      Link format: '<a href="#">{label} <span class="count">({count})</span></a>'
             */
            $views = apply_filters( "{$ct_table->name}_get_views", $views );

            return $views;
        }

        /**
         *
         * @return array
         */
        protected function get_bulk_actions() {

            global $ct_table;

            $actions = array();

            if ( current_user_can( $ct_table->cap->delete_items ) ) {
                $actions['delete'] = __( 'Delete Permanently' );
            }

            $actions = apply_filters( "{$ct_table->name}_bulk_actions", $actions );

            return $actions;
        }

        /**
         *
         * @return array
         */
        protected function get_table_classes() {
            global $ct_table;

            return array( 'widefat', 'fixed', 'striped', $ct_table->name );
        }

        /**
         * Retrieve the table columns
         *
         * @access public
         * @since 1.0.0
         * @return array $columns Array of all the list table columns
         */
        public function get_columns() {
            global $ct_table;

            $columns = array();
            $bulk_actions = $this->get_bulk_actions();

            if( ! empty( $bulk_actions ) ) {
                $columns['cb'] = '<input type="checkbox" />';
            }

            /**
             * Filters the columns displayed in the list table of a specific CT table.
             *
             * @since 1.0.0
             *
             * @param array  $posts_columns An array of column names.
             * @param CT_Table $ct_table    The table object.
             */
            return apply_filters( "manage_{$ct_table->name}_columns", $columns, $ct_table );
        }

        /**
         * Retrieve the table's sortable columns
         *
         * @access public
         * @since 1.0.0
         * @return array Array of all the sortable columns
         */
        public function get_sortable_columns() {
            global $ct_table;

            $sortable_columns = array();

            /**
             * Filters the sortable columns in the list table of a specific CT table.
             *
             * Format:
             * 'internal-name' => 'orderby'
             * or
             * 'internal-name' => array( 'orderby', true )
             * The second format will make the initial sorting order be descending
             *
             * @since 1.0.0
             *
             * @param array     $sortable_columns   An array of column names.
             * @param CT_Table  $ct_table           The table object.
             */
            return apply_filters( "manage_{$ct_table->name}_sortable_columns", $sortable_columns, $ct_table );
        }

        /**
         * This function renders most of the columns in the list table.
         *
         * @access public
         * @since 1.0.0
         *
         * @param stdClass  $item           The current object.
         * @param string    $column_name    The name of the column
         * @return string                   The column value.
         */
        public function column_default( $item, $column_name ) {
            global $ct_table;

            $value = isset( $item->$column_name ) ? $item->$column_name : '';

            $primary_key = $ct_table->db->primary_key;

            ob_start();
            /**
             * Fires for each custom column of a specific CT table in the list table.
             *
             * The dynamic portion of the hook name, `$ct_table->name`, refers to the CT table name.
             *
             * @since 1.0.0
             *
             * @param string    $column_name The name of the column to display.
             * @param int       $object_id   The current object ID.
             * @param stdClass  $object      The current object.
             * @param CT_Table  $ct_table    The CT table object.
             */
            do_action( "manage_{$ct_table->name}_custom_column", $column_name, $item->$primary_key, $item, $ct_table );
            $custom_output = ob_get_clean();

            if( ! empty( $custom_output ) ) {
                return $custom_output;
            }

            $bulk_actions = $this->get_bulk_actions();

            $first_column_index = ( ! empty( $bulk_actions ) ) ? 1 : 0;

            $can_edit_item = current_user_can( $ct_table->cap->edit_item, $item->$primary_key );
            $columns = $this->get_columns();
            $columns_keys = array_keys( $columns );

            if( $column_name === $columns_keys[$first_column_index] && $can_edit_item ) {

                // Turns first column into a text link with url to edit the item
                $value = sprintf( '<a href="%s" aria-label="%s">%s</a>',
                    ct_get_edit_link( $ct_table->name, $item->$primary_key ),
                    esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $value ) ),
                    $value
                );

                // Small screens toggle
                $value .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details' ) . '</span></button>';

            }

            return $value;
        }

        /**
         * Generates and displays row action links.
         *
         * @since 4.3.0
         * @access protected
         *
         * @param object $item        The item being acted upon.
         * @param string $column_name Current column name.
         * @param string $primary     Primary column name.
         *
         * @return string Row actions output for posts.
         */
        protected function handle_row_actions( $item, $column_name, $primary ) {
            if ( $primary !== $column_name ) {
                return '';
            }

            global $ct_table;

            $primary_key = $ct_table->db->primary_key;
            $actions = array();

            if ( $ct_table->views->edit && current_user_can( $ct_table->cap->edit_item, $item->$primary_key ) ) {
                $actions['edit'] = sprintf(
                    '<a href="%s" aria-label="%s">%s</a>',
                    ct_get_edit_link( $ct_table->name, $item->$primary_key ),
                    esc_attr( __( 'Edit' ) ),
                    __( 'Edit' )
                );
            }

            if ( current_user_can( $ct_table->cap->delete_item, $item->$primary_key ) ) {
                $actions['delete'] = sprintf(
                    '<a href="%s" class="submitdelete" onclick="%s" aria-label="%s">%s</a>',
                    ct_get_delete_link( $ct_table->name, $item->$primary_key ),
                    "return confirm('" .
                        esc_attr( __( "Are you sure you want to delete this item?\\n\\nClick \\'Cancel\\' to go back, \\'OK\\' to confirm the delete." ) ) .
                    "');",
                    esc_attr( __( 'Delete permanently' ) ),
                    __( 'Delete Permanently' )
                );
            }

            /**
             * Filters the array of row action links on the Posts list table.
             *
             * The filter is evaluated only for non-hierarchical post types.
             *
             * @since 2.8.0
             *
             * @param array $actions An array of row action links. Defaults are
             *                         'Edit', 'Quick Edit', 'Restore, 'Trash',
             *                         'Delete Permanently', 'Preview', and 'View'.
             * @param WP_Post $post The post object.
             */
            $actions = apply_filters( "{$ct_table->name}_row_actions", $actions, $item );

            return $this->row_actions( $actions );
        }

        /**
         * Handles the checkbox column output.
         *
         * @since 1.0.0
         *
         * @param WP_Post $item The current WP_Post object.
         */
        public function column_cb( $item ) {
            global $ct_table;

            $primary_key = $ct_table->db->primary_key;

            if ( current_user_can( $ct_table->cap->edit_items ) ): ?>
                <label class="screen-reader-text" for="cb-select-<?php echo $item->$primary_key; ?>"><?php
                    printf( __( 'Select Item #%d' ), $item->$primary_key );
                    ?></label>
                <input id="cb-select-<?php echo $item->$primary_key; ?>" type="checkbox" name="item[]" value="<?php echo $item->$primary_key; ?>" />
                <div class="locked-indicator">
                    <span class="locked-indicator-icon" aria-hidden="true"></span>
                    <span class="screen-reader-text"><?php
                        printf(
                        /* translators: %d: item ID */
                            __( '&#8220;Item #%d&#8221; is locked' ),
                            $item->$primary_key
                        );
                        ?></span>
                </div>
            <?php endif;
        }

        /**
         * Renders the message to be displayed when there are no results.
         *
         * @since  1.0.0
         */
        function no_items() {
            global $ct_table;

            echo $ct_table->labels->not_found;
        }

        public function prepare_items() {

            global $ct_table, $ct_query;

            // Get per page setting
            $per_page = $this->get_items_per_page( 'edit_' . $ct_table->name . '_per_page' );

            // Update query vars based on settings
            $ct_query->query_vars['items_per_page'] = $per_page;

            // Get query results
            $this->items = $ct_query->get_results();

            $total_items = $ct_query->found_results;

            // Setup pagination args based on items found and per page settings
            $this->set_pagination_args( array(
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil( $total_items / $per_page )
            ) );
        }

    }

endif;