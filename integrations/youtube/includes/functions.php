<?php
/**
 * Admin
 *
 * @package GamiPress\Youtube\Admin
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
function gamipress_youtube_get_video_id_from_url( $url ) {

    // Support for the following valid URLs
    // youtube.com/v/{video_id}
    // youtube.com/vi/{video_id}
    // youtube.com/?v={video_id}
    // youtube.com/?vi={video_id}
    // youtube.com/watch?v={video_id}
    // youtube.com/watch?vi={video_id}
    // youtu.be/{video_id}
    // youtube.com/embed/{video_id}
    // http://youtube.com/v/{video_id}
    // http://www.youtube.com/v/{video_id}
    // https://www.youtube.com/v/{video_id}
    // youtube.com/watch?v={video_id}&wtv=wtv
    // http://www.youtube.com/watch?dev=inprogress&v={video_id}&feature=related
    // https://m.youtube.com/watch?v={video_id}
    preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);

    $video_id = (isset( $matches[1] ) ? $matches[1] : $url );

    return $video_id;

}