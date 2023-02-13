<?php
/**
 * Admin Users
 *
 * @package     GamiPress\Admin\Users
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Display achievements for a user on their profile screen
 *
 * @since  1.0.0
 * @param  object $user The current user's $user object
 * @return void
 */
function gamipress_user_profile_data( $user = null ) {
    ?>

    <hr>

    <?php // Verify user meets minimum role to manage earned achievements
    if ( current_user_can( gamipress_get_manager_capability() ) ) : ?>

        <h2><?php echo gamipress_dashicon( 'gamipress' ); ?> <?php _e( 'GamiPress', 'gamipress' ); ?></h2>

    <?php endif; ?>

    <?php // Output markup to user rank
    gamipress_profile_user_rank( $user );

    // Output markup to list user points
    gamipress_profile_user_points( $user );

    // Output markup to list user earnings
    gamipress_profile_user_earnings( $user );

    // Output markup for awarding achievement for user
    gamipress_profile_award_achievement( $user );

    // Output markup for awarding requirement for user
    gamipress_profile_award_requirement( $user );

}
add_action( 'show_user_profile', 'gamipress_user_profile_data' );
add_action( 'edit_user_profile', 'gamipress_user_profile_data' );

/**
 * Update user rank ajax handler
 *
 * @since 1.5.9
 */
function gamipress_ajax_profile_update_user_rank() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    $rank_id = absint( $_POST['rank_id'] );
    $user_id = absint( $_POST['user_id'] );

    // Check if user has permissions
    if ( ! current_user_can( 'edit_user', $user_id ) )
        wp_send_json_error( __( 'You can perform this action.', 'gamipress' ) );

    // Check if valid user ID
    if( $user_id === 0 )
        wp_send_json_error( __( 'Invalid user ID.', 'gamipress' ) );

    $rank = gamipress_get_post( $rank_id );

    // Check if is a valid rank
    if ( ! $rank )
        wp_send_json_error( __( 'Invalid post ID.', 'gamipress' ) );

    if ( ! gamipress_is_rank( $rank ) )
        wp_send_json_error( __( 'Invalid rank ID.', 'gamipress' ) );

    // Update the user rank
    gamipress_update_user_rank( $user_id, absint( $rank_id ), get_current_user_id() );

    wp_send_json_success( array(
        'message' => __( 'Rank updated successfully.', 'gamipress' ),
        'rank' => array(
            'ID' => $rank->ID,
            'post_title' => $rank->post_title,
            'thumbnail' => gamipress_get_rank_post_thumbnail( $rank->ID, array( 32, 32 ) ),
        )
    ) );

}
add_action( 'wp_ajax_gamipress_profile_update_user_rank', 'gamipress_ajax_profile_update_user_rank' );

/**
 * Update user points ajax handler
 *
 * @since   1.5.9
 * @updated 1.6.0 Now also return the current user ranks in order to see any rank change through the points earned
 */
