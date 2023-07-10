<?php
/**
 * Requirements
 *
 * @package GamiPress\Youtube\Requirements
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add custom fields to the requirement object
 *
 * @since 1.0.0
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_youtube_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] ) && $requirement['trigger_type'] === 'gamipress_youtube_watch_specific_video' ) {

        $requirement['youtube_video_id'] = get_post_meta( $requirement_id, '_gamipress_youtube_video_id', true );

    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_youtube_requirement_object', 10, 2 );

/**
 * Custom field field on requirements UI
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_youtube_requirement_ui_fields( $requirement_id, $post_id ) {

    $video_id = get_post_meta( $requirement_id, '_gamipress_youtube_video_id', true ); ?>

    <span class="youtube-video-id"><input type="text" value="<?php echo $video_id; ?>" placeholder="Youtube video ID or URL" /></span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_youtube_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the custom field on requirements UI
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_youtube_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] ) && $requirement['trigger_type'] === 'gamipress_youtube_watch_specific_video' ) {

        // Save the custom field
        update_post_meta( $requirement_id, '_gamipress_youtube_video_id', gamipress_youtube_get_video_id_from_url( $requirement['youtube_video_id'] ) );

    }

}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_youtube_ajax_update_requirement', 10, 2 );