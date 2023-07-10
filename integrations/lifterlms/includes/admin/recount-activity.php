<?php
/**
 * Recount Activity
 *
 * @package GamiPress\LifterLMS\Admin\Recount_Activity
 * @since 1.0.9
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add recountable options to the Recount Activity Tool
 *
 * @since 1.0.9
 *
 * @param array $recountable_activity_triggers
 *
 * @return array
 */
function gamipress_lifterlms_recountable_activity_triggers( $recountable_activity_triggers ) {

    // LearnDash
    $recountable_activity_triggers[__( 'LifterLMS', 'gamipress' )] = array(
        'lifterlms_quizzes'    => __( 'Recount quizzes completed', 'gamipress' ),
        'lifterlms_lessons'     => __( 'Recount lessons completed', 'gamipress' ),
        'lifterlms_sections'    => __( 'Recount sections completed', 'gamipress' ),
        'lifterlms_courses'    => __( 'Recount courses completed', 'gamipress' ),
    );

    return $recountable_activity_triggers;

}
add_filter( 'gamipress_recountable_activity_triggers', 'gamipress_lifterlms_recountable_activity_triggers' );

/**
 * Recount quizzes completed
 *
 * @since   1.0.9
 * @updated 1.1.0 Added $loop parameter
 *
 * @param array $response
 * @param int   $loop
 * @param int   $limit
 * @param int   $offset
 *
 * @return array $response
 */
function gamipress_lifterlms_activity_recount_quizzes( $response, $loop, $limit, $offset ) {

    global $wpdb;

    // Get all stored users count
    $users_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users} AS u" ) );

    // On first loop send an informational text
    if( $loop === 0 && $users_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count );
        $response['log'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count ) . "<br>\n";

        // Return early to inform
        return $response;
    }

    // Get all stored users
    $users = $wpdb->get_results( "SELECT u.ID FROM {$wpdb->users} AS u LIMIT {$offset}, {$limit}" );

    foreach( $users as $user ) {

        $student_quizzes = new LLMS_Student_Quizzes( $user->ID );

        if( ! $student_quizzes ) {
            continue;
        }

        $quizzes = $student_quizzes->get_all();

        if( ! is_array( $quizzes ) ) {
            continue;
        }

        foreach( $quizzes as $quiz ) {

            $quiz_id = $quiz->get( 'id' );

            // Call to quiz completion listener
            gamipress_lifterlms_complete_quiz( $user->ID, $quiz_id, $quiz );

            $lesson_id = $quiz->get( 'lesson_id' );
            $course = llms_get_post_parent_course( $lesson_id );
            $course_id = 0;

            if( $course ) {
                $course_id = $course->get( 'id' );
            }

            if( $course_id !== 0 ) {
                $response['log'] .= sprintf( __( '[Complete quiz] Quiz: %s User: %s Course: %s', 'gamipress' ),
                        '<a href="' . get_edit_post_link( $quiz_id ) . '" target="_blank">' . $quiz_id . '</a>',
                        '<a href="' . get_edit_user_link( $user->ID ) . '" target="_blank">' . $user->ID . '</a>',
                        '<a href="' . get_edit_post_link( $course_id ) . '" target="_blank">' . $course_id . '</a>'
                    ) . "<br>\n";
            } else {
                $response['log'] .= sprintf( __( '[Complete quiz] Quiz: %s User: %s', 'gamipress' ),
                        '<a href="' . get_edit_post_link( $quiz_id ) . '" target="_blank">' . $quiz_id . '</a>',
                        '<a href="' . get_edit_user_link( $user->ID ) . '" target="_blank">' . $user->ID . '</a>'
                    ) . "<br>\n";
            }
        }
    }

    $recounted_users = $limit * ( $loop + 1 );

    // Check remaining posts
    if( $recounted_users < $users_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining users to finish recount', 'gamipress' ), ( $users_count - $recounted_users ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_lifterlms_quizzes', 'gamipress_lifterlms_activity_recount_quizzes', 10, 4 );

/**
 * Recount topics completed
 *
 * @since   1.0.9
 * @updated 1.1.0 Added $loop parameter
 *
 * @param array $response
 * @param int   $loop
 * @param int   $limit
 * @param int   $offset
 *
 * @return array $response
 */
function gamipress_lifterlms_activity_recount_lessons( $response, $loop, $limit, $offset ) {

    global $wpdb, $wp_current_filter;

    // Get all stored users count
    $users_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users} AS u" ) );

    // On first loop send an informational text
    if( $loop === 0 && $users_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count );
        $response['log'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count ) . "<br>\n";

        // Return early to inform
        return $response;
    }

    // Get all stored users
    $users = $wpdb->get_results( "SELECT u.ID FROM {$wpdb->users} AS u LIMIT {$offset}, {$limit}" );

    foreach( $users as $user ) {

        $student = new LLMS_Student( $user->ID );

        if( ! $student ) {
            continue;
        }

        // Get all user courses completed
        $courses = $student->get_completed_courses();

        if( ! is_array( $courses ) ) {
            continue;
        }

        foreach( $courses as $course_id ) {

            $llms_course = new LLMS_Course( $course_id );

            if( ! $llms_course ) {
                continue;
            }

            $lessons = $llms_course->get_lessons( 'ids' );

            if( ! is_array( $lessons ) ) {
                continue;
            }

            foreach( $lessons as $lesson_id ) {
                // Force current filter to be the one required by LifterLMS
                $wp_current_filter[] = 'lifterlms_lesson_completed';

                gamipress_lifterlms_common_listener( $user->ID, $lesson_id );

                $response['log'] .= sprintf( __( '[Complete course] Lesson: %s Course: %s User: %s', 'gamipress' ),
                        '<a href="' . get_edit_post_link( $lesson_id ) . '" target="_blank">' . $lesson_id . '</a>',
                        '<a href="' . get_edit_post_link( $course_id ) . '" target="_blank">' . $course_id . '</a>',
                        '<a href="' . get_edit_user_link( $user->ID ) . '" target="_blank">' . $user->ID . '</a>'
                    ) . "<br>\n";
            }

        }
    }

    $recounted_users = $limit * ( $loop + 1 );

    // Check remaining posts
    if( $recounted_users < $users_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining users to finish recount', 'gamipress' ), ( $users_count - $recounted_users ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_lifterlms_lessons', 'gamipress_lifterlms_activity_recount_lessons', 10, 4 );

