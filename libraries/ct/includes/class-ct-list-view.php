<?php
/**
 * List View class
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CT_List_View' ) ) :

    class CT_List_View extends CT_View {

        protected $per_page = 20;

        protected $columns = array();

        public function __construct( $name, $args ) {

            parent::__construct( $name, $args );

            $this->per_page = isset( $args['per_page'] ) ? $args['per_page'] : 20;
            $this->columns  = isset( $args['columns'] ) ? $args['columns'] : array();

        }

        public function add_hooks() {

            parent::add_hooks();

            add_filter( "manage_{$this->name}_columns", array( $this, 'get_columns' ) );
            add_filter( "manage_{$this->name}_sortable_columns", array( $this, 'get_sortable_columns' ) );

        }

        /**
         * Set columns passed from view columns arg.
         *
         * @since 1.0.0
         *
         * @param array $columns
         *
         * @return array
         */
        public function get_columns( $columns ) {

            foreach( $this->columns as $column => $column_args ) {

                if( is_array( $column_args ) && isset( $column_args['label'] ) ) {
                    // 'column_name' => array( 'label' => 'Column Label' )
                    $columns[$column] = $column_args['label'];
                } else if( gettype( $column_args ) === 'string' ) {
                    // 'column_name' => 'Column Label'
                    $columns[$column] = $column_args;
                }

            }

            return $columns;

        }

        /**
         * Set columns passed from view columns arg.
         *
         * @since 1.0.0
         *
         * @param array $sortable_columns
         *
         * @return array
         */
        public function get_sortable_columns( $sortable_columns ) {

            foreach( $this->columns as $column => $column_args ) {

                if( is_array( $column_args ) && isset( $column_args['sortable'] ) ) {
                    // 'column_name' => array( 'sortable' => 'sortable_setup' )
                    $sortable_columns[$column] = $column_args['sortable'];
                }

            }

            return $sortable_columns;

        }

        public function init() {

            global $ct_registered_tables, $ct_table, $ct_query, $ct_list_table;

            if( ! isset( $ct_registered_tables[$this->name] ) ) {
                return;
            }

            // Setup CT_Table
            $ct_table = $ct_registered_tables[$this->name];

            // Check for bulk delete
            if( isset( $_GET['action'] ) ) {

                if( $_GET['action'] === 'delete' ) {
                    // Deleting
                    $this->bulk_delete();
                }

            }

            // Check for delete action
            if( isset( $_GET['ct-action'] ) ) {

                if( $_GET['ct-action'] === 'delete' ) {
                    // Deleting
                    $this->delete();
                }

            }

            // Setup the query and the list table objects
            $ct_query = new CT_Query( $_GET );
            $ct_list_table = new CT_List_Table();

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

            $this->render_list_table_columns_preferences();
            $this->render_per_page_options();

        }

        /**
         * Render the list table columns preferences.
         *
         * @since 1.0.0
         */
        public function render_list_table_columns_preferences() {

            global $ct_table, $ct_list_table;

            // Set up vars
            $columns = $ct_list_table->get_columns();
            $hidden  = get_hidden_columns( $ct_table->name );

            if ( ! $columns )
                return;

            $legend = ! empty( $columns['_title'] ) ? $columns['_title'] : __( 'Columns' );
            ?>
            <fieldset class="metabox-prefs">
                <legend><?php echo $legend; ?></legend>
                <?php
                $special = array( '_title', 'cb' );

                foreach ( $columns as $column => $title ) {
                    // Can't hide these for they are special
                    if ( in_array( $column, $special ) || empty( $title ) ) {
                        continue;
                    }

                    $id = "$column-hide";
                    echo '<label>';
                    echo '<input class="hide-column-tog" name="' . $id . '" type="checkbox" id="' . $id . '" value="' . $column . '"' . checked( ! in_array( $column, $hidden ), true, false ) . ' />';
                    echo "$title</label>\n";
                }
                ?>
            </fieldset>
            <?php
        }

        /**
         * Render the items per page option
         *
         * @since 1.0.0
         */
        public function render_per_page_options() {

            global $ct_table;

            if ( ! $this->per_page ) {
                return;
            }

            // Set up vars
            $per_page_label = __( 'Number of items per page:' );

            $option = str_replace( '-', '_', "edit_{$ct_table->name}_per_page" );

            $per_page = (int) get_user_option( $option );

            if ( empty( $per_page ) || $per_page < 1 )
                $per_page = $this->per_page;

            $per_page = apply_filters( "{$option}", $per_page );

            // This needs a submit button
            add_filter( 'screen_options_show_submit', '__return_true' );

            ?>
            <fieldset class="screen-options">
                <legend><?php _e( 'Pagination' ); ?></legend>
                <?php if ( $per_page_label ) : ?>
                    <label for="<?php echo esc_attr( $option ); ?>"><?php echo $per_page_label; ?></label>
                    <input type="number" step="1" min="1" max="999" class="screen-per-page" name="wp_screen_options[value]"
                           id="<?php echo esc_attr( $option ); ?>" maxlength="3"
                           value="<?php echo esc_attr( $per_page ); ?>" />
                <?php endif; ?>
                <input type="hidden" name="wp_screen_options[option]" value="<?php echo esc_attr( $option ); ?>" />
            </fieldset>
            <?php
        }

        /**
         * Screen option value before it is set.
         *
         * @since 1.0.0
         *
         * @param bool|int $value_to_set    Screen option value to set. Default false to skip.
         * @param string   $option          The option name.
         * @param int      $value           The option value.
         *
         * @return bool|mixed               False to skip or any other value to set as option value
         */
        public function set_screen_settings( $value_to_set, $option, $value ) {

            global $ct_table, $ct_list_table;

            if( ! $ct_table ) {
                return $value_to_set;
            }

            if( ! $ct_list_table ) {
                return $value_to_set;
            }

            $view_settings = array(
                str_replace( '-', '_', "edit_{$ct_table->name}_per_page" ) // Per page
            );

            // Columns hidden setting
            $columns = $ct_list_table->get_columns();
            $special = array( '_title', 'cb' );

            foreach ( $columns as $column => $title ) {
                // Can't hide these for they are special
                if ( in_array( $column, $special ) || empty( $title ) )
                    continue;

                $view_settings[] = "$column-hide";
            }

            // If option is on this view settings list, then save it
            if( in_array( $option, $view_settings ) ) {
                $value_to_set = $value;
            }

            return $value_to_set;

        }

        public function bulk_delete() {

            global $ct_table;

            // If not CT object, die
            if ( ! $ct_table )
                wp_die( __( 'Invalid item type.' ) );

            // If not CT object allow ui, die
            if ( ! $ct_table->show_ui ) {
                wp_die( __( 'Sorry, you are not allowed to delete items of this type.' ) );
            }

            // Nonce check
            if ( ! isset( $_REQUEST['_wpnonce'] ) ) {
                wp_die( __( 'Sorry, you are not allowed to delete items of this type.' ) );
            }

            if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-' . sanitize_key( $ct_table->labels->plural_name ) ) ) {
                wp_die( __( 'Sorry, you are not allowed to delete items of this type.' ) );
            }

            $object_ids = array();

            // Check received items
            if ( ! empty( $_REQUEST['item'] ) ) {
                $object_ids = array_map('intval', $_REQUEST['item']);
            }

            $deleted = 0;

			foreach ( (array) $object_ids as $object_id ) {

                // If not current user can delete, die
                if ( ! current_user_can( $ct_table->cap->delete_item, $object_id ) ) {
                    wp_die( __( 'Sorry, you are not allowed to delete this item.' ) );
                }

                if ( ! ct_delete_object( $object_id, true ) )
                    wp_die( __( 'Error in deleting.' ) );

                $deleted++;
            }

            $location = add_query_arg( array( 'deleted' => $deleted ), $this->get_link() );

            wp_redirect( $location );
            exit;
        }

        public function delete() {

            global $ct_table;

            // If not CT object, die
            if ( ! $ct_table ) {
                wp_die( __( 'Invalid item type.' ) );
            }

            // If not CT object allow ui, die
            if ( ! $ct_table->show_ui ) {
                wp_die( __( 'Sorry, you are not allowed to delete items of this type.' ) );
            }

            $primary_key = $ct_table->db->primary_key;

            // Object ID is required
            if( ! isset( $_GET[$primary_key] ) ) {
                wp_die( __( 'Sorry, you are not allowed to delete items of this type.' ) );
            }

            $object_id = (int) $_GET[$primary_key];

            // Nonce check
            if ( ! isset( $_REQUEST['_wpnonce'] ) ) {
                wp_die( __( 'Sorry, you are not allowed to delete this item.' ) );
            }

            if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'ct_delete_' . $object_id ) ) {
                wp_die( __( 'Sorry, you are not allowed to delete this item.' ) );
            }

            // If user can not delete it, bail
            if ( ! current_user_can( $ct_table->cap->delete_item, $object_id ) ) {
                wp_die( __( 'Sorry, you are not allowed to delete this item.' ) );
            }

            if ( ! ct_delete_object( $object_id ) )
                wp_die( __( 'Error in deleting.' ) );

            $location = add_query_arg( array( 'deleted' => 1 ), $this->get_link() );

            wp_redirect( $location );
            exit;

        }

        /**
         * View content.
         *
         * @since 1.0.0
         */
        public function render() {

            global $ct_table, $ct_list_table;

            $ct_list_table->prepare_items();

            $bulk_counts = array(
                'updated'   => isset( $_REQUEST['updated'] )   ? absint( $_REQUEST['updated'] )   : 0,
                'locked'    => isset( $_REQUEST['locked'] )    ? absint( $_REQUEST['locked'] )    : 0,
                'deleted'   => isset( $_REQUEST['deleted'] )   ? absint( $_REQUEST['deleted'] )   : 0,
                'trashed'   => isset( $_REQUEST['trashed'] )   ? absint( $_REQUEST['trashed'] )   : 0,
                'untrashed' => isset( $_REQUEST['untrashed'] ) ? absint( $_REQUEST['untrashed'] ) : 0,
            );

            $bulk_messages = array(
                'updated'   => _n( '%s item updated.', '%s items updated.', $bulk_counts['updated'] ),
                'locked'    => ( 1 == $bulk_counts['locked'] ) ? __( '1 item not updated, somebody is editing it.' ) :
                    _n( '%s item not updated, somebody is editing it.', '%s items not updated, somebody is editing them.', $bulk_counts['locked'] ),
                'deleted'   => _n( '%s item permanently deleted.', '%s items permanently deleted.', $bulk_counts['deleted'] ),
                'trashed'   => _n( '%s item moved to the Trash.', '%s items moved to the Trash.', $bulk_counts['trashed'] ),
                'untrashed' => _n( '%s item restored from the Trash.', '%s items restored from the Trash.', $bulk_counts['untrashed'] ),
            );

            /**
             * Filters the bulk action updated messages.
             *
             * @since 1.0.0
             *
             * @param array $bulk_messages Arrays of messages. Messages are keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
             * @param array $bulk_counts   Array of item counts for each message, used to build internationalized strings.
             */
            $bulk_messages = apply_filters( 'bulk_object_updated_messages', $bulk_messages, $bulk_counts );
            $bulk_counts = array_filter( $bulk_counts );

            ?>

            <div class="wrap">

                <h1 class="wp-heading-inline"><?php echo $ct_table->labels->plural_name; ?></h1>

                <?php if ( property_exists( $ct_table->views, 'add' ) && $ct_table->views->add && current_user_can( $ct_table->cap->create_items ) ) :
                    echo ' <a href="' . esc_url( $ct_table->views->add->get_link() ) . '" class="page-title-action">' . esc_html( $ct_table->labels->add_new_item ) . '</a>';
                endif; ?>

                <hr class="wp-header-end">

                <?php
                // If we have a bulk message to issue:
                $messages = array();
                foreach ( $bulk_counts as $message => $count ) {
                    if ( isset( $bulk_messages[ $message ] ) )
                        $messages[] = sprintf( $bulk_messages[ $message ], number_format_i18n( $count ) );

                    //if ( $message == 'trashed' && isset( $_REQUEST['ids'] ) ) {
                        //$ids = preg_replace( '/[^0-9,]/', '', $_REQUEST['ids'] );
                        //$messages[] = '<a href="' . esc_url( wp_nonce_url( "edit.php?post_type=$post_type&doaction=undo&action=untrash&ids=$ids", "bulk-posts" ) ) . '">' . __('Undo') . '</a>';
                    //}
                }

                if ( $messages )
                    echo '<div id="message" class="updated notice is-dismissible"><p>' . join( ' ', $messages ) . '</p></div>';
                unset( $messages );

                $_SERVER['REQUEST_URI'] = remove_query_arg( array( 'locked', 'skipped', 'updated', 'deleted', 'trashed', 'untrashed' ), $_SERVER['REQUEST_URI'] );
                ?>

                <?php $ct_list_table->views(); ?>

                <form id="ct-list-filter" method="get">

                    <input type="hidden" name="page" value="<?php echo esc_attr( $this->args['menu_slug'] ); ?>" />

                    <?php $ct_list_table->search_box( $ct_table->labels->search_items, $ct_table->name ); ?>

                    <?php $ct_list_table->display(); ?>

                </form>

                <div id="ajax-response"></div>
                <br class="clear" />

            </div>

            <?php
        }

    }

endif;