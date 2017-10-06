<?php
/**
 * Plugin Name:     GamiPress
 * Plugin URI:      https://gamipress.com
 * Description:     The most flexible and powerful gamification system for WordPress.
 * Version:         1.2.5
 * Author:          GamiPress
 * Author URI:      https://gamipress.com/
 * Text Domain:     gamipress
 * License:         GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package         GamiPress
 * @author          Tsunoa
 * @copyright       Copyright (c) Tsunoa
*/

/*
 * GamiPress is based on BadgeOS by LearningTimes, LLC (https://credly.com/)
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General
 * Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

final class GamiPress {

	/**
	 * @var         GamiPress $instance The one true GamiPress
	 * @since       1.0.0
	 */
	private static $instance;

	/**
	 * @var         array $settings GamiPress stored settings
	 * @since       1.0.2
	 */
	public $settings = null;

	/**
	 * @var         array $points_types GamiPress registered points types
	 * @since       1.0.0
	 */
	public $points_types = array();

	/**
	 * @var         array $achievement_types GamiPress registered achievement types
	 * @since       1.0.0
	 */
	public $achievement_types = array();

	/**
	 * @var         array $requirement_types GamiPress registered requirement types
	 * @since       1.0.5
	 */
	public $requirement_types = array();

    /**
     * @var         array $activity_triggers GamiPress registered activity triggers
     * @since       1.0.0
     */
    public $activity_triggers = array();

	/**
	 * @var         array $shortcodes GamiPress registered shortcodes
	 * @since       1.0.0
	 */
	public $shortcodes = array();

	/**
	 * Get active instance
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      object self::$instance The one true GamiPress
	 */
	public static function instance() {

		if( !self::$instance ) {

			self::$instance = new GamiPress();
			self::$instance->constants();
			self::$instance->libraries();
			self::$instance->includes();
			self::$instance->hooks();
			self::$instance->load_textdomain();

		}

		return self::$instance;

	}

	/**
	 * Setup plugin constants
	 *
	 * @access      private
	 * @since       1.0.0
	 * @return      void
	 */
	private function constants() {

		// Plugin version
		define( 'GAMIPRESS_VER', '1.2.5' );

		// Plugin file
		define( 'GAMIPRESS_FILE', __FILE__ );

		// Plugin path
		define( 'GAMIPRESS_DIR', plugin_dir_path( __FILE__ ) );

		// Plugin URL
		define( 'GAMIPRESS_URL', plugin_dir_url( __FILE__ ) );

	}

    /**
     * Include plugin libraries
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function libraries() {

		// Global libraries
        require_once GAMIPRESS_DIR . 'libraries/p2p/load.php';

		// Admin libraries
		if( is_admin() ) {

			require_once GAMIPRESS_DIR . 'libraries/cmb2/init.php';
			require_once GAMIPRESS_DIR . 'libraries/cmb2-metatabs-options/cmb2_metatabs_options.php';
			require_once GAMIPRESS_DIR . 'libraries/cmb2-tabs/cmb2-tabs.php';
			require_once GAMIPRESS_DIR . 'libraries/cmb2-field-edd-license/cmb2-field-edd-license.php';
			require_once GAMIPRESS_DIR . 'libraries/cmb2-rgba-colorpicker/jw-cmb2-rgba-colorpicker.php';
			require_once GAMIPRESS_DIR . 'libraries/advanced-select-field-type.php';
			require_once GAMIPRESS_DIR . 'libraries/size-field-type.php';
			require_once GAMIPRESS_DIR . 'libraries/display-field-type.php';
			require_once GAMIPRESS_DIR . 'libraries/button-field-type.php';
			require_once GAMIPRESS_DIR . 'libraries/html-field-type.php';

		}

    }

	/**
	 * Include plugin files
	 *
	 * @access      private
	 * @since       1.0.0
	 * @return      void
	 */
	private function includes() {

		require_once GAMIPRESS_DIR . 'includes/admin.php';
		require_once GAMIPRESS_DIR . 'includes/post-types.php';
		require_once GAMIPRESS_DIR . 'includes/achievement-functions.php';
		require_once GAMIPRESS_DIR . 'includes/activity-functions.php';
		require_once GAMIPRESS_DIR . 'includes/ajax-functions.php';
		require_once GAMIPRESS_DIR . 'includes/functions.php';
		require_once GAMIPRESS_DIR . 'includes/listeners.php';
		require_once GAMIPRESS_DIR . 'includes/log-functions.php';
		require_once GAMIPRESS_DIR . 'includes/points-functions.php';
		require_once GAMIPRESS_DIR . 'includes/requirement-functions.php';
		require_once GAMIPRESS_DIR . 'includes/scripts.php';
		require_once GAMIPRESS_DIR . 'includes/shortcodes.php';
		require_once GAMIPRESS_DIR . 'includes/content-filters.php';
		require_once GAMIPRESS_DIR . 'includes/rules-engine.php';
		require_once GAMIPRESS_DIR . 'includes/template-functions.php';
		require_once GAMIPRESS_DIR . 'includes/triggers.php';
		require_once GAMIPRESS_DIR . 'includes/user.php';
		require_once GAMIPRESS_DIR . 'includes/widgets.php';

	}

	/**
	 * Setup plugin hooks
	 *
	 * @access      private
	 * @since       1.0.0
	 * @return      void
	 */
	private function hooks() {

		// Setup our activation and deactivation hooks
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// Hook in all our important pieces
		add_action( 'init', array( $this, 'register_points_relationships' ) );
		add_action( 'init', array( $this, 'register_achievement_relationships' ) );
		add_action( 'init', array( $this, 'register_image_sizes' ) );

	}

	/**
	 * Register points Post2Post relationships
	 */
	function register_points_relationships() {

        // Connect points awards to points type
        // Used to get a points type's active awards (e.g. This points type has these 3 points awards active)
        p2p_register_connection_type( array(
            'name'      => 'points-award-to-points-type',
            'from'      => 'points-award',
            'to'        => 'points-type',
            'admin_box' => false,
            'fields'    => array(
                'order'   => array(
                    'title'   => __( 'Order', 'gamipress' ),
                    'type'    => 'text',
                    'default' => 0,
                ),
            ),
        ) );

	}

	/**
	 * Register achievements Post2Post relationships
	 */
	function register_achievement_relationships() {

		// Grab all our registered achievement types and loop through them
		$achievement_types = gamipress_get_achievement_types_slugs();
		if ( is_array( $achievement_types ) && ! empty( $achievement_types ) ) {
			foreach ( $achievement_types as $achievement_type ) {

				// Connect steps to each achievement type
				// Used to get an achievement's required steps (e.g. This badge requires these 3 steps)
				p2p_register_connection_type( array(
					'name'      => 'step-to-' . $achievement_type,
					'from'      => 'step',
					'to'        => $achievement_type,
					'admin_box' => false,
					'fields'    => array(
						'order'   => array(
							'title'   => __( 'Order', 'gamipress' ),
							'type'    => 'text',
							'default' => 0,
						),
					),
				) );

				// Connect each achievement type to a step
				// Used to get a step's required achievement (e.g. this step requires earning Level 1)
				p2p_register_connection_type( array(
					'name'      => $achievement_type . '-to-step',
					'from'      => $achievement_type,
					'to'        => 'step',
					'admin_box' => false,
					'fields'    => array(
						'order'   => array(
							'title'   => __( 'Order', 'gamipress' ),
							'type'    => 'text',
							'default' => 0,
						),
					),
				) );

				// Connect each achievement type to a points award
				// Used to get a points award's required achievement (e.g. this points award requires earning Level 1)
				p2p_register_connection_type( array(
					'name'      => $achievement_type . '-to-points-award',
					'from'      => $achievement_type,
					'to'        => 'points-award',
					'admin_box' => false,
					'fields'    => array(
						'order'   => array(
							'title'   => __( 'Order', 'gamipress' ),
							'type'    => 'text',
							'default' => 0,
						),
					),
				) );

			}
		}

	}

	/**
	 * Register custom WordPress image size(s)
	 */
	function register_image_sizes() {

		$achievement_image_size = gamipress_get_option( 'achievement_image_size', array( 'width' => 100, 'height' => 100 ) );

		add_image_size( 'gamipress-achievement', absint( $achievement_image_size['width'] ), absint( $achievement_image_size['height'] ) );

	}

	/**
	 * Activation hook for the plugin.
	 */
	function activate() {

		// Include our important bits
		$this->includes();

		require_once GAMIPRESS_DIR . 'includes/install.php';

		gamipress_install();
	}

	/**
	 * Deactivation hook for the plugin.
	 */
	function deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Internationalization
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function load_textdomain() {
		// Set filter for language directory
		$lang_dir = GAMIPRESS_DIR . '/languages/';
		$lang_dir = apply_filters( 'gamipress_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale', get_locale(), 'gamipress' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'gamipress', $locale );

		// Setup paths to current locale file
		$mofile_local   = $lang_dir . $mofile;
		$mofile_global  = WP_LANG_DIR . '/gamipress/' . $mofile;

		if( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/gamipress/ folder
			load_textdomain( 'gamipress', $mofile_global );
		} elseif( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/gamipress/languages/ folder
			load_textdomain( 'gamipress', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'gamipress', false, $lang_dir );
		}
	}

}

/**
 * The main function responsible for returning the one true GamiPress instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \GamiPress The one true GamiPress
 */
function GamiPress() {
	return GamiPress::instance();
}

GamiPress();