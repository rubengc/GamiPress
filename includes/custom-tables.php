<?php
/**
 * Custom Tables
 *
 * @package     GamiPress\Custom_Tables
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.2.8
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Custom Tables
require_once GAMIPRESS_DIR . 'includes/custom-tables/logs.php';
require_once GAMIPRESS_DIR . 'includes/custom-tables/user-earnings.php';
// Rest API
require_once GAMIPRESS_DIR . 'includes/api/logs.php';
require_once GAMIPRESS_DIR . 'includes/api/user-earnings.php';


/**
 * Register all GamiPress Custom DB Tables
 *
 * @since   1.2.8
 * @updated 1.4.3 User Earnings v2: Added the field title
 * @updated 1.4.7 Logs v2: Added the field trigger_type
 * @updated 1.5.1 Logs v3: Removed the field description and make type and trigger_type indexes
 *
 * @return void
 */
function gamipress_register_custom_tables() {

    // User Earnings Table
    ct_register_table( 'gamipress_user_earnings', array(
        'singular' => __( 'User Earning', 'gamipress' ),
        'plural' => __( 'User Earnings', 'gamipress' ),
        'labels' => array(
            'not_found' => __( 'This user has not earned anything', 'gamipress' )
        ),
        'show_ui' => true,
        'show_in_rest' => true,
        'rest_base' => 'gamipress-user-earnings',
        'version' => 4,
        'global' => gamipress_is_network_wide_active(),
        'capability' => gamipress_get_manager_capability(),
        'supports' => array( 'meta' ),
        'views' => array(
            'list' => array(
                'menu_title' => __( 'User Earnings', 'gamipress' ),
                'parent_slug' => 'gamipress',
            ),
            'add' => false,
            'edit' => array(
                'show_in_menu' => false,
            ),
        ),
        'schema' => array(
            'user_earning_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'auto_increment' => true,
                'primary_key' => true,
            ),
            'title' => array(
                'type' => 'text',
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
        'show_in_rest' => true,
        'rest_base' => 'gamipress-logs',
        'version' => 5,
        'global' => gamipress_is_network_wide_active(),
        'capability' => gamipress_get_manager_capability(),
        'supports' => array( 'meta' ),
        'views' => array(
            'list' => array(
                'menu_title' => __( 'Logs', 'gamipress' ),
                'parent_slug' => 'gamipress',
            ),
            'add' => false,
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
            'type' => array(
                'type' => 'text',
                'key' => true,
            ),
            'trigger_type' => array(
                'type' => 'varchar',
                'length' => '255',
                'key' => true,
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
                'default' => '0000-00-00 00:00:00',
            )
        ),
    ) );

}
add_action( 'ct_init', 'gamipress_register_custom_tables' );