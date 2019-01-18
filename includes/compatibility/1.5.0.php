<?php
/**
 * GamiPress 1.5.0 compatibility functions
 *
 * @package     GamiPress\1.5.0
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.5.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Filter our step titles to link to achievements and achievement type archives
 *
 * @deprecated
 *
 * @see gamipress_format_requirement_title_with_post_link()
 *
 * @since  1.0.0
 *
 * @param  string $title Our step title
 * @param  object $step  Our step's post object
 *
 * @return string        Our potentially updated title
 */
function gamipress_step_link_title_to_achievement( $title = '', $step = null ) {

    // Grab our step requirements
    $step_requirements = gamipress_get_step_requirements( $step->ID );

    // Setup a URL to link to a specific achievement or an achievement type
    if ( ! empty( $step_requirements['achievement_post'] ) )
        $url = get_permalink( $step_requirements['achievement_post'] );
    // elseif ( ! empty( $step_requirements['achievement_type'] ) )
    //  $url = get_post_type_archive_link( $step_requirements['achievement_type'] );

    // If we have a URL, update the title to link to it
    if ( isset( $url ) && ! empty( $url ) )
        $title = '<a href="' . esc_url( $url ) . '">' . $title . '</a>';

    return $title;
}
//add_filter( 'gamipress_step_title_display', 'gamipress_step_link_title_to_achievement', 10, 2 );

/**
 * Filter our points awards titles to link to achievements and achievement type archives
 *
 * @deprecated
 *
 * @see gamipress_format_requirement_title_with_post_link()
 *
 * @since  1.0.0
 *
 * @param  string $title 			Our points award title
 * @param  object $points_award  	Our points award's post object
 * @return string        			Our potentially updated title
 */
function gamipress_points_award_link_title_to_achievement( $title = '', $points_award = null ) {

    // Grab our points award requirements
    $points_award_requirements = gamipress_get_points_award_requirements( $points_award->ID );

    // Setup a URL to link to a specific achievement or an achievement type
    if ( ! empty( $points_award_requirements['achievement_post'] ) )
        $url = get_permalink( $points_award_requirements['achievement_post'] );
    // elseif ( ! empty( $points_award_requirements['achievement_type'] ) )
    //  $url = get_post_type_archive_link( $points_award_requirements['achievement_type'] );

    // If we have a URL, update the title to link to it
    if ( isset( $url ) && ! empty( $url ) )
        $title = '<a href="' . esc_url( $url ) . '">' . $title . '</a>';

    return $title;
}
//add_filter( 'gamipress_points_award_title_display', 'gamipress_points_award_link_title_to_achievement', 10, 2 );

/**
 * Filter our points deducts titles to link to achievements and achievement type archives
 *
 * @deprecated
 *
 * @see gamipress_format_requirement_title_with_post_link()
 *
 * @since  1.3.7
 *
 * @param  string $title 			Our points deduct title
 * @param  object $points_deduct  	Our points deduct's post object
 * @return string        			Our potentially updated title
 */
function gamipress_points_deduct_link_title_to_achievement( $title = '', $points_deduct = null ) {

    // Grab our points deduct requirements
    $points_deduct_requirements = gamipress_get_points_deduct_requirements( $points_deduct->ID );

    // Setup a URL to link to a specific achievement or an achievement type
    if ( ! empty( $points_deduct_requirements['achievement_post'] ) )
        $url = get_permalink( $points_deduct_requirements['achievement_post'] );
    // elseif ( ! empty( $points_deduct_requirements['achievement_type'] ) )
    //  $url = get_post_type_archive_link( $points_deduct_requirements['achievement_type'] );

    // If we have a URL, update the title to link to it
    if ( isset( $url ) && ! empty( $url ) )
        $title = '<a href="' . esc_url( $url ) . '">' . $title . '</a>';

    return $title;
}
//add_filter( 'gamipress_points_deduct_title_display', 'gamipress_points_deduct_link_title_to_achievement', 10, 2 );

/**
 * Filter our step titles to link to achievements and achievement type archives
 *
 * @deprecated
 *
 * @see gamipress_format_requirement_title_with_post_link()
 *
 * @since  1.3.1
 *
 * @param  string $title Our rank requirement title
 * @param  object $rank_requirement  Our rank requirement's post object
 *
 * @return string        Our potentially updated title
 */
function gamipress_rank_requirement_link_title_to_achievement( $title = '', $rank_requirement = null ) {

    // Grab our rank requirement requirements
    $requirements = gamipress_get_rank_requirement_requirements( $rank_requirement->ID );

    // Setup a URL to link to a specific achievement or an achievement type
    if ( ! empty( $requirements['achievement_post'] ) )
        $url = get_permalink( $requirements['achievement_post'] );
    // elseif ( ! empty( $requirements['achievement_type'] ) )
    //  $url = get_post_type_archive_link( $requirements['achievement_type'] );

    // If we have a URL, update the title to link to it
    if ( isset( $url ) && ! empty( $url ) )
        $title = '<a href="' . esc_url( $url ) . '">' . $title . '</a>';

    return $title;
}
//add_filter( 'gamipress_rank_requirement_title_display', 'gamipress_rank_requirement_link_title_to_achievement', 10, 2 );