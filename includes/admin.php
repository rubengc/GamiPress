<?php
/**
 * Admin
 *
 * @package     GamiPress\Admin
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_DIR . 'includes/admin/contextual-help.php';
require_once GAMIPRESS_DIR . 'includes/admin/debug.php';
require_once GAMIPRESS_DIR . 'includes/admin/meta-boxes.php';
require_once GAMIPRESS_DIR . 'includes/admin/plugins.php';
require_once GAMIPRESS_DIR . 'includes/admin/ranks.php';
require_once GAMIPRESS_DIR . 'includes/admin/requirements.php';
require_once GAMIPRESS_DIR . 'includes/admin/requirements-ui.php';
require_once GAMIPRESS_DIR . 'includes/admin/log-extra-data-ui.php';
require_once GAMIPRESS_DIR . 'includes/admin/pages/add-ons.php';
require_once GAMIPRESS_DIR . 'includes/admin/pages/settings.php';
require_once GAMIPRESS_DIR . 'includes/admin/pages/support.php';
require_once GAMIPRESS_DIR . 'includes/admin/pages/tools.php';
require_once GAMIPRESS_DIR . 'includes/admin/upgrades.php';

/**
 * Create GamiPress menus
 */
function gamipress_admin_menu() {

    // Set minimum role setting for menus
    $minimum_role = gamipress_get_manager_capability();

    // Achievement types
    $achievement_types = gamipress_get_achievement_types();

    // Achievements menu
    if( ! empty( $achievement_types ) ) {
        add_menu_page( __( 'Achivements', 'gamipress' ), __( 'Achievements', 'gamipress' ), $minimum_role, 'gamipress_achievements', 'gamipress_achievements', 'dashicons-awards', 53 );
    }

    // Rank types
    $rank_types = gamipress_get_rank_types();

    // Achievements menu
    if( ! empty( $rank_types ) ) {
        add_menu_page( __( 'Ranks', 'gamipress' ), __( 'Ranks', 'gamipress' ), $minimum_role, 'gamipress_ranks', 'gamipress_ranks', 'dashicons-rank', 54 );
    }

    // GamiPress menu
    add_menu_page( 'GamiPress', 'GamiPress', $minimum_role, 'gamipress', 'gamipress_settings', 'dashicons-gamipress', 55 );

}
add_action( 'admin_menu', 'gamipress_admin_menu' );

/**
 * Create GamiPress Settings menus
 */
function gamipress_admin_submenu() {

    // Set minimum role setting for menus
    $minimum_role = gamipress_get_manager_capability();

    // GamiPress sub menus
    add_submenu_page( 'gamipress', __( 'Add-ons', 'gamipress' ), __( 'Add-ons', 'gamipress' ), $minimum_role, 'gamipress_add_ons', 'gamipress_add_ons_page' );
    add_submenu_page( 'gamipress', __( 'Help / Support', 'gamipress' ), __( 'Help / Support', 'gamipress' ), $minimum_role, 'gamipress_help_support', 'gamipress_help_support_page' );

}
add_action( 'admin_menu', 'gamipress_admin_submenu', 12 );

/**
 * Register GamiPress dashboard widget.
 */
function gamipress_dashboard_widgets() {

    wp_add_dashboard_widget( 'gamipress', 'GamiPress', 'gamipress_dashboard_widget' );
}
add_action( 'wp_dashboard_setup', 'gamipress_dashboard_widgets' );

/**
 * GamiPress dashboard widget output.
 */
