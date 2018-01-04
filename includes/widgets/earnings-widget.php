<?php
/**
 * User Earnings Widget
 *
 * @package     GamiPress\Widgets\Widget\Earnings
 * @since       1.3.9
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Earnings_Widget extends GamiPress_Widget {

    public function __construct() {
        parent::__construct(
            'gamipress_earnings_widget',
            __( 'GamiPress: User Earnings', 'gamipress' ),
            __( 'Display a list of user earnings.', 'gamipress' )
        );
    }

    public function get_tabs() {

        $tabs = GamiPress()->shortcodes['gamipress_earnings']->tabs;

        // Add the widget title field to the general tab
        $tabs['general']['fields'][] = 'title';

        return $tabs;

    }

    public function get_fields() {
        return GamiPress()->shortcodes['gamipress_earnings']->fields;
    }

    public function get_widget( $args, $instance ) {
        echo gamipress_do_shortcode( 'gamipress_earnings', array(
            'current_user'      => ( $instance['current_user'] === 'on' ? 'yes' : 'no' ),
            'user_id'           => $instance['user_id'],
            'limit'             => $instance['limit'],
            'pagination'        => ( $instance['pagination'] === 'on' ? 'yes' : 'no' ),
            'order'             => $instance['order'],

            'points'            => ( $instance['points'] === 'on' ? 'yes' : 'no' ),
            'points_types'      => $instance['points_types'],
            'awards'            => ( $instance['awards'] === 'on' ? 'yes' : 'no' ),
            'deducts'           => ( $instance['deducts'] === 'on' ? 'yes' : 'no' ),

            'achievements'      => ( $instance['achievements'] === 'on' ? 'yes' : 'no' ),
            'achievement_types' => $instance['achievement_types'],
            'steps'             => ( $instance['steps'] === 'on' ? 'yes' : 'no' ),

            'ranks'             => ( $instance['ranks'] === 'on' ? 'yes' : 'no' ),
            'rank_types'        => $instance['rank_types'],
            'rank_requirements' => ( $instance['rank_requirements'] === 'on' ? 'yes' : 'no' ),
        ) );
    }
}