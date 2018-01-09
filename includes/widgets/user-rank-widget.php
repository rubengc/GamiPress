<?php
/**
 * User Rank Widget
 *
 * @package     GamiPress\Widgets\Widget\User_Rank
 * @since       1.3.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_User_Rank_Widget extends GamiPress_Widget {

    public function __construct() {

        parent::__construct(
            'gamipress_user_rank_widget',
            __( 'GamiPress: User Rank', 'gamipress' ),
            __( 'Display previous, current and/or next rank of an user.', 'gamipress' )
        );

    }

    public function get_tabs() {

        $tabs = GamiPress()->shortcodes['gamipress_user_rank']->tabs;

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
        $fields = GamiPress()->shortcodes['gamipress_user_rank']->fields;

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

        echo gamipress_do_shortcode( 'gamipress_user_rank', array(

            'type'          => $instance['type'],
            'prev_rank'     => ( $instance['prev_rank'] === 'on' ? 'yes' : 'no' ),
            'current_rank'  => ( $instance['current_rank'] === 'on' ? 'yes' : 'no' ),
            'next_rank' 	=> ( $instance['next_rank'] === 'on' ? 'yes' : 'no' ),
            'current_user'  => ( $instance['current_user'] === 'on' ? 'yes' : 'no' ),
            'user_id'       => $instance['user_id'],
            'columns'       => $instance['columns'],

            'title'         => ( $instance['show_title'] === 'on' ? 'yes' : 'no' ),
            'thumbnail'     => ( $instance['thumbnail'] === 'on' ? 'yes' : 'no' ),
            'excerpt'       => ( $instance['excerpt'] === 'on' ? 'yes' : 'no' ),
            'requirements'  => ( $instance['requirements'] === 'on' ? 'yes' : 'no' ),
            'toggle'        => ( $instance['toggle'] === 'on' ? 'yes' : 'no' ),
            'earners'       => ( $instance['earners'] === 'on' ? 'yes' : 'no' ),

        ) );

    }

}