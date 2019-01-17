<?php
/**
 * Table meta class
 *
 * @author GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gamil.com>
 *
 * @since 1.0.0
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CT_Table_Meta' ) ) :

    class CT_Table_Meta {

        /**
         * Table key.
         *
         * @since 1.0.0
         * @access public
         * @var string $name
         */
        public $name;

        /**
         * Table Meta database.
         *
         * @since 1.0.0
         * @access public
         * @var CT_DataBase $db
         */
        public $db;

        /**
         * Table Meta table object.
         *
         * @since 1.0.0
         * @access public
         * @var CT_Table $table
         */
        public $table;

        /**
         * CT_Table_Meta constructor.
         * @param CT_Table $table
         */
        public function __construct( $table ) {

            $this->table = $table;
            $this->name = $this->table->name . '_meta';

            $this->db = new CT_DataBase( $this->name, array(
                'version' => 1,
                'global' => $this->table->db->global,
                'schema' => array(
                    'meta_id' => array(
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'nullable' => false,
                        'auto_increment' => true,
                        'primary_key' => true
                    ),
                    $this->table->db->primary_key => array(
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => 0,
                        'key' => true
                    ),
                    'meta_key' => array(
                        'type' => 'varchar',
                        'length' => 255,
                        'nullable' => true,
                        'default' => null,
                        'key' => true
                    ),
                    'meta_value' => array(
                        'type' => 'longtext',
                        'nullable' => true,
                        'default' => null
                    ),
                )
            ) );

        }

    }

endif;