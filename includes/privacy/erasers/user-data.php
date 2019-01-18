<?php
/**
 * User Data Erasers
 *
 * @package     GamiPress\Privacy\Erasers\User_Data
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.5.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register eraser for user meta data.
 *
 * @since 1.5.0
 *
 * @param array $erasers
 *
 * @return array
 */
function gamipress_privacy_register_user_data_erasers( $erasers ) {

    $erasers[] = array(
        'eraser_friendly_name'    => __( 'Gamification Data', 'gamipress' ),
        'callback'                  => 'gamipress_privacy_user_data_eraser',
    );

    return $erasers;

}
add_filter( 'wp_privacy_personal_data_erasers', 'gamipress_privacy_register_user_data_erasers' );

/**
 * Eraser for user meta data.
 *
 * @since 1.5.0
 *
 * @param string    $email_address
 * @param int       $page
 *
 * @return array
 */
function gamipress_privacy_user_data_eraser( $email_address, $page = 1 ) {

    // Setup vars
    $points_types = gamipress_get_points_types();
    $achievement_types = gamipress_get_achievement_types();
    $rank_types = gamipress_get_rank_types();

    $user = get_user_by( 'email', $email_address );
    $response = array(
        'items_removed'  => true,
        'items_retained' => false,
        'messages'       => array(),
        'done'           => true
    );

    if ( $user && $user->ID ) {

        /* --------------------------
         * Points types
           -------------------------- */

        // Get all user points types and their hidden meta data
        foreach( $points_types as $points_type => $points_type_data ) {

            // User balance
            if ( ! gamipress_delete_user_meta( $user->ID, "_gamipress_{$points_type}_points" ) ) {
                $response['messages'][] = sprintf( __( 'Your %s balance was unable to be removed at this time.', 'gamipress' ), $points_type_data['plural_name'] );
                $response['items_retained'] = true;
            }

            // Awarded
            if ( ! gamipress_delete_user_meta( $user->ID, "_gamipress_{$points_type}_points_awarded" ) ) {
                $response['messages'][] = sprintf( __( 'Your %s awarded was unable to be removed at this time.', 'gamipress' ), $points_type_data['plural_name'] );
                $response['items_retained'] = true;
            }

            // Deducted
            if ( ! gamipress_delete_user_meta( $user->ID, "_gamipress_{$points_type}_points_deducted" ) ) {
                $response['messages'][] = sprintf( __( 'Your %s deducted was unable to be removed at this time.', 'gamipress' ), $points_type_data['plural_name'] );
                $response['items_retained'] = true;
            }

            // Expended
            if ( ! gamipress_delete_user_meta( $user->ID, "_gamipress_{$points_type}_points_expended" ) ) {
                $response['messages'][] = sprintf( __( 'Your %s expended was unable to be removed at this time.', 'gamipress' ), $points_type_data['plural_name'] );
                $response['items_retained'] = true;
            }

            /**
             * User points balance to erase
             *
             * @param array     $response       The response to return to the eraser with keys:
             *                                  'items_removed'  => bool,
             *                                  'items_retained' => bool,
             *                                  'messages'       => array,
             *                                  'done'           => bool
             * @param int       $user_id        The user ID
             * @param string    $points_type    The points type slug
             */
            $response = apply_filters( "gamipress_privacy_erase_user_points_{$points_type}", $response, $user->ID, $points_type );

        }

        /* --------------------------
         * Rank types
           -------------------------- */

        // Get all user rank types and their hidden meta data
        foreach( $rank_types as $rank_type => $rank_type_data ) {

            if ( ! gamipress_delete_user_meta( $user->ID, "_gamipress_{$rank_type}_rank" ) ) {
                $response['messages'][] = sprintf( __( 'Your %s was unable to be removed at this time.', 'gamipress' ), $rank_type_data['singular_name'] );
                $response['items_retained'] = true;
            }

            /**
             * User ranks to erase
             *
             * @param array     $response       The response to return to the eraser with keys:
             *                                  'items_removed'  => bool,
             *                                  'items_retained' => bool,
             *                                  'messages'       => array,
             *                                  'done'           => bool
             * @param int       $user_id        The user ID
             * @param string    $rank_type      The rank type slug
             */
            $response = apply_filters( "gamipress_privacy_erase_user_rank_{$rank_type}", $response, $user->ID, $rank_type );

        }

    }

    // Return our removed items
    return $response;

}