/**
 * Recount lessons completed
 *
 * @since   1.0.9
 * @updated 1.1.0 Added $loop parameter
 *
 * @param array $response
 * @param int   $loop
 * @param int   $limit
 * @param int   $offset
 *
 * @return array $response
 */
function gamipress_lifterlms_activity_recount_sections( $response, $loop, $limit, $offset ) {

    global $wpdb, $wp_current_filter;

    // Get all stored users count
    $users_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users} AS u" ) );

    // On first loop send an informational text
    if( $loop === 0 && $users_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count );
        $response['log'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count ) . "<br>\n";

        // Return early to inform
        return $response;
    }

    // Get all stored users
    $users = $wpdb->get_results( "SELECT u.ID FROM {$wpdb->users} AS u LIMIT {$offset}, {$limit}" );

    foreach( $users as $user ) {

        $student = new LLMS_Student( $user->ID );

        if( ! $student ) {
            continue;
        }

        // Get all user courses completed
        $courses = $student->get_completed_courses();

        if( ! is_array( $courses ) ) {
            continue;
        }

        foreach( $courses as $course_id ) {

            $llms_course = new LLMS_Course( $course_id );

            if( ! $llms_course ) {
                continue;
            }

            $sections = $llms_course->get_sections( 'ids' );

            if( ! is_array( $sections ) ) {
                continue;
            }

            foreach( $sections as $section_id ) {
                // Force current filter to be the one required by LifterLMS
                $wp_current_filter[] = 'lifterlms_section_completed';

                gamipress_lifterlms_common_listener( $user->ID, $section_id );

                $response['log'] .= sprintf( __( '[Complete course] Section: %s Course: %s User: %s', 'gamipress' ),
                        '<a href="' . get_edit_post_link( $section_id ) . '" target="_blank">' . $section_id . '</a>',
                        '<a href="' . get_edit_post_link( $course_id ) . '" target="_blank">' . $course_id . '</a>',
                        '<a href="' . get_edit_user_link( $user->ID ) . '" target="_blank">' . $user->ID . '</a>'
                    ) . "<br>\n";
            }

        }
    }

    $recounted_users = $limit * ( $loop + 1 );

    // Check remaining posts
    if( $recounted_users < $users_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining users to finish recount', 'gamipress' ), ( $users_count - $recounted_users ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_lifterlms_sections', 'gamipress_lifterlms_activity_recount_sections', 10, 4 );

/**
 * Recount courses completed
 *
 * @since   1.0.9
 * @updated 1.1.0 Added $loop parameter
 *
 * @param array $response
 * @param int   $loop
 * @param int   $limit
 * @param int   $offset
 *
 * @return array $response
 */
function gamipress_lifterlms_activity_recount_courses( $response, $loop, $limit, $offset ) {

    global $wpdb, $wp_current_filter;

    // Get all stored users count
    $users_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users} AS u" ) );

    // On first loop send an informational text
    if( $loop === 0 && $users_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count );
        $response['log'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count ) . "<br>\n";

        // Return early to inform
        return $response;
    }

    // Get all stored users
    $users = $wpdb->get_results( "SELECT u.ID FROM {$wpdb->users} AS u LIMIT {$offset}, {$limit}" );

    foreach( $users as $user ) {

        $student = new LLMS_Student( $user->ID );

        if( ! $student ) {
            continue;
        }

        // Get all user courses completed
        $courses = $student->get_completed_courses();

        if( ! is_array( $courses ) ) {
            continue;
        }

        foreach( $courses as $course_id ) {

            // Force current filter to be the one required by LifterLMS
            $wp_current_filter[] = 'lifterlms_course_completed';

            gamipress_lifterlms_common_listener( $user->ID, $course_id );

            $response['log'] .= sprintf( __( '[Complete course] Course: %s User: %s', 'gamipress' ),
                    '<a href="' . get_edit_post_link( $course_id ) . '" target="_blank">' . $course_id . '</a>',
                    '<a href="' . get_edit_user_link( $user->ID ) . '" target="_blank">' . $user->ID . '</a>'
                ) . "<br>\n";

        }
    }

    $recounted_users = $limit * ( $loop + 1 );

    // Check remaining posts
    if( $recounted_users < $users_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining users to finish recount', 'gamipress' ), ( $users_count - $recounted_users ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_lifterlms_courses', 'gamipress_lifterlms_activity_recount_courses', 10, 4 );