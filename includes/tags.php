<?php
/**
 * Tags
 *
 * @package     GamiPress\Tags
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.8.6
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get an array of email pattern tags
 *
 * @since 1.3.0
 *
 * @return array The registered email pattern tags
 */
function gamipress_get_pattern_tags() {

    $pattern_tags = array();

    // Site tags
    $pattern_tags[] = '<strong>' . __( 'Site Tags', 'gamipress' ) . '</strong>';

    $pattern_tags = array_merge( $pattern_tags, apply_filters( 'gamipress_site_pattern_tags', array(
        '{site_title}'          =>  __( 'Site name.', 'gamipress' ),
        '{site_link}'           =>  __( 'Link to the site with site name as text.', 'gamipress' ),
    ) ) );

    // User Tags
    $pattern_tags[] = '<strong>' . __( 'User Tags', 'gamipress' ) . '</strong>';

    $pattern_tags = array_merge( $pattern_tags, apply_filters( 'gamipress_user_pattern_tags', array(
        '{user_id}'             =>  __( 'User ID (useful for shortcodes that user ID can be passed as attribute).', 'gamipress' ),
        '{user}'                =>  __( 'User display name.', 'gamipress' ),
        '{user_first}'          =>  __( 'User first name.', 'gamipress' ),
        '{user_last}'           =>  __( 'User last name.', 'gamipress' ),
        '{user_email}'          =>  __( 'User email.', 'gamipress' ),
        '{user_username}'       =>  __( 'User username.', 'gamipress' ),
    ) ) );

    return apply_filters( 'gamipress_pattern_tags', $pattern_tags );

}

/**
 * Get an array of email pattern tags used on achievement earned email
 *
 * @since 1.3.0
 *
 * @return array The registered achievement earned email pattern tags
 */
function gamipress_get_achievement_earned_pattern_tags() {

    $pattern_tags = gamipress_get_pattern_tags();

    $pattern_tags[] = '<strong>' . __( 'Achievement Tags', 'gamipress' ) . '</strong>';

    return apply_filters( 'gamipress_achievement_earned_pattern_tags', array_merge( $pattern_tags, array(
        '{achievement_id}'              =>  __(  'Achievement ID (useful for shortcodes that achievement ID can be passed as attribute).', 'gamipress' ),
        '{achievement_title}'           =>  __(  'Achievement title.', 'gamipress' ),
        '{achievement_url}'             =>  __(  'URL to the achievement.', 'gamipress' ),
        '{achievement_link}'            =>  __(  'Link to the achievement with the achievement title as text.', 'gamipress' ),
        '{achievement_excerpt}'         =>  __(  'Achievement excerpt.', 'gamipress' ),
        '{achievement_image}'           =>  __(  'Achievement featured image.', 'gamipress' ),
        '{achievement_steps}'           =>  __(  'Achievement steps.', 'gamipress' ),
        '{achievement_congratulations}' =>  __(  'Achievement congratulations text.', 'gamipress' ),
        '{achievement_type}'            =>  __(  'Type of the achievement.', 'gamipress' ),
    ) ) );

}

/**
 * Get an array of email pattern tags used on step completed email
 *
 * @since 1.3.0
 *
 * @return array The registered step completed email pattern tags
 */
function gamipress_get_step_completed_pattern_tags() {

    $pattern_tags = gamipress_get_pattern_tags();

    $pattern_tags[] = '<strong>' . __( 'Step Tags', 'gamipress' ) . '</strong>';

    return apply_filters( 'gamipress_step_completed_pattern_tags', array_merge( $pattern_tags, array(
        '{label}'                       =>  __(  'Step label.', 'gamipress' ),
        '{achievement_id}'              =>  __(  'Step achievement ID (useful for shortcodes that achievement ID can be passed as attribute).', 'gamipress' ),
        '{achievement_title}'           =>  __(  'Step achievement title.', 'gamipress' ),
        '{achievement_url}'             =>  __(  'URL to the step achievement.', 'gamipress' ),
        '{achievement_link}'            =>  __(  'Link to the step achievement with the achievement title as text.', 'gamipress' ),
        '{achievement_excerpt}'         =>  __(  'Step achievement excerpt.', 'gamipress' ),
        '{achievement_image}'           =>  __(  'Step achievement featured image.', 'gamipress' ),
        '{achievement_steps}'           =>  __(  'Step achievement steps.', 'gamipress' ),
        '{achievement_congratulations}' =>  __(  'Step achievement congratulations text.', 'gamipress' ),
        '{achievement_type}'            =>  __(  'Type of the step achievement.', 'gamipress' ),
    ) ) );

}

