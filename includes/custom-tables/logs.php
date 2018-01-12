<?php
/**
 * Logs
 *
 * @package     GamiPress\Custom_Tables\Logs
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
function gamipress_logs_query_where( $where, $ct_query ) {

    global $ct_table;

    if( $ct_table->name !== 'gamipress_logs' ) {
        return $where;
    }

    $table_name = $ct_table->db->table_name;

    // Type
    if( isset( $ct_query->query_vars['type'] ) && ! empty( $ct_query->query_vars['type'] ) ) {

        $type = $ct_query->query_vars['type'];

        if( is_array( $type ) ) {
            $type = "'" . implode( "', '", $type ) . "'";

            $where .= " AND {$table_name}.type IN ( {$type} )";
        } else {
            $where .= " AND {$table_name}.type = '{$type}'";
        }

    }

    // Access
    if( isset( $ct_query->query_vars['access'] ) && ! empty( $ct_query->query_vars['access'] ) ) {

        $access = $ct_query->query_vars['access'];

        if( is_array( $access ) ) {
            $access = "'" . implode( "', '", $access ) . "'";

            $where .= " AND {$table_name}.access IN ( {$access} )";
        } else {
            $where .= " AND {$table_name}.access = '{$access}'";
        }

    }

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

    // Include
    if( isset( $ct_query->query_vars['log__in'] ) && ! empty( $ct_query->query_vars['log__in'] ) ) {

        if( is_array( $ct_query->query_vars['log__in'] ) ) {
            $include = implode( ", ", $ct_query->query_vars['log__in'] );
        } else {
            $include = $ct_query->query_vars['log__in'];
        }

        if( ! empty( $include ) ) {
            $where .= " AND {$table_name}.log_id IN ( {$include} )";
        }
    }

    // Exclude
    if( isset( $ct_query->query_vars['log__not_in'] ) && ! empty( $ct_query->query_vars['log__not_in'] ) ) {

        if( is_array( $ct_query->query_vars['log__not_in'] ) ) {
            $exclude = implode( ", ", $ct_query->query_vars['log__not_in'] );
        } else {
            $exclude = $ct_query->query_vars['log__not_in'];
        }

        if( ! empty( $exclude ) ) {
            $where .= " AND {$table_name}.log_id NOT IN ( {$exclude} )";
        }
    }

    return $where;
}
add_filter( 'ct_query_where', 'gamipress_logs_query_where', 10, 2 );

/**
 * Columns for logs list view
 *
 * @since  1.2.8
 *
 * @param array $columns
 *
 * @return array
 */
function gamipress_manage_logs_columns( $columns = array() ) {

    $columns['title']       = __( 'Title', 'gamipress' );
    $columns['type']        = __( 'Type', 'gamipress' );
    $columns['user_id']     = __( 'User', 'gamipress' );
    $columns['admin_id']    = __( 'Administrator', 'gamipress' );
    $columns['date']        = __( 'Date', 'gamipress' );

    return $columns;
}
add_filter( 'manage_gamipress_logs_columns', 'gamipress_manage_logs_columns' );

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

            echo isset( $log_types[$log->type] ) ? $log_types[$log->type] : $log->type;

            break;
        case 'user_id':
            $user = get_userdata( $log->user_id );

            if( $user ) :

                if( current_user_can('edit_users')) {
                    ?>

                    <a href="<?php echo get_edit_user_link( $log->user_id ); ?>"><?php echo $user->display_name . ' (' . $user->user_login . ')'; ?></a>

                    <?php
                } else {
                    echo $user->display_name;
                }

            endif;
            break;
        case 'admin_id':

            if( in_array( $log->type, array( 'achievement_award', 'points_award', 'points_revoke', 'rank_award' ) ) ) :

                $admin_id = ct_get_object_meta( $object_id, '_gamipress_admin_id', true );
                $admin = get_userdata( $admin_id );

                if( $admin ) :

                    if( current_user_can('edit_users')) {
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

function gamipress_logs_edit_form_top( $object ) {

    global $ct_table;

    if( $ct_table->name !== 'gamipress_logs' ) {
        return;
    }

    ?>
    <div class="gamipress-log-title-preview">
        <h1><?php echo $object->title; ?></h1>
    </div>
    <?php
}
add_action( 'ct_edit_form_top', 'gamipress_logs_edit_form_top' );

// Remove submit div for logs
function gamipress_add_logs_meta_boxes() {
    remove_meta_box( 'submitdiv', 'gamipress_logs', 'side' );

}
add_action( 'add_meta_boxes', 'gamipress_add_logs_meta_boxes' );