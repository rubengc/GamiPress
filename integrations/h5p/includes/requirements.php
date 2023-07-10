<?php
/**
 * Requirements
 *
 * @package GamiPress\H5P\Requirements
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add the content type field to the requirement object
 *
 * @since 1.0.0
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_h5p_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type'
            || $requirement['trigger_type'] === 'gamipress_h5p_max_complete_specific_content_type'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_min_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_max_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_between_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_min_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_max_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_between_percentage' ) ) {

        // Specific content type
        $requirement['h5p_content_type'] = get_post_meta( $requirement_id, '_gamipress_h5p_content_type', true );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_h5p_complete_content_min_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_min_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_min_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_content_max_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_max_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_max_score' ) ) {

        // Min/max score
        $requirement['h5p_score'] = get_post_meta( $requirement_id, '_gamipress_h5p_score', true );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_h5p_complete_content_between_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_between_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_between_score' ) ) {

        // Between score
        $requirement['h5p_min_score'] = get_post_meta( $requirement_id, '_gamipress_h5p_min_score', true );
        $requirement['h5p_max_score'] = get_post_meta( $requirement_id, '_gamipress_h5p_max_score', true );

    }

   if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_h5p_complete_content_min_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_min_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_min_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_content_max_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_max_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_max_percentage' ) ) {

        // Percentage score
        $requirement['h5p_percentage'] = get_post_meta( $requirement_id, '_gamipress_h5p_percentage', true );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_h5p_complete_content_between_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_between_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_between_percentage' ) ) {

        // Percentage score
        $requirement['h5p_min_percentage'] = get_post_meta( $requirement_id, '_gamipress_h5p_min_percentage', true );
        $requirement['h5p_max_percentage'] = get_post_meta( $requirement_id, '_gamipress_h5p_max_percentage', true );
    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_h5p_requirement_object', 10, 2 );

/**
 * Content type field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_h5p_requirement_ui_fields( $requirement_id, $post_id ) {

    global $wpdb;

    // Get active libraries
    $content_types = $wpdb->get_results(
        "SELECT l.name, l.title
        FROM {$wpdb->prefix}h5p_libraries AS l
        WHERE l.runnable = 1"
    );

    $requirement_content_type = get_post_meta( $requirement_id, '_gamipress_h5p_content_type', true ); ?>

    <span class="h5p-content-type">
        <select>
        <?php foreach( $content_types as $content_type ) : ?>
            <option value="<?php echo $content_type->name; ?>" <?php selected( $requirement_content_type, $content_type->name ); ?>><?php echo $content_type->title; ?></option>
        <?php endforeach; ?>
        </select>
    </span>

    <?php

    $score = absint( get_post_meta( $requirement_id, '_gamipress_h5p_score', true ) );
    $min_score = get_post_meta( $requirement_id, '_gamipress_h5p_min_score', true );
    $max_score = get_post_meta( $requirement_id, '_gamipress_h5p_max_score', true );
    $percentage = get_post_meta( $requirement_id, '_gamipress_h5p_percentage', true );
    $min_percentage = get_post_meta( $requirement_id, '_gamipress_h5p_min_percentage', true );
    $max_percentage = get_post_meta( $requirement_id, '_gamipress_h5p_max_percentage', true );

    ?>

    <span class="h5p-score"><input type="text" value="<?php echo $score; ?>" size="3" maxlength="3" placeholder="100" /></span>
    <span class="h5p-min-score"><input type="text" value="<?php echo ( ! empty( $min_score ) ? absint( $min_score ) : '' ); ?>" size="3" maxlength="3" placeholder="Min" /> -</span>
    <span class="h5p-max-score"><input type="text" value="<?php echo ( ! empty( $max_score ) ? absint( $max_score ) : '' ); ?>" size="3" maxlength="3" placeholder="Max" /></span>
    <span class="h5p-percentage"><input type="text" value="<?php echo ( ! empty( $percentage ) ? absint( $percentage ) : '' ); ?>" size="3" maxlength="3" placeholder="" />%</span>
    <span class="h5p-min-percentage"><input type="text" value="<?php echo ( ! empty( $min_percentage ) ? absint( $min_percentage ) : '' ); ?>" size="3" maxlength="3" placeholder="Min" />% -</span>
    <span class="h5p-max-percentage"><input type="text" value="<?php echo ( ! empty( $max_percentage ) ? absint( $max_percentage ) : '' ); ?>" size="3" maxlength="3" placeholder="Max" />%</span>
    

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_h5p_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the content type on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_h5p_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type'
            || $requirement['trigger_type'] === 'gamipress_h5p_max_complete_specific_content_type'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_min_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_max_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_between_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_min_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_max_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_between_percentage' ) ) {

        // Save the content type field
        update_post_meta( $requirement_id, '_gamipress_h5p_content_type', $requirement['h5p_content_type'] );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_h5p_complete_content_min_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_min_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_min_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_content_max_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_max_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_max_score' ) ) {

        // Min/max score
        update_post_meta( $requirement_id, '_gamipress_h5p_score', $requirement['h5p_score'] );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_h5p_complete_content_between_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_between_score'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_between_score' ) ) {

        // Between score
        update_post_meta( $requirement_id, '_gamipress_h5p_min_score', $requirement['h5p_min_score'] );
        update_post_meta( $requirement_id, '_gamipress_h5p_max_score', $requirement['h5p_max_score'] );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_h5p_complete_content_min_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_min_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_min_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_content_max_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_max_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_max_percentage' ) ) {

        // Percentage score
        update_post_meta( $requirement_id, '_gamipress_h5p_percentage', $requirement['h5p_percentage'] );
    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_h5p_complete_content_between_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_between_percentage'
            || $requirement['trigger_type'] === 'gamipress_h5p_complete_specific_content_type_between_percentage' ) ) {

        // Percentage score
        update_post_meta( $requirement_id, '_gamipress_h5p_min_percentage', $requirement['h5p_min_percentage'] );
        update_post_meta( $requirement_id, '_gamipress_h5p_max_percentage', $requirement['h5p_max_percentage'] );
    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_h5p_ajax_update_requirement', 10, 2 );