/**
 * Get an array of email pattern tags used on points award completed email
 *
 * @since 1.3.0
 *
 * @return array The registered points award completed email pattern tags
 */
function gamipress_get_points_award_completed_pattern_tags() {

    $pattern_tags = gamipress_get_pattern_tags();

    $pattern_tags[] = '<strong>' . __( 'Points Award Tags', 'gamipress' ) . '</strong>';

    return apply_filters( 'gamipress_points_award_completed_pattern_tags', array_merge( $pattern_tags, array(
        '{label}'           =>  __( 'Points award label.', 'gamipress' ),
        '{points}'          =>  __( 'The amount of points earned.', 'gamipress' ),
        '{points_balance}'  =>  __( 'The full amount of points user has earned until this date.', 'gamipress' ),
        '{points_type}'     =>  __( 'The points award points type. Singular or plural is based on the amount of points earned.', 'gamipress' ),
        '{points_image}'     =>  __( 'The points type featured image.', 'gamipress' ),
    ) ) );

}

/**
 * Get an array of email pattern tags used on points deduct completed email
 *
 * @since 1.3.7
 *
 * @return array The registered points deduct completed email pattern tags
 */
function gamipress_get_points_deduct_completed_pattern_tags() {

    $pattern_tags = gamipress_get_pattern_tags();

    $pattern_tags[] = '<strong>' . __( 'Points Deduction Tags', 'gamipress' ) . '</strong>';

    return apply_filters( 'gamipress_points_deduct_completed_pattern_tags', array_merge( $pattern_tags, array(
        '{label}'           =>  __( 'Points deduction label.', 'gamipress' ),
        '{points}'          =>  __( 'The amount of points deducted.', 'gamipress' ),
        '{points_balance}'  =>  __( 'The full amount of points user has earned until this date.', 'gamipress' ),
        '{points_type}'     =>  __( 'The points deduction points type. Singular or plural is based on the amount of points deducted.', 'gamipress' ),
        '{points_image}'     =>  __( 'The points type featured image.', 'gamipress' ),
    ) ) );

}

/**
 * Get an array of email pattern tags used on rank earned email
 *
 * @since 1.3.1
 *
 * @return array The registered rank earned email pattern tags
 */
function gamipress_get_rank_earned_pattern_tags() {

    $pattern_tags = gamipress_get_pattern_tags();

    $pattern_tags[] = '<strong>' . __( 'Rank Tags', 'gamipress' ) . '</strong>';

    return apply_filters( 'gamipress_rank_earned_pattern_tags', array_merge( $pattern_tags, array(
        '{rank_id}'                 =>  __(  'Rank ID (useful for shortcodes that rank ID can be passed as attribute).', 'gamipress' ),
        '{rank_title}'              =>  __(  'Rank title.', 'gamipress' ),
        '{rank_url}'                =>  __(  'URL to the rank.', 'gamipress' ),
        '{rank_link}'               =>  __(  'Link to the rank with the rank title as text.', 'gamipress' ),
        '{rank_excerpt}'            =>  __(  'Rank excerpt.', 'gamipress' ),
        '{rank_image}'              =>  __(  'Rank featured image.', 'gamipress' ),
        '{rank_requirements}'       =>  __(  'Rank requirements.', 'gamipress' ),
        '{rank_congratulations}'    =>  __(  'Rank congratulations text.', 'gamipress' ),
        '{rank_type}'               =>  __(  'Type of the rank.', 'gamipress' ),
    ) ) );

}

/**
 * Get an array of email pattern tags used on rank requirement completed email
 *
 * @since 1.3.1
 *
 * @return array The registered rank requirement completed email pattern tags
 */
