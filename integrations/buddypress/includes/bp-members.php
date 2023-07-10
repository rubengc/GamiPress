<?php
/**
 * BuddyPress Membership
 *
 * @package GamiPress\BuddyPress\BuddyPress_Members
 * @since 1.0.1
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Loads GamiPress_BP_Component Class from bp_init
 *
 * @since 1.0.1
 */
function gamipress_bp_load_components() {

    if ( function_exists( 'buddypress' ) && buddypress() && ! buddypress()->maintenance_mode && gamipress_bp_is_active( 'xprofile' ) ) {

        // Load the component if site pass all checks
        $GLOBALS['gamipress_points_bp_component'] = new GamiPress_Points_BP_Component();
        $GLOBALS['gamipress_achievements_bp_component'] = new GamiPress_Achievements_BP_Component();
        $GLOBALS['gamipress_ranks_bp_component'] = new GamiPress_Ranks_BP_Component();

    }

}
add_action( 'bp_init', 'gamipress_bp_load_components', 1 );

/**
 * Creates a BuddyPress member page for points
 *
 * @since 1.0.8
 */
function gamipress_bp_points_tab() {

    add_action( 'bp_template_content', 'gamipress_bp_points_tab_content' );

    bp_core_load_template( apply_filters( 'gamipress_bp_points_tab', 'members/single/plugins' ) );

}

/**
 * Displays a member points
 *
 * @since 1.0.8
 */
function gamipress_bp_points_tab_content() {

    $points_types_to_show = gamipress_bp_tab_get_points_types();

    if ( empty( $points_types_to_show ) ) {
        return;
    }

    echo gamipress_points_shortcode( array(
        'type'          => implode( ',', $points_types_to_show ),
        'current_user'  => 'no',
        'user_id'       => bp_displayed_user_id(),
        'wpms'          => (bool) gamipress_get_option( 'ms_show_all_achievements', false ) ? 'yes' : 'no',
    ) );

}

/**
 * Creates a BuddyPress member page for achievements
 *
 * @since 1.0.1
 */
function gamipress_bp_achievements_tab() {

    add_action( 'bp_template_content', 'gamipress_bp_achievements_tab_content' );

    bp_core_load_template( apply_filters( 'gamipress_bp_achievements_tab', 'members/single/plugins' ) );

}

/**
 * Displays a member achievements
 *
 * @since 1.0.1
 */
function gamipress_bp_achievements_tab_content() {

    $achievements_types_to_show = gamipress_bp_tab_get_achievements_types();

    // Bail if not types provided
    if ( empty( $achievements_types_to_show ) ) return;

    $achievements_tab_title = gamipress_bp_get_option( 'achievements_tab_title', __( 'Achievements', 'gamipress' ) );
    $achievements_tab_slug = gamipress_bp_get_option( 'achievements_tab_slug', '' );

    // If empty slug generate it from the title
    if( empty( $achievements_tab_slug ) )
        $achievements_tab_slug = sanitize_title( $achievements_tab_title );

    $type = '';
    $current_uri = $_SERVER['REQUEST_URI'];

    foreach ( $achievements_types_to_show as $achievement_type ) {

        // Check if current URI matches any type (need to check the achievements tab slug + achievement type
        if ( strpos( $current_uri, $achievements_tab_slug . '/' . $achievement_type ) ) {
            $type = $achievement_type;
            // Exit on find achievement type
            break;
        }
    }

    if ( empty( $type ) ) {

        if( isset( $achievements_types_to_show[0] ) ) {
            $type = $achievements_types_to_show[0];
        } else {
            return;
        }
    }

    $prefix = 'members_achievements_';

    // Setup achievement atts
    $achievement_atts = array();

    // Loop achievement shortcode fields to pass to the shortcode call
    foreach( GamiPress()->shortcodes['gamipress_achievement']->fields as $field_id => $field_args ) {

        if( $field_id === 'id' ) {
            continue;
        }

        if( $field_args['type'] === 'checkbox' ) {
            $achievement_atts[$field_id] = ( (bool) gamipress_bp_get_option( $prefix . $field_id, false ) ? 'yes' : 'no' );
        } else {
            $achievement_atts[$field_id] = gamipress_bp_get_option( $prefix . $field_id, ( isset( $field_args['default'] ) ? $field_args['default'] : '' ) );
        }

    }

    echo gamipress_achievements_shortcode( array_merge( array(
        'type'          => $type,
        'columns'       => gamipress_bp_get_option( $prefix . 'columns', '1' ),
        'filter'        => 'no',
        'filter_value'  => 'completed',
        'search'        => 'no',
        'current_user'  => 'no',
        'user_id'       => bp_displayed_user_id(),
        'wpms'          => (bool) gamipress_get_option( 'ms_show_all_achievements', false ) ? 'yes' : 'no',
        'limit'         => gamipress_bp_get_option( $prefix . 'limit', '10' ),
        'orderby'     	=> gamipress_bp_get_option( $prefix . 'orderby', 'menu_order' ),
        'order'       	=> gamipress_bp_get_option( $prefix . 'order', 'ASC' ),
        'include'     	=> '',
        'exclude'     	=> '',
    ), $achievement_atts ) );

}

