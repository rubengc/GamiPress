<?php
/**
 * Rank Widget
 *
 * @package     GamiPress\Widgets\Widget\Rank
 * @since       1.3.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Rank_Widget extends GamiPress_Widget {

    public function __construct() {

        parent::__construct(
            'gamipress_rank_widget',
            __( 'GamiPress: Rank', 'gamipress' ),
            __( 'Display a desired rank.', 'gamipress' )
        );

    }

    public function get_fields() {

        // Need to change field title to show_title to avoid problems with widget title field
        $fields = GamiPress()->shortcodes['gamipress_rank']->fields;

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

        echo gamipress_do_shortcode( 'gamipress_rank', array(
            'id'            => $instance['id'],
            'title'         => ( $instance['show_title'] === 'on' ? 'yes' : 'no' ),
            'link'          => ( $instance['link'] === 'on' ? 'yes' : 'no' ),
            'thumbnail'     => ( $instance['thumbnail'] === 'on' ? 'yes' : 'no' ),
            'excerpt'       => ( $instance['excerpt'] === 'on' ? 'yes' : 'no' ),
            'requirements'  => ( $instance['requirements'] === 'on' ? 'yes' : 'no' ),
            'toggle'        => ( $instance['toggle'] === 'on' ? 'yes' : 'no' ),
            'unlock_button' => ( $instance['unlock_button'] === 'on' ? 'yes' : 'no' ),
            'earners'       => ( $instance['earners'] === 'on' ? 'yes' : 'no' ),
            'layout'        => $instance['layout'],
        ) );

    }

}