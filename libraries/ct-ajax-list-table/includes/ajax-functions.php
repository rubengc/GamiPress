<?php
/**
 * Ajax Functions
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

function ct_ajax_list_table_handle_request() {

    global $ct_table, $ct_query, $ct_list_table;

    if( ! isset( $_GET['object'] ) ) {
        wp_send_json_error();
    }

    $ct_table = ct_setup_table( $_GET['object'] );

    if( ! is_object( $ct_table ) ) {
        wp_send_json_error();
    }

    // Setup this constant to allow from CT_List_Table meet that this render comes from this plugin
    @define( 'IS_CT_AJAX_LIST_TABLE', true );

    $query_args = wp_parse_args( json_decode( $_GET['query_args'] ), array(
        'paged' => 1,
        'items_per_page' => 20,
    ) );

    if( isset( $_GET['paged'] ) ) {
        $query_args['paged'] = $_GET['paged'];
    }

    // Set up vars
    $ct_query = new CT_Query( $query_args );
    $ct_list_table = new CT_List_Table();

    $ct_list_table->prepare_items();

    ob_start();
    $ct_list_table->display();
    $output = ob_get_clean();

    wp_send_json_success( $output );

}
add_action( 'wp_ajax_ct_ajax_list_table_request', 'ct_ajax_list_table_handle_request' );