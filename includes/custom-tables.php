<?php
/**
 * Custom Tables
 *
 * @package     GamiPress\Custom_Tables
 * @since       1.2.8
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_DIR . 'includes/custom-tables/logs.php';
require_once GAMIPRESS_DIR . 'includes/custom-tables/user-earnings.php';

/**
 * Register all GamiPress Custom DB Tables
 *
 * @since  1.2.8
 *
 * @return void
 */
function gamipress_register_custom_tables() {

    // User Earnings Table
    ct_register_table( 'gamipress_user_earnings', array(
        'singular' => __( 'User Earning', 'gamipress' ),
        'plural' => __( 'User Earnings', 'gamipress' ),
        'labels' => array(
        ),
        'show_ui' => false,
        'version' => 1,
        'schema' => array(
            'user_earning_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'auto_increment' => true,
                'primary_key' => true,
            ),
            'user_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),
            'post_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),
            'post_type' => array(
                'type' => 'varchar',
                'length' => '50',
            ),
            'points' => array(
                'type' => 'bigint',
            ),
            'points_type' => array(
                'type' => 'varchar',
                'length' => '50',
            ),
            'date' => array(
                'type' => 'datetime',
                'default' => '0000-00-00 00:00:00'
            )
        ),
    ) );

    // Logs Table
    ct_register_table( 'gamipress_logs', array(
        'singular' => __( 'Log', 'gamipress' ),
        'plural' => __( 'Logs', 'gamipress' ),
        'show_ui' => true,
        'version' => 1,
        'supports' => array( 'meta' ),
        'views' => array(
            'list' => array(
                'menu_title' => __( 'Logs', 'gamipress' ),
                'parent_slug' => 'gamipress'
            ),
            'add' => array(
                'show_in_menu' => false,
            ),
            'edit' => array(
                'show_in_menu' => false,
            ),
        ),
        'schema' => array(
            'log_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'auto_increment' => true,
                'primary_key' => true,
            ),
            'title' => array(
                'type' => 'text',
            ),
            'description' => array(
                'type' => 'text',
            ),
            'type' => array(
                'type' => 'text',
            ),
            'access' => array(
                'type' => 'varchar',
                'length' => '50',
            ),
            'user_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),
            'date' => array(
                'type' => 'datetime',
                'default' => '0000-00-00 00:00:00'
            )
        ),
    ) );

}
add_action( 'ct_init', 'gamipress_register_custom_tables' );