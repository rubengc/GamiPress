<?php
/**
 * Inline Achievement Widget
 *
 * @package     GamiPress\Widgets\Widget\Inline_Achievement
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       2.3.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Inline_Achievement_Widget extends GamiPress_Widget {

    /**
     * Shortcode for this widget.
     *
     * @var string
     */
    protected $shortcode = 'gamipress_inline_achievement';

    public function __construct() {

        parent::__construct(
            $this->shortcode . '_widget',
            __( 'GamiPress: Inline Achievement', 'gamipress' ),
            __( 'Display a desired achievement inline.', 'gamipress' )
        );

    }

    public function get_fields() {

        // Need to change field title to show_title to avoid problems with widget title field
        $fields = GamiPress()->shortcodes[$this->shortcode]->fields;

        return $fields;

    }

    public function get_widget( $args, $instance ) {

        // Build shortcode attributes from widget instance
        $atts = gamipress_build_shortcode_atts( $this->shortcode, $instance );

        echo gamipress_do_shortcode( $this->shortcode, $atts );

    }

}