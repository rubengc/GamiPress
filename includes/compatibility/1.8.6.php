<?php
/**
 * GamiPress 1.8.6 compatibility functions
 *
 * @package     GamiPress\1.8.6
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.8.6
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get an array of email pattern tags
 *
 * @deprecated Use gamipress_get_pattern_tags() instead
 *
 * @since 1.3.0
 *
 * @return array The registered email pattern tags
 */
function gamipress_get_email_pattern_tags() {

    $pattern_tags = gamipress_get_pattern_tags();

    return apply_filters( 'gamipress_email_pattern_tags', $pattern_tags );

}

/**
 * Get a string with the desired email pattern tags html markup
 *
 * @deprecated Use gamipress_get_pattern_tags_html() instead
 *
 * @since 1.3.0
 *
 * @param string $email
 *
 * @return string Log pattern tags html markup
 */
function gamipress_get_email_pattern_tags_html( $email = '' ) {

    return gamipress_get_pattern_tags_html( $email );

}