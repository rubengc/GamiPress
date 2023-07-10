<?php
/**
 * User Profile
 *
 * @package GamiPress\PeepSo\User_Profile
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function gamipress_peepso_load_profile_actions() {

    // Bail if we are at admin area
    if( is_admin() ) return;

    $points_tab_title = gamipress_peepso_get_option( 'points_tab_title', __( 'Points', 'gamipress' ) );
    $points_tab_slug = gamipress_peepso_get_option( 'points_tab_slug', '' );

    // If empty slug generate it from the title
    if( empty( $points_tab_slug ) ) {
        $points_tab_slug = sanitize_title( $points_tab_title );
    }

    // Register the points segment action
    add_action( "peepso_profile_segment_{$points_tab_slug}", 'gamipress_peepso_profile_segment' );

    $achievements_tab_title = gamipress_peepso_get_option( 'achievements_tab_title', __( 'Achievements', 'gamipress' ) );
    $achievements_tab_slug = gamipress_peepso_get_option( 'achievements_tab_slug', '' );

    // If empty slug generate it from the title
    if( empty( $achievements_tab_slug ) ) {
        $achievements_tab_slug = sanitize_title( $achievements_tab_title );
    }

    // Register the achievements segment action
    add_action( "peepso_profile_segment_{$achievements_tab_slug}", 'gamipress_peepso_profile_segment' );

    $ranks_tab_title = gamipress_peepso_get_option( 'ranks_tab_title', __( 'Ranks', 'gamipress' ) );
    $ranks_tab_slug = gamipress_peepso_get_option( 'ranks_tab_slug', '' );

    // If empty slug generate it from the title
    if( empty( $ranks_tab_slug ) ) {
        $ranks_tab_slug = sanitize_title( $ranks_tab_title );
    }

    // Register the ranks segment action
    add_action( "peepso_profile_segment_{$ranks_tab_slug}", 'gamipress_peepso_profile_segment' );

}
add_filter( 'init', 'gamipress_peepso_load_profile_actions' );

/**
 * Add new user navigation profile links
 *
 * @since 1.0.0
 *
 * @param array $links Array of current navigation links
 *
 * @return array
 */
function gamipress_peepso_navigation_profile( $links ) {

    // Points tab
    $points_placement = gamipress_peepso_get_option( 'points_placement', '' );

    if( in_array( $points_placement, array( 'tab', 'both' ) ) ) {

        $points_tab_title = gamipress_peepso_get_option( 'points_tab_title', __( 'Points', 'gamipress' ) );
        $points_tab_slug = gamipress_peepso_get_option( 'points_tab_slug', '' );

        // If empty slug generate it from the title
        if( empty( $points_tab_slug ) ) {
            $points_tab_slug = sanitize_title( $points_tab_title );
        }

        $links['points'] = array(
            'href' => $points_tab_slug,
            'label'=> $points_tab_title,
            'icon' => gamipress_peepso_get_option( 'points_tab_icon', 'ps-icon-star' )
        );

    }

    // Achievements tab
    $achievements_placement = gamipress_peepso_get_option( 'achievements_placement', '' );

    if( in_array( $achievements_placement, array( 'tab', 'both' ) ) ) {

        $achievements_tab_title = gamipress_peepso_get_option( 'achievements_tab_title', __( 'Achievements', 'gamipress' ) );
        $achievements_tab_slug = gamipress_peepso_get_option( 'achievements_tab_slug', '' );

        // If empty slug generate it from the title
        if( empty( $achievements_tab_slug ) ) {
            $achievements_tab_slug = sanitize_title( $achievements_tab_title );
        }

        $links['achievements'] = array(
            'href' => $achievements_tab_slug,
            'label'=> $achievements_tab_title,
            'icon' => gamipress_peepso_get_option( 'achievements_tab_icon', 'ps-icon-certificate' )
        );

    }

    // Ranks tab
    $ranks_placement = gamipress_peepso_get_option( 'ranks_placement', '' );

    if( in_array( $ranks_placement, array( 'tab', 'both' ) ) ) {

        $ranks_tab_title = gamipress_peepso_get_option( 'ranks_tab_title', __( 'Ranks', 'gamipress' ) );
        $ranks_tab_slug = gamipress_peepso_get_option( 'ranks_tab_slug', '' );

        // If empty slug generate it from the title
        if( empty( $ranks_tab_slug ) ) {
            $ranks_tab_slug = sanitize_title( $ranks_tab_title );
        }

        $links['ranks'] = array(
            'href' => $ranks_tab_slug,
            'label'=> $ranks_tab_title,
            'icon' => gamipress_peepso_get_option( 'ranks_tab_icon', 'ps-icon-award' )
        );

    }

    return $links;

}
add_filter( 'peepso_navigation_profile', 'gamipress_peepso_navigation_profile' );

