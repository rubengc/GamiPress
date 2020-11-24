<?php
/**
 * Admin Dashboard
 *
 * @package     GamiPress\Admin\Dashboard
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register GamiPress dashboard widget.
 *
 * @since 1.0.0
 * @updated 1.4.8 Added network and user checks
 */
function gamipress_dashboard_widgets() {

    // Bail if GamiPress is active network wide and we are not in main site
    if( gamipress_is_network_wide_active() && ! is_main_site() ) {
        return;
    }

    // Bail if current user can manage GamiPress
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        return;
    }

    wp_add_dashboard_widget( 'gamipress', 'GamiPress', 'gamipress_dashboard_widget' );

}
add_action( 'wp_dashboard_setup', 'gamipress_dashboard_widgets' );

/**
 * GamiPress dashboard widget output.
 *
 * @since 1.0.0
 */
function gamipress_dashboard_widget() {

    // Get our types
    $achievement_types = gamipress_get_achievement_types();
    $points_types = gamipress_get_points_types();
    $rank_types = gamipress_get_rank_types();
    ?>

    <h3>
        <a href="<?php echo admin_url( 'edit.php?post_type=achievement-type' ); ?>" id="achievement-types">
            <?php echo gamipress_dashicon( 'awards' ); ?>
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
            <?php echo gamipress_dashicon( 'star-filled' ); ?>
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
            <?php echo gamipress_dashicon( 'rank' ); ?>
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

            if ( date( 'Y-m-d', $time ) === $today ) {
                $relative = __( 'Today', 'gamipress' );
            } elseif ( date( 'Y-m-d', $time ) === $yesterday ) {
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
                sprintf( _x( '%1$s, %2$s', 'dashboard' ), $relative, mysql2date( get_option( 'time_format' ), $log->date ) )
            );
        }

        echo '</ul>';
        echo '</div>';

    } else {
        echo '<p>' . __( 'Nothing to show :)', 'gamipress' ) .'</p>';
    }

    wp_reset_postdata();
}