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
        'points_award' => __( 'Points Award', 'gamipress' ),
    ) );
}

/**
 * Get an array of log pattern tags
 *
 * @since  1.0.2

 * @return array The registered log pattern tags
 */
function gamipress_get_log_pattern_tags() {
    return apply_filters( 'gamipress_log_pattern_tags', array(
        '{user}'                =>  __(  'User assigned.', 'gamipress' ),
        '{admin}'               =>  __(  'Admin that awards.', 'gamipress' ),
        '{achievement}'         =>  __(  'Achievement user has earned.', 'gamipress' ),
        '{achievement_type}'    =>  __(  'Type of the achievement earned.', 'gamipress' ),
        '{trigger_type}'        =>  __(  'Event type user has triggered.', 'gamipress' ),
        '{count}'               =>  __(  'Times user triggered this event.', 'gamipress' ),
        '{points}'              =>  __(  'Points user has earned.', 'gamipress' ),
        '{points_type}'         =>  __(  'Type of the points earned.', 'gamipress' ),
        '{total_points}'        =>  __(  'Points user has earned until this log.', 'gamipress' ),
    ) );
}

/**
 * Get a string with the desired log pattern tags html markup
 *
 * @since  1.0.2
 *
 * @param array $specific_tags
 *
 * @return string Log pattern tags html markup
 */
