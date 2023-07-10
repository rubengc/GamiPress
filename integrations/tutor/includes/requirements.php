<?php
/**
 * Requirements
 *
 * @package GamiPress\Tutor\Requirements
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add the custom field to the requirement object
 *
 * @since 1.0.0
 *
 * @param $requirement
 * @param $requirement_id
 *
 * @return array
 */
function gamipress_tutor_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_tutor_complete_quiz_course_category'
            || $requirement['trigger_type'] === 'gamipress_tutor_pass_quiz_course_category'
            || $requirement['trigger_type'] === 'gamipress_tutor_fail_quiz_course_category'
            || $requirement['trigger_type'] === 'gamipress_tutor_complete_lesson_course_category'
            || $requirement['trigger_type'] === 'gamipress_tutor_complete_course_category'
            || $requirement['trigger_type'] === 'gamipress_tutor_enroll_course_category' ) ) {

        // Tutor category
        $requirement['tutor_category'] = get_post_meta( $requirement_id, '_gamipress_tutor_category', true );
    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_tutor_requirement_object', 10, 2 );

/**
 * Custom field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_tutor_requirement_ui_fields( $requirement_id, $post_id ) {

    $selected = get_post_meta( $requirement_id, '_gamipress_tutor_category', true );

    // Get tutor categories
    $terms = get_terms( array( 
        'taxonomy' => 'course-category',
        'hide_empty' => false,
    ) );

    ?>

    <select class="select-tutor-category">
        <?php foreach( $terms as $term ) : ?>
            <option value="<?php echo $term->term_id ?>" <?php selected( $selected, $term->term_id ); ?>><?php echo $term->name; ?></option>
        <?php endforeach; ?>
    </select>
    

    <?php
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_tutor_requirement_ui_fields', 10, 2 );

/**
 * Custom handler to save the custom field on requirements UI
 *
 * @since 1.0.0
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_tutor_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_tutor_complete_quiz_course_category'
            || $requirement['trigger_type'] === 'gamipress_tutor_pass_quiz_course_category'
            || $requirement['trigger_type'] === 'gamipress_tutor_fail_quiz_course_category'
            || $requirement['trigger_type'] === 'gamipress_tutor_complete_lesson_course_category'
            || $requirement['trigger_type'] === 'gamipress_tutor_complete_course_category'
            || $requirement['trigger_type'] === 'gamipress_tutor_enroll_course_category' ) ) {

        // Tutor category
        update_post_meta( $requirement_id, '_gamipress_tutor_category', $requirement['tutor_category'] );

    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_tutor_ajax_update_requirement', 10, 2 );