function gamipress_ajax_profile_update_user_points() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    $points             = absint( $_POST['points'] );
    $register_movement  = ( bool ) $_POST['register_movement'];
    $earnings_text      = sanitize_text_field( $_POST['earnings_text'] );
    $points_type        = sanitize_text_field( $_POST['points_type'] );
    $user_id            = absint( $_POST['user_id'] );

    // Check if user can edit other users
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        wp_send_json_error( __( 'You can perform this action.', 'gamipress' ) );
    }

    // Check if user can manage GamiPress
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You can perform this action.', 'gamipress' ) );
    }

    // Check if valid user ID
    if( $user_id === 0 ) {
        wp_send_json_error( __( 'Invalid user ID.', 'gamipress' ) );
    }

    // Check if valid amount
    if( ! is_numeric( $points ) ) {
        wp_send_json_error( __( 'Invalid points amount.', 'gamipress' ) );
    }

    // Check if is valid points type
    if( $points_type !== '' && ! in_array( $points_type, gamipress_get_points_types_slugs() ) ) {
        wp_send_json_error( __( 'Invalid points type.', 'gamipress' ) );
    }

    // Grab the user's current points
    $current_points = gamipress_get_user_points( $user_id, $points_type );

    // Update the user points
    gamipress_update_user_points( $user_id, $points, get_current_user_id(), null, $points_type );

    if( $register_movement ) {

        // Insert the custom user earning for the manual balance adjustment
        gamipress_insert_user_earning( $user_id, array(
            'title'	        => $earnings_text,
            'user_id'	    => $user_id,
            'post_id'	    => gamipress_get_points_type_id( $points_type ),
            'post_type' 	=> 'points-type',
            'points'	    => $points - $current_points,
            'points_type'	=> $points_type,
            'date'	        => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
        ) );

    }

    // After update the user points balance, is possible that user unlocks a rank
    // For that, we need to return the current user ranks again and check for differences
    $ranks = array();

    foreach( gamipress_get_rank_types_slugs() as $rank_type ) {

        // Get the rank object to build the same response as gamipress_ajax_profile_update_user_rank() function
        $rank = gamipress_get_user_rank( $user_id, $rank_type );

        $ranks[] = array(
            'ID' => $rank->ID,
            'post_title' => $rank->post_title,
            'post_type' => $rank->post_type, // Included to meet the rank type
            'thumbnail' => gamipress_get_rank_post_thumbnail( $rank->ID, array( 32, 32 ) ),
        );
    }

    wp_send_json_success( array(
        'message' => __( 'Points updated successfully.', 'gamipress' ),
        'points' => gamipress_format_amount( $points, $points_type ),
        'ranks' => $ranks
    ) );

}
add_action( 'wp_ajax_gamipress_profile_update_user_points', 'gamipress_ajax_profile_update_user_points' );

/**
 * Generate markup to show user rank
 *
 * @since  1.0.0
 *
 * @param  object $user         The current user's $user object
 *
 * @return string               concatenated markup
 */
function gamipress_profile_user_rank( $user = null ) {

    $rank_types = gamipress_get_rank_types();

    $can_manage = current_user_can( gamipress_get_manager_capability() );

    // Return if not rank types and user is not a manager
    if( empty( $rank_types ) && ! $can_manage ) {
        return;
    }
    ?>

    <table class="form-table">
        <tr>
            <th>
                <label><?php echo $can_manage ? __( 'Ranks', 'gamipress' ) : __( 'Your Ranks', 'gamipress' ); ?></label>
            </th>
            <td>

                <?php if( empty( $rank_types ) && $can_manage ) : ?>

                    <?php // No rank types configured yet ?>
                    <span class="description">
                        <?php echo sprintf( __( 'No rank types configured, visit %s to configure some rank types.', 'gamipress' ), '<a href="' . admin_url( 'edit.php?post_type=rank-type' ) . '">' . __( 'this page', 'gamipress' ) . '</a>' ); ?>
                    </span>

                <?php else : ?>

                    <div class="profile-ranks gamipress-profile-cards">

                        <?php // Show the information of each user rank ?>
                        <?php foreach( $rank_types as $rank_type => $data ) : ?>

                            <div class="profile-rank-wrapper gamipress-profile-card-wrapper">
                                <div class="profile-rank profile-rank-<?php echo $rank_type; ?> gamipress-profile-card">

                                    <span class="profile-rank-type-name"><?php echo $data['singular_name']; ?></span>

                                    <?php // Get and display the current user rank
                                    $user_rank = gamipress_get_user_rank( $user->ID, $rank_type );

                                    if( $user_rank ) : ?>

                                        <div class="profile-rank-thumbnail"><?php echo gamipress_get_rank_post_thumbnail( $user_rank->ID, array( 32, 32 ) ); ?></div>

                                        <span class="profile-rank-title"><?php echo $user_rank->post_title; ?></span>

                                    <?php endif; ?>


                                    <?php if( $can_manage ) :
                                        // Show an editable form of ranks

                                        // Get all published ranks of this type
                                        $ranks = gamipress_get_ranks( array(
                                            'post_type' => $rank_type,
                                            'posts_per_page' => -1
                                        ) ); ?>

                                        <?php if( empty( $ranks ) ) : ?>

                                        <?php // No ranks of this type configured yet ?>
                                        <span class="description">
                                                <?php echo sprintf( __( 'No %1$s configured, visit %2$s to configure some %1$s.', 'gamipress' ),
                                                    strtolower( $data['plural_name'] ),
                                                    '<a href="' . admin_url( 'edit.php?post_type=' . $rank_type ) . '">' . __( 'this page', 'gamipress' ) . '</a>'
                                                ); ?>
                                            </span>

                                    <?php else : ?>

                                        <a href="#" class="profile-rank-toggle"><?php echo __( 'Edit', 'gamipress' ); ?></a>

                                        <div class="profile-rank-form-wrapper">

                                            <select name="user_<?php echo $rank_type; ?>_rank" id="user_<?php echo $rank_type; ?>_rank" style="min-width: 15em;">
                                                <?php foreach( $ranks as $rank ) : ?>
                                                    <option value="<?php echo $rank->ID; ?>" <?php selected( $user_rank->ID, $rank->ID ); ?>><?php echo $rank->post_title; ?></option>
                                                <?php endforeach; ?>
                                            </select>

                                            <span class="description"><?php echo sprintf( __( '%s listed are ordered by priority.', 'gamipress' ), $data['plural_name'] ); ?></span>

                                            <div class="profile-rank-form-buttons">
                                                <a href="#" class="button button-primary profile-rank-save"><?php echo __( 'Save', 'gamipress' ); ?></a>
                                                <a href="#" class="button profile-rank-cancel"><?php echo __( 'Cancel', 'gamipress' ); ?></a>
                                                <span class="spinner"></span>
                                            </div>

                                        </div>

                                    <?php endif; ?>

                                    <?php endif; ?>

                                </div>
                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </td>
        </tr>
    </table>

    <hr>
    <?php
}