/**
 * Creates a BuddyPress member page for achievements
 *
 * @since 1.1.1
 */
function gamipress_bp_ranks_tab() {

    add_action( 'bp_template_content', 'gamipress_bp_ranks_tab_content' );

    bp_core_load_template( apply_filters( 'gamipress_bp_ranks_tab', 'members/single/plugins' ) );

}

/**
 * Displays a member ranks
 *
 * @since 1.1.1
 */
function gamipress_bp_ranks_tab_content() {

    $ranks_types_to_show = gamipress_bp_tab_get_ranks_types();

    if ( empty( $ranks_types_to_show ) ) return;

    $prefix = 'members_ranks_';

    // Setup rank atts
    $rank_atts = array();

    // Loop rank shortcode fields to pass to the shortcode call
    foreach( GamiPress()->shortcodes['gamipress_rank']->fields as $field_id => $field_args ) {

        if( $field_id === 'id' ) {
            continue;
        }

        if( $field_args['type'] === 'checkbox' ) {
            $rank_atts[$field_id] = ( (bool) gamipress_bp_get_option( $prefix . $field_id, false ) ? 'yes' : 'no' );
        } else {
            $rank_atts[$field_id] = gamipress_bp_get_option( $prefix . $field_id, ( isset( $field_args['default'] ) ? $field_args['default'] : '' ) );
        }

    }

    echo gamipress_ranks_shortcode( array_merge( array(
        'type'          => implode( ',', $ranks_types_to_show ),
        'columns'       => gamipress_bp_get_option( $prefix . 'columns', '1' ),
        'current_user'  => 'no',
        'user_id'       => bp_displayed_user_id(),
        'wpms'          => (bool) gamipress_get_option( 'ms_show_all_achievements', false ) ? 'yes' : 'no',
        'orderby'     	=> gamipress_bp_get_option( $prefix . 'orderby', 'priority' ),
        'order'       	=> gamipress_bp_get_option( $prefix . 'order', 'DESC' ),
        'include'     	=> '',
        'exclude'     	=> '',
    ), $rank_atts ) );

}

/**
 * Displays user information at top
 *
 * @since 1.1.1
 */
function gamipress_bp_before_member_header() {

    $user_id = bp_displayed_user_id();

    gamipress_bp_user_details_display( $user_id, 'top' );

}
add_action( 'bp_before_member_header_meta', 'gamipress_bp_before_member_header' );

/**
 * Displays user information before activity content
 *
 * @since 1.4.2
 *
 * @param string $content
 *
 * @return string
 */
function gamipress_bp_activity_content( $content ) {

    global $activities_template;

    if( empty( $activities_template->activity->user_id ) ) {
        return $content;
    }

    $user_id = $activities_template->activity->user_id;

    ob_start();
    gamipress_bp_user_details_display( $user_id, 'activity' );
    $details = ob_get_clean();

    return $details . $content;

}
add_filter( 'bp_get_activity_content_body', 'gamipress_bp_activity_content' );

/**
 * Displays user information for activities with no content
 *
 * @since 1.4.2
 */
function gamipress_bp_activity_empty_content() {

    if( bp_activity_has_content() ) {
        return;
    }

    if( function_exists( 'bp_nouveau_activity_has_content' ) ) {
        if( bp_nouveau_activity_has_content() ) {
            return;
        }
    }

    gamipress_bp_user_details_display( bp_get_activity_user_id(), 'activity' );

}
add_action( 'bp_activity_entry_content', 'gamipress_bp_activity_empty_content', 9 );

