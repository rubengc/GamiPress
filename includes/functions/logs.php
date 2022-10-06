<?php
/**
 * Logs Functions
 *
 * @package     GamiPress\Logs_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get an array of log types
 *
 * @since  1.0.0

 * @return array The registered logs types
 */
function gamipress_get_log_types() {

    return apply_filters( 'gamipress_logs_types', array(
        'event_trigger' => __( 'Event Trigger', 'gamipress' ),
        'achievement_earn' => __( 'Achievement Earn', 'gamipress' ),
        'achievement_award' => __( 'Achievement Award', 'gamipress' ),
        'points_earn' => __( 'Points Earn', 'gamipress' ),
        'points_deduct' => __( 'Points Deduction', 'gamipress' ),
        'points_expend' => __( 'Points Expend', 'gamipress' ),
        'points_award' => __( 'Points Award', 'gamipress' ),
        'points_revoke' => __( 'Points Revoke', 'gamipress' ),
        'rank_earn' => __( 'Rank Earn', 'gamipress' ),
        'rank_award' => __( 'Rank Award', 'gamipress' ),
    ) );

}

/**
 * Get an array of log pattern tags
 *
 * @since   1.0.2
 * @updated 1.3.7 Added the content parameter
 * @updated 1.8.2 Added the {trigger_type_key} tag
 *
 * @param string $context
 *
 * @return array The registered log pattern tags
 */
function gamipress_get_log_pattern_tags( $context = 'default' ) {

    return apply_filters( 'gamipress_log_pattern_tags', array(
        '{user}'                =>  __( 'User assigned.', 'gamipress' ),
        '{admin}'               =>  __( 'Admin that awards.', 'gamipress' ),
        '{achievement}'         =>  __( 'Achievement user has earned.', 'gamipress' ),
        '{achievement_type}'    =>  __( 'Type of the achievement earned.', 'gamipress' ),
        '{trigger_type}'        =>  __( 'Event triggered label.', 'gamipress' ),
        '{trigger_type_key}'    =>  __( 'Event triggered internal key.', 'gamipress' ),
        '{count}'               =>  __( 'Times user triggered this event.', 'gamipress' ),
        '{points}'              =>  __( 'Points user has earned.', 'gamipress' ),
        '{points_type}'         =>  __( 'Type of the points earned.', 'gamipress' ),
        '{total_points}'        =>  __( 'Points user has earned until this log.', 'gamipress' ),
        '{rank}'                =>  __( 'Rank user has ranked.', 'gamipress' ),
        '{rank_type}'           =>  __( 'Rank type user has ranked.', 'gamipress' ),
        '{site_title}'          =>  __( 'Site name.', 'gamipress' ),
    ), $context );

}

/**
 * Get an array of log pattern tags based on custom context
 *
 * @since   1.3.7
 *
 * @param array     $tags       The registered log pattern tags
 * @param string    $context    The context

 * @return array                Custom log pattern tags by context
 */
function gamipress_get_log_pattern_tags_by_context( $tags, $context = 'default' ) {

    switch( $context ) {
        case 'deduct':
            $tags['{points}'] = __( 'Points user has lost.', 'gamipress' );
            $tags['{points_type}'] =  __( 'Type of the points lost.', 'gamipress' );
            break;
        case 'expend':
            $tags['{points}'] = __( 'Points user has expended.', 'gamipress' );
            $tags['{points_type}'] =  __( 'Type of the points expended.', 'gamipress' );
            break;
    }

    return $tags;

}
add_filter( 'gamipress_log_pattern_tags', 'gamipress_get_log_pattern_tags_by_context', 10, 2 );

/**
 * Get a string with the desired log pattern tags html markup
 *
 * @since  1.0.2
 *
 * @param array $specific_tags
 * @param string $context
 *
 * @return string Log pattern tags html markup
 */
function gamipress_get_log_pattern_tags_html( $specific_tags = array(), $context = 'default' ) {

    $output = '<ul class="gamipress-log-pattern-tags-list">';

    foreach( gamipress_get_log_pattern_tags( $context ) as $tag => $description ) {

        if( ! empty( $specific_tags ) && ! in_array( $tag, $specific_tags ) ) {
            continue;
        }

        $attr_id = 'tag-' . str_replace( array( '{', '}', '_' ), array( '', '', '-' ), $tag );

        $output .= "<li id='{$attr_id}'><code>{$tag}</code> - {$description}</li>";
    }

    $output .= '</ul>';

    return $output;

}

