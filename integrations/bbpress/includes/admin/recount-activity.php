<?php
/**
 * Recount Activity
 *
 * @package GamiPress\bbPress\Admin\Recount_Activity
 * @since 1.0.2
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add recountable options to the Recount Activity Tool
 *
 * @since 1.0.2
 *
 * @param array $recountable_activity_triggers
 *
 * @return array
 */
function gamipress_bbp_recountable_activity_triggers( $recountable_activity_triggers ) {

    $recountable_activity_triggers[__( 'bbPress', 'gamipress' )] = array(
        'bbp_activities' => __( 'Recount forum activities (forums, topics and replies)', 'gamipress' ),
        'bbp_favorites' => __( 'Recount topics favorites', 'gamipress' ),
    );

    return $recountable_activity_triggers;

}
add_filter( 'gamipress_recountable_activity_triggers', 'gamipress_bbp_recountable_activity_triggers' );

/**
 * Recount bbPress activity
 *
 * @since 1.0.2
 *
 * @param array $response
 * @param int   $loop
 * @param int   $limit
 * @param int   $offset
 *
 * @return array $response
 */
function gamipress_bbp_activity_recount_activities( $response, $loop, $limit, $offset ) {

    global $wpdb;

    $post_types = array(
        bbp_get_forum_post_type(),
        bbp_get_topic_post_type(),
        bbp_get_reply_post_type(),
    );

    // Get all stored posts count
    $posts_count = absint( $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts AS p WHERE p.post_type IN ('" . implode( "', '", $post_types ) . "') AND p.post_status = 'publish'" ) );

    // On first loop send an informational text
    if( $loop === 0 && $posts_count > $limit ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d items found, recounting...', 'gamipress' ), $posts_count );
        $response['log'] = sprintf( __( '%d items found, recounting...', 'gamipress' ), $posts_count ) . "<br>\n";

        // Return early to inform
        return $response;
    }

    // Get all published forums, topics and replies
    $posts = $wpdb->get_results(
        "SELECT p.ID, p.post_type, p.post_author, p.post_parent FROM $wpdb->posts AS p WHERE p.post_type IN ('" . implode( "', '", $post_types ) . "') AND p.post_status = 'publish' LIMIT {$offset}, {$limit}"
    );

    foreach( $posts as $post ) {

        if( $post->post_type === bbp_get_forum_post_type() ) {

            // Trigger new forum action for each forum
            gamipress_bbp_new_forum( array(
                'forum_id' => $post->ID,
                'forum_author' => $post->post_author,
            ) );

            $response['log'] .= sprintf( __( '[Create forum] Forum: %s User: %s', 'gamipress-learndash-integration' ),
                '<a href="' . get_edit_post_link( $post->ID ) . '" target="_blank">' . $post->ID . '</a>',
                '<a href="' . get_edit_user_link( $post->post_author ) . '" target="_blank">' . $post->post_author . '</a>'
            ) . "<br>\n";

        } else if( $post->post_type === bbp_get_topic_post_type() ) {

            // Trigger new topic action for each topic
            gamipress_bbp_new_topic( $post->ID, $post->post_parent, array(), $post->post_author );

            $response['log'] .= sprintf( __( '[Create topic] Topic: %s Forum: %s User: %s', 'gamipress-learndash-integration' ),
                        '<a href="' . get_edit_post_link( $post->ID ) . '" target="_blank">' . $post->ID . '</a>',
                        '<a href="' . get_edit_post_link( $post->post_parent ) . '" target="_blank">' . $post->post_parent . '</a>',
                        '<a href="' . get_edit_user_link( $post->post_author ) . '" target="_blank">' . $post->post_author . '</a>'
            ) . "<br>\n";

        } else if( $post->post_type === bbp_get_reply_post_type() ) {

            $forum_id = wp_get_post_parent_id( $post->post_parent );

            if( $forum_id ) {
                // Trigger new reply action for each reply
                gamipress_bbp_new_reply( $post->ID, $post->post_parent, $forum_id, array(), $post->post_author );

                $response['log'] .= sprintf( __( '[Create reply] Reply: %s Topic: %s Forum: %s User: %s', 'gamipress-learndash-integration' ),
                        '<a href="' . get_edit_post_link( $post->ID ) . '" target="_blank">' . $post->ID . '</a>',
                        '<a href="' . get_edit_post_link( $post->post_parent ) . '" target="_blank">' . $post->post_parent . '</a>',
                        '<a href="' . get_edit_post_link( $forum_id ) . '" target="_blank">' . $forum_id . '</a>',
                        '<a href="' . get_edit_user_link( $post->post_author ) . '" target="_blank">' . $post->post_author . '</a>'
                ) . "<br>\n";

            }

        }

    }

    $recounted_posts = $limit * ( $loop + 1 );

    // Check remaining posts
    if( $recounted_posts <= $posts_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining items to finish recount', 'gamipress' ), ( $posts_count - $recounted_posts ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_bbp_activities', 'gamipress_bbp_activity_recount_activities', 10, 4 );


/**
 * Recount favorites
 *
 * @since 1.0.2
 *
 * @param array $response
 *
 * @return array $response
 */
function gamipress_bbp_activity_recount_favorites( $response, $loop, $limit, $offset ) {

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

        // Get all favorites topics
        $favorites = bbp_get_user_favorites_topic_ids( $user->ID );

        if( $favorites ) {

            foreach( $favorites as $topic_id ) {
                // Trigger favorite action for each favorite topic
                gamipress_bbp_favorite_topic( $user->ID, $topic_id );
            }

        }

    }

    $recounted_users = $limit * ( $loop + 1 );

    // Check remaining posts
    if( $recounted_users <= $users_count ) {
        $response['run_again'] = true;
        $response['message'] = sprintf( __( '%d remaining users to finish recount', 'gamipress' ), ( $users_count - $recounted_users ) );
    }

    return $response;

}
add_filter( 'gamipress_activity_recount_bbp_favorites', 'gamipress_bbp_activity_recount_favorites', 10, 4 );