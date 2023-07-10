<?php
/**
 * Listeners
 *
 * @package GamiPress\Groundhogg\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Tag added listener
 *
 * @since  1.0.0
 *
 * @param Groundhogg\Contact    $contact    The user contact
 * @param int                   $tag_id     The tag ID
 */
function gamipress_groundhogg_tag_added( $contact, $tag_id ) {

    // Make sure the contact has a user ID assigned
    if ( $contact->get_user_id() === 0 ) {
        return;
    }

    $user_id = $contact->get_user_id();

    // Trigger any tag added
    do_action( 'gamipress_groundhogg_tag_added', $tag_id, $user_id, $contact );

    // Trigger specific tag added
    do_action( 'gamipress_groundhogg_specific_tag_added', $tag_id, $user_id, $contact );

}
add_action( 'groundhogg/contact/tag_applied', 'gamipress_groundhogg_tag_added', 10, 2 );

/**
 * Tag removed listener
 *
 * @since  1.0.0
 *
 * @param Groundhogg\Contact    $contact    The user contact
 * @param int                   $tag_id     The tag ID
 */
function gamipress_groundhogg_tag_removed( $contact, $tag_id ) {

    // Make sure the contact has a user ID assigned
    if ( $contact->get_user_id() === 0 ) {
        return;
    }

    $user_id = $contact->get_user_id();

    // Trigger any tag added
    do_action( 'gamipress_groundhogg_tag_removed', $tag_id, $user_id, $contact );

    // Trigger specific tag added
    do_action( 'gamipress_groundhogg_specific_tag_removed', $tag_id, $user_id, $contact );

}
add_action( 'groundhogg/contact/tag_removed', 'gamipress_groundhogg_tag_removed', 10, 2 );
