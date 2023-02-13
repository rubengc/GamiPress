<?php
/**
 * Logs
 *
 * @package     GamiPress\Custom_Tables\Logs
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.2.8
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Parse query args for logs
 *
 * @since   1.2.8
 * @updated 1.6.5 Added support to before and after query vars
 *
 * @param string $where
 * @param CT_Query $ct_query
 *
 * @return string
 */
function gamipress_logs_query_where( $where, $ct_query ) {

    global $ct_table;

    if( $ct_table->name !== 'gamipress_logs' ) {
        return $where;
    }

    $table_name = $ct_table->db->table_name;

    // Shorthand
    $qv = $ct_query->query_vars;

    // Type
    $where .= gamipress_custom_table_where( $qv, 'type', 'type', 'string' );

    // Trigger Type
    $where .= gamipress_custom_table_where( $qv, 'trigger_type', 'trigger_type', 'string' );

    // Access
    $where .= gamipress_custom_table_where( $qv, 'access', 'access', 'string' );

    // User ID
    $where .= gamipress_custom_table_where( $qv, 'user_id', 'user_id', 'integer' );

    // Include
    $where .= gamipress_custom_table_where( $qv, 'log__in', 'log_id', 'integer' );

    // Exclude
    $where .= gamipress_custom_table_where( $qv, 'log__not_in', 'log_id', 'integer', '!=', 'NOT IN' );

    // Before
    if( isset( $qv['before'] ) ) {

        $before = $qv['before'];

        // Turn a string date into time
        if( gettype( $qv['before'] ) === 'string' ) {
            $before = strtotime( $qv['before'] );
        }

        if( $before > 0 ) {
            $before = date( 'Y-m-d H:i:s', $before );
            $where .= " AND {$table_name}.date < '{$before}'";
        }

    }

    // After
    if( isset( $qv['after'] ) ) {

        $after = $qv['after'];

        // Turn a string date into time
        if( gettype( $qv['after'] ) === 'string' ) {
            $after = strtotime( $qv['after'] );
        }

        if( $after > 0 ) {
            $after = date( 'Y-m-d H:i:s', $after );
            $where .= " AND {$table_name}.date > '{$after}'";
        }

    }

    return $where;
}
add_filter( 'ct_query_where', 'gamipress_logs_query_where', 10, 2 );

/**
 * Define the search fields for logs
 *
 * @since 1.6.4.2
 *
 * @param array $search_fields
 *
 * @return array
 */
function gamipress_logs_search_fields( $search_fields ) {

    $search_fields[] = 'title';

    return $search_fields;

}
add_filter( 'ct_query_gamipress_logs_search_fields', 'gamipress_logs_search_fields' );

/**
 * Parse search query args for logs
 *
 * @since 1.3.9.7
 *
 * @param string $search
 * @param CT_Query $ct_query
 *
 * @return string
 */
function gamipress_logs_query_search( $search, $ct_query ) {

    global $ct_table, $wpdb;

    if( $ct_table->name !== 'gamipress_logs' ) {
        return $search;
    }

    $table_name = $ct_table->db->table_name;

    // Shorthand
    $qv = $ct_query->query_vars;

    // Check if is search and query is not filtered by an specific user
    if( isset( $qv['s'] ) && ! empty( $qv['s'] ) && ! isset( $qv['user_id'] ) ) {

        // Made a user sub-search to retrieve them
        $users = get_users( array(
            'search' => sprintf( '*%s*', $qv['s'] ),
            'search_columns' => array(
                'user_login',
                'user_email',
                'display_name',
            ),
            'fields' => 'ID',
        ) );

        if( ! empty( $users ) ) {
            $search .= " AND ( {$table_name}.user_id IN (" . implode( ',', array_map( 'absint', $users ) ) . ") )";
        }

    }

    return $search;

}
add_filter( 'ct_query_search', 'gamipress_logs_query_search', 10, 2 );

/**
 * Define the field for logs views
 *
 * @since 1.8.3
 *
 * @param string $field_id
 *
 * @return string
 */
function gamipress_logs_views_field( $field_id = '' ) {
    return 'type';
}
add_filter( 'ct_list_gamipress_logs_views_field', 'gamipress_logs_views_field' );

/**
 * Define the field labels for logs views
 *
 * @since 1.8.3
 *
 * @param array $field_labels
 *
 * @return array
 */
function gamipress_logs_views_field_labels( $field_labels = array() ) {
    return gamipress_get_log_types();
}
add_filter( 'ct_list_gamipress_logs_views_field_labels', 'gamipress_logs_views_field_labels' );

/**
 * Columns for logs list view
 *
 * @since 1.2.8
 *
 * @param array $columns
 *
 * @return array
 */
function gamipress_manage_logs_columns( $columns = array() ) {

    $columns['title']       = __( 'Title', 'gamipress' );
    $columns['type']        = __( 'Type', 'gamipress' );
    $columns['user_id']     = __( 'User', 'gamipress' );
    $columns['post_id']     = __( 'Post', 'gamipress' );
    $columns['admin_id']    = __( 'Administrator', 'gamipress' );
    $columns['date']        = __( 'Date', 'gamipress' );

    return $columns;
}
add_filter( 'manage_gamipress_logs_columns', 'gamipress_manage_logs_columns' );

/**
 * Sortable columns for logs list view
 *
 * @since 1.6.7
 *
 * @param array $sortable_columns
 *
 * @return array
 */
