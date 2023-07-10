<?php
/**
 * Functions
 *
 * @package GamiPress\wpDiscuz\Functions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the comment user ID
 *
 * @param WP_Comment $comment
 *
 * @return int
 */
function gamipress_wpdiscuz_get_commment_user_id( $comment ) {

    $user_id = absint( $comment->user_id );

    // If comment has not assigned a user id and wpDiscuz is configured to use the user email, then need to search this user by email
    if ( $user_id === 0 && wpDiscuz()->options->login["isUserByEmail"] ) {

        // Get the user by email
        $user = get_user_by( 'email', $comment->comment_author_email );

        if ($user)
            $user_id = $user->ID;
    }

    return absint( $user_id );
}