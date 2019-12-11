<?php
/**
 * HTML Functions
 *
 * @package     GamiPress\HTML_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.7.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Turn an array into a HTML list of hidden inputs
 *
 * @since 1.6.5
 *
 * @param array $array      Array of elements to render
 * @param array $excluded   Optional, elements excluded for being rendered
 *
 * @return string
 */
function gamipress_array_as_hidden_inputs( $array, $excluded = array() ) {

    $html = '';

    foreach( $array as $key => $value ) {
        // Skip excluded keys
        if( in_array( $key, $excluded ) ) continue;

        // Sanitize value
        $value = is_array( $value ) ? implode(',', $value ) : $value;

        $html .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '">';
    }

    return $html;
}

/**
 * Generates the required HTML with the dashicon provided
 *
 * @since 1.7.1
 *
 * @param string $dashicon      Dashicon class
 * @param string $tag           Optional, tag used (recommended i or span)
 *
 * @return string
 */
function gamipress_dashicon( $dashicon = 'gamipress', $tag = 'i' ) {

    return '<' . $tag . ' class="dashicons dashicons-' . $dashicon . '"></' . $tag . '>';

}