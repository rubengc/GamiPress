<?php
/**
 * Filters
 *
 * @package     GamiPress\Filters
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add filters to remove stuff from our singular pages and add back in how we want it
 *
 * @since 1.0.0
 */
function gamipress_do_single_filters() {
	// Check we're in the right place
	if ( gamipress_is_single_achievement() || gamipress_is_single_rank() ) {
		// Filter out the post title
		// add_filter( 'the_title', 'gamipress_remove_to_reformat_entries_title', 10, 2 );

		// and filter out the post image
		add_filter( 'post_thumbnail_html', 'gamipress_remove_to_reformat_entries_thumbnail', 10, 2 );
	}
}
add_action( 'wp_enqueue_scripts', 'gamipress_do_single_filters' );

/**
 * Filter out the post title/post image and add back (later) how we want it
 *
 * @since 1.0.0
 *
 * @param  string   $html        The page content prior to filtering
 * @param  int      $post_id     The post ID.
 *
 * @return string               The page content after being filtered
 */
function gamipress_remove_to_reformat_entries_title( $html = '', $post_id = 0 ) {

    global $gamipress_template_args;

	// Remove, but only on the main loop!
	if ( ( gamipress_is_single_achievement( $post_id ) || gamipress_is_single_rank( $post_id ) )    // Ensure that is an achievement or a rank
        && empty( $GLOBALS['gamipress_reformat_title'] )                                            // Ensure to pass it only one time
        && ! is_array( $gamipress_template_args )                                                   // Prevents to pass this check on GamiPress shortcodes
    ) {
        // Now that we're where we want to be, tell the filters to stop removing
        $GLOBALS['gamipress_reformat_title'] = true;
		return '';
    }

	// Nothing to see here... move along
	return $html;
}

/**
 * Filter out the post title/post image and add back (later) how we want it
 *
 * @since 1.0.0
 *
 * @param  string   $html        The page content prior to filtering
 * @param  int      $post_id     The post ID.
 *
 * @return string               The page content after being filtered
 */
function gamipress_remove_to_reformat_entries_thumbnail( $html = '', $post_id = 0 ) {

    global $gamipress_template_args;

    // Remove, but only on the main loop!
    if ( ( gamipress_is_single_achievement( $post_id ) || gamipress_is_single_rank( $post_id ) )    // Ensure that is an achievement or a rank
        && empty( $GLOBALS['gamipress_reformat_thumbnail'] )                                        // Ensure to pass it only one time
        && ! is_array( $gamipress_template_args )                                                   // Prevents to pass this check on GamiPress shortcodes
    ) {
        // Now that we're where we want to be, tell the filters to stop removing
        $GLOBALS['gamipress_reformat_thumbnail'] = true;
        return '';
    }

    // Nothing to see here... move along
    return $html;
}

/**
 * Filter achievement content to add our removed content back
 *
 * @since  1.0.0
 *
 * @param  string $content The page content
 *
 * @return string          The page content after reformat
 */
function gamipress_reformat_entries( $content ) {

	if ( gamipress_is_single_achievement( get_the_ID() ) ) {

		// Filter content, but only is a single achievement!
		return gamipress_apply_single_template( $content, 'single-achievement' );

	} else if ( gamipress_is_single_rank( get_the_ID() ) ) {

		// Filter content, but only is a single rank!
		return gamipress_apply_single_template( $content, 'single-rank' );
	}

	return $content;
}
add_filter( 'the_content', 'gamipress_reformat_entries' );

/**
 * Apply the given single template
 *
 * @since  1.3.1
 *
 * @param string $content
 * @param string $single_template
 *
 * @return string
 */
function gamipress_apply_single_template( $content, $single_template = '' ) {

	global $gamipress_template_args;

	// Now that we're where we want to be, tell the filters to stop removing
	$GLOBALS['gamipress_reformat_content'] = true;

	// Initialize GamiPress template args global
	$gamipress_template_args = array();

	// Get the original post content (to use as achievement description)
	$gamipress_template_args['original_content'] = $content;

	// Set the configured layout
	$gamipress_template_args['layout'] = gamipress_get_post_meta( get_the_ID(), '_gamipress_layout' );

	// If not layout defined, fallback to left layout
	if( empty( $gamipress_template_args['layout'] ) ) {
		$gamipress_template_args['layout'] = 'left';
	}

    // Set the configured alignment
    $gamipress_template_args['align'] = gamipress_get_post_meta( get_the_ID(), '_gamipress_align' );

    // If not alignment defined, fallback to none alignment
    if( empty( $gamipress_template_args['align'] ) ) {
        $gamipress_template_args['align'] = 'none';
    }

	ob_start();

	// Try to load single-{template}-{post_type}.php, if not exists load single-{template}.php
	gamipress_get_template_part( $single_template, gamipress_get_post_type( get_the_ID() ) );

	$new_content = ob_get_clean();

	// Ok, we're done reformatting
	$GLOBALS['gamipress_reformat_content'] = false;

	return $new_content;

}

/**
 * Helper function tests that we're in the main loop
 *
 * @since  1.3.1
 *
 * @param  bool|integer $id The page id
 *
 * @return boolean     A boolean determining if the function is in the main loop
 */
function gamipress_is_single_achievement( $id = false ) {

	$slugs = gamipress_get_achievement_types_slugs();

	// only run our filters on the achievement singular page
	if ( is_admin() || empty( $slugs ) || ! is_singular( $slugs ) )
		return false;
	// w/o id, we're only checking template context
	if ( ! $id )
		return true;

	// Checks several variables to be sure we're in the main loop (and won't effect things like post pagination titles)
	return ( ( $GLOBALS['post']->ID == $id ) && in_the_loop() && empty( $GLOBALS['gamipress_reformat_content'] ) );

}

/**
 * Generate HTML markup for an points type's points awards
 *
 * @since  1.0.0
 *
 * @param  array   $points_awards   An points type's points awards
 * @param  int $user_id         The given user's ID
 * @param  array   $template_args   The given template args
 *
 * @return string                   The markup for our list
 */
function gamipress_get_points_awards_for_points_types_list_markup( $points_awards = array(), $user_id = 0, $template_args = array() ) {

	// If we don't have any points awards, or our points awards aren't an array, return nothing
	if ( ! $points_awards || ! is_array( $points_awards ) ) {
		return null;
    }

	$count = count( $points_awards );

	// If we have no points awards, return nothing
	if ( ! $count ) {
		return null;
    }

	// Grab the current user's ID if none was specifed
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
    }

    $a = wp_parse_args( $template_args, gamipress_points_types_shortcode_defaults() );

	// Setup our variables
	$output = '';

    if( $a['heading'] === 'yes' ) {

        $points_type = gamipress_get_points_award_points_type( $points_awards[0]->ID );

        if( $points_type ) {
            $plural_name = gamipress_get_post_meta( '_gamipress_plural_name', $points_type->ID );

            if( ! $plural_name )
                $plural_name = $points_type->post_title;

            $points_awards_heading = sprintf( '%1$d %2$s %3$s', $count, $plural_name, _n( 'Award', 'Awards', $count, 'gamipress' ) ); // 2 Credits Awards
        } else {
            $points_awards_heading = sprintf( '%1$d %2$s', $count, _n( 'Award', 'Awards', $count, 'gamipress' ) ); // 2 Awards
        }

        /**
         * Filters the points award heading text
         *
         * @since 1.0.0
         *
         * @param string    $points_awards_heading  The heading text (eg: 2 Points Awards)
         * @param array     $points_awards          The points awards
         * @param int       $user_id                The user's ID
         * @param array     $template_args          The given template args
         *
         * @return string
         */
        $points_awards_heading = apply_filters( 'gamipress_points_awards_heading', $points_awards_heading, $points_awards, $user_id, $template_args );

        $output .= '<' . $a['heading_size'] . ' class="gamipress-points-awards-heading">' . $points_awards_heading . '</' . $a['heading_size'] . '>';

    }

	$output .= '<ul class="gamipress-points-awards">';

	// Concatenate our output
	foreach ( $points_awards as $points_award ) {

        if( $user_id === 0 ) {
            $earned_status = 'user-has-not-earned';
        } else {
            // Check if user can earn this points award and add the earned class
            $can_earn = gamipress_can_user_earn_requirement( $points_award->ID, $user_id );
            $earned_status = $can_earn ? 'user-has-not-earned' : 'user-has-earned';
        }

		$title = $points_award->post_title;

		// If points award doesn't have a title, then try to build one
		if( empty( $title ) ) {
			$title = gamipress_build_requirement_title( $points_award->ID );
        }

        /**
         * Filters the points award CSS class
         *
         * @since 1.0.0
         *
         * @param string    $class          CSS class based on earned status ('user-has-earned' | 'user-has-not-earned')
         * @param stdClass  $points_award   Requirement object
         * @param int       $user_id        User's ID
         * @param array     $template_args  The given template args
         *
         * @return string
         */
        $class = apply_filters( 'gamipress_points_award_class', $earned_status, $points_award, $user_id, $template_args );

        /**
         * Filters the points award HTML title to display
         *
         * @since 1.0.0
         *
         * @param string    $title          Title to display
         * @param stdClass  $points_award   Requirement object
         * @param int       $user_id        User's ID
         * @param array     $template_args  The given template args
         *
         * @return string
         */
        $title = apply_filters( 'gamipress_points_award_title_display', $title, $points_award, $user_id, $template_args );

		$output .= '<li class="'. esc_attr( $class ) .'">'. $title . '</li>';
	}

	$output .= '</ul><!-- .gamipress-points-awards -->';

	// Return our output
	return $output;

}

