<?php
/**
 * User Earnings Widget
 *
 * @package     GamiPress\Widgets\Widget\Earnings
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.3.9
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Earnings_Widget extends GamiPress_Widget {

    /**
     * Shortcode for this widget.
     *
     * @var string
     */
    protected $shortcode = 'gamipress_earnings';

    public function __construct() {
        parent::__construct(
            $this->shortcode . '_widget',
            __( 'GamiPress: User Earnings', 'gamipress' ),
            __( 'Display a list of user earnings.', 'gamipress' )
        );
    }

    public function get_tabs() {
        $tabs = GamiPress()->shortcodes[$this->shortcode]->tabs;

        // Add the widget title field to the general tab
        $tabs['general']['fields'][] = 'title';

        return $tabs;
    }

    public function get_fields() {
        return GamiPress()->shortcodes[$this->shortcode]->fields;
    }

    public function get_widget( $args, $instance ) {
        // Build shortcode attributes from widget instance
        $atts = gamipress_build_shortcode_atts( $this->shortcode, $instance );

        echo gamipress_do_shortcode( $this->shortcode, $atts );
    }
}