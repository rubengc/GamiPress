<?php
/**
 * Requirements
 *
 * @package GamiPress\LearnDash\Requirements
 * @since 1.0.4
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
function gamipress_ld_requirement_object( $requirement, $requirement_id ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_specific_quiz_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_specific_course_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_category_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_tag_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_max_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_specific_quiz_max_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_specific_course_max_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_category_max_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_tag_max_grade' ) ) {

        // Minimum/Maximum grade percent
        $requirement['ld_score'] = get_post_meta( $requirement_id, '_gamipress_ld_score', true );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_between_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_specific_quiz_between_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_specific_course_between_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_category_between_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_tag_between_grade' ) ) {

        // Between grade percent
        $requirement['ld_min_score'] = get_post_meta( $requirement_id, '_gamipress_ld_min_score', true );
        $requirement['ld_max_score'] = get_post_meta( $requirement_id, '_gamipress_ld_max_score', true );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_category'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_category_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_category_max_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_category_between_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_pass_quiz_course_category'
            || $requirement['trigger_type'] === 'gamipress_ld_fail_quiz_course_category'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_topic_course_category'
            || $requirement['trigger_type'] === 'gamipress_ld_assignment_upload_course_category'
            || $requirement['trigger_type'] === 'gamipress_ld_approve_assignment_course_category'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_lesson_course_category'
            || $requirement['trigger_type'] === 'gamipress_ld_enroll_course_category'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_course_category' ) ) {

        // Course category
        $requirement['ld_course_category_id'] = get_post_meta( $requirement_id, '_gamipress_ld_course_category_id', true );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_tag_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_tag_max_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_tag_between_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_pass_quiz_course_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_fail_quiz_course_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_topic_course_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_assignment_upload_course_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_approve_assignment_course_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_lesson_course_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_enroll_course_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_course_tag' ) ) {

        // Course tag
        $requirement['ld_course_tag_id'] = get_post_meta( $requirement_id, '_gamipress_ld_course_tag_id', true );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ld_complete_topic_category' ) ) {

        // Topic category
        $requirement['ld_topic_category_id'] = get_post_meta( $requirement_id, '_gamipress_ld_topic_category_id', true );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ld_complete_topic_tag' ) ) {

        // Topic tag
        $requirement['ld_topic_tag_id'] = get_post_meta( $requirement_id, '_gamipress_ld_topic_tag_id', true );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ld_assignment_upload_lesson_category'
            || $requirement['trigger_type'] === 'gamipress_ld_approve_assignment_lesson_category'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_lesson_category' ) ) {

        // Lesson category
        $requirement['ld_lesson_category_id'] = get_post_meta( $requirement_id, '_gamipress_ld_lesson_category_id', true );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ld_assignment_upload_lesson_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_approve_assignment_lesson_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_lesson_tag' ) ) {

        // Lesson tag
        $requirement['ld_lesson_tag_id'] = get_post_meta( $requirement_id, '_gamipress_ld_lesson_tag_id', true );

    }

    return $requirement;
}
add_filter( 'gamipress_requirement_object', 'gamipress_ld_requirement_object', 10, 2 );

/**
 * Category field on requirements UI
 *
 * @param $requirement_id
 * @param $post_id
 */
function gamipress_ld_requirement_ui_fields( $requirement_id, $post_id ) {

    $score = absint( get_post_meta( $requirement_id, '_gamipress_ld_score', true ) );
    $min_score = get_post_meta( $requirement_id, '_gamipress_ld_min_score', true );
    $max_score = get_post_meta( $requirement_id, '_gamipress_ld_max_score', true );
    ?>

    <span class="ld-quiz-score"><input type="text" value="<?php echo $score; ?>" size="3" maxlength="3" placeholder="100" />%</span>
    <span class="ld-quiz-min-score"><input type="text" value="<?php echo ( ! empty( $min_score ) ? absint( $min_score ) : '' ); ?>" size="3" maxlength="3" placeholder="Min" />% -</span>
    <span class="ld-quiz-max-score"><input type="text" value="<?php echo ( ! empty( $max_score ) ? absint( $max_score ) : '' ); ?>" size="3" maxlength="3" placeholder="Max" />%</span>

    <?php

    // Course taxonomy fields
    gamipress_ld_taxonomy_fields( $requirement_id, 'course' );

    // Topic taxonomy fields
    gamipress_ld_taxonomy_fields( $requirement_id, 'topic' );

    // Lesson taxonomy fields
    gamipress_ld_taxonomy_fields( $requirement_id, 'lesson' );
}
add_action( 'gamipress_requirement_ui_html_after_achievement_post', 'gamipress_ld_requirement_ui_fields', 10, 2 );

