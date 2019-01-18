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

		global $post_type;

		if(
			( in_array( $hook, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) ) && post_type_supports( $post_type, 'editor' ) 	// Add/edit views of post types that supports editor feature
			|| $hook === 'gamipress_page_gamipress_settings'																						// GamiPress settings screen
		) {

			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Enqueue GamiPress Select2
			wp_enqueue_script( 'gamipress-select2-js' );
			wp_enqueue_style( 'gamipress-select2-css' );

            // Setup an array of post type labels to use on post selector field
            $post_types = get_post_types( array(), 'objects' );
            $post_type_labels = array();

            foreach( $post_types as $key => $obj ) {
                $post_type_labels[$key] = $obj->labels->singular_name;
            }

            // Localize admin functions script
            wp_localize_script( 'gamipress-admin-functions-js', 'gamipress_admin_functions', array(
                'post_type_labels' => $post_type_labels
            ) );

			wp_enqueue_script( 'gamipress-admin-functions-js' );

			wp_enqueue_script( 'gamipress-shortcodes-editor', GAMIPRESS_URL . 'assets/js/gamipress-shortcodes-editor' . $min . '.js', array( 'jquery', 'gamipress-admin-functions-js', 'gamipress-select2-js' ), GAMIPRESS_VER, true );

			wp_localize_script( 'gamipress-shortcodes-editor', 'gamipress_shortcodes_editor', array(
				'id_placeholder'          => __( 'Select a Post', 'gamipress' ),
				'id_multiple_placeholder' => __( 'Select Post(s)', 'gamipress' ),
				'user_placeholder'        => __( 'Select an User', 'gamipress' ),
				'post_type_placeholder'   => __( 'Default: All', 'gamipress' ),
				'rank_placeholder'        => __( 'Select a Rank', 'gamipress' ),
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
                . '<span class="wp-media-buttons-icon dashicons dashicons-gamipress"></span> ' . __( 'GamiPress Shortcode', 'gamipress' )
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
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_shortcode_selector() {
		$output = '';

		foreach( $this->shortcodes as $shortcode ) {
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
