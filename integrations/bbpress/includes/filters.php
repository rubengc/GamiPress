<?php
/**
 * Content Filters
 *
 * @package GamiPress\bbPress\Content_Filters
 * @since 1.0.4
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Reply author details
 *
 * @since  1.1.4
 */
function gamipress_bbp_reply_author_details() {

     $reply_id = bbp_get_reply_id();

    // User is not a guest
    if ( bbp_is_reply_anonymous( $reply_id ) )
        return;

    // Get the reply author ID
    $user_id = bbp_get_reply_author_id( $reply_id );

    gamipress_bbp_author_details( $user_id );

}
add_action( 'bbp_theme_after_reply_author_details', 'gamipress_bbp_reply_author_details' );

/**
 * Topic author details
 *
 * @since  1.1.4
 */
function gamipress_bbp_topic_author_details() {

    $user_id = bbp_get_topic_author_id();

    if( $user_id === 0 )
        return;

    gamipress_bbp_author_details( $user_id );

}
add_action( 'bbp_theme_after_topic_author_details', 'gamipress_bbp_topic_author_details' );

/**
 * Author details
 *
 * @since  1.0.0
 */
function gamipress_bbp_author_details( $user_id ) {

    /* -------------------------------
     * Points Types
       ------------------------------- */

    // Setup points types vars
    $points_types = gamipress_get_points_types();
    $points_types_slugs = gamipress_get_points_types_slugs();

    // Get points type display settings
    $points_types_to_show = gamipress_bbp_get_points_types();
    $points_types_thumbnail = (bool) gamipress_bbp_get_option( 'points_types_thumbnail', false );
    $points_types_thumbnail_size = (int) gamipress_bbp_get_option( 'points_types_thumbnail_size', 25 );
    $points_types_label = (bool) gamipress_bbp_get_option( 'points_types_label', false );

    // Parse thumbnail size
    if( $points_types_thumbnail_size > 0 ) {
        $points_types_thumbnail_size = array( $points_types_thumbnail_size, $points_types_thumbnail_size );
    } else {
        $points_types_thumbnail_size = 'gamipress-points';
    }

    if( ! empty( $points_types_to_show ) ) : ?>

        <div class="gamipress-bbpress-points">

            <?php foreach( $points_types_to_show as $points_type_to_show ) :

                // If points type not registered, skip
                if( ! in_array( $points_type_to_show, $points_types_slugs ) )
                    continue;

                $points_type = $points_types[$points_type_to_show];
                $label_position = gamipress_get_points_type_label_position( $points_type_to_show );
                $user_points = gamipress_get_user_points( $user_id, $points_type_to_show ); ?>

                <div class="gamipress-bbpress-points-type gamipress-bbpress-<?php echo $points_type_to_show; ?>">

                    <?php // The points thumbnail ?>
                    <?php if( $points_types_thumbnail ) :
                        $points_thumbnail = gamipress_get_points_type_thumbnail( $points_type_to_show, $points_types_thumbnail_size ); ?>

                        <?php if( ! empty( $points_thumbnail ) ) : ?>
                            <span class="gamipress-bbpress-points-thumbnail gamipress-bbpress-<?php echo $points_type_to_show; ?>-thumbnail">
                                <?php echo $points_thumbnail; ?>
                            </span>
                        <?php endif; ?>

                    <?php endif; ?>

                    <?php // The points label (before) ?>
                    <?php if( $points_types_label && $label_position === 'before' ) : ?>

                        <span class="gamipress-bbpress-points-label gamipress-bbpress-<?php echo $points_type_to_show; ?>-label">
                            <?php echo _n( $points_type['singular_name'], $points_type['plural_name'], $user_points, 'gamipress-bbp' ); ?>
                        </span>

                    <?php endif; ?>

                    <?php // The user points amount ?>
                    <span class="gamipress-bbpress-user-points gamipress-bbpress-user-<?php echo $points_type_to_show; ?>">
                        <?php echo gamipress_format_amount( $user_points, $points_type_to_show ); ?>
                    </span>

                    <?php // The points label (after) ?>
                    <?php if( $points_types_label && $label_position !== 'before' ) : ?>

                        <span class="gamipress-bbpress-points-label gamipress-bbpress-<?php echo $points_type_to_show; ?>-label">
                            <?php echo _n( $points_type['singular_name'], $points_type['plural_name'], $user_points, 'gamipress-bbp' ); ?>
                        </span>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif;


    /* -------------------------------
     * Achievement Types
       ------------------------------- */

    // Setup achievement types vars
    $achievement_types = gamipress_get_achievement_types();
    $achievement_types_slugs = gamipress_get_achievement_types_slugs();

    // Get achievement type display settings
    $achievement_types_to_show          = gamipress_bbp_get_achievement_types();
    $achievement_types_thumbnail        = (bool) gamipress_bbp_get_option( 'achievement_types_thumbnail', false );
    $achievement_types_thumbnail_size   = (int) gamipress_bbp_get_option( 'achievement_types_thumbnail_size', 25 );
    $achievement_types_title            = (bool) gamipress_bbp_get_option( 'achievement_types_title', false );
    $achievement_types_link             = (bool) gamipress_bbp_get_option( 'achievement_types_link', false );
    $achievement_types_label            = (bool) gamipress_bbp_get_option( 'achievement_types_label', false );
    $achievements_limit                 = absint( gamipress_bbp_get_option( 'achievements_limit', '' ) );


    // Parse thumbnail size
    if( $achievement_types_thumbnail_size > 0 ) {
        $achievement_types_thumbnail_size = array( $achievement_types_thumbnail_size, $achievement_types_thumbnail_size );
    } else {
        $achievement_types_thumbnail_size = 'gamipress-achievement';
    }

    if( ! empty( $achievement_types_to_show ) ) : ?>

        <div class="gamipress-bbpress-achievements">

            <?php foreach( $achievement_types_to_show as $achievement_type_to_show ) :

                // If achievements type not registered, skip
                if( ! in_array( $achievement_type_to_show, $achievement_types_slugs ) )
                    continue;

                $achievement_type = $achievement_types[$achievement_type_to_show];
                $user_achievements = gamipress_get_user_achievements( array(
                    'user_id' => $user_id,
                    'achievement_type' => $achievement_type_to_show,
                    'limit' => ( $achievements_limit > 0 ? $achievements_limit : -1 ),
                    'groupby' => 'achievement_id',
                    'display' => true,
                ) );

                // If user has not earned any achievements of this type, skip
                if( empty( $user_achievements ) ) {
                    continue;
                } ?>

                <div class="gamipress-bbpress-achievement gamipress-bbpress-<?php echo $achievement_type_to_show; ?>">

                    <?php // The achievement type label
                    if( $achievement_types_label) : ?>
                        <span class="gamipress-bbpress-achievement-type-label gamipress-bbpress-<?php echo $achievement_type_to_show; ?>-label">
                            <?php echo $achievement_type['plural_name']; ?>:
                        </span>
                    <?php endif; ?>

                    <?php // Lets to get just the achievement thumbnail and title
                    foreach( $user_achievements as $user_achievement ) : ?>

                        <?php // The achievement thumbnail ?>
                        <?php if( $achievement_types_thumbnail ) :
                            $achievement_thumbnail = gamipress_get_achievement_post_thumbnail( $user_achievement->ID, $achievement_types_thumbnail_size ); ?>

                            <?php if( ! empty( $achievement_thumbnail ) ) : ?>

                                <?php // The achievement link ?>
                                <?php if( $achievement_types_link ) : ?>

                                    <a href="<?php echo get_permalink( $user_achievement->ID ); ?>" title="<?php echo get_the_title( $user_achievement->ID ); ?>" class="gamipress-bbpress-achievement-thumbnail gamipress-bbpress-<?php echo $achievement_type_to_show; ?>-thumbnail">
                                        <?php echo $achievement_thumbnail; ?>
                                    </a>

                                <?php else : ?>

                                    <span title="<?php echo get_the_title( $user_achievement->ID ); ?>" class="gamipress-bbpress-achievement-thumbnail gamipress-bbpress-<?php echo $achievement_type_to_show; ?>-thumbnail">
                                        <?php echo $achievement_thumbnail; ?>
                                    </span>

                                <?php endif; ?>

                            <?php endif; ?>

                        <?php endif; ?>

                        <?php // The achievement title ?>
                        <?php if( $achievement_types_title ) :
                            $achievement_title = get_the_title( $user_achievement->ID ); ?>

                            <?php if( ! empty( $achievement_title ) ) : ?>

                                <?php // The achievement link ?>
                                <?php if( $achievement_types_link ) : ?>

                                    <a href="<?php echo get_permalink( $user_achievement->ID ); ?>" title="<?php echo get_the_title( $user_achievement->ID ); ?>" class="gamipress-bbpress-achievement-title gamipress-bbpress-<?php echo $achievement_type_to_show; ?>-title">
                                        <?php echo $achievement_title; ?>
                                    </a>

                                <?php else : ?>

                                    <span class="gamipress-bbpress-achievement-title gamipress-bbpress-<?php echo $achievement_type_to_show; ?>-title">
                                        <?php echo $achievement_title; ?>
                                    </span>

                                <?php endif; ?>

                            <?php endif; ?>

                        <?php endif; ?>

                    <?php endforeach; ?>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif;

    /* -------------------------------
     * Rank Types
       ------------------------------- */

    // Setup rank types vars
    $rank_types = gamipress_get_rank_types();
    $rank_types_slugs = gamipress_get_rank_types_slugs();

    // Get rank type display settings
    $rank_types_to_show         = gamipress_bbp_get_rank_types();
    $rank_types_thumbnail       = (bool) gamipress_bbp_get_option( 'rank_types_thumbnail', false );
    $rank_types_thumbnail_size  = (int) gamipress_bbp_get_option( 'rank_types_thumbnail_size', 25 );
    $rank_types_title           = (bool) gamipress_bbp_get_option( 'rank_types_title', false );
    $rank_types_link            = (bool) gamipress_bbp_get_option( 'rank_types_link', false );
    $rank_types_label           = (bool) gamipress_bbp_get_option( 'rank_types_label', false );

    // Parse thumbnail size
    if( $rank_types_thumbnail_size > 0 ) {
        $rank_types_thumbnail_size = array( $rank_types_thumbnail_size, $rank_types_thumbnail_size );
    } else {
        $rank_types_thumbnail_size = 'gamipress-rank';
    }

    if( ! empty( $rank_types_to_show ) ) : ?>

        <div class="gamipress-bbpress-ranks">

            <?php foreach( $rank_types_to_show as $rank_type_to_show ) :

                // If points type not registered, skip
                if( ! in_array( $rank_type_to_show, $rank_types_slugs ) )
                    continue;

                $rank_type = $rank_types[$rank_type_to_show];
                $user_rank = gamipress_get_user_rank( $user_id, $rank_type_to_show ); ?>

                <div class="gamipress-bbpress-rank gamipress-bbpress-<?php echo $rank_type_to_show; ?>">

                    <?php // The rank type label
                    if( $rank_types_label ) : ?>
                        <span class="gamipress-bbpress-rank-label gamipress-bbpress-<?php echo $rank_type_to_show; ?>-label">
                            <?php echo $rank_type['singular_name']; ?>:
                        </span>
                    <?php endif; ?>

                    <?php // The rank thumbnail ?>
                    <?php if( $rank_types_thumbnail ) :
                        $rank_thumbnail = gamipress_get_rank_post_thumbnail( $user_rank->ID, $rank_types_thumbnail_size ); ?>

                        <?php if( ! empty( $rank_thumbnail ) ) : ?>
                            <?php // The rank link ?>
                            <?php if( $rank_types_link ) : ?>

                                <a href="<?php echo get_permalink( $user_rank->ID ); ?>" title="<?php echo $user_rank->post_title; ?>" class="gamipress-bbpress-rank-thumbnail gamipress-bbpress-<?php echo $rank_type_to_show; ?>-thumbnail">
                                    <?php echo $rank_thumbnail; ?>
                                </a>

                            <?php else : ?>

                                <span title="<?php echo $user_rank->post_title; ?>" class="gamipress-bbpress-rank-thumbnail gamipress-bbpress-<?php echo $rank_type_to_show; ?>-thumbnail">
                                    <?php echo $rank_thumbnail; ?>
                                </span>

                            <?php endif; ?>
                        <?php endif; ?>

                    <?php endif; ?>

                    <?php // The rank title ?>
                    <?php if( $rank_types_title && ! empty( $user_rank->post_title ) ) : ?>

                        <?php // The rank link ?>
                        <?php if( $rank_types_link ) : ?>

                            <a href="<?php echo get_permalink( $user_rank->ID ); ?>" title="<?php echo $user_rank->post_title; ?>" class="gamipress-bbpress-rank-title gamipress-bbpress-<?php echo $rank_type_to_show; ?>-title">
                                <?php echo $user_rank->post_title; ?>
                            </a>

                        <?php else : ?>

                            <span class="gamipress-bbpress-rank-title gamipress-bbpress-<?php echo $rank_type_to_show; ?>-title">
                                <?php echo $user_rank->post_title; ?>
                            </span>

                        <?php endif; ?>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>
        </div>
    <?php endif;

}