function gamipress_get_rank_requirement_completed_pattern_tags() {

    $pattern_tags = gamipress_get_pattern_tags();

    $pattern_tags[] = '<strong>' . __( 'Rank Requirement Tags', 'gamipress' ) . '</strong>';

    return apply_filters( 'gamipress_rank_requirement_completed_pattern_tags', array_merge( $pattern_tags, array(
        '{label}'                   =>  __(  'Requirement label.', 'gamipress' ),
        '{rank_id}'                 =>  __(  'Requirement rank ID (useful for shortcodes that rank ID can be passed as attribute).', 'gamipress' ),
        '{rank_title}'              =>  __(  'Requirement rank title.', 'gamipress' ),
        '{rank_url}'                =>  __(  'URL to the requirement rank.', 'gamipress' ),
        '{rank_link}'               =>  __(  'Link to the requirement rank with the rank title as text.', 'gamipress' ),
        '{rank_excerpt}'            =>  __(  'Requirement rank excerpt.', 'gamipress' ),
        '{rank_image}'              =>  __(  'Requirement rank featured image.', 'gamipress' ),
        '{rank_requirements}'       =>  __(  'Requirement rank requirements.', 'gamipress' ),
        '{rank_congratulations}'    =>  __(  'Requirement rank congratulations text.', 'gamipress' ),
        '{rank_type}'               =>  __(  'Type of the rank.', 'gamipress' ),
    ) ) );

}

/**
 * Get a string with the desired email pattern tags html markup
 *
 * @since 1.3.0
 *
 * @param string $pattern
 *
 * @return string Log pattern tags html markup
 */
function gamipress_get_pattern_tags_html( $pattern = '' ) {

    if( $pattern === 'achievement_earned' ) {
        $pattern_tags = gamipress_get_achievement_earned_pattern_tags();
    } else if( $pattern === 'step_completed' ) {
        $pattern_tags = gamipress_get_step_completed_pattern_tags();
    } else if( $pattern === 'points_award_completed' ) {
        $pattern_tags = gamipress_get_points_award_completed_pattern_tags();
    } else if( $pattern === 'points_deduct_completed' ) {
        $pattern_tags = gamipress_get_points_deduct_completed_pattern_tags();
    } else if( $pattern === 'rank_earned' ) {
        $pattern_tags = gamipress_get_rank_earned_pattern_tags();
    } else if( $pattern === 'rank_requirement_completed' ) {
        $pattern_tags = gamipress_get_rank_requirement_completed_pattern_tags();
    } else {
        $pattern_tags = gamipress_get_pattern_tags();
    }

    $output = ' <a href="" class="gamipress-pattern-tags-list-toggle" data-show-text="' . __( 'Show tags', 'gamipress' ) . '" data-hide-text="' . __( 'Show tags', 'gamipress' ) . '">' . __( 'Show tags', 'gamipress' ) . '</a>';
    $output .= '<ul class="gamipress-pattern-tags-list" style="display: none;">';

    foreach( $pattern_tags as $tag => $description ) {

        if( is_numeric( $tag ) ) {
            $output .= "<li id='{$tag}'>{$description}</li>";
        } else {
            $attr_id = 'tag-' . str_replace( array( '{', '}', '_' ), array( '', '', '-' ), $tag );

            $output .= "<li id='{$attr_id}'><code>{$tag}</code> - {$description}</li>";
        }
    }

    $output .= '</ul>';

    return $output;

}

/**
 * Site tags replacements
 *
 * @since 1.8.6
 *
 * @return array
 */
function gamipress_get_site_tags_replacements() {

    $replacements = array();

    // Setup site replacements
    $replacements['{site_title}']   = get_bloginfo( 'name' );
    $replacements['{site_link}']    = '<a href="' . esc_url( home_url() ) . '">' . get_bloginfo( 'name' ) . '</a>';

    return $replacements;

}

/**
 * User tags replacements
 *
 * @since 1.8.6
 *
 * @param int       $user_id The user to get tags values from
 *
 * @return array
 */
function gamipress_get_user_tags_replacements( $user_id ) {

    $replacements = array();

    // Setup user replacements
    $user_id = absint( $user_id );
    $user = ( $user_id !== 0 ? get_userdata( $user_id ) : false );

    $replacements['{user_id}']          =  ( $user ? $user->ID : '' );
    $replacements['{user}']             =  ( $user ? $user->display_name : '' );
    $replacements['{user_first}']       =  ( $user ? $user->first_name : '' );
    $replacements['{user_last}']        =  ( $user ? $user->last_name : '' );
    $replacements['{user_email}']       =  ( $user ? $user->user_email : '' );
    $replacements['{user_username}']    =  ( $user ? $user->user_login : '' );

    return $replacements;

}