/**
 * Common render in user profile
 *
 * @since 1.0.0
 */
function gamipress_peepso_profile_segment() {

    $current_filter = gamipress_peepso_profile_current_filter();

    $profile = PeepSoProfileShortcode::get_instance();
    $user_id = PeepSoUrlSegments::get_view_id( $profile->get_view_user_id() );

    ?>

    <div class="peepso ps-page-profile ps-page-gamipress">

        <?php PeepSoTemplate::exec_template('general','navbar'); ?>

        <?php PeepSoTemplate::exec_template('profile', 'focus', array('current'=>'badges')); ?>

        <section id="mainbody" class="ps-page-unstyled">

            <section id="component" role="article" class="ps-clearfix">

                <?php do_action( "gamipress_{$current_filter}_content", $user_id );  ?>

            </section><!--end component-->

        </section><!--end mainbody-->

    </div><!--end row-->
    <?php PeepSoTemplate::exec_template('activity', 'dialogs'); ?>

    <?php

}

/**
 * Return the user custom segment slug as a common segment slug
 *
 * @since 1.0.4
 *
 * @return string
 */
function gamipress_peepso_profile_current_filter() {

    $current_filter = current_filter();

    // Check if is the points tab
    $points_tab_title = gamipress_peepso_get_option( 'points_tab_title', __( 'Points', 'gamipress' ) );
    $points_tab_slug = gamipress_peepso_get_option( 'points_tab_slug', '' );

    // If empty slug generate it from the title
    if( empty( $points_tab_slug ) ) {
        $points_tab_slug = sanitize_title( $points_tab_title );
    }

    if( $current_filter === "peepso_profile_segment_{$points_tab_slug}" ) {
        return 'peepso_profile_segment_points';
    }

    // Check if is the achievements tab
    $achievements_tab_title = gamipress_peepso_get_option( 'achievements_tab_title', __( 'Achievements', 'gamipress' ) );
    $achievements_tab_slug = gamipress_peepso_get_option( 'achievements_tab_slug', '' );

    // If empty slug generate it from the title
    if( empty( $achievements_tab_slug ) ) {
        $achievements_tab_slug = sanitize_title( $achievements_tab_title );
    }

    if( $current_filter === "peepso_profile_segment_{$achievements_tab_slug}" ) {
        return 'peepso_profile_segment_achievements';
    }

    // Check if is the ranks tab
    $ranks_tab_title = gamipress_peepso_get_option( 'ranks_tab_title', __( 'Ranks', 'gamipress' ) );
    $ranks_tab_slug = gamipress_peepso_get_option( 'ranks_tab_slug', '' );

    // If empty slug generate it from the title
    if( empty( $ranks_tab_slug ) ) {
        $ranks_tab_slug = sanitize_title( $ranks_tab_title );
    }

    if( $current_filter === "peepso_profile_segment_{$ranks_tab_slug}" ) {
        return 'peepso_profile_segment_ranks';
    }

    return $current_filter;

}

/**
 * Render points in user profile
 *
 * @since 1.0.0
 *
 * @param int $user_id Displayed user ID
 */
function gamipress_peepso_profile_points_content( $user_id ) {

    $points_types_to_show = gamipress_peepso_profile_get_points_types();

    if ( empty( $points_types_to_show ) ) {
        return;
    }

    echo gamipress_points_shortcode( array(
        'type'          => implode( ',', $points_types_to_show ),
        'current_user'  => 'no',
        'user_id'       => $user_id,
        'wpms'          => (bool) gamipress_get_option( 'ms_show_all_achievements', false ) ? 'yes' : 'no',
    ) );

}
add_action( 'gamipress_peepso_profile_segment_points_content', 'gamipress_peepso_profile_points_content' );

/**
 * Render achievements in user profile
 *
 * @since 1.0.0
 *
 * @param int $user_id Displayed user ID
 */
