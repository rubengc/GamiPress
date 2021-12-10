<?php
/**
 * GamiPress Email Settings Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_Email_Settings
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register [gamipress_email_settings] shortcode
 *
 * @since 1.0.0
 */
function gamipress_register_email_settings_shortcode() {

    gamipress_register_shortcode( 'gamipress_email_settings', array(
        'name'              => __( 'Email Settings', 'gamipress' ),
        'description'       => __( 'Display the user email notifications preferences for the GamiPress emails.', 'gamipress' ),
        'icon' 	            => 'email',
        'group' 	        => 'gamipress',
        'output_callback'   => 'gamipress_email_settings_shortcode',
        'fields'      => array(
            'groups' => array(
                'name'        => __( 'Divide per groups', 'gamipress' ),
                'description' => __( 'Show email settings divided per groups.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' 	  => 'yes',
            ),
            'types' => array(
                'name'        => __( 'Divide per types', 'gamipress' ),
                'description' => __( 'Show email settings divided per type.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' 	  => 'yes',
            ),
            'current_user' => array(
                'name'        => __( 'Current User', 'gamipress' ),
                'description' => __( 'Show the email settings of the current logged in user.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default' 	    => 'yes',
            ),
            'user_id' => array(
                'name'        => __( 'User', 'gamipress' ),
                'description' => __( 'Show the email settings of a specific user.', 'gamipress' ),
                'type'        => 'select',
                'default'     => '',
                'options_cb'  => 'gamipress_options_cb_users'
            ),
        ),
    ) );

}
add_action( 'init', 'gamipress_register_email_settings_shortcode' );

/**
 * Logs List Shortcode
 *
 * @since  1.0.0
 *
 * @param  array    $atts       Shortcode attributes
 * @param  string   $content    Shortcode content
 *
 * @return string 	   HTML markup
 */
function gamipress_email_settings_shortcode( $atts = array(), $content = '' ) {

    global $gamipress_template_args;

    // Initialize GamiPress template args global
    $gamipress_template_args = array();

    $shortcode = 'gamipress_email_settings';

    $atts = shortcode_atts( array(
        'groups'  => 'yes',
        'types'   => 'yes',
        'current_user'      => 'yes',
        'user_id'     	    => '0',
    ), $atts, $shortcode );

    // Force to set current user as user ID
    if( $atts['current_user'] === 'yes' ) {
        $atts['user_id'] = get_current_user_id();
    } else if( absint( $atts['user_id'] ) === 0 ) {
        $atts['user_id'] = get_current_user_id();
    }

    // Return if current user is a guest
    if( $atts['user_id'] === 0 ) {
        /**
         * Filter to override the message for not logged in users
         *
         * @since 2.2.1
         *
         * @param string    $message    Message to display to not logged in users
         * @param array     $atts       Shortcode attributes
         * @param string    $content    Shortcode content
         */
        return apply_filters( 'gamipress_email_settings_shortcode_not_logged_in_message', __( 'Please, log in to view your email preferences.', 'gamipress' ), $atts, $content );
    }

    // Enqueue assets
    gamipress_enqueue_scripts();

    // GamiPress template args global
    $gamipress_template_args = $atts;

    // Setup the email settings sections to display
    $email_settings = array();

    $email_settings['all'] = array(
        'label' => __( 'Gamification', 'gamipress' ),
        'settings' => array(
            'all' => __( 'Receive emails related to gamification', 'gamipress' ),
        )
    );

    // Add settings per group if enabled
    if( $atts['groups'] === 'yes' ) {

        // Only add the points types section if any of its emails is enabled
        if( ! gamipress_get_option( 'disable_points_award_completed_email', false ) || ! gamipress_get_option( 'disable_points_deduct_completed_email', false ) ) {

            $email_settings['points_types'] = array(
                'label' => __( 'Points', 'gamipress' ),
                'settings' => array(
                    'points_types' => __( 'Receive emails related to points balance movements', 'gamipress' ),
                )
            );

            // Add settings per type if enabled
            if( $atts['types'] === 'yes' ) {
                foreach ( gamipress_get_points_types() as $type => $data ) {
                    $email_settings['points_types']['settings']['points_types_' . $type] = sprintf( __( 'Receive emails related to %s balance movements', 'gamipress' ), $data['plural_name'] );
                }
            }

        }

        // Only add the achievement types section if any of its emails is enabled
        if( ! gamipress_get_option( 'disable_achievement_earned_email', false ) || ! gamipress_get_option( 'disable_step_completed_email', false ) ) {

            $email_settings['achievement_types'] = array(
                'label' => __( 'Achievements', 'gamipress' ),
                'settings' => array(
                    'achievement_types' => __( 'Receive emails related to achievements', 'gamipress' ),
                )
            );

            // Add settings per type if enabled
            if( $atts['types'] === 'yes' ) {
                foreach ( gamipress_get_achievement_types() as $type => $data ) {
                    $email_settings['achievement_types']['settings']['achievement_types_' . $type] = sprintf( __( 'Receive emails related to %s', 'gamipress' ), $data['plural_name'] );
                }
            }

        }

        // Only add the rank types section if any of its emails is enabled
        if( ! gamipress_get_option( 'disable_rank_earned_email', false ) || ! gamipress_get_option( 'disable_rank_requirement_completed_email', false ) ) {

            $email_settings['rank_types'] = array(
                'label' => __( 'Ranks', 'gamipress' ),
                'settings' => array(
                    'rank_types' => __( 'Receive emails related to ranks', 'gamipress' ),
                )
            );

            // Add settings per type if enabled
            if( $atts['types'] === 'yes' ) {
                foreach ( gamipress_get_rank_types() as $type => $data ) {
                    $email_settings['rank_types']['settings']['rank_types_' . $type] = sprintf( __( 'Receive emails related to %s', 'gamipress' ), $data['plural_name'] );
                }
            }

        }
    }

    /**
     * Filter to override the email settings
     *
     * @since 2.2.1
     *
     * @param array     $email_settings The email settings to display
     * @param array     $atts           Shortcode attributes
     * @param string    $content        Shortcode content
     */
    $email_settings = apply_filters( 'gamipress_email_settings_shortcode_settings', $email_settings, $atts, $content );

    $gamipress_template_args['email_settings'] = $email_settings;

    ob_start();
        gamipress_get_template_part( 'email-settings' );
    $output = ob_get_clean();

    /**
     * Filter to override shortcode output
     *
     * @since 1.6.5
     *
     * @param string    $output     Final output
     * @param array     $atts       Shortcode attributes
     * @param string    $content    Shortcode content
     */
    return apply_filters( 'gamipress_email_settings_shortcode_output', $output, $atts, $content );

}
