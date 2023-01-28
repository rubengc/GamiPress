<?php
/**
 * Admin Logs Settings
 *
 * @package     GamiPress\Admin\Settings\Logs
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.3.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Logs Settings meta boxes
 *
 * @since   1.0.0
 * @updated 1.6.5 Added log_all_events setting and only_log_events_with_listeners setting has been removed
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_settings_logs_meta_boxes( $meta_boxes ) {

    $meta_boxes['logs-patterns-settings'] = array(
        'title' => gamipress_dashicon( 'editor-alignleft' ) . __( 'Logs', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_logs_patterns_settings_fields', array(
            'log_patterns_title' => array(
                'name' => __( 'Logs Patterns', 'gamipress' ),
                'description' => __( 'From this settings you can modify the default pattern for upcoming log entries of each category.', 'gamipress' ),
                'type' => 'title',
            ),
            'trigger_log_pattern' => array(
                'name' => __( 'Activity trigger', 'gamipress' ),
                'description' => __( 'Used to register user activity triggered. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{user}', '{trigger_type}', '{trigger_type_key}', '{count}', '{site_title}' ) ),
                'type' => 'text',
                'default' => __( '{user} triggered {trigger_type} (x{count})', 'gamipress' ),
            ),
            'points_earned_log_pattern' => array(
                'name' => __( 'Points earned', 'gamipress' ),
                'description' => __( 'Used when user earns points. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{user}', '{points}', '{points_type}', '{total_points}', '{site_title}' ) ),
                'type' => 'text',
                'default' => __( '{user} earned {points} {points_type} for a new total of {total_points} {points_type}', 'gamipress' ),
            ),
            'points_deducted_log_pattern' => array(
                'name' => __( 'Points deducted', 'gamipress' ),
                'description' => __( 'Used when user gets a deduction of points. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{user}', '{points}', '{points_type}', '{total_points}', '{site_title}' ), 'deduct' ),
                'type' => 'text',
                'default' => __( '{user} deducted {points} {points_type} for a new total of {total_points} {points_type}', 'gamipress' ),
            ),
            'points_expended_log_pattern' => array(
                'name' => __( 'Points expended', 'gamipress' ),
                'description' => __( 'Used when user expend an amount of points. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{user}', '{points}', '{points_type}', '{total_points}', '{site_title}' ), 'expend' ),
                'type' => 'text',
                'default' => __( '{user} expended {points} {points_type} for a new total of {total_points} {points_type}', 'gamipress' ),
            ),
            'requirement_complete_log_pattern' => array(
                'name' => __( 'Points award/step complete', 'gamipress' ),
                'description' => __( 'Used when user completes a points award or step. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{user}', '{achievement}', '{achievement_type}', '{site_title}' ) ),
                'type' => 'text',
                'default' => __( '{user} completed the {achievement_type} {achievement}', 'gamipress' ),
            ),
            'achievement_earned_log_pattern' => array(
                'name' => __( 'Achievement earned', 'gamipress' ),
                'description' => __( 'Used when user earns an achievement. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{user}', '{achievement}', '{achievement_type}', '{site_title}' ) ),
                'type' => 'text',
                'default' => __( '{user} unlocked the {achievement} {achievement_type}', 'gamipress' ),
            ),
            'rank_earned_log_pattern' => array(
                'name' => __( 'Rank earned', 'gamipress' ),
                'description' => __( 'Used when user ranks to a new rank. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{user}', '{rank}', '{rank_type}', '{site_title}' ) ),
                'type' => 'text',
                'default' => __( '{user} ranked to {rank_type} {rank}', 'gamipress' ),
            ),
            'points_awarded_log_pattern' => array(
                'name' => __( 'Points awarded', 'gamipress' ),
                'description' => __( 'Used when an admin awards a user with points. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{admin}', '{user}', '{points}', '{points_type}', '{total_points}', '{site_title}' ) ),
                'type' => 'text',
                'default' => __( '{admin} awarded {user} {points} {points_type} for a new total of {total_points} {points_type}', 'gamipress' ),
            ),
            'points_revoked_log_pattern' => array(
                'name' => __( 'Points revoked', 'gamipress' ),
                'description' => __( 'Used when an admin revokes points of a user. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{admin}', '{user}', '{points}', '{points_type}', '{total_points}', '{site_title}' ) ),
                'type' => 'text',
                'default' => __( '{admin} revoked {user} {points} {points_type} for a new total of {total_points} {points_type}', 'gamipress' ),
            ),
            'achievement_awarded_log_pattern' => array(
                'name' => __( 'Achievement awarded', 'gamipress' ),
                'description' => __( 'Used when an admin awards a user with an achievement. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{admin}', '{user}', '{achievement}', '{achievement_type}', '{site_title}' ) ),
                'type' => 'text',
                'default' => __( '{admin} awarded {user} with the the {achievement} {achievement_type}', 'gamipress' ),
            ),
            'rank_awarded_log_pattern' => array(
                'name' => __( 'Rank awarded', 'gamipress' ),
                'description' => __( 'Used when an admin ranks a user to a new rank. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html( array( '{admin}', '{user}', '{rank}', '{rank_type}', '{site_title}' ) ),
                'type' => 'text',
                'default' => __( '{admin} ranked {user} to {rank_type} {rank}', 'gamipress' ),
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_logs_meta_boxes', 'gamipress_settings_logs_meta_boxes' );