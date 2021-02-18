<?php
/**
 * GamiPress Widget Class
 *
 * @package     GamiPress\Widgets\Widget
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Widget extends WP_Widget {

    /**
     * Unique identifier for this widget.
     *
     * Will also serve as the widget class.
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $widget_slug;

    /**
     * Array of default values for widget settings.
     *
     * @since 1.0.0
     *
     * @var array
     */
    protected $defaults = array();

    /**
     * Store the instance properties as property.
     *
     * @since 1.0.0
     *
     * @var array
     */
    protected $_instance = array();

    /**
     * Array of CMB2 fields args.
     *
     * @since 1.0.0
     *
     * @var array
     */
    protected $fields = array();

    public function __construct( $widget_slug, $name, $description ) {
        $this->widget_slug = $widget_slug;

        parent::__construct(
            $this->widget_slug,
            esc_html( $name ),
            array(
                'classname' => $this->widget_slug,
                'customize_selective_refresh' => true,
                'description' => esc_html( $description ),
            )
        );

    }

    /**
     * Front-end display of widget.
     *
     * @since 1.0.0
     *
     * @param  array  $args      The widget arguments set up when a sidebar is registered.
     * @param  array  $instance  The widget settings as set by user.
     */
    public function widget( $args, $instance ) {

        global $gamipress_renderer, $gamipress_current_widget;

        // Setup renderer
        $gamipress_renderer = 'widget';
        $gamipress_current_widget = $this;

        $atts = array(
            'args'     => $args,
            'instance' => $instance,
        );

        if( ! isset( $atts['args'] ) ) {
            $atts['args'] = array();
        }

        // Set up default values for attributes
        $atts = shortcode_atts(
            array(
                // Ensure variables
                'instance'      => array(),
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => '',
            ),
            $args,
            $this->widget_slug
        );

        $this->setup_fields();

        $instance = shortcode_atts(
            $this->defaults,
            $instance,
            $this->widget_slug
        );

        // Let's render the widget
        $widget = '';

        // Before widget hook
        $widget .= $atts['before_widget'];

        // Title
        $widget .= ( $instance['title'] ) ? $atts['before_title'] . esc_html( $instance['title'] ) . $atts['after_title'] : '';

        ob_start();

        // {$widget_slug}_before
        do_action( $this->widget_slug . '_before' );

        // Widget content
        $this->get_widget( $args, $instance );

        // {$widget_slug}_after
        do_action( $this->widget_slug . '_after' );

        $widget .= ob_get_clean();

        // After widget hook
        $widget .= $atts['after_widget'];

        // Reset renderer
        $gamipress_renderer = false;
        $gamipress_current_widget = null;

        echo $widget;
    }

    /**
     * Return the widget/shortcode output
     *
     * @since 1.0.0
     *
     * @param  array  $args      The widget arguments set up when a sidebar is registered.
     * @param  array  $instance  The widget settings as set by user.
     * @return string            The widget output.
     */
    public function get_widget( $args, $instance ) {

    }

    /**
     * The widget tabs.
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_tabs() {
        return array();
    }

    /**
     * The widget form fields.
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_fields() {
        return array();
    }

    /**
     * Setup the widget form fields.
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function setup_fields() {
        // Set up fields
        $this->fields = $this->get_fields();

        if( ! isset( $this->fields['title'] ) ) {
            $this->fields = array(
                    'title' => array(
                        'name'      => __( 'Title:', 'gamipress' ),
                        'type'      => 'text',
                        'default'   => '',
                    ),
                ) + $this->fields;
        }

        /**
         * Available filter to extend the widget form fields
         *
         * @since 1.9.9.2
         *
         * @param array $fields
         * @param array $widget_slug
         *
         * @return array
         */
        $this->fields = apply_filters( 'gamipress_setup_widget_fields', $this->fields, $this->widget_slug );

        $this->defaults = array();

        // Set up fields defaults
        foreach( $this->fields as $field_id => $field ) {
            $this->defaults[$field_id] = ( isset( $field['default'] ) ? $field['default'] : '' );
        }
    }

    /**
     * Back-end widget form with defaults.
     *
     * @param  array  $instance  Current settings.
     * @return string Default return is 'noform'.
     */
    public function form( $instance ) {

        $this->setup_fields();

        if( count( $this->fields ) > 0 ) {
            // If there are no settings, set up defaults
            $this->_instance = wp_parse_args( (array) $instance, $this->defaults);

            $cmb2 = $this->cmb2();

            $cmb2->object_id( $this->option_name );
            CMB2_Hookup::enqueue_cmb_css();
            CMB2_Hookup::enqueue_cmb_js();
            $cmb2->show_form();

            return '';
        }

        return 'noform';
    }

    /**
     * Update form values as they are saved.
     *
     * @param  array  $new_instance  New settings for this instance as input by the user.
     * @param  array  $old_instance  Old settings for this instance.
     * @return array  Settings to save or bool false to cancel saving.
     */
    public function update( $new_instance, $old_instance ) {
        $this->setup_fields();

        $sanitized = $this->cmb2( true )->get_sanitized_values( $new_instance );

        return $sanitized;
    }

    /**
     * Creates a new instance of CMB2 and adds the fields
     *
     * @since  1.0.0
     *
     * @return CMB2
     */
    public function cmb2( $saving = false ) {

        $tabs = $this->get_tabs();

        foreach ( $tabs as $tab_id => $tab ) {
            // Generate the id of the tab based on shortcode slug
            $tab['id'] = $this->get_field_name( $tab_id );

            foreach( $tab['fields'] as $tab_field_index => $tab_field ) {
                // Update the id of the tab field based on shortcode slug
                $tab['fields'][$tab_field_index] = $this->get_field_name( $tab_field );
            }

            $tabs[$tab_id] = $tab;
        }

        // Create a new box in the class
        $cmb2 = new CMB2( array(
            'id'        => $this->option_name .'_box', // Option name is taken from the WP_Widget class.
            'classes'   => 'gamipress-form gamipress-widget-form',
            'tabs'      => $tabs,
            'hookup'    => false,
            'show_on'   => array(
                'key'   => 'options-page',
                'value' => array( $this->option_name )
            ),
        ), $this->option_name );

        foreach ( $this->fields as $field_id => $field ) {
            $field['id'] = $field_id;
            $field['id_key'] = $field_id; // Saves the given id into a custom parameter to use it as the real field id

            if ( ! $saving ) {
                $field['id'] = $this->get_field_name( $field_id );
            }

            $field['default_cb'] = array( $this, 'default_cb' );

            // Remove default to make default_cb work
            if( isset( $field['default'] ) ) {
                unset( $field['default'] );
            }

            $cmb2->add_field( $field );
        }

        return $cmb2;
    }

    /**
     * Sets the field default, or the field value.
     *
     * @param  array      $field_args CMB2 field args array
     * @param  CMB2_Field $field CMB2 Field object.
     *
     * @return mixed      Field value.
     */
    public function default_cb( $field_args, $field ) {

        if( $field->args( 'type' ) === 'checkbox' ) {
            return isset( $this->_instance[ $field->args( 'id_key' ) ] )
                && $this->_instance[ $field->args( 'id_key' ) ] === 'on'
                ? 'on'
                : '';
        }

        return isset( $this->_instance[ $field->args( 'id_key' ) ] )
            ? $this->_instance[ $field->args( 'id_key' ) ]
            : null;
    }

}