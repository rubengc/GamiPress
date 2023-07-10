<?php
/**
 * GamiPress Ranks BuddyPress Component
 *
 * @package GamiPress\BuddyPress\GamiPress_Ranks_BP_Component
 * @since 1.1.1
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'GamiPress_Ranks_BP_Component' ) ) {

    /**
     * Class GamiPress_Ranks_BP_Component
     *
     * @since 1.0.1
     */
    class GamiPress_Ranks_BP_Component extends BP_Component {

        function __construct() {
            parent::start(
                'gamipress-ranks',
                __( 'GamiPress Ranks', 'gamipress' ),
                BP_PLUGIN_DIR
            );

        }

        // Globals
        public function setup_globals( $args = '' ) {
            $ranks_tab_title = gamipress_bp_get_option( 'ranks_tab_title', __( 'Ranks', 'gamipress' ) );
            $ranks_tab_slug = gamipress_bp_get_option( 'ranks_tab_slug', '' );

            // If empty slug generate it from the title
            if( empty( $ranks_tab_slug ) ) {
                $ranks_tab_slug = sanitize_title( $ranks_tab_title );
            }

            parent::setup_globals( array(
                'has_directory' => true,
                'root_slug'     => $ranks_tab_slug,
                'slug'          => $ranks_tab_slug,
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

            if( ! (bool) gamipress_bp_get_option( 'ranks_tab', false ) ) {
                return;
            }

            $ranks_types_to_show = gamipress_bp_members_get_ranks_types();

            if( empty( $ranks_types_to_show ) ) {
                return;
            }

            $tab_title = gamipress_bp_get_option( 'ranks_tab_title', __( 'Ranks', 'gamipress' ) );

            $sub_nav = '';

            // Add to the main navigation
            $main_nav = array(
                'name'                => $tab_title,
                'slug'                => $this->slug,
                'position'            => 100,
                'screen_function'     => 'gamipress_bp_ranks_tab',
                'default_subnav_slug' => $this->slug
            );

            parent::setup_nav( $main_nav, $sub_nav );
        }

    }

}