/**
 * Generate markup to list user earned points
 *
 * @since  1.0.0
 *
 * @param  object $user         The current user's $user object
 *
 * @return string               concatenated markup
 */
function gamipress_profile_user_points( $user = null ) {

    $points_types = gamipress_get_points_types();

    $can_manage = current_user_can( gamipress_get_manager_capability() );

    // Return if not points types and user is not a manager
    if( empty( $points_types ) && ! $can_manage ) {
        return;
    }
    ?>

    <table class="form-table">
        <tr>
            <th>
                <label><?php echo $can_manage ? __( 'Points Balances', 'gamipress' ) : __( 'Your Balances', 'gamipress' ); ?></label>
            </th>
            <td>

                <?php if( empty( $points_types ) && $can_manage ) : ?>

                    <span class="description">
                        <?php echo sprintf( __( 'No points types configured, visit %s to configure some points types.', 'gamipress' ), '<a href="' . admin_url( 'edit.php?post_type=points-type' ) . '">' . __( 'this page', 'gamipress' ) . '</a>' ); ?>
                    </span>

                <?php else : ?>

                    <div class="profile-points gamipress-profile-cards">

                        <?php // Filter available to re-enable the default points
                        if( apply_filters( 'gamipress_user_points_backward_compatibility', false ) ) :
                            $user_points = gamipress_get_user_points( $user->ID ); ?>

                            <div class="profile-points-wrapper gamipress-profile-card-wrapper">

                                <div class="profile-points profile-points-default gamipress-profile-card">

                                    <span class="profile-points-type-name"><?php _e( 'Points', 'gamipress' ); ?></span>

                                    <div class="profile-points-thumbnail"></div>

                                    <span class="profile-points-amount"><?php echo $user_points; ?></span>

                                    <?php if( $can_manage ) :
                                        // Show an editable form of points ?>

                                        <a href="#" class="profile-points-toggle"><?php echo __( 'Edit', 'gamipress' ); ?></a>

                                        <div class="profile-points-form-wrapper">

                                            <input type="number" name="user_points" id="user_points" value="<?php echo $user_points; ?>" class="regular-text" data-points-type="" />

                                            <span class="description"><?php echo __( 'Enter a new total will automatically log the change and difference between totals.', 'gamipress' ); ?></span>

                                            <div class="profile-points-form-buttons">
                                                <a href="#" class="button button-primary profile-points-save"><?php echo __( 'Save', 'gamipress' ); ?></a>
                                                <a href="#" class="button profile-points-cancel"><?php echo __( 'Cancel', 'gamipress' ); ?></a>
                                                <span class="spinner"></span>
                                            </div>

                                        </div>

                                    <?php endif; ?>

                                </div>

                            </div>

                        <?php endif; ?>

                        <?php foreach( $points_types as $points_type => $data ) :
                            $user_points = gamipress_get_user_points( $user->ID, $points_type ); ?>

                            <div class="profile-points-wrapper gamipress-profile-card-wrapper">

                                <div class="profile-points profile-points-<?php echo $points_type; ?> gamipress-profile-card">

                                    <span class="profile-points-type-name"><?php echo $data['plural_name']; ?></span>

                                    <div class="profile-points-thumbnail"><?php echo gamipress_get_points_type_thumbnail( $points_type, array( 32, 32 ) ); ?></div>

                                    <span class="profile-points-amount"><?php echo gamipress_format_amount( $user_points, $points_type ); ?></span>

                                    <?php if( $can_manage ) :
                                        // Show an editable form of points ?>

                                        <a href="#" class="profile-points-toggle"><?php echo __( 'Edit', 'gamipress' ); ?></a>

                                        <div class="profile-points-form-wrapper">

                                            <div class="profile-points-new-balance-input">
                                                <label for="user_<?php echo $points_type; ?>_points"><?php echo __( 'New balance:', 'gamipress' ); ?></label>
                                                <input type="number" name="user_<?php echo $points_type; ?>_points" id="user_<?php echo $points_type; ?>_points" value="<?php echo $user_points; ?>" class="regular-text" data-points-type="<?php echo $points_type; ?>" />
                                                <span class="description"><?php echo __( 'Enter a new total will automatically log the change and difference between totals.', 'gamipress' ); ?></span>
                                            </div>

                                            <label for="user_<?php echo $points_type; ?>_register_points_movement" class="profile-points-register-movement-input-label"><?php echo __( 'Register on user earnings:', 'gamipress' ); ?></label>
                                            <div class="gamipress-switch gamipress-switch-small profile-points-register-movement-input">
                                                <input type="checkbox" name="user_<?php echo $points_type; ?>_register_points_movement" id="user_<?php echo $points_type; ?>_register_points_movement">
                                                <label for="user_<?php echo $points_type; ?>_register_points_movement"><?php echo __( 'Check this option to register this balance movement on user earnings.', 'gamipress' ); ?></label>
                                            </div>

                                            <div class="profile-points-earning-text-input" style="display: none;">
                                                <label for="user_<?php echo $points_type; ?>_points_earning_text"><?php echo __( 'Earning entry text:', 'gamipress' ); ?></label>
                                                <input type="text" name="user_<?php echo $points_type; ?>_points_earning_text" id="user_<?php echo $points_type; ?>_points_earning_text" value="<?php echo __( 'Manual balance adjustment', 'gamipress' ); ?>" class="regular-text" />
                                                <span class="description"><?php echo __( 'Enter the line text to be displayed on user earnings.', 'gamipress' ); ?></span>
                                            </div>

                                            <div class="profile-points-form-buttons">
                                                <a href="#" class="button button-primary profile-points-save"><?php echo __( 'Save', 'gamipress' ); ?></a>
                                                <a href="#" class="button profile-points-cancel"><?php echo __( 'Cancel', 'gamipress' ); ?></a>
                                                <span class="spinner"></span>
                                            </div>

                                        </div>

                                    <?php endif; ?>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>
            </td>
        </tr>
    </table>

    <hr>
    <?php
}

