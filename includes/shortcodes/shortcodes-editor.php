<?php
/**
 * GamiPress Shortcodes Editor Class
 *
 * @package     GamiPress\Shortcodes\Editor
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
*/
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Shortcodes_Editor {

    /**
     * @var         array $shortcodes All registered shortcodes
     * @since       1.4.7
     */
    public $shortcodes = array();

	/**
	 * @var         bool $button_rendered Flag to check if button has been rendered
	 * @since       1.4.7
	 */
	public $button_rendered = false;

	/**
	 * @var         bool $rendering_shortcodes Flag to check if currently is rendering shortcodes
	 * @since       1.4.7.1
	 */
	public $rendering_shortcodes = false;

	public function __construct() {

		$this->shortcodes = gamipress_get_shortcodes();

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 99 );
		add_action( 'media_buttons', array( $this, 'render_button'), 20 );
		add_action( 'admin_footer',  array( $this, 'render_modal' ) );

	}

	/**
	 * Enqueue and localize relevant admin scripts
	 *
	 * @since  1.0.0
	 */
	public function admin_scripts( $hook ) {

		if(
            in_array( $hook, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) // Add/edit post screen
			|| $hook === 'gamipress_page_gamipress_settings'		                            // GamiPress settings screen
			|| gamipress_starts_with( $hook, 'admin_page_edit_gamipress_' )		                // GamiPress custom tables add/edit screen
            || in_array( $hook, array( 'profile.php', 'user-edit.php' ) )                       // User edit screen (added to avoid issues with plugins that creates an editor on user profile)
		) {

			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Enqueue admin functions
            gamipress_enqueue_admin_functions_script();

            // Enqueue shortcodes editor
			wp_enqueue_script( 'gamipress-shortcodes-editor', GAMIPRESS_URL . 'assets/js/gamipress-shortcodes-editor' . $min . '.js', array( 'jquery', 'gamipress-admin-functions-js', 'gamipress-select2-js' ), GAMIPRESS_VER, true );

            wp_localize_script( 'gamipress-shortcodes-editor', 'gamipress_shortcodes_editor', array(
                'nonce'                     => gamipress_get_admin_nonce(),
                'id_placeholder'            => __( 'Select a Post', 'gamipress' ),
                'id_multiple_placeholder'   => __( 'Select Post(s)', 'gamipress' ),
                'user_placeholder'          => __( 'Select an User', 'gamipress' ),
                'post_type_placeholder'     => __( 'Default: All', 'gamipress' ),
                'rank_placeholder'          => __( 'Select a Rank', 'gamipress' ),
            ) );

		}

    }

	/**
	 * Render shortcode modal insert button
	 *
	 * @param string $editor_id
	 *
	 * @since 1.0.0
	 */
	public function render_button( $editor_id ) {

		// Prevent to render button inside shortcodes editor window
		if( $this->rendering_shortcodes ) { return; }

		$this->button_rendered = true;

		echo '<a id="insert_gamipress_shortcodes" href="#TB_inline?width=660&height=800&inlineId=select_gamipress_shortcode" class="thickbox button gamipress_media_link" data-width="800">'
                . '<span class="wp-media-buttons-icon dashicons dashicons-gamipress"></span> ' . 'GamiPress'
            . '</a>';
	}

	/**
	 * Render shortcode modal content
	 *
	 * @since 1.0.0
	 */
	public function render_modal() {

		// Return early if button hasn't been rendered
		if( ! $this->button_rendered ) { return; }

		$this->rendering_shortcodes = true;

		?>

		<div id="select_gamipress_shortcode" style="display:none;">
			<div class="wrap">
				<h3><?php _e( 'GamiPress shortcode', 'gamipress' ); ?></h3>
				<p><?php printf( __( 'See the %s page for more information', 'gamipress' ), '<a target="_blank" href="' . admin_url( 'admin.php?page=gamipress_help_support' ) . '">' . __( 'Help/Support', 'gamipress' ) . '</a>' ); ?></p>
				<div class="alignleft">
					<select id="select_shortcode"><?php echo $this->get_shortcode_selector(); ?></select>
				</div>
				<div class="alignright">
					<a id="gamipress_insert" class="button-primary" href="#" style="color:#fff;"><?php esc_attr_e( 'Insert Shortcode', 'gamipress' ); ?></a>
					<a id="gamipress_cancel" class="button-secondary" href="#"><?php esc_attr_e( 'Cancel', 'gamipress' ); ?></a>
				</div>
				<div id="shortcode_options" class="alignleft clear">
					<?php $this->get_shortcode_sections(); ?>
				</div>
			</div>
		</div>

		<?php

		$this->rendering_shortcodes = false;
	}

	/**
	 * Generate the shortcode selector options
	 *
	 * @since   1.0.0
	 * @updated 1.7.6 Added support for shortcode groups
	 *
	 * @return string
	 */
	private function get_shortcode_selector() {

	    // Setup vars
		$output = '';
        $current_group = '';
		$groups = gamipress_get_shortcodes_groups();

		$shortcodes = array();

		// Make a first loop to reorder all shortcodes by group
        foreach( $this->shortcodes as $shortcode ) {

            $group = $shortcode->group;

            // If group is not registered fallback to others
            if( ! isset( $groups[$group] ) ) $group = 'others';

            // Initialize shortcode group array
            if( ! isset( $shortcodes[$group] ) ) $shortcodes[$group] = array();

            // Add the shortcode to the group
            $shortcodes[$group][] = $shortcode;
        }

        if( isset( $shortcodes['others'] ) ) {
            // Move others to the end
            $others = $shortcodes['others'];
            unset( $shortcodes['others'] );
            $shortcodes['others'] = $others;
        }

		foreach( $shortcodes as $group => $group_shortcodes ) {

		    // Skip empty groups
		    if( ! is_array( $group_shortcodes ) ) continue;

            if( $current_group !== $group ) {

                // Close the previous group
                if( $current_group !== '' ) $output .= '</optgroup>';

                // Render the shortcode group
                $output .= sprintf( '<optgroup label="%1$s">', $groups[$group] );

                // Set the current group for next loop
                $current_group = $group;

            }

            // Render the group shortcodes as options
            foreach( $group_shortcodes as $shortcode )
                $output .= sprintf( '<option value="%1$s">%2$s</option>', $shortcode->slug, $shortcode->name );

		}

		return $output;
	}

	/**
	 * Render all shortcodes sections
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_shortcode_sections() {
		foreach( $this->shortcodes as $shortcode ) {
			$this->get_shortcode_section( $shortcode );
		}
	}

	/**
	 * Render a shortcode section
	 *
	 * @since 1.0.0
	 *
	 * @param GamiPress_Shortcode $shortcode
	 */
	private function get_shortcode_section( $shortcode ) {
		?>
		<div class="shortcode-section alignleft" id="<?php echo $shortcode->slug; ?>_wrapper">
			<p><strong>[<?php echo $shortcode->slug; ?>]</strong> - <?php echo $shortcode->description; ?></p>

			<?php $shortcode->show_form(); ?>
		</div>
		<?php
	}
}

/**
 * Initialize the shortcodes editor.
 *
 * @since 1.0.0
 */
function gamipress_shortcodes_add_editor_button() {

	global $pagenow;

	// Prevent render on customizer and widgets
	if( $pagenow === 'customize.php' || $pagenow === 'widgets.php' ) {
		return;
	}

	if( (bool) gamipress_get_option( 'disable_shortcodes_editor', false ) ) {
		return;
	}

	new GamiPress_Shortcodes_Editor();
}
add_action( 'admin_init', 'gamipress_shortcodes_add_editor_button' );