/**
 * Helper function to easily query logs and centralize code of multiples log query functions
 *
 * @since   1.6.0
 * @updated 1.6.9 Added 'where' and 'get_var' arguments, improvements on query args processing and support to where definition in format: array( 'key' =>'key', 'value' =>'value', 'compare' =>'compare' )
 * @updated 1.7.6 Added support for array selects, to group_by parameter and the output parameter
 *
 * @param array $args
 *
 * @return mixed
 */
function gamipress_query_logs( $args ) {

    global $wpdb;

    $logs 		= GamiPress()->db->logs;
    $logs_meta 	= GamiPress()->db->logs_meta;

    $args = wp_parse_args( $args, array(
        'select'            => 'l.id',  // You can pass 'l.*', 'l.{field}', 'COUNT(*)' or an array as select
        'where'      	    => array(), // Supported formats: 'key' =>'value' | array( 'key'=>'key', 'value'=>'value', 'compare'=>'compare', 'type'=>'type' )
        'date_query'      	=> array(), // Supports before and after parameters
        'user_id'           => 0,
        'group_by'          => '',
        'order_by'          => 'l.date',
        'order'             => 'DESC',
        'limit'             => 0,
        'since'             => 0,
        'get_var'           => false,   // Force the use of get_var() function
        'output'            => OBJECT,  // Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.

        // Deprecated
        'meta'      	    => array(),
    ) );

    // Backward compatibility
    if( isset( $args['meta'] ) && count( $args['meta'] ) ) {
        $args['where'] = array_merge( $args['where'], $args['meta'] );
    }

    $log_fields = array(
        'log_id',
        'title',
        'type',
        'trigger_type',
        'access',
        'user_id',
        'date'
    );

    // Initialize query definitions
    $select     = array();
    $joins      = array();
    $where      = array();
    $query_args = array();

    // Select
    if( is_array( $args['select'] ) ) {

        foreach( $args['select'] as $key => $value ) {

            if( isset( $value['field'] ) ) {
                $field = $value['field'];
            } else {
                $field = $key;
            }

            if( in_array( $field, $log_fields ) ) {
                $get_key = "l.{$field}";
            } else {

                $index = count( $joins );

                $get_key = "lm{$index}.meta_value";
                $joins[] = "INNER JOIN {$logs_meta} AS lm{$index} ON ( lm{$index}.log_id = l.log_id AND lm{$index}.meta_key = %s )";
                $query_args[] = $field;
            }

            if ( isset( $value['function'] ) && $value['function'] ) {
                $get = "{$value['function']}({$get_key})";
            } else if ( isset( $value['cast'] ) && $value['cast'] ) {
                $get = "CAST({$get_key} AS {$value['cast']})";
            } else {
                $get = "{$get_key}";
            }

            $select[] = "{$get} as `{$key}`";

        }

        $args['select'] = ( ! empty( $select ) ? implode( ', ', $select ) : '' );
    }

    // User ID
    if( absint( $args['user_id'] ) !== 0 ) {

        $where[] = "l.user_id = %s";

        $query_args[] = $args['user_id'];

    }

    // Where

    if( isset( $args['where'] ) && is_array( $args['where'] ) ) {

        // Loop all log meta to build the where clause
        foreach ( $args['where'] as $key => $value ) {

            // Field key
            if( is_array( $value ) && isset( $value['key'] ) ) {
                $field = $value['key'];
            } else {
                $field = $key;
            }

            // Field value
            if( is_array( $value ) && isset( $value['value'] ) ) {
                $field_value = $value['value'];
            } else {
                $field_value = $value;
            }

            // Field compare
            if( is_array( $field_value ) ) {
                $default_compare = 'IN';
            } else {
                $default_compare = '=';
            }

            $compare = ( is_array( $value ) && isset( $value['compare'] ) ? $value['compare'] : $default_compare );

            // Field type
            $default_type = 'string';

            $type = ( is_array( $value ) && isset( $value['type'] ) ? $value['type'] : $default_type );

            // Parse common types to an unique one type
            switch( $type ) {
                case 'int':
                case 'integer':
                case 'number':
                case 'numeric':
                    $type = 'integer';
                    break;
                case 'text':
                case 'string':
                case 'char':
                case 'varchar':
                default:
                    $type = 'string';
                    break;
            }

            if( in_array( $field, $log_fields ) ) {

                // Query log field

                if( is_array( $field_value ) ) {

                    if( $type === 'integer' ) {
                        $field_value = implode( ", ", $field_value );
                    } else {
                        $field_value = "'" . implode( "', '", $field_value ) . "'";
                    }

                    $where[] = "l.{$field} {$compare} ({$field_value})";
                } else {

                    if( $type === 'integer' ) {
                        $where[] = "l.{$field} {$compare} %d";
                    } else {
                        $where[] = "l.{$field} {$compare} %s";
                    }

                    $query_args[] = $field_value;
                }

            } else {

                // Query log meta

                $index = count( $joins );

                $meta_key = '_gamipress_' . sanitize_key( $field );

                // Setup query definitions
                $joins[] = "LEFT JOIN {$logs_meta} AS lm{$index} ON ( l.log_id = lm{$index}.log_id AND lm{$index}.meta_key = '{$meta_key}' )";

                // If meta value is an array then need to compare using IN operator
                if( is_array( $field_value ) ) {

                    if( $type === 'integer' ) {
                        $field_value = implode( ", ", $field_value );
                    } else {
                        $field_value = "'" . implode( "', '", $field_value ) . "'";
                    }

                    $where[] = "lm{$index}.meta_value {$compare} ({$field_value})";

                } else {
                    // Meta query in format: 'key' =>'value'
                    if( $type === 'integer' ) {
                        $where[] = "lm{$index}.meta_value {$compare} %d";
                    } else {
                        $where[] = "lm{$index}.meta_value {$compare} %s";
                    }

                    $query_args[] = $field_value;
                }

            }

        }

    }

    // Date query before
    if( isset( $args['date_query']['before'] ) && ! empty( $args['date_query']['before'] ) ) {
        $where[] = "l.date <= %s";
        $query_args[] = $args['date_query']['before'];
    }

    // Date query after
    if( isset( $args['date_query']['after'] ) && ! empty( $args['date_query']['after'] ) ) {
        $where[] = "l.date >= %s";
        $query_args[] = $args['date_query']['after'];
    }

    // Since
    if( absint( $args['since'] ) > 0 ) {
        $date = date( 'Y-m-d H:i:s', $args['since'] );

        $where[] = "l.date >= %s";
        $query_args[] = $date;
    }

    // Group By
    $group_by = '';

    if( ! empty( $args['group_by'] ) ) {
        $group_by = 'GROUP BY ' . $args['group_by'];
    }

    // Order By
    $order_by = '';

    if( ! empty( $args['order_by'] ) && ! empty( $args['order'] ) ) {
        $order_by = 'ORDER BY ' . $args['order_by'] . ' ' . $args['order'];
    }

    // Turn arrays into strings
    $select = $args['select'];
    $joins = implode( ' ', $joins );
    $where = ( ! empty( $where ) ? 'WHERE ( ' . implode( ' ) AND ( ', $where ) . ' ) ' : '' );
    $limit = ( absint( $args['limit'] ) !== 0 ? 'LIMIT ' . absint( $args['limit'] ) : '' );

    // Prepare the query SQL
    $sql = $wpdb->prepare(
        "SELECT {$select}
         FROM {$logs} AS l
         {$joins}
         {$where}
         {$group_by}
         {$order_by}
         {$limit}",
        $query_args
    );

    if( strtoupper( $select ) === 'COUNT(*)' || $args['get_var'] ) {
        // If is a count, ensure return an integer
        $logs = absint( $wpdb->get_var( $sql ) );
    } else {
        $logs = $wpdb->get_results( $sql, $args['output'] );
    }

    return $logs;

}

