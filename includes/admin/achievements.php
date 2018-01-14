<?php
/**
 * Admin Achievements
 *
 * @package     GamiPress\Admin\Achievements
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
    $chunks                     = array_chunk( $posts_columns, $pos, true );
    $chunks[0]['points']   = __( 'Points Awarded', 'gamipress' );
    $chunks[0]['earned_by']   = __( 'Earned By', 'gamipress' );
    $chunks[0]['maximum_earnings']   = __( 'Max. Earnings', 'gamipress' );
    $chunks[0]['unlock_with_points']   = __( 'Unlock with Points', 'gamipress' );

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
            $awarded_points_type = get_post_meta( $post_id, $prefix . 'points_type', true );

            $points_types = gamipress_get_points_types();

            $points_types[''] = array(
                'singular_name' => __( 'Point', 'gamipress' ),
                'plural_name' => __( 'Points', 'gamipress' ),
            );

            if( ! isset( $points_types[$awarded_points_type] ) ) {
                break;
            }

            $points_type = $points_types[$awarded_points_type];

            $points = absint( get_post_meta( $post_id, $prefix . 'points', true ) );

            if( $points === 0 ) {
                break;
            }

            echo $points . ' ' . _n( $points_type['singular_name'], $points_type['plural_name'], $points );
            break;
        case 'earned_by':

            $earned_by_options = apply_filters( 'gamipress_achievement_earned_by', array(
                'triggers' 			=> __( 'Completing Steps', 'gamipress' ),
                'points' 			=> __( 'Minimum Number of Points', 'gamipress' ),
                'rank' 				=> __( 'Reach a Rank', 'gamipress' ),
                'admin' 			=> __( 'Admin-awarded Only', 'gamipress' ),
            ) );

            $earned_by = get_post_meta( $post_id, $prefix . 'earned_by', true );

            echo ( isset( $earned_by_options[$earned_by] ) ? $earned_by_options[$earned_by] : $earned_by );

            break;
        case 'maximum_earnings':
            $maximum_earnings = absint( get_post_meta( $post_id, $prefix . 'maximum_earnings', true ) );

            if( $maximum_earnings === 0 ) {
                echo __( 'Unlimited', 'gamipress' );
            } else {
                echo $maximum_earnings;
            }
            break;
        case 'unlock_with_points':
            $unlock_with_points = get_post_meta( $post_id, $prefix . 'unlock_with_points', true );

            if( ! (bool) $unlock_with_points ) {
                break;
            }

            $points_type_to_unlock = get_post_meta( $post_id, $prefix . 'points_type_to_unlock', true );

            $points_types = gamipress_get_points_types();

            $points_types[''] = array(
                'singular_name' => __( 'Point', 'gamipress' ),
                'plural_name' => __( 'Points', 'gamipress' ),
            );

            if( ! isset( $points_types[$points_type_to_unlock] ) ) {
                break;
            }

            $points_type = $points_types[$points_type_to_unlock];

            $points_to_unlock = absint( get_post_meta( $post_id, $prefix . 'points_to_unlock', true ) );

            if( $points_to_unlock === 0 ) {
                break;
            }

            echo $points_to_unlock . ' ' . _n( $points_type['singular_name'], $points_type['plural_name'], $points_to_unlock );
            break;
    }

}