/**
 * Generate HTML markup for an points type's points deducts
 *
 * @since  1.3.7
 *
 * @param  array   $points_deducts  An points type's points deducts
 * @param  int $user_id         The given user's ID
 * @param  array   $template_args   The given template args
 *
 * @return string                   The markup for our list
 */
function gamipress_get_points_deducts_for_points_types_list_markup( $points_deducts = array(), $user_id = 0, $template_args = array() ) {

	// If we don't have any points deducts, or our points deducts aren't an array, return nothing
	if ( ! $points_deducts || ! is_array( $points_deducts ) ) {
		return null;
    }

	$count = count( $points_deducts );

	// If we have no points deducts, return nothing
	if ( ! $count ) {
		return null;
    }

	// Grab the current user's ID if none was specifed
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
    }

    $a = wp_parse_args( $template_args, gamipress_points_types_shortcode_defaults() );

	// Setup our variables
	$output = '';

    if( $a['heading'] === 'yes' ) {

        $points_type = gamipress_get_points_deduct_points_type( $points_deducts[0]->ID );

        if( $points_type ) {
            $plural_name = gamipress_get_post_meta( '_gamipress_plural_name', $points_type->ID );

            if( ! $plural_name ) {
                $plural_name = $points_type->post_title;
            }

            $points_deducts_heading = sprintf( '%1$d %2$s %3$s', $count, $plural_name, _n( 'Deduction', 'Deductions', $count, 'gamipress' ) ); // 2 Credits Deducts
        } else {
            $points_deducts_heading = sprintf( '%1$d %2$s', $count, _n( 'Deduction', 'Deductions', $count, 'gamipress' ) ); // 2 Deductions
        }

        /**
         * Filters the points deduct heading text
         *
         * @since 1.3.7
         *
         * @param string    $points_deducts_heading The heading text (eg: 2 Points Deductions)
         * @param array     $points_deducts         The points deducts
         * @param int       $user_id                The user's ID
         * @param array     $template_args          The given template args
         *
         * @return string
         */
        $points_deducts_heading = apply_filters( 'gamipress_points_deducts_heading', $points_deducts_heading, $points_deducts, $user_id, $template_args );

        $output .= '<' . $a['heading_size'] . ' class="gamipress-points-deducts-heading">' . $points_deducts_heading . '</' . $a['heading_size'] . '>';

    }

	$output .= '<ul class="gamipress-points-deducts">';

	// Concatenate our output
	foreach ( $points_deducts as $points_deduct ) {

        if( $user_id === 0 ) {
            $earned_status = 'user-has-not-earned';
        } else {
            // Check if user can earn this points deduct and add the earned class
            $can_earn = gamipress_can_user_earn_requirement( $points_deduct->ID, $user_id );
            $earned_status = $can_earn ? 'user-has-not-earned' : 'user-has-earned';
        }

		$title = $points_deduct->post_title;

		// If points deduct doesn't have a title, then try to build one
		if( empty( $title ) ) {
			$title = gamipress_build_requirement_title( $points_deduct->ID );
        }

        /**
         * Filters the points deduct CSS class
         *
         * @since 1.3.7
         *
         * @param string    $class          CSS class based on earned status ('user-has-earned' | 'user-has-not-earned')
         * @param stdClass  $points_deduct  Requirement object
         * @param int       $user_id        User's ID
         * @param array     $template_args  The given template args
         *
         * @return string
         */
        $class = apply_filters( 'gamipress_points_deduct_class', $earned_status, $points_deduct, $user_id, $template_args );

        /**
         * Filters the points deduct HTML title to display
         *
         * @since 1.3.7
         *
         * @param string    $title          Title to display
         * @param stdClass  $points_deduct  Requirement object
         * @param int       $user_id        User's ID
         * @param array     $template_args  The given template args
         *
         * @return string
         */
        $title = apply_filters( 'gamipress_points_deduct_title_display', $title, $points_deduct, $user_id, $template_args );

		$output .= '<li class="'. esc_attr( $class ) .'">'. $title . '</li>';
	}

	$output .= '</ul><!-- .gamipress-points-deducts -->';

	// Return our output
	return $output;

}

/**
 * Gets achievement's required steps and returns HTML markup for these steps
 *
 * @since  1.0.0
 * @param  int $achievement_id The given achievement's post ID
 * @param  int $user_id        A given user's ID
 *
 * @return string                  The markup for our list
 */
function gamipress_get_required_achievements_for_achievement_list( $achievement_id = 0, $user_id = 0 ) {

	// Grab the current post ID if no achievement_id was specified
	if ( ! $achievement_id ) {
		global $post;
		$achievement_id = $post->ID;
	}

	// Grab the current user's ID if none was specifed
	if ( ! $user_id )
		$user_id = wp_get_current_user()->ID;

	// Grab our achievement's required steps
	$steps = gamipress_get_achievement_steps( $achievement_id );

	// Return our markup output
	return gamipress_get_required_achievements_for_achievement_list_markup( $steps, $user_id );

}

/**
 * Generate HTML markup for an achievement's required steps
 *
 * This will generate an unorderd list (<ul>) if steps are non-sequential
 * and an ordered list (<ol>) if steps require sequentiality.
 *
 * @since  1.0.0
 *
 * @param  array   	$steps           An achievement's required steps
 * @param  int 	$achievement_id  The given achievement's ID
 * @param  int 	$user_id         The given user's ID
 * @param  array	$template_args   The given template args
 *
 * @return string                   The markup for our list
 */
function gamipress_get_required_achievements_for_achievement_list_markup( $steps = array(), $achievement_id = 0, $user_id = 0, $template_args = array() ) {

	// If we don't have any steps, or our steps aren't an array, return nothing
	if ( ! $steps || ! is_array( $steps ) )
		return null;

	// Grab the current post ID if no achievement_id was specified
	if ( ! $achievement_id ) {
		global $post;
		$achievement_id = $post->ID;
	}

	$count = count( $steps );

	// If we have no steps, return nothing
	if ( ! $count )
		return null;

	// Grab the current user's ID if none was specified
	if ( ! $user_id )
		$user_id = get_current_user_id();

    $a = wp_parse_args( $template_args, gamipress_achievement_shortcode_defaults() );

	// Setup our variables
	$output = '';
	$container = gamipress_is_achievement_sequential() ? 'ol' : 'ul';

    if( $a['heading'] === 'yes' ) {

        $steps_heading = sprintf( '%1$d %2$s', $count, _n( 'Step', 'Steps', $count, 'gamipress' ) );

        /**
         * Filters the steps heading text
         *
         * @since 1.0.0
         *
         * @param string    $steps_heading          The heading text (eg: 2 Steps)
         * @param array     $steps                  The achievement steps
         * @param int       $user_id                The user's ID
         * @param array     $template_args          The given template args
         *
         * @return string
         */
        $steps_heading = apply_filters( 'gamipress_steps_heading', $steps_heading, $steps, $user_id, $template_args );

        $output .= '<' . $a['heading_size'] . ' class="gamipress-achievement-steps-heading">' . $steps_heading . '</' . $a['heading_size'] . '>';

    }

	$output .= '<' . $container .' class="gamipress-achievement-steps gamipress-required-achievements">';

	// Concatenate our output
	foreach ( $steps as $step ) {

        $can_earn = true;

        if( $user_id === 0 ) {
            $earned_status = 'user-has-not-earned';
        } else {
            // Check if user can earn this step and add the earned class
            $can_earn = gamipress_can_user_earn_requirement( $step->ID, $user_id );
            $earned_status = $can_earn ? 'user-has-not-earned' : 'user-has-earned';
        }

		// Force earned class if user can't earn this step but has earned it in the past
		if( ! $can_earn && gamipress_has_user_earned_achievement( $step->ID, $user_id ) ) {
            $earned_status = 'user-has-earned';
        }

		$title = $step->post_title;

		// If step doesn't have a title, then try to build one
		if( empty( $title ) ) {
			$title = gamipress_build_requirement_title( $step->ID );
        }

        /**
         * Filters the step CSS class
         *
         * @since 1.0.0
         *
         * @param string    $class          CSS class based on earned status ('user-has-earned' | 'user-has-not-earned')
         * @param stdClass  $step           Requirement object
         * @param int       $user_id        User's ID
         * @param array     $template_args  The given template args
         *
         * @return string
         */
        $class = apply_filters( 'gamipress_step_class', $earned_status, $step, $user_id, $template_args );

        /**
         * Filters the step HTML title to display
         *
         * @since 1.0.0
         *
         * @param string    $title          Title to display
         * @param stdClass  $step           Requirement object
         * @param int       $user_id        User's ID
         * @param array     $template_args  The given template args
         *
         * @return string
         */
        $title = apply_filters( 'gamipress_step_title_display', $title, $step, $user_id, $template_args );

		$output .= '<li class="'. esc_attr( $class ) .'">'. $title . '</li>';
	}

	$output .= '</'. $container .'><!-- .gamipress-required-achievements -->';

	// Return our output
	return $output;

}

/**
 * Filter requirements titles to link to assigned post
 *
 * @since   1.0.0
 * @updated 1.5.0 Improved checks to get correct post permalink and centralize all requirements link functions to this
 * @updated 1.8.7 Added permalink to the 'earn-rank' trigger
 *
 * @param  string $title        The requirement title
 * @param  object $requirement  The requirement object
 *
 * @return string        Our potentially updated title
 */