/**
 * Return user logs with the specified meta data
 *
 * @since  1.3.7
 * @updated 1.3.9.6 Added $since parameter
 * @updated 1.3.9.8 Improvements on since checks
 * @updated 1.6.0   Code moved to gamipress_query_logs() function
 *
 * @param int       $user_id
 * @param array     $where
 * @param int       $since
 *
 * @return array
 */
function gamipress_get_user_logs( $user_id = 0, $where = array(), $since = 0 ) {

    return gamipress_query_logs( array(
        'select' => 'l.*',
        'user_id' => $user_id,
        'where' => $where,
        'since' => $since
    ) );

}

/**
 * Return count of user logs with the specified meta data
 *
 * @since   1.1.8
 * @updated 1.3.7 Added support for multiples types
 * @updated 1.4.2 Added $since parameter
 * @updated 1.6.0 Code moved to gamipress_query_logs() function
 *
 * @param int       $user_id
 * @param array     $where
 *
 * @return int
 */
function gamipress_get_user_log_count( $user_id = 0, $where = array(), $since = 0 ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {
        return gamipress_get_user_log_count_old( $user_id, $where );
    }

    return gamipress_query_logs( array(
        'select' => 'COUNT(*)',
        'user_id' => $user_id,
        'where' => $where,
        'since' => $since
    ) );

}

