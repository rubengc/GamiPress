<?php
/**
 * User Points Types Widget
 *
 * @package     GamiPress\Widgets\Widget\Points_Types
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Points_Types_Widget extends GamiPress_Widget {

    public function __construct() {
        parent::__construct(
            'gamipress_points_types_widget',
            __( 'GamiPress: Points Types', 'gamipress' ),
            __( 'Display a list of points types with their points awards.', 'gamipress' )
        );
    }

    public function get_fields() {
        return GamiPress()->shortcodes['gamipress_points_types']->fields;
    }

    public function get_widget( $args, $instance ) {
        echo gamipress_do_shortcode( 'gamipress_points_types', array(
            'type'              => is_array( $instance['type'] ) ? implode( ',', $instance['type'] ) : $instance['type'],
            'thumbnail'         => ( isset( $instance['thumbnail'] ) && $instance['thumbnail'] === 'on' ? 'yes' : 'no' ),
            'awards'     => ( isset( $instance['awards'] ) && $instance['awards'] === 'on' ? 'yes' : 'no' ),
            'deducts'    => ( isset( $instance['deducts'] ) && $instance['deducts'] === 'on' ? 'yes' : 'no' ),
            'toggle'            => ( isset( $instance['toggle'] ) && $instance['toggle'] === 'on' ? 'yes' : 'no' ),
            'wpms'              => ( isset( $instance['wpms'] ) && $instance['wpms'] === 'on' ? 'yes' : 'no' ),
        ) );
    }
}