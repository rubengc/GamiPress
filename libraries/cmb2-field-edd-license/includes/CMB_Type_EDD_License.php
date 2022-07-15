<?php
/**
 * CMB EDD License field type
 *
 * @since  1.0.0
 *
 * @package      CMB2\Type\EDD_License
 * @author       Tsunoa
 * @copyright    Copyright (c) Tsunoa
 */
class CMB_Type_EDD_License extends CMB2_Type_Base {

	/**
	 * The type of field
	 *
	 * @var string
	 */
	public $type = 'input';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param CMB2_Types $types
	 * @param array      $args
	 */
	public function __construct( $types, $args = array(), $type = '' ) {

		parent::__construct( $types, $args );

		$this->type = $type ? $type : $this->type;

	}

	/**
	 * Handles outputting an 'input' element
	 *
	 * @since  1.0.0
	 * @param  array  $args Override arguments
	 * @return string       Form input element
	 */
	public function render( $args = array() ) {

        global $cmb2_field_edd_license;

		$args = empty( $args ) ? $this->args : $args;

        $args = $this->parse_args( $this->type, array(
			// CMB2
			'name'            => $this->_name(),
			'id'              => $this->_id(),
			'value'           => $this->field->escaped_value(),
			'desc'            => $this->_desc( true ),
			'type'            => 'text',
			'class'           => 'regular-text',
		), $args );

		$field_args = $this->parse_args( $this->type, array(
			// License
			'server'                            => '',
			'item_id'                           => '',
			'item_name'                         => '',
			'file'                              => '',
			'version'                           => '',
			'author'                            => '',
			'wp_override'                       => false,
			// Extra settings
			'deactivate_button'                 => __( 'Deactivate License', 'cmb2-edd-license' ),      // string|false String to set the button text, false to remove it
			'clear_button'                      => __( 'Clear License', 'cmb2-edd-license' ),           // string|false String to set the button text, false to remove it
			'license_expiration'                => true,                                                // bool         True to enable license expiration notice, false to deactivate it
			'renew_license'                     => __( 'Renew your license key.', 'cmb2-edd-license' ), // string|false String to set the renew license text, false to remove it
			'renew_license_timestamp'           => ( DAY_IN_SECONDS * 30 ),                             // int          Minimum time to show the license renewal text, by default 30 days
			// Links, used for license errors as a shortcut to business website
			'renew_license_link' 		        => false,                                               // string|false Link where users can renew their licenses, false to remove it
			'license_management_link' 	        => false,                                               // string|false Link where users can manage their licenses, false to remove it
			'contact_link' 				        => false,                                               // string|false Link where users can contact with your team, false to remove it
            // Hide license settings
            'hide_license'                      => true,                                                // bool         True to hide the license (just if license is valid), with default settings license will be displayed as: **********1234
            'hide_license_character'            => '*',                                                 // string       Character to hide the license
            'hide_license_visible_characters'   => 4,                                                   // int          Number of visible license characters
		), $this->field->_data( 'args' ) );

		$this->field->add_js_dependencies( array(
			'cmb-edd-license-js'
		) );

		$license = rgc_cmb2_edd_license_data( $args['value'] );
		$license_status = ( $license !== false ) ? $license->license : false;

        // If user has input a license but there isn't any license object, then perform a new API request
        // This lines fixes an issue with not properly activated keys
        if( ! empty( $args['value'] ) && $license === false ) {

            $cmb2_field_edd_license->api_request( $field_args['server'], $args['value'], $field_args, 'activate_license' );

            $license = rgc_cmb2_edd_license_data( $args['value'] );
            $license_status = ( $license !== false ) ? $license->license : false;

        }

		if( $license_status !== false) {
			// Add the class license-{$license_status} (valid or invalid)
            $args['class'] .= ' license-' . $license_status;
		}

		// Error notice
		$error_notice = '';

		if( $license !== false && $license_status !== 'valid' ) {

			$error_notice = '<p class="license-error">' . $this->get_license_error( $license, $field_args ) . '</p>';

		}

		// Deactivation button
		$deactivation_button = '';

		if( $field_args['deactivate_button'] !== false && $license_status === 'valid' ) {
			$deactivation_button = '<p class="deactivate-license">' .
                    wp_nonce_field( 'cmb2_edd_license_deactivation_nonce_action', 'cmb2_edd_license_deactivation_nonce' ) .
                    '<input type="hidden" name="edd_license_deactivate_cmb_id" value="' . $this->field->cmb_id . '"/>' .
                    '<input type="hidden" name="edd_license_deactivate_field_id" value="' . $this->_id() . '"/>' .
                    '<input type="hidden" name="edd_license_deactivate_object_id" value="' . $this->field->object_id . '"/>' .
                    '<input type="hidden" name="edd_license_deactivate_object_type" value="' . $this->field->object_type . '"/>' .
                    '<button type="button" class="button deactivate-license-button" name="edd_license_deactivate_license" value="' . $args['value'] . '">' . $field_args['deactivate_button'] . '</button>' .
            '</p>';
		}

		// Clear button
        $clear_button = '';

        if( $field_args['clear_button'] !== false && $license_status !== 'valid' && ! empty( $args['value'] ) ) {
            $clear_button = '<p class="clear-license">' .
                wp_nonce_field( 'cmb2_edd_license_clear_nonce_action', 'cmb2_edd_license_clear_nonce' ) .
                '<input type="hidden" name="edd_license_clear_cmb_id" value="' . $this->field->cmb_id . '"/>' .
                '<input type="hidden" name="edd_license_clear_field_id" value="' . $this->_id() . '"/>' .
                '<input type="hidden" name="edd_license_clear_object_id" value="' . $this->field->object_id . '"/>' .
                '<input type="hidden" name="edd_license_clear_object_type" value="' . $this->field->object_type . '"/>' .
                '<button type="button" class="button clear-license-button" name="edd_license_clear_license" value="' . $args['value'] . '">' . $field_args['clear_button'] . '</button>' .
                '</p>';
        }

		// Expiration notice
		$expiration_notice = '';

		if( $field_args['license_expiration'] !== false && $license_status === 'valid' ) {

			if( 'lifetime' === $license->expires ) {

				$expiration_notice = '<p class="license-expiration-notice license-lifetime-notice">' . __( 'Your license never expires.', 'cmb2-edd-license' ) . '</p>';

			} else {

				$expiration_notice = '<p class="license-expiration-notice license-expiration-date-notice">' . sprintf(
					__( 'Your license expires on %s.', 'cmb2-edd-license' ),
					date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) )
				) . '</p>';

			}
		}

		// Renew notice
		$renew_notice = '';

		if( $field_args['renew_license'] !== false && $license_status === 'valid' ) {

			$renew_notice = '<p class="renew-license-notice">' . $field_args['renew_license'] . '</p>';

			// Renew link
			if( $field_args['renew_license_link'] !== false && ! empty( $field_args['renew_license_link'] ) ) {

				$renew_notice = sprintf( '<p class="renew-license-notice"><a href="%s" target="_blank">%s</a></p>',
					$field_args['renew_license_link'],
					$field_args['renew_license']
				);

			}

			// if license timestamp, then need to check if renew text should be removed
			if( $field_args['renew_license_timestamp'] !== false ) {

				$now = current_time( 'timestamp' );
				$expiration = strtotime( $license->expires, current_time( 'timestamp' ) );

				if( $expiration > $now && ! ( $expiration - $now < $field_args['renew_license_timestamp'] ) ) {

					$renew_notice = '';

				}

			}

		}

		// Hide license
        $hidden_field = '';

        if( $field_args['hide_license'] !== false && ! empty( $args['value'] ) ) {

            $character = $field_args['hide_license_character'];
            $visible_characters = absint( $field_args['hide_license_visible_characters'] );

            if( $visible_characters > 0 ) {

                // Hide a portion of the license
                $hidden_license = str_repeat( $character, strlen( $args['value'] ) - $visible_characters ) . substr( $args['value'] , -$visible_characters );

            } else {

                // Completely hide the license
                $hidden_license = str_repeat( $character, strlen( $args['value'] ) );

            }

            // Setup a hidden field to keep license value
            $hidden_field_args = $args;
            $hidden_field_args['type'] = 'hidden';

            $hidden_field = sprintf( '<input%s/>', $this->concat_attrs( $hidden_field_args, array( 'desc', 'js_dependencies' ) ) );

            // Update field value with the hidden license and set the readonly attribute
            $args['value'] = $hidden_license;
            $args['readonly'] = true;

            // Unset the field name to prevent getting updated with hidden characters
            unset( $args['name'] );
        }


		return $this->rendered(
			sprintf( '<input%s/>%s',
				$this->concat_attrs( $args, array( 'desc', 'js_dependencies' ) ),
                $args['desc'] .
				$error_notice .
				$deactivation_button .
                $clear_button .
				$expiration_notice .
				$renew_notice .
                $hidden_field
			)
		);
	}

	public function get_license_error( $license, $field_args ) {

		$message = '';

		if( ! empty( $license ) && is_object( $license ) ) {

			if ( false === $license->success ) {

				switch( $license->error ) {

					case 'expired' :

						$message = sprintf(
							__( 'Your license key expired on %s.', 'cmb2-edd-license' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) )
						);

						if( $field_args['renew_license_link'] !== false ) {
							$message .= ' ' . sprintf( __( 'Please <a href="%s" target="_blank">renew your license key</a>.', 'cmb2-edd-license' ), $field_args['renew_license_link'] );
						}

						break;

					case 'revoked' :

						$message = __( 'Your license key has been disabled.', 'cmb2-edd-license' );

						if( $field_args['contact_link'] !== false ) {
							$message .= ' ' . sprintf( __( 'Please <a href="%s" target="_blank">contact us</a> for more information.', 'cmb2-edd-license' ), $field_args['contact_link'] );
						}

						break;

					case 'missing' :

						$message = __( 'Invalid license.', 'cmb2-edd-license' );

						if( $field_args['license_management_link'] !== false ) {
							$message .= ' ' . sprintf( __( 'Please <a href="%s" target="_blank">visit your account page</a> and verify it.', 'cmb2-edd-license' ), $field_args['license_management_link'] );
						}

						break;

					case 'invalid' :
					case 'site_inactive' :

						$message = sprintf( __( 'Your %s is not active for this URL.', 'cmb2-edd-license' ), $field_args['item_name'] );

						if( $field_args['license_management_link'] !== false ) {
							$message .= ' ' . sprintf( __( 'Please <a href="%s" target="_blank">visit your account page</a> to manage your license key URLs.', 'cmb2-edd-license' ), $field_args['license_management_link'] );
						}

						break;

					case 'item_name_mismatch' :

						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'cmb2-edd-license' ), $field_args['item_name'] );

						break;

					case 'no_activations_left':

						$message = __( 'Your license key has reached its activation limit.', 'cmb2-edd-license' );

						if( $field_args['license_management_link'] !== false ) {
							$message .= ' ' . sprintf( __( '<a href="%s">View possible upgrades</a> now.', 'cmb2-edd-license' ), $field_args['license_management_link'] );
						}

						break;

					case 'license_not_activable':

						$message = __( 'The key you entered belongs to a bundle, please use the product specific license key.', 'cmb2-edd-license' );

						break;

					default :

						$error = ! empty(  $license->error ) ?  $license->error : __( 'Unknown error', 'cmb2-edd-license' );
						$message = sprintf( __( 'There was an error with this license key: %s.', 'cmb2-edd-license' ), $error );

						if( $field_args['contact_link'] !== false ) {
							$message .= ' ' . sprintf( __( 'Please <a href="%s" target="_blank">contact us</a> for more information.', 'cmb2-edd-license' ), $field_args['contact_link'] );
						}

						break;
				}

			}

		}

		return apply_filters( 'cmb2_edd_license_error_message', $message, $license, $field_args );

	}
}