/**
 * Displays user information before activity content
 *
 * @since 1.4.2
 *
 * @param string $content
 *
 * @return string
 */
function gamipress_bp_activity_comment_content( $content ) {

    global $activities_template;

    if( empty( $activities_template->activity->current_comment->user_id ) ) {
        return $content;
    }

    $user_id = $activities_template->activity->current_comment->user_id;

    ob_start();
    gamipress_bp_user_details_display( $user_id, 'activity' );
    $details = ob_get_clean();

    return $details . $content;

}
add_filter( 'bp_activity_comment_content', 'gamipress_bp_activity_comment_content' );

/**
 * Displays user information on member listing avatar
 *
 * @since 1.4.2
 *
 * @param string $avatar
 *
 * @return string
 */
function gamipress_bp_get_member_avatar( $avatar ) {

    global $members_template;

    if( empty( $members_template->member->id ) ) {
        return $avatar;
    }

    $user_id = $members_template->member->id;

    ob_start();
    gamipress_bp_user_details_display( $user_id, 'listing' );
    $details = ob_get_clean();

    if( empty( trim( $details ) ) ) {
        return $avatar;
    }

    /**
     * Filter available to decide if should append <a> tags to the member avatar details
     * Note: A tags are to close the member avatar link
     *
     * @since 1.4.6
     *
     * @param bool      $a_tags
     * @param string    $avatar
     * @param int       $user_id
     *
     * @return bool
     */
    $a_tags = apply_filters( 'gamipress_bp_member_avatar_a_tags', true, $avatar, $user_id );

    if( $a_tags ) {
        return $avatar . '</a>' . $details . '<a>';
    } else {
        return $avatar . $details;
    }

}
add_filter( 'bp_get_member_avatar', 'gamipress_bp_get_member_avatar' );

/**
 * Helper function to render the user details
 *
 * @since 1.4.2
 *
 * @param int       $user_id            The user ID
 * @param string    $view               The view where is rendering (top|listing|activity)
 */