/**
 * Generate markup to list user earnings
 *
 * @since  1.0.0
 *
 * @param  object $user         The current user's $user object
 *
 * @return string               concatenated markup
 */
function gamipress_profile_user_earnings( $user = null ) {

    /**
     * Filter to allow set the number of user earnings to show on user profile
     *
     * @since 1.8.3
     *
     * @param int $items_per_page
     *
     * @return int
     */
    $items_per_page = apply_filters( 'gamipress_user_profile_earnings_items_per_page', 10 );
    ?>

    <h2><?php echo current_user_can( gamipress_get_manager_capability() ) ? __( 'User Earnings', 'gamipress' ) : __( 'Your Achievements', 'gamipress' ); ?></h2>

    <?php ct_render_ajax_list_table( 'gamipress_user_earnings',
        array(
            'user_id' => absint( $user->ID ),
            'items_per_page' => $items_per_page,
        ),
        array(
            'views' => false,
            'search_box' => false
        )
    ); ?>

    <hr>

    <?php
}

/**
 * Generate markup for awarding an achievement to a user
 *
 * @since  1.0.0
 *
 * @param  object $user         The current user's $user object
 *
 * @return string               concatenated markup
 */
function gamipress_profile_award_achievement( $user = null ) {

    // Return if user is not a manager
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    // Grab our types
    $achievement_types = gamipress_get_achievement_types();
    ?>

    <h2><?php _e( 'Award Achievement', 'gamipress' ); ?></h2>

    <table class="form-table">

        <tr>
            <th><label for="gamipress-award-achievement-type-select"><?php _e( 'Select an achievement type to award:', 'gamipress' ); ?></label></th>
            <td>
                <select id="gamipress-award-achievement-type-select">
                    <option value=""><?php _e( 'Choose an achievement type', 'gamipress' ); ?></option>
                    <?php foreach ( $achievement_types as $slug => $data ) :
                        echo '<option value="'. $slug .'">' . ucwords( $data['singular_name'] ) .'</option>';
                    endforeach; ?>
                </select>
            </td>
        </tr>

    </table>

    <div id="gamipress-awards-options">
        <?php foreach ( $achievement_types as $slug => $data ) : ?>
            <div id="<?php echo esc_attr( $slug ); ?>" data-loaded="false" style="display: none;">
                <span class="spinner is-active"></span>
            </div>
        <?php endforeach; ?>

    </div><!-- #gamipress-awards-options -->

    <hr>

    <?php
}

