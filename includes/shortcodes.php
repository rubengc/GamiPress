<?php
/**
 * Shortcodes
 *
 * @package     GamiPress\Shortcodes
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
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
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_last_achievements_earned.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_earnings.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_logs.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_user_points.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_site_points.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_points.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_points_types.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_rank.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_ranks.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_user_rank.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_email_settings.php';
// Inline Shortcodes
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_inline_achievement.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_inline_last_achievements_earned.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_inline_rank.php';
require_once GAMIPRESS_DIR . 'includes/shortcodes/gamipress_inline_user_rank.php';

/**
 * Register a new GamiPress Shortcode
 *
 * @since  1.0.0
 *
 * @param  array  $args Shortcode Args.
 *
 * @return object       Shortcode Object.
 */
function gamipress_register_shortcode( $shortcode, $args ) {

	GamiPress()->shortcodes[ $shortcode ] = new GamiPress_Shortcode( $shortcode, $args );

	return GamiPress()->shortcodes[ $shortcode ];

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
 * Get all registered shortcodes groups.
 *
 * @since  1.7.6
 *
 * @return array Registered shortcodes groups.
 */
function gamipress_get_shortcodes_groups() {

    return apply_filters( 'gamipress_shortcodes_groups', array(
        'gamipress' => 'GamiPress',
        'others'    => __( 'Others', 'gamipress' ),
    ) );

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
		'<hr/>
		<strong style="font-size: 14px;">%1$s &ndash; [%2$s]</strong> <a href="#" onclick="jQuery(this).next().slideToggle();return false;">%3$s</a>
		<div style="display: none;">
            <p>%4$s</p>
            <ul>
                <li><strong>%5$s</strong></li>
                %6$s
            </ul>
            <p>%7$s</p>
		</div>',
		$shortcode->name,
		$shortcode->slug,
        __( 'More details', 'gamipress' ),
		$shortcode->description,
		__( 'Attributes:', 'gamipress' ),
		gamipress_shortcode_help_render_fields( $shortcode->fields ),
		gamipress_shortcode_help_render_example( $shortcode )
	);
}

/**
 * Render attributes portion of shortcode help section.
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

			if( isset( $field['default'] ) && is_array( $field['default'] ) ) {
                $field['default'] = implode( ',', $field['default'] );
            }

			$accepts = isset( $field['options'] ) && ! empty( $field['options'] ) ? sprintf( __( 'Accepts: %s', 'gamipress' ), '<code>' . implode( '</code>, <code>', array_keys( $field['options'] ) ) . '</code>' ) : '';
			$default = isset( $field['default'] ) && ! empty( $field['default'] ) ? sprintf( __( 'Default: %s', 'gamipress' ), '<code>' . $field['default'] . '</code>' ) : '';

			// Setup the description (allowing provide it from description, desc and shortcode_desc
            $description = '';
            $description = isset( $field['description'] )       ? $field['description']     : $description;
            $description = isset( $field['desc'] )              ? $field['desc']            : $description;
            $description = isset( $field['shortcode_desc'] )    ? $field['shortcode_desc']  : $description;

			$output .= sprintf(
				'<li><strong>%1$s</strong> – %2$s <em>%3$s %4$s</em></li>',
				esc_attr( $field_id ),
                $description,
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

	if( is_array( $value ) )
        $value = implode( ',', $value );

	return "{$key}=\"$value\"";

}

/**
 * Remove multisite specific fields
 *
 * @since 	1.2.0
 * @updated 1.4.9 wpms field is removed also if GamiPress is network wide active
 *
 * @param array $fields
 *
 * @return array
 */
function gamipress_shortcodes_remove_multisite_fields( $fields ) {

	if( ! is_multisite() || gamipress_is_network_wide_active() ) {
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

/**
 * Function to report form error just if logged in user has permissions to manage GamiPress
 *
 * @since 	1.5.9
 *
 * @param string $error_message
 * @param string $shortcode
 *
 * @return string
 */
function gamipress_shortcode_error( $error_message, $shortcode ) {

    global $gamipress_current_widget;

    // Setup label and shortcode label to customize message for shortcodes, widgets or blocks
    switch( gamipress_get_renderer() ) {
        case 'widget':
            $label = __( 'Widget:', 'gamipress' );
            $shortcode_label = $gamipress_current_widget->name;
            break;
        case 'block':
            $label = __( 'Block:', 'gamipress' );
            $shortcode_label = GamiPress()->shortcodes[$shortcode]->name;
            break;
        case 'shortcode':
        default:
            $label = __( 'Shortcode:', 'gamipress' );
            $shortcode_label = '&#91;' . $shortcode . '&#93;';
            break;
    }

    if( current_user_can( gamipress_get_manager_capability() ) ) {
        // Notify to admins about the error
        return '<div class="gamipress-shortcode-error">'
                . '<div class="gamipress-shortcode-error-content">'
                    . $error_message
                . '</div>'
                . '<div class="gamipress-shortcode-error-shortcode">'
                    . $label . ' ' . $shortcode_label
                . '</div>'
                . '<div class="gamipress-shortcode-error-reminder">'
                    . __( 'Message visible only to administrators.', 'gamipress' )
                . '</div>'
            . '</div>';
    } else {
        // Do not output anything for non admins
        return '';
    }

}

/**
 * Function to get the actual renderer
 *
 * @since 	2.0.0
 *
 * @return string shortcode | block | widget
 */
function gamipress_get_renderer() {

    global $gamipress_renderer, $wp;

    // Support for block Rest API rendering
    if( isset( $wp->query_vars[ 'rest_route' ] )
        && ! empty( $wp->query_vars[ 'rest_route' ] )
        && strpos( $wp->query_vars[ 'rest_route' ], 'block-renderer' ) !== false ) {
        $gamipress_renderer = 'block';
    }

    if( ! $gamipress_renderer ) {
        $gamipress_renderer = 'shortcode';
    }

    return $gamipress_renderer;

}

/**
 * Function to set the current renderer
 *
 * @since 	1.5.9
 *
 * @param string $renderer
 */
function gamipress_set_renderer( $renderer ) {

    global $gamipress_renderer;

    $gamipress_renderer = $renderer;

}

/**
 * Helper function to build an array of shortcode attributes from the values given
 *
 * @since 	1.7.0
 *
 * @param string    $shortcode
 * @param array     $values
 *
 * @return array
 */
function gamipress_build_shortcode_atts( $shortcode, $values ) {

    $atts = array();

    // Bail if shortcode is not registered
    if( ! isset( GamiPress()->shortcodes[$shortcode] ) ) return $atts;

    // Loop all shortcode fields to pass their value
    foreach( GamiPress()->shortcodes[$shortcode]->fields as $field_id => $field ) {

        // If attribute exists on array of values given then process it
        if( isset( $values[$field_id] ) ) {
            $value = $values[$field_id];

            // If is a checkbox field, then turn value into yes or no
            if( $field['type'] === 'checkbox' ) {
                $value = ( $values[$field_id] === 'on' ? 'yes' : 'no' );
            }

            // If value is an array, setup a comma separated list of it's values
            if( is_array( $value ) ) {
                $value = implode( ',', $value );
            }

            $atts[$field_id] = $value;
        }
    }

    return $atts;


}