/**
 * Helper function to retrieve the points types configured at author details screen
 *
 * @since  1.0.4
 *
 * @return array
 */
function gamipress_bbp_get_points_types() {

    $points_types = array();

    $points_types_slugs = gamipress_get_points_types_slugs();

    $points_types_to_show = gamipress_bbp_get_option( 'points_types', array() );

    foreach( $points_types_to_show as $points_type_slug ) {

        if( ! in_array( $points_type_slug, $points_types_slugs ) ) {
            continue;
        }

        $points_types[] = $points_type_slug;
    }

    return $points_types;

}

/**
 * Helper function to retrieve the achievement types configured at author details screen
 *
 * @since  1.0.4
 *
 * @return array
 */
function gamipress_bbp_get_achievement_types() {

    $achievements_types = array();

    $achievement_types_slugs = gamipress_get_achievement_types_slugs();

    $achievements_types_to_show = gamipress_bbp_get_option( 'achievement_types', array() );

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
 * Helper function to retrieve the rank types configured at author details screen
 *
 * @since  1.0.4
 *
 * @return array
 */
function gamipress_bbp_get_rank_types() {

    $ranks_types = array();

    $rank_types_slugs = gamipress_get_rank_types_slugs();

    $ranks_types_to_show = gamipress_bbp_get_option( 'rank_types', array() );

    foreach( $ranks_types_to_show as $rank_type_slug ) {

        // Skip if not registered
        if( ! in_array( $rank_type_slug, $rank_types_slugs ) ) {
            continue;
        }

        $ranks_types[] = $rank_type_slug;
    }

    return $ranks_types;

}