<?php
/**
 * Vimeo Widget
 *
 * @package     GamiPress\Widgets\Widget\Vimeo
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Vimeo_Widget extends GamiPress_Widget {

    public function __construct() {

        parent::__construct(
            'gamipress_vimeo_widget',
            __( 'GamiPress: Vimeo', 'gamipress' ),
            __( 'Display a Vimeo video.', 'gamipress' )
        );

    }

    public function get_fields() {

        return GamiPress()->shortcodes['gamipress_vimeo']->fields;

    }

    public function get_widget( $args, $instance ) {

        echo gamipress_do_shortcode( 'gamipress_vimeo', array(
            'url'               => $instance['url'],
            'width'             => $instance['width'],
            'height'            => $instance['height'],
        ) );

    }

}