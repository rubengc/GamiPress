<?php
/**
 * GamiPress Achievements BuddyPress Component
 *
 * @package GamiPress\BuddyPress\GamiPress_Achievements_BP_Component
 * @since 1.0.1
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'GamiPress_Achievements_BP_Component' ) ) {

    /**
     * Class GamiPress_Achievements_BP_Component
     *
     * @since 1.0.1
     */
    class GamiPress_Achievements_BP_Component extends BP_Component {

        function __construct() {
            parent::start(
                'gamipress-achievements',
                __( 'GamiPress Achievements', 'gamipress' ),
                BP_PLUGIN_DIR
            );

        }

        // Globals
        public function setup_globals( $args = '' ) {
            $achievements_tab_title = gamipress_bp_get_option( 'achievements_tab_title', __( 'Achievements', 'gamipress' ) );
            $achievements_tab_slug = gamipress_bp_get_option( 'achievements_tab_slug', '' );

            // If empty slug generate it from the title
            if( empty( $achievements_tab_slug ) ) {
                $achievements_tab_slug = sanitize_title( $achievements_tab_title );
            }

            parent::setup_globals( array(
                'has_directory' => true,
                'root_slug'     => $achievements_tab_slug,
                'slug'          => $achievements_tab_slug,
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

            if( ! (bool) gamipress_bp_get_option( 'achievements_tab', false ) ) {
                return;
            }

            $achievements_types_to_show = gamipress_bp_tab_get_achievements_types();

            if( empty( $achievements_types_to_show ) ) {
                return;
            }

            $parent_url = trailingslashit( bp_displayed_user_domain() . $this->slug );

            // Get registered achievement types
            $achievement_types = gamipress_get_achievement_types();

            $tab_title = gamipress_bp_get_option( 'achievements_tab_title', __( 'Achievements', 'gamipress' ) );


            $sub_nav = array();

            // Loop achievement types order
            foreach ( $achievements_types_to_show as $achievement_type_slug ) {

                if( in_array( $achievement_type_slug, gamipress_get_requirement_types_slugs() )         // If is a requirement type
                    || ! isset( $achievement_types[$achievement_type_slug] )                            // or is not registered
                ) {
                    continue;
                }

                // Only run once to set main nav and default sub nav
                if ( empty( $main ) ) {
                    // Add to the main navigation
                    $main_nav = array(
                        'name'                => $tab_title,
                        'slug'                => $this->slug,
                        'position'            => 100,
                        'screen_function'     => 'gamipress_bp_achievements_tab',
                        'default_subnav_slug' => $achievement_type_slug
                    );

                    $main = true;
                }

                $sub_nav[] = array(
                    'name'            => $achievement_types[$achievement_type_slug]['plural_name'],
                    'slug'            => $achievement_type_slug,
                    'parent_url'      => $parent_url,
                    'parent_slug'     => $this->slug,
                    'screen_function' => 'gamipress_bp_achievements_tab',
                    'position'        => 10,
                );

            }

            parent::setup_nav( $main_nav, $sub_nav );
        }

    }

}