function gamipress_bp_user_details_display( $user_id, $view ) {

    if( absint( $user_id ) === 0 ) {
        return;
    }

    $display_options = array(
        'show_points' => false,
        'show_achievements' => false,
        'show_ranks' => false,
    );

    // Get placements
    $points_placement = gamipress_bp_get_option( 'points_placement', array() );
    $achievements_placement = gamipress_bp_get_option( 'achievements_placement', array() );
    $ranks_placement = gamipress_bp_get_option( 'ranks_placement', array() );

    if( ! is_array( $points_placement ) ) $points_placement = array( $points_placement );
    if( ! is_array( $achievements_placement ) ) $achievements_placement = array( $achievements_placement );
    if( ! is_array( $ranks_placement ) ) $ranks_placement = array( $ranks_placement );

    // Check if should show or not
    $display_options['show_points'] = in_array( $view, $points_placement );
    $display_options['show_achievements'] = in_array( $view, $achievements_placement );
    $display_options['show_ranks'] = in_array( $view, $ranks_placement );

    // Points
    if( $display_options['show_points'] ) {
        $display_options['points_types_to_show'] = gamipress_bp_members_get_points_types();
        $display_options['points_types_thumbnail'] = (bool) gamipress_bp_get_option( 'members_points_types_top_thumbnail', false );
        $display_options['points_types_thumbnail_size'] = (int) gamipress_bp_get_option( 'members_points_types_top_thumbnail_size', 25 );
        $display_options['points_types_label'] = (bool) gamipress_bp_get_option( 'members_points_types_top_label', false );
    }

    // Achievements
    if( $display_options['show_achievements'] ) {
        $display_options['achievement_types_to_show'] = gamipress_bp_members_get_achievements_types();
        $display_options['achievement_types_thumbnail'] = (bool) gamipress_bp_get_option( 'members_achievements_top_thumbnail', false );
        $display_options['achievement_types_thumbnail_size'] = (int) gamipress_bp_get_option( 'members_achievements_top_thumbnail_size', 25 );
        $display_options['achievement_types_title'] = (bool) gamipress_bp_get_option( 'members_achievements_top_title', false );
        $display_options['achievement_types_link'] = (bool) gamipress_bp_get_option( 'members_achievements_top_link', false );
        $display_options['achievement_types_label'] = (bool) gamipress_bp_get_option( 'members_achievements_top_label', false );
        $display_options['achievement_types_limit'] = (int) gamipress_bp_get_option( 'members_achievements_top_limit', 10 );
    }

    // Ranks
    if( $display_options['show_ranks'] ) {
        $display_options['rank_types_to_show'] = gamipress_bp_members_get_ranks_types();
        $display_options['rank_types_thumbnail'] = (bool) gamipress_bp_get_option( 'members_ranks_top_thumbnail', false );
        $display_options['rank_types_thumbnail_size'] = (int) gamipress_bp_get_option( 'members_ranks_top_thumbnail_size', 25 );
        $display_options['rank_types_title'] = (bool) gamipress_bp_get_option( 'members_ranks_top_title', false );
        $display_options['rank_types_link'] = (bool) gamipress_bp_get_option( 'members_ranks_top_link', false );
        $display_options['rank_types_label'] = (bool) gamipress_bp_get_option( 'members_ranks_top_label', false );
    }

    /**
     * Filter available to override the display options
     *
     * @since 1.4.2
     *
     * @param array     $display_options    Display options from settings
     * @param int       $user_id            The user ID
     * @param string    $view               The view where is rendering (top|listing|activity)
     *
     * @return array
     */
    $display_options = apply_filters( 'gamipress_bp_user_details_display_options', $display_options, $user_id, $view );

    /**
     * Filter available to override the display options for a specific view
     * Available views are: top|listing|activity
     *
     * @since 1.4.2
     *
     * @param array     $display_options    Display options from settings
     * @param int       $user_id            The user ID
     *
     * @return array
     */
    $display_options = apply_filters( "gamipress_bp_user_details_{$view}_display_options", $display_options, $user_id );

    // Shorthand
    $a = $display_options;

    // Bail if nothing to show
    if( ! $a['show_points'] && ! $a['show_achievements'] && ! $a['show_ranks'] ) {
        return;
    }

    // On network wide active installs, we need to switch to main blog mostly for posts permalinks and thumbnails
    $blog_id = gamipress_switch_to_main_site_if_network_wide_active();

    ?><div class="gamipress-buddypress-user-details gamipress-buddypress-user-details-<?php echo $view; ?>"><?php

    // Points
    if( $a['show_points'] ) {
        gamipress_bp_user_points_display( $user_id, $a );
    }

    // Achievements
    if( $a['show_achievements'] ) {
        gamipress_bp_user_achievements_display( $user_id, $a );
    }

    // Ranks
    if( $a['show_ranks'] ) {
        gamipress_bp_user_ranks_display( $user_id, $a );
    }

    ?></div><?php

    // If switched to blog, return back to que current blog
    if( $blog_id !== get_current_blog_id() && is_multisite() ) {
        restore_current_blog();
    }
}

/**
 * Helper function to render the user points
 *
 * @since 1.4.2
 *
 * @param int       $user_id            The user ID
 * @param array     $display_options    The display options
 */
