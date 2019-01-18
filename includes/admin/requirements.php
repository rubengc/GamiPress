<?php
/**
 * Admin Requirements
 *
 * @package     GamiPress\Admin\Requirements
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register our custom requirements columns
 *
 * @since 1.0.6
 *
 * @param $posts_columns
 * @param $post_type
 *
 * @return mixed
 */
function gamipress_requirements_posts_columns( $posts_columns, $post_type ) {
    if( ! in_array( $post_type, gamipress_get_requirement_types_slugs() ) ) {
        return $posts_columns;
    }

    // Try to place our column before date column
    $pos = array_search( 'date', array_keys( $posts_columns ) );

    if ( ! is_int( $pos ) ) {
        $pos = 1;
    }

    // Place our column in our desired position
    $chunks                     = array_chunk( $posts_columns, $pos, true );

    if( ( $post_type === 'points-award' || $post_type === 'points-deduct' ) ) {
        $chunks[0]['connected_to']  = __( 'Points Type', 'gamipress' );
    } else if( $post_type === 'step' ) {
        $chunks[0]['connected_to']  = __( 'Achievement', 'gamipress' );
    } else if( $post_type === 'rank-requirement' ) {
        $chunks[0]['connected_to']  = __( 'Rank', 'gamipress' );
    }

    return call_user_func_array( 'array_merge', $chunks );
}
add_filter( 'manage_posts_columns', 'gamipress_requirements_posts_columns', 10, 2 );

/**
 * Output for our custom requirements columns
 *
 * @since 1.0.6
 *
 * @param $column_name
 * @param $post_id
 */
function gamipress_requirements_posts_custom_columns( $column_name, $post_id ) {

    if( ! in_array( gamipress_get_post_type( $post_id ), gamipress_get_requirement_types_slugs() ) || $column_name !== 'connected_to' ) {
        return;
    }

    $post_type = gamipress_get_post_type( $post_id );

    if( ( $post_type === 'points-award' ) ) {
        $connected_label = __( 'Points Type', 'gamipress' );
        $connected_object =  gamipress_get_points_award_points_type( $post_id );
    } else if( ( $post_type === 'points-deduct' ) ) {
        $connected_label = __( 'Points Type', 'gamipress' );
        $connected_object =  gamipress_get_points_deduct_points_type( $post_id );
    } else if( $post_type === 'step' ) {
        $connected_label = __( 'Achievement', 'gamipress' );
        $connected_object = gamipress_get_step_achievement( $post_id );
    } else if( $post_type === 'rank-requirement' ) {
        $connected_label = __( 'Rank', 'gamipress' );
        $connected_object = gamipress_get_rank_requirement_rank( $post_id );
    }

    if( $connected_object ) : ?>
        <a href="<?php echo get_edit_post_link( $connected_object->ID ); ?>"><?php echo $connected_object->post_title; ?></a>
    <?php else : ?>
        <span style="color: #a00;"><?php printf( __( 'Missed %s', 'gamipress' ), $connected_label ); ?></span>
    <?php endif;

}
add_action( 'manage_posts_custom_column', 'gamipress_requirements_posts_custom_columns', 10, 2 );