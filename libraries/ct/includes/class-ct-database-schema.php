<?php
/**
 * Database Table Schema class
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CT_DataBase_Schema' ) ) :

    class CT_DataBase_Schema {

        /**
         * @var array Fields schema definition
         */
        public $fields = array();

        /**
         * @var array Keys schema definition
         */
        public $keys = array();

        public $primary_key = '';

        public function __construct( $schema ) {

            $this->keys = array();

            if( gettype( $schema ) === 'string' ) {
                $schema = $this->string_schema_to_array( $schema );
            }

            if( is_array( $schema ) ) {

                foreach( $schema as $field_id => $field_args ) {
                    $field_args = wp_parse_args( $field_args, array(
                        'type'              => '',
                        'length'            => 0,
                        'decimals'          => 0,           // numeric fields
                        'format'            => '',          // time fields
                        'options'           => array(),     // ENUM and SET types
                        'nullable'          => false,
                        'unsigned'          => null,        // numeric field
                        'zerofill'          => null,        // numeric field
                        'binary'            => null,        // text fields
                        'charset'           => false,       // text fields
                        'collate'           => false,       // text fields
                        'default'           => false,
                        'auto_increment'    => false,
                        'unique'            => false,
                        'primary_key'       => false,
                        'key'               => false,
                    ) );

                    $this->fields[$field_id] = $field_args;
                }

            }

        }

        public function __toString() {

            $fields_def = array();

            foreach( $this->fields as $field_id => $field_args ) {
                // Turn field array to schema
                $fields_def[] = $this->field_array_to_schema( $field_id, $field_args );
            }

            // Setup PRIMARY KEY definition
            $sql = implode( ', ', $fields_def ) . ', '
                . 'PRIMARY KEY  (`' . $this->primary_key . '`)'; // Add two spaces to avoid issues

            // Setup KEY definitions
            if( ! empty( $this->keys ) ) {
                $sql .= ', ' . implode( ', ', $this->keys );
            }

            return $sql;
        }

        /**
         * Convert a field definition into a schema string
         *
         * @param string $field_id
         * @param array $field_args
         *
         * @return string               A schema string like: field type(length,decimals) UNSIGNED NOT NULL
         */
        public function field_array_to_schema( $field_id, $field_args ) {

            $schema = '';

            // Field name
            $schema .= '`' . $field_id . '` ';

            // Type definition
            $schema .= $field_args['type'];

            // Type definition args
            switch( strtoupper( $field_args['type'] ) ) {
                case 'ENUM':
                case 'SET':
                    if( is_array( $field_args['options'] ) ) {
                        $schema .= '(' . implode( ',', $field_args['options'] ) . ')';
                    } else {
                        $schema .= '(' . $field_args['options'] . ')';
                    }
                    break;
                case 'REAL':
                case 'DOUBLE':
                case 'FLOAT':
                case 'DECIMAL':
                case 'NUMERIC':
                    if( $field_args['length'] !== 0 ) {
                        $schema .= '(' . $field_args['length'] . ',' . $field_args['decimals'] . ')';
                    }
                    break;
                case 'TIME':
                case 'TIMESTAMP':
                case 'DATETIME':
                    if( $field_args['format'] !== '' ) {
                        $schema .= '(' . $field_args['format'] . ')';
                    }
                    break;
                default:
                    if( $field_args['length'] !== 0 ) {
                        $schema .= '(' . $field_args['length'] . ')';
                    }
                    break;
            }

            $schema .= ' ';

            // Type specific definitions
            switch( strtoupper( $field_args['type'] ) ) {
                case 'TINYINT':
                case 'SMALLINT':
                case 'MEDIUMINT':
                case 'INT':
                case 'INTEGER':
                case 'BIGINT':
                case 'REAL':
                case 'DOUBLE':
                case 'FLOAT':
                case 'DECIMAL':
                case 'NUMERIC':
                    // UNSIGNED definition
                    if( $field_args['unsigned'] !== null ) {
                        if( $field_args['unsigned'] ) {
                            $schema .= 'UNSIGNED ';
                        } else {
                            $schema .= 'SIGNED ';
                        }
                    }

                    // ZEROFILL definition
                    if( $field_args['zerofill'] !== null && $field_args['zerofill'] ) {
                        $schema .= 'ZEROFILL ';
                    }
                    break;
                case 'CHAR':
                case 'VARCHAR':
                case 'TINYTEXT':
                case 'TEXT':
                case 'MEDIUMTEXT':
                case 'LONGTEXT':
                case 'ENUM':
                case 'SET':
                    // BINARY definition
                    if( $field_args['binary'] !== null && $field_args['binary']) {
                        $schema .= 'BINARY ';
                    }

                    // CHARACTER SET definition
                    if( $field_args['charset'] !== false ) {
                        $schema .= 'CHARACTER SET ' . $field_args['charset'] . ' ';
                    }

                    // COLLATE definition
                    if( $field_args['collate'] !== false ) {
                        $schema .= 'COLLATE ' . $field_args['collate'] . ' ';
                    }
                    break;
            }


            // NULL definition
            if( $field_args['nullable'] ) {
                $schema .= 'NULL ';
            } else {
                $schema .= 'NOT NULL ';
            }

            // DEFAULT definition
            if( $field_args['default'] !== false ) {

                if( gettype( $field_args['default'] ) === 'string' ) {
                    $field_args['default'] = "'" . $field_args['default'] . "'";
                }

                if( $field_args['default'] === null ) {
                    $field_args['default'] = 'NULL';
                }

                $schema .= 'DEFAULT ' . $field_args['default'] . ' ';
            }

            // UNIQUE definition
            if( $field_args['unique'] ) {
                $schema .= 'UNIQUE ';
            }

            // AUTO_INCREMENT definition
            if( $field_args['auto_increment'] ) {
                $schema .= 'AUTO_INCREMENT ';
            }

            // PRIMARY KEY definition
            if( $field_args['primary_key'] ) {
                $this->primary_key = $field_id;
            }

            // KEY definition
            if( $field_args['key'] ) {

                /*
                 * Indexes have a maximum size of 767 bytes. WordPress 4.2 was moved to utf8mb4, which uses 4 bytes per character.
                 * This means that an index which used to have room for floor(767/3) = 255 characters, now only has room for floor(767/4) = 191 characters.
                 */
                $max_index_length = 191;
                $length = min( $field_args['length'], $max_index_length );

                // Ensure that length has a value
                if( $length === 0 ) {
                    $length = $max_index_length;
                }

                if( $this->is_numeric( $field_args['type'] ) || strtoupper( $field_args['type'] ) === 'DATETIME' ) {
                    $this->keys[] = 'KEY `' . $field_id . '`(`' . $field_id . '`)';
                } else {
                    $this->keys[] = 'KEY `' . $field_id . '`(`' . $field_id . '`(' . $length . '))';
                }
            }

            return $schema;

        }

        /**
         * Convert a schema string into an array of fields definitions
         *
         * @param string $schema
         *
         * @return array
         */
        public function string_schema_to_array( $schema ) {

            $schema_array = explode( ',', trim( $schema ) );
            $new_schema = array();

            foreach( $schema_array as $field_def ) {

                $field_id = '';
                $field_args = array();

                $field_def_parts = explode( ' ', trim( $field_def ) );

                foreach( $field_def_parts as $index => $field_def_part ) {


                    if( $index === 0 ) {

                        // Field id at index 0
                        if( $field_def_part !== 'PRIMARY' && $field_def_part !== 'KEY' ) {
                            $field_id = $field_def_part;
                            continue;
                        }

                        // PRIMARY KEY at index 0
                        if( $field_def_part === 'PRIMARY' ) {
                            if( isset( $field_def_parts[$index+1] ) && strtoupper( $field_def_parts[$index+1] ) === 'KEY' && isset( $field_def_parts[$index+2] ) ) {
                                $primary_key_field = str_replace( array( '(', ')' ), '', $field_def_parts[$index+2] );

                                if( isset( $this->fields[$primary_key_field] ) ) {
                                    $this->fields[$primary_key_field]['primary_key'] = true;
                                    continue;
                                }
                            }
                        }
                    }

                    // NOT NULL definition
                    if( strtoupper( $field_def_part ) === 'NOT' ) {
                        if( isset( $field_def_parts[$index+1] ) && strtoupper( $field_def_parts[$index+1] ) === 'NULL' ) {
                            $field_args['nullable'] = false;
                            continue;
                        }
                    }

                    // NULL definition
                    if( strtoupper( $field_def_part ) === 'NULL' ) {
                        if( isset( $field_def_parts[$index-1] ) && strtoupper( $field_def_parts[$index-1] ) !== 'NOT' ) {
                            $field_args['nullable'] = true;
                            continue;
                        }
                    }

                    // UNSIGNED definition
                    if( strtoupper( $field_def_part ) === 'UNSIGNED' ) {
                        $field_args['unsigned'] = true;
                        continue;
                    }

                    // SIGNED definition
                    if( strtoupper( $field_def_part ) === 'SIGNED' ) {
                        $field_args['unsigned'] = false;
                        continue;
                    }

                    // ZEROFILL definition
                    if( strtoupper( $field_def_part ) === 'ZEROFILL' ) {
                        $field_args['zerofill'] = true;
                        continue;
                    }

                    // BINARY definition
                    if( strtoupper( $field_def_part ) === 'BINARY' ) {
                        $field_args['binary'] = true;
                        continue;
                    }

                    // CHARACTER SET definition
                    if( strtoupper( $field_def_part ) === 'CHARACTER' ) {
                        if( isset( $field_def_parts[$index+1] ) && strtoupper( $field_def_parts[$index+1] ) === 'SET' ) {
                            $field_args['charset'] = $field_def_parts[$index+2];
                            continue;
                        }
                    }

                    // COLLATE definition
                    if( strtoupper( $field_def_part ) === 'COLLATE' ) {
                        if( isset( $field_def_parts[$index+1] ) ) {
                            $field_args['collate'] = $field_def_parts[$index+1];
                            continue;
                        }
                    }

                    // DEFAULT definition
                    if( strtoupper( $field_def_part ) === 'DEFAULT' ) {
                        if( isset( $field_def_parts[$index+1] ) ) {
                            $field_args['default'] = $field_def_parts[$index+1];
                            continue;
                        }
                    }

                    // UNIQUE definition
                    if( strtoupper( $field_def_part ) === 'UNIQUE' ) {
                        $field_args['unique'] = true;
                        continue;
                    }

                    // AUTO_INCREMENT definition
                    if( strtoupper( $field_def_part ) === 'AUTO_INCREMENT' ) {
                        $field_args['auto_increment'] = true;
                        continue;
                    }

                    // PRIMARY KEY definition
                    if( strtoupper( $field_def_part ) === 'PRIMARY' ) {
                        if( isset( $field_def_parts[$index+1] ) && strtoupper( $field_def_parts[$index+1] ) === 'KEY' ) {
                            $field_args['primary_key'] = true;
                            continue;
                        }
                    }

                    $type_parts = explode( '(', $field_def_part );

                    // Possible field type
                    if( in_array( strtoupper( $field_def_part ), $this->allowed_field_types() ) ) {
                        $field_args['type'] = strtoupper( $field_def_part );
                        continue;
                    } else if( isset( $type_parts[0] ) && in_array( strtoupper( $type_parts[0] ), $this->allowed_field_types() ) ) {
                        $field_args['type'] = strtoupper( $type_parts[0] );

                        if( isset( $type_parts[1] ) ) {
                            $type_def = explode( ',',  str_replace(')', '', $type_parts[1] ) );

                            switch( $field_args['type'] ) {
                                case 'ENUM':
                                case 'SET':
                                    $field_args['options'] = $type_def;
                                    break;
                                case 'REAL':
                                case 'DOUBLE':
                                case 'FLOAT':
                                case 'DECIMAL':
                                case 'NUMERIC':
                                    $field_args['length'] = $type_def[0];

                                    if( isset( $type_def[1] ) ) {
                                        $field_args['decimals'] = $type_def[1];
                                    }
                                    break;
                                case 'TIME':
                                case 'TIMESTAMP':
                                case 'DATETIME':
                                    $field_args['format'] = $type_def[0];
                                    break;
                                default:
                                    $field_args['length'] = $type_def[0];
                                    break;
                            }
                        }
                    }
                }

                if( ! empty( $field_id ) && ! empty( $field_args ) ) {
                    $new_schema[$field_id] = $field_args;
                }
            }

            return $new_schema;

        }

        /**
         * Get the list of allowed types
         *
         * @return array
         */
        public function allowed_field_types() {
            return array(
                'BIT',
                'TINYINT',
                'SMALLINT',
                'MEDIUMINT',
                'INT',
                'INTEGER',
                'BIGINT',
                'REAL',
                'DOUBLE',
                'FLOAT',
                'DECIMAL',
                'NUMERIC',
                'DATE',
                'TIME',
                'TIMESTAMP',
                'DATETIME',
                'YEAR',
                'CHAR',
                'VARCHAR',
                'BINARY',
                'VARBINARY',
                'TINYBLOB',
                'BLOB',
                'MEDIUMBLOB',
                'LONGBLOB',
                'TINYTEXT',
                'TEXT',
                'JSON'
            );
        }

        /**
         * Check if given type is numeric
         *
         * @param string $type
         *
         * @return bool
         */
        public function is_numeric( $type ) {

            return in_array( strtoupper( $type ), array(
                'TINYINT',
                'SMALLINT',
                'MEDIUMINT',
                'INT',
                'INTEGER',
                'BIGINT',
                'REAL',
                'DOUBLE',
                'FLOAT',
                'DECIMAL',
                'NUMERIC',
            ) );

        }

    }

endif;