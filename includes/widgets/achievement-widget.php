<?php
/**
 * Achievement Widget
 *
 * @package     GamiPress\Widgets\Widget\Achivement
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Achievement_Widget extends GamiPress_Widget {

    public function __construct() {
        parent::__construct(
            'gamipress_achievement_widget',
            __( 'GamiPress: Achievement', 'gamipress' ),
            __( 'Display a desired achievement.', 'gamipress' )
        );
    }

    public function get_fields() {
        return GamiPress()->shortcodes['gamipress_achievement']->fields;
    }

    public function get_widget( $args, $instance ) {
        echo gamipress_do_shortcode( 'gamipress_achievement', array(
            'id'        => $instance['id'],
            'thumbnail' => ( $instance['thumbnail'] === 'on' ? 'yes' : 'no' ),
            'excerpt'   => ( $instance['excerpt'] === 'on' ? 'yes' : 'no' ),
            'steps'     => ( $instance['steps'] === 'on' ? 'yes' : 'no' ),
            'toggle'    => ( $instance['toggle'] === 'on' ? 'yes' : 'no' ),
            'earners'   => ( $instance['earners'] === 'on' ? 'yes' : 'no' ),
        ) );
    }

}