function gamipress_bp_user_points_display( $user_id, $display_options ) {

    // Shorthand
    $a = $display_options;

    // Setup points types vars
    $points_types = gamipress_get_points_types();
    $points_types_slugs = gamipress_get_points_types_slugs();

    // Get points display settings
    $points_types_to_show =  $a['points_types_to_show'];
    $thumbnail = (bool) $a['points_types_thumbnail'];
    $thumbnail_size = (int) $a['points_types_thumbnail_size'];
    $show_label = (bool)  $a['points_types_label'];

    // Parse thumbnail size
    $thumbnail_size = ( ( $thumbnail_size > 0 ) ? array( $thumbnail_size, $thumbnail_size ) : 'gamipress-points' );

    if( empty( $points_types_to_show ) ) {
        return;
    }

    ob_start(); ?>

    <div class="gamipress-buddypress-points">

        <?php foreach( $points_types_to_show as $points_type_to_show ) :

            // If points type not registered, skip
            if( ! in_array( $points_type_to_show, $points_types_slugs ) )
                continue;

            $points_type = $points_types[$points_type_to_show];
            $label_position = gamipress_get_points_type_label_position( $points_type_to_show );
            $user_points = gamipress_get_user_points( $user_id, $points_type_to_show ); ?>

            <div class="gamipress-buddypress-points-type gamipress-buddypress-<?php echo $points_type_to_show; ?>">

                <?php // The points thumbnail ?>
                <?php if( $thumbnail ) :
                    $points_thumbnail = gamipress_get_points_type_thumbnail( $points_type_to_show, $thumbnail_size ); ?>

                    <?php if( ! empty( $points_thumbnail ) ) : ?>
                        <span class="activity gamipress-buddypress-points-thumbnail gamipress-buddypress-<?php echo $points_type_to_show; ?>-thumbnail">
                            <?php echo $points_thumbnail; ?>
                        </span>
                    <?php endif; ?>

                <?php endif; ?>

                <?php // The points label (before) ?>
                <?php if( $show_label && $label_position === 'before' ) : ?>
                    <span class="activity gamipress-buddypress-points-label gamipress-buddypress-<?php echo $points_type_to_show; ?>-label">
                        <?php echo _n( $points_type['singular_name'], $points_type['plural_name'], $user_points ); ?>
                    </span>
                <?php endif; ?>

                <?php // The user points amount ?>
                <span class="activity gamipress-buddypress-user-points gamipress-buddypress-user-<?php echo $points_type_to_show; ?>">
                    <?php echo gamipress_format_amount( $user_points, $points_type_to_show ); ?>
                </span>

                <?php // The points label (after) ?>
                <?php if( $show_label && $label_position !== 'before' ) : ?>
                    <span class="activity gamipress-buddypress-points-label gamipress-buddypress-<?php echo $points_type_to_show; ?>-label">
                        <?php echo _n( $points_type['singular_name'], $points_type['plural_name'], $user_points ); ?>
                    </span>
                <?php endif; ?>

            </div>

        <?php endforeach; ?>
    </div>

    <?php $output = ob_get_clean();

    /**
     * Filter to override the user points output
     *
     * @since 1.4.6
     *
     * @param string    $output             The user points output
     * @param int       $user_id            The user ID
     * @param array     $display_options    The display options
     *
     * @return string
     */
    $output = apply_filters( 'gamipress_bp_user_points_display', $output, $user_id, $display_options );

    echo $output;
}

/**
 * Helper function to render the user achievements
 *
 * @since 1.4.2
 *
 * @param int       $user_id            The user ID
 * @param array     $display_options    The display options
 */
