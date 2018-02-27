<?php
/**
 * Log Functions
 *
 * @package     GamiPress\Log_Functions
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
        'points_deduct' => __( 'Points Deduct', 'gamipress' ),
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
        '{trigger_type}'        =>  __( 'Event type user has triggered.', 'gamipress' ),
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
 * Return user logs with the specified meta data
 *
 * @since  1.3.7
 * @updated 1.3.9.6 Added $since parameter
 * @updated 1.3.9.8 Improvements on since checks
 *
 * @param int       $user_id
 * @param array     $log_meta
 * @param integer   $since
 *
 * @return array
 */
function gamipress_get_user_logs( $user_id = 0, $log_meta = array(), $since = 0 ) {

    global $wpdb;

    $logs 		= GamiPress()->db->logs;
    $logs_meta 	= GamiPress()->db->logs_meta;

    // Initialize query definitions
    $joins = array();
    $where = array();

    // Initialize query args
    $query_args = array( absint( $user_id ) );

    // Loop all log meta to build the where clause
    foreach ( (array) $log_meta as $key => $meta ) {

        if( $key === 'type' ) {

            if( is_array( $meta ) ) {
                $meta = "'" . implode( "', '", $meta ) . "'";
                $where[] = "l.type IN ({$meta})";
            } else {
                $where[] = "l.type = %s";
                $query_args[] = $meta;
            }

        } else {

            $index = count( $joins );

            // Setup query definitions
            $joins[] = "LEFT JOIN {$logs_meta} AS lm{$index} ON ( l.log_id = lm{$index}.log_id )";
            $where[] = "lm{$index}.meta_key = %s AND lm{$index}.meta_value = %s";

            // Setup query vars
            $query_args[] = '_gamipress_' . sanitize_key( $key );
            $query_args[] = $meta;

        }

    }

    // Setup since clause
    if( $since !== 0 ) {

        $date = date( 'Y-m-d H:i:s', $since );

        $where[] = "l.date >= '$date'";

    }

    // Turn arrays into strings
    $joins = implode( ' ', $joins );
    $where = (  ! empty( $where ) ? '( '. implode( ' ) AND ( ', $where ) . ' ) ' : '' );

    $user_logs = $wpdb->get_results( $wpdb->prepare(
        "SELECT l.*
         FROM   {$logs} AS l
         {$joins}
         WHERE l.user_id = %s
          AND ( {$where} )",
        $query_args
    ) );

    return $user_logs;

}

/**
 * Return count of user logs with the specified meta data
 *
 * @since   1.1.8
 * @updated 1.3.7 Added support for multiples types
 * @updated 1.4.2 Added $since parameter
 *
 * @param int       $user_id
 * @param array     $log_meta
 *
 * @return bool
 */
function gamipress_get_user_log_count( $user_id = 0, $log_meta = array(), $since = 0 ) {

    global $wpdb;

    $logs 		= GamiPress()->db->logs;
    $logs_meta 	= GamiPress()->db->logs_meta;

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {
        return gamipress_get_user_log_count_old( $user_id, $log_meta );
    }

    // Initialize query definitions
    $joins = array();
    $where = array();

    // Initialize query args
    $query_args = array( absint( $user_id ) );

    foreach ( (array) $log_meta as $key => $meta ) {

        if( $key === 'type' ) {

            // Since 1.2.8 _gamipress_type meta was moved to gamipress_logs DB table
            if( is_array( $meta ) ) {
                $meta = "'" . implode( "', '", $meta ) . "'";
                $where[] = "l.type IN ({$meta})";
            } else {
                $where[] = "l.type = %s";
                $query_args[] = $meta;
            }

        } else {

            $index = count( $joins );

            // Setup query definitions
            $joins[] = "LEFT JOIN {$logs_meta} AS lm{$index} ON ( l.log_id = lm{$index}.log_id )";
            $where[] = "lm{$index}.meta_key = %s AND lm{$index}.meta_value = %s";

            // Setup query vars
            $query_args[] = '_gamipress_' . sanitize_key( $key );
            $query_args[] = $meta;

        }

    }

    // Setup since clause
    if( $since !== 0 ) {

        $date = date( 'Y-m-d H:i:s', $since );

        $where[] = "l.date >= '$date'";

    }

    // Turn arrays into strings
    $joins = implode( ' ', $joins );
    $where = (  ! empty( $where ) ? '( '. implode( ' ) AND ( ', $where ) . ' ) ' : '' );

    $user_triggers = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*)
         FROM   {$logs} AS l
         {$joins}
         WHERE l.user_id = %s
          AND ( {$where} )",
        $query_args
    ) );

    return absint( $user_triggers );

}

