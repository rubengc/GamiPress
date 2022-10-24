<?php
/**
 * Admin Ranks
 *
 * @package     GamiPress\Admin\Ranks
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.3.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register ranks related hooks
 *
 * @since 1.3.1
 */
function gamipress_load_ranks_admin_hooks() {

    foreach( gamipress_get_rank_types() as $slug => $rank ) {
        add_filter( "manage_{$slug}_posts_columns", 'gamipress_rank_posts_columns' );
        add_filter( "manage_edit-{$slug}_sortable_columns", 'gamipress_rank_sortable_columns' );
        add_action( "manage_{$slug}_posts_custom_column", 'gamipress_rank_posts_custom_columns', 10, 2 );
    }

}
add_action( 'admin_init', 'gamipress_load_ranks_admin_hooks' );

/**
 * Overrides the enter title here on rank edit screen
 *
 * @since 1.3.1
 *
 * @param $placeholder
 * @param $post
 *
 * @return string
 */
function gamipress_admin_enter_title_here( $placeholder, $post ) {

    if( gamipress_is_rank( $post->post_type ) ) {
        return __( 'Rank name', 'gamipress' );
    }

    return $placeholder;

}
add_filter( 'enter_title_here', 'gamipress_admin_enter_title_here', 10, 2 );

/**
 * Register our custom ranks columns
 *
 * @since   1.3.1
 * @updated 1.3.9.5 Added the thumbnail column
 *
 * @param array $posts_columns
 *
 * @return array
 */
function gamipress_rank_posts_columns( $posts_columns ) {

    $posts_columns['title'] = __( 'Name', 'gamipress' );

    unset( $posts_columns['author'] );

    // Prepend the thumbnail column
    $chunks                 = array_chunk( $posts_columns, 1, true );
    $chunks[0]['thumbnail'] = __( 'Image', 'gamipress' );

    $posts_columns = call_user_func_array( 'array_merge', $chunks );

    // Try to place our column before date column
    $pos = array_search( 'date', array_keys( $posts_columns ) );

    if ( ! is_int( $pos ) ) {
        $pos = 1;
    }

    // Place our column in our desired position
    $chunks                     = array_chunk( $posts_columns, $pos, true );
    $chunks[0]['priority']   = __( 'Priority', 'gamipress' );
    $chunks[0]['unlock_with_points']   = __( 'Unlock with Points', 'gamipress' );

    return call_user_func_array( 'array_merge', $chunks );

}

/**
 * Set ranks sortable columns
 *
 * @since 1.3.1
 *
 * @param array $sortable_columns
 *
 * @return array
 */
function gamipress_rank_sortable_columns( $sortable_columns ) {

    $sortable_columns['priority'] = 'priority';

    return $sortable_columns;

}

/**
 * Output for our custom ranks columns
 *
 * @since   1.3.1
 * @updated 1.3.9.5 Added the thumbnail column output
 *
 * @param string    $column_name
 * @param integer   $post_id
 */
function gamipress_rank_posts_custom_columns( $column_name, $post_id ) {

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
        case 'priority':

            echo absint( get_post_field( 'menu_order', $post_id ) );

            break;
        case 'unlock_with_points':

            $unlock_with_points = gamipress_get_post_meta( $post_id, $prefix . 'unlock_with_points' );

            if( ! (bool) $unlock_with_points ) {
                break;
            }

            // Setup points vars
            $points_type_to_unlock = gamipress_get_post_meta( $post_id, $prefix . 'points_type_to_unlock' );
            $points_to_unlock = absint( gamipress_get_post_meta( $post_id, $prefix . 'points_to_unlock' ) );

            if( $points_to_unlock !== 0 ) {
                echo gamipress_format_points( $points_to_unlock, $points_type_to_unlock );
            }

            break;
    }

}

/**
 * Filter query for ranks
 *
 * @since 1.3.1
 *
 * @param $query
 */
function gamipress_rank_pre_get_posts( $query ) {

    if( ! is_admin() ) {
        return;
    }

    if( ! gamipress_is_rank( $query->get( 'post_type') ) ) {
        return;
    }

    $orderby = $query->get( 'orderby' );

    // If order by is set to custom field, make it work, also set this order by as default when no order by has specified
    if( $orderby === 'priority' || empty( $orderby ) ) {
        $query->set( 'orderby', 'menu_order' );
    }

    // Override the default order by
    if( $orderby === 'menu_order title' ) {
        $query->set( 'orderby', 'menu_order' );
        $query->set( 'order', 'DESC' );
    }

}
add_action( 'pre_get_posts', 'gamipress_rank_pre_get_posts' );

// Function callback to render next rank on rank details box
function gamipress_next_rank_content_cb( $field, $object_id, $object_type ) {

    // Bail if is an auto-draft rank
    if( get_post_field( 'post_status', $object_id ) === 'auto-draft' ) {
        return;
    }

    $rank = gamipress_get_next_rank( $object_id );
    $rank_type = gamipress_get_post_type( $object_id );

    if( $rank ) : ?>
        <div class="cmb-th">
            <label><?php echo sprintf( __( 'Next %s', 'gamipress' ), gamipress_get_rank_type_singular( $rank_type, true ) ); ?></label>
        </div>
        <p>
            <?php echo $rank->post_title; ?>
            <a href="<?php echo get_edit_post_link( $rank->ID ); ?>"><?php _e( 'Edit' ); ?></a>
        </p>
    <?php endif;

}

// Function callback to render previous rank on rank details box
function gamipress_prev_rank_content_cb( $field, $object_id, $object_type ) {

    // Bail if is an auto-draft rank
    if( get_post_field( 'post_status', $object_id ) === 'auto-draft' ) {
        return;
    }

    $rank = gamipress_get_prev_rank( $object_id );
    $rank_type = gamipress_get_post_type( $object_id );

    if( $rank ) : ?>
        <div class="cmb-th">
            <label><?php echo sprintf( __( 'Previous %s', 'gamipress' ), gamipress_get_rank_type_singular( $rank_type, true ) ); ?></label>
        </div>
        <p>
            <?php echo $rank->post_title; ?>
            <a href="<?php echo get_edit_post_link( $rank->ID ); ?>"><?php _e( 'Edit' ); ?></a>
        </p>
    <?php endif;

}