/**
 * Helper function to generate the category and tag fields
 *
 * @param int $requirement_id
 * @param string $element
 */
function gamipress_ld_taxonomy_fields( $requirement_id, $element ) {

    // Category select
    $category_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_ld_' . $element . '_category_id', true ) );
    $categories = get_terms( array(
        'taxonomy' => 'ld_' . $element . '_category',
        'hide_empty' => false,
    ) );

    ?>

    <span class="ld-<?php echo $element; ?>-category">
        <select>
            <?php if( is_array( $categories ) && count( $categories ) ) : ?>
                <?php foreach( $categories as $category ) : ?>
                    <option value="<?php echo $category->term_id; ?>" <?php selected( $category_id, $category->term_id ); ?>><?php echo $category->name; ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </span>

    <?php

    // Tag select
    $tag_id = absint( gamipress_get_post_meta( $requirement_id, '_gamipress_ld_' . $element . '_tag_id', true ) );
    $tags = get_terms( array(
        'taxonomy' => 'ld_' . $element . '_tag',
        'hide_empty' => false,
    ) );

    ?>

    <span class="ld-<?php echo $element; ?>-tag">
        <select>
            <?php if( is_array( $tags ) && count( $tags ) ) : ?>
                <?php foreach( $tags as $tag ) : ?>
                    <option value="<?php echo $tag->term_id; ?>" <?php selected( $tag_id, $tag->term_id ); ?>><?php echo $tag->name; ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </span>

    <?php
}

/**
 * Custom handler to save the score on requirements UI
 *
 * @param $requirement_id
 * @param $requirement
 */
function gamipress_ld_ajax_update_requirement( $requirement_id, $requirement ) {

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_specific_quiz_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_specific_course_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_category_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_tag_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_max_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_specific_quiz_max_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_specific_course_max_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_category_max_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_tag_max_grade' ) ) {

        // Save the score field
        update_post_meta( $requirement_id, '_gamipress_ld_score', $requirement['ld_score'] );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_between_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_specific_quiz_between_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_specific_course_between_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_category_between_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_tag_between_grade' ) ) {

        // Between grade percent
        update_post_meta( $requirement_id, '_gamipress_ld_min_score', $requirement['ld_min_score'] );
        update_post_meta( $requirement_id, '_gamipress_ld_max_score', $requirement['ld_max_score'] );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_category'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_category_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_category_max_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_category_between_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_pass_quiz_course_category'
            || $requirement['trigger_type'] === 'gamipress_ld_fail_quiz_course_category'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_topic_course_category'
            || $requirement['trigger_type'] === 'gamipress_ld_assignment_upload_course_category'
            || $requirement['trigger_type'] === 'gamipress_ld_approve_assignment_course_category'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_lesson_course_category'
            || $requirement['trigger_type'] === 'gamipress_ld_enroll_course_category'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_course_category' ) ) {

        // Course category
        update_post_meta( $requirement_id, '_gamipress_ld_course_category_id', $requirement['ld_course_category_id'] );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_tag_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_tag_max_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_quiz_course_tag_between_grade'
            || $requirement['trigger_type'] === 'gamipress_ld_pass_quiz_course_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_fail_quiz_course_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_topic_course_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_assignment_upload_course_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_approve_assignment_course_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_lesson_course_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_enroll_course_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_course_tag' ) ) {

        // Course tag
        update_post_meta( $requirement_id, '_gamipress_ld_course_tag_id', $requirement['ld_course_tag_id'] );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ld_complete_topic_category' ) ) {

        // Topic category
        update_post_meta( $requirement_id, '_gamipress_ld_topic_category_id', $requirement['ld_topic_category_id'] );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ld_complete_topic_tag' ) ) {

        // Topic tag
        update_post_meta( $requirement_id, '_gamipress_ld_topic_tag_id', $requirement['ld_topic_tag_id'] );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ld_assignment_upload_lesson_category'
            || $requirement['trigger_type'] === 'gamipress_ld_approve_assignment_lesson_category'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_lesson_category' ) ) {

        // Lesson category
        update_post_meta( $requirement_id, '_gamipress_ld_lesson_category_id', $requirement['ld_lesson_category_id'] );

    }

    if( isset( $requirement['trigger_type'] )
        && ( $requirement['trigger_type'] === 'gamipress_ld_assignment_upload_lesson_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_approve_assignment_lesson_tag'
            || $requirement['trigger_type'] === 'gamipress_ld_complete_lesson_tag' ) ) {

        // Lesson tag
        update_post_meta( $requirement_id, '_gamipress_ld_lesson_tag_id', $requirement['ld_lesson_tag_id'] );

    }
}
add_action( 'gamipress_ajax_update_requirement', 'gamipress_ld_ajax_update_requirement', 10, 2 );