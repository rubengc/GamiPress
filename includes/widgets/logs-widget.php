<?php
/**
 * Logs Widget
 *
 * @package     GamiPress\Widgets\Widget\Logs
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Logs_Widget extends GamiPress_Widget {

    public function __construct() {

        parent::__construct(
            'gamipress_logs_widget',
            __( 'GamiPress: Logs', 'gamipress' ),
            __( 'Display a list of logs.', 'gamipress' )
        );

    }

    public function get_fields() {

        return GamiPress()->shortcodes['gamipress_logs']->fields;

    }

    public function get_widget( $args, $instance ) {

        echo gamipress_do_shortcode( 'gamipress_logs', array(
            'type'          => $instance['type'],
            'current_user'  => ( $instance['current_user'] === 'on' ? 'yes' : 'no' ),
            'user_id'       => $instance['user_id'],
            'access'        => $instance['access'],
            'order'         => $instance['order'],
            'limit'         => $instance['limit'],
            'pagination'    => ( $instance['pagination'] === 'on' ? 'yes' : 'no' ),
            'orderby'       => $instance['orderby'],
            'include'       => is_array( $instance['include'] ) ? implode( ',', $instance['include'] ) : $instance['include'],
            'exclude'       => is_array( $instance['exclude'] ) ? implode( ',', $instance['exclude'] ) : $instance['exclude'],
        ) );

    }
}