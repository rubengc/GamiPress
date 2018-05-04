<?php
/**
 * User Points Widget
 *
 * @package     GamiPress\Widgets\Widget\Points
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Points_Widget extends GamiPress_Widget {

    public function __construct() {

        parent::__construct(
            'gamipress_points_widget',
            __( 'GamiPress: User Points', 'gamipress' ),
            __( 'Display current or specific user points balance.', 'gamipress' )
        );

    }

    public function get_fields() {

        return GamiPress()->shortcodes['gamipress_points']->fields;

    }

    public function get_widget( $args, $instance ) {

        echo gamipress_do_shortcode( 'gamipress_points', array(
            'type'          => is_array( $instance['type'] ) ? implode( ',', $instance['type'] ) : $instance['type'],
            'thumbnail'     => ( $instance['thumbnail'] === 'on' ? 'yes' : 'no' ),
            'label'         => ( $instance['label'] === 'on' ? 'yes' : 'no' ),
            'current_user'  => ( $instance['current_user'] === 'on' ? 'yes' : 'no' ),
            'user_id'       => $instance['user_id'],
            'inline'        => ( $instance['inline'] === 'on' ? 'yes' : 'no' ),
            'columns'       => $instance['columns'],
            'layout'        => $instance['layout'],
            'wpms'          => ( isset( $instance['wpms'] ) && $instance['wpms'] === 'on' ? 'yes' : 'no' ),
        ) );

    }
}