function gamipress_format_requirement_title_with_post_link( $title = '', $requirement = null ) {

	// Grab our step requirements
    $requirement = gamipress_get_requirement_object( $requirement->ID );

    $url = $requirement['url'];

    if( empty( $url ) ) {

        $trigger = $requirement['trigger_type'];
        $post_id = 0;
        $site_id = get_current_blog_id();

        if ( in_array( $trigger, array( 'earn-rank', 'revoke-rank' ) ) && ! empty( $requirement['rank_required'] ) ) {

            // Set the post ID for the rank required to reach
            $post_id = $requirement['rank_required'];
            $site_id = ( gamipress_is_network_wide_active() ? get_main_site_id() : get_current_blog_id() );

        } else if ( ! empty( $requirement['achievement_post'] ) ) {

            // Set the post ID for the post assigned
            $post_id = $requirement['achievement_post'];
            $site_id = $requirement['achievement_post_site_id'];

        }

        // Set the URL to link to a specific post
        $url = gamipress_get_specific_activity_trigger_permalink( $post_id, $trigger, $site_id );

    }

	// If we have a URL, update the title to link to it
	if ( ! empty( $url ) ) {
		$title = '<a href="' . esc_url( $url ) . '">' . $title . '</a>';
    }

	return $title;

}
add_filter( 'gamipress_step_title_display', 'gamipress_format_requirement_title_with_post_link', 10, 2 );
add_filter( 'gamipress_points_award_title_display', 'gamipress_format_requirement_title_with_post_link', 10, 2 );
add_filter( 'gamipress_points_deduct_title_display', 'gamipress_format_requirement_title_with_post_link', 10, 2 );
add_filter( 'gamipress_rank_requirement_title_display', 'gamipress_format_requirement_title_with_post_link', 10, 2 );

/**
 * Generate markup for an achievement's times earned output
 *
 * @since  1.5.9
 *
 * @param  int 	    $achievement_id The given achievement's ID
 * @param  array 	$template_args 	Achievement template args
 *
 * @return string                  The HTML markup for our times earned output
 */
function gamipress_achievement_times_earned_markup( $achievement_id = 0, $template_args = array() ) {

    // Grab the current post ID if no achievement_id was specified
    if ( ! $achievement_id ) {
        global $post;
        $achievement_id = $post->ID;
    }

    if( ! isset( $template_args['user_id'] ) ) {
        $template_args['user_id'] = get_current_user_id();
    }

    $user_id = absint( $template_args['user_id'] );

    // Guest not supported yet (basically because they has not earned this achievement)
    if( $user_id === 0 ) {
        return '';
    }

    $maximum_earnings = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_maximum_earnings' ) );

    // Return if maximum earnings configured is 1
    if( $maximum_earnings === 1 ) {
        return '';
    }

    $earned_times = gamipress_get_earnings_count( array( 'user_id' => $user_id, 'post_id' => $achievement_id ) );

    /**
     * Filter to override the minimum times required to show the achievement times earned text, by default 1
     *
     * @since  2.0.0
     *
     * @param  int 	    $minimum_earned_times   Minimum times required to show the achievement times earned text, by default 1
     * @param  int 	    $achievement_id         The given achievement's ID
     * @param  int 	    $user_id                The user's ID
     * @param  int 	    $earned_times           The user's times earned this achievement
     * @param  int 	    $maximum_earnings       The achievement's maximum times to be earned
     * @param  array 	$template_args 	        Achievement template args
     */
    $minimum_earned_times = apply_filters( 'gamipress_achievement_times_earned_minimum_times_to_show', 1, $achievement_id, $user_id, $earned_times, $maximum_earnings, $template_args );

    // Return if user hasn't earned this achievement the minimum times required to show
    if( $earned_times <= $minimum_earned_times ) {
        return '';
    }

    // Setup the times earned text based on if achievements has unlimited times to be earned
    if( $maximum_earnings === 0 ) {
        $times_earned_pattern = __( '%d times earned', 'gamipress' );

        $times_earned_text = sprintf( $times_earned_pattern, $earned_times );
    } else {
        $times_earned_pattern = __( '%d of %d times earned', 'gamipress' );

        $times_earned_text = sprintf( $times_earned_pattern, $earned_times, $maximum_earnings );
    }

    /**
     * Filter to override the achievement times earned text
     *
     * @since  1.5.9
     *
     * @param  string 	$times_earned_text  Times earned text, by default "X times earned"
     * @param  int 	    $achievement_id     The given achievement's ID
     * @param  int 	    $user_id            The user's ID
     * @param  int 	    $earned_times       The user's times earned this achievement
     * @param  int 	    $maximum_earnings   The achievement's maximum times to be earned
     * @param  array 	$template_args 	    Achievement template args
     */
    $times_earned_text = apply_filters( 'gamipress_achievement_times_earned_text', $times_earned_text, $achievement_id, $user_id, $earned_times, $maximum_earnings, $template_args );

    // The time earned markup
    $output = '<div class="gamipress-achievement-times-earned">' . $times_earned_text . '</div>';

    /**
     * Filter to override the achievement times earned markup
     *
     * @since  1.5.9
     *
     * @param  string 	$output             Times earned markup
     * @param  int 	    $achievement_id     The given achievement's ID
     * @param  int 	    $user_id            The user's ID
     * @param  int 	    $earned_times       The user's times earned this achievement
     * @param  int 	    $maximum_earnings   The achievement's maximum times to be earned
     * @param  array 	$template_args 	    Achievement template args
     */
    return apply_filters( 'gamipress_achievement_times_earned_markup', $output, $achievement_id, $user_id, $earned_times, $maximum_earnings, $template_args );

}

/**
 * Generate markup for an achievement's times earned by all users output
 *
 * @since  2.0.0
 *
 * @param  int 	    $achievement_id The given achievement's ID
 * @param  array 	$template_args 	Achievement template args
 *
 * @return string                  The HTML markup for our times earned output
 */
function gamipress_achievement_global_times_earned_markup( $achievement_id = 0, $template_args = array() ) {

    // Grab the current post ID if no achievement_id was specified
    if ( ! $achievement_id ) {
        global $post;
        $achievement_id = $post->ID;
    }

    $maximum_earnings = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_global_maximum_earnings' ) );

    $earned_times = gamipress_get_earnings_count( array( 'post_id' => $achievement_id ) );

    /**
     * Filter to override the minimum times required to show the achievement times earned by all users text, by default 1
     *
     * @since  2.0.0
     *
     * @param  int 	    $minimum_earned_times   Minimum times required to show the achievement times earned text, by default 1
     * @param  int 	    $achievement_id         The given achievement's ID
     * @param  int 	    $earned_times           The user's times earned this achievement
     * @param  int 	    $maximum_earnings       The achievement's global maximum times to be earned
     * @param  array 	$template_args 	        Achievement template args
     */
    $minimum_earned_times = apply_filters( 'gamipress_achievement_global_times_earned_minimum_times_to_show', 1, $achievement_id, $earned_times, $maximum_earnings, $template_args );

    // Return if user hasn't earned this achievement the minimum times required to show
    if( $earned_times <= $minimum_earned_times ) {
        return '';
    }

    $post_type = gamipress_get_post_type( $achievement_id );
    $achievement_type_singular = gamipress_get_achievement_type_singular( $post_type );

    // Setup the times earned text based on if achievements has unlimited times to be earned
    if( $maximum_earnings === 0 ) {
        $times_earned_pattern = __( '%d users have earned this %s', 'gamipress' );

        $times_earned_text = sprintf( $times_earned_pattern, $earned_times, $achievement_type_singular );
    } else {
        $times_earned_pattern = __( '%d of %d users have earned this %s', 'gamipress' );

        $times_earned_text = sprintf( $times_earned_pattern, $earned_times, $maximum_earnings, $achievement_type_singular );
    }

    /**
     * Filter to override the achievement times earned by all users text
     *
     * @since  1.5.9
     *
     * @param  string 	$times_earned_text  Times earned text, by default "X users have earned this Y"
     * @param  int 	    $achievement_id     The given achievement's ID
     * @param  int 	    $earned_times       The user's times earned this achievement
     * @param  int 	    $maximum_earnings   The achievement's maximum times to be earned
     * @param  array 	$template_args 	    Achievement template args
     */
    $times_earned_text = apply_filters( 'gamipress_achievement_global_times_earned_text', $times_earned_text, $achievement_id, $earned_times, $maximum_earnings, $template_args );

    // The time earned markup
    $output = '<div class="gamipress-achievement-global-times-earned">' . $times_earned_text . '</div>';

    /**
     * Filter to override the achievement times earned by all users markup
     *
     * @since  1.5.9
     *
     * @param  string 	$output             Times earned markup
     * @param  int 	    $achievement_id     The given achievement's ID
     * @param  int 	    $earned_times       The user's times earned this achievement
     * @param  int 	    $maximum_earnings   The achievement's maximum times to be earned
     * @param  array 	$template_args 	    Achievement template args
     */
    return apply_filters( 'gamipress_achievement_global_times_earned_markup', $output, $achievement_id, $earned_times, $maximum_earnings, $template_args );

}