function gamipress_manage_logs_sortable_columns( $sortable_columns ) {

    $sortable_columns['title']      = array( 'title', false );
    $sortable_columns['type']       = array( 'type', false );
    $sortable_columns['user_id']    = array( 'user_id', false );
    $sortable_columns['post_id']    = array( 'post_id', false );
    $sortable_columns['admin_id']   = array( 'admin_id', false );
    $sortable_columns['date']       = array( 'date', true );

    return $sortable_columns;

}
add_filter( 'manage_gamipress_logs_sortable_columns', 'gamipress_manage_logs_sortable_columns' );

/**
 * Columns rendering for logs list view
 *
 * @since  1.2.8
 *
 * @param string $column_name
 * @param integer $object_id
 */
function gamipress_manage_logs_custom_column(  $column_name, $object_id ) {

    // Setup vars
    $log = ct_get_object( $object_id );

    switch( $column_name ) {
        case 'title':
            ?>

            <strong>
                <a href="<?php echo ct_get_edit_link( 'gamipress_logs', $log->log_id ); ?>"><?php echo apply_filters( 'gamipress_render_log_title', $log->title, $log->log_id ); ?></a> - <span class="post-state"><?php echo $log->access === 'public' ? __( 'Public', 'gamipress' ) : __( 'Private', 'gamipress' ); ?></span>
            </strong>

            <?php
            break;
        case 'type':
            $log_types = gamipress_get_log_types();

            $post_id = ct_get_object_meta( $object_id, '_gamipress_achievement_id', true );
            $post_type = gamipress_get_post_type( $post_id );

            if( $log->type === 'achievement_earn' || $log->type === 'achievement_award' && in_array( $post_type, gamipress_get_requirement_types_slugs() ) ) {

                switch( $post_type ) {
                    case 'step':
                        if( $log->type === 'achievement_earn' ) {
                            echo __( 'Achievement Step Earn', 'gamipress' );
                        } else {
                            echo __( 'Achievement Step Award', 'gamipress' );
                        }
                        break;
                    case 'rank-requirement':
                        if( $log->type === 'achievement_earn' ) {
                            echo __( 'Rank Requirement Earn', 'gamipress' );
                        } else {
                            echo __( 'Rank Requirement Award', 'gamipress' );
                        }
                        break;
                    default:
                        echo isset( $log_types[$log->type] ) ? $log_types[$log->type] : $log->type;
                        break;
                }

            } else {
                echo isset( $log_types[$log->type] ) ? $log_types[$log->type] : $log->type;
            }

            break;
        case 'user_id':
            $user = get_userdata( $log->user_id );

            if( $user ) :

                if( current_user_can( 'edit_users' ) ) {
                    ?>

                    <a href="<?php echo get_edit_user_link( $log->user_id ); ?>"><?php echo $user->display_name . ' (' . $user->user_login . ')'; ?></a>

                    <?php
                } else {
                    echo $user->display_name;
                }

            endif;
            break;
        case 'post_id':

            $trigger = ct_get_object_meta( $object_id, '_gamipress_trigger_type', true );

            if( in_array( $trigger, array_keys( gamipress_get_specific_activity_triggers() ) ) ) :
                // Is is an specific activity trigger, then will get a post assigned

                $post_id = ct_get_object_meta($object_id, '_gamipress_achievement_post', true);
                $site_id = ct_get_object_meta($object_id, '_gamipress_achievement_post_site_id', true);

                if( $post_id ) :

                    $post_title = gamipress_get_specific_activity_trigger_post_title( $post_id, $trigger, $site_id );

                    if( current_user_can( 'edit_post', $post_id ) ) : ?>

                        <a href="<?php echo get_edit_post_link( $post_id ); ?>"><?php echo $post_title; ?></a>

                    <?php else :

                        echo $post_title;

                    endif;

                endif;

            else :
                // Also, some activity triggers stores a post ID (for example, comments and visits)

                $post_id = ct_get_object_meta($object_id, '_gamipress_post_id', true);

                if( $post_id ) :

                    $post_title = gamipress_get_specific_activity_trigger_post_title( $post_id, $trigger, get_current_blog_id() );

                    if( current_user_can( 'edit_post', $post_id ) ) : ?>

                        <a href="<?php echo get_edit_post_link( $post_id ); ?>"><?php echo $post_title; ?></a>

                    <?php else :

                        echo $post_title;

                    endif;

                endif;

            endif;

            break;
        case 'admin_id':

            if( in_array( $log->type, array( 'achievement_award', 'points_award', 'points_revoke', 'rank_award' ) ) ) :

                $admin_id = ct_get_object_meta( $object_id, '_gamipress_admin_id', true );
                $admin = get_userdata( $admin_id );

                if( $admin ) :

                    if( current_user_can( 'edit_users' ) ) {
                        ?>

                        <a href="<?php echo get_edit_user_link( $admin_id ); ?>"><?php echo $admin->display_name . ' (' . $admin->user_login . ')'; ?></a>

                        <?php
                    } else {
                        echo $admin->display_name;
                    }

                endif;

            endif;

            break;
        case 'date':
            ?>

            <abbr title="<?php echo date( 'Y/m/d g:i:s a', strtotime( $log->date ) ); ?>"><?php echo date( 'Y/m/d', strtotime( $log->date ) ); ?></abbr>

            <?php
            break;
    }
}
add_action( 'manage_gamipress_logs_custom_column', 'gamipress_manage_logs_custom_column', 10, 2 );

/**
 * Remove submit div box on logs
 *
 * @since  1.2.8
 */
function gamipress_add_logs_meta_boxes() {
    remove_meta_box( 'submitdiv', 'gamipress_logs', 'side' );
}
add_action( 'add_meta_boxes', 'gamipress_add_logs_meta_boxes' );