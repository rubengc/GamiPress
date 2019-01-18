<?php
/**
 * User Data Exporters
 *
 * @package     GamiPress\Privacy\Exporters\User_Data
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.5.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register exporter for user meta data.
 *
 * @since 1.5.0
 *
 * @param array $exporters
 *
 * @return array
 */
function gamipress_privacy_register_user_data_exporters( $exporters ) {

    $exporters[] = array(
        'exporter_friendly_name'    => __( 'Gamification Data', 'gamipress' ),
        'callback'                  => 'gamipress_privacy_user_data_exporter',
    );

    return $exporters;

}
add_filter( 'wp_privacy_personal_data_exporters', 'gamipress_privacy_register_user_data_exporters' );

/**
 * Exporter for user meta data.
 *
 * @since 1.5.0
 *
 * @param string    $email_address
 * @param int       $page
 *
 * @return array
 */
function gamipress_privacy_user_data_exporter( $email_address, $page = 1 ) {

    // Setup vars
    $export_items   = array();
    $points_types = gamipress_get_points_types();
    $achievement_types = gamipress_get_achievement_types();
    $rank_types = gamipress_get_rank_types();

    $user = get_user_by( 'email', $email_address );

    if ( $user && $user->ID ) {

        /* --------------------------
         * Points types
           -------------------------- */

        $user_points = array();

        // Get all user points types and their hidden meta data
        foreach( $points_types as $points_type => $points_type_data ) {

            // User balance
            $user_points[$points_type] = array(
                'name'  => $points_type_data['plural_name'],
                'value' => gamipress_get_user_points( $user->ID, $points_type ),
            );

            // Awarded
            $user_points[$points_type . '_awarded'] = array(
                'name'  => sprintf( __( '%s Awarded', 'gamipress' ), $points_type_data['plural_name'] ),
                'value' => gamipress_get_user_points_awarded( $user->ID, $points_type ),
            );

            // Deducted
            $user_points[$points_type . '_deducted'] = array(
                'name'  => sprintf( __( '%s Deducted', 'gamipress' ), $points_type_data['plural_name'] ),
                'value' => gamipress_get_user_points_deducted( $user->ID, $points_type ),
            );

            // Expended
            $user_points[$points_type . '_expended'] = array(
                'name'  => sprintf( __( '%s Expended', 'gamipress' ), $points_type_data['plural_name'] ),
                'value' => gamipress_get_user_points_expended( $user->ID, $points_type ),
            );

            /**
             * User points balance to export
             *
             * @param array     $user_points    The user points data to export
             * @param int       $user_id        The user ID
             * @param string    $points_type    The points type slug
             */
            $user_points = apply_filters( "gamipress_privacy_get_user_points_{$points_type}", $user_points, $user->ID, $points_type );

        }

        $export_items[] = array(
            'group_id'    => 'gamipress-points-types',
            'group_label' => __( 'Points Balances', 'gamipress' ),
            'item_id'     => "gamipress-points-types-{$user->ID}",
            'data'        => $user_points,
        );

        /* --------------------------
         * Rank types
           -------------------------- */

        $user_ranks = array();

        // Get all user rank types and their hidden meta data
        foreach( $rank_types as $rank_type => $rank_type_data ) {

            $user_rank = gamipress_get_user_rank( $user->ID, $rank_type );

            // User Rank
            $user_ranks[$rank_type] = array(
                'name'  => $rank_type_data['singular_name'],
                'value' => $user_rank->post_title,
            );

            /**
             * User ranks to export
             *
             * @param array     $user_rank      The user rank data to export
             * @param int       $user_id        The user ID
             * @param string    $rank_type      The rank type slug
             */
            $user_ranks = apply_filters( "gamipress_privacy_get_user_rank_{$rank_type}", $user_ranks, $user->ID, $rank_type );

        }

        $export_items[] = array(
            'group_id'    => 'gamipress-rank-types',
            'group_label' => __( 'Ranks', 'gamipress' ),
            'item_id'     => "gamipress-rank-types-{$user->ID}",
            'data'        => $user_ranks,
        );

    }

    // Return our exported items
    return array(
        'data' => $export_items,
        'done' => true
    );

}