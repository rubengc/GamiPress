<?php
/**
 * Requirements
 *
 * @package GamiPress\Presto_Player\Requirements
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add the custom field to the requirement object
 *
 * @since 1.0.0
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_presto_player_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_presto_player_watch_video_min_percent'
            || $requirement['trigger_type'] === 'gamipress_presto_player_watch_specific_video_min_percent' ) ) {

        // Percent
        $requirement['presto_player_percent'] = absint( get_post_meta( $requirement_id, '_gamipress_presto_player_percent', true ) );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_presto_player_watch_video_between_percent'
            || $requirement['trigger_type'] === 'gamipress_presto_player_watch_specific_video_between_percent' ) ) {

        // Min and max percents
        $requirement['presto_player_min_percent'] = absint( get_post_meta( $requirement_id, '_gamipress_presto_player_min_percent', true ) );
        $requirement['presto_player_max_percent'] = absint( get_post_meta( $requirement_id, '_gamipress_presto_player_max_percent', true ) );
    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_presto_player_requirement_object', 10, 2 );

/**
 * Custom field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_presto_player_requirement_ui_fields( $requirement_id, $post_id ) {

    $percent = absint( get_post_meta( $requirement_id, '_gamipress_presto_player_percent', true ) );
    $min_percent = get_post_meta( $requirement_id, '_gamipress_presto_player_min_percent', true );
    $max_percent = get_post_meta( $requirement_id, '_gamipress_presto_player_max_percent', true );
    ?>

    <span class="presto-player-percent"><?php echo __( 'Percent:', 'gamipress' ); ?> <input type="number" min="0" max="100" step="1" value="<?php echo $percent; ?>" placeholder="<?php echo __( 'Percent', 'gamipress' ); ?>" />%</span>
    <span class="presto-player-min-percent"><input type="text" value="<?php echo ( ! empty( $min_percent ) ? absint( $min_percent ) : '' ); ?>" min="0" max="100" step="1" placeholder="Min" />% -</span>
    <span class="presto-player-max-percent"><input type="text" value="<?php echo ( ! empty( $max_percent ) ? absint( $max_percent ) : '' ); ?>" min="0" max="100" step="1" placeholder="Max" />%</span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_presto_player_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the custom field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_presto_player_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_presto_player_watch_video_min_percent'
            || $requirement['trigger_type'] === 'gamipress_presto_player_watch_specific_video_min_percent' ) ) {

        // Percent
        update_post_meta( $requirement_id, '_gamipress_presto_player_percent', $requirement['presto_player_percent'] );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_presto_player_watch_video_between_percent'
            || $requirement['trigger_type'] === 'gamipress_presto_player_watch_specific_video_between_percent' ) ) {

        // Min and max percent
        update_post_meta( $requirement_id, '_gamipress_presto_player_min_percent', $requirement['presto_player_min_percent'] );
        update_post_meta( $requirement_id, '_gamipress_presto_player_max_percent', $requirement['presto_player_max_percent'] );
    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_presto_player_ajax_update_requirement', 10, 2 );