/**
 * Return the last user log with the specified meta data
 *
 * @since  1.4.2
 *
 * @param int       $user_id
 * @param array     $where
 *
 * @return stdClass|false
 */
function gamipress_get_user_last_log( $user_id = 0, $where = array() ) {

    $user_logs = gamipress_query_logs( array(
        'select' => 'l.*',
        'user_id' => $user_id,
        'where' => $where,
        'limit' => 1
    ) );

    return ( is_array( $user_logs ) && isset( $user_logs[0] ) ? $user_logs[0] : false );

}

/**
 * Posts a log entry when a user unlocks any achievement post
 *
 * @since   1.0.0
 * @updated 1.2.8 Added $type parameter
 * @updated 1.4.7 Added $trigger_type parameter
 *
 * @param  string   $type  	        The log type
 * @param  int      $user_id  	    The user ID
 * @param  string   $access         Access to this log ( public|private )
 * @param  string   $trigger_type   Access to this log ( public|private )
 * @param  array    $log_meta       Log meta data
 *
 * @return int             	        The log ID of the newly created log entry
 */
function gamipress_insert_log( $type = '', $user_id = 0, $access = 'public', $trigger_type = '', $log_meta = array() ) {

    global $wpdb;

    // Backward compatibility for functions that called it by the old way
    if( is_array( $trigger_type ) && empty( $log_meta ) ) {
        $log_meta = $trigger_type;
        $trigger_type = '';
    }

    // Trigger type is not a meta yet, so update it correctly
    if( isset( $log_meta['trigger_type'] ) ) {
        $trigger_type = $log_meta['trigger_type'];
        unset( $log_meta['trigger_type'] );
    }

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.4.7' ) ) {

        $log_meta['trigger_type'] = $trigger_type;

        return gamipress_insert_log_old_147( $type, $user_id, $access, $log_meta );
    }

    // Setup table
    $ct_table = ct_setup_table( 'gamipress_logs' );

    // Post data
    $log_data = array(
        'title'	        => '',
        'type' 	        => $type,
        'trigger_type' 	=> $trigger_type,
        'access'	    => $access,
        'user_id'	    => $user_id === 0 ? get_current_user_id() : absint( $user_id ),
        'date'	        => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
    );

    // Auto-generated post title
    $log_data['title'] = gamipress_parse_log_pattern( $log_meta['pattern'], $log_data, $log_meta );

    // Store log entry
    $log_id = $ct_table->db->insert( $log_data );

    // Store log meta data
    if ( $log_id && ! empty( $log_meta ) ) {

        $metas = array();

        foreach ( (array) $log_meta as $key => $value ) {
            // Sanitize vars
            $meta_key = '_gamipress_' . sanitize_key( $key );
            $meta_key = wp_unslash( $meta_key );
            $meta_value = wp_unslash( $value );
            $meta_value = esc_sql( $meta_value );
            $meta_value = sanitize_meta( $meta_key, $meta_value, $ct_table->name );
            $meta_value = maybe_serialize( $meta_value );

            // Setup the insert value
            $metas[] = $wpdb->prepare( '%d, %s, %s', array( $log_id, $meta_key, $meta_value ) );
        }

        $logs_meta = GamiPress()->db->logs_meta;
        $metas = implode( '), (', $metas );

        // Since the log is recently inserted, is faster to run a single query to insert all metas instead of insert them one-by-one
        $wpdb->query( "INSERT INTO {$logs_meta} (log_id, meta_key, meta_value) VALUES ({$metas})" );

    }

    // Hook to add custom data
    do_action( 'gamipress_insert_log', $log_id, $log_data, $log_meta, $user_id );

    ct_reset_setup_table();

    return $log_id;

}