/**
 * Generate markup for an achievement's points output
 *
 * @since   1.0.0
 * @updated 1.5.9  Added $template_args parameter
 *
 * @param  int  $achievement_id The given achievement's ID
 * @param  array    $template_args 	Achievement template args
 *
 * @return string                   The HTML markup for our points
 */
function gamipress_achievement_points_markup( $achievement_id = 0, $template_args = array() ) {

	// Grab the current post ID if no achievement_id was specified
	if ( ! $achievement_id ) {
		global $post;
		$achievement_id = $post->ID;
	}

	$points = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_points' ) );

	// Return if no points configured
	if( $points === 0 ) {
		return '';
	}

    $points_type = gamipress_get_post_meta( $achievement_id, '_gamipress_points_type' );

	// Set the default value for the points awarded thumbnail
	if( ! isset( $template_args['points_awarded_thumbnail'] ) ) {
        $template_args['points_awarded_thumbnail'] = 'yes';
    }

	$output = '<div class="gamipress-achievement-points gamipress-achievement-points-type-' . $points_type . '">'
            . ( $template_args['points_awarded_thumbnail'] === 'yes' ? gamipress_get_points_type_thumbnail( $points_type ) . ' ' : '' )
            . gamipress_format_points( $points, $points_type  )
        . '</div>';

    // Return the points awarded output
	return apply_filters( 'gamipress_achievement_points_markup', $output, $achievement_id, $points, $points_type, $template_args );

}

/**
 * Generate markup for an achievement's unlock with points output
 *
 * @since  1.3.7
 *
 * @param  int 	    $achievement_id The given achievement's ID
 * @param  array 	$template_args 	Achievement template args
 *
 * @return string                  The HTML markup for our points
 */
function gamipress_achievement_unlock_with_points_markup( $achievement_id = 0, $template_args = array() ) {

	// Grab the current post ID if no achievement_id was specified
	if ( ! $achievement_id ) {
		$achievement_id = get_the_ID();
    }

	$user_id = get_current_user_id();

	// Guest not supported yet (basically because they have no points)
	if( $user_id === 0 ) {
		return '';
    }

	if( ! isset( $template_args['user_id'] ) ) {
		$template_args['user_id'] = get_current_user_id();
    }

	// Return if user is displaying achievements of another user
	if( $user_id !== absint( $template_args['user_id'] ) ) {
		return '';
    }

	// Return if this option not was enabled
	if( ! (bool) gamipress_get_post_meta( $achievement_id, '_gamipress_unlock_with_points' ) ) {
		return '';
    }

	$points = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_points_to_unlock' ) );

	// Return if no points configured
	if( $points === 0 ) {
		return '';
    }

	$earned = gamipress_achievement_user_exceeded_max_earnings( $user_id, $achievement_id );

	// Return if user has completely earned this achievement
	if( $earned ) {
		return '';
    }

	// Setup vars
	$points_type = gamipress_get_post_meta( $achievement_id, '_gamipress_points_type_to_unlock' );

	// Template vars
    $achievement_title = gamipress_get_post_field( 'post_title', $achievement_id );
	$points_formatted = gamipress_format_points( $points, $points_type );

    /**
     * Filters if achievement unlock with points requires confirmation, by default true
     *
     * @since  1.7.8.1
     *
     * @param  bool 	$confirmation   If the given achievement's ID requires confirmation on unlock using points
     * @param  int 	    $achievement_id The given achievement's ID
     * @param  int 	    $user_id        The user's ID
     * @param  int 	    $points         Points amount to unlock
     * @param  string 	$points_type    Points type of points amount to unlock
     * @param  array 	$template_args 	Achievement template args
     *
     * @return bool                     Whatever if achievement requires confirmation or not
     */
	$confirmation = apply_filters( 'gamipress_achievement_unlock_with_points_confirmation', true, $achievement_id, $user_id, $points, $points_type, $template_args );

	$button_text = sprintf( __( 'Unlock using %s', 'gamipress' ), $points_formatted );

    /**
     * Filters the achievement unlock with points button text
     *
     * @since  1.8.6
     *
     * @param  string 	$button_text    The button text, by default "Unlock using 100 points"
     * @param  int 	    $achievement_id The given achievement's ID
     * @param  int 	    $user_id        The user's ID
     * @param  int 	    $points         Points amount to unlock
     * @param  string 	$points_type    Points type of points amount to unlock
     * @param  array 	$template_args 	Achievement template args
     *
     * @return string                   Whatever if achievement requires confirmation or not
     */
    $button_text = apply_filters( 'gamipress_achievement_unlock_with_points_button_text', $button_text, $achievement_id, $user_id, $points, $points_type, $template_args );

	ob_start(); ?>
		<div class="gamipress-achievement-unlock-with-points" data-id="<?php echo $achievement_id; ?>">
			<button type="button" class="gamipress-achievement-unlock-with-points-button"><?php echo $button_text; ?></button>
            <?php if( $confirmation ) : ?>
                <div class="gamipress-achievement-unlock-with-points-confirmation" style="display: none;">
                    <p><?php echo sprintf( __( 'Do you want to unlock %s using %s?', 'gamipress' ), $achievement_title, $points_formatted ); ?></p>
                    <button type="button" class="gamipress-achievement-unlock-with-points-confirm-button"><?php echo __( 'Yes', 'gamipress' ); ?></button>
                    <button type="button" class="gamipress-achievement-unlock-with-points-cancel-button"><?php echo __( 'No', 'gamipress' ); ?></button>
                </div>
            <?php endif; ?>
            <div class="gamipress-spinner" style="display: none;"></div>
        </div>
	<?php $output = ob_get_clean();

    /**
     * Filters the achievement unlock with points markup
     *
     * @since  1.3.7
     *
     * @param  string 	$output         The HTML markup for our points
     * @param  int 	    $achievement_id The given achievement's ID
     * @param  int 	    $user_id        The user's ID
     * @param  int 	    $points         Points amount to unlock
     * @param  string 	$points_type    Points type of points amount to unlock
     * @param  array 	$template_args 	Achievement template args
     *
     * @return string                  The HTML markup for our points
     */
	$output = apply_filters( 'gamipress_achievement_unlock_with_points_markup', $output, $achievement_id, $user_id, $points, $points_type, $template_args );

	// Return the unlock with points output
	return $output;

}

/**
 * Generate markup for an rank's unlock with points output
 *
 * @since  1.3.7
 *
 * @param  int 	$rank_id 		The given rank's ID
 * @param  array 	$template_args 	Rank template args
 *
 * @return string                  The HTML markup for our points
 */
function gamipress_rank_unlock_with_points_markup( $rank_id = 0, $template_args = array() ) {

	// Grab the current post ID if no rank_id was specified
	if ( ! $rank_id ) {
		$rank_id = get_the_ID();
    }

	$rank_types = gamipress_get_rank_types();
	$rank_type = gamipress_get_post_type( $rank_id );

	if( ! isset( $rank_types[$rank_type] ) ) {
		return '';
    }

	$user_id = get_current_user_id();

	// Guest not supported yet (basically because they have no points)
	if( $user_id === 0 ) {
		return '';
    }

	if( ! isset( $template_args['user_id'] ) ) {
		$template_args['user_id'] = get_current_user_id();
    }

	// Return if user is displaying ranks of another user
	if( $user_id !== absint( $template_args['user_id'] ) ) {
		return '';
    }

	// Return if this option not was enabled
	if( ! (bool) gamipress_get_post_meta( $rank_id, '_gamipress_unlock_with_points' ) ) {
		return '';
    }

	$points = absint( gamipress_get_post_meta( $rank_id, '_gamipress_points_to_unlock' ) );

	// Return if no points configured
	if( $points === 0 ) {
		return '';
    }

    // Bail if not is the next rank to unlock
    if( gamipress_get_next_user_rank_id( $user_id, $rank_type ) !== $rank_id ) {
        return '';
    }

	$user_rank = gamipress_get_user_rank( $user_id, $rank_type );

	// Return if user is in a higher rank
	if( gamipress_get_rank_priority( $rank_id ) <= gamipress_get_rank_priority( $user_rank ) ) {
		return '';
    }

	// Setup vars
	$points_type = gamipress_get_post_meta( $rank_id, '_gamipress_points_type_to_unlock' );

    // Template vars
    $rank_title = gamipress_get_post_field( 'post_title', $rank_id );
    $points_formatted = gamipress_format_points( $points, $points_type );

    /**
     * Filters if rank unlock with points requires confirmation, by default true
     *
     * @since  1.7.8.1
     *
     * @param  bool 	$confirmation   If the given rank's ID requires confirmation on unlock using points
     * @param  int 	    $rank_id        The given rank's ID
     * @param  int 	    $user_id        The user's ID
     * @param  int 	    $points         Points amount to unlock
     * @param  string 	$points_type    Points type of points amount to unlock
     * @param  array 	$template_args 	Achievement template args
     *
     * @return bool                     Whatever if achievement requires confirmation or not
     */
    $confirmation = apply_filters( 'gamipress_rank_unlock_with_points_confirmation', true, $rank_id, $user_id, $points, $points_type, $template_args );

    $button_text = sprintf( __( 'Unlock using %s', 'gamipress' ), $points_formatted );

    /**
     * Filters the rank unlock with points button text
     *
     * @since  1.8.6
     *
     * @param  string 	$button_text    The button text, by default "Unlock using 100 points"
     * @param  int 	    $rank_id        The given rank's ID
     * @param  int 	    $user_id        The user's ID
     * @param  int 	    $points         Points amount to unlock
     * @param  string 	$points_type    Points type of points amount to unlock
     * @param  array 	$template_args 	Achievement template args
     *
     * @return string                   Whatever if achievement requires confirmation or not
     */
    $button_text = apply_filters( 'gamipress_rank_unlock_with_points_button_text', $button_text, $rank_id, $user_id, $points, $points_type, $template_args );

    ob_start(); ?>
	<div class="gamipress-rank-unlock-with-points" data-id="<?php echo $rank_id; ?>">
		<button type="button" class="gamipress-rank-unlock-with-points-button"><?php echo $button_text; ?></button>
        <?php if( $confirmation ) : ?>
            <div class="gamipress-rank-unlock-with-points-confirmation" style="display: none;">
                <p><?php echo sprintf( __( 'Do you want to unlock %s using %s?', 'gamipress' ), $rank_title, $points_formatted ); ?></p>
                <button type="button" class="gamipress-rank-unlock-with-points-confirm-button"><?php echo __( 'Yes', 'gamipress' ); ?></button>
                <button type="button" class="gamipress-rank-unlock-with-points-cancel-button"><?php echo __( 'No', 'gamipress' ); ?></button>
            </div>
        <?php endif; ?>
        <div class="gamipress-spinner" style="display: none;"></div>
	</div>
	<?php $output = ob_get_clean();

    /**
     * Filters the achievement unlock with points markup
     *
     * @since  1.3.7
     *
     * @param  string 	$output         The HTML markup for our points
     * @param  int 	    $rank_id        The given rank's ID
     * @param  int 	    $user_id        The user's ID
     * @param  int 	    $points         Points amount to unlock
     * @param  string 	$points_type    Points type of points amount to unlock
     * @param  array 	$template_args 	Achievement template args
     *
     * @return string                  The HTML markup for our points
     */
    $output = apply_filters( 'gamipress_rank_unlock_with_points_markup', $output, $rank_id, $user_id, $points, $points_type, $template_args );

    // Return our markup
	return $output;

}

