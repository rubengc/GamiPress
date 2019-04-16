<?php
/**
 * Ranks Widget
 *
 * @package     GamiPress\Widgets\Widget\Ranks
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.3.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Ranks_Widget extends GamiPress_Widget {

    /**
     * Shortcode for this widget.
     *
     * @var string
     */
    protected $shortcode = 'gamipress_ranks';

    public function __construct() {

        parent::__construct(
            $this->shortcode . '_widget',
            __( 'GamiPress: Ranks', 'gamipress' ),
            __( 'Display a list of ranks.', 'gamipress' )
        );

    }

    public function get_tabs() {

        $tabs = GamiPress()->shortcodes[$this->shortcode]->tabs;

        // Add the widget title field to the general tab
        $tabs['general']['fields'][] = 'title';

        // Add the renamed rank title field to the achievement tab
        $tabs['rank']['fields'][] = 'show_title';

        // Get the numeric index of the rank field 'title'
        $index = array_search( 'title', $tabs['rank']['fields'] );

        // Remove the title from this tab
        unset( $tabs['rank']['fields'][$index] );

        return $tabs;

    }

    public function get_fields() {

        // Need to change field title to show_title to avoid problems with widget title field
        $fields = GamiPress()->shortcodes[$this->shortcode]->fields;

        // Get the fields keys
        $keys = array_keys( $fields );

        // Get the numeric index of the field 'title'
        $index = array_search( 'title', $keys );

        // Replace the 'title' key by 'show_title'
        $keys[$index] = 'show_title';

        // Combine new array with new keys with an array of values
        $fields = array_combine( $keys, array_values( $fields ) );

        return $fields;

    }

    public function get_widget( $args, $instance ) {

        // Get back replaced fields
        $instance['title'] = $instance['show_title'];

        // Build shortcode attributes from widget instance
        $atts = gamipress_build_shortcode_atts( $this->shortcode, $instance );

        echo gamipress_do_shortcode( $this->shortcode, $atts );

    }

}