function gamipress_bp_user_achievements_display( $user_id, $display_options ) {

    // Shorthand
    $a = $display_options;

    // Setup achievement types vars
    $achievement_types = gamipress_get_achievement_types();
    $achievement_types_slugs = gamipress_get_achievement_types_slugs();

    // Get achievements display settings
    $achievement_types_to_show = $a['achievement_types_to_show'];
    $thumbnail = (bool) $a['achievement_types_thumbnail'];
    $thumbnail_size = (int) $a['achievement_types_thumbnail_size'];
    $show_title = (bool) $a['achievement_types_title'];
    $show_link = (bool) $a['achievement_types_link'];
    $show_label = (bool) $a['achievement_types_label'];
    $limit = (int) $a['achievement_types_limit'];

    // Parse thumbnail size
    $thumbnail_size = ( ( $thumbnail_size > 0 ) ? array( $thumbnail_size, $thumbnail_size ) : 'gamipress-achievement' );

    if( empty( $achievement_types_to_show ) ) {
        return;
    }

    ob_start(); ?>

    <div class="gamipress-buddypress-achievements">

        <?php foreach( $achievement_types_to_show as $achievement_type_to_show ) :

            // If achievements type not registered, skip
            if( ! in_array( $achievement_type_to_show, $achievement_types_slugs ) )
                continue;

            $achievement_type = $achievement_types[$achievement_type_to_show];
            $user_achievements = gamipress_get_user_achievements( array(
                'user_id' => $user_id,
                'achievement_type' => $achievement_type_to_show,
                'groupby' => 'achievement_id',
                'limit' => $limit,
            ) );

            // If user has not earned any achievements of this type, skip
            if( empty( $user_achievements ) ) {
                continue;
            } ?>

            <div class="gamipress-buddypress-user-achievements">

                <?php // The achievement type label ?>
                <?php if( $show_label ) : ?>
                    <span class="activity gamipress-buddypress-achievement-type-label gamipress-buddypress-<?php echo $achievement_type_to_show; ?>-label">
                        <?php echo $achievement_type['plural_name']; ?>:
                    </span>
                <?php endif; ?>

                <?php foreach( $user_achievements as $user_achievement ) :
                    // Skip achievement if not exists
                    if( ! gamipress_get_post( $user_achievement->ID ) ) {
                        continue;
                    } ?>

                    <span id="gamipress-buddypress-achievement-<?php echo $user_achievement->ID; ?>" class="gamipress-buddypress-achievement gamipress-buddypress-<?php echo $achievement_type_to_show; ?>">

                        <?php // The achievement thumbnail ?>
                        <?php if( $thumbnail ) :
                            $achievement_thumbnail = gamipress_get_achievement_post_thumbnail( $user_achievement->ID, $thumbnail_size ); ?>

                            <?php if( ! empty( $achievement_thumbnail ) ) : ?>

                                <?php // The achievement link ?>
                                <?php if( $show_link ) : ?>
                                    <a href="<?php echo get_permalink( $user_achievement->ID ); ?>" title="<?php echo get_the_title( $user_achievement->ID ); ?>" class="activity gamipress-buddypress-achievement-thumbnail gamipress-buddypress-<?php echo $achievement_type_to_show; ?>-thumbnail">
                                        <?php echo $achievement_thumbnail; ?>
                                    </a>
                                <?php else : ?>
                                    <span title="<?php echo get_the_title( $user_achievement->ID ); ?>" class="activity gamipress-buddypress-achievement-thumbnail gamipress-buddypress-<?php echo $achievement_type_to_show; ?>-thumbnail">
                                        <?php echo $achievement_thumbnail; ?>
                                    </span>
                                <?php endif; ?>

                            <?php endif; ?>

                        <?php endif; ?>

                        <?php // The achievement title ?>
                        <?php if( $show_title ) :
                            $achievement_title = get_the_title( $user_achievement->ID ); ?>

                            <?php if( ! empty( $achievement_title ) ) : ?>

                                <?php // The achievement link ?>
                                <?php if( $show_link ) : ?>
                                    <a href="<?php echo get_permalink( $user_achievement->ID ); ?>" title="<?php echo get_the_title( $user_achievement->ID ); ?>" class="gamipress-buddypress-achievement-title gamipress-buddypress-<?php echo $achievement_type_to_show; ?>-title">
                                        <?php echo get_the_title( $user_achievement->ID ); ?>
                                    </a>
                                <?php else : ?>
                                    <span class="activity gamipress-buddypress-achievement-title gamipress-buddypress-<?php echo $achievement_type_to_show; ?>-title">
                                        <?php echo get_the_title( $user_achievement->ID ); ?>
                                    </span>
                                <?php endif; ?>

                            <?php endif; ?>

                        <?php endif; ?>

                    </span><!-- .gamipress-buddypress-achievement -->

                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>

    </div>

    <?php $output = ob_get_clean();

    /**
     * Filter to override the user achievements output
     *
     * @since 1.4.6
     *
     * @param string    $output             The user achievements output
     * @param int       $user_id            The user ID
     * @param array     $display_options    The display options
     *
     * @return string
     */
    $output = apply_filters( 'gamipress_bp_user_achievements_display', $output, $user_id, $display_options );

    echo $output;

}

/**
 * Helper function to render the user ranks
 *
 * @since 1.4.2
 *
 * @param int       $user_id            The user ID
 * @param array     $display_options    The display options
 */
