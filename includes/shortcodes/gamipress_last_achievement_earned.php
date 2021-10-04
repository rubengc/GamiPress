<?php
/**
 * GamiPress Last Achievement Earned Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Last_Achievement_Earned
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register [gamipress_last_achievement_earned] shortcode
 *
 * @since 1.0.0
 */
function gamipress_register_last_achievement_earned_shortcode() {

	$achievement_fields = GamiPress()->shortcodes['gamipress_achievement']->fields;

	unset( $achievement_fields['id'] );

	gamipress_register_shortcode( 'gamipress_last_achievement_earned', array(
		'name'              => __( 'Last Achievement Earned', 'gamipress' ),
		'description'       => __( 'Display the last achievement earned by the current user or a desired user.', 'gamipress' ),
        'icon' 	            => 'awards',
        'group' 	        => 'gamipress',
		'output_callback'   => 'gamipress_last_achievement_earned_shortcode',
        'tabs' => array(
            'general' => array(
                'icon' => 'dashicons-admin-generic',
                'title' => __( 'General', 'gamipress' ),
                'fields' => array(
                    'type',
                    'current_user',
                    'user_id',
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
		), $achievement_fields ),
	) );

}
add_action( 'init', 'gamipress_register_last_achievement_earned_shortcode' );

/**
 * Last Achievement Earned Shortcode
 *
 * @since  1.0.0
 *
 * @param  array    $atts      Shortcode attributes
 * @param  string   $content   Shortcode content
 *
 * @return string 	   HTML markup
 */
function gamipress_last_achievement_earned_shortcode( $atts = array(), $content = '' ) {

	global $gamipress_template_args;

	// Initialize GamiPress template args global
	$gamipress_template_args = array();

    $shortcode = 'gamipress_last_achievement_earned';

	$atts = shortcode_atts( array_merge( array(
		'type'        	    => '',
		'current_user'      => 'yes',
		'user_id'     	    => '0',
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
	    return apply_filters( 'gamipress_last_achievement_earned_shortcode_no_user_output', '', $atts, $content );
    }

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
        return apply_filters( 'gamipress_last_achievement_earned_shortcode_no_achievement_output', '', $atts, $content );
    }

    // Set the achievement ID
    $atts['id'] = $achievement_id;

	$output = gamipress_achievement_shortcode( $atts, $content );

    /**
     * Filter to override shortcode output
     *
     * @since 2.1.2
     *
     * @param string    $output     Final output
     * @param array     $atts       Shortcode attributes
     * @param string    $content    Shortcode content
     */
    return apply_filters( 'gamipress_last_achievement_earned_shortcode_output', $output, $atts, $content );

}
