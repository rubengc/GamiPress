<?php
/**
 * Requirements
 *
 * @package GamiPress\Thrive_Quiz_Builder\Requirements
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add custom fields to the requirement object
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_thrive_quiz_builder_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_thrive_quiz_builder_complete_percentage_quiz'
            || $requirement['trigger_type'] === 'gamipress_thrive_quiz_builder_complete_specific_percentage_quiz' ) ) {

        // Percentage
        $requirement['thrive_quiz_builder_percentage_condition'] = get_post_meta( $requirement_id, '_gamipress_thrive_quiz_builder_percentage_condition', true );
        $requirement['thrive_quiz_builder_percentage'] = get_post_meta( $requirement_id, '_gamipress_thrive_quiz_builder_percentage', true );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_thrive_quiz_builder_complete_quiz_type' ) ) {

        // Quiz type
        $requirement['thrive_quiz_builder_quiz_type'] = get_post_meta( $requirement_id, '_gamipress_thrive_quiz_builder_quiz_type', true );

    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_thrive_quiz_builder_requirement_object', 10, 2 );

/**
 * Category field on requirements UI
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_thrive_quiz_builder_requirement_ui_fields( $requirement_id, $post_id ) {

    $percentage_conditions = gamipress_thrive_quiz_builder_get_percentage_conditions();
    $percentage_condition = get_post_meta( $requirement_id, '_gamipress_thrive_quiz_builder_percentage_condition', true );
    $percentage = absint( get_post_meta( $requirement_id, '_gamipress_thrive_quiz_builder_percentage', true ) );

    $quiz_types = gamipress_thrive_quiz_builder_get_quiz_types();
    $quiz_type = get_post_meta( $requirement_id, '_gamipress_thrive_quiz_builder_quiz_type', true );
    ?>

    <span class="thrive-quiz-builder-quiz-percentage">
        <select>
            <?php foreach( $percentage_conditions as $id => $name ) : ?>
                <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $percentage_condition, $id ); ?>><?php echo esc_html( $name ); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" value="<?php echo esc_attr( $percentage ); ?>" size="3" maxlength="3" placeholder="100" />%
    </span>

    <span class="thrive-quiz-builder-quiz-type">
        <select>
            <?php foreach( $quiz_types as $id => $name ) : ?>
                <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $quiz_type, $id ); ?>><?php echo esc_html( $name ); ?></option>
            <?php endforeach; ?>
        </select>
    </span>

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_thrive_quiz_builder_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the custom fields on requirements UI
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_thrive_quiz_builder_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_thrive_quiz_builder_complete_percentage_quiz'
            || $requirement['trigger_type'] === 'gamipress_thrive_quiz_builder_complete_specific_percentage_quiz' ) ) {

        // Percentage
        update_post_meta( $requirement_id, '_gamipress_thrive_quiz_builder_percentage_condition', $requirement['thrive_quiz_builder_percentage_condition'] );
        update_post_meta( $requirement_id, '_gamipress_thrive_quiz_builder_percentage', $requirement['thrive_quiz_builder_percentage'] );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_thrive_quiz_builder_complete_quiz_type' ) ) {

        // Quiz type
        update_post_meta( $requirement_id, '_gamipress_thrive_quiz_builder_quiz_type', $requirement['thrive_quiz_builder_quiz_type'] );

    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_thrive_quiz_builder_ajax_update_requirement', 10, 2 );