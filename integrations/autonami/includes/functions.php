<?php
/**
 * Functions
 *
 * @package GamiPress\Autonami\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_autonami_ajax_get_posts() {

    global $wpdb;

    $results = array();

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    if( isset( $_REQUEST['post_type'] ) && in_array( 'autonami_tags', $_REQUEST['post_type'] ) ) {

        $tags = gamipress_autonami_get_tags();

        foreach ( $tags as $tag ) {

            if( ! empty( $search ) ) {
                if( strpos( strtolower( $tag['name'] ), strtolower( $search ) ) === false ) {
                    continue;
                }
            }

            // Results should meet same structure like posts
            $results[] = array(
                'ID' => $tag['id'],
                'post_title' => $tag['name'],
            );

        }

        // Return our results
        wp_send_json_success( $results );
        die;

    } else if( isset( $_REQUEST['post_type'] ) && in_array( 'autonami_lists', $_REQUEST['post_type'] ) ) {

        $lists = gamipress_autonami_get_lists();

        foreach ( $lists as $list ) {

            if( ! empty( $search ) ) {
                if( strpos( strtolower( $list['name'] ), strtolower( $search ) ) === false ) {
                    continue;
                }
            }

            // Results should meet same structure like posts
            $results[] = array(
                'ID' => $list['id'],
                'post_title' => $list['name'],
            );

        }

        // Return our results
        wp_send_json_success( $results );
        die;

    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_autonami_ajax_get_posts', 5 );

// Helper function to get the contact user ID
function gamipress_autonami_get_contact_user_id( $contact ) {

    $user_id = 0;

    $email = $contact->contact->get_email();
	$user = get_user_by( 'email', $email );
    $user_id = $user->ID;

    return $user_id;

}

/**
 * Get the tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_autonami_get_tags( ){

    $tags = array();

    $all_tags = BWFCRM_Tag::get_tags();
    
	foreach ( $all_tags as $tag ) {
		$tags[] = array(
			'id' => $tag['ID'],
			'name'  => $tag['name'],
		);
	}

	return $tags;

}

/**
 * Get the tag title
 *
 * @since 1.0.0
 *
 * @param int $tag_id
 *
 * @return string|null
 */
function gamipress_autonami_get_tag_title( $tag_id ) {

    // Empty title if no ID provided
    if( absint( $tag_id ) === 0 ) {
        return '';
    }

    $all_tags = BWFCRM_Tag::get_tags();

	foreach ( $all_tags as $tag ) {

        if ( absint( $tag_id ) === absint( $tag['ID'] ) ){
            $tag_title = $tag['name'];
        }
		
	}

    return $tag_title;

}

/**
 * Get the lists
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_autonami_get_lists( ){

    $lists = array();

    $all_lists = BWFCRM_Lists::get_lists();
    
	foreach ( $all_lists as $list ) {
		$lists[] = array(
			'id' => $list['ID'],
			'name'  => $list['name'],
		);
	}

	return $lists;

}

/**
 * Get the list title
 *
 * @since 1.0.0
 *
 * @param int $list_id
 *
 * @return string|null
 */
function gamipress_autonami_get_list_title( $list_id ) {

    // Empty title if no ID provided
    if( absint( $list_id ) === 0 ) {
        return '';
    }

    $all_lists = BWFCRM_Lists::get_lists();

	foreach ( $all_lists as $list ) {

        if ( absint( $list_id ) === absint( $list['ID'] ) ){
            $list_title = $list['name'];
        }
		
	}

    return $list_title;

}