function gamipress_peepso_profile_achievements_content( $user_id ) {

    $achievements_types_to_show = gamipress_peepso_profile_get_achievements_types();

    if ( empty( $achievements_types_to_show ) ) {
        return;
    }

    $prefix = 'profile_achievements_';

    // Setup achievement atts
    $achievement_atts = array();

    // Loop achievement shortcode fields to pass to the shortcode call
    foreach( GamiPress()->shortcodes['gamipress_achievement']->fields as $field_id => $field_args ) {

        if( $field_id === 'id' ) {
            continue;
        }

        if( $field_args['type'] === 'checkbox' ) {
            $achievement_atts[$field_id] = ( (bool) gamipress_peepso_get_option( $prefix . $field_id, false ) ? 'yes' : 'no' );
        } else {
            $achievement_atts[$field_id] = gamipress_peepso_get_option( $prefix . $field_id, ( isset( $field_args['default'] ) ? $field_args['default'] : '' ) );
        }

    }

    echo gamipress_achievements_shortcode( array_merge( array(
        'type'          => implode( ',', $achievements_types_to_show ),
        'columns'       => gamipress_peepso_get_option( $prefix . 'columns', '1' ),
        'filter'        => 'no',
        'filter_value'  => 'completed',
        'search'        => 'no',
        'current_user'  => 'no',
        'user_id'       => $user_id,
        'wpms'          => (bool) gamipress_get_option( 'ms_show_all_achievements', false ) ? 'yes' : 'no',
        'limit'         => gamipress_peepso_get_option( $prefix . 'limit', '10' ),
        'orderby'     	=> gamipress_peepso_get_option( $prefix . 'orderby', 'menu_order' ),
        'order'       	=> gamipress_peepso_get_option( $prefix . 'order', 'ASC' ),
        'include'     	=> '',
        'exclude'     	=> '',
    ), $achievement_atts ) );

}
add_action( 'gamipress_peepso_profile_segment_achievements_content', 'gamipress_peepso_profile_achievements_content' );

/**
 * Render ranks in user profile
 *
 * @since 1.0.0
 *
 * @param int $user_id Displayed user ID
 */
function gamipress_peepso_profile_ranks_content( $user_id ) {

    $ranks_types_to_show = gamipress_peepso_profile_get_ranks_types();

    if ( empty( $ranks_types_to_show ) ) {
        return;
    }

    $prefix = 'profile_ranks_';

    // Setup rank atts
    $rank_atts = array();

    // Loop rank shortcode fields to pass to the shortcode call
    foreach( GamiPress()->shortcodes['gamipress_rank']->fields as $field_id => $field_args ) {

        if( $field_id === 'id' ) {
            continue;
        }

        if( $field_args['type'] === 'checkbox' ) {
            $rank_atts[$field_id] = ( (bool) gamipress_peepso_get_option( $prefix . $field_id, false ) ? 'yes' : 'no' );
        } else {
            $rank_atts[$field_id] = gamipress_peepso_get_option( $prefix . $field_id, ( isset( $field_args['default'] ) ? $field_args['default'] : '' ) );
        }

    }

    echo gamipress_ranks_shortcode( array_merge( array(
        'type'          => implode( ',', $ranks_types_to_show ),
        'columns'       => gamipress_peepso_get_option( $prefix . 'columns', '1' ),
        'current_user'  => 'no',
        'user_id'       => $user_id,
        'wpms'          => (bool) gamipress_get_option( 'ms_show_all_achievements', false ) ? 'yes' : 'no',
        'orderby'     	=> gamipress_peepso_get_option( $prefix . 'orderby', 'priority' ),
        'order'       	=> gamipress_peepso_get_option( $prefix . 'order', 'DESC' ),
        'include'     	=> '',
        'exclude'     	=> '',
    ), $rank_atts ) );

}
add_action( 'gamipress_peepso_profile_segment_ranks_content', 'gamipress_peepso_profile_ranks_content' );

/**
 * Display user information at top
 *
 * @since 1.0.0
 *
 * @param int $user_id Displayed user ID
 */
