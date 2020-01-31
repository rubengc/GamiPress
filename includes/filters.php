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
 * @param  integer $user_id         The given user's ID
 * @param  array   $template_args   The given template args
 *
 * @return string                   The markup for our list
 */
function gamipress_get_points_awards_for_points_types_list_markup( $points_awards = array(), $user_id = 0, $template_args = array() ) {

	// If we don't have any points awards, or our points awards aren't an array, return nothing
	if ( ! $points_awards || ! is_array( $points_awards ) )
		return null;

	$count = count( $points_awards );

	// If we have no points awards, return nothing
	if ( ! $count )
		return null;

	// Grab the current user's ID if none was specifed
	if ( ! $user_id )
		$user_id = get_current_user_id();

	// Setup our variables
	$output = '';
	$post_type_object = get_post_type_object( 'points-award' );
	$points_type = gamipress_get_points_award_points_type( $points_awards[0]->ID );

	if( $points_type ) {
		$plural_name = gamipress_get_post_meta( '_gamipress_plural_name', $points_type->ID );

		if( ! $plural_name )
			$plural_name = $points_type->post_title;

		$points_awards_heading = sprintf( __( '%1$d %2$s Awards', 'gamipress' ), $count, $plural_name ); // 2 Credits Awards
	} else {
		$points_awards_heading = sprintf( __( '%1$d %2$s', 'gamipress' ), $count, $post_type_object->labels->name ); // 2 Awards
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

	$output .= '<h4>' . $points_awards_heading . '</h4>';
	$output .= '<ul class="gamipress-points-awards">';

	// Concatenate our output
	foreach ( $points_awards as $points_award ) {

		// Check if user has earned this points award, and add an 'earned' class
		$earned_status = 'user-has-not-earned';

		$maximum_earnings = absint( gamipress_get_post_meta( $points_award->ID, '_gamipress_maximum_earnings' ) );

		// An unlimited maximum of earnings means points awards could be earned anyway
		if( $maximum_earnings > 0 ) {
			$earned_times = count( gamipress_get_user_achievements( array(
				'user_id' => absint( $user_id ),
				'achievement_id' => absint( $points_award->ID ),
			) ) );

			// User has earned it more times than required times, so is earned
			if( $earned_times >= $maximum_earnings ) {
				$earned_status = 'user-has-earned';
			}
		}

		$title = $points_award->post_title;

		// If points award doesn't have a title, then try to build one
		if( empty( $title ) )
			$title = gamipress_build_requirement_title( $points_award->ID );

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
 * @param  integer $user_id         The given user's ID
 * @param  array   $template_args   The given template args
 *
 * @return string                   The markup for our list
 */
function gamipress_get_points_deducts_for_points_types_list_markup( $points_deducts = array(), $user_id = 0, $template_args = array() ) {

	// If we don't have any points deducts, or our points deducts aren't an array, return nothing
	if ( ! $points_deducts || ! is_array( $points_deducts ) )
		return null;

	$count = count( $points_deducts );

	// If we have no points deducts, return nothing
	if ( ! $count )
		return null;

	// Grab the current user's ID if none was specifed
	if ( ! $user_id )
		$user_id = get_current_user_id();

	// Setup our variables
	$output = '';
	$post_type_object = get_post_type_object( 'points-deduct' );
	$points_type = gamipress_get_points_deduct_points_type( $points_deducts[0]->ID );

	if( $points_type ) {
		$plural_name = gamipress_get_post_meta( '_gamipress_plural_name', $points_type->ID );

		if( ! $plural_name ) {
			$plural_name = $points_type->post_title;
		}

		$points_deducts_heading = sprintf( __( '%1$d %2$s Deducts', 'gamipress' ), $count, $plural_name ); // 2 Credits Deducts
	} else {
		$points_deducts_heading = sprintf( __( '%1$d %2$s', 'gamipress' ), $count, $post_type_object->labels->name ); // 2 Deducts
	}

    /**
     * Filters the points deduct heading text
     *
     * @since 1.3.7
     *
     * @param string    $points_deducts_heading The heading text (eg: 2 Points Deducts)
     * @param array     $points_deducts         The points deducts
     * @param int       $user_id                The user's ID
     * @param array     $template_args          The given template args
     *
     * @return string
     */
    $points_deducts_heading = apply_filters( 'gamipress_points_deducts_heading', $points_deducts_heading, $points_deducts, $user_id, $template_args );

	$output .= '<h4>' . $points_deducts_heading . '</h4>';
	$output .= '<ul class="gamipress-points-deducts">';

	// Concatenate our output
	foreach ( $points_deducts as $points_deduct ) {

		// Check if user has earned this points deduct, and add an 'earned' class
		$earned_status = 'user-has-not-earned';

		$maximum_earnings = absint( gamipress_get_post_meta( $points_deduct->ID, '_gamipress_maximum_earnings' ) );

		// An unlimited maximum of earnings means points deducts could be earned anyway
		if( $maximum_earnings > 0 ) {
			$earned_times = count( gamipress_get_user_achievements( array(
				'user_id' => absint( $user_id ),
				'achievement_id' => absint( $points_deduct->ID ),
			) ) );

			// User has earned it more times than required times, so is earned
			if( $earned_times >= $maximum_earnings ) {
				$earned_status = 'user-has-earned';
			}
		}

		$title = $points_deduct->post_title;

		// If points deduct doesn't have a title, then try to build one
		if( empty( $title ) )
			$title = gamipress_build_requirement_title( $points_deduct->ID );

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
 * @param  integer $achievement_id The given achievement's post ID
 * @param  integer $user_id        A given user's ID
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
 * @param  integer 	$achievement_id  The given achievement's ID
 * @param  integer 	$user_id         The given user's ID
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

	// Setup our variables
	$output = '';
	$container = gamipress_is_achievement_sequential() ? 'ol' : 'ul';

	$post_type_object = get_post_type_object( 'step' );

    $steps_heading =  sprintf( __( '%1$d Required %2$s', 'gamipress' ), $count, $post_type_object->labels->name );

    /**
     * Filters the steps heading text
     *
     * @since 1.0.0
     *
     * @param string    $steps_heading          The heading text (eg: 2 Required Steps)
     * @param array     $steps                  The achievement steps
     * @param int       $user_id                The user's ID
     * @param array     $template_args          The given template args
     *
     * @return string
     */
    $steps_heading = apply_filters( 'gamipress_steps_heading', $steps_heading, $steps, $user_id, $template_args );

	$output .= '<h4>' . $steps_heading . '</h4>';
	$output .= '<' . $container .' class="gamipress-required-achievements">';

	// Concatenate our output
	foreach ( $steps as $step ) {

		// Check if user has earned this step, and add an 'earned' class
		$earned_status = is_user_logged_in() && gamipress_get_user_achievements( array(
			'user_id' => absint( $user_id ),
			'achievement_id' => absint( $step->ID ),
		) ) ? 'user-has-earned' : 'user-has-not-earned';

		$title = $step->post_title;

		// If step doesn't have a title, then try to build one
		if( empty( $title ) )
			$title = gamipress_build_requirement_title( $step->ID );

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
 *
 * @param  string $title        The requirement title
 * @param  object $requirement  The requirement object
 *
 * @return string        Our potentially updated title
 */
function gamipress_format_requirement_title_with_post_link( $title = '', $requirement = null ) {

	// Grab our step requirements
    $requirement_object = gamipress_get_requirement_object( $requirement->ID );

	// Setup a URL to link to a specific achievement or an achievement type
	if ( ! empty( $requirement_object['achievement_post'] ) ) {
        $url = gamipress_get_specific_activity_trigger_permalink( $requirement_object['achievement_post'], $requirement_object['trigger_type'], $requirement_object['achievement_post_site_id'] );
    }

	// If we have a URL, update the title to link to it
	if ( isset( $url ) && ! empty( $url ) )
		$title = '<a href="' . esc_url( $url ) . '">' . $title . '</a>';

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
 * @param  integer 	$achievement_id The given achievement's ID
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

    $earned_times = count( gamipress_get_user_achievements( array( 'user_id' => $user_id, 'achievement_id' => $achievement_id ) ) );

    // Return if user hasn't earned it or just earned it 1 time
    if( $earned_times <= 1 ) {
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
     * @param  integer 	$achievement_id     The given achievement's ID
     * @param  integer 	$user_id            The user's ID
     * @param  integer 	$earned_times       The user's times earned this achievement
     * @param  integer 	$maximum_earnings   The achievement's maximum times to be earned
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
     * @param  integer 	$achievement_id     The given achievement's ID
     * @param  integer 	$user_id            The user's ID
     * @param  integer 	$earned_times       The user's times earned this achievement
     * @param  integer 	$maximum_earnings   The achievement's maximum times to be earned
     * @param  array 	$template_args 	    Achievement template args
     */
    return apply_filters( 'gamipress_achievement_times_earned_markup', $output, $achievement_id, $user_id, $earned_times, $maximum_earnings, $template_args );

}

/**
 * Generate markup for an achievement's points output
 *
 * @since   1.0.0
 * @updated 1.5.9  Added $template_args parameter
 *
 * @param  integer  $achievement_id The given achievement's ID
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


	$output = '<div class="gamipress-achievement-points gamipress-achievement-points-type-' . $points_type . '">' . gamipress_format_points( $points, $points_type  ) . '</div>';

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
	if ( ! $achievement_id )
		$achievement_id = get_the_ID();

	$user_id = get_current_user_id();

	// Guest not supported yet (basically because they has not points)
	if( $user_id === 0 )
		return '';

	if( ! isset( $template_args['user_id'] ) )
		$template_args['user_id'] = get_current_user_id();

	// Return if user is displaying achievements of another user
	if( $user_id !== absint( $template_args['user_id'] ) )
		return '';

	// Return if this option not was enabled
	if( ! (bool) gamipress_get_post_meta( $achievement_id, '_gamipress_unlock_with_points' ) )
		return '';

	$points = absint( gamipress_get_post_meta( $achievement_id, '_gamipress_points_to_unlock' ) );

	// Return if no points configured
	if( $points === 0 )
		return '';

	$earned = gamipress_achievement_user_exceeded_max_earnings( $user_id, $achievement_id );

	// Return if user has completely earned this achievement
	if( $earned )
		return '';

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
     * @param  bool 	    $confirmation   If the given achievement's ID requires confirmation on unlock using points
     * @param  int 	    $achievement_id The given achievement's ID
     * @param  int 	    $user_id        The user's ID
     * @param  int 	    $points         Points amount to unlock
     * @param  string 	$points_type    Points type of points amount to unlock
     * @param  array 	$template_args 	Achievement template args
     *
     * @return bool                     Whatever if achievement requires confirmation or not
     */
	$confirmation = apply_filters( 'gamipress_achievement_unlock_with_points_confirmation', true, $achievement_id, $user_id, $points, $points_type, $template_args );

	ob_start(); ?>
		<div class="gamipress-achievement-unlock-with-points" data-id="<?php echo $achievement_id; ?>">
			<button type="button" class="gamipress-achievement-unlock-with-points-button"><?php echo sprintf( __( 'Unlock using %s', 'gamipress' ), $points_formatted ); ?></button>
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
 * @param  integer 	$rank_id 		The given rank's ID
 * @param  array 	$template_args 	Rank template args
 *
 * @return string                  The HTML markup for our points
 */
function gamipress_rank_unlock_with_points_markup( $rank_id = 0, $template_args = array() ) {

	// Grab the current post ID if no rank_id was specified
	if ( ! $rank_id )
		$rank_id = get_the_ID();

	$rank_types = gamipress_get_rank_types();
	$rank_type = gamipress_get_post_type( $rank_id );

	if( ! isset( $rank_types[$rank_type] ) )
		return '';

	$user_id = get_current_user_id();

	// Guest not supported yet (basically because they has not points)
	if( $user_id === 0 )
		return '';

	if( ! isset( $template_args['user_id'] ) )
		$template_args['user_id'] = get_current_user_id();

	// Return if user is displaying ranks of another user
	if( $user_id !== absint( $template_args['user_id'] ) )
		return '';

	// Return if this option not was enabled
	if( ! (bool) gamipress_get_post_meta( $rank_id, '_gamipress_unlock_with_points' ) )
		return '';

	$points = absint( gamipress_get_post_meta( $rank_id, '_gamipress_points_to_unlock' ) );

	// Return if no points configured
	if( $points === 0 )
		return '';

	$user_rank = gamipress_get_user_rank( $user_id, $rank_type );

	// Return if user is in a higher rank
	if( gamipress_get_rank_priority( $rank_id ) <= gamipress_get_rank_priority( $user_rank ) )
		return '';

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

	ob_start(); ?>
	<div class="gamipress-rank-unlock-with-points" data-id="<?php echo $rank_id; ?>">
		<button type="button" class="gamipress-rank-unlock-with-points-button"><?php echo sprintf( __( 'Unlock using %s', 'gamipress' ), $points_formatted ); ?></button>
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
    $output = apply_filters( 'gamipress_rank_unlock_with_points_markup', $output, $rank_id, $user_id, $template_args );

    // Return our markup
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
		$classes[] = gamipress_get_user_achievements( array( 'user_id' => get_current_user_id(), 'achievement_id' => get_the_ID() ) ) ? 'user-has-earned' : 'user-has-not-earned';

	} else if( is_singular( gamipress_get_rank_types_slugs() ) ) {
		// Single Rank

		// Check if current user has earned the rank they're viewing, rank is earned by default if is the lowest priority of this type
		if( gamipress_is_lowest_priority_rank( get_the_ID() ) ) {
			$earned = true;
		} else {
			$earned = gamipress_get_user_achievements( array( 'user_id' => get_current_user_id(), 'achievement_id' => get_the_ID() ) );
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
 * @param  integer $achievement_id Achievement ID.
 * @param  integer $user_id        User ID.
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

	return apply_filters( 'gamipress_earned_achievement_message', $earned_message, $achievement_id, $user_id );
}

/**
 * Check if user has earned a given achievement.
 *
 * @since  1.0.0
 *
 * @param  integer $achievement_id Achievement ID.
 * @param  integer $user_id        User ID.
 *
 * @return bool                    True if user has earned the achievement, otherwise false.
 */
function gamipress_has_user_earned_achievement( $achievement_id = 0, $user_id = 0 ) {

	$earned_achievements = gamipress_get_user_achievements( array( 'user_id' => absint( $user_id ), 'achievement_id' => absint( $achievement_id ) ) );
	$earned_achievement = ! empty( $earned_achievements );

	return apply_filters( 'gamipress_has_user_earned_achievement', $earned_achievement, $achievement_id, $user_id );

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
 * @param  integer $rank_id 		The given rank's post ID
 * @param  integer $user_id        	A given user's ID
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
 * @param  integer 	$rank_id        The given rank's ID
 * @param  integer 	$user_id        The given user's ID
 * @param  array	$template_args  The given template args
 *
 * @return string                   The markup for our list
 */
function gamipress_get_rank_requirements_list_markup( $requirements = array(), $rank_id = 0, $user_id = 0, $template_args = array() ) {

	// If we don't have any steps, or our steps aren't an array, return nothing
	if ( ! $requirements || ! is_array( $requirements ) )
		return null;

	// Grab the current post ID if no achievement_id was specified
	if ( ! $rank_id ) {
		global $post;
		$rank_id = $post->ID;
	}

	$count = count( $requirements );

	// If we have no steps, return nothing
	if ( ! $count )
		return null;

	// Grab the current user's ID if none was specified
	if ( ! $user_id )
		$user_id = get_current_user_id();

	// Setup our variables
	$output = '';
	$container = gamipress_is_achievement_sequential() ? 'ol' : 'ul';

    $requirements_heading = $count . ' ' . _n( 'Requirement', 'Requirements', $count, 'gamipress' );

    /**
     * Filters the steps heading text
     *
     * @since 1.0.0
     *
     * @param string    $steps_heading          The heading text (eg: 2 Requirements)
     * @param array     $requirements           The rank requirements
     * @param int       $user_id                The user's ID
     * @param array     $template_args          The given template args
     *
     * @return string
     */
    $requirements_heading = apply_filters( 'gamipress_rank_requirements_heading', $requirements_heading, $requirements, $user_id, $template_args );

	$output .= '<h4>' . $requirements_heading . '</h4>';
	$output .= '<' . $container .' class="gamipress-required-requirements">';

	// Concatenate our output
	foreach ( $requirements as $requirement ) {

		// Check if user has earned this requirement, and add an 'earned' class
		$earned_status = is_user_logged_in() && gamipress_get_user_achievements( array(
			'user_id' => absint( $user_id ),
			'achievement_id' => absint( $requirement->ID ),
		) ) ? 'user-has-earned' : 'user-has-not-earned';

		$title = $requirement->post_title;

		// If step doesn't have a title, then try to build one
		if( empty( $title ) )
			$title = gamipress_build_requirement_title( $requirement->ID );

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
 * @param  integer $rank_id 		Rank ID.
 * @param  integer $user_id        	User ID.
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

	return apply_filters( 'gamipress_earned_rank_message', $earned_message, $rank_id, $user_id );

}

/**
 * Check if user has earned a given achievement.
 *
 * @since  1.3.1
 *
 * @param  integer $rank_id 		Rank ID.
 * @param  integer $user_id        	User ID.
 *
 * @return bool                    	True if user has earned the rank, otherwise false.
 */
function gamipress_has_user_earned_rank( $rank_id = 0, $user_id = 0 ) {

	$earned_achievements = gamipress_get_user_achievements( array( 'user_id' => absint( $user_id ), 'achievement_id' => absint( $rank_id ) ) );
	$earned_achievement = ! empty( $earned_achievements );

	return apply_filters( 'gamipress_has_user_earned_rank', $earned_achievement, $rank_id, $user_id );

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
					// Points Award and Deduct

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

			}

			break;
		case 'description':

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
					// Points Award and Deduct

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

			$column_output = sprintf( '<strong class="gamipress-earning-title">%s</strong>'
				. '<br>'
				. '<span class="gamipress-earning-description">%s</span>',
				$earning_title,
				$earning_description
			);

			break;
		case 'points':

			$points = absint( $user_earning->points );

			if( $points > 0 && isset( $points_types[$user_earning->points_type] ) ) {

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