/**
 * Checks if should render open graph meta tags
 *
 * @since  1.8.6
 */
function gamipress_maybe_render_open_graph_meta_tags() {

    $allowed_types = array_merge( gamipress_get_achievement_types_slugs(), gamipress_get_rank_types_slugs() );

    // Bail if not is a singular achievement type or rank type
    if( ! is_singular( $allowed_types ) ) {
        return;
    }

    $enable_open_graph = (bool) gamipress_get_option( 'enable_open_graph_tags', false );

    // Bail if open graph meta tags option is not enabled
    if( ! $enable_open_graph ) {
        return;
    }

    gamipress_render_open_graph_meta_tags( get_the_ID() );

}
add_action( 'wp_head', 'gamipress_maybe_render_open_graph_meta_tags' );


/**
 * Render open graph meta tags for the received post ID
 *
 * @since  1.8.6
 *
 * @param int $post_id
 */
function gamipress_render_open_graph_meta_tags( $post_id ) {

    $post = get_post( $post_id );

    // Get the post information
    $url = get_permalink( $post );
    $title = $post->post_title;
    $description = $post->post_content;
    $image = get_the_post_thumbnail_url( $post );

    // Process the description
    $description = apply_filters( 'the_content', $description );
    $description = trim( strip_tags( wp_kses_no_null( $description ) ) );
    ?>
    <meta property="og:url" content="<?php echo esc_url( $url ); ?>"/>
    <meta property="og:title" content="<?php echo esc_attr( $title ); ?>"/>
    <meta property="og:description" content="<?php echo esc_attr( $description ); ?>"/>
    <meta property="og:image" content="<?php echo esc_url( $image ); ?>"/>
    <?php
}

/**
 * Generate markup for share an achievement's output
 *
 * @since  1.8.6
 *
 * @param  int 	    $achievement_id The given achievement's ID
 * @param  array 	$template_args 	Achievement template args
 *
 * @return string                  The HTML markup for our points
 */
function gamipress_achievement_share_markup( $achievement_id = 0, $template_args = array() ) {

    // Grab the current post ID if no achievement_id was specified
    if ( ! $achievement_id ) {
        $achievement_id = get_the_ID();
    }

    $user_id = get_current_user_id();

    // Guest not supported yet (basically because they has not earned this)
    if( $user_id === 0 ) {
        return '';
    }

    if( ! isset( $template_args['user_id'] ) ) {
        $template_args['user_id'] = get_current_user_id();
    }

    // Return if user is displaying achievements of another user
    if( $user_id !== absint( $template_args['user_id'] ) ) {
        return '';
    }

    // Bail if share is not enable
    if( ! (bool) gamipress_get_option( 'enable_share', false ) ) {
        return '';
    }

    $social_networks = gamipress_get_option( 'social_networks', false );

    // Bail if not social networks enabled
    if( empty( $social_networks ) ) {
        return '';
    }

    // Check if user has earned this achievement
    $earned = gamipress_has_user_earned_achievement( $achievement_id, $user_id );

    if( ! $earned ) {
        return '';
    }

    $button_style = gamipress_get_option( 'social_button_style', 'square' );

    $titles = array(
        'facebook' => __( 'Share on Facebook', 'gamipress' ),
        'twitter' => __( 'Share on Twitter', 'gamipress' ),
        'linkedin' => __( 'Share on LinkedIn', 'gamipress' ),
        'pinterest' => __( 'Share on Pinterest', 'gamipress' ),
    );

    $names = array(
        'facebook' => __( 'Facebook', 'gamipress' ),
        'twitter' => __( 'Twitter', 'gamipress' ),
        'linkedin' => __( 'LinkedIn', 'gamipress' ),
        'pinterest' => __( 'Pinterest', 'gamipress' ),
    );

    // Setup vars
    $url = get_the_permalink( $achievement_id );
    $title = get_the_title( $achievement_id );
    $image = get_the_post_thumbnail_url( $achievement_id );

    // Get the achievement twitter text
    $twitter_text = gamipress_get_option( 'twitter_achievement_text', __( 'I earned the {achievement_type} {achievement_title} on {site_title}', 'gamipress' ) );

    // Parse the text
    $twitter_text = gamipress_parse_tags( 'achievement_earned', $achievement_id, $user_id, $twitter_text );

    ob_start(); ?>

    <p class="gamipress-share-buttons-label"><?php _e( 'Share:', 'gamipress' ); ?></p>

    <div class="gamipress-share-buttons"
         data-url="<?php echo esc_attr( $url ); ?>"
         data-title="<?php echo esc_attr( $title ); ?>"
         data-image="<?php echo esc_attr( $image ); ?>"
         data-twitter-text="<?php echo esc_attr( $twitter_text ); ?>">

        <?php
        /**
         * Action at top of the share buttons
         *
         * @since  1.8.6
         *
         * @param  int 	    $achievement_id The given achievement's ID
         * @param  int 	    $user_id        The user's ID
         * @param  array 	$template_args 	Achievement template args
         */
        do_action( 'gamipress_achievement_share_buttons_top', $achievement_id, $user_id, $template_args ) ?>

        <?php foreach( $social_networks as $social_network ) : ?>
            <a href="#"
               title="<?php echo ( isset( $titles[$social_network] ) ? $titles[$social_network] : '' ); ?>"
               class="gamipress-share-button gamipress-share-button-<?php echo $button_style; ?> gamipress-share-button-<?php echo $social_network; ?>"
               data-network="<?php echo $social_network; ?>">
                <span><?php echo ( isset( $names[$social_network] ) ? $names[$social_network] : '' ); ?></span>
            </a>
        <?php endforeach; ?>

        <?php
        /**
         * Action at bottom of the share buttons
         *
         * @since  1.8.6
         *
         * @param  int 	    $achievement_id The given achievement's ID
         * @param  int 	    $user_id        The user's ID
         * @param  array 	$template_args 	Achievement template args
         */
        do_action( 'gamipress_achievement_share_buttons_bottom', $achievement_id, $user_id, $template_args ) ?>

    </div>

    <?php $output = ob_get_clean();

    /**
     * Filters the achievement share markup
     *
     * @since  1.8.6
     *
     * @param  string 	$output         The HTML markup for social sharing
     * @param  int 	    $achievement_id The given achievement's ID
     * @param  int 	    $user_id        The user's ID
     * @param  array 	$template_args 	Achievement template args
     *
     * @return string                  The HTML markup for our points
     */
    $output = apply_filters( 'gamipress_achievement_share_markup', $output, $achievement_id, $user_id, $template_args );

    return $output;

}

/**
 * Generate markup for share an rank's output
 *
 * @since  1.8.6
 *
 * @param  int 	    $rank_id        The given rank's ID
 * @param  array 	$template_args 	Rank template args
 *
 * @return string                  The HTML markup for our points
 */
