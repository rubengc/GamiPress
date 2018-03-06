<?php
/**
 * Database Table Schema Updater class
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
        public $db;

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

            $this->db = $ct_db;
            $this->schema = $ct_db->schema;

        }

        public function run() {

            if( $this->schema ) {

                $alters = array();

                // Get schema fields and current table definition to being compared
                $schema_fields = $this->schema->fields;
                $current_schema_fields = array();

                // Get a description of current schema
                $schema_description = $this->db->db->get_results( "DESCRIBE {$this->db->table_name}" );

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

                        // TODO!!!
                    }

                }

                // SQL to be executed at end of checks
                $sql = '';

                foreach( $alters as $alter ) {

                    $column = $alter['column'];

                    switch( $alter['action'] ) {
                        case 'ADD':
                            $sql .= "ALTER TABLE {$this->db->table_name} ADD " . $this->schema->field_array_to_schema( $column, $schema_fields[$column] ) . "; ";
                            break;
                        case 'MODIFY':
                            $sql .= "ALTER TABLE {$this->db->table_name} MODIFY " . $this->schema->field_array_to_schema( $column, $schema_fields[$column] ) . "; ";
                            break;
                        case 'DROP':
                            $sql .= "ALTER TABLE {$this->db->table_name} DROP COLUMN {$column}; ";
                        break;

                    }
                }

                // Execute the SQL
                $updated = $this->db->db->query( $sql );

                // Was anything updated?
                return ! empty( $updated );

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

                $type_parts = explode( '(', $field->Type )[1];
                $type_definition = explode( ')', $type_parts )[0];

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