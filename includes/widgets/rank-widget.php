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
        return GamiPress()->shortcodes['gamipress_rank']->fields;
    }

    public function get_widget( $args, $instance ) {
        echo gamipress_do_shortcode( 'gamipress_rank', array(
            'id'            => $instance['id'],
            'title'         => ( $instance['title'] === 'on' ? 'yes' : 'no' ),
            'thumbnail'     => ( $instance['thumbnail'] === 'on' ? 'yes' : 'no' ),
            'excerpt'       => ( $instance['excerpt'] === 'on' ? 'yes' : 'no' ),
            'requirements'  => ( $instance['requirements'] === 'on' ? 'yes' : 'no' ),
            'toggle'        => ( $instance['toggle'] === 'on' ? 'yes' : 'no' ),
            'earners'       => ( $instance['earners'] === 'on' ? 'yes' : 'no' ),
        ) );
    }

}