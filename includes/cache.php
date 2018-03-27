<?php
/**
 * GamiPress Cache Class
 *
 * Used to store commonly used query results
 *
 * @package     GamiPress\Cache
 * @since       1.4.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Cache {

    /**
     * @var         array $hidden_achievements_ids Contains hidden achievements ids separated by achievement type
     * @since       1.4.7
     */
    public $hidden_achievements_ids = array();

    /**
     * @var         array $installed_tables Contains table checks from gamipress_database_table_exists()
     * @since       1.4.7
     */
    public $installed_tables = array();

    /**
     * @var         array $installed_table_fields Contains table columns checks from gamipress_database_table_has_column()
     * @since       1.4.7
     */
    public $installed_table_columns = array();

}