function gamipress_rank_share_markup( $rank_id = 0, $template_args = array() ) {

    // Grab the current post ID if no rank_id was specified
    if ( ! $rank_id ) {
        $rank_id = get_the_ID();
    }

    $user_id = get_current_user_id();

    // Guest not supported yet (basically because they has not earned this)
    if( $user_id === 0 ) {
        return '';
    }

    if( ! isset( $template_args['user_id'] ) ) {
        $template_args['user_id'] = get_current_user_id();
    }

    // Return if user is displaying ranks of another user
    if( $user_id !== absint( $template_args['user_id'] ) ) {
        return '';
    }

    // Bail if share is not enable
    if( ! (bool) gamipress_get_option( 'enable_share', false ) ) {
        return '';
    }

    $social_networks = gamipress_get_option( 'social_networks', false );

    // Bail if not social networks enabled
    if( empty( $social_networks ) ) {
        return '';
    }

    // Check if user has earned this achievement
    $earned = gamipress_has_user_earned_achievement( $rank_id, $user_id );

    if( ! $earned ) {
        return '';
    }

    $button_style = gamipress_get_option( 'social_button_style', 'square' );

    $titles = array(
        'facebook' => __( 'Share on Facebook', 'gamipress' ),
        'twitter' => __( 'Share on Twitter', 'gamipress' ),
        'linkedin' => __( 'Share on LinkedIn', 'gamipress' ),
        'pinterest' => __( 'Share on Pinterest', 'gamipress' ),
    );

    $names = array(
        'facebook' => __( 'Facebook', 'gamipress' ),
        'twitter' => __( 'Twitter', 'gamipress' ),
        'linkedin' => __( 'LinkedIn', 'gamipress' ),
        'pinterest' => __( 'Pinterest', 'gamipress' ),
    );

    // Setup vars
    $url = get_the_permalink( $rank_id );
    $title = get_the_title( $rank_id );
    $image = get_the_post_thumbnail_url( $rank_id );

    // Get the rank twitter text
    $twitter_text = gamipress_get_option( 'twitter_rank_text', __( 'I reached the {rank_type} {rank_title} on {site_title}', 'gamipress' ) );

    // Parse the text
    $twitter_text = gamipress_parse_tags( 'rank_earned', $rank_id, $user_id, $twitter_text );

    ob_start(); ?>

    <p class="gamipress-share-buttons-label"><?php _e( 'Share:', 'gamipress' ); ?></p>

    <div class="gamipress-share-buttons"
         data-url="<?php echo esc_attr( $url ); ?>"
         data-title="<?php echo esc_attr( $title ); ?>"
         data-image="<?php echo esc_attr( $image ); ?>"
         data-twitter-text="<?php echo esc_attr( $twitter_text ); ?>">

        <?php
        /**
         * Action at top of the share buttons
         *
         * @since  1.8.6
         *
         * @param  int 	    $rank_id        The given rank's ID
         * @param  int 	    $user_id        The user's ID
         * @param  array 	$template_args 	Rank template args
         */
        do_action( 'gamipress_rank_share_buttons_top', $rank_id, $user_id, $template_args ) ?>

        <?php foreach( $social_networks as $social_network ) : ?>
            <a href="#"
               title="<?php echo ( isset( $titles[$social_network] ) ? $titles[$social_network] : '' ); ?>"
               class="gamipress-share-button gamipress-share-button-<?php echo $button_style; ?> gamipress-share-button-<?php echo $social_network; ?>"
               data-network="<?php echo $social_network; ?>">
                <span><?php echo ( isset( $names[$social_network] ) ? $names[$social_network] : '' ); ?></span>
            </a>
        <?php endforeach; ?>

        <?php
        /**
         * Action at bottom of the share buttons
         *
         * @since  1.8.6
         *
         * @param  int 	    $rank_id        The given rank's ID
         * @param  int 	    $user_id        The user's ID
         * @param  array 	$template_args 	Rank template args
         */
        do_action( 'gamipress_rank_share_buttons_bottom', $rank_id, $user_id, $template_args ) ?>

    </div>

    <?php $output = ob_get_clean();

    /**
     * Filters the rank share markup
     *
     * @since  1.8.6
     *
     * @param  string 	$output         The HTML markup for social sharing
     * @param  int 	    $rank_id        The given rank's ID
     * @param  int 	    $user_id        The user's ID
     * @param  array 	$template_args 	Rank template args
     *
     * @return string                  The HTML markup for our points
     */
    $output = apply_filters( 'gamipress_rank_share_markup', $output, $rank_id, $user_id, $template_args );

    return $output;

}

/**
 * Adds "earned"/"not earned" post_class based on viewer's status
 *
 * @param  array $classes Post classes
 *
 * @return array          Updated post classes
 */
function gamipress_add_earned_class_single( $classes = array() ) {

	if( is_singular( gamipress_get_achievement_types_slugs() ) ) {
		// Single Achievement

		// Check if current user has earned the achievement they're viewing
		$classes[] =gamipress_has_user_earned_achievement( get_the_ID(), get_current_user_id() ) ? 'user-has-earned' : 'user-has-not-earned';

	} else if( is_singular( gamipress_get_rank_types_slugs() ) ) {
		// Single Rank

		// Check if current user has earned the rank they're viewing, rank is earned by default if is the lowest priority of this type
		if( gamipress_is_lowest_priority_rank( get_the_ID() ) ) {
			$earned = true;
		} else {
			$earned = gamipress_has_user_earned_achievement( get_the_ID(), get_current_user_id() );
		}

		$classes[] = $earned ? 'user-has-earned' : 'user-has-not-earned';

	}

	return $classes;

}
add_filter( 'post_class', 'gamipress_add_earned_class_single' );

/**
 * Returns a message if user has earned the achievement.
 *
 * @since  1.0.0
 *
 * @param  int $achievement_id Achievement ID.
 * @param  int $user_id        User ID.
 *
 * @return string                  HTML Markup.
 */
function gamipress_render_earned_achievement_text( $achievement_id = 0, $user_id = 0 ) {

	$earned_message = '';

	if ( gamipress_has_user_earned_achievement( $achievement_id, $user_id ) ) {

		$achievement_types = gamipress_get_achievement_types();
		$achievement_type = $achievement_types[gamipress_get_post_type( $achievement_id )];

		$earned_message .= '<div class="gamipress-achievement-earned"><p>' . sprintf( __( 'You have earned this %s!', 'gamipress' ), $achievement_type['singular_name'] ) . '</p></div>';

		if ( $congrats_text = gamipress_get_post_meta( $achievement_id, '_gamipress_congratulations_text' ) ) {
			$earned_message .= '<div class="gamipress-achievement-congratulations">' . wpautop( $congrats_text ) . '</div>';
		}
	}

    /**
     * Available filter to override the message if user has earned the achievement
     *
     * @since  1.0.0
     *
     * @param  string   $earned_message The earned message HTML markup.
     * @param  int      $achievement_id Achievement ID.
     * @param  int      $user_id        User ID.
     *
     * @return string                   HTML Markup.
     */
	return apply_filters( 'gamipress_earned_achievement_message', $earned_message, $achievement_id, $user_id );
}

/**
 * Check if user has earned a given achievement.
 *
 * @since  1.0.0
 *
 * @param  int $achievement_id  Achievement ID.
 * @param  int $user_id         User ID.
 *
 * @return bool                 True if user has earned the achievement, otherwise false.
 */
function gamipress_has_user_earned_achievement( $achievement_id = 0, $user_id = 0 ) {

    $user_id = absint( $user_id );
    $achievement_id = absint( $achievement_id );

    if( $user_id === 0 ) {
        $user_id = get_current_user_id();
    }

    if( $user_id === 0 ) {
        return false;
    }

    $earned = gamipress_get_earnings_count( array( 'user_id' => $user_id, 'post_id' => $achievement_id ) );
    $earned = $earned > 0;

    /**
     * Available filter to override the has user earned achievement result.
     *
     * @since 1.0.0
     *
     * @param bool  $earned             Whatever if user has earned this achievement or not.
     * @param int   $achievement_id     Achievement ID.
     * @param int   $user_id            User ID.
     *
     * @return bool                     True if user has earned the achievement, otherwise false.
     */
	return apply_filters( 'gamipress_has_user_earned_achievement', $earned, $achievement_id, $user_id );

}


/**
 * Hide the hidden achievement post link from next and previous post link
 *
 * @since  1.0.0
 *
 * @param string  $output   The adjacent post link.
 * @param string  $format   Link anchor format.
 * @param string  $link     Link permalink format.
 * @param WP_Post $post     The adjacent post.
 * @param string  $adjacent Whether the post is previous or next.
 *
 * @return string
 */
function gamipress_hide_next_previous_hidden_achievement_link( $output, $format, $link, $post, $adjacent ) {

	$post = get_post();

	if( $post && gamipress_is_achievement( $post ) && $output ) {

		// Get post link, without hidden achievement
		$output = gamipress_get_post_link_without_hidden_achievement( $post->ID, $adjacent );

	}

	return $output;

}
add_filter( 'next_post_link', 'gamipress_hide_next_previous_hidden_achievement_link', 10, 5 );
add_filter( 'previous_post_link', 'gamipress_hide_next_previous_hidden_achievement_link', 10, 5 );


/**
 * Get post link without hidden achievement link
 *
 * @since  1.0.0
 *
 * @param int 		$achievement_id	The achievement ID
 * @param string 	$rel			'next'|'previous'
 *
 * @return string
 */
function gamipress_get_post_link_without_hidden_achievement( $achievement_id, $rel ) {

	// Check if is an achievement
	if( ! gamipress_is_achievement( $achievement_id ) ) {
		return '';
	}

	$link = '';

	// Get next post id without hidden achievement id
	$next_post_id = gamipress_get_next_previous_achievement_id( $achievement_id, $rel );

	if ( $next_post_id ) {
		// Generate post link
		$link = gamipress_generate_post_link_by_post_id( $next_post_id, $rel );
	}


	return $link;

}

