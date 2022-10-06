<?php
/**
 * User Earnings Exporters
 *
 * @package     GamiPress\Privacy\Exporters\User_Earnings
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.5.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register exporter for user user earnings.
 *
 * @since 1.5.0
 *
 * @param array $exporters
 *
 * @return array
 */
function gamipress_privacy_register_user_earnings_exporters( $exporters ) {

    $exporters[] = array(
        'exporter_friendly_name'    => __( 'User Earnings', 'gamipress' ),
        'callback'                  => 'gamipress_privacy_user_earnings_exporter',
    );

    return $exporters;

}
add_filter( 'wp_privacy_personal_data_exporters', 'gamipress_privacy_register_user_earnings_exporters' );

/**
 * Exporter for user user earnings.
 *
 * @since 1.5.0
 *
 * @param string    $email_address
 * @param int       $page
 *
 * @return array
 */
function gamipress_privacy_user_earnings_exporter( $email_address, $page = 1 ) {

    global $wpdb;

    // Setup query vars
    $user_earnings = GamiPress()->db->user_earnings;

    // Important: keep always SELECT *, %d is user ID, and limit/offset will added automatically
    $query = "SELECT * FROM {$user_earnings} WHERE user_id = %d";
    $count_query = str_replace( "SELECT *", "SELECT COUNT(*)", $query );

    // Setup vars
    $export_items   = array();
    $limit = 500;
    $offset = $limit * ( $page - 1 );
    $done = true;

    $user = get_user_by( 'email', $email_address );

    if ( $user && $user->ID ) {

        // Get user earnings
        $earnings = $wpdb->get_results( $wpdb->prepare(
            $query . " LIMIT {$offset}, {$limit}",
            $user->ID
        ) );

        if( is_array( $earnings ) ) {

            foreach( $earnings as $earning ) {

                // Add the user log to the exported items array
                $export_items[] = array(
                    'group_id'    => 'gamipress-user-earnings',
                    'group_label' => __( 'User Earnings', 'gamipress' ),
                    'item_id'     => "gamipress-user-earnings-{$earning->user_earning_id}",
                    'data'        => gamipress_privacy_get_user_earning_data( $earning ),
                );

            }

        }

        // Check remaining items
        $exported_items_count = $limit * $page;
        $items_count = absint( $wpdb->get_var( $wpdb->prepare( $count_query, $user->ID ) ) );

        // Process done!
        $done = (bool) ( $exported_items_count >= $items_count );

    }

    // Return our exported items
    return array(
        'data' => $export_items,
        'done' => $done
    );

}

/**
 * Function to retrieve user earning data.
 *
 * @since 1.5.0
 *
 * @param stdClass $user_earning
 *
 * @return array
 */
