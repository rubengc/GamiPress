<?php
/**
 * Scripts
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function ct_ajax_list_table_register_scripts() {

    // Stylesheets
    wp_register_style( 'ct-ajax-list-table-css', CT_AJAX_LIST_TABLE_URL . 'assets/css/ct-ajax-list-table.css', array( ), CT_AJAX_LIST_TABLE_VER, 'all' );

    // Scripts
    wp_register_script( 'ct-ajax-list-table-js', CT_AJAX_LIST_TABLE_URL . 'assets/js/ct-ajax-list-table.js', array( 'jquery' ), CT_AJAX_LIST_TABLE_VER, true );


}
add_action( 'admin_init', 'ct_ajax_list_table_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function ct_ajax_list_table_enqueue_scripts() {

    // Stylesheets
    wp_enqueue_style( 'ct-ajax-list-table-css' );

    // Localize script
    wp_localize_script( 'ct-ajax-list-table-js', 'ct_ajax_list_table', array(
        'nonce' => wp_create_nonce( 'ct_ajax_list_table' ),
    ) );

    wp_enqueue_script( 'ct-ajax-list-table-js' );


}