/**
 * Parse log pattern replacements
 *
 * @since  1.0.0
 *
 * @param  string  $log_pattern   The pattern
 * @param  array   $log_data      Log post data
 * @param  array   $log_meta      Log meta data
 *
 * @return integer             	  The post ID of the newly created log entry
 */
function gamipress_parse_log_pattern( $log_pattern = '',  $log_data = array(), $log_meta = array()) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {

        if( isset( $log_data['user_id'] ) ) {
            $log_data['post_author'] = $log_data['user_id'];
        }

        if( isset( $log_data['type'] ) ) {
            $log_meta['type'] = $log_data['type'];
        }

        return gamipress_parse_log_pattern_old( $log_pattern, $log_data, $log_meta );
    }

    global $gamipress_pattern_replacements;

    // Setup site pattern replacements
    $gamipress_pattern_replacements['{site_title}'] = get_bloginfo( 'name' );

    $user = get_userdata( $log_data['user_id'] );

    // Setup user pattern replacements
    $gamipress_pattern_replacements['{user_id}'] = ( $user ? $user->ID : '' );
    $gamipress_pattern_replacements['{user}'] = ( $user ? $user->display_name : '' );

    // TODO: Add more user tags

    $gamipress_pattern_replacements['{trigger_type}'] = gamipress_get_activity_trigger_label( $log_data['trigger_type'] );
    $gamipress_pattern_replacements['{trigger_type_key}'] = $log_data['trigger_type'];

    foreach( $log_meta as $log_meta_key => $log_meta_value ) {

        if( in_array( $log_meta_key, array( 'pattern', 'type' ) ) )
            continue;

        // Implode meta value if is an array of values
        if( is_array( $log_meta_value ) )
            $log_meta_value = implode( ', ', $log_meta_value );

        $gamipress_pattern_replacements['{' . $log_meta_key . '}'] = $log_meta_value;
    }

    do_action( 'gamipress_before_parse_log_pattern', $log_data, $log_meta );

    return str_replace( array_keys( $gamipress_pattern_replacements ), $gamipress_pattern_replacements, $log_pattern );

}

/**
 * Log pattern replacements for achievements earn
 *
 * @since  1.0.0
 *
 * @uses    global  $gamipress_pattern_replacements
 *
 * @param   array   $log_data     Log post data
 * @param   array   $log_meta     Log meta data
 */
function gamipress_parse_achievement_log_pattern( $log_data, $log_meta ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {

        if( isset( $log_data['user_id'] ) ) {
            $log_data['post_author'] = $log_data['user_id'];
        }

        if( isset( $log_data['type'] ) ) {
            $log_meta['type'] = $log_data['type'];
        }

        gamipress_parse_achievement_log_pattern_old( $log_data, $log_meta );
        return;
    }

    // If log has assigned an achievement, then add achievement pattern replacements
    if( isset( $log_meta['achievement_id'] ) ) {

        global $gamipress_pattern_replacements;

        // Achievement pattern replacements
        $achievement       = gamipress_get_post( $log_meta['achievement_id'] );
        $achievement_types = gamipress_get_achievement_types();
        $achievement_type  = ( $achievement && isset( $achievement_types[$achievement->post_type]['singular_name'] ) ) ? $achievement_types[$achievement->post_type]['singular_name'] : '';

        // {achievement} tag
        $gamipress_pattern_replacements['{achievement}'] = $achievement->post_title;

        // {achievement_type} tag
        $gamipress_pattern_replacements['{achievement_type}'] = $achievement_type;

        if( $log_data['type'] === 'achievement_award' ) {
            $admin = get_userdata( $log_meta['admin_id'] );

            // {admin_username} tag
            $gamipress_pattern_replacements['{admin}'] = $admin->display_name;
        }
    }

}
add_action( 'gamipress_before_parse_log_pattern', 'gamipress_parse_achievement_log_pattern', 10, 2 );

/**
 * Log pattern replacements for points earn/award
 *
 * @since  1.0.0
 *
 * @uses    global  $gamipress_pattern_replacements
 *
 * @param   array   $log_data     Log post data
 * @param   array   $log_meta     Log meta data
 */