/**
 * Achievement earned tags replacements
 *
 * @since 1.8.6
 *
 * @param int       $achievement_id The achievement to get tags values from
 * @param int       $user_id        The user to get tags values from
 *
 * @return array
 */
function gamipress_get_achievement_earned_tags_replacements( $achievement_id, $user_id ) {

    $replacements = array();

    // Get site and user tags replacements
    $replacements = array_merge( $replacements, gamipress_get_site_tags_replacements() );
    $replacements = array_merge( $replacements, gamipress_get_user_tags_replacements( $user_id ) );

    // Get the achievement post object
    $achievement = gamipress_get_post( $achievement_id );

    if( $achievement ) {

        $achievement_types = gamipress_get_achievement_types();
        $achievement_type = $achievement_types[$achievement->post_type];

        $achievement_steps_html = '';

        $steps = gamipress_get_achievement_steps( $achievement->ID );

        if( is_array( $steps ) && count( $steps ) ) {

            $list_tag = gamipress_is_achievement_sequential( $achievement->ID ) ? 'ol' : 'ul';

            $achievement_steps_html .= "<{$list_tag}>";

            foreach( $steps as $step ) {
                // check if user has earned this Achievement, and add an 'earned' class
                $earned = gamipress_get_earnings_count( array(
                        'user_id' => absint( $user_id ),
                        'post_id' => absint( $step->ID ),
                        'since' => absint( gamipress_achievement_last_user_activity( $achievement->ID, $user_id ) )
                    ) ) > 0;

                $title = $step->post_title;
                $url = gamipress_get_post_meta( $step->ID, '_gamipress_url' );

                $achievement_steps_html .= '<li style="' . ( $earned ? 'text-decoration: line-through;' : '' ) . '">'
                        . ( ! empty( $url ) ? '<a href="' . $url. '">' : '' )
                        . $title
                        . ( ! empty( $url ) ? '</a>' : '' )
                    . '</li>';
            }

            $achievement_steps_html .= "</{$list_tag}>";
        }

        $replacements['{achievement_id}'] = $achievement->ID;
        $replacements['{achievement_title}'] = $achievement->post_title;
        $replacements['{achievement_url}'] = get_the_permalink( $achievement->ID );
        $replacements['{achievement_link}'] = sprintf( '<a href="%s" title="%s">%s</a>', $replacements['{achievement_url}'], $replacements['{achievement_title}'], $replacements['{achievement_title}'] );
        $replacements['{achievement_excerpt}'] = $achievement->post_excerpt;
        $replacements['{achievement_image}'] = gamipress_get_achievement_post_thumbnail( $achievement->ID );
        $replacements['{achievement_steps}'] = $achievement_steps_html;
        $replacements['{achievement_type}'] = $achievement_type['singular_name'];
        $replacements['{achievement_congratulations}'] = gamipress_get_post_meta( $achievement->ID, '_gamipress_congratulations_text' );

    }

    return $replacements;

}

/**
 * Step completed tags replacements
 *
 * @since 1.8.6
 *
 * @param int       $step_id The step to get tags values from
 * @param int       $user_id The user to get tags values from
 *
 * @return array
 */
function gamipress_get_step_completed_tags_replacements( $step_id, $user_id ) {

    $replacements = array();

    // Get site and user tags replacements
    $replacements = array_merge( $replacements, gamipress_get_site_tags_replacements() );
    $replacements = array_merge( $replacements, gamipress_get_user_tags_replacements( $user_id ) );

    // Get the step post object
    $step = gamipress_get_post( $step_id );

    if( $step ) {
        $replacements['{label}'] = $step->post_title;

        // Get the step achievement to parse their tags
        $achievement = gamipress_get_step_achievement( $step->ID );

        if( $achievement ) {
            $replacements = array_merge( $replacements, gamipress_get_achievement_earned_tags_replacements( $achievement->ID, $user_id ) );
        }
    }

    return $replacements;

}

/**
 * Points award completed tags replacements
 *
 * @since 1.8.6
 *
 * @param int       $points_award_id    The points award to get tags values from
 * @param int       $user_id            The user to get tags values from
 *
 * @return array
 */
