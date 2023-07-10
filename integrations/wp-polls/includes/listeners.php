<?php
/**
 * Listeners
 *
 * @package GamiPress\WP_Polls\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function gamipress_wp_polls_vote_poll() {

    // Get the poll ID
    $poll_id = ( isset($_REQUEST['poll_id'] ) ? (int) sanitize_key( $_REQUEST['poll_id'] ) : 0 );

    if( $poll_id === 0 ) return;

    $user_id = get_current_user_id();

    // Guests not allowed yet
    if( $user_id === 0 ) return;

    // Award user for vote a poll
    do_action( 'gamipress_wp_polls_vote_poll', $poll_id, $user_id );

    // Award user for vote a specific poll
    do_action( 'gamipress_wp_polls_vote_specific_poll', $poll_id, $user_id );

}
add_action( 'wp_polls_vote_poll_success', 'gamipress_wp_polls_vote_poll' );