function gamipress_peepso_after_render_user_name( $user_id ) {

    /* -------------------------------
     * Points
       ------------------------------- */

    $points_placement = gamipress_peepso_get_option( 'points_placement', '' );

    if( $points_placement === 'top' || $points_placement === 'both' ) {

        // Setup points types vars
        $points_types = gamipress_get_points_types();
        $points_types_slugs = gamipress_get_points_types_slugs();

        // Get points display settings
        $points_types_to_show = gamipress_peepso_profile_get_points_types();
        $points_types_thumbnail = (bool) gamipress_peepso_get_option( 'profile_points_types_top_thumbnail', false );
        $points_types_thumbnail_size = (int) gamipress_peepso_get_option( 'profile_points_types_top_thumbnail_size', 25 );
        $points_types_label = (bool) gamipress_peepso_get_option( 'profile_points_types_top_label', false );

        // Parse thumbnail size
        if( $points_types_thumbnail_size > 0 ) {
            $points_types_thumbnail_size = array( $points_types_thumbnail_size, $points_types_thumbnail_size );
        } else {
            $points_types_thumbnail_size = 'gamipress-points';
        }

        if( ! empty( $points_types_to_show ) ) : ?>

            <div class="gamipress-peepso-points">

                <?php foreach( $points_types_to_show as $points_type_to_show ) :

                    // If points type not registered, skip
                    if( ! in_array( $points_type_to_show, $points_types_slugs ) )
                        continue;

                    $points_type = $points_types[$points_type_to_show];
                    $user_points = gamipress_get_user_points( $user_id, $points_type_to_show ); ?>

                    <div class="gamipress-peepso-points-type gamipress-peepso-<?php echo $points_type_to_show; ?>">

                        <?php // The points thumbnail ?>
                        <?php if( $points_types_thumbnail ) : ?>

                            <span class="gamipress-peepso-points-thumbnail gamipress-peepso-<?php echo $points_type_to_show; ?>-thumbnail">
                            <?php echo gamipress_get_points_type_thumbnail( $points_type_to_show, $points_types_thumbnail_size ); ?>
                        </span>

                        <?php endif; ?>

                        <?php // The user points amount ?>
                        <span class="gamipress-peepso-user-points gamipress-peepso-user-<?php echo $points_type_to_show; ?>">
                        <?php echo $user_points; ?>
                    </span>

                        <?php // The points label ?>
                        <?php if( $points_types_label ) : ?>

                            <span class="gamipress-peepso-points-label gamipress-peepso-<?php echo $points_type_to_show; ?>-label">
                            <?php echo _n( $points_type['singular_name'], $points_type['plural_name'], $user_points ); ?>
                        </span>

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>
            </div>
        <?php endif;

    }

    /* -------------------------------
     * Achievements
       ------------------------------- */

    $achievements_placement = gamipress_peepso_get_option( 'achievements_placement', '' );

    if( $achievements_placement === 'top' || $achievements_placement === 'both' ) {

        // Setup achievement types vars
        $achievement_types = gamipress_get_achievement_types();
        $achievement_types_slugs = gamipress_get_achievement_types_slugs();

        // Get achievements display settings
        $achievement_types_to_show = gamipress_peepso_profile_get_achievements_types();
        $achievement_types_thumbnail = (bool) gamipress_peepso_get_option( 'profile_achievements_top_thumbnail', false );
        $achievement_types_thumbnail_size = (int) gamipress_peepso_get_option( 'profile_achievements_top_thumbnail_size', 25 );
        $achievement_types_title = (bool) gamipress_peepso_get_option( 'profile_achievements_top_title', false );
        $achievement_types_link = (bool) gamipress_peepso_get_option( 'profile_achievements_top_link', false );
        $achievement_types_label = (bool) gamipress_peepso_get_option( 'profile_achievements_top_label', false );
        $achievement_types_limit = (int) gamipress_peepso_get_option( 'profile_achievements_top_limit', 10 );

        // Parse thumbnail size
        if( $achievement_types_thumbnail_size > 0 ) {
            $achievement_types_thumbnail_size = array( $achievement_types_thumbnail_size, $achievement_types_thumbnail_size );
        } else {
            $achievement_types_thumbnail_size = 'gamipress-achievement';
        }

        if( ! empty( $achievement_types_to_show ) ) : ?>

            <div class="gamipress-peepso-achievements">

                <?php foreach( $achievement_types_to_show as $achievement_type_to_show ) :

                    // If achievements type not registered, skip
                    if( ! in_array( $achievement_type_to_show, $achievement_types_slugs ) )
                        continue;

                    $achievement_type = $achievement_types[$achievement_type_to_show];
                    $user_achievements = gamipress_get_user_achievements( array(
                        'user_id' => $user_id,
                        'achievement_type' => $achievement_type_to_show,
                        'groupby' => 'achievement_id',
                        'limit' => $achievement_types_limit
                    ) );

                    // If user has not earned any achievements of this type, skip
                    if( empty( $user_achievements ) ) {
                        continue;
                    } ?>

                    <div class="gamipress-peepso-achievement gamipress-peepso-<?php echo $achievement_type_to_show; ?>">

                        <?php // The achievement type label ?>
                        <?php if( $achievement_types_label ) : ?>
                        <span class="gamipress-peepso-achievement-type-label gamipress-peepso-<?php echo $achievement_type_to_show; ?>-label">
                            <?php echo $achievement_type['plural_name']; ?>:
                        </span>
                        <?php endif; ?>

                        <?php // Lets to get just the achievement thumbnail and title
                        foreach( $user_achievements as $user_achievement ) : ?>

                            <?php // The achievement thumbnail ?>
                            <?php if( $achievement_types_thumbnail ) : ?>

                                <?php // The achievement link ?>
                                <?php if( $achievement_types_link ) : ?>

                                    <a href="<?php echo get_permalink( $user_achievement->ID ); ?>" title="<?php echo get_the_title( $user_achievement->ID ); ?>" class="gamipress-peepso-achievement-thumbnail gamipress-peepso-<?php echo $achievement_type_to_show; ?>-thumbnail">
                                        <?php echo gamipress_get_achievement_post_thumbnail( $user_achievement->ID, $achievement_types_thumbnail_size ); ?>
                                    </a>

                                <?php else : ?>

                                    <span title="<?php echo get_the_title( $user_achievement->ID ); ?>" class="gamipress-peepso-achievement-thumbnail gamipress-peepso-<?php echo $achievement_type_to_show; ?>-thumbnail">
                                        <?php echo gamipress_get_achievement_post_thumbnail( $user_achievement->ID, $achievement_types_thumbnail_size ); ?>
                                    </span>

                                <?php endif; ?>

                            <?php endif; ?>

                            <?php // The achievement title ?>
                            <?php if( $achievement_types_title ) : ?>

                                <?php // The achievement link ?>
                                <?php if( $achievement_types_link ) : ?>

                                    <a href="<?php echo get_permalink( $user_achievement->ID ); ?>" title="<?php echo get_the_title( $user_achievement->ID ); ?>" class="gamipress-peepso-achievement-title gamipress-peepso-<?php echo $achievement_type_to_show; ?>-title">
                                        <?php echo get_the_title( $user_achievement->ID ); ?>
                                    </a>

                                <?php else : ?>

                                    <span class="gamipress-peepso-achievement-title gamipress-peepso-<?php echo $achievement_type_to_show; ?>-title">
                                        <?php echo get_the_title( $user_achievement->ID ); ?>
                                    </span>

                                <?php endif; ?>

                            <?php endif; ?>

                        <?php endforeach; ?>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif;

    }

    /* -------------------------------
     * Ranks
       ------------------------------- */

    $ranks_placement = gamipress_peepso_get_option( 'ranks_placement', '' );

    if( $ranks_placement === 'top' || $ranks_placement === 'both' ) {

        // Setup rank types vars
        $rank_types = gamipress_get_rank_types();
        $rank_types_slugs = gamipress_get_rank_types_slugs();

        // Get ranks display settings
        $rank_types_to_show = gamipress_peepso_profile_get_ranks_types();
        $rank_types_thumbnail = (bool) gamipress_peepso_get_option( 'profile_ranks_top_thumbnail', false );
        $rank_types_thumbnail_size = (int) gamipress_peepso_get_option( 'profile_ranks_top_thumbnail_size', 25 );
        $rank_types_title = (bool) gamipress_peepso_get_option( 'profile_ranks_top_title', false );
        $rank_types_link = (bool) gamipress_peepso_get_option( 'profile_ranks_top_link', false );
        $rank_types_label = (bool) gamipress_peepso_get_option( 'profile_ranks_top_label', false );

        // Parse thumbnail size
        if( $rank_types_thumbnail_size > 0 ) {
            $rank_types_thumbnail_size = array( $rank_types_thumbnail_size, $rank_types_thumbnail_size );
        } else {
            $rank_types_thumbnail_size = 'gamipress-rank';
        }

        if( ! empty( $rank_types_to_show ) ) : ?>

            <div class="gamipress-peepso-ranks">

                <?php foreach( $rank_types_to_show as $rank_type_to_show ) :

                    // If rank type not registered, skip
                    if( ! in_array( $rank_type_to_show, $rank_types_slugs ) )
                        continue;

                    $rank_type = $rank_types[$rank_type_to_show];
                    $user_rank = gamipress_get_user_rank( $user_id, $rank_type_to_show ); ?>

                    <div class="gamipress-peepso-rank gamipress-peepso-<?php echo $rank_type_to_show; ?>">

                        <?php // The rank type label ?>
                        <?php if( $rank_types_label ) : ?>
                        <span class="gamipress-peepso-rank-label gamipress-peepso-<?php echo $rank_type_to_show; ?>-label">
                            <?php echo $rank_type['singular_name']; ?>:
                        </span>
                        <?php endif; ?>

                        <?php // The rank thumbnail ?>
                        <?php if( $rank_types_thumbnail ) : ?>

                            <?php // The rank link ?>
                            <?php if( $rank_types_link ) : ?>

                                <a href="<?php echo get_permalink( $user_rank->ID ); ?>" title="<?php echo $user_rank->post_title; ?>" class="gamipress-peepso-rank-thumbnail gamipress-peepso-<?php echo $rank_type_to_show; ?>-thumbnail">
                                    <?php echo gamipress_get_rank_post_thumbnail( $user_rank->ID, $rank_types_thumbnail_size ); ?>
                                </a>

                            <?php else : ?>

                                <span title="<?php echo $user_rank->post_title; ?>" class="gamipress-peepso-rank-thumbnail gamipress-peepso-<?php echo $rank_type_to_show; ?>-thumbnail">
                                <?php echo gamipress_get_rank_post_thumbnail( $user_rank->ID, $rank_types_thumbnail_size ); ?>
                            </span>

                            <?php endif; ?>

                        <?php endif; ?>

                        <?php // The rank title ?>
                        <?php if( $rank_types_title ) : ?>

                            <?php // The rank link ?>
                            <?php if( $rank_types_link ) : ?>

                                <a href="<?php echo get_permalink( $user_rank->ID ); ?>" title="<?php echo $user_rank->post_title; ?>" class="gamipress-peepso-rank-title gamipress-peepso-<?php echo $rank_type_to_show; ?>-title">
                                    <?php echo $user_rank->post_title; ?>
                                </a>

                            <?php else : ?>

                                <span class="gamipress-peepso-rank-title gamipress-peepso-<?php echo $rank_type_to_show; ?>-title">
                                <?php echo $user_rank->post_title; ?>
                            </span>

                            <?php endif; ?>

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>
            </div>
        <?php endif;

    }

}
add_action( 'peepso_profile_cover_full_after_name', 'gamipress_peepso_after_render_user_name' );