function gamipress_get_points_award_completed_tags_replacements( $points_award_id, $user_id ) {

    $replacements = array();

    // Get site and user tags replacements
    $replacements = array_merge( $replacements, gamipress_get_site_tags_replacements() );
    $replacements = array_merge( $replacements, gamipress_get_user_tags_replacements( $user_id ) );

    // Get the points award post object
    $points_award = gamipress_get_post( $points_award_id );

    if( $points_award ) {
        $replacements['{label}'] = $points_award->post_title;

        // Get the points type to allow specific points type template
        $points_type = gamipress_get_points_award_points_type( $points_award->ID );

        if( $points_type ) {

            // Setup vars
            $points = absint( gamipress_get_post_meta( $points_award->ID, '_gamipress_points' ) );
            $singular = $points_type->post_title;
            $plural = gamipress_get_post_meta( $points_type->ID, '_gamipress_plural_name' );
            $points_balance = gamipress_get_user_points( $user_id, $points_type->post_name );

            $replacements['{points}'] = gamipress_format_amount( $points, $points_type->post_name );
            $replacements['{points_balance}'] = $points_balance;
            $replacements['{points_type}'] = _n( $singular, $plural, $points );
            $replacements['{points_image}'] = gamipress_get_points_type_thumbnail( $points_type->ID );

        }

    }

    return $replacements;

}

/**
 * Points deduct completed tags replacements
 *
 * @since 1.8.6
 *
 * @param int       $points_deduct_id       The points deduct to get tags values from
 * @param int       $user_id                The user to get tags values from
 *
 * @return array
 */
function gamipress_get_points_deduct_completed_tags_replacements( $points_deduct_id, $user_id ) {

    $replacements = array();

    // Get site and user tags replacements
    $replacements = array_merge( $replacements, gamipress_get_site_tags_replacements() );
    $replacements = array_merge( $replacements, gamipress_get_user_tags_replacements( $user_id ) );

    // Get the points deduct post object
    $points_deduct = gamipress_get_post( $points_deduct_id );

    if( $points_deduct ) {
        $replacements['{label}'] = $points_deduct->post_title;

        // Get the points type to allow specific points type template
        $points_type = gamipress_get_points_deduct_points_type( $points_deduct->ID );

        if( $points_type ) {

            // Setup vars
            $points = absint( gamipress_get_post_meta( $points_deduct->ID, '_gamipress_points' ) );
            $singular = $points_type->post_title;
            $plural = gamipress_get_post_meta( $points_type->ID, '_gamipress_plural_name' );
            $points_balance = gamipress_get_user_points( $user_id, $points_type->post_name );

            $replacements['{points}'] = gamipress_format_amount( $points, $points_type->post_name );
            $replacements['{points_balance}'] = $points_balance;
            $replacements['{points_type}'] = _n( $singular, $plural, $points );
            $replacements['{points_image}'] = gamipress_get_points_type_thumbnail( $points_type->ID );

        }

    }

    return $replacements;

}

/**
 * Rank earned tags replacements
 *
 * @since 1.8.6
 *
 * @param int       $rank_id The rank to get tags values from
 * @param int       $user_id The user to get tags values from
 *
 * @return array
 */
function gamipress_get_rank_earned_tags_replacements( $rank_id, $user_id ) {

    $replacements = array();

    // Get site and user tags replacements
    $replacements = array_merge( $replacements, gamipress_get_site_tags_replacements() );
    $replacements = array_merge( $replacements, gamipress_get_user_tags_replacements( $user_id ) );

    // Get the rank post object
    $rank = gamipress_get_post( $rank_id );

    if( $rank ) {

        $rank_requirements_html = '';

        $requirements = gamipress_get_rank_requirements( $rank->ID );

        if( is_array( $requirements ) && count( $requirements ) ) {

            $list_tag = gamipress_is_achievement_sequential( $rank->ID ) ? 'ol' : 'ul';

            $rank_requirements_html .= "<{$list_tag}>";

            foreach( $requirements as $requirement ) {
                // check if user has earned this requirement, and add an 'earned' class
                $earned = gamipress_get_earnings_count( array(
                        'user_id' => absint( $user_id ),
                        'post_id' => absint( $requirement->ID ),
                        'since' => absint( gamipress_achievement_last_user_activity( $requirement->ID, $user_id ) )
                    ) ) > 0;

                $title = $requirement->post_title;
                $url = gamipress_get_post_meta( $requirement->ID, '_gamipress_url' );

                $rank_requirements_html .= '<li style="' . ( $earned ? 'text-decoration: line-through;' : '' ) . '">'
                        . ( ! empty( $url ) ? '<a href="' . $url. '">' : '' )
                        . $title
                        . ( ! empty( $url ) ? '</a>' : '' )
                    . '</li>';
            }

            $rank_requirements_html .= "</{$list_tag}>";
        }

        $replacements['{rank_id}'] = $rank->ID;
        $replacements['{rank_title}'] = $rank->post_title;
        $replacements['{rank_url}'] = get_the_permalink( $rank->ID );
        $replacements['{rank_link}'] = sprintf( '<a href="%s" title="%s">%s</a>', $replacements['{rank_url}'], $replacements['{rank_title}'], $replacements['{rank_title}'] );
        $replacements['{rank_excerpt}'] = $rank->post_excerpt;
        $replacements['{rank_image}'] = gamipress_get_rank_post_thumbnail( $rank->ID );
        $replacements['{rank_requirements}'] = $rank_requirements_html;
        $replacements['{rank_type}'] = gamipress_get_rank_type_singular( $rank->post_type, true );
        $replacements['{rank_congratulations}'] = gamipress_get_post_meta( $rank->ID, '_gamipress_congratulations_text' );

    }

    return $replacements;

}

