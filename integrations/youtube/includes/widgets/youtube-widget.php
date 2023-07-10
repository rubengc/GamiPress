<?php
/**
 * Youtube Widget
 *
 * @package     GamiPress\Widgets\Widget\Youtube
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Youtube_Widget extends GamiPress_Widget {

    public function __construct() {

        parent::__construct(
            'gamipress_youtube_widget',
            __( 'GamiPress: Youtube', 'gamipress' ),
            __( 'Display a Youtube video.', 'gamipress' )
        );

    }

    public function get_fields() {

        return GamiPress()->shortcodes['gamipress_youtube']->fields;

    }

    public function get_widget( $args, $instance ) {

        echo gamipress_do_shortcode( 'gamipress_youtube', array(
            'url'               => $instance['url'],
            'width'             => $instance['width'],
            'height'            => $instance['height'],
            'autoplay'          => ( $instance['autoplay'] === 'on' ? 'yes' : 'no' ),
            'controls'          => ( $instance['controls'] === 'on' ? 'yes' : 'no' ),
        ) );

    }

}