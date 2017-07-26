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
 * Posts a log entry when a user unlocks any achievement post
 *
 * @since  1.0.0
 *
 * @param  int      $user_id  	The user ID
 * @param  string   $access     Access to this log ( public|private )
 * @return integer             	The post ID of the newly created log entry
 */
function gamipress_insert_log( $user_id = 0, $access = 'public', $log_meta = array() ) {
    // Post data
    $args = array(
        'post_type' 	=> 'gamipress-log',
        'post_status'	=> ( ( $access === 'public' ) ? 'publish' : 'private' ),
        'post_author'	=> $user_id,
        'post_parent'	=> 0,
        'post_title'	=> '',
        'post_content'	=> ''
    );

    // Auto-generated post title
    $args['post_title'] = gamipress_parse_log_pattern( $log_meta['pattern'], $args, $log_meta );

    // Store log entry
    $log_id = wp_insert_post( $args );

    // Store log meta data
    if ( $log_id && ! empty( $log_meta ) ) {
        foreach ( (array) $log_meta as $key => $meta ) {
            update_post_meta( $log_id, '_gamipress_' . sanitize_key( $key ), $meta );
        }
    }

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
        $achievement_type  = ( $achievement && isset( $achievement_types[$achievement->post_type]['single_name'] ) ) ? $achievement_types[$achievement->post_type]['single_name'] : '';

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

    if ( $post_args['post_type'] === 'gamipress-log') {
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
    global $post;

    $prefix = '_gamipress_';

    if( $log_id === null ) {
        $log_id = $post->ID;
    }

    $log_data = get_post( $log_id, ARRAY_A );

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


