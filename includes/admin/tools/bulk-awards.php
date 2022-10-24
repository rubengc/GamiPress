<?php
/**
 * Bulk Awards Tool
 *
 * @package     GamiPress\Admin\Tools\Bulk_Awards
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.4.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register Bulk Awards Tool meta boxes
 *
 * @since  1.4.1
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_bulk_awards_tool_meta_boxes( $meta_boxes ) {

    // Grab our points types as an array
    $points_types_options = array(
        '' => __( 'Choose a points type', 'gamipress' )
    );

    foreach( gamipress_get_points_types() as $slug => $data ) {
        $points_types_options[$slug] = $data['plural_name'];
    }

    $meta_boxes['bulk-awards'] = array(
        'title' => gamipress_dashicon( 'yes' ) . __( 'Bulk Awards', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_bulk_awards_tool_fields', array(

            // Points

            'bulk_award_points' => array(
                'name' => __( 'Points to Award', 'gamipress' ),
                'desc' => __( 'Points amount to award (the amount will be added to the current user balance).', 'gamipress' ),
                'type' => 'text_small',
                'default' => '0',
            ),
            'bulk_award_points_type' => array(
                'name' => __( 'Points Type', 'gamipress' ),
                'desc' => __( 'Points type of points amount to award.', 'gamipress' ),
                'type' => 'select',
                'options' => $points_types_options
            ),
            'bulk_award_points_register_movement' => array(
                'name' => __( 'Register on user earnings', 'gamipress' ),
                'desc' => __( 'Check this option to register this balance movement on user earnings.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
            'bulk_award_points_earnings_text' => array(
                'name' => __( 'Earning entry text', 'gamipress' ),
                'desc' => __( 'Enter the line text to be displayed on user earnings.', 'gamipress' ),
                'type' => 'text',
                'default' => __( 'Manual balance adjustment', 'gamipress' ),
            ),
            'bulk_award_points_all_users' => array(
                'name' => __( 'Award to all users', 'gamipress' ),
                'desc' => __( 'Check this option to award the points amount to all users.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
            'bulk_award_points_users' => array(
                'name' => __( 'Users to award', 'gamipress' ),
                'desc' => __( 'Choose users to award this points amount.', 'gamipress' ),
                'type' => 'advanced_select',
                'multiple' => true,
                'classes' 	        => 'gamipress-user-selector',
                'attributes' 	    => array(
                    'data-placeholder' => __( 'Choose users(s)', 'gamipress' ),
                    'data-close-on-select' => 'false',
                ),
                'options_cb' => 'gamipress_options_cb_users',
            ),
            'bulk_award_points_roles' => array(
                'name' => __( 'Roles to award', 'gamipress' ),
                'desc' => __( 'Choose roles to award this points amount.', 'gamipress' ),
                'type' => 'advanced_select',
                'multiple' => true,
                'classes' 	        => 'gamipress-selector',
                'attributes' 	    => array(
                    'data-placeholder' => __( 'Choose role(s)', 'gamipress' ),
                    'data-close-on-select' => 'false',
                ),
                'options_cb' => 'gamipress_options_cb_roles',
            ),
            'bulk_award_points_button' => array(
                'label' => __( 'Award Points', 'gamipress' ),
                'type' => 'button',
                'button' => 'primary'
            ),

            // Achievements

            'bulk_award_achievements' => array(
                'name' => __( 'Achievements to Award', 'gamipress' ),
                'desc' => __( 'Choose the achievements to award.', 'gamipress' ),
                'type' => 'advanced_select',
                'multiple' => true,
                'classes' 	        => 'gamipress-post-selector',
                'attributes' 	    => array(
                    'data-post-type' => implode( ',',  gamipress_get_achievement_types_slugs() ),
                    'data-placeholder' => __( 'Choose achievement(s)', 'gamipress' ),
                    'data-close-on-select' => 'false',
                ),
                'options_cb' => 'gamipress_options_cb_posts',
            ),
            'bulk_award_achievements_all_users' => array(
                'name' => __( 'Award to all users', 'gamipress' ),
                'desc' => __( 'Check this option to award the achievements to all users.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
            'bulk_award_achievements_users' => array(
                'name' => __( 'Users to award', 'gamipress' ),
                'desc' => __( 'Choose users to award this achievements.', 'gamipress' ),
                'type' => 'advanced_select',
                'multiple' => true,
                'classes' 	        => 'gamipress-user-selector',
                'attributes' 	    => array(
                    'data-placeholder' => __( 'Choose users(s)', 'gamipress' ),
                    'data-close-on-select' => 'false',
                ),
                'options_cb' => 'gamipress_options_cb_users',
            ),
            'bulk_award_achievements_roles' => array(
                'name' => __( 'Roles to award', 'gamipress' ),
                'desc' => __( 'Choose roles to award this achievements.', 'gamipress' ),
                'type' => 'advanced_select',
                'multiple' => true,
                'classes' 	        => 'gamipress-selector',
                'attributes' 	    => array(
                    'data-placeholder' => __( 'Choose role(s)', 'gamipress' ),
                    'data-close-on-select' => 'false',
                ),
                'options_cb' => 'gamipress_options_cb_roles',
            ),
            'bulk_award_achievements_button' => array(
                'label' => __( 'Award Achievements', 'gamipress' ),
                'type' => 'button',
                'button' => 'primary'
            ),

            // Ranks

            'bulk_award_rank' => array(
                'name' => __( 'Rank to Award', 'gamipress' ),
                'desc' => __( 'Choose the rank to award.', 'gamipress' )
                . '<br>' . __( '<strong>Important!</strong> Users on higher rank will be downgrade to this rank.', 'gamipress' ),
                'type' => 'advanced_select',
                'classes' 	        => 'gamipress-post-selector',
                'attributes' 	    => array(
                    'data-post-type' => implode( ',',  gamipress_get_rank_types_slugs() ),
                    'data-placeholder' => __( 'Choose rank', 'gamipress' ),
                ),
                'options_cb' => 'gamipress_options_cb_posts',
            ),
            'bulk_award_rank_all_users' => array(
                'name' => __( 'Award to all users', 'gamipress' ),
                'desc' => __( 'Check this option to award the rank to all users.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
            'bulk_award_rank_users' => array(
                'name' => __( 'Users to award', 'gamipress' ),
                'desc' => __( 'Choose users to award this rank.', 'gamipress' ),
                'type' => 'advanced_select',
                'multiple' => true,
                'classes' 	        => 'gamipress-user-selector',
                'attributes' 	    => array(
                    'data-placeholder' => __( 'Choose users(s)', 'gamipress' ),
                    'data-close-on-select' => 'false',
                ),
                'options_cb' => 'gamipress_options_cb_users',
            ),
            'bulk_award_rank_roles' => array(
                'name' => __( 'Roles to award', 'gamipress' ),
                'desc' => __( 'Choose roles to award this rank.', 'gamipress' ),
                'type' => 'advanced_select',
                'multiple' => true,
                'classes' 	        => 'gamipress-selector',
                'attributes' 	    => array(
                    'data-placeholder' => __( 'Choose role(s)', 'gamipress' ),
                    'data-close-on-select' => 'false',
                ),
                'options_cb' => 'gamipress_options_cb_roles',
            ),
            'bulk_award_rank_button' => array(
                'label' => __( 'Award Rank', 'gamipress' ),
                'type' => 'button',
                'button' => 'primary'
            ),
        ) ),
        'vertical_tabs' => true,
        'tabs' => apply_filters( 'gamipress_bulk_awards_tool_tabs', array(
            'bulk_award_points' => array(
                'icon' => 'dashicons-star-filled',
                'title' => __( 'Points', 'gamipress' ),
                'fields' => array(
                    'bulk_award_points',
                    'bulk_award_points_type',
                    'bulk_award_points_register_movement',
                    'bulk_award_points_earnings_text',
                    'bulk_award_points_all_users',
                    'bulk_award_points_users',
                    'bulk_award_points_roles',
                    'bulk_award_points_button',
                ),
            ),
            'bulk_award_achievements' => array(
                'icon' => 'dashicons-awards',
                'title' => __( 'Achievements', 'gamipress' ),
                'fields' => array(
                    'bulk_award_achievements',
                    'bulk_award_achievements_all_users',
                    'bulk_award_achievements_users',
                    'bulk_award_achievements_roles',
                    'bulk_award_achievements_button',
                ),
            ),
            'bulk_award_rank' => array(
                'icon' => 'dashicons-rank',
                'title' => __( 'Ranks', 'gamipress' ),
                'fields' => array(
                    'bulk_award_rank',
                    'bulk_award_rank_all_users',
                    'bulk_award_rank_users',
                    'bulk_award_rank_roles',
                    'bulk_award_rank_button',
                ),
            )
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_tools_general_meta_boxes', 'gamipress_bulk_awards_tool_meta_boxes' );

/**
 * AJAX handler for the recount activity tool
 *
 * @since 1.4.1
 */