/**
 * Return the last user log with the specified meta data
 *
 * @since  1.4.2
 *
 * @param int       $user_id
 * @param array     $log_meta
 *
 * @return stdClass|false
 */
function gamipress_get_user_last_log( $user_id = 0, $log_meta = array() ) {

    global $wpdb;

    $logs 		= GamiPress()->db->logs;
    $logs_meta 	= GamiPress()->db->logs_meta;

    // Initialize query definitions
    $joins = array();
    $where = array();

    // Initialize query args
    $query_args = array( absint( $user_id ) );

    // Loop all log meta to build the where clause
    foreach ( (array) $log_meta as $key => $meta ) {

        if( $key === 'type' ) {

            if( is_array( $meta ) ) {
                $meta = "'" . implode( "', '", $meta ) . "'";
                $where[] = "l.type IN ({$meta})";
            } else {
                $where[] = "l.type = %s";
                $query_args[] = $meta;
            }

        } else {

            $index = count( $joins );

            // Setup query definitions
            $joins[] = "LEFT JOIN {$logs_meta} AS lm{$index} ON ( l.log_id = lm{$index}.log_id )";
            $where[] = "lm{$index}.meta_key = %s AND lm{$index}.meta_value = %s";

            // Setup query vars
            $query_args[] = '_gamipress_' . sanitize_key( $key );
            $query_args[] = $meta;

        }

    }

    // Turn arrays into strings
    $joins = implode( ' ', $joins );
    $where = (  ! empty( $where ) ? '( '. implode( ' ) AND ( ', $where ) . ' ) ' : '' );

    $user_logs = $wpdb->get_row( $wpdb->prepare(
        "SELECT l.*
         FROM   {$logs} AS l
         {$joins}
         WHERE l.user_id = %s
          AND ( {$where} )
         ORDER BY l.date DESC
         LIMIT 1",
        $query_args
    ) );

    return is_object( $user_logs ) ? $user_logs : false;

}

/**
 * Posts a log entry when a user unlocks any achievement post
 *
 * @since  1.0.0
 * @updated  1.2.8 Added $type
 *
 * @param  string   $type  	    The log type
 * @param  int      $user_id  	The user ID
 * @param  string   $access     Access to this log ( public|private )
 * @param  array    $log_meta   Log meta data
 *
 * @return integer             	The post ID of the newly created log entry
 */
function gamipress_insert_log( $type = '', $user_id = 0, $access = 'public', $log_meta = array() ) {

    // If not properly upgrade to required version fallback to compatibility function
    if( ! is_gamipress_upgraded_to( '1.2.8' ) ) {

        $log_meta['type'] = $type;

        return gamipress_insert_log_old( $user_id, $access, $log_meta );
    }

    // Setup table
    ct_setup_table( 'gamipress_logs' );

    // Post data
    $log_data = array(
        'title'	        => '',
        'description'	=> '',
        'type' 	        => $type,
        'access'	    => $access,
        'user_id'	    => $user_id === 0 ? get_current_user_id() : absint( $user_id ),
        'date'	        => date( 'Y-m-d H:i:s' ),
    );

    // Auto-generated post title
    $log_data['title'] = gamipress_parse_log_pattern( $log_meta['pattern'], $log_data, $log_meta );

    // Store log entry
    $log_id = ct_insert_object( $log_data );

    // Store log meta data
    if ( $log_id && ! empty( $log_meta ) ) {

        foreach ( (array) $log_meta as $key => $meta ) {

            ct_update_object_meta( $log_id, '_gamipress_' . sanitize_key( $key ), $meta );

        }

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
    $gamipress_pattern_replacements['{user_id}'] = $user->ID;
    $gamipress_pattern_replacements['{user}'] = $user->display_name;

    // TODO: Add more user tags

    foreach( $log_meta as $log_meta_key => $log_meta_value ) {
        if( in_array( $log_meta_key, array( 'pattern', 'type' ) ) ) {
            continue;
        }

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
        $gamipress_pattern_replacements['{points}'] = absint( $log_meta['points'] );

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
        $gamipress_pattern_replacements['{rank_type}'] = $rank ? gamipress_get_rank_type_singular( $rank->post_type ) : '';

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
        $object_data['title'] = gamipress_get_parsed_log( $original_object_data['log_id'] );
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
        $meta_keys[] = 'trigger_type';
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
    }

    foreach( $meta_keys as $meta_key ) {
        $log_meta[$meta_key] =  ct_get_object_meta( $log_id, $prefix . $meta_key, true );
    }

    return $log_meta;

}
add_filter( 'gamipress_get_log_meta_data', 'gamipress_get_log_meta_data_defaults', 10, 3 );