function gamipress_get_log_pattern_tags_html( $specific_tags = array() ) {
    $output = '<ul class="gamipress-log-pattern-tags-list">';

    foreach( gamipress_get_log_pattern_tags() as $tag => $description ) {

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
 * Return count of user logs with the specified meta data
 *
 * @since  1.1.8
 *
 * @param int       $user_id
 * @param array     $log_meta
 *
 * @return bool
 */
function gamipress_get_user_log_count( $user_id = 0, $log_meta = array() ) {

    global $wpdb;

    // Initialize query definitions
    $joins = array();
    $where = array();

    // Initialize query args
    $query_args = array( 'gamipress-log', $user_id );

    foreach ( (array) $log_meta as $key => $meta ) {
        $index = count( $joins );

        // Setup query definitions
        $joins[] = "LEFT JOIN $wpdb->postmeta AS pm{$index} ON ( p.ID = pm{$index}.post_id )";
        $where[] = "pm{$index}.meta_key = %s AND pm{$index}.meta_value = %s";

        // Setup query vars
        $query_args[] = '_gamipress_' . sanitize_key( $key );
        $query_args[] = $meta;
    }

    // Turn arrays into strings
    $joins = implode( ' ', $joins );
    $where = (  ! empty( $where ) ? '( '. implode( ' ) AND ( ', $where ) . ' ) ' : '' );

    $user_triggers = $wpdb->get_var( $wpdb->prepare(
        "
        SELECT COUNT(*)
        FROM   $wpdb->posts AS p
        {$joins}
        WHERE p.post_type = %s
            AND p.post_author = %s
            AND ( {$where} )
        ",
        $query_args
    ) );

    return absint( $user_triggers );
}

/**
 * Posts a log entry when a user unlocks any achievement post
 *
 * @since  1.0.0
 *
 * @param  int      $user_id  	The user ID
 * @param  string   $access     Access to this log ( public|private )
 * @param  array    $log_meta   Log meta data
 *
 * @return integer             	The post ID of the newly created log entry
 */
function gamipress_insert_log( $user_id = 0, $access = 'public', $log_meta = array() ) {

    // Post data
    $log_data = array(
        'post_type' 	=> 'gamipress-log',
        'post_status'	=> ( ( $access === 'public' ) ? 'publish' : 'private' ),
        'post_author'	=> $user_id === 0 ? get_current_user_id() : $user_id,
        'post_parent'	=> 0,
        'post_title'	=> '',
        'post_content'	=> ''
    );

    // Auto-generated post title
    $log_data['post_title'] = gamipress_parse_log_pattern( $log_meta['pattern'], $log_data, $log_meta );

    // Store log entry
    $log_id = wp_insert_post( $log_data );

    // Store log meta data
    if ( $log_id && ! empty( $log_meta ) ) {
        foreach ( (array) $log_meta as $key => $meta ) {
            update_post_meta( $log_id, '_gamipress_' . sanitize_key( $key ), $meta );
        }
    }

    // Hook to add custom data
    do_action( 'gamipress_insert_log', $log_id, $log_data, $log_meta, $user_id );

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
    global $gamipress_pattern_replacements;

    $post_author = get_userdata( $log_data['post_author'] );

    // Setup user pattern replacements
    $gamipress_pattern_replacements['{user_id}'] = $post_author->ID;
    $gamipress_pattern_replacements['{user}'] = ( is_admin() ? $post_author->display_name . ' (' . $post_author->user_login . ')' : $post_author->display_name );

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

    // If log has assigned an achievement, then add achievement pattern replacements
    if( isset( $log_meta['achievement_id'] ) ) {

        global $gamipress_pattern_replacements;

        // Achievement pattern replacements
        $achievement       = get_post( $log_meta['achievement_id'] );
        $achievement_types = gamipress_get_achievement_types();
        $achievement_type  = ( $achievement && isset( $achievement_types[$achievement->post_type]['singular_name'] ) ) ? $achievement_types[$achievement->post_type]['singular_name'] : '';

        // {achievement} tag
        $gamipress_pattern_replacements['{achievement}'] = $achievement->post_title;

        // {achievement_type} tag
        $gamipress_pattern_replacements['{achievement_type}'] = $achievement_type;
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

    // If log is a points based entry, then add points pattern replacements
    if( $log_meta['type'] === 'points_award' || $log_meta['type'] === 'points_earn' ) {
        global $gamipress_pattern_replacements;

        $points_type = $log_meta['points_type'];

        $points_types = gamipress_get_points_types();

        // Default points label
        $points_label = __( 'points', 'gamipress' );

        if( isset( $points_types[$points_type] ) ) {
            // Points type label
            $points_label = strtolower( $points_types[$points_type]['plural_name'] );
        }

        // {points_type} tag
        $gamipress_pattern_replacements['{points_type}'] = $points_label;

        if( $log_meta['type'] === 'points_award' ) {
            $admin = get_userdata( $log_meta['admin_id'] );

            // {admin_username} tag
            $gamipress_pattern_replacements['{admin}'] = ( is_admin() ? $admin->display_name . ' (' . $admin->user_login . ')' : $admin->display_name );
        }
    }

}
add_action( 'gamipress_before_parse_log_pattern', 'gamipress_parse_points_log_pattern', 10, 2 );

/**
 * Update the post title of a log entry.
 *
 * @since  1.0.0
 *
 * @param  array $data      Post data.
 * @param  array $post_args Post args.
 * @return array            Updated post data.
 */
function gamipress_maybe_apply_log_pattern( $data = array(), $post_args = array() ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $data;
    }

    if ( $post_args['ID'] !== 0 && $post_args['post_type'] === 'gamipress-log' ) {
        $data['post_title'] = gamipress_get_parsed_log( $post_args['ID'] );
    }

    return $data;
}
add_filter( 'wp_insert_post_data' , 'gamipress_maybe_apply_log_pattern' , 99, 2 );


/**
 * Get and apply the pattern of a log entry.
 *
 * @since  1.0.0
 *
 * @param  integer $log_id The log's ID.
 * @return string          Parsed log pattern.
 */
function gamipress_get_parsed_log( $log_id = null ) {

    $prefix = '_gamipress_';

    if( $log_id === null ) {
        $log_id = get_the_ID();
    }

    $log_data = get_post( $log_id, ARRAY_A );

    if( ! $log_data ) {
        return '';
    }

    if( $log_data['post_type'] !== 'gamipress-log' ) {
        return '';
    }

    $log_meta = array(
        'pattern' => get_post_meta( $log_id, $prefix . 'pattern', true ),
        'type' => get_post_meta( $log_id, $prefix . 'type', true )
    );

    $log_meta = apply_filters( 'gamipress_get_log_meta_data', $log_meta, $log_id );

    return gamipress_parse_log_pattern( $log_meta['pattern'], $log_data, $log_meta );

}

/**
 * Get log meta data defaults from a log's post ID
 *
 * @param $log_meta
 * @param $log_id
 *
 * @return mixed
 */
function gamipress_get_log_meta_data_defaults( $log_meta, $log_id ) {
    $prefix = '_gamipress_';

    $meta_keys = array();

    if( $log_meta['type'] === 'event_trigger' ) {
        // Event trigger meta data
        $meta_keys[] = 'trigger_type';
        $meta_keys[] = 'count';
    } else if( $log_meta['type'] === 'achievement_earn' ||  $log_meta['type'] === 'achievement_award' ) {
        // Achievement earn meta data
        $meta_keys[] = 'achievement_id';

        if( $log_meta['type'] === 'achievement_award' ) {
            // Specific achievement award meta data
            $meta_keys[] = 'admin_id';
        }
    } else if( $log_meta['type'] === 'points_award' || $log_meta['type'] === 'points_earn' ) {
        // Points earn/award meta data
        $meta_keys[] = 'points';
        $meta_keys[] = 'points_type';
        $meta_keys[] = 'total_points';

        if( $log_meta['type'] === 'points_award' ) {
            // Specific points award meta data
            $meta_keys[] = 'admin_id';
        }
    }

    foreach( $meta_keys as $meta_key ) {
        $log_meta[$meta_key] =  get_post_meta( $log_id, $prefix . $meta_key, true );
    }

    return $log_meta;
}
add_filter( 'gamipress_get_log_meta_data', 'gamipress_get_log_meta_data_defaults', 10, 2 );