function gamipress_bp_user_ranks_display( $user_id, $display_options ) {

    // Shorthand
    $a = $display_options;

    // Setup rank types vars
    $rank_types = gamipress_get_rank_types();
    $rank_types_slugs = gamipress_get_rank_types_slugs();

    // Get ranks display settings
    $rank_types_to_show = $a['rank_types_to_show'];
    $thumbnail = (bool) $a['rank_types_thumbnail'];
    $thumbnail_size = (int) $a['rank_types_thumbnail_size'];
    $show_title = (bool) $a['rank_types_title'];
    $show_link = (bool) $a['rank_types_link'];
    $show_label = (bool) $a['rank_types_label'];

    // Parse thumbnail size
    $thumbnail_size = ( ( $thumbnail_size > 0 ) ? array( $thumbnail_size, $thumbnail_size ) : 'gamipress-rank' );

    if( empty( $rank_types_to_show ) ) {
        return;
    }

    ob_start(); ?>

    <div class="gamipress-buddypress-ranks">

        <?php foreach( $rank_types_to_show as $rank_type_to_show ) :

            // If rank type not registered, skip
            if( ! in_array( $rank_type_to_show, $rank_types_slugs ) )
                continue;

            $rank_type = $rank_types[$rank_type_to_show];
            $user_rank = gamipress_get_user_rank( $user_id, $rank_type_to_show ); ?>

            <div class="gamipress-buddypress-rank gamipress-buddypress-<?php echo $rank_type_to_show; ?>">

                <?php // The rank type label ?>
                <?php if( $show_label ) : ?>
                    <span class="activity gamipress-buddypress-rank-label gamipress-buddypress-<?php echo $rank_type_to_show; ?>-label">
                        <?php echo $rank_type['singular_name']; ?>:
                    </span>
                <?php endif; ?>

                <?php // The rank thumbnail ?>
                <?php if( $thumbnail ) :
                    $rank_thumbnail = gamipress_get_rank_post_thumbnail( $user_rank->ID, $thumbnail_size ); ?>

                    <?php if( ! empty( $rank_thumbnail ) ) : ?>

                        <?php // The rank link ?>
                        <?php if( $show_link ) : ?>
                            <a href="<?php echo get_permalink( $user_rank->ID ); ?>" title="<?php echo $user_rank->post_title; ?>" class="activity gamipress-buddypress-rank-thumbnail gamipress-buddypress-<?php echo $rank_type_to_show; ?>-thumbnail">
                                <?php echo gamipress_get_rank_post_thumbnail( $user_rank->ID, $thumbnail_size ); ?>
                            </a>
                        <?php else : ?>
                            <span title="<?php echo $user_rank->post_title; ?>" class="activity gamipress-buddypress-rank-thumbnail gamipress-buddypress-<?php echo $rank_type_to_show; ?>-thumbnail">
                                <?php echo gamipress_get_rank_post_thumbnail( $user_rank->ID, $thumbnail_size ); ?>
                            </span>
                        <?php endif; ?>

                    <?php endif; ?>

                <?php endif; ?>

                <?php // The rank title ?>
                <?php if( $show_title && ! empty( $user_rank->post_title ) ) : ?>

                    <?php // The rank link ?>
                    <?php if( $show_link ) : ?>
                        <a href="<?php echo get_permalink( $user_rank->ID ); ?>" title="<?php echo $user_rank->post_title; ?>" class="activity gamipress-buddypress-rank-title gamipress-buddypress-<?php echo $rank_type_to_show; ?>-title">
                            <?php echo $user_rank->post_title; ?>
                        </a>
                    <?php else : ?>
                        <span class="activity gamipress-buddypress-rank-title gamipress-buddypress-<?php echo $rank_type_to_show; ?>-title">
                            <?php echo $user_rank->post_title; ?>
                        </span>
                    <?php endif; ?>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </div>

    <?php $output = ob_get_clean();

    /**
     * Filter to override the user ranks output
     *
     * @since 1.4.6
     *
     * @param string    $output             The user ranks output
     * @param int       $user_id            The user ID
     * @param array     $display_options    The display options
     *
     * @return string
     */
    $output = apply_filters( 'gamipress_bp_user_ranks_display', $output, $user_id, $display_options );

    echo $output;
}

/**
 * Override the achievement earners list to use BP details
 *
 * @since    1.0.1
 * @updated  1.1.1 Also hook to gamipress_get_rank_earners_list_user for ranks support
 *
 * @param string  $user_content The list item output for the given user
 * @param integer $user_id      The given user's ID
 *
 * @return string               The updated user output
 */
function gamipress_bp_override_earners( $user_content, $user_id ) {

    $user = new BP_Core_User( $user_id );

    return '<li><a href="' .  $user->user_url . '">' . $user->avatar_mini . '</a></li>';

}
add_filter( 'gamipress_get_achievement_earners_list_user', 'gamipress_bp_override_earners', 10, 2 );
add_filter( 'gamipress_get_rank_earners_list_user', 'gamipress_bp_override_earners', 10, 2 );

