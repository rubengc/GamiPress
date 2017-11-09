<?php
/**
 * Shortcodes
 *
 * @package     GamiPress\Shortcodes
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// GamiPress Shortcodes Editor
require_once GAMIPRESS_DIR . 'includes/shortcodes/shortcodes-editor.php';

// GamiPress Shortcodes
require_once GAMIPRESS_DIR . 'includes/shortcodes/shortcode.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_achievement.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_achievements.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_logs.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_points.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_points_types.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_rank.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_ranks.php';

/**
 * Register a new GamiPress Shortcode
 *
 * @since  1.0.0
 *
 * @param  array  $args Shortcode Args.
 * @return object       Shortcode Object.
 */
function gamipress_register_shortcode( $shortcode, $args ) {
	GamiPress()->shortcodes[ $shortcode ] = new GamiPress_Shortcode( $shortcode, $args );
}

/**
 * Get all registered GamiPress shortcodes.
 *
 * @since  1.0.0
 *
 * @return array Registered GamiPress shortcodes.
 */
function gamipress_get_shortcodes() {

	return apply_filters( 'gamipress_shortcodes', GamiPress()->shortcodes );

}

/**
 * Add all shortcodes to the help page.
 *
 * @since 1.0.0
 */
function gamipress_help_support_page_shortcodes() {

	foreach ( gamipress_get_shortcodes() as $shortcode ) {
		gamipress_shortcode_help_render_help( $shortcode );
	}

}
add_action( 'gamipress_help_support_page_shortcodes', 'gamipress_help_support_page_shortcodes' );

/**
 * Render help section for a given shortcode.
 *
 * @since 1.0.0
 *
 * @param GamiPress_Shortcode $shortcode Shortcode object.
 */
function gamipress_shortcode_help_render_help( $shortcode ) {
	printf(
		'
		<hr/>
		<h3>%1$s &ndash; [%2$s]</h3>
		<p>%3$s</p>
		<ul style="margin:1em 2em; padding:1em;">
		<li><strong>%4$s</strong></li>
		%5$s
		</ul>
		<p>%6$s</p>
		',
		$shortcode->name,
		$shortcode->slug,
		$shortcode->description,
		__( 'Attributes:', 'gamipress' ),
		gamipress_shortcode_help_render_fields( $shortcode->fields ),
		gamipress_shortcode_help_render_example( $shortcode )
	);
}

/**
 * Render attributes portion of shordcode help section.
 *
 * @since  1.0.0
 *
 * @param  array 	$fields Shortcode fields.
 * @return string           HTML Markup.
 */
function gamipress_shortcode_help_render_fields( $fields ) {

	$output = '';

	if ( ! empty( $fields ) ) {
		foreach ( $fields as $field_id => $field ) {

			if( $field['type'] === 'title' ) {
				continue;
			}

			// Checkboxes without default means 'no' as default
			if( $field['type'] === 'checkbox' && empty( $field['default'] ) ) {
				$field['default'] = 'no';
			}

			$accepts = ! empty( $field['options'] ) ? sprintf( __( 'Accepts: %s', 'gamipress' ), '<code>' . implode( '</code>, <code>', array_keys( $field['options'] ) ) . '</code>' ) : '';
			$default = ! empty( $field['default'] ) ? sprintf( __( 'Default: %s', 'gamipress' ), '<code>' . $field['default'] . '</code>' ) : '';

			$output .= sprintf(
				'<li><strong>%1$s</strong> – %2$s <em>%3$s %4$s</em></li>',
				esc_attr( $field_id ),
				isset( $field['description'] ) ? $field['description'] : '',
				$accepts,
				$default
			);

		}
	}

	return $output;

}

/**
 * Render example shortcode usage for help section.
 *
 * @since  1.0.0
 *
 * @param  GamiPress_Shortcode $shortcode 	Shortcode object.
 * @return string            				HTML Markup.
 */
function gamipress_shortcode_help_render_example( $shortcode ) {

	$fields = @wp_list_pluck( $shortcode->fields, 'default' );
	$examples = array_map( 'gamipress_shortcode_help_attributes', array_keys( $fields ), array_values( $fields ) );
	$flattened_examples = implode( ' ', $examples );

	return sprintf( __( 'Example: %s', 'gamipress' ), "<code>[{$shortcode->slug} {$flattened_examples}]</code>" );

}

/**
 * Render attribute="value" for attributes in shortcode example.
 *
 * @since  1.0.0
 *
 * @param  string $key   Key name.
 * @param  string $value Value.
 * @return string        key="value".
 */
function gamipress_shortcode_help_attributes( $key, $value ) {

	switch( $key ) {
		case 'user_id':
			$value = get_current_user_id();
			break;
		case 'wpms':
			$value = is_multisite() ? 'yes' : 'no';
			break;
	}

	return "{$key}=\"$value\"";

}

/**
 * Remove multisite specific fields
 *
 * @since 1.2.0
 *
 * @param array $fields
 *
 * @return array
 */
function gamipress_shortcodes_remove_multisite_fields( $fields ) {

	if( ! is_multisite() ) {
		if( isset( $fields['wpms'] ) ) {
			unset( $fields['wpms'] );
		}
	}

	return $fields;
}
add_filter( 'gamipress_gamipress_achievements_shortcode_fields', 'gamipress_shortcodes_remove_multisite_fields' );
add_filter( 'gamipress_gamipress_points_shortcode_fields', 'gamipress_shortcodes_remove_multisite_fields' );
add_filter( 'gamipress_gamipress_points_types_shortcode_fields', 'gamipress_shortcodes_remove_multisite_fields' );
add_filter( 'gamipress_gamipress_ranks_shortcode_fields', 'gamipress_shortcodes_remove_multisite_fields' );
