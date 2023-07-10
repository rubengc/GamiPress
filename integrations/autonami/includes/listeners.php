<?php
/**
 * Listeners
 *
 * @package GamiPress\Autonami\Listeners
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Trigger listener
 *
 * @since 1.0.0
 *
 * @param BWFCRM_Tag $tags
 * @param BWFCRM_Contact $contact
 */
function gamipress_autonami_tag_added( $tags, $contact ) {

    $user_id = gamipress_autonami_get_contact_user_id( $contact );

    // Make sure subscriber has a user ID assigned
    if ( $user_id === 0 ) {   
        return;
    }

    if ( ! is_array( $tags ) ) {

        $tag_id = $tags->get_id();

        // Trigger any tag added
        do_action( 'gamipress_autonami_tag_added', $tag_id, $user_id, $contact );

        // Trigger specific tag added
        do_action( 'gamipress_autonami_specific_tag_added', $tag_id, $user_id, $contact );

    } else {
   
        foreach ( $tags as $tag ) {

            $tag_id = $tag->get_id();

            // Trigger any tag added
            do_action( 'gamipress_autonami_tag_added', $tag_id, $user_id, $contact );

            // Trigger specific tag added
            do_action( 'gamipress_autonami_specific_tag_added', $tag_id, $user_id, $contact );

        }
    }

}
add_action( 'bwfan_tags_added_to_contact', 'gamipress_autonami_tag_added', 10, 2 );

/**
 * Trigger listener
 *
 * @since 1.0.0
 *
 * @param BWFCRM_Tag $tags
 * @param BWFCRM_Contact $contact
 */
function gamipress_autonami_tag_removed( $tags, $contact ) {

    $user_id = gamipress_autonami_get_contact_user_id( $contact );

    // Make sure subscriber has a user ID assigned
    if ( $user_id === 0 ) {   
        return;
    }
   
    foreach ( $tags as $tag_id ) {

        // Trigger any tag added
        do_action( 'gamipress_autonami_tag_removed', $tag_id, $user_id, $contact );

        // Trigger specific tag added
        do_action( 'gamipress_autonami_specific_tag_removed', $tag_id, $user_id, $contact );

    }
    

}
add_action( 'bwfan_tags_removed_from_contact', 'gamipress_autonami_tag_removed', 10, 2 );


/**
 * Trigger listener
 *
 * @since 1.0.0
 *
 * @param BWFCRM_Lists $lists
 * @param BWFCRM_Contact $contact
 */
function gamipress_autonami_list_added( $lists, $contact ) {

    $user_id = gamipress_autonami_get_contact_user_id( $contact );

    // Make sure subscriber has a user ID assigned
    if ( $user_id === 0 ) {
        return;
    }

    if ( ! is_array( $lists ) ){

        $list_id = $lists->get_id();

        // Trigger any list added
        do_action( 'gamipress_autonami_list_added', $list_id, $user_id, $contact );

        // Trigger specific list added
        do_action( 'gamipress_autonami_specific_list_added', $list_id, $user_id, $contact );

    } else {

        foreach( $lists as $list ) {

            $list_id = $list->get_id();
    
            // Trigger any list added
            do_action( 'gamipress_autonami_list_added', $list_id, $user_id, $contact );
    
            // Trigger specific list added
            do_action( 'gamipress_autonami_specific_list_added', $list_id, $user_id, $contact );
    
        }
        
    }
    

}
add_action( 'bwfan_contact_added_to_lists', 'gamipress_autonami_list_added', 10, 2 );


/**
 * Trigger listener
 *
 * @since 1.0.0
 *
 * @param BWFCRM_Lists $lists
 * @param BWFCRM_Contact $contact
 */
function gamipress_autonami_list_removed( $lists, $contact ) {

    $user_id = gamipress_autonami_get_contact_user_id( $contact );

    // Make sure subscriber has a user ID assigned
    if ( $user_id === 0 ) {
        return;
    }

    foreach( $lists as $list_id ) {
    
        // Trigger any list removed
        do_action( 'gamipress_autonami_list_removed', $list_id, $user_id, $contact );
    
        // Trigger specific list removed
        do_action( 'gamipress_autonami_specific_list_removed', $list_id, $user_id, $contact );
    
    }   

}
add_action( 'bwfan_contact_removed_from_lists', 'gamipress_autonami_list_removed', 10, 2 );
