<?php
/**
 * Admin
 *
 * @package GamiPress\wpDiscuz\Admin
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Shortcut function to get plugin options
 *
 * @since  1.0.0
 *
 * @param string    $option_name
 * @param bool      $default
 *
 * @return mixed
 */
function gamipress_wpdiscuz_get_option( $option_name, $default = false ) {

    $prefix = 'wpdiscuz_';

    return gamipress_get_option( $prefix . $option_name, $default );

}

/**
 * Plugin Settings meta boxes
 *
 * @since  1.0.0
 *
 * @param $meta_boxes
 *
 * @return mixed
 */
function gamipress_wpdiscuz_settings_meta_boxes( $meta_boxes ) {

    $prefix = 'wpdiscuz_';

    $meta_boxes['gamipress-wpdiscuz-settings'] = array(
        'title' => __( 'wpDiscuz', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_wpdiscuz_settings_fields', array(

            // Points Types

            $prefix . 'points_types' => array(
                'name' => __( 'User Points Types', 'gamipress' ),
                'desc' => __( 'Choose the points types you want to show on user reply details.', 'gamipress' )
                    . '<br>' . __( '<strong>Note:</strong> You can drag and drop this options to reorder them. The order you set will affect to the order they will get displayed on the reply details.', 'gamipress' ),
                'type' => 'multicheck',
                'options_cb' => 'gamipress_wpdiscuz_points_types_option_cb',
            ),
            $prefix . 'points_types_display_title' => array(
                'name' => __( 'Display Options', 'gamipress' ),
                'desc' => __( 'Setup how points types should look on user reply details.', 'gamipress' ),
                'type' => 'title',
            ),
            $prefix . 'points_types_thumbnail' => array(
                'name' => __( 'Show Thumbnail', 'gamipress' ),
                'desc' => __( 'Show the points type featured image.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'points_types_thumbnail_size' => array(
                'name' => __( 'Thumbnail Size', 'gamipress' ),
                'desc' => __( 'Set the thumbnail size (in pixels).', 'gamipress' ),
                'type' => 'text',
                'default' => '25',
            ),
            $prefix . 'points_types_label' => array(
                'name' => __( 'Show Label', 'gamipress' ),
                'desc' => __( 'Show the points type singular or plural label.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),

            // Achievement Types

            $prefix . 'achievement_types' => array(
                'name' => __( 'User Profile Achievement Types', 'gamipress' ),
                'desc' => __( 'Choose the achievement types you want to show on user reply details.', 'gamipress' )
                    . '<br>' . __( '<strong>Note:</strong> You can drag and drop this options to reorder them. The order you set will affect to the order they will get displayed on the reply details.', 'gamipress' ),
                'type' => 'multicheck',
                'options_cb' => 'gamipress_wpdiscuz_achievement_types_option_cb',
            ),
            $prefix . 'achievement_types_display_title' => array(
                'name' => __( 'Display Options', 'gamipress' ),
                'desc' => __( 'Setup how achievements should look on user reply details.', 'gamipress' ),
                'type' => 'title',
            ),
            $prefix . 'achievement_types_thumbnail' => array(
                'name' => __( 'Show Thumbnail', 'gamipress' ),
                'desc' => __( 'Show the achievement featured image.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'achievement_types_thumbnail_size' => array(
                'name' => __( 'Thumbnail Size', 'gamipress' ),
                'desc' => __( 'Set the thumbnail size (in pixels).', 'gamipress' ),
                'type' => 'text',
                'default' => '25',
            ),
            $prefix . 'achievement_types_title' => array(
                'name' => __( 'Show Title', 'gamipress' ),
                'desc' => __( 'Show the achievements title.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'achievement_types_link' => array(
                'name' => __( 'Show Link', 'gamipress' ),
                'desc' => __( 'Add a link to the achievement page.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'achievement_types_label' => array(
                'name' => __( 'Show Label', 'gamipress' ),
                'desc' => __( 'Show the achievement type label.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'achievements_limit' => array(
                'name' => __( 'Number of achievements to show', 'gamipress' ),
                'desc' => __( 'Setup the number of achievements to show. Leave blank if you want to show all user earned achievement', 'gamipress' ),
                'type' => 'text',
            ),

            // Rank Types

            $prefix . 'rank_types' => array(
                'name' => __( 'User Profile Rank Types', 'gamipress' ),
                'desc' => __( 'Choose the rank types you want to show on user reply details.', 'gamipress' )
                    . '<br>' . __( '<strong>Note:</strong> You can drag and drop this options to reorder them. The order you set will affect to the order they will get displayed on the reply details.', 'gamipress' ),
                'type' => 'multicheck',
                'options_cb' => 'gamipress_wpdiscuz_rank_types_option_cb',
            ),
            $prefix . 'rank_types_display_title' => array(
                'name' => __( 'Display Options', 'gamipress' ),
                'desc' => __( 'Setup how ranks should look on user reply details.', 'gamipress' ),
                'type' => 'title',
            ),
            $prefix . 'rank_types_thumbnail' => array(
                'name' => __( 'Show Thumbnail', 'gamipress' ),
                'desc' => __( 'Show the rank featured image.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'rank_types_thumbnail_size' => array(
                'name' => __( 'Thumbnail Size', 'gamipress' ),
                'desc' => __( 'Set the thumbnail size (in pixels).', 'gamipress' ),
                'type' => 'text',
                'default' => '25',
            ),
            $prefix . 'rank_types_title' => array(
                'name' => __( 'Show Title', 'gamipress' ),
                'desc' => __( 'Show the ranks title.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'rank_types_link' => array(
                'name' => __( 'Show Link', 'gamipress' ),
                'desc' => __( 'Add a link to the rank page.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'rank_types_label' => array(
                'name' => __( 'Show Label', 'gamipress' ),
                'desc' => __( 'Show the rank type label.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
        ) ),
        'vertical_tabs' => true,
        'tabs' => apply_filters( 'gamipress_wpdiscuz_settings_tabs', array(
            'points' => array(
                'title' => __( 'Points', 'gamipress' ),
                'icon' => 'dashicons-star-filled',
                'fields' => array(
                    $prefix . 'points_types',
                    $prefix . 'points_types_display_title',
                    $prefix . 'points_types_thumbnail',
                    $prefix . 'points_types_thumbnail_size',
                    $prefix . 'points_types_label',
                )
            ),
            'achievements' => array(
                'title' => __( 'Achievements', 'gamipress' ),
                'icon' => 'dashicons-awards',
                'fields' => array(
                    $prefix . 'achievement_types',
                    $prefix . 'achievement_types_display_title',
                    $prefix . 'achievement_types_thumbnail',
                    $prefix . 'achievement_types_thumbnail_size',
                    $prefix . 'achievement_types_title',
                    $prefix . 'achievement_types_link',
                    $prefix . 'achievement_types_label',
                    $prefix . 'achievements_limit',
                )
            ),
            'ranks' => array(
                'title' => __( 'Ranks', 'gamipress' ),
                'icon' => 'dashicons-rank',
                'fields' => array(
                    $prefix . 'rank_types',
                    $prefix . 'rank_types_display_title',
                    $prefix . 'rank_types_thumbnail',
                    $prefix . 'rank_types_thumbnail_size',
                    $prefix . 'rank_types_title',
                    $prefix . 'rank_types_link',
                    $prefix . 'rank_types_label',
                )
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_addons_meta_boxes', 'gamipress_wpdiscuz_settings_meta_boxes' );

function gamipress_wpdiscuz_points_types_option_cb() {

    $points_types_slugs = gamipress_get_points_types_slugs();
    $points_types = gamipress_get_points_types();

    return gamipress_wpdiscuz_order_options( 'wpdiscuz_points_types_order', $points_types_slugs, $points_types );

}

function gamipress_wpdiscuz_achievement_types_option_cb() {

    $achievement_types_slugs = array_diff(
        gamipress_get_achievement_types_slugs(),
        gamipress_get_requirement_types_slugs()
    );
    $achievement_types = gamipress_get_achievement_types();

    return gamipress_wpdiscuz_order_options( 'wpdiscuz_achievement_types_order', $achievement_types_slugs, $achievement_types );

}

function gamipress_wpdiscuz_rank_types_option_cb() {

    $rank_types_slugs = gamipress_get_rank_types_slugs();
    $rank_types = gamipress_get_rank_types();

    return gamipress_wpdiscuz_order_options( 'wpdiscuz_rank_types_order', $rank_types_slugs, $rank_types );

}

/**
 * Common function to handle options generation
 *
 * @param string $setting_key
 * @param array $items
 * @param array $types
 *
 * @return array
 */
function gamipress_wpdiscuz_order_options( $setting_key, $items, $types ) {

    $gamipress_settings = ( $exists = get_option( 'gamipress_settings' ) ) ? $exists : array();

    $items_order = isset( $gamipress_settings[$setting_key] ) ?
        $gamipress_settings[$setting_key] : $items;

    $options = array();

    foreach( $items_order as $item_order ) {

        $options[$item_order] = '<input type="hidden" name="' . esc_attr( $setting_key ) . '[]" value="' . esc_attr( $item_order ) . '" />'
            . $types[$item_order]['plural_name'];
    }

    $unordered_items = array_diff( $items, $items_order );

    // Append new rank types
    foreach( $unordered_items as $unordered_item ) {
        $options[$unordered_item] = '<input type="hidden" name="' . esc_attr( $setting_key ) . '[]" value="' . esc_attr( $unordered_item ) . '" />'
            . $types[$unordered_item]['plural_name'];
    }

    return $options;

}

function gamipress_wpdiscuz_save_gamipress_settings() {

    if( ! isset( $_POST['submit-cmb'] ) )
        return;

    if( ! ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'gamipress_settings' ) )
        return;

    if( ! isset( $_POST['wpdiscuz_points_types_order'] ) || ! isset( $_POST['wpdiscuz_achievement_types_order'] ) || ! isset( $_POST['wpdiscuz_rank_types_order'] ) )
        return;

    // Setup GamiPress options
    $gamipress_settings = ( $exists = get_option( 'gamipress_settings' ) ) ? $exists : array();

    // Setup new setting
    $gamipress_settings['wpdiscuz_points_types_order'] = gamipress_wpdiscuz_sanitize_text_array( $_POST['wpdiscuz_points_types_order'] );
    $gamipress_settings['wpdiscuz_achievement_types_order'] = gamipress_wpdiscuz_sanitize_text_array( $_POST['wpdiscuz_achievement_types_order'] );
    $gamipress_settings['wpdiscuz_rank_types_order'] = gamipress_wpdiscuz_sanitize_text_array( $_POST['wpdiscuz_rank_types_order'] );

    // Update GamiPress settings
    update_option( 'gamipress_settings', $gamipress_settings );

}
add_action( 'cmb2_save_options-page_fields', 'gamipress_wpdiscuz_save_gamipress_settings' );

/**
 * Sanitize an array of text elements
 *
 * @since 1.0.0
 *
 * @param array $items
 * @return array
 */
function gamipress_wpdiscuz_sanitize_text_array( $items ) {

    foreach( $items as $i => $item )
        $items[$i] = sanitize_text_field( $item );

    return $items;

}

/**
 * Plugin automatic updates
 *
 * @since  1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_wpdiscuz_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress'] = __( 'wpDiscuz integration', 'gamipress' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_wpdiscuz_automatic_updates' );