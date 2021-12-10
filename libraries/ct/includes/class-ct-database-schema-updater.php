<?php
/**
 * Database Table Schema Updater class
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CT_DataBase_Schema_Updater' ) ) :

    class CT_DataBase_Schema_Updater {

        /**
         * @var CT_DataBase Database object
         */
        public $ct_db;

        /**
         * @var CT_DataBase_Schema Database Schema object
         */
        public $schema;

        /**
         * CT_DataBase_Schema_Updater constructor.
         *
         * @param CT_DataBase $ct_db
         */
        public function __construct( $ct_db ) {

            $this->ct_db = $ct_db;
            $this->schema = $ct_db->schema;

        }

        /**
         * Run the database schema update
         *
         * @return bool
         */
        public function run() {

            if( $this->schema ) {

                $alters = array();

                // Get schema fields and current table definition to being compared
                $schema_fields = $this->schema->fields;
                $current_schema_fields = array();

                // Get a description of current schema
                $schema_description = $this->ct_db->db->get_results( "DESCRIBE {$this->ct_db->table_name}" );

                // Check stored schema with configured fields to check field deletions and build a custom array to be used after
                foreach( $schema_description as $field ) {

                    $current_schema_fields[$field->Field] = $this->object_field_to_array( $field );

                    if( ! isset( $schema_fields[$field->Field] ) ) {
                        // A field to be removed
                        $alters[] = array(
                            'action' => 'DROP',
                            'column' => $field->Field
                        );
                    }

                }

                // Check configured fields with stored fields to check field creations
                foreach( $schema_fields as $field_id => $field_args ) {

                    if( ! isset( $current_schema_fields[$field_id] ) ) {
                        // A field to be added
                        $alters[] = array(
                            'action' => 'ADD',
                            'column' => $field_id
                        );

                    } else {
                        // Check changes in field definition

                        // Check if key definition has changed
                        if( $field_args['key'] !== $current_schema_fields[$field_id]['key'] ) {
                            $alters[] = array(
                                // Based the action on current key, if is true then ADD, if is false then DROP
                                'action' => ( $field_args['key'] ? 'ADD INDEX' : 'DROP INDEX' ),
                                'column' => $field_id
                            );
                        }

                        // TODO: Check the rest of available field args to determine was changed!!!
                    }

                }

                // Queries to be executed at end of checks
                $queries = array();

                foreach( $alters as $alter ) {

                    $column = $alter['column'];

                    switch( $alter['action'] ) {
                        case 'ADD':
                            $queries[] = "ALTER TABLE `{$this->ct_db->table_name}` ADD " . $this->schema->field_array_to_schema( $column, $schema_fields[$column] ) . "; ";
                            break;
                        case 'ADD INDEX':

                            /*
                             * Indexes have a maximum size of 767 bytes. WordPress 4.2 was moved to utf8mb4, which uses 4 bytes per character.
                             * This means that an index which used to have room for floor(767/3) = 255 characters, now only has room for floor(767/4) = 191 characters.
                             */
                            $max_index_length = 191;

                            if( $schema_fields[$column]['length'] > $max_index_length || $schema_fields[$column]['type'] === 'text' ) {
                                $add_index_query = '`' . $column . '`(`' . $column . '`(' . $max_index_length . '))';
                            } else {
                                $add_index_query = '`' . $column . '`(' . $column . '`)';
                            }

                            // Prevent errors if index already exists
                            drop_index( $this->ct_db->table_name, $column );

                            // For indexes query should be executed directly
                            $this->ct_db->db->query( "ALTER TABLE `{$this->ct_db->table_name}` ADD INDEX {$add_index_query}" );
                            break;
                        case 'MODIFY':
                            $queries[] = "ALTER TABLE `{$this->ct_db->table_name}` MODIFY " . $this->schema->field_array_to_schema( $column, $schema_fields[$column] ) . "; ";
                            break;
                        case 'DROP':
                            $queries[] = "ALTER TABLE `{$this->ct_db->table_name}` DROP COLUMN `{$column}`; ";

                            // Better use a built-in function here?
                            //maybe_drop_column( $this->ct_db->table_name, $column, "ALTER TABLE `{$this->ct_db->table_name}` DROP COLUMN {$column}" );
                        break;
                        case 'DROP INDEX':
                            // For indexes query should be executed directly
                            //$this->ct_db->db->query( "ALTER TABLE `{$this->ct_db->table_name}` DROP INDEX {$column}" );

                            // Use a built-in function for safe drop
                            drop_index( $this->ct_db->table_name, $column );
                            break;

                    }
                }

                if( ! empty( $queries ) ) {

                    // Execute the each SQL query
                    foreach( $queries as $sql ) {
                        $updated = $this->ct_db->db->query( $sql );
                    }

                    // Was anything updated?
                    return ! empty( $updated );
                }

                return true;

            }

        }

        /**
         * Object field returned by the DESCRIBE sentence
         *
         * @param stdClass $field stdClass object with next keys:
         * - string         Field      Field name
         * - string         Type       Field type ("type(length) signed|unsigned")
         * - string         Null       Nullable definition ("YES"|"NO")
         * - string         Key        Key. "PRI" for primary, "MUL" for key definition ("PRI"|"MUL")
         * - string|NULL    Default    Default definition. A NULL object if not defined. ("Default value"|NULL)
         * - string         Extra      Extra definitions, "auto_increment" for example
         *
         * @return array
         */
        public function object_field_to_array( $field ) {

            $field_args = array(
                'type'              => '',
                'length'            => 0,
                'decimals'          => 0,                                   // numeric fields
                'format'            => '',                                  // time fields
                'options'           => array(),                             // ENUM and SET types
                'nullable'          => (bool) ( $field->Null === 'YES' ),
                'unsigned'          => null,                                // numeric field
                'zerofill'          => null,                                // numeric field
                'binary'            => null,                                // text fields
                'charset'           => false,                               // text fields
                'collate'           => false,                               // text fields
                'default'           => false,
                'auto_increment'    => false,
                'unique'            => false,
                'primary_key'       => (bool) ( $field->Key === 'PRI' ),
                'key'               => (bool) ( $field->Key === 'MUL' ),
            );

            // Determine the field type
            if( strpos( $field->Type, '(' ) !== false ) {
                // Check for "type(length)" or "type(length) signed|unsigned"

                $type_parts = explode( '(', $field->Type );

                $field_args['type'] = $type_parts[0];

            } else if( strpos( $field->Type, ' ' ) !== false ) {
                // Check for "type signed|unsigned"
                $type_parts = explode( ' ', $field->Type );

                $field_args['type'] = $type_parts[0];
            }

            $field_args['type'] = $field->Type;

            if( strpos( $field->Type, '(' ) !== false ) {
                // Check for "type(length)" or "type(length) signed|unsigned"

                $type_parts = explode( '(', $field->Type );
                $type_part = $type_parts[1];

                $type_definition_parts = explode( ')', $type_part );
                $type_definition = $type_definition_parts[0];

                if( ! empty( $type_definition ) ) {

                    // Determine type definition args
                    switch( strtoupper( $field_args['type'] ) ) {
                        case 'ENUM':
                        case 'SET':
                            $field_args['options'] = explode( ',', $type_definition );
                            break;
                        case 'REAL':
                        case 'DOUBLE':
                        case 'FLOAT':
                        case 'DECIMAL':
                        case 'NUMERIC':
                            if( strpos( $type_definition, ',' ) !== false ) {
                                $decimals = explode( ',', $type_definition );

                                $field_args['length'] = $decimals[0];
                                $field_args['decimals'] = $decimals[1];
                            } else if( absint( $type_definition ) !== 0 ) {
                                $field_args['length'] = $type_definition;
                            }
                            break;
                        case 'TIME':
                        case 'TIMESTAMP':
                        case 'DATETIME':
                            $field_args['format'] = $type_definition;
                            break;
                        default:
                            if( absint( $type_definition ) !== 0 ) {
                                $field_args['length'] = $type_definition;
                            }
                            break;
                    }

                }

            }

            // Check for "type signed|unsigned zerofill ..." or "type(length) signed|unsigned zerofill ..."
            $type_definition_parts = explode( ' ', $field->Type );

            // Loop each field definition part to check extra parameters
            foreach( $type_definition_parts as $type_definition_part ) {

                if( $type_definition_part === 'unsigned' ) {
                    $field_args['unsigned'] = true;
                }

                if( $type_definition_part === 'signed' ) {
                    $field_args['unsigned'] = false;
                }

                if( $type_definition_part === 'zerofill' ) {
                    $field_args['zerofill'] = true;
                }

                if( $type_definition_part === 'binary' ) {
                    $field_args['binary'] = true;
                }

            }

            return $field_args;

        }

    }

endif;