<?php
/**
 * Admin
 *
 * @package     GamiPress\Admin
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_DIR . 'includes/admin/debug.php';
require_once GAMIPRESS_DIR . 'includes/admin/meta-boxes.php';
require_once GAMIPRESS_DIR . 'includes/admin/requirements.php';
require_once GAMIPRESS_DIR . 'includes/admin/requirements-ui.php';
require_once GAMIPRESS_DIR . 'includes/admin/log-extra-data-ui.php';
require_once GAMIPRESS_DIR . 'includes/admin/settings.php';
require_once GAMIPRESS_DIR . 'includes/admin/support.php';

/**
 * Create GamiPress Settings menus
 */
function gamipress_admin_menu() {

    // Set minimum role setting for menus
    $minimum_role = gamipress_get_manager_capability();

    // Achievement types
    $achievement_types = gamipress_get_achievement_types();

    // Achievements menu
    if( ! empty( $achievement_types ) ) {
        add_menu_page( __( 'Achivements', 'gamipress' ), __( 'Achievements', 'gamipress' ), $minimum_role, 'gamipress_achievements', 'gamipress_achievements', 'dashicons-awards', 54 );
    }

    // GamiPress menu
    add_menu_page( 'GamiPress', 'GamiPress', $minimum_role, 'gamipress', 'gamipress_settings', 'dashicons-gamipress', 55 );

    // GamiPress sub menus
    add_submenu_page( 'gamipress', __( 'Help / Support', 'gamipress' ), __( 'Help / Support', 'gamipress' ), $minimum_role, 'gamipress_sub_help_support', 'gamipress_help_support_page' );

}
add_action( 'admin_menu', 'gamipress_admin_menu' );

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
    ?>
    <div id="gamipress-registered-posts" class="gamipress-registered-posts">
        <ul>
            <li>
                <a href="<?php echo admin_url( 'edit.php?post_type=achievement-type' ); ?>" id="achievement-types">
                    <?php printf( _n( '%d Achievement Type', '%d Achievement Types', count( $achievement_types ) - 2 ), count( $achievement_types ) - 2 ); ?>
                </a>
            </li>
            <?php foreach( $achievement_types as $achievement_type_slug => $achievement_type) : ?>
                <?php if( $achievement_type_slug === 'step' || $achievement_type_slug === 'points-award' ) { continue; } ?>
                <?php $achievement_type_count = wp_count_posts( $achievement_type_slug ); ?>
                <li>
                    <a href="<?php echo admin_url( 'edit.php?post_type=' . $achievement_type_slug ); ?>">
                        <?php printf( _n( '%d ' . $achievement_type['singular_name'], '%d ' . $achievement_type['plural_name'], $achievement_type_count->publish ), $achievement_type_count->publish ); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <ul>
            <li>
                <a href="<?php echo admin_url( 'edit.php?post_type=points-type' ); ?>" id="points-types">
                    <?php printf( _n( '%d Points Type', '%d Points Types', count( $points_types ) ), count( $points_types ) ); ?>
                </a>
            </li>
            <?php foreach( $points_types as $points_type_slug => $points_type) : ?>
                <li>
                    <a href="<?php echo get_edit_post_link( $points_type['ID'] ); ?>">
                        <?php echo $points_type['plural_name']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <h3><?php _e( 'Latest Logs', 'gamipress' ); ?></h3>

    <?php
    $posts = new WP_Query( array(
        'post_type'      => 'gamipress-log',
        'post_status'    => 'any',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'posts_per_page' => 5,
        'no_found_rows'  => true,
        'cache_results'  => false,
    ) );

    if ( $posts->have_posts() ) {

        echo '<div id="gamipress-latest-logs" class="gamipress-latest-logs">';

        echo '<ul>';

        $today    = date( 'Y-m-d', current_time( 'timestamp' ) );
        $yesterday = date( 'Y-m-d', strtotime( '-1 day', current_time( 'timestamp' ) ) );

        while ( $posts->have_posts() ) {
            $posts->the_post();

            $time = get_the_time( 'U' );
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

            // Use the post edit link for those who can edit, the permalink otherwise.
            $edit_post_link = current_user_can( 'edit_post', get_the_ID() ) ? get_edit_post_link() : get_permalink();

            $draft_or_post_title = _draft_or_post_title();
            printf(
                '<li><a href="%1$s">%2$s</a> <span>%3$s</span></li>',
                $edit_post_link,
                $draft_or_post_title,
                sprintf( _x( '%1$s, %2$s', 'dashboard' ), $relative, get_the_time() )
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
    if( ! in_array( $post_type, array( 'points-type', 'achievement-type' ) ) ) {
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
    if( ! in_array( get_post_type( $post_id ), array( 'points-type', 'achievement-type' ) ) ) {
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