<?php
/**
 * Requirements
 *
 * @package GamiPress\Sensei\Requirements
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add the score field to the requirement object
 *
 * @since  1.0.0
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_sensei_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_sensei_complete_quiz_grade'
            || $requirement['trigger_type'] === 'gamipress_sensei_complete_specific_quiz_grade'
            || $requirement['trigger_type'] === 'gamipress_sensei_complete_quiz_max_grade'
            || $requirement['trigger_type'] === 'gamipress_sensei_complete_specific_quiz_max_grade' ) ) {

        // Minimum grade percent
        $requirement['sensei_score'] = get_post_meta( $requirement_id, '_gamipress_sensei_score', true );

    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_sensei_requirement_object', 10, 2 );

/**
 * Score field on requirements UI
 *
 * @since  1.0.0
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_sensei_requirement_ui_fields( $requirement_id, $post_id ) {

    $score = absint( get_post_meta( $requirement_id, '_gamipress_sensei_score', true ) );
    ?>

    <span class="sensei-quiz-score"><input type="text" value="<?php echo $score; ?>" size="3" maxlength="3" placeholder="100" />%</span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_sensei_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the score on requirements UI
 *
 * @since  1.0.0
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_sensei_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_sensei_complete_quiz_grade'
            || $requirement['trigger_type'] === 'gamipress_sensei_complete_specific_quiz_grade'
            || $requirement['trigger_type'] === 'gamipress_sensei_complete_quiz_max_grade'
            || $requirement['trigger_type'] === 'gamipress_sensei_complete_specific_quiz_max_grade' ) ) {

        // Save the score field
        update_post_meta( $requirement_id, '_gamipress_sensei_score', $requirement['sensei_score'] );

    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_sensei_ajax_update_requirement', 10, 2 );