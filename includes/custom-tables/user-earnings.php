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

    // Points Type
    if( isset( $ct_query->query_vars['points_type'] ) && ! empty( $ct_query->query_vars['points_type'] ) ) {

        $points_type = $ct_query->query_vars['points_type'];

        if( is_array( $points_type ) ) {
            $points_type = "'" . implode( "', '", $points_type ) . "'";

            $where .= " AND {$table_name}.points_type IN ( {$points_type} )";
        } else {
            $where .= " AND {$table_name}.points_type = '{$points_type}'";
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

    $columns['name']    = __( 'Name', 'gamipress' );
    $columns['date']    = __( 'Date', 'gamipress' );

    if( current_user_can( gamipress_get_manager_capability() ) ) {
        $columns['action']  = __( 'Action', 'gamipress' );
    }

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

    $can_manage = current_user_can( gamipress_get_manager_capability() );

    // Setup vars
    $requirement_types = gamipress_get_requirement_types();
    $achievement_types = gamipress_get_achievement_types();
    $rank_types = gamipress_get_rank_types();
    $achievement = ct_get_object( $object_id );

    switch( $column_name ) {
        case 'name':

            if( in_array( $achievement->post_type, gamipress_get_requirement_types_slugs() ) ) : ?>

                <?php if( $achievement->post_type === 'step' && $parent_achievement = gamipress_get_parent_of_achievement( $achievement->post_id ) ) : ?>

                    <?php // Step ?>

                    <?php // Achievement thumbnail ?>
                    <?php echo gamipress_get_achievement_post_thumbnail( $parent_achievement->ID, array( 32, 32 ) ); ?>

                    <?php // Step title ?>
                    <strong><?php echo get_the_title( $achievement->post_id ); ?></strong>

                    <?php // Step relationship details ?>
                    <?php echo ( isset( $requirement_types[$achievement->post_type] ) ? '<br>' . $requirement_types[$achievement->post_type]['singular_name'] : '' ); ?>
                    <?php echo ( isset( $achievement_types[$parent_achievement->post_type] ) ? ', ' . $achievement_types[$parent_achievement->post_type]['singular_name'] . ': ' : '' ); ?>
                    <?php echo '<a href="' . ( $can_manage ? get_edit_post_link( $parent_achievement->ID ) : get_post_permalink( $parent_achievement->ID ) ) . '">' . get_the_title( $parent_achievement->ID ) . '</a>'; ?>

                <?php elseif( $achievement->post_type === 'points-award' && $points_type = gamipress_get_points_award_points_type( $achievement->post_id ) ) : ?>

                    <?php // Points award ?>

                    <?php // Points type thumbnail ?>
                    <?php echo gamipress_get_points_type_thumbnail( $points_type->ID, array( 32, 32 ) ); ?>

                    <?php // Points award title ?>
                    <strong><?php echo get_the_title( $achievement->post_id ); ?></strong>

                    <?php // Points award relationship details ?>
                    <?php echo ( isset( $requirement_types[$achievement->post_type] ) ? '<br>' . $requirement_types[$achievement->post_type]['singular_name'] : '' ); ?>
                    <?php echo ', ' . ( $can_manage ? '<a href="' . get_edit_post_link( $points_type->ID ) . '">' . get_the_title( $points_type->ID ) . '</a>' : get_the_title( $points_type->ID ) ); ?>

                <?php elseif( $achievement->post_type === 'points-deduct' && $points_type = gamipress_get_points_deduct_points_type( $achievement->post_id ) ) : ?>

                    <?php // Points deduct ?>

                    <?php // Points type thumbnail ?>
                    <?php echo gamipress_get_points_type_thumbnail( $points_type->ID, array( 32, 32 ) ); ?>

                    <?php // Points deduct title ?>
                    <strong><?php echo get_the_title( $achievement->post_id ); ?></strong>

                    <?php // Points deduct relationship details ?>
                    <?php echo ( isset( $requirement_types[$achievement->post_type] ) ? '<br>' . $requirement_types[$achievement->post_type]['singular_name'] : '' ); ?>
                    <?php echo ', ' . ( $can_manage ? '<a href="' . get_edit_post_link( $points_type->ID ) . '">' . get_the_title( $points_type->ID ) . '</a>' : get_the_title( $points_type->ID ) ); ?>

                <?php elseif( $achievement->post_type === 'rank-requirement' && $rank = gamipress_get_rank_requirement_rank( $achievement->post_id ) ) : ?>

                    <?php // Rank Requirement ?>

                    <?php // Rank thumbnail ?>
                    <?php echo gamipress_get_rank_post_thumbnail( $rank->ID, array( 32, 32 ) ); ?>

                    <?php // Rank requirement title ?>
                    <strong><?php echo get_the_title( $achievement->post_id ); ?></strong>

                    <?php // Rank relationship details ?>
                    <?php echo ( isset( $requirement_types[$achievement->post_type] ) ? '<br>' . $requirement_types[$achievement->post_type]['singular_name'] : '' ); ?>
                    <?php echo ( isset( $rank_types[$rank->post_type] ) ? ', ' . $rank_types[$rank->post_type]['singular_name'] . ': ' : '' ); ?>
                    <?php echo '<a href="' . ( $can_manage ? get_edit_post_link( $rank->ID ) : get_post_permalink( $rank->ID ) ) . '">' . get_the_title( $rank->ID ) . '</a>'; ?>

                <?php endif; ?>

            <?php elseif( in_array( $achievement->post_type, gamipress_get_achievement_types_slugs() ) ) : ?>

                <?php // Achievement ?>

                <?php // Achievement thumbnail ?>
                <?php echo gamipress_get_achievement_post_thumbnail( $achievement->post_id, array( 32, 32 ) ); ?>

                <?php // Achievement title ?>
                <strong><?php echo '<a href="' . ( $can_manage ? get_edit_post_link( $achievement->post_id ) : get_post_permalink( $achievement->post_id ) ) . '">' . get_the_title( $achievement->post_id ) . '</a>'; ?></strong>
                <?php echo ( isset( $achievement_types[$achievement->post_type] ) ? '<br>' . $achievement_types[$achievement->post_type]['singular_name'] : '' ); ?>

            <?php elseif( in_array( $achievement->post_type, gamipress_get_rank_types_slugs() ) ) : ?>

                <?php // Rank ?>

                <?php // Rank thumbnail ?>
                <?php echo gamipress_get_rank_post_thumbnail( $achievement->post_id, array( 32, 32 ) ); ?>

                <?php // Rank title ?>
                <strong><?php echo '<a href="' . ( $can_manage ? get_edit_post_link( $achievement->post_id ) : get_post_permalink( $achievement->post_id ) ) . '">' . get_the_title( $achievement->post_id ) . '</a>'; ?></strong>
                <?php echo ( isset( $rank_types[$achievement->post_type] ) ? '<br>' . $rank_types[$achievement->post_type]['singular_name'] : '' ); ?>

            <?php endif; ?>

            <button type="button" class="toggle-row"><span class="screen-reader-text"><?php __( 'Show more details', 'gamipress' ); ?></span></button>

            <?php
            break;
        case 'date':
            $time = strtotime( $achievement->date );
            ?>

            <abbr title="<?php echo date( 'Y/m/d g:i:s a', $time ); ?>"><?php echo date( 'Y/m/d', $time ); ?></abbr>

            <?php
            break;
        case 'action':

            // If user is not manager then skip this
            if( ! $can_manage ) {
                break;
            }

            // Setup our revoke URL
            $revoke_url = add_query_arg( array(
                'action'         => 'revoke',
                'user_id'        => absint( $achievement->user_id ),
                'achievement_id' => absint( $achievement->post_id ),
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