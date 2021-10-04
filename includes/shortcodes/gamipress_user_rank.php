<?php
/**
 * GamiPress User Rank Shortcode
 *
 * @package     GamiPress\Shortcodes\Shortcode\GamiPress_User_Rank
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.3.9.3
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register [gamipress_user_rank] shortcode
 *
 * @since 1.0.0
 */
function gamipress_register_user_rank_shortcode() {

    // Setup the rank fields
    $rank_fields = GamiPress()->shortcodes['gamipress_rank']->fields;

    unset( $rank_fields['id'] );

    gamipress_register_shortcode( 'gamipress_user_rank', array(
        'name'              => __( 'User Rank', 'gamipress' ),
        'description'       => __( 'Display previous, current and/or next rank of a user.', 'gamipress' ),
        'icon' 	            => 'rank',
        'group' 	        => 'gamipress',
        'output_callback'   => 'gamipress_user_rank_shortcode',
        'tabs' => array(
            'general' => array(
                'icon' => 'dashicons-admin-generic',
                'title' => __( 'General', 'gamipress' ),
                'fields' => array(
                    'type',
                    'prev_rank',
                    'current_rank',
                    'next_rank',
                    'current_user',
                    'user_id',
                    'columns',
                ),
            ),
            'rank' => array(
                'icon' => 'dashicons-awards',
                'title' => __( 'Rank', 'gamipress' ),
                'fields' => array_keys( $rank_fields ),
            ),
        ),
        'fields'      => array_merge( array(
            'type' => array(
                'name'        => __( 'Rank Type', 'gamipress' ),
                'description' => __( 'Rank type to display.', 'gamipress' ),
                'type'        => 'select',
                'option_all'  => false,
                'option_none' => true,
                'options_cb'  => 'gamipress_options_cb_rank_types',
            ),
            'prev_rank' => array(
                'name'        => __( 'Show Previous Rank', 'gamipress' ),
                'description' => __( 'Show the previous user rank.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default'     => 'yes'
            ),
            'current_rank' => array(
                'name'        => __( 'Show Current Rank', 'gamipress' ),
                'description' => __( 'Show the current user rank.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default'     => 'yes'
            ),
            'next_rank' => array(
                'name'        => __( 'Show Next Rank', 'gamipress' ),
                'description' => __( 'Show the next user rank.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default'     => 'yes'
            ),
            'current_user' => array(
                'name'        => __( 'Current User', 'gamipress' ),
                'description' => __( 'Show the current logged in user ranks.', 'gamipress' ),
                'type' 		  => 'checkbox',
                'classes' 	  => 'gamipress-switch',
                'default'     => 'yes'
            ),
            'user_id' => array(
                'name'        => __( 'User', 'gamipress' ),
                'description' => __( 'Show a specific user ranks.', 'gamipress' ),
                'type'        => 'select',
                'default'     => '',
                'options_cb'  => 'gamipress_options_cb_users'
            ),
            'columns' => array(
                'name'        => __( 'Columns', 'gamipress' ),
                'description' => __( 'Columns to divide ranks.', 'gamipress' ),
                'type' 	=> 'select',
                'options' => array(
                    '1' => __( '1 Column', 'gamipress' ),
                    '2' => __( '2 Columns', 'gamipress' ),
                    '3' => __( '3 Columns', 'gamipress' ),
                ),
                'default' => '1'
            ),
        ), $rank_fields ),
    ) );

}
add_action( 'init', 'gamipress_register_user_rank_shortcode' );

/**
 * User Rank Shortcode
 *
 * @since  1.3.9.3
 *
 * @param  array    $atts       Shortcode attributes
 * @param  string   $content    Shortcode content
 *
 * @return string 	   HTML markup
 */
function gamipress_user_rank_shortcode( $atts = array(), $content = '' ) {

    global $gamipress_template_args;

    // Initialize GamiPress template args global
    $gamipress_template_args = array();

    $shortcode = 'gamipress_user_rank';

    $atts = shortcode_atts( array_merge( array(

        // User rank atts
        'type'        	=> '',
        'prev_rank'     => 'yes',
        'current_rank'  => 'yes',
        'next_rank' 	=> 'yes',
        'current_user' 	=> 'yes',
        'user_id' 		=> '0',
        'columns'       => '1',

    ), gamipress_rank_shortcode_defaults() ), $atts, $shortcode );

    // ---------------------------
    // Shortcode Errors
    // ---------------------------

    // Not type provided
    if( $atts['type'] === '' ) {
        return gamipress_shortcode_error( __( 'Please, provide any rank type.', 'gamipress' ), $shortcode );
    }

    // Wrong rank
    if( ! in_array( $atts['type'], gamipress_get_rank_types_slugs() ) ) {
        return gamipress_shortcode_error( __( 'The type provided isn\'t a valid registered rank type.', 'gamipress' ), $shortcode );
    }

    // Nothing to show
    if( $atts['prev_rank'] === 'no' && $atts['current_rank'] === 'no' && $atts['next_rank'] === 'no' ) {
        return gamipress_shortcode_error( __( 'None of the options to be displayed have been selected (previous, current or next). Please, select one.', 'gamipress' ), $shortcode );
    }

    // Force to set current user as user ID
    if( $atts['current_user'] === 'yes' ) {
        $atts['user_id'] = get_current_user_id();
    }

    // Not user ID provided
    if( $atts['current_user'] === 'no' && absint( $atts['user_id'] ) === 0 ) {
        return gamipress_shortcode_error( __( 'Please, provide the user ID.', 'gamipress' ), $shortcode );
    }

    // Guests not supported
    if( absint( $atts['user_id'] ) === 0 ) {
        return '';
    }

    // ---------------------------
    // Shortcode Processing
    // ---------------------------

    // Enqueue assets
    gamipress_enqueue_scripts();

    // On network wide active installs, we need to switch to main blog mostly for posts permalinks and thumbnails
    $blog_id = gamipress_switch_to_main_site_if_network_wide_active();

    // GamiPress template args global
    $gamipress_template_args = $atts;

    // Setup template vars
    $template_args = array(
        'user_id' 		=> $atts['user_id'], // User ID on rank is used to meet to which user apply earned checks
    );

    $rank_fields = GamiPress()->shortcodes['gamipress_rank']->fields;

    unset( $rank_fields['id'] );

    // Loop rank shortcode fields to pass to the rank template
    foreach( $rank_fields as $field_id => $field_args ) {

        if( isset( $atts[$field_id] ) ) {
            $template_args[$field_id] = $atts[$field_id];
        }
        
    }

    $gamipress_template_args['template_args'] = $template_args;

    // Try to load user-rank-{type}.php, if not exists then load user-rank.php
    ob_start();
    gamipress_get_template_part( 'user-rank', $atts['type'] );
    $output = ob_get_clean();

    // If switched to blog, return back to que current blog
    if( $blog_id !== get_current_blog_id() && is_multisite() ) {
        restore_current_blog();
    }

    /**
     * Filter to override shortcode output
     *
     * @since 1.6.5
     *
     * @param string    $output     Final output
     * @param array     $atts       Shortcode attributes
     * @param string    $content    Shortcode content
     */
    return apply_filters( 'gamipress_user_rank_shortcode_output', $output, $atts, $content );

}