function gamipress_ajax_bulk_awards_tool() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    global $wpdb;

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    // Check parameters received
    if( ! isset( $_POST['bulk_award'] ) || empty( $_POST['bulk_award'] ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    $bulk_award = sanitize_text_field( $_POST['bulk_award'] );
    $loop = ( ! isset( $_POST['loop'] ) ? 0 : absint( $_POST['loop'] ) );
    $limit = 100;
    $offset = $limit * $loop;
    $run_again = false;

    ignore_user_abort( true );

    if ( ! gamipress_is_function_disabled( 'set_time_limit' ) )
        set_time_limit( 0 );

    // Initialize vars
    $points_types = gamipress_get_points_types();
    $rank_types = gamipress_get_rank_types();
    $to_all_users = false;
    $specific_users = array();
    $specific_roles = array();
    $join = array();
    $where = array();

    if( $bulk_award === 'points' ) {
        // Check points parameters

        $points_to_award = absint( $_POST['bulk_award_points'] );

        if( $points_to_award === 0 ) {
            wp_send_json_error( __( 'Choose a valid points amount to award.', 'gamipress' ) );
        }

        $points_type_to_award = sanitize_text_field( $_POST['bulk_award_points_type'] );

        if( $points_type_to_award === '' || ! isset( $points_types[$points_type_to_award] ) ) {
            wp_send_json_error( __( 'Choose a valid points type.', 'gamipress' ) );
        }

        $to_all_users = isset( $_POST['bulk_award_points_all_users'] );

        if( ! $to_all_users ) {
            $specific_users = isset( $_POST['bulk_award_points_users'] ) ? $_POST['bulk_award_points_users'] : array();
            $specific_roles = isset( $_POST['bulk_award_points_roles'] ) ? $_POST['bulk_award_points_roles'] : array();
        }

    } else if( $bulk_award === 'achievements' ) {
        // Check achievements parameters

        $achievements = isset( $_POST['bulk_award_achievements'] ) ? $_POST['bulk_award_achievements'] : array();

        if( empty( $achievements ) ) {
            wp_send_json_error( __( 'Choose at least one achievement to be awarded.', 'gamipress' ) );
        }

        $to_all_users = isset( $_POST['bulk_award_achievements_all_users'] );

        if( ! $to_all_users ) {
            $specific_users = isset( $_POST['bulk_award_achievements_users'] ) ? $_POST['bulk_award_achievements_users'] : array();
            $specific_roles = isset( $_POST['bulk_award_achievements_roles'] ) ? $_POST['bulk_award_achievements_roles'] : array();
        }

    } else if( $bulk_award === 'rank' ) {
        // Check rank parameters

        $rank_id = absint( $_POST['bulk_award_rank'] );

        $rank = get_post( $rank_id );

        if( ! $rank || ! isset( $rank_types[$rank->post_type] ) ) {
            wp_send_json_error( __( 'Choose a valid rank to be awarded.', 'gamipress' ) );
        }

        $to_all_users = isset( $_POST['bulk_award_rank_all_users'] );

        if( ! $to_all_users ) {
            $specific_users = isset( $_POST['bulk_award_rank_users'] ) ? $_POST['bulk_award_rank_users'] : array();
            $specific_roles = isset( $_POST['bulk_award_rank_roles'] ) ? $_POST['bulk_award_rank_roles'] : array();
        }

    }

    // Ensure all specific users are correct
    foreach( $specific_users as $i => $specific_user ) {
        if( absint( $specific_user ) === 0 )
            unset( $specific_users[$i] );
    }

    // Ensure all specific roles are correct
    $editable_roles = get_editable_roles();

    foreach( $specific_roles as $i => $specific_role ) {
        if( ! isset( $editable_roles[$specific_role] ) )
            unset( $specific_roles[$i] );
    }

    // Check users parameters
    if( ! $to_all_users && empty( $specific_users ) && empty( $specific_roles ) )
        wp_send_json_error( __( 'Choose at least one user to award.', 'gamipress' ) );

    if( $to_all_users ) {

        // Get stored users
        $users = gamipress_get_users( array(
            'offset' => $offset,
            'limit' => $limit,
        ) );

        // Return a success message if finished, else run again
        if( empty( $users ) && $loop !== 0 ) {
            wp_send_json_success( __( 'Bulk award process has been done successfully.', 'gamipress' ) );
        } else {
            $run_again = true;
        }

    } else {
        // Get specific stored users

        // Setup specific users where
        if( ! empty( $specific_users ) && count( $specific_users ) > 0 ) {

            // Sanitize user IDs
            foreach ( $specific_users as $i => $user_id ) {
                $specific_users[$i] = absint( $user_id );
            }

            $where[] = "u.ID IN( " . implode( ', ', $specific_users ) . " )";
        }

        // Setup specific users join
        if( ! empty( $specific_roles ) && count( $specific_roles ) > 0 ) {
            // Setup join to the users meta table
            $join[] = "LEFT JOIN {$wpdb->usermeta} AS umcap ON ( umcap.user_id = u.ID AND umcap.meta_key = '" . $wpdb->get_blog_prefix() . "capabilities' )";

            foreach( $specific_roles as $role ) {
                $role = esc_sql( $role );

                $where[] = "umcap.meta_value LIKE '%\\\"{$role}\\\"%'";
            }

        }

        // Setup each query part
        $where = ( ! empty( $where ) ? "WHERE (" . implode( ') OR (', $where ) . ")" : '' );
        $join = ( ! empty( $join ) ? implode( ' ', $join ) : '' );

        $sql = "SELECT u.ID 
                FROM {$wpdb->users} AS u
                {$join}
                {$where}
                ORDER BY u.ID ASC LIMIT {$offset}, {$limit}";

        $users = $wpdb->get_results( $sql );

        // Return a success message if finished, else run again
        if( empty( $users ) && $loop !== 0 ) {
            wp_send_json_success( __( 'Bulk award process has been done successfully.', 'gamipress' ) );
        } else {
            $run_again = true;
        }
    }

    if( empty( $users ) )
        wp_send_json_error( __( 'Could not find users to award.', 'gamipress' ) );

    // Let's to bulk award
    foreach( $users as $user ) {

        if( $bulk_award === 'points' ) {

            // When an admin awards points to user is required to set the total points balance
            $user_points = gamipress_get_user_points( $user->ID, $points_type_to_award );

            // Award points
           gamipress_award_points_to_user( $user->ID, $user_points + $points_to_award, $points_type_to_award, array( 'admin_id' => get_current_user_id() ) );

            $register_movement = isset( $_POST['bulk_award_points_register_movement'] ) ? true : false;
            $earnings_text = isset( $_POST['bulk_award_points_earnings_text'] ) ? sanitize_text_field( $_POST['bulk_award_points_earnings_text'] ) : '';

            if( $register_movement ) {

                // Insert the custom user earning for the manual balance adjustment
                gamipress_insert_user_earning( $user->ID, array(
                    'title'	        => $earnings_text,
                    'user_id'	    => $user->ID,
                    'post_id'	    => gamipress_get_points_type_id( $points_type_to_award ),
                    'post_type' 	=> 'points-type',
                    'points'	    => $points_to_award,
                    'points_type'	=> $points_type_to_award,
                    'date'	        => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
                ) );

            }

        } else if( $bulk_award === 'achievements' ) {

            // Award achievements
            foreach( $achievements as $achievement ) {
                gamipress_award_achievement_to_user( absint( $achievement ), $user->ID, get_current_user_id() );
            }

        } else if( $bulk_award === 'rank' ) {

            // Award rank
            gamipress_update_user_rank( $user->ID, $rank_id, get_current_user_id() );

        }

    }

    if( $run_again ) {

        $awarded_users = $limit * ( $loop + 1 );

        if( $to_all_users ) {
            // Get the users count
            $users_count = gamipress_get_users_count();
        } else {
            $users_count = absint( $wpdb->get_var(
                "SELECT COUNT(*) 
                FROM {$wpdb->users} AS u 
                {$join}
                {$where}
                ORDER BY u.ID ASC"
            ) );
        }

        $remaining_users = $users_count - $awarded_users;

        // Return a run again message (just when awarding to all users)
        wp_send_json_success( array(
            'run_again' => $run_again,
            'message' => sprintf( __( '%d remaining users', 'gamipress' ), ( $remaining_users > 0 ? $remaining_users : 0 ) ),
        ) );

    } else {
        // Return a success message
        wp_send_json_success( __( 'Bulk award process has been done successfully.', 'gamipress' ) );
    }


}
add_action( 'wp_ajax_gamipress_bulk_awards_tool', 'gamipress_ajax_bulk_awards_tool' );