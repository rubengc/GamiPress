<?php
/**
 * GamiPress Shortcode Class
 *
 * @package     GamiPress\Shortcodes\Shortcode
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Shortcode {

	public $name            = '';
	public $description     = '';
	public $slug            = '';
	public $icon            = '';
	public $group           = '';
	public $output_callback = '';
	public $tabs 			= array();
	public $fields      	= array();

	public function __construct( $slug, $args = array() ) {
		$this->slug = $slug;

		// Setup this shortcode's properties
		$this->_set_properties( $args );

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

		CMB2_Hookup::enqueue_cmb_css();
		CMB2_Hookup::enqueue_cmb_js();

		$cmb2->show_form();
	}

	/**
	 * Creates a new instance of CMB2 and adds the fields
	 *
	 * @since  1.0.0
	 *
	 * @return CMB2 $cmb2
	 */
	public function cmb2( $saving = false ) {

		foreach( $this->tabs as $tab_id => $tab ) {
			// Generate the id of the tab based on shortcode slug
			$tab['id'] = $this->slug . '_' . $tab_id;

			foreach( $tab['fields'] as $tab_field_index => $tab_field ) {
				// Update the id of the tab field based on shortcode slug
				$tab['fields'][$tab_field_index] = $this->slug . '_' . $tab_field;
			}

			$this->tabs[$tab_id] = $tab;
		}

		// Create a new box to render the form
		$cmb2 = new CMB2( array(
			'id'      	=> $this->slug .'_box', // Option name is taken from the WP_Widget class.
			'classes' 	=> 'gamipress-form gamipress-shortcode-form',
			'tabs'    	=> $this->tabs,
			'hookup'  	=> false,
			'show_on' 	=> array(
				'key'   => 'options-page', // Tells CMB2 to handle this as an option
				'value' => array( $this->slug )
			),
		), $this->slug );

		foreach( $this->fields as $field_id => $field ) {

			// Set the id of the field based on shortcode slug
			$field['id'] = $this->slug . '_' . $field_id;

			// Loop through group fields
			if( $field['type'] === 'group' && ! empty( $field['fields'] ) ) {

				foreach( $field['fields'] as $group_field_id => $group_field ) {
					// Set the id of the group field based on shortcode slug
					$field['fields'][$group_field_id]['id'] =  $this->slug . '_' . $group_field_id;
				}

			}

			$cmb2->add_field( $field );
		}

		return $cmb2;
	}

	private function _set_properties( $args = array() ) {

		$args = wp_parse_args( $args, array(
            'name'              => '',
            'description'       => '',
            'icon'              => 'gamipress',
            'group'             => 'others',
            'output_callback'   => '',
            'tabs' 			    => array(),
            'fields'      	    => array(),
        ) );

		$this->name             = $args['name'];
		$this->description      = $args['description'];
		$this->icon             = $args['icon'];
		$this->group            = $args['group'];
		$this->output_callback  = $args['output_callback'];

		// Filter to register custom shortcode tabs
		$this->tabs 		    = apply_filters( "gamipress_{$this->slug}_shortcode_tabs", $args['tabs'] );

		// Filter to register custom shortcode fields
		$this->fields      	    = apply_filters( "gamipress_{$this->slug}_shortcode_fields", $args['fields'] );
	}

	public function register_shortcode( $shortcodes = array() ) {

		$shortcodes[ $this->slug ] = $this;

		return $shortcodes;

	}

}