/**
 * Ajax handler to load the award table of the given post type
 *
 * @since 1.8.7
 */
function gamipress_ajax_profile_load_award_achievement_award() {

    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Return if user is not a manager
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You can perform this action.', 'gamipress' ) );
    }

    $post_type  = sanitize_text_field( $_POST['post_type'] );
    $user_id    = absint( $_POST['user_id'] );

    // Check if user has permissions
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        wp_send_json_error( __( 'You can perform this action.', 'gamipress' ) );
    }

    // Grab our types
    $achievement_types = gamipress_get_achievement_types();

    $achievements = gamipress_get_user_achievements( array(
        'user_id' => absint( $user_id ),
        'achievement_type' => $post_type
    ) );

    $achievement_ids = array_map( function( $achievement ) {
        return $achievement->ID;
    }, $achievements );

    // On network wide active installs, we need to switch to main blog mostly for posts permalinks and thumbnails
    $blog_id = gamipress_switch_to_main_site_if_network_wide_active();

    ob_start(); ?>

    <?php foreach ( $achievement_types as $slug => $data ) :

        // Skip if not is the points type to render
        if( $post_type !== $slug ) {
            continue;
        } ?>
        <div id="<?php echo esc_attr( $slug ); ?>">
            <table id="<?php echo esc_attr( $slug ); ?>-table" class="wp-list-table widefat fixed striped gamipress-table">

                <thead>
                <tr>
                    <th><?php echo ucwords( $data['singular_name'] ); ?></th>
                    <th><?php _e( 'Actions', 'gamipress' ); ?></th>
                </tr>
                </thead>

                <tbody>
                <?php
                // Load achievement type entries
                $the_query = new WP_Query( array(
                    'post_type'      	=> $slug,
                    'posts_per_page' 	=> -1,
                    'post_status'    	=> 'publish',
                    'suppress_filters' 	=> false
                ) );

                if ( $the_query->have_posts() ) : ?>

                    <?php while ( $the_query->have_posts() ) : $the_query->the_post();

                        // Setup our award URL
                        $award_url = add_query_arg( array(
                            'action'         => 'award',
                            'achievement_id' => absint( get_the_ID() ),
                            'user_id'        => absint( $user_id )
                        ) );
                        ?>
                        <tr>
                            <td>
                                <?php // Thumbnail ?>
                                <?php echo gamipress_get_achievement_post_thumbnail( get_the_ID(), array( 32, 32 ) ); ?>

                                <?php // Title ?>
                                <strong><?php echo '<a href="' . get_edit_post_link( get_the_ID() ) . '">' . gamipress_get_post_field( 'post_title', get_the_ID() ) . '</a>'; ?></strong>
                            </td>
                            <td>
                                <a class="gamipress-award-achievement" href="<?php echo esc_url( wp_nonce_url( $award_url, 'gamipress_award_achievement' ) ); ?>"><?php printf( __( 'Award %s', 'gamipress' ), ucwords( $data['singular_name'] ) ); ?></a>
                                <?php if ( in_array( get_the_ID(), (array) $achievement_ids ) ) :
                                    // Setup our revoke URL
                                    $revoke_url = add_query_arg( array(
                                        'action'         => 'revoke',
                                        'user_id'        => absint( $user_id ),
                                        'achievement_id' => absint( get_the_ID() ),
                                    ) );
                                    ?>
                                    | <span class="delete"><a class="error gamipress-revoke-achievement" href="<?php echo esc_url( wp_nonce_url( $revoke_url, 'gamipress_revoke_achievement' ) ); ?>"><?php _e( 'Revoke Award', 'gamipress' ); ?></a></span>
                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endwhile; ?>

                <?php else : ?>
                    <tr>
                        <td colspan="3"><?php printf( __( 'No %s found.', 'gamipress' ), $data['plural_name'] ); ?></td>
                    </tr>
                <?php endif; wp_reset_postdata(); ?>

                </tbody>

            </table><!-- #<?php echo esc_attr( $slug ); ?>-table -->
        </div><!-- #<?php echo esc_attr( $slug ); ?> -->

    <?php endforeach; ?>

    <?php $content = ob_get_clean();

    // If switched to blog, return back to que current blog
    if( $blog_id !== get_current_blog_id() && is_multisite() ) {
        restore_current_blog();
    }

    if( empty( $content ) ) {
        wp_send_json_error( __( 'Couldn\'t load this content.', 'gamipress' ) );
    }

    // Return the table rendered
    wp_send_json_success( $content );

}
add_action( 'wp_ajax_gamipress_profile_load_award_achievement_table', 'gamipress_ajax_profile_load_award_achievement_award' );