/**
 * Helper function to retrieve the points types configured at profile screen
 *
 * @since  1.0.8
 *
 * @return array
 */
function gamipress_peepso_profile_get_points_types() {

    $points_types = array();

    $points_types_slugs = gamipress_get_points_types_slugs();

    $points_types_to_show = gamipress_peepso_get_option( 'profile_points_types', array() );

    foreach( $points_types_to_show as $points_type_slug ) {

        if( ! in_array( $points_type_slug, $points_types_slugs ) ) {
            continue;
        }

        $points_types[] = $points_type_slug;
    }

    return $points_types;

}

/**
 * Helper function to retrieve the achievement types configured at profile screen
 *
 * @since  1.0.5
 *
 * @return array
 */
function gamipress_peepso_profile_get_achievements_types() {

    $achievements_types = array();

    $achievement_types_slugs = gamipress_get_achievement_types_slugs();

    $achievements_types_to_show = gamipress_peepso_get_option( 'profile_achievements_types', array() );

    foreach( $achievements_types_to_show as $achievement_type_slug ) {

        // Skip if not registered
        if( ! in_array( $achievement_type_slug, $achievement_types_slugs ) ) {
            continue;
        }

        $achievements_types[] = $achievement_type_slug;
    }

    return $achievements_types;

}

/**
 * Helper function to retrieve the rank types configured at profile screen
 *
 * @since  1.1.1
 *
 * @return array
 */
function gamipress_peepso_profile_get_ranks_types() {

    $ranks_types = array();

    $rank_types_slugs = gamipress_get_rank_types_slugs();

    $ranks_types_to_show = gamipress_peepso_get_option( 'profile_ranks_types', array() );

    foreach( $ranks_types_to_show as $rank_type_slug ) {

        // Skip if not registered
        if( ! in_array( $rank_type_slug, $rank_types_slugs ) ) {
            continue;
        }

        $ranks_types[] = $rank_type_slug;
    }

    return $ranks_types;

}