function gamipress_parse_points_log_pattern( $log_data, $log_meta ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {

        if( isset( $log_data['user_id'] ) ) {
            $log_data['post_author'] = $log_data['user_id'];
        }

        if( isset( $log_data['type'] ) ) {
            $log_meta['type'] = $log_data['type'];
        }

        gamipress_parse_points_log_pattern_old( $log_data, $log_meta );
        return;
    }

    // If log is a points based entry, then add points pattern replacements
    if( $log_data['type'] === 'points_award' || $log_data['type'] === 'points_revoke' || $log_data['type'] === 'points_earn' || $log_data['type'] === 'points_deduct' ) {

        global $gamipress_pattern_replacements;

        $points_type = $log_meta['points_type'];

        $points_types = gamipress_get_points_types();

        // Default points label
        $points_label = __( 'points', 'gamipress' );

        if( isset( $points_types[$points_type] ) ) {
            // Points type label
            $points_label = strtolower( $points_types[$points_type]['plural_name'] );
        }

        // {points} tag, absint ensures a positive amount to build a pattern like "User expended 100 points" instead of "User expended -100 points"
        $gamipress_pattern_replacements['{points}'] = gamipress_format_amount( absint( $log_meta['points'] ), $points_type );

        // {points_type} tag
        $gamipress_pattern_replacements['{points_type}'] = $points_label;

        if( $log_data['type'] === 'points_award' || $log_data['type'] === 'points_revoke' ) {
            $admin = get_userdata( $log_meta['admin_id'] );

            // {admin_username} tag
            $gamipress_pattern_replacements['{admin}'] = $admin->display_name;
        }

    }

}
add_action( 'gamipress_before_parse_log_pattern', 'gamipress_parse_points_log_pattern', 10, 2 );

/**
 * Log pattern replacements for rank earn/award
 *
 * @since  1.3.1
 *
 * @uses    global  $gamipress_pattern_replacements
 *
 * @param   array   $log_data     Log post data
 * @param   array   $log_meta     Log meta data
 */
function gamipress_parse_rank_log_pattern( $log_data, $log_meta ) {

    // If log is a points based entry, then add points pattern replacements
    if( $log_data['type'] === 'rank_award' || $log_data['type'] === 'rank_earn' ) {

        global $gamipress_pattern_replacements;

        $rank = gamipress_get_post( $log_meta['rank_id'] );

        // {rank} and {tank_type} tags
        $gamipress_pattern_replacements['{rank}'] = $rank ? $rank->post_title : '';
        $gamipress_pattern_replacements['{rank_type}'] = $rank ? gamipress_get_rank_type_singular( $rank->post_type, true ) : '';

        if( $log_data['type'] === 'rank_award' ) {
            $admin = get_userdata( $log_meta['admin_id'] );

            // {admin_username} tag
            $gamipress_pattern_replacements['{admin}'] = $admin->display_name;
        }

    }

}
add_action( 'gamipress_before_parse_log_pattern', 'gamipress_parse_rank_log_pattern', 10, 2 );

/**
 * Update the post title of a log entry.
 *
 * @since  1.0.0
 *
 * @param  array $object_data           An array with new object data.
 * @param  array $original_object_data  An array with the original object data.
 * @return array            Updated post data.
 */
function gamipress_maybe_apply_log_pattern( $object_data = array(), $original_object_data = array() ) {

    global $ct_table;

    // If not is our logs, return
    if( $ct_table->name !== 'gamipress_logs' ) {
        return $object_data;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $object_data;
    }

    if ( isset( $original_object_data['log_id'] ) && $original_object_data['log_id'] !== 0 ) {

        $log_id = $original_object_data['log_id'];

        // Just update using pattern if title has been changed
        if( empty( $object_data['title'] ) ) {
            // Check pattern
            if( isset( $_POST['_gamipress_pattern'] ) && ! empty( $_POST['_gamipress_pattern'] ) ) {
                $pattern = sanitize_text_field( $_POST['_gamipress_pattern'] );
            } else {
                $pattern = ct_get_object_meta( $log_id, '_gamipress_pattern', true );
            }

            // Just parse pattern if not empty
            if( ! empty( $pattern ) ) {
                $object_data['title'] = gamipress_get_parsed_log( $log_id );
            }
        }
    }

    return $object_data;

}
add_filter( 'ct_insert_object_data' , 'gamipress_maybe_apply_log_pattern' , 99, 2 );


