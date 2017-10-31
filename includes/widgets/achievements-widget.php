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

        $tabs['general']['fields'][] = 'title';

        return $tabs;

    }

    public function get_fields() {
        return GamiPress()->shortcodes['gamipress_achievements']->fields;
    }

    public function get_widget( $args, $instance ) {
        echo gamipress_do_shortcode( 'gamipress_achievements', array(
            'type'      => is_array( $instance['type'] ) ? implode( ',', $instance['type'] ) : $instance['type'],
            'thumbnail' => ( $instance['thumbnail'] === 'on' ? 'yes' : 'no' ),
            'excerpt'   => ( $instance['excerpt'] === 'on' ? 'yes' : 'no' ),
            'steps'     => ( $instance['steps'] === 'on' ? 'yes' : 'no' ),
            'columns'   => $instance['columns'],
            'filter'    => ( $instance['filter'] === 'on' ? 'yes' : 'no' ),
            'search'    => ( $instance['search'] === 'on' ? 'yes' : 'no' ),
            'limit'     => $instance['limit'],
            'orderby'   => $instance['orderby'],
            'order'     => $instance['order'],
            'current_user'   => ( $instance['current_user'] === 'on' ? 'yes' : 'no' ),
            'user_id'   => $instance['user_id'],
            'include'   => is_array( $instance['include'] ) ? implode( ',', $instance['include'] ) : $instance['include'],
            'exclude'   => is_array( $instance['exclude'] ) ? implode( ',', $instance['exclude'] ) : $instance['exclude'],
            'wpms'      => ( isset( $instance['wpms'] ) && $instance['wpms'] === 'on' ? 'yes' : 'no' ),
        ) );
    }

}