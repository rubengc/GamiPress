<?php
/**
 * GamiPress Last Achievements Earned Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Last_Achievements_Earned
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Register the singular separately to keep backward compatibility
add_shortcode( 'gamipress_last_achievement_earned', 'gamipress_last_achievements_earned_shortcode' );

/**
 * Register [gamipress_last_achievements_earned] shortcode
 *
 * @since 1.0.0
 */
function gamipress_register_last_achievements_earned_shortcode() {

	$achievement_fields = GamiPress()->shortcodes['gamipress_achievement']->fields;

	unset( $achievement_fields['id'] );

    // Register as singular to keep backward compatibility
	gamipress_register_shortcode( 'gamipress_last_achievements_earned', array(
		'name'              => __( 'Last Achievements Earned', 'gamipress' ),
		'description'       => __( 'Display the last achievements earned by the current user or a desired user.', 'gamipress' ),
        'icon' 	            => 'awards',
        'group' 	        => 'gamipress',
		'output_callback'   => 'gamipress_last_achievements_earned_shortcode',
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
                    'columns_small',
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
            'columns' => array(
                'name'        => __( 'Columns', 'gamipress' ),
                'description' => __( 'Columns to divide achievements (only used when limit is higher than 1).', 'gamipress' ),
                'type' 	=> 'select',
                'options' => array(
                    '1' => __( '1 Column', 'gamipress' ),
                    '2' => __( '2 Columns', 'gamipress' ),
                    '3' => __( '3 Columns', 'gamipress' ),
                    '4' => __( '4 Columns', 'gamipress' ),
                    '5' => __( '5 Columns', 'gamipress' ),
                    '6' => __( '6 Columns', 'gamipress' ),
                ),
                'default' => '1'
            ),
            'columns_small' => array(
                'name'        => __( 'Columns in small screens', 'gamipress' ),
                'description' => __( 'Columns to divide achievements in small screens (only used when limit is higher than 1).', 'gamipress' ),
                'type' 	=> 'select',
                'options' => array(
                    '1' => __( '1 Column', 'gamipress' ),
                    '2' => __( '2 Columns', 'gamipress' ),
                    '3' => __( '3 Columns', 'gamipress' ),
                    '4' => __( '4 Columns', 'gamipress' ),
                    '5' => __( '5 Columns', 'gamipress' ),
                    '6' => __( '6 Columns', 'gamipress' ),
                ),
                'default' => '1'
            ),
		), $achievement_fields ),
	) );

}
add_action( 'init', 'gamipress_register_last_achievements_earned_shortcode' );

/**
 * Last Achievements Earned Shortcode
 *
 * @since  1.0.0
 *
 * @param  array    $atts      Shortcode attributes
 * @param  string   $content   Shortcode content
 *
 * @return string 	   HTML markup
 */
function gamipress_last_achievements_earned_shortcode( $atts = array(), $content = '' ) {

	global $gamipress_template_args;

	// Initialize GamiPress template args global
	$gamipress_template_args = array();

    $shortcode = 'gamipress_last_achievements_earned';

	$atts = shortcode_atts( array_merge( array(
		'type'        	    => '',
		'current_user'      => 'yes',
		'user_id'     	    => '0',
		'limit'     	    => '1',
		'columns'     	    => '1',
		'columns_small'     => '1',
	), gamipress_achievement_shortcode_defaults() ), $atts, $shortcode );

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
         * @since 2.1.2
         *
         * @param string    $output     Final output
         * @param array     $atts       Shortcode attributes
         * @param string    $content    Shortcode content
         */
	    return apply_filters( 'gamipress_last_achievements_earned_shortcode_no_user_output', '', $atts, $content );
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
             * @since 2.1.2
             *
             * @param string    $output     Final output
             * @param array     $atts       Shortcode attributes
             * @param string    $content    Shortcode content
             */
            return apply_filters( 'gamipress_last_achievements_earned_shortcode_no_achievement_output', '', $atts, $content );
        }

        // Set the achievement ID
        $atts['id'] = $achievement_id;

        $output = gamipress_achievement_shortcode( $atts, $content );
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
             * @since 2.1.2
             *
             * @param string    $output     Final output
             * @param array     $atts       Shortcode attributes
             * @param string    $content    Shortcode content
             */
            return apply_filters( 'gamipress_last_achievements_earned_shortcode_no_achievement_output', '', $atts, $content );
        }

        // Render the achievements
        $achievements = '';

        foreach( $last_earnings as $earning ) {
            $achievements .= gamipress_render_achievement( $earning->post_id, $atts );
        }

        // Setup the template args
        $gamipress_template_args = $atts;
        $gamipress_template_args['filter'] = 'no';
        $gamipress_template_args['filter_value'] = 'all';
        $gamipress_template_args['search'] = 'no';
        $gamipress_template_args['load_more'] = 'no';
        $gamipress_template_args['query'] = array(
            'achievements'      => $achievements,
            'offset'            => $atts['limit'],
            'query_count'       => $atts['limit'],
            'achievement_count' => $atts['limit'],
        );

        ob_start();
        gamipress_get_template_part( 'achievements', $atts['type'] );
        $output = ob_get_clean();
    }


    /**
     * Filter to override shortcode output
     *
     * @since 2.1.2
     *
     * @param string    $output     Final output
     * @param array     $atts       Shortcode attributes
     * @param string    $content    Shortcode content
     */
    return apply_filters( 'gamipress_last_achievements_earned_shortcode_output', $output, $atts, $content );

}
