<?php
/**
 * Recount Activity
 *
 * @package GamiPress\LearnDash\Admin\Recount_Activity
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
function gamipress_ld_recountable_activity_triggers( $recountable_activity_triggers ) {

    // LearnDash
    $recountable_activity_triggers[__( 'LearnDash', 'gamipress' )] = array(
        'ld_quizzes'    => __( 'Recount quizzes completed', 'gamipress' ),
        'ld_topics'     => __( 'Recount topics completed', 'gamipress' ),
        'ld_lessons'    => __( 'Recount lessons completed', 'gamipress' ),
        'ld_courses'    => __( 'Recount courses completed', 'gamipress' ),
    );

    return $recountable_activity_triggers;

}
add_filter( 'gamipress_recountable_activity_triggers', 'gamipress_ld_recountable_activity_triggers' );

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
function gamipress_ld_activity_recount_quizzes( $response, $loop, $limit, $offset ) {

    global $wpdb;

    // Meta where information resides
    $meta_key = '_sfwd-quizzes';

    // Get all stored users count
    $users_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users} AS u LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id = u.ID AND um.meta_key = '{$meta_key}' WHERE um.meta_key IS NOT NULL" ) );

    // On first loop send an informational text
    if( $loop === 0 && $users_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count );
        $response['log'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count ) . "<br>\n";

        // Return early to inform
        return $response;
    }

    // Get all stored users
    $users = $wpdb->get_results( "SELECT u.ID FROM {$wpdb->users} AS u LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id = u.ID AND um.meta_key = '{$meta_key}' WHERE um.meta_key IS NOT NULL LIMIT {$offset}, {$limit}" );

    foreach( $users as $user ) {

        // Get all user quizzes completed
        $quizzes = get_user_meta( $user->ID, $meta_key, true );

        // Skip if user does not have completed any quizzes
        if( ! is_array( $quizzes ) ) {
            continue;
        }

        foreach( $quizzes as $quiz ) {

            $quiz['quiz'] = get_post( $quiz['quiz'] );

            // Skip incorrect quizzes
            if( ! $quiz['quiz'] )
                continue;

            // Ensure required data
            $quiz['course'] = get_post( $quiz['course'] );

            if( ! isset( $quiz['percentage'] ) )
                $quiz['percentage'] = 0;

            if( ! isset( $quiz['pass'] ) )
                $quiz['pass'] = 0;

            // Call to all separated quiz completion listeners
            gamipress_ld_complete_quiz_listener( $quiz, $user );
            gamipress_ld_complete_quiz_min_grade_listener( $quiz, $user );
            gamipress_ld_complete_quiz_max_grade_listener( $quiz, $user );
            gamipress_ld_complete_quiz_between_grade_listener( $quiz, $user );
            gamipress_ld_pass_quiz_listener( $quiz, $user );
            gamipress_ld_fail_quiz_listener( $quiz, $user );

            $quiz_id = $quiz['quiz']->ID;
            $course_id = ( $quiz['course'] instanceof WP_Post ? $quiz['course']->ID : 0 );

            if( $course_id !== 0 ) {
                $response['log'] .= sprintf( __( '[Complete quiz] Quiz: %s User: %s Course: %s Quiz Score: %s', 'gamipress' ),
                    '<a href="' . get_edit_post_link( $quiz_id ) . '" target="_blank">' . $quiz_id . '</a>',
                    '<a href="' . get_edit_user_link( $user->ID ) . '" target="_blank">' . $user->ID . '</a>',
                    '<a href="' . get_edit_post_link( $course_id ) . '" target="_blank">' . $course_id . '</a>',
                    absint( $quiz['percentage'] )
                ) . "<br>\n";
            } else {
                $response['log'] .= sprintf( __( '[Complete quiz] Quiz: %s User: %s Quiz Score: %s', 'gamipress' ),
                    '<a href="' . get_edit_post_link( $quiz_id ) . '" target="_blank">' . $quiz_id . '</a>',
                    '<a href="' . get_edit_user_link( $user->ID ) . '" target="_blank">' . $user->ID . '</a>',
                    absint( $quiz['percentage'] )
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
add_filter( 'gamipress_activity_recount_ld_quizzes', 'gamipress_ld_activity_recount_quizzes', 10, 4 );

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
function gamipress_ld_activity_recount_topics( $response, $loop, $limit, $offset ) {

    global $wpdb;

    // Meta where information resides
    $meta_key = '_sfwd-course_progress';

    // Get all stored users count
    $users_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users} AS u LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id = u.ID AND um.meta_key = '{$meta_key}' WHERE um.meta_key IS NOT NULL" ) );

    // On first loop send an informational text
    if( $loop === 0 && $users_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count );
        $response['log'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count ) . "<br>\n";

        // Return early to inform
        return $response;
    }

    // Get all stored users
    $users = $wpdb->get_results( "SELECT u.ID FROM {$wpdb->users} AS u LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id = u.ID AND um.meta_key = '{$meta_key}' WHERE um.meta_key IS NOT NULL LIMIT {$offset}, {$limit}" );

    foreach( $users as $user ) {

        // Get all user courses completed
        $courses = get_user_meta( $user->ID, $meta_key, true );

        // Skip if user does not have completed any courses
        if( ! is_array( $courses ) ) {
            continue;
        }

        foreach( $courses as $course_id => $course ) {

            // Skip if user does not have completed any topics
            if( ! is_array( $course['topics'] ) ) {
                continue;
            }

            // Loop all topics completed (topics are separated in lessons)
            foreach( $course['topics'] as $lesson_id => $topics ) {

                // Skip if user does not have completed any topics
                if( ! is_array( $topics ) ) {
                    continue;
                }

                // Loop all lesson topics completed
                foreach( $topics as $topic_id => $completed ) {

                    if( $completed ) {

                        $args = array(
                            'topic' => get_post( $topic_id ),
                            'user' => $user,
                            'lesson' => get_post( $lesson_id ),
                            'course' => get_post( $course_id ),
                        );

                        // Skip incorrect topics
                        if( ! $args['topic'] )
                            continue;

                        gamipress_ld_complete_topic( $args );

                        $response['log'] .= sprintf( __( '[Complete topic] Topic: %s User: %s Lesson: %s Course: %s', 'gamipress' ),
                            '<a href="' . get_edit_post_link( $topic_id ) . '" target="_blank">' . $topic_id . '</a>',
                            '<a href="' . get_edit_user_link( $user->ID ) . '" target="_blank">' . $user->ID . '</a>',
                            '<a href="' . get_edit_post_link( $lesson_id ) . '" target="_blank">' . $lesson_id . '</a>',
                            '<a href="' . get_edit_post_link( $course_id ) . '" target="_blank">' . $course_id . '</a>'
                        ) . "<br>\n";
                    }

                }

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
add_filter( 'gamipress_activity_recount_ld_topics', 'gamipress_ld_activity_recount_topics', 10, 4 );

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
function gamipress_ld_activity_recount_lessons( $response, $loop, $limit, $offset ) {

    global $wpdb;

    // Meta where information resides
    $meta_key = '_sfwd-course_progress';

    // Get all stored users count
    $users_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users} AS u LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id = u.ID AND um.meta_key = '{$meta_key}' WHERE um.meta_key IS NOT NULL" ) );

    // On first loop send an informational text
    if( $loop === 0 && $users_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count );
        $response['log'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count ) . "<br>\n";

        // Return early to inform
        return $response;
    }

    // Get all stored users
    $users = $wpdb->get_results( "SELECT u.ID FROM {$wpdb->users} AS u LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id = u.ID AND um.meta_key = '{$meta_key}' WHERE um.meta_key IS NOT NULL LIMIT {$offset}, {$limit}" );

    foreach( $users as $user ) {

        // Get all user courses completed
        $courses = get_user_meta( $user->ID, $meta_key, true );

        // Skip if user does not have completed any courses
        if( ! is_array( $courses ) ) {
            continue;
        }

        foreach( $courses as $course_id => $course ) {

            // Skip if user does not have completed any lessons
            if( ! is_array( $course['lessons'] ) ) {
                continue;
            }

            // Loop all lessons completed
            foreach( $course['lessons'] as $lesson_id => $completed ) {

                if( $completed ) {

                    $args = array(
                        'user' => $user,
                        'lesson' => get_post( $lesson_id ),
                        'course' => get_post( $course_id ),
                    );

                    // Skip incorrect lessons
                    if( ! $args['lesson'] )
                        continue;

                    gamipress_ld_complete_lesson( $args );

                    $response['log'] .= sprintf( __( '[Complete lesson] Lesson: %s User: %s Course: %s', 'gamipress' ),
                        '<a href="' . get_edit_post_link( $lesson_id ) . '" target="_blank">' . $lesson_id . '</a>',
                        '<a href="' . get_edit_user_link( $user->ID ) . '" target="_blank">' . $user->ID . '</a>',
                        '<a href="' . get_edit_post_link( $course_id ) . '" target="_blank">' . $course_id . '</a>'
                    ) . "<br>\n";
                }

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
add_filter( 'gamipress_activity_recount_ld_lessons', 'gamipress_ld_activity_recount_lessons', 10, 4 );

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
function gamipress_ld_activity_recount_courses( $response, $loop, $limit, $offset ) {

    global $wpdb;

    // Meta where information resides
    $meta_key = '_sfwd-course_progress';

    // Get all stored users count
    $users_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users} AS u LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id = u.ID AND um.meta_key = '{$meta_key}' WHERE um.meta_key IS NOT NULL" ) );

    // On first loop send an informational text
    if( $loop === 0 && $users_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count );
        $response['log'] = sprintf( __( '%d users found, recounting...', 'gamipress' ), $users_count ) . "<br>\n";

        // Return early to inform
        return $response;
    }

    // Get all stored users
    $users = $wpdb->get_results( "SELECT u.ID FROM {$wpdb->users} AS u LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id = u.ID AND um.meta_key = '{$meta_key}' WHERE um.meta_key IS NOT NULL LIMIT {$offset}, {$limit}" );

    foreach( $users as $user ) {

        // Get all user courses completed
        $courses = get_user_meta( $user->ID, $meta_key, true );

        // Skip if user does not have completed any courses
        if( ! is_array( $courses ) ) {
            continue;
        }

        foreach( $courses as $course_id => $course ) {

            $completed = get_user_meta( $user->ID, 'course_completed_' . $course_id, true );

            if( $completed ) {

                $args = array(
                    'user' => $user,
                    'course' => get_post( $course_id ),
                );

                // Skip incorrect courses
                if( ! $args['course'] )
                    continue;

                gamipress_ld_complete_course( $args );

                $response['log'] .= sprintf( __( '[Complete course] Course: %s User: %s', 'gamipress' ),
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
add_filter( 'gamipress_activity_recount_ld_courses', 'gamipress_ld_activity_recount_courses', 10, 4 );