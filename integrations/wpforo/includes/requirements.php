<?php
/**
 * Requirements
 *
 * @package GamiPress\wpForo\Requirements
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add the form field to the requirement object
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_wpforo_requirement_object( $requirement, $requirement_id ) {

    // Forums
    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wpforo_specific_forum_new_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_forum_new_topic'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_forum_like_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_forum_dislike_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_forum_vote_up_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_forum_vote_down_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_forum_answer_question' ) ) {
        // Forum ID
        $requirement['wpf_forum'] = get_post_meta( $requirement_id, '_gamipress_wpforo_forum', true );
    }

    // Topics
    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wpforo_specific_topic_new_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_topic_like_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_topic_dislike_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_topic_vote_up_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_topic_vote_down_post' ) ) {

        // Topic ID
        $requirement['wpf_topic'] = get_post_meta( $requirement_id, '_gamipress_wpforo_topic', true );
    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_wpforo_requirement_object', 10, 2 );

/**
 * Custom field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_wpforo_requirement_ui_fields( $requirement_id, $post_id ) {

    $forum_id = get_post_meta( $requirement_id, '_gamipress_wpforo_forum', true );
    $topic_id = get_post_meta( $requirement_id, '_gamipress_wpforo_topic', true );

    ?>
    <select class="wpf-forum wpf-forum-<?php echo $requirement_id; ?>">
        <?php if( ! empty( $forum_id ) ) :
            $achievement_post_title = gamipress_wpforo_get_forum_title( $forum_id ); ?>
            <option value="<?php esc_attr_e( $forum_id ); ?>" selected="selected"><?php echo $achievement_post_title; ?> (#<?php echo $forum_id; ?>)</option>
        <?php endif; ?>
    </select>

    <select class="wpf-topic wpf-topic-<?php echo $requirement_id; ?>">
        <?php if( ! empty( $topic_id ) ) :
            $achievement_post_title = gamipress_wpforo_get_topic_title( $topic_id ); ?>
            <option value="<?php esc_attr_e( $topic_id ); ?>" selected="selected"><?php echo $achievement_post_title; ?> (#<?php echo $topic_id; ?>)</option>
        <?php endif; ?>
    </select>
    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_wpforo_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the form on requirements UI
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_wpforo_ajax_update_requirement( $requirement_id, $requirement ) {

    // Forums
    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wpforo_specific_forum_new_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_forum_new_topic'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_forum_like_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_forum_dislike_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_forum_vote_up_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_forum_vote_down_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_forum_answer_question' ) ) {

        // Forum ID
        update_post_meta( $requirement_id, '_gamipress_wpforo_forum', $requirement['wpf_forum'] );
    }

    // Topics
    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wpforo_specific_topic_new_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_topic_like_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_topic_dislike_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_topic_vote_up_post'
            || $requirement['trigger_type'] === 'gamipress_wpforo_specific_topic_vote_down_post' ) ) {

        // Topic ID
        update_post_meta( $requirement_id, '_gamipress_wpforo_topic', $requirement['wpf_topic'] );
    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_wpforo_ajax_update_requirement', 10, 2 );