<?php
/**
 * GamiPress Inline Last Achievements Earned Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Inline_Last_Achievements_Earned
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       2.3.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register [gamipress_inline_last_achievements_earned] shortcode
 *
 * @since 2.3.1
 */
function gamipress_register_inline_last_achievements_earned_shortcode() {

	$achievement_fields = GamiPress()->shortcodes['gamipress_inline_achievement']->fields;

	unset( $achievement_fields['id'] );

    // Register as singular to keep backward compatibility
	gamipress_register_shortcode( 'gamipress_inline_last_achievements_earned', array(
		'name'              => __( 'Inline Last Achievements Earned', 'gamipress' ),
		'description'       => __( 'Display the last achievements earned by the current user or a desired user inline.', 'gamipress' ),
        'icon' 	            => 'awards',
        'group' 	        => 'gamipress',
		'output_callback'   => 'gamipress_inline_last_achievements_earned_shortcode',
        'tabs' => array(
            'general' => array(
                'icon' => 'dashicons-admin-generic',
                'title' => __( 'General', 'gamipress' ),
                'fields' => array(
                    'type',
                    'current_user',
                    'user_id',
                    'limit',
                    'columns',
                ),
            ),
            'achievement' => array(
                'icon' => 'dashicons-awards',
                'title' => __( 'Achievement', 'gamipress' ),
                'fields' => array_keys( $achievement_fields ),
            ),
        ),
		'fields'      => array_merge( array(
			'type' => array(
				'name'              => __( 'Achievement Type', 'gamipress' ),
				'description'       => __( 'The type of the last achievement earned.', 'gamipress' ),
				'type'              => 'select',
                'classes' 	        => 'gamipress-selector',
				'option_none'       => true,
				'option_all'        => false,
				'options_cb'        => 'gamipress_options_cb_achievement_types',
				'default'           => '',
			),
			'current_user' => array(
				'name'        => __( 'Current User', 'gamipress' ),
				'description' => __( 'Show the last achievement earned by the current logged in user.', 'gamipress' ),
				'type' 		  => 'checkbox',
				'classes' 	  => 'gamipress-switch',
                'default' 	    => 'yes',
			),
			'user_id' => array(
				'name'        => __( 'User', 'gamipress' ),
				'description' => __( 'Show the last achievement earned by a specific user.', 'gamipress' ),
				'type'        => 'select',
				'default'     => '',
                'options_cb'  => 'gamipress_options_cb_users'
			),
            'limit' => array(
                'name'        => __( 'Limit', 'gamipress' ),
                'description' => __( 'Number of achievements to display.', 'gamipress' ),
                'type'        => 'text',
                'default'     => '1',
            ),
		), $achievement_fields ),
	) );

}
add_action( 'init', 'gamipress_register_inline_last_achievements_earned_shortcode' );

/**
 * Last Achievements Earned Shortcode
 *
 * @since  2.3.1
 *
 * @param  array    $atts      Shortcode attributes
 * @param  string   $content   Shortcode content
 *
 * @return string 	   HTML markup
 */
function gamipress_inline_last_achievements_earned_shortcode( $atts = array(), $content = '' ) {

	global $gamipress_template_args;

	// Initialize GamiPress template args global
	$gamipress_template_args = array();

    $shortcode = 'gamipress_inline_last_achievements_earned';

	$atts = shortcode_atts( array_merge( array(
		'type'        	    => '',
		'current_user'      => 'yes',
		'user_id'     	    => '0',
		'limit'     	    => '1',
	), gamipress_inline_achievement_shortcode_defaults() ), $atts, $shortcode );

    // ---------------------------
    // Shortcode Errors
    // ---------------------------

    // Check if achievement type is valid
    if ( ! in_array( $atts['type'], gamipress_get_achievement_types_slugs() ) ) {
        return gamipress_shortcode_error( __( 'The type provided isn\'t a valid registered achievement type.', 'gamipress' ), $shortcode );
    }

    // ---------------------------
    // Shortcode Processing
    // ---------------------------

    // Enqueue assets
    gamipress_enqueue_scripts();

	// Force to set current user as user ID
	if( $atts['current_user'] === 'yes' ) {
		$atts['user_id'] = get_current_user_id();
	} else if( absint( $atts['user_id'] ) === 0 ) {
		$atts['user_id'] = get_current_user_id();
	}

	if( absint( $atts['user_id'] ) === 0 ) {
        /**
         * Filter to override shortcode output when no user has been found
         *
         * @since 2.3.1
         *
         * @param string    $output     Final output
         * @param array     $atts       Shortcode attributes
         * @param string    $content    Shortcode content
         */
	    return apply_filters( 'gamipress_inline_last_achievements_earned_shortcode_no_user_output', '', $atts, $content );
    }

    $atts['limit'] = absint( $atts['limit'] );


    if( $atts['limit'] === 1 ) {
    // Display a single achievement

        $achievement_id = gamipress_get_last_earning_post_id( array(
            'post_type' => $atts['type'],
            'user_id' => $atts['user_id'],
        ) );

        if( absint( $achievement_id ) === 0 ) {
            /**
             * Filter to override the shortcode output when no achievement has been found
             *
             * @since 2.3.1
             *
             * @param string    $output     Final output
             * @param array     $atts       Shortcode attributes
             * @param string    $content    Shortcode content
             */
            return apply_filters( 'gamipress_inline_last_achievements_earned_shortcode_no_achievement_output', '', $atts, $content );
        }

        // Set the achievement ID
        $atts['id'] = $achievement_id;

        $output = gamipress_inline_achievement_shortcode( $atts, $content );
    } else {
        // Display a list of achievements

        $last_earnings = gamipress_get_last_earnings( array(
            'post_type' => $atts['type'],
            'user_id' => $atts['user_id'],
            'limit' => $atts['limit'],
        ) );

        if( ! $last_earnings ) {
            /**
             * Filter to override the shortcode output when no achievements has been found
             *
             * @since 2.3.1
             *
             * @param string    $output     Final output
             * @param array     $atts       Shortcode attributes
             * @param string    $content    Shortcode content
             */
            return apply_filters( 'gamipress_last_achievements_earned_shortcode_no_achievement_output', '', $atts, $content );
        }

        // Render the achievements
        $achievements = array();

        foreach( $last_earnings as $earning ) {
            $achievements[] = $earning->post_id;
        }

        // Setup the template args
        $gamipress_template_args = $atts;
        $gamipress_template_args['achievements'] = $achievements;

        ob_start();
        gamipress_get_template_part( 'inline-achievements', $atts['type'] );
        $output = ob_get_clean();

        $output = gamipress_parse_inline_output( $output );
    }


    /**
     * Filter to override shortcode output
     *
     * @since 2.3.1
     *
     * @param string    $output     Final output
     * @param array     $atts       Shortcode attributes
     * @param string    $content    Shortcode content
     */
    return apply_filters( 'gamipress_inline_last_achievements_earned_shortcode_output', $output, $atts, $content );

}
