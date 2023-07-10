<?php
/**
 * GamiPress Points BuddyPress Component
 *
 * @package GamiPress\BuddyPress\GamiPress_Points_BP_Component
 * @since 1.0.8
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'GamiPress_Points_BP_Component' ) ) {

    /**
     * Class GamiPress_Points_BP_Component
     *
     * @since 1.0.8
     */
    class GamiPress_Points_BP_Component extends BP_Component {

        function __construct() {
            parent::start(
                'gamipress-points',
                __( 'GamiPress Points', 'gamipress' ),
                BP_PLUGIN_DIR
            );

        }

        // Globals
        public function setup_globals( $args = '' ) {
            $points_tab_title = gamipress_bp_get_option( 'points_tab_title', __( 'Points', 'gamipress' ) );
            $points_tab_slug = gamipress_bp_get_option( 'points_tab_slug', '' );

            // If empty slug generate it from the title
            if( empty( $points_tab_slug ) ) {
                $points_tab_slug = sanitize_title( $points_tab_title );
            }

            parent::setup_globals( array(
                'has_directory' => true,
                'root_slug'     => $points_tab_slug,
                'slug'          => $points_tab_slug,
            ) );
        }

        // BuddyPress actions
        public function setup_actions() {
            parent::setup_actions();
        }

        // Member Profile Menu
        public function setup_nav( $main_nav = '', $sub_nav = '' ) {

            if ( ! is_user_logged_in() && ! bp_displayed_user_id() )
                return;

            if( ! (bool) gamipress_bp_get_option( 'points_tab', false ) ) {
                return;
            }

            $points_types_to_show = gamipress_bp_members_get_points_types();

            if( empty( $points_types_to_show ) ) {
                return;
            }

            $tab_title = gamipress_bp_get_option( 'points_tab_title', __( 'Points', 'gamipress' ) );

            $sub_nav = '';

            // Add to the main navigation
            $main_nav = array(
                'name'                => $tab_title,
                'slug'                => $this->slug,
                'position'            => 100,
                'screen_function'     => 'gamipress_bp_points_tab',
                'default_subnav_slug' => $this->slug
            );

            parent::setup_nav( $main_nav, $sub_nav );
        }

    }

}