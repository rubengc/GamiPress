<?php
/**
 * @package      CMB2\Field_EDD_License
 * @author       GamiPress
 * @copyright    Copyright (c) GamiPress
 *
 * Plugin Name: CMB2 Field Type: EDD License
 * Plugin URI: https://github.com/rubengc/cmb2-field-edd-license
 * GitHub Plugin URI: https://github.com/rubengc/cmb2-field-edd-license
 * Description: CMB2 field type to store and check EDD Software Licensing licenses.
 * Version: 1.0.3
 * Author: GamiPress
 * Author URI: https://gamipress.com/
 * License: GPLv2+
 */

global $cmb2_field_edd_license;

// TODO: Add support for themes

if( ! class_exists( 'CMB2_Field_EDD_License' ) ) {

    /**
     * Class CMB2_Field_EDD_License
     */
    class CMB2_Field_EDD_License {

        /**
         * Current version number
         */
        const VERSION = '1.0.2';

        /**
         * Initialize the plugin by hooking into CMB2
         */
        public function __construct() {

            add_filter( 'cmb2_admin_init', array( $this, 'includes' ) );

            add_filter( 'cmb2_after_init', array( $this, 'license_deactivation_handler' ) );

            add_filter( 'cmb2_after_init', array( $this, 'check_updates' ), 9999 );

            add_filter( 'cmb2_render_class_edd_license', array( $this, 'render_class' ), 10, 2 );

            add_action( 'admin_enqueue_scripts', array( $this, 'setup_admin_scripts' ) );

            add_action( 'cmb2_save_field',  array( $this, 'save_field' ), 10, 4 );

        }

        public function includes() {
            // Plugin updater
            if ( ! class_exists( 'CMB_EDD_SL_Plugin_Updater' ) ) {
                require_once __DIR__ . '/includes/CMB_EDD_SL_Plugin_Updater.php';
            }

            // Field type
            require_once __DIR__ . '/includes/CMB_Type_EDD_License.php';
        }

        public function render_class( $render_class_name, $field_type_object ) {
            return 'CMB_Type_EDD_License';
        }

        /**
         * Enqueue scripts and styles
         */
        public function setup_admin_scripts() {

            // Script is registered instead of enqueued because it is enqueued on demand
            wp_register_script( 'cmb-edd-license-js', plugins_url( 'assets/js/edd-license.js', __FILE__ ), array( 'jquery' ), self::VERSION, true );

            // CSS needs to be enqueued
            // Commented since there isn't any CSS rule applied yet
            //wp_enqueue_style( 'cmb-edd-license-css', plugins_url( 'assets/css/edd-license.css', __FILE__ ), array(), self::VERSION );

        }

        public function license_deactivation_handler() {

            if( ! isset( $_REQUEST['edd_license_deactivate_license'] ) ) {
                return;
            }

            // Ajax check
            $is_ajax = ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' );

            // Add nonce for security and authentication.
            $nonce_name   = isset( $_REQUEST['cmb2_edd_license_deactivation_nonce'] ) ? $_REQUEST['cmb2_edd_license_deactivation_nonce'] : '';
            $nonce_action = 'cmb2_edd_license_deactivation_nonce_action';

            // Check if nonce is set.
            if ( ! isset( $nonce_name ) ) {

                if( $is_ajax ) {
                    wp_send_json_error( __( 'Security verification not sent.', 'cmb2-edd-license' ) );
                } else {
                    return;
                }

            }

            // Check if nonce is valid.
            if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {

                if( $is_ajax ) {
                    wp_send_json_error( __( 'Security verification failed.', 'cmb2-edd-license' ) );
                } else {
                    return;
                }

            }

            $meta_box_id = isset( $_REQUEST['edd_license_deactivate_cmb_id'] ) ? $_REQUEST['edd_license_deactivate_cmb_id'] : '';
            $field_id = isset( $_REQUEST['edd_license_deactivate_field_id'] ) ? $_REQUEST['edd_license_deactivate_field_id'] : '';
            $object_id = isset( $_REQUEST['edd_license_deactivate_object_id'] ) ? $_REQUEST['edd_license_deactivate_object_id'] : '';
            $object_type = isset( $_REQUEST['edd_license_deactivate_object_type'] ) ? $_REQUEST['edd_license_deactivate_object_type'] : '';

            // Check if field id is set.
            if ( empty( $meta_box_id ) || empty( $field_id ) || empty( $object_id ) || empty( $object_type ) ) {

                if( $is_ajax ) {
                    wp_send_json_error( __( 'Some fields are missing.', 'cmb2-edd-license' ) );
                } else {
                    return;
                }

            }

            $field = cmb2_get_field( $meta_box_id, $field_id, $object_id, $object_type );

            if( $field->args( 'type' ) !== 'edd_license' ) {

                if( $is_ajax ) {
                    wp_send_json_error( __( 'Can not setup license object.', 'cmb2-edd-license' ) );
                } else {
                    return;
                }

            }

            $license = cmb2_edd_license_data( $field->escaped_value() );
            $license_status = ( $license !== false ) ? $license->license : false;

            if( $license_status === 'valid' ) {

                $args = wp_parse_args( $field->_data( 'args' ), array(
                    'server'          => '',
                    'license'         => $field->escaped_value(),
                    'item_id'         => '',
                    'item_name'       => '',
                    'file'            => '',
                    'version'         => '',
                    'author'          => '',
                    'wp_override'     => false,
                ) );

                $api_response = $this->api_request( $args['server'], $args['license'], $args, 'deactivate_license' );

                if( $api_response === true ) {

                    if( $is_ajax ) {
                        wp_send_json_success( __( 'License deactivated successfully.', 'cmb2-edd-license' ) );
                    } else {
                        return;
                    }

                } else {

                    if( $is_ajax ) {
                        wp_send_json_error( $api_response );
                    } else {
                        return;
                    }

                }

            } else {

                if( $is_ajax ) {
                    wp_send_json_error( __( 'License has not been activated yet.', 'cmb2-edd-license' ) );
                }

            }

            // TODO: Clear field value on success?
        }

        public function check_updates() {

            if( is_admin() ) {

                // Loop all registered boxes
                foreach( CMB2_Boxes::get_all() as $cmb ) {

                    // Loop all fields
                    foreach( $cmb->meta_box['fields'] as $field ) {
                        if( $field['type'] === 'edd_license' ) {

                            $args = $field;

                            if( $cmb->is_options_page_mb() ) {
                                $option_key = $cmb->object_id;

                                // TODO: On delete actions, $option_key is an array of deleted items
                                if( is_array( $option_key ) ) {
                                    return;
                                }

                                if( ! $option_key && isset( $cmb->meta_box['show_on'] ) && isset( $cmb->meta_box['show_on']['value'] ) ) {
                                    if( is_array( $cmb->meta_box['show_on']['value'] ) ) {
                                        $option_key = $cmb->meta_box['show_on']['value'][0];
                                    } else {
                                        $option_key = $cmb->meta_box['show_on']['value'];
                                    }
                                }

                                $option_key = apply_filters( 'cmb2_edd_license_option_key', $option_key, $cmb );

                                $default = isset( $field['default'] ) ? $field['default'] : '';

                                $args['value'] = cmb2_get_option( $option_key, $field['id'], $default );
                            } else {
                                $args['value'] = cmb2_get_field_value( $cmb, $field['id'], $cmb->object_id, $cmb->mb_object_type() );
                            }

                            $this->check_item_updates( $args );
                        }
                    }
                }

            }

        }

        /**
         * After save field action.
         *
         * @param string            $field_id the current field id paramater.
         * @param bool              $updated  Whether the metadata update action occurred.
         * @param string            $action   Action performed. Could be "repeatable", "updated", or "removed".
         * @param CMB2_Field        $field    This field object
         */
        public function save_field( $field_id, $updated, $action, $field ) {

            if( $field->args( 'type' ) !== 'edd_license' ) {
                return;
            }

            if( $action === 'updated'  ) {

                $args = wp_parse_args( $field->_data( 'args' ), array(
                    'server'          => '',
                    'license'         => $field->escaped_value(),
                    'item_id'         => '',
                    'item_name'       => '',
                    'file'            => '',
                    'version'         => '',
                    'author'          => '',
                    'wp_override'     => false,
                ) );

                $this->api_request( $args['server'], $args['license'], $args, 'activate_license' );

            }

        }

        /**
         * API request to the server URL
         *
         * Taken from Titan Framework EDD License field
         *
         * @param string $server
         * @param string $license_key
         * @param array $args
         * @param string $action
         * @return bool|string
         */
        public function api_request( $server, $license_key, $args, $action = 'check_license' ) {

            // Retrieve license key
            $license_key = trim( esc_attr( $license_key ) );

            // Check if we have all the required parameters.
            if ( empty( $server ) ||  empty( $license_key ) ) {
                return false;
            }

            // Prepare the data to send with the API request.
            $api_params = array(
                'edd_action' => $action,
                'license'    => $license_key,
                'url'        => home_url(),
            );

            // Set the item ID or name. ID has the highest priority
            if ( isset( $args['item_id'] ) && ! empty( $args['item_id'] ) ) {
                $api_params['item_id'] = urlencode( $args['item_id'] );
            } elseif ( isset( $args['item_name'] ) ) {
                $api_params['item_name'] = urlencode( $args['item_name'] );
            }

            if ( ! isset( $api_params['item_id'] ) && ! isset( $api_params['item_name'] ) ) {
                return false;
            }

            // Call the API.
            $response = wp_remote_post( $server, array(
                'timeout'   => 15,
                'sslverify' => false,
                'body'      => $api_params
            ) );

            // Check for request error.
            if ( is_wp_error( $response ) ) {
                return false;
            }

            // Decode license data.
            $license_data = json_decode( wp_remote_retrieve_body( $response ) );

            // If the remote server didn't return a valid response we just return an error and don't set any transients so that activation will be tried again next time the option is saved
            if ( ! is_object( $license_data ) || empty( $license_data ) || ! isset( $license_data->license ) ) {
                return 'no_response';
            }

            // Transient data
            $key                    = substr( md5( $license_key ), 0, 10 );
            $data_lifetime          = apply_filters( 'cmb2_edd_license_data_lifetime', 48 * 60 * 60 );              // Default is set to two days
            $activation_lifetime    = apply_filters( 'cmb2_edd_license_activation_lifetime', 365 * 24 * 60 * 60 );  // Default is set to one year

            if ( $action == 'activate_license') {

                /**
                 * If the license is invalid we can set all transients right away.
                 * The user will need to modify its license anyways so there is no risk
                 * of preventing further activation attempts.
                 */
                if ( 'invalid' === $license_data->license ) {
                    set_transient( "cmb2_edd_license_data_$key", $license_data, $data_lifetime );
                    set_transient( "cmb2_edd_license_try_$key", true, $activation_lifetime );
                    return 'invalid';
                }

                /**
                 * Because sometimes EDD returns a "success" status even though the license hasn't been activated,
                 * we need to check the license status after activating it. Only then we can safely set the
                 * transients and avoid further activation attempts issues.
                 */
                $status = $this->api_request( $server, $license_key, $args );

                if ( in_array( $status, array( 'valid', 'inactive' ) ) ) {

                    /* We set the "try" transient only as the status will be set by the second instance of this method when we check the license status */
                    set_transient( "cmb2_edd_license_try_$key", true, $activation_lifetime );

                }
            } else if ( $action == 'deactivate_license') {
                delete_transient( "cmb2_edd_license_try_$key" );
                delete_transient( "cmb2_edd_license_data_$key" );

                return true;
            } else {

                // Set the data transient.
                set_transient( "cmb2_edd_license_data_$key", $license_data, $data_lifetime );

            }

            // Return the license status.
            return $license_data->license;
        }

        /**
         * Automatically adds an updater checker
         */
        public function check_item_updates( $args = array() ) {

            // Bail if not in admin area
            if( ! is_admin() ) {
                return false;
            }

            // Include required files
            if( ! function_exists( 'get_plugin_data' ) ) {
                include ABSPATH . '/wp-admin/includes/plugin.php';
            }

            $args = wp_parse_args( $args, array(
                'server'          => '',
                'item_id'         => '',
                'item_name'       => '',
                'file'            => '',
                'version'         => '',
                'author'          => '',
                'wp_override'     => false,
            ) );

            // Check if we have all the required parameters.
            if ( empty( $args['server'] ) || empty( $args['file'] ) ) {
                return false;
            }

            // Make sure the file actually exists.
            if ( ! file_exists( $args['file'] ) ) {
                return false;
            }

            // Item name
            $item_name = ! empty( $args['item_name'] ) ? sanitize_text_field( $args['item_name'] ) : false;
            $item_id   = ! empty( $args['item_id'] ) ? (int) $args['item_id'] : false;

            // Retrieve license key
            $license_key = trim( esc_attr( $args['value'] ) );

            // Prepare updater arguments
            $api_params = array(
                'license' => ( cmb2_edd_license_status( $license_key ) === 'valid' ? $license_key : '' ),
            );

            // Add license ID or name for identification
            if ( $item_id != false) {
                $api_params['item_id'] = $item_id;
            } elseif ( $item_name != false) {
                $api_params['item_name'] = $item_name;
            }

            $plugin              		= get_plugin_data( $args['file'], false );
            $api_params['version']     	= ! empty( $args['version'] ) ? sanitize_text_field( $args['version'] ) : $plugin['Version'];
            $api_params['author']      	= ! empty( $args['author'] ) ? sanitize_text_field( $args['author'] ) : $plugin['Author'];
            $api_params['wp_override'] 	= $args['wp_override'];

            // Update server URL
            $server = esc_url( $args['server'] );

            // Setup updater
            $cmb2_edd_updater = new CMB_EDD_SL_Plugin_Updater( $server, $args['file'], $api_params );

            return $cmb2_edd_updater;
        }

    }

    /**
     * Return the license data of a license key
     *
     * @param string $license_key   License key value (Just pass the field value)
     *
     * @return bool|stdClass        License data or false (if not license provided or not checked)
     */
    function cmb2_edd_license_data( $license_key ) {

        if( ! empty( $license_key ) ) {

            $key = substr( md5( $license_key ), 0, 10 );

            if( false !== ( $license_data = get_transient( "cmb2_edd_license_data_$key" ) ) ) {
                return $license_data;
            }

        }

        return false;
    }

    /**
     * Return the status of a license key
     *
     * @param string $license_key   License key value (Just pass the field value)
     * @return bool|string          Value of license status, could return "valid", "invalid" or false (if not license provided or not checked)
     */
    function cmb2_edd_license_status( $license_key ) {

        $license_data = cmb2_edd_license_data( $license_key );

        if( $license_data ) {
            return $license_data->license;
        }

        return false;
    }

    $cmb2_field_edd_license = new CMB2_Field_EDD_License();

}