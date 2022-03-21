<?php
/**
 * Inline Last Achievements Earned Widget
 *
 * @package     GamiPress\Widgets\Widget\Inline_Last_Achievements_Earned
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       2.3.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Inline_Last_Achievements_Earned_Widget extends GamiPress_Widget {

    /**
     * Shortcode for this widget.
     *
     * @var string
     */
    protected $shortcode = 'gamipress_inline_last_achievements_earned';

    public function __construct() {

        parent::__construct(
            $this->shortcode . '_widget',
            __( 'GamiPress: Inline Last Achievements Earned', 'gamipress' ),
            __( 'Display the last achievements earned by the current user or a desired user inline.', 'gamipress' )
        );

    }

    public function get_tabs() {

        $tabs = GamiPress()->shortcodes[$this->shortcode]->tabs;

        return $tabs;

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