function gamipress_privacy_get_user_earning_data( $user_earning ) {

    // Prefix for meta data
    $prefix = '_gamipress_';

    // Setup vars
    $requirement_types = gamipress_get_requirement_types();
    $points_types = gamipress_get_points_types();
    $achievement_types = gamipress_get_achievement_types();
    $rank_types = gamipress_get_rank_types();

    // Setup CT table
    ct_setup_table( 'gamipress_user_earnings' );

    $data = array();

    // User earning title

    $data['title'] = array(
        'name' => __( 'Title', 'gamipress' ),
        'value' => $user_earning->title,
    );

    // User earning type and assigned item

    if( in_array( $user_earning->post_type, gamipress_get_requirement_types_slugs() ) ) {

        if( $user_earning->post_type === 'step' && $achievement = gamipress_get_step_achievement( $user_earning->post_id ) )  {
            // Step

            $data['type'] = array(
                'name' => __( 'Type', 'gamipress' ),
                'value' => sprintf( __( '%s Step', 'gamipress' ), $achievement_types[$achievement->post_type]['singular_name'] ),
            );

            $data['achievement'] = array(
                'name' => $achievement_types[$achievement->post_type]['singular_name'],
                'value' => gamipress_get_post_field( 'post_title', $achievement->ID ),
            );

            $data['achievement_url'] = array(
                'name' => sprintf( __( '%s URL', 'gamipress' ), $achievement_types[$achievement->post_type]['singular_name'] ),
                'value' => get_post_permalink( $achievement->ID ),
            );

        } else if( ( $user_earning->post_type === 'points-award' || $user_earning->post_type === 'points-deduct' ) && $points_type = gamipress_get_points_award_points_type( $user_earning->post_id ) )  {
            // Points Award and Deduction

            $format = ( $user_earning->post_type === 'points-award' ? __( '%s Award', 'gamipress' ) : __( '%s Deduction', 'gamipress' ) );

            $data['type'] = array(
                'name' => __( 'Type', 'gamipress' ),
                'value' => sprintf( $format, $points_types[$points_type->post_name]['plural_name'] ),
            );

        } else if( $user_earning->post_type === 'rank-requirement' && $rank = gamipress_get_rank_requirement_rank( $user_earning->post_id ) ) {
            // Rank requirement

            $data['type'] = array(
                'name' => __( 'Type', 'gamipress' ),
                'value' => sprintf( __( '%s Requirement', 'gamipress' ), $rank_types[$rank->post_type]['singular_name'] ),
            );

            $data['rank'] = array(
                'name' => $rank_types[$rank->post_type]['singular_name'],
                'value' => gamipress_get_post_field( 'post_title', $rank->ID ),
            );

            $data['rank_url'] = array(
                'name' => sprintf( __( '%s URL', 'gamipress' ), $rank_types[$rank->post_type]['singular_name'] ),
                'value' => get_post_permalink( $rank->ID ),
            );
        }

    } else if( in_array( $user_earning->post_type, gamipress_get_achievement_types_slugs() ) ) {
        // Achievement

        $data['type'] = array(
            'name' => __( 'Type', 'gamipress' ),
            'value' => $achievement_types[$user_earning->post_type]['singular_name'],
        );

        $data['achievement'] = array(
            'name' => $achievement_types[$user_earning->post_type]['singular_name'],
            'value' => gamipress_get_post_field( 'post_title', $user_earning->post_id ),
        );

        $data['achievement_url'] = array(
            'name' => sprintf( __( '%s URL', 'gamipress' ), $achievement_types[$user_earning->post_type]['singular_name'] ),
            'value' => get_post_permalink( $user_earning->post_id ),
        );

    } else if( in_array( $user_earning->post_type, gamipress_get_rank_types_slugs() ) ) {
        // Rank

        $data['type'] = array(
            'name' => __( 'Type', 'gamipress' ),
            'value' => $rank_types[$user_earning->post_type]['singular_name'],
        );

        $data['rank'] = array(
            'name' => $rank_types[$user_earning->post_type]['singular_name'],
            'value' => gamipress_get_post_field( 'post_title', $user_earning->post_id ),
        );

        $data['rank_url'] = array(
            'name' => sprintf( __( '%s URL', 'gamipress' ), $rank_types[$user_earning->post_type]['singular_name'] ),
            'value' => get_post_permalink( $user_earning->post_id ),
        );

    }

    // User earning points

    $points = absint( $user_earning->points );

    // Just add the points if points type exists
    if( $points > 0 && isset( $points_types[$user_earning->points_type] ) ) {

        $points_type = $points_types[$user_earning->points_type];

        // Check if is a points deduction or award
        if( $user_earning->post_type === 'points-deduct' ) {
            $name_format = __( '%s Deducted', 'gamipress' );
        } else {
            $name_format = __( '%s Awarded', 'gamipress' );
        }

        $data['points'] = array(
            'name' => sprintf( $name_format, $points_type['plural_name'] ),
            'value' => $points,
        );

    }

    // User earning date

    $data['date'] = array(
        'name' => __( 'Date', 'gamipress' ),
        'value' => $user_earning->date,
    );

    /**
     * User earning to export
     *
     * @param array     $data           The user earning data to export
     * @param int       $user_id        The user ID
     * @param stdClass  $log            The user earning object
     */
    return apply_filters( 'gamipress_privacy_get_user_earning_data', $data, $user_earning->user_id, $user_earning );

}