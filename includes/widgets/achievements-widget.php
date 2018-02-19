<?php
/**
 * Achievements Widget
 *
 * @package     GamiPress\Widgets\Widget\Achivements
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Achievements_Widget extends GamiPress_Widget {

    public function __construct() {
        parent::__construct(
            'gamipress_achievements_widget',
            __( 'GamiPress: Achievements', 'gamipress' ),
            __( 'Display a list of achievements.', 'gamipress' )
        );
    }

    public function get_tabs() {

        $tabs = GamiPress()->shortcodes['gamipress_achievements']->tabs;

        // Add the widget title field to the general tab
        $tabs['general']['fields'][] = 'title';

        // Add the renamed achievement title field to the achievement tab
        $tabs['achievement']['fields'][] = 'show_title';

        // Get the numeric index of the achievement field 'title'
        $index = array_search( 'title', $tabs['achievement']['fields'] );

        // Remove the title from this tab
        unset( $tabs['achievement']['fields'][$index] );

        return $tabs;

    }

    public function get_fields() {

        // Need to change field title to show_title to avoid problems with widget title field
        $fields = GamiPress()->shortcodes['gamipress_achievements']->fields;

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
        echo gamipress_do_shortcode( 'gamipress_achievements', array(
            'type'          => is_array( $instance['type'] ) ? implode( ',', $instance['type'] ) : $instance['type'],
            'columns'       => $instance['columns'],
            'filter'        => ( $instance['filter'] === 'on' ? 'yes' : 'no' ),
            'search'        => ( $instance['search'] === 'on' ? 'yes' : 'no' ),
            'limit'         => $instance['limit'],
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
            'steps'         => ( $instance['steps'] === 'on' ? 'yes' : 'no' ),
            'toggle'        => ( $instance['toggle'] === 'on' ? 'yes' : 'no' ),
            'earners'       => ( $instance['earners'] === 'on' ? 'yes' : 'no' ),
            'layout'        => $instance['layout'],
        ) );
    }

}