/**
 * Helper function to retrieve the points types configured at members screen
 *
 * @since  1.0.8
 *
 * @return array
 */
function gamipress_bp_members_get_points_types() {

    $points_types = array();

    $points_types_slugs = gamipress_get_points_types_slugs();

    $points_types_to_show = gamipress_bp_get_option( 'members_points_types', array() );

    foreach( $points_types_to_show as $points_type_slug ) {

        if( ! in_array( $points_type_slug, $points_types_slugs ) ) {
            continue;
        }

        $points_types[] = $points_type_slug;
    }

    return $points_types;

}

/**
 * Helper function to retrieve the points types configured at members screen
 *
 * @since  1.0.8
 *
 * @return array
 */
function gamipress_bp_tab_get_points_types() {

    $points_types = array();

    $points_types_slugs = gamipress_get_points_types_slugs();

    $points_types_to_show = gamipress_bp_get_option( 'tab_points_types', array() );

    foreach( $points_types_to_show as $points_type_slug ) {

        if( ! in_array( $points_type_slug, $points_types_slugs ) ) {
            continue;
        }

        $points_types[] = $points_type_slug;
    }

    return $points_types;

}

/**
 * Helper function to retrieve the achievement types configured at members screen
 *
 * @since  1.0.5
 *
 * @return array
 */
function gamipress_bp_members_get_achievements_types() {

    $achievements_types = array();

    $achievement_types_slugs = gamipress_get_achievement_types_slugs();

    $achievements_types_to_show = gamipress_bp_get_option( 'members_achievements_types', array() );

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
 * Helper function to retrieve the achievement types configured on profile tabs
 *
 * @since  1.0.5
 *
 * @return array
 */
function gamipress_bp_tab_get_achievements_types() {

    $achievements_types = array();

    $achievement_types_slugs = gamipress_get_achievement_types_slugs();

    $achievements_types_to_show = gamipress_bp_get_option( 'tab_achievements_types', array() );

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
 * Helper function to retrieve the rank types configured at members screen
 *
 * @since  1.1.1
 *
 * @return array
 */
function gamipress_bp_members_get_ranks_types() {

    $ranks_types = array();

    $rank_types_slugs = gamipress_get_rank_types_slugs();

    $ranks_types_to_show = gamipress_bp_get_option( 'members_ranks_types', array() );

    foreach( $ranks_types_to_show as $rank_type_slug ) {

        // Skip if not registered
        if( ! in_array( $rank_type_slug, $rank_types_slugs ) ) {
            continue;
        }

        $ranks_types[] = $rank_type_slug;
    }

    return $ranks_types;

}

/**
 * Helper function to retrieve the rank types configured on profile tabs
 *
 * @since  1.1.1
 *
 * @return array
 */
function gamipress_bp_tab_get_ranks_types() {

    $ranks_types = array();

    $rank_types_slugs = gamipress_get_rank_types_slugs();

    $ranks_types_to_show = gamipress_bp_get_option( 'tab_ranks_types', array() );

    foreach( $ranks_types_to_show as $rank_type_slug ) {

        // Skip if not registered
        if( ! in_array( $rank_type_slug, $rank_types_slugs ) ) {
            continue;
        }

        $ranks_types[] = $rank_type_slug;
    }

    return $ranks_types;

}

/**
 * Display the GamiPress email settings under the member Account > Email Preferences
 *
 * @since  1.5.2
 */
function gamipress_bp_notification_settings() {

    if( ! (bool) gamipress_bp_get_option( 'email_settings', false ) ) {
        return;
    }

    $groups = ( (bool) gamipress_bp_get_option( 'email_groups', false ) ? 'yes' : 'no' );
    $types = ( (bool) gamipress_bp_get_option( 'email_types', false ) ? 'yes' : 'no' );

    echo do_shortcode( '[gamipress_email_settings current_user="no" user_id="' . bp_displayed_user_id() . '" groups="' . $groups . '" types="' . $types . '"]' );
}
add_action( 'bp_notification_settings', 'gamipress_bp_notification_settings', 999 );
