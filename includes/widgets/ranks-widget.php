<?php
/**
 * Ranks Widget
 *
 * @package     GamiPress\Widgets\Widget\Ranks
 * @since       1.3.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Ranks_Widget extends GamiPress_Widget {

    public function __construct() {
        parent::__construct(
            'gamipress_ranks_widget',
            __( 'GamiPress: Ranks', 'gamipress' ),
            __( 'Display a list of ranks.', 'gamipress' )
        );
    }

    public function get_tabs() {

        $tabs = GamiPress()->shortcodes['gamipress_ranks']->tabs;

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
        $fields = GamiPress()->shortcodes['gamipress_ranks']->fields;

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
        echo gamipress_do_shortcode( 'gamipress_ranks', array(
            'type'          => is_array( $instance['type'] ) ? implode( ',', $instance['type'] ) : $instance['type'],
            'columns'       => $instance['columns'],
            'orderby'       => $instance['orderby'],
            'order'         => $instance['order'],
            'current_user'  => ( $instance['current_user'] === 'on' ? 'yes' : 'no' ),
            'user_id'       => $instance['user_id'],
            'include'       => is_array( $instance['include'] ) ? implode( ',', $instance['include'] ) : $instance['include'],
            'exclude'       => is_array( $instance['exclude'] ) ? implode( ',', $instance['exclude'] ) : $instance['exclude'],
            'wpms'          => ( isset( $instance['wpms'] ) && $instance['wpms'] === 'on' ? 'yes' : 'no' ),

            'title'         => ( $instance['show_title'] === 'on' ? 'yes' : 'no' ),
            'link'          => ( $instance['link'] === 'on' ? 'yes' : 'no' ),
            'thumbnail'     => ( $instance['thumbnail'] === 'on' ? 'yes' : 'no' ),
            'excerpt'       => ( $instance['excerpt'] === 'on' ? 'yes' : 'no' ),
            'requirements'  => ( $instance['requirements'] === 'on' ? 'yes' : 'no' ),
            'toggle'        => ( $instance['toggle'] === 'on' ? 'yes' : 'no' ),
            'earners'       => ( $instance['earners'] === 'on' ? 'yes' : 'no' ),
            'layout'        => $instance['layout'],
        ) );
    }

}