/**
 * Get next or previous post id , without hidden achievement id
 *
 * @since  1.0.0
 *
 * @param int 		$achievement_id	The achievement ID
 * @param string 	$rel			'next'|'previous'
 *
 * @return int|bool
 */
function gamipress_get_next_previous_achievement_id( $achievement_id , $rel ) {

	global $wpdb;

	$posts = GamiPress()->db->posts;

	// Redirecting user page based on achievements
	$post = gamipress_get_post( absint( $achievement_id ));

	//Get hidden achievements ids
	$hidden = gamipress_get_hidden_achievement_ids( $post->post_type );

	$operator = ( $rel === 'next' ? '>' : '<' );
	$order = ( $rel === 'next' ? 'ASC' : 'DESC' );
	$hidden_where = ( count( $hidden ) ? "AND ID NOT IN ( " . implode( $hidden, ', ' ) . " )" : '' );


	$next_id = absint( $wpdb->get_var( $wpdb->prepare(
		"SELECT ID
		FROM {$posts}
		WHERE post_type = %s
		AND post_status = %s
		{$hidden_where}
		AND ID {$operator} %d
		ORDER BY ID {$order}
		LIMIT 1",
		$post->post_type,
		'publish',
		$post->ID
	) ) );

	// Return next or previous achievement preventing hidden achievements
	return ( $next_id !== absint( $post->ID ) ? $next_id : false );

}

/**
 * Generate the post link based on custom post object
 *
 * @since  1.0.0
 *
 * @param int 		$post_id
 * @param string 	$rel
 *
 * @return string
 */
function gamipress_generate_post_link_by_post_id( $post_id, $rel ) {

	$post = gamipress_get_post( $post_id );

	if( ! $post ) {
		return '';
	}

    // Title of the post
	$title = get_the_title( $post->ID );

	if ( empty( $post->post_title ) ) {
		if( $rel === 'next' ) {
			$title = __( 'Next Post' );
		} else {
			$title = __( 'Previous Post' );
		}
	}

	$nav = ($rel == 'next') ? '%s <span class="meta-nav">→</span>' : '<span class="meta-nav">←</span> %s';

	// Build link
	$link = '<a href="' . get_permalink( $post ) . '" rel="'.$rel.'">' . sprintf( $nav, $title ) . '</a>';

	return $link;

}

/**
 * Filters the log title.
 *
 * @param string $title The log title.
 * @param int    $id    The log ID.
 *
 * @return string 		The formatted title
 */
function gamipress_log_title_format( $title, $id = null ) {

	if( $id === null ) {
		return $title;
	}

	if( empty( $title ) ) {
		$title = gamipress_get_parsed_log( $id );
	}

	return $title;
}
add_filter( 'gamipress_render_log_title', 'gamipress_log_title_format', 10, 2 );

/**
 * Gets rank's required steps and returns HTML markup for these steps
 *
 * @since  1.0.0
 * @param  int $rank_id 		The given rank's post ID
 * @param  int $user_id        	A given user's ID
 *
 * @return string                  	The markup for our list
 */
function gamipress_get_rank_requirements_list( $rank_id = 0, $user_id = 0 ) {

	// Grab the current post ID if no rank_id was specified
	if ( ! $rank_id ) {
		global $post;
		$rank_id = $post->ID;
	}

	// Grab the current user's ID if none was specifed
	if ( ! $user_id )
		$user_id = wp_get_current_user()->ID;

	// Grab our rank's required requirements
	$requirements = gamipress_get_rank_requirements( $rank_id );

	// Return our markup output
	return gamipress_get_rank_requirements_list_markup( $requirements, $user_id );

}

/**
 * Generate HTML markup for an achievement's required steps
 *
 * This will generate an unorderd list (<ul>) if steps are non-sequential
 * and an ordered list (<ol>) if steps require sequentiality.
 *
 * @since  1.3.1
 *
 * @param  array   	$requirements   An rank's required requirements
 * @param  int 	$rank_id        The given rank's ID
 * @param  int 	$user_id        The given user's ID
 * @param  array	$template_args  The given template args
 *
 * @return string                   The markup for our list
 */
function gamipress_get_rank_requirements_list_markup( $requirements = array(), $rank_id = 0, $user_id = 0, $template_args = array() ) {

	// If we don't have any steps, or our steps aren't an array, return nothing
	if ( ! $requirements || ! is_array( $requirements ) ) {
		return null;
    }

	// Grab the current post ID if no achievement_id was specified
	if ( ! $rank_id ) {
		global $post;
		$rank_id = $post->ID;
	}

	$count = count( $requirements );

	// If we have no steps, return nothing
	if ( ! $count ) {
		return null;
    }

	// Grab the current user's ID if none was specified
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
    }

    $a = wp_parse_args( $template_args, gamipress_rank_shortcode_defaults() );

	// Setup our variables
	$output = '';
	$container = gamipress_is_achievement_sequential() ? 'ol' : 'ul';

    if( $a['heading'] === 'yes' ) {

        $requirements_heading = sprintf( '%1$d %2$s', $count, _n( 'Requirement', 'Requirements', $count, 'gamipress' ) );;

        /**
         * Filters the steps heading text
         *
         * @since 1.0.0
         *
         * @param string    $requirements_heading   The heading text (eg: 2 Requirements)
         * @param array     $requirements           The rank requirements
         * @param int       $user_id                The user's ID
         * @param array     $template_args          The given template args
         *
         * @return string
         */
        $requirements_heading = apply_filters( 'gamipress_rank_requirements_heading', $requirements_heading, $requirements, $user_id, $template_args );

        $output .= '<' . $a['heading_size'] . ' class="gamipress-rank-requirements-heading">' . $requirements_heading . '</' . $a['heading_size'] . '>';

    }

	$output .= '<' . $container .' class="gamipress-rank-requirements gamipress-required-requirements">';

	// Concatenate our output
	foreach ( $requirements as $requirement ) {

	    if( $user_id === 0 ) {
            $earned_status = 'user-has-not-earned';
        } else {
            // Check if user can earn this requirement and add the earned class
            $can_earn = gamipress_can_user_earn_requirement( $requirement->ID, $user_id );
            $earned_status = $can_earn ? 'user-has-not-earned' : 'user-has-earned';
        }

		$title = $requirement->post_title;

		// If step doesn't have a title, then try to build one
		if( empty( $title ) ) {
			$title = gamipress_build_requirement_title( $requirement->ID );
        }

        /**
         * Filters the rank requirement CSS class
         *
         * @since 1.3.1
         *
         * @param string    $class          CSS class based on earned status ('user-has-earned' | 'user-has-not-earned')
         * @param stdClass  $requirement    Requirement object
         * @param int       $user_id        User's ID
         * @param array     $template_args  The given template args
         *
         * @return string
         */
		$class = apply_filters( 'gamipress_rank_requirement_class', $earned_status, $requirement, $user_id, $template_args );

        /**
         * Filters the rank requirement HTML title to display
         *
         * @since 1.3.1
         *
         * @param string    $title          Title to display
         * @param stdClass  $requirement    Requirement object
         * @param int       $user_id        User's ID
         * @param array     $template_args  The given template args
         *
         * @return string
         */
        $title = apply_filters( 'gamipress_rank_requirement_title_display', $title, $requirement, $user_id, $template_args );

		$output .= '<li class="'. esc_attr( $class ) .'">'. $title . '</li>';
	}

	$output .= '</'. $container .'><!-- .gamipress-requirements -->';

	// Return our output
	return $output;

}

/**
 * Helper function tests that we're in the main loop
 *
 * @since  1.3.1
 *
 * @param  bool|integer $id The page id
 *
 * @return boolean     A boolean determining if the function is in the main loop
 */
function gamipress_is_single_rank( $id = false ) {

	$slugs = gamipress_get_rank_types_slugs();

	// only run our filters on the rank singular page
	if ( is_admin() || empty( $slugs ) || ! is_singular( $slugs ) )
		return false;
	// w/o id, we're only checking template context
	if ( ! $id )
		return true;

	// Checks several variables to be sure we're in the main loop (and won't effect things like post pagination titles)
	return ( ( $GLOBALS['post']->ID == $id ) && in_the_loop() && empty( $GLOBALS['gamipress_reformat_content'] ) );

}

/**
 * Returns a message if user has earned the rank.
 *
 * @since  1.3.1
 *
 * @param  int $rank_id 		Rank ID.
 * @param  int $user_id        	User ID.
 *
 * @return string                  	HTML Markup.
 */
function gamipress_render_earned_rank_text( $rank_id = 0, $user_id = 0 ) {

	$earned_message = '';

	if ( gamipress_has_user_earned_rank( $rank_id, $user_id ) ) {

		$rank_types = gamipress_get_rank_types();
		$rank_type = $rank_types[gamipress_get_post_type( $rank_id )];

		$earned_message .= '<div class="gamipress-rank-earned"><p>' . sprintf( __( 'You have reached this %s!', 'gamipress' ), $rank_type['singular_name']) . '</p></div>';

		if ( $congrats_text = gamipress_get_post_meta( $rank_id, '_gamipress_congratulations_text' ) ) {
			$earned_message .= '<div class="gamipress-rank-congratulations">' . wpautop( $congrats_text ) . '</div>';
		}
	}

    /**
     * Available filter to override the message if user has earned the rank
     *
     * @since  1.0.0
     *
     * @param  string   $earned_message The earned message HTML markup.
     * @param  int      $rank_id        Rank ID.
     * @param  int      $user_id        User ID.
     *
     * @return string                   HTML Markup.
     */
	return apply_filters( 'gamipress_earned_rank_message', $earned_message, $rank_id, $user_id );

}

