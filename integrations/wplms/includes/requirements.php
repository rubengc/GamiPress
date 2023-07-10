<?php
/**
 * Requirements
 *
 * @package GamiPress\WPLMS\Requirements
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add the score field to the requirement object
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_wplms_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wplms_complete_course_minimum_mark'
            || $requirement['trigger_type'] === 'gamipress_wplms_complete_specific_course_minimum_mark'
            || $requirement['trigger_type'] === 'gamipress_wplms_complete_quiz_minimum_mark'
            || $requirement['trigger_type'] === 'gamipress_wplms_complete_specific_quiz_minimum_mark'
            || $requirement['trigger_type'] === 'gamipress_wplms_complete_assignment_minimum_mark'
            || $requirement['trigger_type'] === 'gamipress_wplms_complete_specific_assignment_minimum_mark' ) ) {

        // Minimum grade percent
        $requirement['wplms_score'] = get_post_meta( $requirement_id, '_gamipress_wplms_score', true );

    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_wplms_requirement_object', 10, 2 );

/**
 * Score field on requirements UI
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_wplms_requirement_ui_fields( $requirement_id, $post_id ) {

    $score = absint( get_post_meta( $requirement_id, '_gamipress_wplms_score', true ) );
    ?>

    <span class="wplms-score"><input type="text" value="<?php echo $score; ?>" size="3" maxlength="3" placeholder="100" /></span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_wplms_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the score on requirements UI
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_wplms_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_wplms_complete_course_minimum_mark'
            || $requirement['trigger_type'] === 'gamipress_wplms_complete_specific_course_minimum_mark'
            || $requirement['trigger_type'] === 'gamipress_wplms_complete_quiz_minimum_mark'
            || $requirement['trigger_type'] === 'gamipress_wplms_complete_specific_quiz_minimum_mark'
            || $requirement['trigger_type'] === 'gamipress_wplms_complete_assignment_minimum_mark'
            || $requirement['trigger_type'] === 'gamipress_wplms_complete_specific_assignment_minimum_mark' ) ) {

        // Save the score field
        update_post_meta( $requirement_id, '_gamipress_wplms_score', $requirement['wplms_score'] );

    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_wplms_ajax_update_requirement', 10, 2 );