function gamipress_dashboard_widget() {
    $achievement_types = gamipress_get_achievement_types();

    $points_types = gamipress_get_points_types();

    $rank_types = gamipress_get_rank_types();
    ?>

    <h3>
        <a href="<?php echo admin_url( 'edit.php?post_type=achievement-type' ); ?>" id="achievement-types">
            <i class="dashicons dashicons-awards"></i>
            <?php printf( _n( '%d Achievement Type', '%d Achievement Types', count( $achievement_types ) ), count( $achievement_types ) ); ?>
        </a>
    </h3>

    <div id="gamipress-registered-achievements" class="gamipress-registered-achievements">
        <ul>
            <?php foreach( $achievement_types as $achievement_type_slug => $achievement_type) : ?>
                <?php $achievement_type_count = wp_count_posts( $achievement_type_slug ); ?>
                <li>
                    <a href="<?php echo admin_url( 'edit.php?post_type=' . $achievement_type_slug ); ?>">
                        <?php printf( _n( '%d ' . $achievement_type['singular_name'], '%d ' . $achievement_type['plural_name'], $achievement_type_count->publish ), $achievement_type_count->publish ); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <h3>
        <a href="<?php echo admin_url( 'edit.php?post_type=points-type' ); ?>" id="points-types">
            <i class="dashicons dashicons-star-filled"></i>
            <?php printf( _n( '%d Points Type', '%d Points Types', count( $points_types ) ), count( $points_types ) ); ?>
        </a>
    </h3>

    <div id="gamipress-registered-points" class="gamipress-registered-points">
        <ul>
            <?php foreach( $points_types as $points_type_slug => $points_type) : ?>
                <li>
                    <a href="<?php echo get_edit_post_link( $points_type['ID'] ); ?>">
                        <?php echo $points_type['plural_name']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <h3>
        <a href="<?php echo admin_url( 'edit.php?post_type=rank-type' ); ?>" id="achievement-types">
            <i class="dashicons dashicons-rank"></i>
            <?php printf( _n( '%d Rank Type', '%d Rank Types', count( $rank_types ) ), count( $rank_types ) ); ?>
        </a>
    </h3>

    <div id="gamipress-registered-ranks" class="gamipress-registered-ranks">
        <ul>
            <?php foreach( $rank_types as $rank_type_slug => $rank_type) : ?>
                <?php $rank_type_count = wp_count_posts( $rank_type_slug ); ?>
                <li>
                    <a href="<?php echo admin_url( 'edit.php?post_type=' . $rank_type_slug ); ?>">
                        <?php printf( _n( '%d ' . $rank_type['singular_name'], '%d ' . $rank_type['plural_name'], $rank_type_count->publish ), $rank_type_count->publish ); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <h3><?php _e( 'Latest Logs', 'gamipress' ); ?></h3>

    <?php

    if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {
        gamipress_dashboard_widget_logs_old();
        return;
    }

    // Setup table
    ct_setup_table( 'gamipress_logs' );

    $query = new CT_Query( array(
        'orderby'        => 'date',
        'order'          => 'DESC',
        'items_per_page' => 5,
        'no_found_rows'  => true,
        'cache_results'  => false,
    ) );

    $logs = $query->get_results();

    if ( count( $logs ) > 0 ) {

        echo '<div id="gamipress-latest-logs" class="gamipress-latest-logs">';

        echo '<ul>';

        $today    = date( 'Y-m-d', current_time( 'timestamp' ) );
        $yesterday = date( 'Y-m-d', strtotime( '-1 day', current_time( 'timestamp' ) ) );

        foreach ( $logs as $log ) {

            $time = strtotime( $log->date );

            if ( date( 'Y-m-d', $time ) == $today ) {
                $relative = __( 'Today', 'gamipress' );
            } elseif ( date( 'Y-m-d', $time ) == $yesterday ) {
                $relative = __( 'Yesterday', 'gamipress' );
            } elseif ( date( 'Y', $time ) !== date( 'Y', current_time( 'timestamp' ) ) ) {
                /* translators: date and time format for recent posts on the dashboard, from a different calendar year, see https://secure.php.net/date */
                $relative = date_i18n( __( 'M jS Y' ), $time );
            } else {
                /* translators: date and time format for recent posts on the dashboard, see https://secure.php.net/date */
                $relative = date_i18n( __( 'M jS' ), $time );
            }

            $edit_post_link = ct_get_edit_link( 'gamipress_logs', $log->log_id );

            printf(
                '<li><a href="%1$s">%2$s</a> <span>%3$s</span></li>',
                $edit_post_link,
                apply_filters( 'gamipress_render_log_title', $log->title, $log->log_id ),
                sprintf( _x( '%1$s, %2$s', 'dashboard' ), $relative, mysql2date( get_option('time_format'), $time ) )
            );
        }

        echo '</ul>';
        echo '</div>';

    } else {
        echo '<p>' . __( 'Nothing to show :)', 'gamipress' ) .'</p>';
    }

    wp_reset_postdata();
}

/**
 * Register our custom columns
 *
 * @since 1.0.6
 *
 * @param $posts_columns
 * @param $post_type
 *
 * @return mixed
 */
function gamipress_posts_columns( $posts_columns, $post_type ) {
    if( ! in_array( $post_type, array( 'points-type', 'achievement-type', 'rank-type' ) ) ) {
        return $posts_columns;
    }

    $posts_columns['title'] = __( 'Singular Name', 'gamipress' );

    // Try to place our column before date column
    $pos = array_search( 'date', array_keys( $posts_columns ) );

    if ( ! is_int( $pos ) ) {
        $pos = 1;
    }

    // Place our column in our desired position
    $chunks                     = array_chunk( $posts_columns, $pos, true );
    $chunks[0]['plural_name']   = __( 'Plural Name', 'gamipress' );
    $chunks[0]['post_name']     = __( 'Slug', 'gamipress' );

    return call_user_func_array( 'array_merge', $chunks );
}
add_filter( 'manage_posts_columns', 'gamipress_posts_columns', 10, 2 );

/**
 * Output for our custom columns
 *
 * @since 1.0.6
 *
 * @param $column_name
 * @param $post_id
 */
function gamipress_posts_custom_columns( $column_name, $post_id ) {
    if( ! in_array( get_post_type( $post_id ), array( 'points-type', 'achievement-type', 'rank-type' ) ) ) {
        return;
    }

    switch( $column_name ) {
        case 'plural_name':
            echo get_post_meta( $post_id, '_gamipress_plural_name', true );
            break;
        case 'post_name':
            echo get_post_field( 'post_name', $post_id );
            break;
    }
}
add_action( 'manage_posts_custom_column', 'gamipress_posts_custom_columns', 10, 2 );