/**
 * Check if user has earned a given achievement.
 *
 * @since  1.3.1
 *
 * @param  int $rank_id 		Rank ID.
 * @param  int $user_id        	User ID.
 *
 * @return bool                    	True if user has earned the rank, otherwise false.
 */
function gamipress_has_user_earned_rank( $rank_id = 0, $user_id = 0 ) {

    $user_id = absint( $user_id );
    $rank_id = absint( $rank_id );

    if( $user_id === 0 ) {
        $user_id = get_current_user_id();
    }

    if( $user_id === 0 ) {
        return false;
    }

    $earned = gamipress_get_earnings_count( array( 'user_id' => $user_id, 'post_id' => $rank_id ) );
    $earned = $earned > 0;

    /**
     * Available filter to override the has user earned rank result.
     *
     * @since 1.0.0
     *
     * @param bool  $earned     Whatever if user has earned this rank or not.
     * @param int   $rank_id    Rank ID.
     * @param int   $user_id    User ID.
     *
     * @return bool             True if user has earned the rank, otherwise false.
     */
	return apply_filters( 'gamipress_has_user_earned_rank', $earned, $rank_id, $user_id );

}

/**
 * Auto flush permalinks wth a soft flush when a 404 error is detected on an GamiPress page
 *
 * Taken from Easy Digital Downloads
 *
 * @since 1.3.3
 * @return string
 */
function gamipress_refresh_permalinks_on_bad_404() {

	global $wp;

	if( ! is_404() ) {
		return;
	}

	if( isset( $_GET['gamipress-flush'] ) ) {
		return;
	}

	if( false === get_transient( 'gamipress_refresh_404_permalinks' ) ) {

		$gamipress_slugs = array();

		$gamipress_slugs = array_merge( $gamipress_slugs, gamipress_get_achievement_types() );
		$gamipress_slugs = array_merge( $gamipress_slugs, gamipress_get_points_types() );
		$gamipress_slugs = array_merge( $gamipress_slugs, gamipress_get_rank_types() );

		$parts = explode( '/', $wp->request );

		$is_a_gamipress_page = false;

		foreach( $parts as $part ) {

			if( isset( $gamipress_slugs[$part] ) ) {
				$is_a_gamipress_page = true;
				break;
			}

		}

		if( ! $is_a_gamipress_page ) {
			return;
		}

		flush_rewrite_rules( false );

		set_transient( 'gamipress_refresh_404_permalinks', 1, HOUR_IN_SECONDS * 12 );

		wp_redirect( home_url( add_query_arg( array( 'gamipress-flush' => 1 ), $wp->request ) ) ); exit;

	}
}
add_action( 'template_redirect', 'gamipress_refresh_permalinks_on_bad_404' );

/**
 * Render earnings column
 *
 * @since 1.0.0
 *
 * @param string    $column_output  Default column output
 * @param string    $column_name    The column name
 * @param stdClass  $user_earning   The column name
 * @param array     $template_args  Template received arguments
 *
 * @return string
 */
function gamipress_earnings_render_column( $column_output, $column_name, $user_earning, $template_args ) {

	// Setup vars
	$requirement_types = gamipress_get_requirement_types();
	$points_types = gamipress_get_points_types();
	$achievement_types = gamipress_get_achievement_types();
	$rank_types = gamipress_get_rank_types();

	switch( $column_name ) {
		case 'user':

		    $user = get_userdata( $user_earning->user_id );

		    if( $user ) {
                $column_output = get_avatar( $user_earning->user_id ) . $user->display_name;
            }

            break;
		case 'thumbnail':

			if( in_array( $user_earning->post_type, gamipress_get_requirement_types_slugs() ) ) {

				if( $user_earning->post_type === 'step' && $achievement = gamipress_get_step_achievement( $user_earning->post_id ) )  {
					// Step

					// Get the achievement thumbnail and build a link to the achievement
					$column_output = gamipress_get_achievement_post_thumbnail( $achievement->ID );

				} else if( ( $user_earning->post_type === 'points-award' || $user_earning->post_type === 'points-deduct' ) && $points_type = gamipress_get_points_award_points_type( $user_earning->post_id ) )  {
					// Points Award and Deduction

					// Get the points type thumbnail
					$column_output = gamipress_get_points_type_thumbnail( $points_type->ID );

				} else if( $user_earning->post_type === 'rank-requirement' && $rank = gamipress_get_rank_requirement_rank( $user_earning->post_id ) ) {
					// Rank requirement

					// Get the rank thumbnail
					$column_output = gamipress_get_rank_post_thumbnail( $rank->ID );
				}

			} else if( in_array( $user_earning->post_type, gamipress_get_achievement_types_slugs() ) ) {
				// Achievement

				// Get the achievement thumbnail
				$column_output = gamipress_get_achievement_post_thumbnail( $user_earning->post_id );

			} else if( in_array( $user_earning->post_type, gamipress_get_rank_types_slugs() ) ) {
				// Rank

				// Get the rank thumbnail
				$column_output = gamipress_get_rank_post_thumbnail( $user_earning->post_id );

            } else {
			    // Default

                // Get the points type thumbnail
                $column_output = gamipress_get_points_type_thumbnail( $user_earning->post_id );
            }

			break;
		case 'description':

            $earning_title = $user_earning->title;
            $earning_description = '';

			if( in_array( $user_earning->post_type, gamipress_get_requirement_types_slugs() ) ) {

				$earning_title = gamipress_get_post_field( 'post_title', $user_earning->post_id );
				$earning_description = '';

				if( $user_earning->post_type === 'step' && $achievement = gamipress_get_step_achievement( $user_earning->post_id ) )  {
					// Step

					// Build a link to the achievement
					$earning_description = sprintf( '%s %s: <a href="%s">%s</a>',
						$achievement_types[$achievement->post_type]['singular_name'],
						__( 'Step', 'gamipress' ),
						get_post_permalink( $achievement->ID ),
						gamipress_get_post_field( 'post_title', $achievement->ID )
					);

				} else if( ( $user_earning->post_type === 'points-award' || $user_earning->post_type === 'points-deduct' ) && $points_type = gamipress_get_points_award_points_type( $user_earning->post_id ) )  {
					// Points Award and Deduction

					$earning_description = sprintf( '%s %s',
						$points_types[$points_type->post_name]['plural_name'],
						( $user_earning->post_type === 'points-award' ? __( 'Award', 'gamipress' ) : __( 'Deduction', 'gamipress' ) )
					);

				} else if( $user_earning->post_type === 'rank-requirement' && $rank = gamipress_get_rank_requirement_rank( $user_earning->post_id ) ) {
					// Rank requirement

					// Build a link to the rank
					$earning_description = sprintf( '%s %s: <a href="%s">%s</a>',
						$rank_types[$rank->post_type]['singular_name'],
						__( 'Requirement', 'gamipress' ),
						get_post_permalink( $rank->ID ),
						gamipress_get_post_field( 'post_title', $rank->ID )
					);
				}

			} else if( in_array( $user_earning->post_type, gamipress_get_achievement_types_slugs() ) ) {
				// Achievement

				// Build a link to the achievement
				$earning_title = sprintf( '<a href="%s">%s</a>',
					get_post_permalink( $user_earning->post_id ),
					gamipress_get_post_field( 'post_title', $user_earning->post_id )
				);
				$earning_description = $achievement_types[$user_earning->post_type]['singular_name'];

			} else if( in_array( $user_earning->post_type, gamipress_get_rank_types_slugs() ) ) {
				// Rank

				// Build a link to the rank
				$earning_title = sprintf( '<a href="%s">%s</a>',
					get_post_permalink( $user_earning->post_id ),
					gamipress_get_post_field( 'post_title', $user_earning->post_id )
				);
				$earning_description = $rank_types[$user_earning->post_type]['singular_name'];

			}

			// Default description for custom user earnings with points assigned
			if( empty( $earning_description ) ) {

                $points = (int) $user_earning->points;

                if( $points !== 0 && isset( $points_types[$user_earning->points_type] ) ) {

                    $earning_description = sprintf( '%s %s',
                        $points_types[$user_earning->points_type]['plural_name'],
                        ( $points > 0 ? __( 'Award', 'gamipress' ) : __( 'Deduction', 'gamipress' ) )
                    );

                }

            }

			$column_output = sprintf( '<strong class="gamipress-earning-title">%s</strong>'
				. '<br>'
				. '<span class="gamipress-earning-description">%s</span>',
				$earning_title,
				$earning_description
			);

			break;
		case 'points':

			$points = (int) $user_earning->points;

			if( $points !== 0 && isset( $points_types[$user_earning->points_type] ) ) {

				// Setup the output as %d point(s)
				$column_output = gamipress_format_points( $points, $user_earning->points_type );

				// For points deducts turn amount to negative
				if( $user_earning->post_type === 'points-deduct' && $points > 0 ) {
					$negative_points = $points * -1;
					$column_output = gamipress_format_points( $negative_points, $user_earning->points_type );;
				}

			}

			break;
		case 'date':

			$column_output = date_i18n( get_option( 'date_format' ), strtotime( $user_earning->date ) );

			break;
	}

	return $column_output;

}
add_action( 'gamipress_earnings_render_column', 'gamipress_earnings_render_column', 10, 4 );