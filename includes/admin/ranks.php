<?php
/**
 * Admin Ranks
 *
 * @package     GamiPress\Admin\Ranks
 * @since       1.3.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function gamipress_load_ranks_admin_hooks() {

    foreach( gamipress_get_rank_types() as $slug => $rank ) {
        add_filter( "manage_{$slug}_posts_columns", 'gamipress_rank_posts_columns' );
        add_filter( "manage_edit-{$slug}_sortable_columns", 'gamipress_rank_sortable_columns' );
        add_action( "manage_{$slug}_posts_custom_column", 'gamipress_rank_posts_custom_columns', 10, 2 );
    }

}
add_action( 'admin_init', 'gamipress_load_ranks_admin_hooks' );

/**
 * Register our custom ranks columns
 *
 * @since 1.3.1
 *
 * @param $posts_columns
 *
 * @return mixed
 */
function gamipress_rank_posts_columns( $posts_columns ) {

    $posts_columns['title'] = __( 'Name', 'gamipress' );
    $posts_columns['priority'] = __( 'Priority', 'gamipress' );

    unset( $posts_columns['date'] );
    unset( $posts_columns['author'] );

    return $posts_columns;

}

/**
 * Set ranks sortable columns
 *
 * @since 1.3.1
 *
 * @param $sortable_columns
 *
 * @return mixed
 */
function gamipress_rank_sortable_columns( $sortable_columns ) {

    $sortable_columns['priority'] = 'priority';

    return $sortable_columns;

}

/**
 * Output for our custom ranks columns
 *
 * @since 1.3.1
 *
 * @param $column_name
 * @param $post_id
 */
function gamipress_rank_posts_custom_columns( $column_name, $post_id ) {

    switch( $column_name ) {
        case 'priority':
            echo get_post_field( 'menu_order', $post_id );
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

function gamipress_next_rank_content_cb( $field, $object_id, $object_type ) {

    // Bail if is an auto-draft rank
    if( get_post_field( 'post_status', $object_id ) === 'auto-draft' ) {
        return;
    }

    $rank = gamipress_get_next_rank( $object_id );
    $rank_type = get_post_type( $object_id );

    if( $rank ) : ?>
        <div class="cmb-th">
            <label><?php echo sprintf( __( 'Next %s', 'gamipress' ), gamipress_get_rank_type_singular( $rank_type ) ); ?></label>
        </div>
        <p>
            <?php echo $rank->post_title; ?>
            <a href="<?php echo get_edit_post_link( $rank->ID ); ?>"><?php _e( 'Edit' ); ?></a>
        </p>
    <?php endif;

}

function gamipress_prev_rank_content_cb( $field, $object_id, $object_type ) {

    // Bail if is an auto-draft rank
    if( get_post_field( 'post_status', $object_id ) === 'auto-draft' ) {
        return;
    }

    $rank = gamipress_get_prev_rank( $object_id );
    $rank_type = get_post_type( $object_id );

    if( $rank ) : ?>
        <div class="cmb-th">
            <label><?php echo sprintf( __( 'Previous %s', 'gamipress' ), gamipress_get_rank_type_singular( $rank_type ) ); ?></label>
        </div>
        <p>
            <?php echo $rank->post_title; ?>
            <a href="<?php echo get_edit_post_link( $rank->ID ); ?>"><?php _e( 'Edit' ); ?></a>
        </p>
    <?php endif;

}