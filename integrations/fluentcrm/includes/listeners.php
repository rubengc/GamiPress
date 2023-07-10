<?php
/**
 * Listeners
 *
 * @package GamiPress\FluentCRM\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Tag added listener
 *
 * @since  1.0.0
 *
 * @param array $tags_ids
 * @param \FluentCrm\App\Models\Subscriber $subscriber
 */
function gamipress_fluentcrm_tag_added( $tags_ids, $subscriber ) {

    $user_id = gamipress_fluentcrm_get_subscriber_user_id( $subscriber );

    // Make sure subscriber has a user ID assigned
    if ( $user_id === 0 ) {
        return;
    }

    foreach( $tags_ids as $tag_id ) {

        // Trigger any tag added
        do_action( 'gamipress_fluentcrm_tag_added', $tag_id, $user_id, $subscriber );

        // Trigger specific tag added
        do_action( 'gamipress_fluentcrm_specific_tag_added', $tag_id, $user_id, $subscriber );

    }

}
add_action( 'fluentcrm_contact_added_to_tags', 'gamipress_fluentcrm_tag_added', 10, 2 );

/**
 * Tag removed listener
 *
 * @since  1.0.0
 *
 * @param array $tags_ids
 * @param \FluentCrm\App\Models\Subscriber $subscriber
 */
function gamipress_fluentcrm_tag_removed( $tags_ids, $subscriber ) {

    $user_id = gamipress_fluentcrm_get_subscriber_user_id( $subscriber );

    // Make sure subscriber has a user ID assigned
    if ( $user_id === 0 ) {
        return;
    }

    foreach( $tags_ids as $tag_id ) {

        // Trigger any tag removed
        do_action( 'gamipress_fluentcrm_tag_removed', $tag_id, $user_id, $subscriber );

        // Trigger specific tag removed
        do_action( 'gamipress_fluentcrm_specific_tag_removed', $tag_id, $user_id, $subscriber );

    }

}
add_action( 'fluentcrm_contact_removed_from_tags', 'gamipress_fluentcrm_tag_removed', 10, 2 );

/**
 * Tag added listener
 *
 * @since  1.0.0
 *
 * @param array $lists_ids
 * @param \FluentCrm\App\Models\Subscriber $subscriber
 */
function gamipress_fluentcrm_list_added( $lists_ids, $subscriber ) {

    $user_id = gamipress_fluentcrm_get_subscriber_user_id( $subscriber );

    // Make sure subscriber has a user ID assigned
    if ( $user_id === 0 ) {
        return;
    }

    foreach( $lists_ids as $list_id ) {

        // Trigger any list added
        do_action( 'gamipress_fluentcrm_list_added', $list_id, $user_id, $subscriber );

        // Trigger specific list added
        do_action( 'gamipress_fluentcrm_specific_list_added', $list_id, $user_id, $subscriber );

    }

}
add_action( 'fluentcrm_contact_added_to_lists', 'gamipress_fluentcrm_list_added', 10, 2 );

/**
 * Tag removed listener
 *
 * @since  1.0.0
 *
 * @param array $lists_ids
 * @param \FluentCrm\App\Models\Subscriber $subscriber
 */
function gamipress_fluentcrm_list_removed( $lists_ids, $subscriber ) {

    $user_id = gamipress_fluentcrm_get_subscriber_user_id( $subscriber );

    // Make sure subscriber has a user ID assigned
    if ( $user_id === 0 ) {
        return;
    }

    foreach( $lists_ids as $list_id ) {

        // Trigger any list removed
        do_action( 'gamipress_fluentcrm_tag_removed', $list_id, $user_id, $subscriber );

        // Trigger specific list removed
        do_action( 'gamipress_fluentcrm_specific_tag_removed', $list_id, $user_id, $subscriber );

    }

}
add_action( 'fluentcrm_contact_removed_from_lists', 'gamipress_fluentcrm_list_removed', 10, 2 );
