<?php
/**
 * Database Table class
 *
 * @credits  Jhon James Jacob (https://jjj.blog)
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CT_DataBase' ) ) :

    class CT_DataBase {

        /**
         * @var string Table name, without the global table prefix
         */
        protected $name = '';

        /**
         * @var string Table primary key
         */
        public $primary_key = '';

        /**
         * @var int Database version
         */
        protected $version = 0;

        /**
         * @var boolean Is this table for a site, or global
         */
        public $global = false;

        /**
         * @var string Database version key (saved in _options or _sitemeta)
         */
        protected $db_version_key = '';

        /**
         * @var string Current database version
         */
        protected $db_version = 0;

        /**
         * @var string Table name
         */
        public $table_name = '';

        /**
         * @var CT_DataBase_Schema Table schema
         */
        public $schema = '';

        /**
         * @var string Database engine for table (default InnoDB)
         */
        public $engine = '';

        /**
         * @var string Database character-set & collation for table
         */
        public $charset_collation = '';

        /**
         * @var WPDB Database object (usually $GLOBALS['wpdb'])
         */
        public $db = false;

        /**
         * @var string Used to meet if database is in a group of tables to reduce database exists query calls
         */
        public $group = '';

        /**
         * @var bool Stores if database has been found to reduce query calls
         */
        protected $exists = false;

        /** Methods ***************************************************************/

        /**
         * Hook into queries, admin screens, and more!
         *
         * @since 1.0.0
         */
        public function __construct( $name, $args ) {

            $this->name = $name;

            $this->primary_key = ( isset( $args['primary_key'] ) ) ? $args['primary_key'] : '';

            $this->version = ( isset( $args['version'] ) ) ? $args['version'] : 1;

            $this->global = ( isset( $args['global'] ) && $args['global'] === true ) ? true : false;

            $this->schema = ( isset( $args['schema'] ) ) ? new CT_DataBase_Schema( $args['schema'] ) : '';

            $this->group = ( isset( $args['group'] ) ) ? $args['group'] : strtok( $this->name, '_');

            // If not primary key given, then look at out schema
            if( $this->schema && ! $this->primary_key ) {
                foreach( $this->schema->fields as $field_id => $field_args ) {
                    if( $field_args['primary_key'] === true ) {
                        $this->primary_key = $field_id;
                        break;
                    }
                }
            }

            $this->engine = ( isset( $args['engine'] ) ) ? $args['engine'] : 'InnoDB';

            // Bail if no database object or table name
            if ( empty( $GLOBALS['wpdb'] ) || empty( $this->name ) ) {
                return;
            }

            // Setup the database
            $this->set_db();

            // Get the version of he table currently in the database
            $this->get_db_version();

            // Add the table to the object
            $this->set_wpdb_tables();

            // Setup the database schema
            $this->set_schema();

            // Add hooks to WordPress actions
            $this->add_hooks();
        }

        /** Abstract **************************************************************/

        /**
         * Setup this database table
         *
         * @since 1.0.0
         */
        protected function set_schema() {

        }

        /**
         * Upgrade this database table
         *
         * @since 1.0.0
         */
        protected function upgrade() {

            $schema_updater = new CT_DataBase_Schema_Updater( $this );

            $schema_updater->run();

        }

        /** Public ****************************************************************/

        /**
         * Update table version & references.
         *
         * Hooked to the "switch_blog" action.
         *
         * @since 1.0.0
         *
         * @param int $site_id
         */
        public function switch_blog( $site_id = 0 ) {

            // Update DB version based on the current site
            if ( false === $this->global ) {
                $this->db_version = get_blog_option( $site_id, $this->db_version_key, false );
            }

            // Update table references based on th current site
            $this->set_wpdb_tables();
        }

        /**
         * Maybe upgrade the database table. Handles creation & schema changes.
         *
         * Hooked to the "admin_init" action.
         *
         * @since 1.0.0
         */
        public function maybe_upgrade() {

            // Bail if no upgrade needed
            if ( version_compare( (int) $this->db_version, (int) $this->version, '>=' ) && $this->exists() ) {
                return;
            }

            // Include file with dbDelta() for create/upgrade usages
            if ( ! function_exists( 'dbDelta' ) ) {
                require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            }

            // Bail if global and upgrading global tables is not allowed
            if ( ( true === $this->global ) && ! wp_should_upgrade_global_tables() ) {
                return;
            }

            // Create or upgrade?
            $this->exists()
                ? $this->upgrade()
                : $this->create();

            // Set the database version
            if ( $this->exists() ) {
                $this->set_db_version();
            }
        }

        public function get( $id ) {

            return $this->db->get_row( $this->db->prepare( "SELECT * FROM {$this->table_name} WHERE {$this->primary_key} = %s", $id ) );

        }

        public function query( $args = array(), $output = OBJECT ) {

            return $this->db->get_results( "SELECT * FROM {$this->table_name}" );

        }

        public function insert( $data ) {

            if( $this->db->insert( $this->table_name, $data ) ) {
                return $this->db->insert_id;
            }

            return false;

        }

        public function update( $data, $where ) {

            $table_data = array();
            $schema_fields = array_keys( $this->schema->fields );

            // Filter extra data to prevent insert data outside table schema
            foreach( $data as $field => $value ) {
                if( ! in_array( $field, $schema_fields ) ) {
                    continue;
                }

                $table_data[$field] = $value;
            }

            return $this->db->update( $this->table_name, $table_data, $where );

        }

        public function delete( $value ) {

            return $this->db->query( $this->db->prepare( "DELETE FROM {$this->table_name} WHERE {$this->primary_key} = %s", $value ) );

        }

        /** Private ***************************************************************/

        /**
         * Setup the necessary WPDB variables
         *
         * @since 1.0.0
         */
        private function set_db() {

            // Setup database
            $this->db   = $GLOBALS['wpdb'];
            $this->name = sanitize_key( $this->name );

            // Maybe create database key
            if ( empty( $this->db_version_key ) ) {
                $this->db_version_key = "wpdb_{$this->name}_version";
            }
        }

        /**
         * Modify the database object and add the table to it
         *
         * This is necessary to do directly because WordPress does have a mechanism
         * for manipulating them safely. It's pretty fragile, but oh well.
         *
         * @since 1.0.0
         */
        private function set_wpdb_tables() {

            // Global
            if ( true === $this->global ) {
                $prefix                       = $this->db->get_blog_prefix( 0 );
                $this->db->{$this->name}      = "{$prefix}{$this->name}";
                $this->db->ms_global_tables[] = $this->name;

                // Site
            } else {
                $prefix                  = $this->db->get_blog_prefix( null );
                $this->db->{$this->name} = "{$prefix}{$this->name}";
                $this->db->tables[]      = $this->name;
            }

            // Set the table name locally
            $this->table_name = $this->db->{$this->name};

            // Charset
            if ( ! empty( $this->db->charset ) ) {
                $this->charset_collation = "DEFAULT CHARACTER SET {$this->db->charset}";
            }

            // Collation
            if ( ! empty( $this->db->collate ) ) {
                $this->charset_collation .= " COLLATE {$this->db->collate}";
            }
        }

        /**
         * Set the database version to the table version.
         *
         * Saves global table version to "wp_sitemeta" to the main network
         *
         * @since 1.0.0
         */
        private function set_db_version() {

            // Set the class version
            $this->db_version = $this->version;

            // Update the DB version
            ( true === $this->global )
                ? update_network_option( null, $this->db_version_key, $this->version )
                :         update_option(       $this->db_version_key, $this->version );
        }

        /**
         * Get the table version from the database.
         *
         * Gets global table version from "wp_sitemeta" to the main network
         *
         * @since 1.0.0
         */
        private function get_db_version() {
            $this->db_version = ( true === $this->global )
                ? get_network_option( null, $this->db_version_key, false )
                :         get_option(       $this->db_version_key, false );
        }

        /**
         * Add class hooks to WordPress actions
         *
         * @since 1.0.0
         */
        private function add_hooks() {

            // Activation hook
            register_activation_hook( __FILE__, array( $this, 'maybe_upgrade' ) );

            // Add table to the global database object
            add_action( 'switch_blog', array( $this, 'switch_blog'   ) );
            add_action( 'admin_init',  array( $this, 'maybe_upgrade' ) );
        }

        /**
         * Create the table
         *
         * @since 1.0.0
         */
        private function create() {

            // Run CREATE TABLE query
            $created = dbDelta( "CREATE TABLE `{$this->table_name}` ( {$this->schema} ) ENGINE={$this->engine} {$this->charset_collation};" );

            // Was anything created?
            $this->exists = ! empty( $created );

            // Add the table to the group when created
            if( $this->exists && $this->group !== '' ) {
                ct_add_table_to_group( $this->group, $this->table_name );
            }

            return ! empty( $created );
        }

        /**
         * Check if table already exists
         *
         * @since 1.0.0
         *
         * @return bool
         */
        public function exists() {

            if( $this->exists === true ) {
                return $this->exists;
            }

            if( $this->group === '' ) {
                // Table not in group
                $table_exist = $this->db->get_var( $this->db->prepare(
                    "SHOW TABLES LIKE %s",
                    $this->db->esc_like( $this->table_name )
                ) );

                $this->exists = ! empty( $table_exist );
            } else {
                // Table in group
                $tables = ct_get_tables_in_group( $this->group );

                $this->exists = in_array( $this->table_name, $tables );
            }

            return $this->exists;

        }

    }
endif;