/**
 * Generate markup for awarding an achievement to a user
 *
 * @since  1.6.8
 *
 * @param  object $user         The current user's $user object
 *
 * @return string               concatenated markup
 */
function gamipress_profile_award_requirement( $user = null ) {

    // Return if user is not a manager
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    // Grab our types
    $requirement_types = gamipress_get_requirement_types();

    ?>

    <h2><?php _e( 'Award Requirement', 'gamipress' ); ?></h2>

    <table class="form-table">

        <tr>
            <th><label for="gamipress-award-requirement-type-select"><?php _e( 'Select a requirement type to award:', 'gamipress' ); ?></label></th>
            <td>
                <select id="gamipress-award-requirement-type-select">
                    <option value=""><?php _e( 'Choose a requirement type', 'gamipress' ); ?></option>
                    <?php foreach ( $requirement_types as $slug => $data ) :
                        echo '<option value="'. $slug .'">' . ucwords( $data['singular_name'] ) .'</option>';
                    endforeach; ?>
                </select>
            </td>
        </tr>

    </table>

    <div id="gamipress-awards-options">
        <?php foreach ( $requirement_types as $slug => $data ) : ?>
            <div id="<?php echo esc_attr( $slug ); ?>" data-loaded="false" style="display: none;">
                <span class="spinner is-active"></span>
            </div>
        <?php endforeach; ?>

    </div><!-- #gamipress-awards-options -->

    <hr>

    <?php
}

/**
 * Ajax handler to load the requiremetn award table of the given post type
 *
 * @since 1.8.7
 */
