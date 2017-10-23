<?php
/**
 * User Earnings
 *
 * @package     GamiPress\Custom_Tables\User_Earnings
 * @since       1.2.8
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Parse query args for user earnings
 *
 * @since  1.2.8
 *
 * @param string $where
 * @param CT_Query $ct_query
 *
 * @return string
 */
function gamipress_user_earnings_query_where( $where, $ct_query ) {

    global $ct_table;

    if( $ct_table->name !== 'gamipress_user_earnings' ) {
        return $where;
    }

    $table_name = $ct_table->db->table_name;

    // User ID
    if( isset( $ct_query->query_vars['user_id'] ) && absint( $ct_query->query_vars['user_id'] ) !== 0 ) {

        $user_id = $ct_query->query_vars['user_id'];

        if( is_array( $user_id ) ) {
            $user_id = implode( ", ", $user_id );

            $where .= " AND {$table_name}.user_id IN ( {$user_id} )";
        } else {
            $where .= " AND {$table_name}.user_id = {$user_id}";
        }
    }

    // Post ID
    if( isset( $ct_query->query_vars['post_id'] ) && absint( $ct_query->query_vars['post_id'] ) !== 0 ) {

        $post_id = $ct_query->query_vars['post_id'];

        if( is_array( $post_id ) ) {
            $post_id = implode( ", ", $post_id );

            $where .= " AND {$table_name}.post_id IN ( {$post_id} )";
        } else {
            $where .= " AND {$table_name}.post_id = {$post_id}";
        }
    }

    // Post Type
    if( isset( $ct_query->query_vars['post_type'] ) && ! empty( $ct_query->query_vars['post_type'] ) ) {

        $post_type = $ct_query->query_vars['post_type'];

        if( is_array( $post_type ) ) {
            $post_type = "'" . implode( "', '", $post_type ) . "'";

            $where .= " AND {$table_name}.post_type IN ( {$post_type} )";
        } else {
            $where .= " AND {$table_name}.post_type = '{$post_type}'";
        }

    }

    // Since
    if( isset( $ct_query->query_vars['since'] ) && absint( $ct_query->query_vars['since'] ) > 0 ) {

        $since = date( 'Y-m-d H:i:s', $ct_query->query_vars['since'] );

        $where .= " AND {$table_name}.date > '{$since}'";
    }

    return $where;
}
add_filter( 'ct_query_where', 'gamipress_user_earnings_query_where', 10, 2 );

/**
 * Columns for user earnings list view
 *
 * @since  1.2.8
 *
 * @param array $columns
 *
 * @return array
 */
function gamipress_manage_user_earnings_columns( $columns = array() ) {

    $columns['image']   = __( 'Image', 'gamipress' );
    $columns['name']    = __( 'Name', 'gamipress' );
    $columns['date']    = __( 'Date', 'gamipress' );
    $columns['action']  = __( 'Action', 'gamipress' );

    return $columns;
}
add_filter( 'manage_gamipress_user_earnings_columns', 'gamipress_manage_user_earnings_columns' );

/**
 * Columns rendering for user earnings list view
 *
 * @since  1.2.8
 *
 * @param string $column_name
 * @param integer $object_id
 */
function gamipress_manage_user_earnings_custom_column(  $column_name, $object_id ) {

    // Setup vars
    $achievement_types = gamipress_get_achievement_types();
    $achievement = ct_get_object( $object_id );

    // Backward compatibility
    $achievement->ID = $achievement->post_id;
    $achievement->date_earned = strtotime( $achievement->date );
    $user_id = $achievement->user_id;

    switch( $column_name ) {
        case 'image':

            echo gamipress_get_achievement_post_thumbnail( $achievement->ID, array( 50, 50 ) );

            break;
        case 'name':

            if( $achievement->post_type === 'step' || $achievement->post_type === 'points-award' ) : ?>

                <strong><?php echo get_the_title( $achievement->ID ); ?></strong>
                <?php echo ( isset( $achievement_types[$achievement->post_type] ) ? '<br>' . $achievement_types[$achievement->post_type]['singular_name'] : '' ); ?>

                <?php // Output parent achievement
                if( $parent_achievement = gamipress_get_parent_of_achievement( $achievement->ID ) ) : ?>

                    <?php echo ( isset( $achievement_types[$parent_achievement->post_type] ) ? ', ' . $achievement_types[$parent_achievement->post_type]['singular_name'] . ': ' : '' ); ?>
                    <?php echo '<a href="' . get_edit_post_link( $parent_achievement->ID ) . '">' . get_the_title( $parent_achievement->ID ) . '</a>'; ?>

                <?php elseif( $points_type = gamipress_get_points_award_points_type( $achievement->ID ) ) : ?>

                    <?php echo ', <a href="' . get_edit_post_link( $points_type->ID ) . '">' . get_the_title( $points_type->ID ) . '</a>'; ?>

                <?php endif; ?>

            <?php else : ?>

                <strong><?php echo '<a href="' . get_edit_post_link( $achievement->ID ) . '">' . get_the_title( $achievement->ID ) . '</a>'; ?></strong>
                <?php echo ( isset( $achievement_types[$achievement->post_type] ) ? '<br>' . $achievement_types[$achievement->post_type]['singular_name'] : '' ); ?>

            <?php endif;

            break;
        case 'date':
            ?>

            <abbr title="<?php echo date( 'Y/m/d g:i:s a', $achievement->date_earned ); ?>"><?php echo date( 'Y/m/d', $achievement->date_earned ); ?></abbr>

            <?php
            break;
        case 'action':
            // Setup our revoke URL
            $revoke_url = add_query_arg( array(
                'action'         => 'revoke',
                'user_id'        => absint( $user_id ),
                'achievement_id' => absint( $achievement->ID ),
                'user_earning_id' => absint( $achievement->user_earning_id ),
            ) );
            ?>

            <span class="delete"><a class="error" href="<?php echo esc_url( wp_nonce_url( $revoke_url, 'gamipress_revoke_achievement' ) ); ?>"><?php _e( 'Revoke Award', 'gamipress' ); ?></a></span>

            <?php
            break;
    }
}
add_action( 'manage_gamipress_user_earnings_custom_column', 'gamipress_manage_user_earnings_custom_column', 10, 2 );

// Remove row actions on user earnings table
function gamipress_user_earnings_row_actions( $row_actions, $object ) {
    return array();
}
add_filter( 'gamipress_user_earnings_row_actions', 'gamipress_user_earnings_row_actions', 10, 2 );