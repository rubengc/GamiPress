<?php
/**
 * Admin
 *
 * @package GamiPress\Vimeo\Admin
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Extract the video ID from the given URL
 *
 * @since 1.0.0
 *
 * @param string $url
 *
 * @return string
 */
function gamipress_vimeo_get_video_id_from_url( $url ) {

    // Support for the following valid URLs
    // http://vimeo.com/{video_id}
    // http://player.vimeo.com/video/{video_id}
    // http://player.vimeo.com/video/{video_id}?title=0&byline=0&portrait=0
    // http://vimeo.com/channels/user/{video_id}
    preg_match("#(?:https?://)?(?:www.)?(?:player.)?vimeo.com/(?:[a-z]*/)*([0-9]{6,11})[?]?.*#", $url, $matches);

    $video_id = (isset( $matches[1] ) ? $matches[1] : $url );

    return $video_id;

}