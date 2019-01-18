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

function ct_ajax_list_table_enqueue_scripts() {
    wp_enqueue_script( 'ct-ajax-list-table-js', CT_AJAX_LIST_TABLE_URL . 'assets/js/ct-ajax-list-table.js', array( 'jquery' ), CT_AJAX_LIST_TABLE_VER, true );

    wp_enqueue_style( 'ct-ajax-list-table-css', CT_AJAX_LIST_TABLE_URL . 'assets/css/ct-ajax-list-table.css', array(), CT_AJAX_LIST_TABLE_VER );
}