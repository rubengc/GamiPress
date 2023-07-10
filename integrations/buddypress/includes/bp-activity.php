<?php
/**
 * BuddyPress Activity
 *
 * @package GamiPress\BuddyPress\BuddyPress_Activity
 * @since 1.0.1
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register custom meta boxes
 *
 * @since   1.0.1
 * @updated 1.2.6
 */
function gamipress_bp_activity_meta_boxes() {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_bp_';

    if ( ! gamipress_bp_is_active( 'activity' ) ) {
        return;
    }


    // Points Type
    gamipress_add_meta_box(
        'bp-activity-points-type-data',
        __( 'BuddyPress Member Activity', 'gamipress' ),
        array( 'points-type' ),
        array(
            $prefix . 'create_points_award_activity' => array(
                'name' => __( 'Awards activity entries', 'gamipress' ),
                'desc' => __( 'Create an activity entry on user\'s profile when they get awarded by points award of this type.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
            $prefix . 'create_points_deduct_activity' => array(
                'name' => __( 'Deducts activity entries', 'gamipress' ),
                'desc' => __( 'Create an activity entry on user\'s profile when they get deducted by a points deduct of this type.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
        )
    );

    // Achievement Type
    gamipress_add_meta_box(
        'bp-activity-achievement-type-data',
        __( 'BuddyPress Member Activity', 'gamipress' ),
        array( 'achievement-type' ),
        array(
            $prefix . 'create_achievement_activity' => array(
                'name' => __( 'Achievement activity entries', 'gamipress' ),
                'desc' => __( 'Create an activity entry on user\'s profile when they earn an achievement of this type.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
            $prefix . 'create_step_activity' => array(
                'name' => __( 'Step activity entries', 'gamipress' ),
                'desc' => __( 'Create an activity entry on user\'s profile when they complete a step of an achievement of this type.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
        )
    );

    // Rank Type
    gamipress_add_meta_box(
        'bp-activity-rank-type-data',
        __( 'BuddyPress Member Activity', 'gamipress' ),
        array( 'rank-type' ),
        array(
            $prefix . 'create_rank_activity' => array(
                'name' => __( 'Rank activity entries', 'gamipress' ),
                'desc' => __( 'Create an activity entry on user\'s profile when they reach a new rank of this type.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
            $prefix . 'create_rank_requirement_activity' => array(
                'name' => __( 'Rank requirement activity entries', 'gamipress' ),
                'desc' => __( 'Create an activity entry on user\'s profile when they complete a rank requirement of a rank of this type.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
        )
    );

}
add_action( 'cmb2_admin_init', 'gamipress_bp_activity_meta_boxes' );

/**
 * Create BuddyPress Activity when a user earns an achievement.
 *
 * @since   1.0.1
 * @updated 1.2.6 Make function more flexible, now logic is handled by 'gamipress_bp_activity_details' filter
 *
 * @param int       $user_id
 * @param int       $post_id
 * @param string    $trigger_type
 * @param int       $site_id
 * @param array     $args
 */
function gamipress_award_achievement_bp_activity( $user_id, $post_id, $trigger_type, $site_id, $args ) {

    if ( ! gamipress_bp_is_active( 'activity' ) ) {
        return;
    }

    $activity = array();

    $activity = apply_filters( 'gamipress_bp_activity_details', $activity, $user_id, $post_id, $trigger_type, $site_id, $args );

    // Insert the activity
    if( ! empty( $activity ) )
        bp_activity_add( $activity );

}
add_action( 'gamipress_award_achievement', 'gamipress_award_achievement_bp_activity', 10, 5 );

/**
 * Create BuddyPress Activity when a user earns an achievement.
 *
 * @since   1.3.2
 *
 * @param int       $user_id
 * @param WP_Post   $new_rank
 * @param WP_Post   $old_rank
 * @param int       $admin_id
 * @param int       $achievement_id
 */
function gamipress_update_user_rank_bp_activity( $user_id, $new_rank, $old_rank, $admin_id, $achievement_id ) {

    if ( ! gamipress_bp_is_active( 'activity' ) ) {
        return;
    }

    $activity = array();

    $activity = apply_filters( 'gamipress_bp_activity_details', $activity, $user_id, $new_rank->ID, '', get_current_blog_id(), array() );

    // Insert the activity
    if( ! empty( $activity ) )
        bp_activity_add( $activity );

}
add_action( 'gamipress_update_user_rank', 'gamipress_update_user_rank_bp_activity', 10, 5 );

/**
 * Points award and deduct activity entry details
 *
 * @since   1.2.6
 *
 * @param array     $activity
 * @param int       $user_id
 * @param int       $post_id
 * @param string    $trigger_type
 * @param int       $site_id
 * @param array     $args
 *
 * @return array
 */
function gamipress_bp_points_activity_details( $activity, $user_id, $post_id, $trigger_type, $site_id, $args ) {

    // Setup vars
    $prefix             = '_gamipress_bp_';
    $post               = gamipress_get_post( $post_id );
    $post_type          = $post->post_type;
    $points_types       = gamipress_get_points_types();

    // Bail if isn't a points award or deduct
    if( ! in_array( $post_type, array( 'points-award', 'points-deduct' ) ) )
        return $activity;

    $points_type = gamipress_get_points_award_points_type( $post_id );

    // Bail if not correctly assigned to a points type
    if( ! $points_type )
        return $activity;

    // Bail if create activity option isn't enabled
    if( ! (bool) gamipress_get_post_meta( $points_type->ID, $prefix . 'create_' . str_replace( '-', '_', $post_type ) . '_activity', true ) )
        return $activity;

    $points = absint( gamipress_get_post_meta( $post_id, '_gamipress_points' ) );
    $formatted_points = gamipress_format_points( $points, $points_type->post_name );

    // Setup our entry content
    $content = '<div id="gamipress-' . $post_type . '-' . $post_id . '" class="gamipress-' . $post_type . ' user-has-earned">';
    $content .= '<div class="gamipress-points-type-image">' . gamipress_get_points_type_thumbnail( $points_type->post_name ) . '</div>';
    $content .= '<div class="gamipress-points-type-description">' . $post->post_title . '</div>';
    $content .= '</div>';

    // Bypass checking our activity items from moderation, as we know we are legit.
    add_filter( 'bp_bypass_check_for_moderation', '__return_true' );

    $action_pattern = __( '%1$s earned %2$s', 'gamipress' );

    if( $post_type === 'points-deduct' ) {
        $action_pattern = __( '%1$s lost %2$s', 'gamipress' );
    }


    // Setup the activity details
    $activity = array(
        'action'       => sprintf( $action_pattern, bp_core_get_userlink( $user_id ), $formatted_points ),
        'content'      => $content,
        'component'    => 'gamipress',
        'type'         => 'activity_update',
        'primary_link' => get_permalink( $post_id ),
        'user_id'      => $user_id,
        'item_id'      => $post_id,
    );

    return $activity;

}
add_filter( 'gamipress_bp_activity_details', 'gamipress_bp_points_activity_details', 10, 6 );

/**
 * Achievement activity entry details
 *
 * @since   1.2.6
 *
 * @param array     $activity
 * @param int       $user_id
 * @param int       $post_id
 * @param string    $trigger_type
 * @param int       $site_id
 * @param array     $args
 *
 * @return array
 */
function gamipress_bp_achievement_activity_details( $activity, $user_id, $post_id, $trigger_type, $site_id, $args ) {

    // Setup vars
    $prefix             = '_gamipress_bp_';
    $post               = gamipress_get_post( $post_id );
    $post_type          = $post->post_type;
    $achievement_types  = gamipress_get_achievement_types();

    // Bail if isn't an achievement
    if( ! in_array( $post_type, gamipress_get_achievement_types_slugs() ) )
        return $activity;

    // Bail if create activity option isn't enabled
    if( ! (bool) gamipress_get_post_meta( $achievement_types[$post_type]['ID'], $prefix . 'create_achievement_activity', true ) )
        return $activity;

    // Grab the singular name for our achievement type
    $singular_name = strtolower( $achievement_types[$post_type]['singular_name'] );

    // Setup our entry content
    $content = '<div id="gamipress-achievement-' . $post_id . '" class="gamipress-achievement user-has-earned">';
    $content .= '<div class="gamipress-achievement-image"><a href="'. get_permalink( $post_id ) . '">' . gamipress_get_achievement_post_thumbnail( $post_id ) . '</a></div>';
    $content .= '<div class="gamipress-achievement-description">' . gamipress_bp_activity_get_post_excerpt( $post_id ) . '</div>';
    $content .= '</div>';

    // Bypass checking our activity items from moderation, as we know we are legit.
    add_filter( 'bp_bypass_check_for_moderation', '__return_true' );

    // Setup the activity details
    $activity = array(
        'action'       => sprintf( __( '%1$s earned the %2$s %3$s', 'gamipress' ), bp_core_get_userlink( $user_id ), $singular_name, '<a href="' . get_permalink( $post_id ) . '">' . $post->post_title . '</a>' ),
        'content'      => $content,
        'component'    => 'gamipress',
        'type'         => 'activity_update',
        'primary_link' => get_permalink( $post_id ),
        'user_id'      => $user_id,
        'item_id'      => $post_id,
    );

    return $activity;

}
add_filter( 'gamipress_bp_activity_details', 'gamipress_bp_achievement_activity_details', 10, 6 );

/**
 * Step activity entry details
 *
 * @since   1.2.6
 *
 * @param array     $activity
 * @param int       $user_id
 * @param int       $post_id
 * @param string    $trigger_type
 * @param int       $site_id
 * @param array     $args
 *
 * @return array
 */
function gamipress_bp_step_activity_details( $activity, $user_id, $post_id, $trigger_type, $site_id, $args ) {

    // Setup vars
    $prefix             = '_gamipress_bp_';
    $post               = gamipress_get_post( $post_id );
    $post_type          = $post->post_type;
    $achievement_types  = gamipress_get_achievement_types();

    // Bail if isn't a step
    if( $post_type !== 'step' )
        return $activity;

    $achievement = gamipress_get_step_achievement( $post_id );

    // Bail if create activity option isn't enabled
    if( ! (bool) gamipress_get_post_meta( $achievement_types[$achievement->post_type]['ID'], $prefix . 'create_step_activity', true ) )
        return $activity;

    // Grab the singular name for our achievement type
    $singular_name = strtolower( $achievement_types[$achievement->post_type]['singular_name'] );

    // Setup our entry content
    $content = '<div id="gamipress-achievement-' . $achievement->ID . '" class="gamipress-achievement user-has-earned">';
    $content .= '<div class="gamipress-achievement-image"><a href="'. get_permalink( $achievement->ID ) . '">' . gamipress_get_achievement_post_thumbnail( $achievement->ID ) . '</a></div>';
    $content .= '<div class="gamipress-achievement-description">' . gamipress_bp_activity_get_post_excerpt( $achievement->ID ) . '</div>';
    $content .= '</div>';

    // Bypass checking our activity items from moderation, as we know we are legit.
    add_filter( 'bp_bypass_check_for_moderation', '__return_true' );

    // Setup the activity details
    $activity = array(
        'action'       => sprintf( __( '%1$s completed the step "%2$s" of the %3$s %4$s', 'gamipress' ), bp_core_get_userlink( $user_id ), $post->post_title, $singular_name, '<a href="' . get_permalink( $achievement->ID ) . '">' . $achievement->post_title . '</a>' ),
        'content'      => $content,
        'component'    => 'gamipress',
        'type'         => 'activity_update',
        'primary_link' => get_permalink( $achievement->ID ),
        'user_id'      => $user_id,
        'item_id'      => $post_id,
    );

    return $activity;

}
add_filter( 'gamipress_bp_activity_details', 'gamipress_bp_step_activity_details', 10, 6 );

/**
 * Rank activity entry details
 *
 * @since   1.2.6
 *
 * @param array     $activity
 * @param int       $user_id
 * @param int       $post_id
 * @param string    $trigger_type
 * @param int       $site_id
 * @param array     $args
 *
 * @return array
 */
function gamipress_bp_rank_activity_details( $activity, $user_id, $post_id, $trigger_type, $site_id, $args ) {

    // Setup vars
    $prefix             = '_gamipress_bp_';
    $post               = gamipress_get_post( $post_id );
    $post_type          = $post->post_type;
    $rank_types         = gamipress_get_rank_types();

    // Bail if isn't a rank
    if( ! in_array( $post_type, gamipress_get_rank_types_slugs() ) )
        return $activity;

    // Bail if create activity option isn't enabled
    if( ! (bool) gamipress_get_post_meta( $rank_types[$post_type]['ID'], $prefix . 'create_rank_activity', true ) )
        return $activity;

    // Grab the singular name for our rank type
    $singular_name = strtolower( $rank_types[$post_type]['singular_name'] );

    // Setup our entry content
    $content = '<div id="gamipress-rank-' . $post_id . '" class="gamipress-rank user-has-earned">';
    $content .= '<div class="gamipress-rank-image"><a href="'. get_permalink( $post_id ) . '">' . gamipress_get_rank_post_thumbnail( $post_id ) . '</a></div>';
    $content .= '<div class="gamipress-rank-description">' . gamipress_bp_activity_get_post_excerpt( $post_id ) . '</div>';
    $content .= '</div>';

    // Bypass checking our activity items from moderation, as we know we are legit.
    add_filter( 'bp_bypass_check_for_moderation', '__return_true' );

    // Setup the activity details
    $activity = array(
        'action'       => sprintf( __( '%1$s reached the %2$s %3$s', 'gamipress' ), bp_core_get_userlink( $user_id ), $singular_name, '<a href="' . get_permalink( $post_id ) . '">' . $post->post_title . '</a>' ),
        'content'      => $content,
        'component'    => 'gamipress',
        'type'         => 'activity_update',
        'primary_link' => get_permalink( $post_id ),
        'user_id'      => $user_id,
        'item_id'      => $post_id,
    );

    return $activity;

}
add_filter( 'gamipress_bp_activity_details', 'gamipress_bp_rank_activity_details', 10, 6 );

/**
 * Rank requirement activity entry details
 *
 * @since   1.2.6
 *
 * @param array     $activity
 * @param int       $user_id
 * @param int       $post_id
 * @param string    $trigger_type
 * @param int       $site_id
 * @param array     $args
 *
 * @return array
 */
function gamipress_bp_rank_requirement_activity_details( $activity, $user_id, $post_id, $trigger_type, $site_id, $args ) {

    // Setup vars
    $prefix             = '_gamipress_bp_';
    $post               = gamipress_get_post( $post_id );
    $post_type          = $post->post_type;
    $rank_types         = gamipress_get_rank_types();

    // Bail if isn't a rank requirement
    if( $post_type !== 'rank-requirement' )
        return $activity;

    $rank = gamipress_get_rank_requirement_rank( $post_id );

    // Bail if create activity option isn't enabled
    if( ! (bool) gamipress_get_post_meta( $rank_types[$rank->post_type]['ID'], $prefix . 'create_rank_requirement_activity', true ) )
        return $activity;

    // Grab the singular name for our rank type
    $singular_name = strtolower( $rank_types[$rank->post_type]['singular_name'] );

    // Setup our entry content
    $content = '<div id="gamipress-rank-' . $rank->ID . '" class="gamipress-rank user-has-earned">';
    $content .= '<div class="gamipress-rank-image"><a href="'. get_permalink( $rank->ID ) . '">' . gamipress_get_rank_post_thumbnail( $rank->ID ) . '</a></div>';
    $content .= '<div class="gamipress-rank-description">' . gamipress_bp_activity_get_post_excerpt( $rank->ID ) . '</div>';
    $content .= '</div>';

    // Bypass checking our activity items from moderation, as we know we are legit.
    add_filter( 'bp_bypass_check_for_moderation', '__return_true' );

    // Setup the activity details
    $activity = array(
        'action'       => sprintf( __( '%1$s completed the requirement "%2$s" of the %3$s %4$s', 'gamipress' ), bp_core_get_userlink( $user_id ), $post->post_title, $singular_name, '<a href="' . get_permalink( $rank->ID ) . '">' . $rank->post_title . '</a>' ),
        'content'      => $content,
        'component'    => 'gamipress',
        'type'         => 'activity_update',
        'primary_link' => get_permalink( $rank->ID ),
        'user_id'      => $user_id,
        'item_id'      => $post_id,
    );

    return $activity;

}
add_filter( 'gamipress_bp_activity_details', 'gamipress_bp_rank_requirement_activity_details', 10, 6 );

/**
 * Filter activity allowed html tags to allow divs with classes and ids.
 *
 * @since 1.0.1
 *
 * @param array $activity_allowed_tags
 *
 * @return array
 */
function gamipress_bp_activity_allowed_tags( $activity_allowed_tags ) {

    $activity_allowed_tags['div'] = array();
    $activity_allowed_tags['div']['id'] = array();
    $activity_allowed_tags['div']['class'] = array();

    return $activity_allowed_tags;

}
add_filter( 'bp_activity_allowed_tags', 'gamipress_bp_activity_allowed_tags' );

/**
 * Helper function to get a post excerpt
 *
 * @since 1.0.1
 *
 * @param int $post_id
 *
 * @return string
 */
function gamipress_bp_activity_get_post_excerpt( $post_id ) {

    $excerpt = has_excerpt( $post_id ) ? gamipress_get_post_field( 'post_excerpt', $post_id ) : gamipress_get_post_field( 'post_content', $post_id );

    return wpautop( do_blocks( apply_filters( 'get_the_excerpt', $excerpt, get_post( $post_id ) ) ) );

}