function gamipress_ajax_profile_load_award_requirement_table() {

    // Security check, forces to die if not security passed
    check_ajax_referer( 'gamipress_admin', 'nonce' );

    // Return if user is not a manager
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You can perform this action.', 'gamipress' ) );
    }

    $post_type  = sanitize_text_field( $_POST['post_type'] );
    $user_id    = absint( $_POST['user_id'] );

    // Check if user has permissions
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        wp_send_json_error( __( 'You can perform this action.', 'gamipress' ) );
    }

    // Grab our types
    $achievement_types = gamipress_get_achievement_types();
    $rank_types = gamipress_get_rank_types();
    $requirement_types = gamipress_get_requirement_types();

    $achievements = gamipress_get_user_achievements( array(
        'user_id' => absint( $user_id ),
        'achievement_type' => $post_type
    ) );

    $achievement_ids = array_map( function( $achievement ) {
        return $achievement->ID;
    }, $achievements );

    // On network wide active installs, we need to switch to main blog mostly for posts permalinks and thumbnails
    $blog_id = gamipress_switch_to_main_site_if_network_wide_active();

    ob_start(); ?>

    <?php foreach ( $requirement_types as $slug => $data ) :

        // Skip if not is the points type to render
        if( $post_type !== $slug ) {
            continue;
        } ?>
        <div id="<?php echo esc_attr( $slug ); ?>">
            <table id="<?php echo esc_attr( $slug ); ?>-table" class="wp-list-table widefat fixed striped gamipress-table">

                <thead>
                <tr>
                    <th><?php echo ucwords( $data['singular_name'] ); ?></th>
                    <th><?php _e( 'Actions', 'gamipress' ); ?></th>
                </tr>
                </thead>

                <tbody>
                <?php
                // Load achievement type entries
                $the_query = new WP_Query( array(
                    'post_type'      	=> $slug,
                    'posts_per_page' 	=> -1,
                    'post_status'    	=> 'publish',
                    'suppress_filters' 	=> false
                ) );

                if ( $the_query->have_posts() ) : ?>

                    <?php while ( $the_query->have_posts() ) : $the_query->the_post();

                        // If not parent object, skip
                        if( $slug === 'step' && ! $achievement = gamipress_get_step_achievement( get_the_ID() ) ) {
                            continue;
                        } else if( $slug === 'points-award' && ! $points_type = gamipress_get_points_award_points_type( get_the_ID() ) ) {
                            continue;
                        } else if( $slug === 'points-deduct' && ! $points_type = gamipress_get_points_deduct_points_type( get_the_ID() ) ) {
                            continue;
                        } else if( $slug === 'rank-requirement' && ! $rank = gamipress_get_rank_requirement_rank( get_the_ID() ) ) {
                            continue;
                        }

                        // Setup our award URL
                        $award_url = add_query_arg( array(
                            'action'         => 'award',
                            'achievement_id' => absint( get_the_ID() ),
                            'user_id'        => absint( $user_id )
                        ) );
                        ?>
                        <tr>
                            <td>
                                <?php // Output parent achievement
                                if( $slug === 'step' && $achievement ) : ?>

                                    <?php // Achievement thumbnail ?>
                                    <?php echo gamipress_get_achievement_post_thumbnail( $achievement->ID, array( 32, 32 ) ); ?>

                                    <?php // Step title ?>
                                    <strong><?php echo gamipress_get_post_field( 'post_title', get_the_ID() ); ?></strong>

                                    <?php // Step relationship details ?>
                                    <?php echo ( isset( $achievement_types[$achievement->post_type] ) ? '<br> ' . $achievement_types[$achievement->post_type]['singular_name'] . ': ' : '' ); ?>
                                    <?php echo '<a href="' . get_edit_post_link( $achievement->ID ) . '">' . gamipress_get_post_field( 'post_title', $achievement->ID ) . '</a>'; ?>

                                <?php elseif( in_array( $slug, array( 'points-award', 'points-deduct' ) ) && $points_type ) : ?>

                                    <?php // Points type thumbnail ?>
                                    <?php echo gamipress_get_points_type_thumbnail( $points_type->ID, array( 32, 32 ) ); ?>

                                    <?php // Points award/deduct title ?>
                                    <strong><?php echo gamipress_get_post_field( 'post_title', get_the_ID() ); ?></strong>
                                    <br>
                                    <?php echo '<a href="' . get_edit_post_link( $points_type->ID ) . '">' . gamipress_get_post_field( 'post_title', $points_type->ID ) . '</a>'; ?>

                                <?php elseif( $slug === 'rank-requirement' && $rank ) : ?>

                                    <?php // Rank thumbnail ?>
                                    <?php echo gamipress_get_rank_post_thumbnail( $rank->ID, array( 32, 32 ) ); ?>

                                    <?php // Rank requirement title ?>
                                    <strong><?php echo gamipress_get_post_field( 'post_title', get_the_ID() ); ?></strong>

                                    <?php // Rank requirement relationship details ?>
                                    <?php echo ( isset( $rank_types[$rank->post_type] ) ? '<br> ' . $rank_types[$rank->post_type]['singular_name'] . ': ' : '' ); ?>
                                    <?php echo '<a href="' . get_edit_post_link( $rank->ID ) . '">' . gamipress_get_post_field( 'post_title', $rank->ID ) . '</a>'; ?>

                                <?php endif; ?>
                            </td>
                            <td>
                                <a class="gamipress-award-achievement" href="<?php echo esc_url( wp_nonce_url( $award_url, 'gamipress_award_achievement' ) ); ?>"><?php printf( __( 'Award %s', 'gamipress' ), ucwords( $data['singular_name'] ) ); ?></a>
                                <?php if ( in_array( get_the_ID(), (array) $achievement_ids ) ) :
                                    // Setup our revoke URL
                                    $revoke_url = add_query_arg( array(
                                        'action'         => 'revoke',
                                        'user_id'        => absint( $user_id ),
                                        'achievement_id' => absint( get_the_ID() ),
                                    ) );
                                    ?>
                                    | <span class="delete"><a class="error gamipress-revoke-achievement" href="<?php echo esc_url( wp_nonce_url( $revoke_url, 'gamipress_revoke_achievement' ) ); ?>"><?php _e( 'Revoke Award', 'gamipress' ); ?></a></span>
                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endwhile; ?>

                <?php else : ?>
                    <tr>
                        <td colspan="3"><?php printf( __( 'No %s found.', 'gamipress' ), $data['plural_name'] ); ?></td>
                    </tr>
                <?php endif; wp_reset_postdata(); ?>

                </tbody>

            </table><!-- #<?php echo esc_attr( $slug ); ?> -->
        </div><!-- #<?php echo esc_attr( $slug ); ?> -->

    <?php endforeach; ?>

    <?php $content = ob_get_clean();

    // If switched to blog, return back to que current blog
    if( $blog_id !== get_current_blog_id() && is_multisite() ) {
        restore_current_blog();
    }

    if( empty( $content ) ) {
        wp_send_json_error( __( 'Couldn\'t load this content.', 'gamipress' ) );
    }

    // Return the table rendered
    wp_send_json_success( $content );

}
add_action( 'wp_ajax_gamipress_profile_load_award_requirement_table', 'gamipress_ajax_profile_load_award_requirement_table' );