/**
 * Get and apply the pattern of a log entry.
 *
 * @since  1.0.0
 *
 * @param  integer $log_id The log's ID.
 *
 * @return string          Parsed log pattern.
 */
function gamipress_get_parsed_log( $log_id = null ) {

    if( $log_id === null ) {
        return '';
    }

    $prefix = '_gamipress_';

    // Setup table
    ct_setup_table( 'gamipress_logs' );

    $log_data = ct_get_object( $log_id, ARRAY_A );

    if( ! $log_data ) {
        return '';
    }

    $log_meta = array(
        'pattern' => ct_get_object_meta( $log_id, $prefix . 'pattern', true ),
    );

    $log_meta = apply_filters( 'gamipress_get_log_meta_data', $log_meta, $log_id, $log_data );

    ct_reset_setup_table();

    return gamipress_parse_log_pattern( $log_meta['pattern'], $log_data, $log_meta );

}

/**
 * Get log meta data defaults from a log's object ID
 *
 * @param $log_meta
 * @param $log_id
 * @param $log_data
 *
 * @return mixed
 */
function gamipress_get_log_meta_data_defaults( $log_meta, $log_id, $log_data ) {

    $prefix = '_gamipress_';

    $meta_keys = array();

    if( $log_data['type'] === 'event_trigger' ) {
        // Event trigger meta data

        // If not upgraded to 1.4.7 yet, return trigger_type as a meta data
        if( ! is_gamipress_upgraded_to( '1.4.7' ) ) {
            $meta_keys[] = 'trigger_type';
        }

        $meta_keys[] = 'count';
    } else if( $log_data['type'] === 'achievement_earn' || $log_data['type'] === 'achievement_award' ) {
        // Achievement earn meta data
        $meta_keys[] = 'achievement_id';

        if( $log_data['type'] === 'achievement_award' ) {
            // Specific achievement award meta data
            $meta_keys[] = 'admin_id';
        }
    } else if( $log_data['type'] === 'points_award' || $log_data['type'] === 'points_revoke' || $log_data['type'] === 'points_earn' || $log_data['type'] === 'points_deduct' ) {
        // Points earn/deduct/award/revoke meta data
        $meta_keys[] = 'points';
        $meta_keys[] = 'points_type';
        $meta_keys[] = 'total_points';

        if( $log_data['type'] === 'points_award' || $log_data['type'] === 'points_revoke' ) {
            // Specific points award meta data
            $meta_keys[] = 'admin_id';
        }
    } else if( $log_data['type'] === 'rank_earn' || $log_data['type'] === 'rank_award' ) {
        // Achievement earn meta data
        $meta_keys[] = 'rank_id';

        if( $log_data['type'] === 'rank_award' ) {
            // Specific rank award meta data
            $meta_keys[] = 'admin_id';
        }
    }

    foreach( $meta_keys as $meta_key ) {
        $log_meta[$meta_key] = ct_get_object_meta( $log_id, $prefix . $meta_key, true );
    }

    return $log_meta;

}
add_filter( 'gamipress_get_log_meta_data', 'gamipress_get_log_meta_data_defaults', 10, 3 );

/**
 * Get the log object data
 *
 * @since 2.1.2
 *
 * @param int       $log_id         The log ID
 * @param string    $meta_key       The meta key to retrieve. By default, returns
 *                                  data for all keys. Default empty.
 * @param bool      $single         Optional. Whether to return a single value. Default false.
 *
 * @return mixed                    Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function gamipress_get_log_meta( $log_id, $meta_key = '', $single = false ) {

    ct_setup_table( 'gamipress_logs' );

    $meta_value = ct_get_object_meta( $log_id, $meta_key, $single );

    ct_reset_setup_table();

    return $meta_value;

}

/**
 * Update the log object data
 *
 * @since 2.1.2
 *
 * @param int    $log_id     The log ID
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed  $prev_value Optional. Previous value to check before removing.
 *                           Default empty.
 *
 * @return int|bool         Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function gamipress_update_log_meta( $log_id, $meta_key, $meta_value, $prev_value = '' ) {

    ct_setup_table( 'gamipress_logs' );

    $meta_id = ct_update_object_meta( $log_id, $meta_key, $meta_value, $prev_value );

    ct_reset_setup_table();

    return $meta_id;

}