/**
 * Rank requirement tags replacements
 *
 * @since 1.8.6
 *
 * @param int       $rank_requirement_id    The rank requirement to get tags values from
 * @param int       $user_id                The user to get tags values from
 *
 * @return array
 */
function gamipress_get_rank_requirement_completed_tags_replacements( $rank_requirement_id, $user_id ) {

    $replacements = array();

    // Get site and user tags replacements
    $replacements = array_merge( $replacements, gamipress_get_site_tags_replacements() );
    $replacements = array_merge( $replacements, gamipress_get_user_tags_replacements( $user_id ) );

    // Get the rank requirement post object
    $requirement = gamipress_get_post( $rank_requirement_id );

    if( $requirement ) {
        $replacements['{label}'] = $requirement->post_title;

        // Get the requirement rank to parse their tags
        $rank = gamipress_get_rank_requirement_rank( $requirement->ID );

        if( $rank ) {
            $replacements = array_merge( $replacements, gamipress_get_rank_earned_tags_replacements( $rank->ID, $user_id ) );
        }
    }

    return $replacements;

}

/**
 * Parse tags
 *
 * @since 1.8.6
 *
 * @param string    $pattern The pattern to get replacements from
 * @param int       $post_id The post to get tags values from
 * @param int       $user_id The user to get tags values from
 * @param string    $content The content to apply tags
 *
 * @return string
 */
function gamipress_parse_tags( $pattern, $post_id, $user_id, $content ) {

    if( $pattern === 'achievement_earned' ) {
        $replacements = gamipress_get_achievement_earned_tags_replacements( $post_id, $user_id );
    } else if( $pattern === 'step_completed' ) {
        $replacements = gamipress_get_step_completed_tags_replacements( $post_id, $user_id );
    } else if( $pattern === 'points_award_completed' ) {
        $replacements = gamipress_get_points_award_completed_tags_replacements( $post_id, $user_id );
    } else if( $pattern === 'points_deduct_completed' ) {
        $replacements = gamipress_get_points_deduct_completed_tags_replacements( $post_id, $user_id );
    } else if( $pattern === 'rank_earned' ) {
        $replacements = gamipress_get_rank_earned_tags_replacements( $post_id, $user_id );
    } else if( $pattern === 'rank_requirement_completed' ) {
        $replacements = gamipress_get_rank_requirement_completed_tags_replacements( $post_id, $user_id );
    } else {
        $replacements = gamipress_get_site_tags_replacements();
        $replacements = array_merge( $replacements, gamipress_get_user_tags_replacements( $user_id ) );
    }

    /**
     * Filter the replacements to apply to the given content
     *
     * @since 1.8.6
     *
     * @param array     $replacements   The replacements to apply to the content
     * @param string    $pattern        The pattern to get replacements from
     * @param int       $post_id        The post to get tags values from
     * @param int       $user_id        The user to get tags values from
     * @param string    $content        The content to apply tags
     *
     * @return array
     */
    $replacements = apply_filters( 'gamipress_parse_tags_replacements', $replacements, $pattern, $post_id, $user_id, $content );

    return str_replace( array_keys( $replacements ), $replacements, $content );

}