/**
 * On delete our post, remove relationships
 *
 * @since 1.1.5
 *
 * @param integer $post_id
 */
function gamipress_on_delete_post( $post_id ) {

    $post_type = get_post_type( $post_id );

    $is_achievement = gamipress_is_achievement( $post_id );

    $is_rank = gamipress_is_rank( $post_id );

    if( ! in_array( $post_type, array( 'points-type', 'achievement-type', 'rank-type' ) ) || ! $is_achievement || ! $is_rank ) {
        return;
    }

    if( $post_type === 'achievement-type' || $post_type === 'rank-type' ) {
        // Remove all achievements of this achievement type or if is a rank type, remove all ranks of this rank type

        global $wpdb;

        $dependents = $wpdb->get_results( $wpdb->prepare( "SELECT p.ID FROM   $wpdb->posts AS p WHERE  p.post_type = %s", $post_type ) );

        foreach( $dependents as $dependent ) {
            // Remove the achievement
            wp_delete_post( $dependent['ID'] );
        }
    } else if( $post_type === 'points-type' ) {

        // Get assigned points awards
        $points_awards = gamipress_get_assigned_requirements( $post_id, 'points-award' );

        if( $points_awards ) {

            foreach( $points_awards as $points_award ) {
                // Remove the points award
                wp_delete_post( $points_award->ID );
            }

        }

        // Get assigned points deducts
        $points_deducts = gamipress_get_assigned_requirements( $post_id, 'points-deduct' );

        if( $points_deducts ) {

            foreach( $points_deducts as $points_deduct ) {
                // Remove the points deduct
                wp_delete_post( $points_deduct->ID );
            }

        }

    } else if( $is_achievement || $is_rank ) {
        // Remove steps/rank requirements assigned

        if( $is_rank ) {
            $requirement_type = 'rank-requirement';
        } else if ( $is_achievement ) {
            $requirement_type = 'step';
        }

        if( ! isset( $requirement_type ) ) {
            return;
        }

        // Get assigned requirements
        $assigned_requirements = gamipress_get_assigned_requirements( $post_id, $requirement_type );

        if( $assigned_requirements ) {

            foreach( $assigned_requirements as $requirement ) {
                // Remove the requirement
                wp_delete_post( $requirement->ID );
            }

        }
    }

}
add_action( 'delete_post', 'gamipress_on_delete_post' );

/**
 * Processes all GamiPress actions sent via POST and GET by looking for the 'gamipress-action' request and running do_action() to call the function
 *
 * @since 1.1.5
 */
function gamipress_process_actions() {
    if ( isset( $_POST['gamipress-action'] ) ) {
        do_action( 'gamipress_action_post_' . $_POST['gamipress-action'], $_POST );
    }

    if ( isset( $_GET['gamipress-action'] ) ) {
        do_action( 'gamipress_action_get_' . $_GET['gamipress-action'], $_GET );
    }
}
add_action( 'admin_init', 'gamipress_process_actions' );

/**
 * Overrides the enter title here on rank edit screen
 *
 * @param $placeholder
 * @param $post
 *
 * @return string|void
 */
function gamipress_admin_enter_title_here( $placeholder, $post ) {

    if( gamipress_is_rank( $post->post_type ) ) {
        return __( 'Rank name', 'gamipress' );
    }

    return $placeholder;

}
add_filter( 'enter_title_here', 'gamipress_admin_enter_title_here', 10, 2 );

/**
 * Add custom footer text to the admin dashboard
 *
 * @since	    1.3.8.1
 *
 * @param       string $footer_text The existing footer text
 *
 * @return      string
 */
function gamipress_admin_footer_text( $footer_text ) {
    global $typenow;

    if (
        $typenow == 'points-type'
        || $typenow == 'achievement-type'
        || $typenow == 'rank-type'
        || in_array( $typenow, gamipress_get_achievement_types_slugs() )
        || in_array( $typenow, gamipress_get_rank_types_slugs() )
        || ( isset( $_GET['page'] ) && (
                $_GET['page'] === 'gamipress_settings'
                || $_GET['page'] === 'gamipress_logs'
                || $_GET['page'] === 'edit_gamipress_logs'
                || $_GET['page'] === 'gamipress_add_ons'
                || $_GET['page'] === 'gamipress_help_support'
                || $_GET['page'] === 'gamipress_tools'
            )
        )
    ) {

        $gamipress_footer_text = sprintf( __( 'Thank you for using <a href="%1$s" target="_blank">GamiPress</a>! Please leave us a <a href="%2$s" target="_blank">%3$s</a> rating on WordPress.org', 'gamipress' ),
            'https://gamipress.com',
            'https://wordpress.org/support/plugin/gamipress/reviews/?rate=5#new-post',
            '&#9733;&#9733;&#9733;&#9733;&#9733;'
        );

        return str_replace( '</span>', '', $footer_text ) . ' | ' . $gamipress_footer_text . '</span>';

    } else {

        return $footer_text;

    }

}
add_filter( 'admin_footer_text', 'gamipress_admin_footer_text' );