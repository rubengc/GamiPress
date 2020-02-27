<?php
/**
 * Functions
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Render an ajax list table
 *
 * @since 1.0.0
 *
 * @param CT_Table|string   $table      CT_Table object or CT_Table name
 * @param array             $query_args Query parameters
 * @param array             $view_args  View parameters
 */
function ct_render_ajax_list_table( $table, $query_args = array(), $view_args = array() ) {

    global $ct_table, $ct_query, $ct_list_table, $ct_ajax_list_items_per_page;

    $ct_table = ct_setup_table( $table );

    if( is_object( $ct_table ) ) {

        // Setup this constant to allow from CT_List_Table meet that this render comes from this plugin
        @define( 'IS_CT_AJAX_LIST_TABLE', true );

        // Enqueue assets
        ct_ajax_list_table_enqueue_scripts();

        // Setup query args
        $query_args = wp_parse_args( $query_args, array(
            'paged' => 1,
            'items_per_page' => 20,
        ) );

        // setup view args
        $view_args = wp_parse_args( $view_args, array(
            'views' => true,
            'search_box' => true,
        ) );

        // Add a filter to override the items per page user setting
        $ct_ajax_list_items_per_page = $query_args['items_per_page'];
        add_filter( 'edit_' . $ct_table->name . '_per_page', 'ct_ajax_list_override_items_per_page' );

        // Set up vars
        $ct_query = new CT_Query( $query_args );
        $ct_list_table = new CT_List_Table();

        $ct_list_table->prepare_items();

        ?>

        <div class="wrap ct-ajax-list-table" data-object="<?php echo $ct_table->name; ?>" data-query-args="<?php echo str_replace( '"', "'", json_encode( $query_args ) ); ?>">

            <?php
            if( $view_args['views'] ) {
                $ct_list_table->views();
            }
            ?>

            <?php // <form id="ct-list-filter" method="get"> ?>

                <?php
                if( $view_args['search_box'] ) {
                    $ct_list_table->search_box( $ct_table->labels->search_items, $ct_table->name );
                }
                ?>

                <?php ct_render_ajax_list_tablenav( $ct_list_table, 'top' ); ?>

                <table class="wp-list-table <?php echo implode( ' ', $ct_list_table->get_table_classes() ); ?>">
                    <thead>
                    <tr>
                        <?php $ct_list_table->print_column_headers(); ?>
                    </tr>
                    </thead>

                    <?php $singular = $ct_list_table->_args['singular']; ?>
                    <tbody id="the-list"<?php
                    if ( $singular ) {
                        echo " data-wp-lists='list:$singular'";
                    } ?>>
                    <?php $ct_list_table->display_rows_or_placeholder(); ?>
                    </tbody>

                    <tfoot>
                    <tr>
                        <?php $ct_list_table->print_column_headers( false ); ?>
                    </tr>
                    </tfoot>

                </table>

                <?php ct_render_ajax_list_tablenav( $ct_list_table, 'bottom' ); ?>

            <?php // </form> ?>

            <div id="ajax-response"></div>
            <br class="clear" />

        </div>

        <?php

    }

}

/**
 * Overrides the items per page user setting
 *
 * @since 1.0.0
 *
 * @param int $items_per_page
 *
 * @return int
 */
function ct_ajax_list_override_items_per_page( $items_per_page ) {

    global $ct_ajax_list_items_per_page;

    if( absint( $ct_ajax_list_items_per_page ) !== 0 ) {
        return absint( $ct_ajax_list_items_per_page );
    }

    return $items_per_page;

}

/**
 * Custom table nav function
 *
 * @param CT_List_Table $ct_list_table
 * @param string        $which
 */
function ct_render_ajax_list_tablenav( $ct_list_table, $which ) {
    ?>
    <div class="tablenav <?php echo esc_attr( $which ); ?>">

        <?php if ( $ct_list_table->has_items() ): ?>
            <div class="alignleft actions bulkactions">
                <?php $ct_list_table->bulk_actions( $which ); ?>
            </div>
        <?php endif;
        $ct_list_table->extra_tablenav( $which );
        $ct_list_table->pagination( $which );
        ?>

        <br class="clear" />
    </div>
    <?php
}