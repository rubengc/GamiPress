<?php
/**
 * Admin Achievements
 *
 * @package     GamiPress\Admin\Achievements
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.3.9.5
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register achievements related hooks
 *
 * @since 1.3.9.5
 */
function gamipress_load_achievements_admin_hooks() {

    foreach( gamipress_get_achievement_types() as $slug => $achievement ) {
        add_filter( "manage_{$slug}_posts_columns", 'gamipress_achievement_posts_columns' );
        add_action( "manage_{$slug}_posts_custom_column", 'gamipress_achievement_posts_custom_columns', 10, 2 );
    }

}
add_action( 'admin_init', 'gamipress_load_achievements_admin_hooks' );

/**
 * Register our custom achievements columns
 *
 * @since 1.3.9.5
 *
 * @param array $posts_columns
 *
 * @return array
 */
function gamipress_achievement_posts_columns( $posts_columns ) {

    $posts_columns['title'] = __( 'Name', 'gamipress' );

    // Prepend the thumbnail column
    $chunks                 = array_chunk( $posts_columns, 1, true );
    $chunks[0]['thumbnail'] = __( 'Image', 'gamipress' );

    $posts_columns = call_user_func_array( 'array_merge', $chunks );

    // Try to place our column before date column
    $pos = array_search( 'author', array_keys( $posts_columns ) );

    if ( ! is_int( $pos ) ) {
        $pos = 1;
    }

    // Place our column in our desired position
    $chunks                             = array_chunk( $posts_columns, $pos, true );
    $chunks[0]['points']                = __( 'Points Awarded', 'gamipress' );
    $chunks[0]['earned_by']             = __( 'Earned By', 'gamipress' );
    $chunks[0]['maximum_earnings']      = __( 'Max. Earnings', 'gamipress' );
    $chunks[0]['unlock_with_points']    = __( 'Unlock with Points', 'gamipress' );

    return call_user_func_array( 'array_merge', $chunks );

}

/**
 * Output for our custom achievements columns
 *
 * @since 1.3.9.5
 *
 * @param string    $column_name
 * @param integer   $post_id
 */
function gamipress_achievement_posts_custom_columns( $column_name, $post_id ) {

    $prefix = '_gamipress_';

    switch( $column_name ) {
        case 'thumbnail':
            $can_edit_post = current_user_can( 'edit_post', $post_id );

            if( $can_edit_post ) {
                printf(
                    '<a href="%s" aria-label="%s">%s</a>',
                    get_edit_post_link( $post_id ),
                    /* translators: %s: post title */
                    esc_attr( sprintf( __( '&#8220;%s&#8221; (Edit)' ), get_post_field( 'post_title', $post_id ) ) ),
                    get_the_post_thumbnail( $post_id, array( 32, 32 ) )
                );
            } else {
                echo get_the_post_thumbnail( $post_id, array( 32, 32 ) );
            }

            break;
        case 'points':

            // Get the points vars
            $awarded_points_type = gamipress_get_post_meta( $post_id, $prefix . 'points_type' );
            $points = absint( gamipress_get_post_meta( $post_id, $prefix . 'points' ) );

            if( $points !== 0 ) {
                echo gamipress_format_points( $points, $awarded_points_type );
            }

            break;
        case 'earned_by':

            $earned_by_options = apply_filters( 'gamipress_achievement_earned_by', array(
                'triggers' 			=> __( 'Completing Steps', 'gamipress' ),
                'points' 			=> __( 'Minimum Number of Points', 'gamipress' ),
                'rank' 				=> __( 'Reach a Rank', 'gamipress' ),
                'admin' 			=> __( 'Admin-awarded Only', 'gamipress' ),
            ) );

            $earned_by = gamipress_get_post_meta( $post_id, $prefix . 'earned_by' );

            echo ( isset( $earned_by_options[$earned_by] ) ? $earned_by_options[$earned_by] : $earned_by );

            switch( $earned_by ) {
                case 'points':

                    // Get the points vars
                    $required_points_type = gamipress_get_post_meta( $post_id, $prefix . 'points_type_required' );
                    $required_points = absint( gamipress_get_post_meta( $post_id, $prefix . 'points_required' ) );

                    if( $required_points !== 0 ) {
                        echo '<br><strong>' . gamipress_format_points( $required_points, $required_points_type ) . '</strong>';
                    }

                    break;
                case 'rank':

                    // Get the rank ID
                    $required_rank_id = absint( gamipress_get_post_meta( $post_id, $prefix . 'rank_required' ) );

                    if( $required_rank_id !== 0 ) {
                        echo '<br><strong><a href="' . get_edit_post_link( $required_rank_id ) . '">' . gamipress_get_post_field( 'post_title', $required_rank_id ) . '</a></strong>';
                    }

                    break;
            }

            break;
        case 'maximum_earnings':
            $maximum_earnings = absint( gamipress_get_post_meta( $post_id, $prefix . 'maximum_earnings' ) );
            $label = '<strong>' . ( $maximum_earnings === 0 ? __( 'Unlimited', 'gamipress' ) : $maximum_earnings ) . '</strong>';

            echo sprintf( __( '%s time(s) per user', 'gamipress' ), $label );
            echo "<br>";

            $global_maximum_earnings = absint( gamipress_get_post_meta( $post_id, $prefix . 'global_maximum_earnings' ) );
            $label = '<strong>' . ( $global_maximum_earnings === 0 ? __( 'Unlimited', 'gamipress' ) : $global_maximum_earnings ) . '</strong>';

            echo sprintf( __( '%s time(s) for all users', 'gamipress' ), $label );
            break;
        case 'unlock_with_points':

            $unlock_with_points = gamipress_get_post_meta( $post_id, $prefix . 'unlock_with_points' );

            if( ! (bool) $unlock_with_points ) {
                break;
            }

            // Get the points vars
            $points_type_to_unlock = gamipress_get_post_meta( $post_id, $prefix . 'points_type_to_unlock' );
            $points_to_unlock = absint( gamipress_get_post_meta( $post_id, $prefix . 'points_to_unlock' ) );

            if( $points_to_unlock !== 0 ) {
                echo gamipress_format_points( $points_to_unlock, $points_type_to_unlock );
            }

            break;
    }

}