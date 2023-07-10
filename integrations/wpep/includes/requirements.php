<?php
/**
 * Requirements
 *
 * @package GamiPress\WPEP\Requirements
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add custom fields to the requirement object
 *
 * @since 1.0.0
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_wpep_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wpep_complete_assessment_min_grade'
            || $requirement['trigger_type'] === 'gamipress_wpep_complete_specific_assessment_min_grade'
            || $requirement['trigger_type'] === 'gamipress_wpep_complete_assessment_max_grade'
            || $requirement['trigger_type'] === 'gamipress_wpep_complete_specific_assessment_max_grade' ) ) {

        // Minimum/Maximum grade percent
        $requirement['wpep_score'] = get_post_meta( $requirement_id, '_gamipress_wpep_score', true );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wpep_complete_assessment_between_grade'
            || $requirement['trigger_type'] === 'gamipress_wpep_complete_specific_assessment_between_grade' ) ) {

        // Between grade percent
        $requirement['wpep_min_score'] = get_post_meta( $requirement_id, '_gamipress_wpep_min_score', true );
        $requirement['wpep_max_score'] = get_post_meta( $requirement_id, '_gamipress_wpep_max_score', true );

    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_wpep_requirement_object', 10, 2 );

/**
 * Custom fields on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_wpep_requirement_ui_fields( $requirement_id, $post_id ) {

    $score = absint( get_post_meta( $requirement_id, '_gamipress_wpep_score', true ) );
    $min_score = get_post_meta( $requirement_id, '_gamipress_wpep_min_score', true );
    $max_score = get_post_meta( $requirement_id, '_gamipress_wpep_max_score', true );
    ?>

    <span class="wpep-assessment-score"><input type="text" value="<?php echo $score; ?>" size="3" maxlength="3" placeholder="100" />%</span>
    <span class="wpep-assessment-min-score"><input type="text" value="<?php echo ( ! empty( $min_score ) ? absint( $min_score ) : '' ); ?>" size="3" maxlength="3" placeholder="Min" />% -</span>
    <span class="wpep-assessment-max-score"><input type="text" value="<?php echo ( ! empty( $max_score ) ? absint( $max_score ) : '' ); ?>" size="3" maxlength="3" placeholder="Max" />%</span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_wpep_requirement_ui_fields', 10, 2 );

/**
 * Handler to save custom fields on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_wpep_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wpep_complete_assessment_min_grade'
            || $requirement['trigger_type'] === 'gamipress_wpep_complete_specific_assessment_min_grade'
            || $requirement['trigger_type'] === 'gamipress_wpep_complete_assessment_max_grade'
            || $requirement['trigger_type'] === 'gamipress_wpep_complete_specific_assessment_max_grade' ) ) {

        // Save the score field
        update_post_meta( $requirement_id, '_gamipress_wpep_score', $requirement['wpep_score'] );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wpep_complete_assessment_between_grade'
            || $requirement['trigger_type'] === 'gamipress_wpep_complete_specific_assessment_between_grade' ) ) {

        // Between grade percent
        update_post_meta( $requirement_id, '_gamipress_wpep_min_score', $requirement['wpep_min_score'] );
        update_post_meta( $requirement_id, '_gamipress_wpep_max_score', $requirement['wpep_max_score'] );

    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_wpep_ajax_update_requirement', 10, 2 );