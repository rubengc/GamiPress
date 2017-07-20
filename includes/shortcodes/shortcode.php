<?php
	/**
	 * GamiPress Shortcode Class
	 *
	 * @package     GamiPress\Shortcodes\Shortcode
	 * @since       1.0.0
	 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Shortcode {

	public $name            = '';
	public $description     = '';
	public $slug            = '';
	public $output_callback = '';
	public $fields      = array();

	public function __construct( $slug, $_args = array() ) {
		$this->slug = $slug;

		// Setup this shortcode's properties
		$this->_set_properties( $_args );

		// Register this shortcode with WP and GamiPress
		add_shortcode( $this->slug, $this->output_callback );

		add_filter( 'gamipress_shortcodes', array( $this, 'register_shortcode' ) );
	}

	/**
	 * Shortcode form with defaults.
	 */
	public function show_form() {
		$cmb2 = $this->cmb2();

		$cmb2->object_id( $this->slug );

		CMB2_hookup::enqueue_cmb_css();
		CMB2_hookup::enqueue_cmb_js();

		$cmb2->show_form();
	}

	/**
	 * Creates a new instance of CMB2 and adds the fields
	 *
	 * @since  1.0.0
	 * @return CMB2
	 */
	public function cmb2( $saving = false ) {

		// Create a new box to render the form
		$cmb2 = new CMB2( array(
			'id'      => $this->slug .'_box', // Option name is taken from the WP_Widget class.
			'hookup'  => false,
			'show_on' => array(
				'key'   => 'options-page', // Tells CMB2 to handle this as an option
				'value' => array( $this->slug )
			),
		), $this->slug );

		foreach ( $this->fields as $field_id => $field ) {
			$field['id'] = $this->slug . '_' . $field_id;

			$cmb2->add_field( $field );
		}

		return $cmb2;
	}

	private function _set_properties( $_args = array() ) {

		$defaults = array(
			'name'            => '',
			'description'     => '',
			'output_callback' => '',
			'fields'      	  => array(),
		);

		$args = wp_parse_args( $_args, $defaults );

		$this->name            = $args['name'];
		$this->description     = $args['description'];
		$this->output_callback = $args['output_callback'];
		$this->fields      	   = $args['fields'];
	}

	public function register_shortcode( $shortcodes = array() ) {
		$shortcodes[ $this->slug ] = $this;
		return $shortcodes;
	}

}
