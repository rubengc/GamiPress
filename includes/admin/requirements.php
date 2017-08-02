<?php
/**
 * Admin Requirements
 *
 * @package     GamiPress\Admin\Requirements
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
    $chunks[0]['connected_to']  = ( ( $post_type === 'points-award' ) ? __( 'Points Type', 'gamipress' ) : __( 'Achievement', 'gamipress' ) );

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
    if( ! in_array( get_post_type( $post_id ), gamipress_get_requirement_types_slugs() ) || $column_name !== 'connected_to' ) {
        return;
    }

    $connected_label = ( ( get_post_type( $post_id ) === 'points-award' ) ? __( 'Points Type', 'gamipress' ) : __( 'Achievement', 'gamipress' ) );
    $connected_id = gamipress_get_requirement_connected_id( $post_id );

    if( $connected_id ) :
        $post_object = get_post( $connected_id );
        if( $post_object ) : ?>
            <a href="<?php echo get_edit_post_link( $post_object->ID ); ?>"><?php echo $post_object->post_title; ?></a>
        <?php else : ?>
            <span data-connected-id="<?php echo $connected_id; ?>" style="color: #a00;"><?php printf( __( '%s was removed', 'gamipress' ), $connected_label ); ?></span>
        <?php endif; ?>
    <?php else : ?>
        <span style="color: #a00;"><?php printf( __( 'Missed %s', 'gamipress' ), $connected_label ); ?></span>
    <?php endif;
}
add_action('manage_posts_custom_column', 'gamipress_requirements_posts_custom_columns', 10, 2);