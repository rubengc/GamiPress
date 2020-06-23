<?php
/**
 * Admin Social Settings
 *
 * @package     GamiPress\Admin\Settings\Social
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.8.6
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Social Settings meta boxes
 *
 * @since  1.8.6
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_settings_social_meta_boxes( $meta_boxes ) {

    $meta_boxes['social-settings'] = array(
        'title' => gamipress_dashicon( 'share' ) . __( 'Social Settings', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_social_settings_fields', array(
            'enable_share' => array(
                'name' => __( 'Enable social sharing', 'gamipress' ),
                'desc' => __( 'Check this option to allow users share the achievements and ranks they have earned.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            'social_buttons_preview' => array(
                'name' => __( 'Preview', 'gamipress' ),
                'type' => 'html',
                'content' =>
                    '<p class="gamipress-share-buttons-label">' . __( 'Share:', 'gamipress' ) . '</p>'
                    . '<a href="#" class="gamipress-share-button gamipress-share-button-facebook" title="' . __( 'Share on Facebook', 'gamipress' ) . '" data-network="facebook"></a>'
                    . '<a href="#" class="gamipress-share-button gamipress-share-button-twitter" title="' . __( 'Share on Twitter', 'gamipress' ) . '" data-network="twitter"></a>'
                    . '<a href="#" class="gamipress-share-button gamipress-share-button-linkedin" title="' . __( 'Share on LinkedIn', 'gamipress' ) . '" data-network="linkedin"></a>'
                    . '<a href="#" class="gamipress-share-button gamipress-share-button-pinterest" title="' . __( 'Share on Pinterest', 'gamipress' ) . '" data-network="pinterest"></a>',
            ),
            'social_networks' => array(
                'name' => __( 'Social Networks', 'gamipress' ),
                'type' => 'multicheck_inline',
                'classes' => 'gamipress-switch',
                'select_all_button' => false,
                'options' => array(
                    'facebook'  =>  __( 'Facebook', 'gamipress' ),
                    'twitter'   =>  __( 'Twitter', 'gamipress' ),
                    'linkedin'  =>  __( 'LinkedIn', 'gamipress' ),
                    'pinterest' =>  __( 'Pinterest', 'gamipress' ),
                ),
                'default' => array( 'facebook', 'twitter', 'linkedin', 'pinterest' )
            ),
            'social_button_style' => array(
                'name' => __( 'Button style', 'gamipress' ),
                'type' => 'radio_inline',
                'options' => array(
                    'square'   =>  __( 'Square', 'gamipress' ),
                    'rounded'  =>  __( 'Rounded', 'gamipress' ),
                    'circle'  =>  __( 'Circle', 'gamipress' ),
                ),
                'default' => 'square',
            ),
            'twitter_achievement_text' => array(
                'name' => __( 'Twitter achievement text', 'gamipress' ),
                'desc' => __( 'Default text when sharing an earned achievement on Twitter. Maximum 280 characters (leave at least 24 characters empty for the achievement URL).', 'gamipress' )
                    . '<br>' . __( 'Available tags:', 'gamipress' ) . gamipress_get_pattern_tags_html( 'achievement_earned' ),
                'type' => 'textarea_small',
                'char_counter' => true,
                'char_max' => 280,
                'default' => __( 'I earned the {achievement_type} {achievement_title} on {site_title}', 'gamipress' ),
            ),
            'twitter_rank_text' => array(
                'name' => __( 'Twitter rank text', 'gamipress' ),
                'desc' => __( 'Default text when sharing an earned rank on Twitter. Maximum 280 characters (leave at least 24 characters empty for the rank URL).', 'gamipress' )
                    . '<br>' . __( 'Available tags:', 'gamipress' ) . gamipress_get_pattern_tags_html( 'rank_earned' ),
                'type' => 'textarea_small',
                'char_counter' => true,
                'char_max' => 280,
                'default' => __( 'I reached the {rank_type} {rank_title} on {site_title}', 'gamipress' ),
            ),
            'enable_open_graph_tags' => array(
                'name' => __( 'Open Graph meta tags', 'gamipress' ),
                'desc' => __( 'Open Graph meta tags are human-invisible information required to format the look of your website URLs when shared on social networks. There are plugins that already place them like Yoast SEO, if you don\'t have one, check this option to let GamiPress insert those tags on the achievements and ranks pages.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_social_meta_boxes', 'gamipress_settings_social_meta_boxes' );