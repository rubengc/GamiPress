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
			'server'          => '',
			'item_id'         => '',
			'item_name'       => '',
			'file'            => '',
			'version'         => '',
			'author'          => '',
			'wp_override'     => false,
			// Extra settings
			'deactivate_button' => __( 'Deactivate License', 'cmb2-edd-license' ),
			'license_expiration' => true,
			'renew_license' => __( 'Renew your license key.', 'cmb2-edd-license' ),
			'renew_license_link' => false,
			'renew_license_timestamp' => ( DAY_IN_SECONDS * 30 ),
		), $this->field->_data( 'args' ) );

		$this->field->add_js_dependencies( array(
			'cmb-edd-license-js'
		) );

		$license = cmb2_edd_license_data( $args['value'] );
		$license_status = ( $license !== false ) ? $license->license : false;

		if( $license_status !== false) {
			// Add the class license-{$license_status} (valid or invalid)
            $args['class'] .= ' license-' . $license_status;
		}

		// Deactivation button
		$deactivation_button = '';

		if( $field_args['deactivate_button'] !== false && $license_status === 'valid' ) {
			$deactivation_button = '<p class="deactivate-license">' .
                '<form name="edd_license_deactivate_form" method="post">' .
                    wp_nonce_field( 'cmb2_edd_license_deactivation_nonce_action', 'cmb2_edd_license_deactivation_nonce' ) .
                    '<input type="hidden" name="edd_license_deactivate_cmb_id" value="' . $this->field->cmb_id . '"/>' .
                    '<input type="hidden" name="edd_license_deactivate_field_id" value="' . $this->_id() . '"/>' .
                    '<input type="hidden" name="edd_license_deactivate_object_id" value="' . $this->field->object_id . '"/>' .
                    '<input type="hidden" name="edd_license_deactivate_object_type" value="' . $this->field->object_type . '"/>' .
                    '<button type="submit" class="button deactivate-license-button" name="edd_license_deactivate_license" value="' . $args['value'] . '">' . $field_args['deactivate_button'] . '</button>' .
                '</form>' .
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


		return $this->rendered(
			sprintf( '<input%s/>%s',
				$this->concat_attrs( $args, array( 'desc', 'js_dependencies' ) ),
                $args['desc'] .
				$deactivation_button .
				$expiration_notice .
				$renew_notice
			)
		);
	}
}