/**
 * Process the adding/revoking of achievements on the user profile page
 *
 * @since  1.0.0
 */
function gamipress_process_user_data() {

    // verify user meets minimum role to view earned achievements
    if ( current_user_can( gamipress_get_manager_capability() ) ) {

        // Process awarding achievement to user
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'award' &&  isset( $_GET['user_id'] ) && isset( $_GET['achievement_id'] ) ) {

            // Verify our nonce
            check_admin_referer( 'gamipress_award_achievement' );

            // Award the achievement
            gamipress_award_achievement_to_user( absint( $_GET['achievement_id'] ), absint( $_GET['user_id'] ), get_current_user_id() );

            // Redirect back to the user editor
            wp_redirect( add_query_arg( 'user_id', absint( $_GET['user_id'] ), admin_url( 'user-edit.php' ) ) );
            exit();
        }

        // Process revoking achievement from a user
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'revoke' && isset( $_GET['user_id'] ) && isset( $_GET['achievement_id'] ) ) {

            // Verify our nonce
            check_admin_referer( 'gamipress_revoke_achievement' );

            $earning_id = isset( $_GET['user_earning_id'] ) ? absint( $_GET['user_earning_id'] ) : 0 ;

            // Revoke the achievement
            gamipress_revoke_achievement_to_user( absint( $_GET['achievement_id'] ), absint( $_GET['user_id'] ), $earning_id );

            // If revoking from user earnings screen return directly without redirect
            if( isset( $_GET['page'] ) && $_GET['page'] === 'gamipress_user_earnings' ) {
                exit();
            }

            // Redirect back to the user editor
            wp_redirect( add_query_arg( 'user_id', absint( $_GET['user_id'] ), admin_url( 'user-edit.php' ) ) );
            exit();

        }

    }

}
add_action( 'admin_init', 'gamipress_process_user_data' );