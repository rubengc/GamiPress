<?php
/**
 * GamiPress 1.2.8 compatibility functions
 *
 * @package     GamiPress\1.2.8
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.2.8
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/* --------------------------
 * Achievement functions
   -------------------------- */

/**
 * Get an array of all users who have earned a given achievement
 *
 * @since  1.0.0
 *
 * @param  integer $achievement_id The given achievement's post ID
 *
 * @return array                   Array of user objects
 */
function gamipress_get_achievement_earners_old( $achievement_id = 0 ) {

    // Grab our earners
    $earners = new WP_User_Query( array(
        'meta_key'     => '_gamipress_achievements',
        'meta_value'   => $achievement_id,
        'meta_compare' => 'LIKE'
    ) );

    $earned_users = array();
    foreach( $earners->results as $earner ) {
        if ( gamipress_has_user_earned_achievement( $achievement_id, $earner->ID ) ) {
            $earned_users[] = $earner;
        }
    }
    // Send back our query results
    return $earned_users;
}

/**
 * Change all earned meta from one achievement type to another.
 *
 * @since 1.0.0
 *
 * @param string $original_type Original achievement type.
 * @param string $new_type      New achievement type.
 */
function gamipress_update_earned_meta_achievement_types_old( $original_type = '', $new_type = '' ) {

    $metas = gamipress_get_unserialized_achievement_metas( '_gamipress_achievements', $original_type );

    if ( ! empty( $metas ) ) {
        foreach ( $metas as $meta ) {
            foreach ( $meta->meta_value as $site_id => $achievements ) {
                $meta->meta_value[ $site_id ] = gamipress_update_meta_achievement_types( $achievements, $original_type, $new_type );
            }
            update_user_meta( $meta->user_id, $meta->meta_key, $meta->meta_value );
        }
    }

}

/* --------------------------
 * Rules Engine
   -------------------------- */

/**
 * Revoke an achievement from a user
 *
 * @since  	1.0.0
 *
 * @param  integer $achievement_id The given achievement's post ID
 * @param  integer $user_id        The given user's ID
 *
 * @return void
 */
function gamipress_revoke_achievement_from_user_old( $achievement_id = 0, $user_id = 0 ) {

    // Use the current user's ID if none specified
    if ( ! $user_id )
        $user_id = wp_get_current_user()->ID;

    // Grab the user's earned achievements
    $earned_achievements = gamipress_get_user_achievements( array( 'user_id' => $user_id ) );

    // Loop through each achievement and drop the achievement we're revoking
    foreach ( $earned_achievements as $key => $achievement ) {
        if ( $achievement->ID == $achievement_id ) {

            // Drop the achievement from our earnings
            unset( $earned_achievements[$key] );

            // Re-key our array
            $earned_achievements = array_values( $earned_achievements );

            // Update user's earned achievements
            gamipress_update_user_achievements( array( 'user_id' => $user_id, 'all_achievements' => $earned_achievements ) );

            // Available hook for taking further action when an achievement is revoked
            do_action( 'gamipress_revoke_achievement', $user_id, $achievement_id );

            // Stop after dropping one, because we don't want to delete ALL instances
            break;
        }
    }

}

/* --------------------------
 * User functions
   -------------------------- */

/**
 * Get a user's gamipress achievements
 *
 * @since  1.0.0
 *
 * @param  array $args An array of all our relevant arguments
 *
 * @return array       An array of all the achievement objects that matched our parameters, or empty if none
 */
function gamipress_get_user_achievements_old( $args = array() ) {

    // Setup our default args
    $defaults = array(
        'user_id'          => 0,     // The given user's ID
        'site_id'          => get_current_blog_id(), // The given site's ID
        'achievement_id'   => false, // A specific achievement's post ID
        'achievement_type' => false, // A specific achievement type
        'since'            => 0,     // A specific timestamp to use in place of $limit_in_days
    );

    $args = wp_parse_args( $args, $defaults );

    // Use current user's ID if none specified
    if ( ! $args['user_id'] )
        $args['user_id'] = get_current_user_id();

    // Grab the user's current achievements
    $achievements = ( $earned_items = get_user_meta( absint( $args['user_id'] ), '_gamipress_achievements', true ) ) ? (array) $earned_items : array();

    // If we want all sites (or no specific site), return the full array
    if ( empty( $achievements ) || empty( $args['site_id']) || 'all' == $args['site_id'] )
        return $achievements;

    // Otherwise, we only want the specific site's achievements
    $achievements = $achievements[$args['site_id']];

    if ( is_array( $achievements ) && ! empty( $achievements ) ) {
        foreach ( $achievements as $key => $achievement ) {

            // Drop any achievements earned before our since timestamp
            if ( absint($args['since']) > $achievement->date_earned )
                unset($achievements[$key]);

            // Drop any achievements that don't match our achievement ID
            if ( ! empty( $args['achievement_id'] ) && absint( $args['achievement_id'] ) != $achievement->ID )
                unset($achievements[$key]);

            // Drop any achievements that don't match our achievement type
            if ( ! empty( $args['achievement_type'] ) && ( $args['achievement_type'] != $achievement->post_type && ( !is_array( $args['achievement_type'] ) || !in_array( $achievement->post_type, $args['achievement_type'] ) ) ) )
                unset($achievements[$key]);

            if( isset( $args['display'] ) && $args['display'] ) {
                // Unset hidden achievements on display context
                $hidden = gamipress_get_hidden_achievement_by_id( $achievement->ID );

                if( ! empty( $hidden ) ) {
                    unset( $achievements[$key] );
                }
            }
        }
    }

    // Return our $achievements array_values (so our array keys start back at 0), or an empty array
    return ( is_array( $achievements ) ? array_values( $achievements ) : array());

}

/**
 * Updates the user's earned achievements
 *
 * We can either replace the achievement's array, or append new achievements to it.
 *
 * @since  1.0.0
 *
 * @param  array        $args An array containing all our relevant arguments
 *
 * @return integer|bool       The updated umeta ID on success, false on failure
 */
function gamipress_update_user_achievements_old( $args = array() ) {

    // Setup our default args
    $defaults = array(
        'user_id'          => 0,     // The given user's ID
        'site_id'          => get_current_blog_id(), // The given site's ID
        'all_achievements' => false, // An array of ALL achievements earned by the user
        'new_achievements' => false, // An array of NEW achievements earned by the user
    );
    $args = wp_parse_args( $args, $defaults );

    // Use current user's ID if none specified
    if ( ! $args['user_id'] )
        $args['user_id'] = get_current_user_id();

    // If we don't already have an array stored for this site, create a fresh one
    // Grab our user's achievements
    $achievements = gamipress_get_user_achievements( array(
        'user_id' => absint( $args['user_id'] ),
        'site_id' => 'all'
    ) );

    if ( !isset( $achievements[$args['site_id']] ) )
        $achievements[$args['site_id']] = array();

    // Determine if we should be replacing or appending to our achievements array
    if ( is_array( $args['all_achievements'] ) )
        $achievements[$args['site_id']] = $args['all_achievements'];
    elseif ( is_array( $args['new_achievements'] ) && ! empty( $args['new_achievements'] ) )
        $achievements[$args['site_id']] = array_merge( $achievements[$args['site_id']], $args['new_achievements'] );

    // Finally, update our user meta
    return update_user_meta( absint( $args['user_id'] ), '_gamipress_achievements', $achievements);

}

/* --------------------------
 * Logs
   -------------------------- */

/**
 * Register all GamiPress CPTs
 *
 * @since  1.0.0
 * @return void
 */
function gamipress_register_post_types_old() {

    if( is_gamipress_upgraded_to( '1.2.8' ) ) {
        return;
    }

    // Register Log
    register_post_type( 'gamipress-log', array(
        'labels'             => array(
            'name'               => __( 'Logs', 'gamipress' ),
            'singular_name'      => __( 'Log', 'gamipress' ),
            'add_new'            => __( 'Add New', 'gamipress' ),
            'add_new_item'       => __( 'Add New Log Entry', 'gamipress' ),
            'edit_item'          => __( 'Edit Log Entry', 'gamipress' ),
            'new_item'           => __( 'New Log Entry', 'gamipress' ),
            'all_items'          => __( 'Logs', 'gamipress' ),
            'view_item'          => __( 'View Logs', 'gamipress' ),
            'search_items'       => __( 'Search Logs', 'gamipress' ),
            'not_found'          => __( 'No Log Entries found', 'gamipress' ),
            'not_found_in_trash' => __( 'No Log Entries found in Trash', 'gamipress' ),
            'parent_item_colon'  => '',
            'menu_name'          => __( 'Logs', 'gamipress' )
        ),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => current_user_can( gamipress_get_manager_capability() ),
        'show_in_menu'       => 'gamipress',
        'show_in_nav_menus'  => false,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'gamipress-log' ),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( '' )
    ) );

}
add_action( 'init', 'gamipress_register_post_types_old', 11 );

/**
 * Register custom meta boxes used throughout GamiPress
 *
 * @since  1.0.0
 */
function gamipress_meta_boxes_old() {

    if( is_gamipress_upgraded_to( '1.2.8' ) ) {
        return;
    }

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_';

    // Log
    gamipress_add_meta_box(
        'log-data',
        __( 'Log Data', 'gamipress' ),
        'gamipress-log',
        array(
            'post_author' => array(
                'name' 	=> __( 'User', 'gamipress' ),
                'desc' 	=> __( 'User assigned to this log.', 'gamipress' ),
                'type' 	=> 'select',
                'options_cb' => 'gamipress_log_post_author_options'
            ),
            $prefix . 'type' => array(
                'name' 	=> __( 'Type', 'gamipress' ),
                'desc' 	=> __( 'The log type.', 'gamipress' ),
                'type' 	=> 'select',
                'options' 	=> gamipress_get_log_types(),
            ),
            $prefix . 'pattern' => array(
                'name' 	=> __( 'Pattern', 'gamipress' ),
                'desc' 	=> __( 'The log output pattern. Available tags:', 'gamipress' ) . gamipress_get_log_pattern_tags_html(),
                'type' 	=> 'text',
            ),
        ),
        array( 'priority' => 'high' )
    );

}
add_action( 'cmb2_admin_init', 'gamipress_meta_boxes_old' );

/**
 * GamiPress dashboard widget output.
 */
function gamipress_dashboard_widget_logs_old() {
    $posts = new WP_Query( array(
        'post_type'      => 'gamipress-log',
        'post_status'    => 'any',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'posts_per_page' => 5,
        'no_found_rows'  => true,
        'cache_results'  => false,
    ) );

    if ( $posts->have_posts() ) {

        echo '<div id="gamipress-latest-logs" class="gamipress-latest-logs">';

        echo '<ul>';

        $today      = date( 'Y-m-d', current_time( 'timestamp' ) );
        $yesterday  = date( 'Y-m-d', strtotime( '-1 day', current_time( 'timestamp' ) ) );

        while ( $posts->have_posts() ) {
            $posts->the_post();

            $time = get_the_time( 'U' );
            if ( date( 'Y-m-d', $time ) == $today ) {
                $relative = __( 'Today', 'gamipress' );
            } elseif ( date( 'Y-m-d', $time ) == $yesterday ) {
                $relative = __( 'Yesterday', 'gamipress' );
            } elseif ( date( 'Y', $time ) !== date( 'Y', current_time( 'timestamp' ) ) ) {
                /* translators: date and time format for recent posts on the dashboard, from a different calendar year, see https://secure.php.net/date */
                $relative = date_i18n( __( 'M jS Y' ), $time );
            } else {
                /* translators: date and time format for recent posts on the dashboard, see https://secure.php.net/date */
                $relative = date_i18n( __( 'M jS' ), $time );
            }

            // Use the post edit link for those who can edit, the permalink otherwise.
            $edit_post_link = current_user_can( 'edit_post', get_the_ID() ) ? get_edit_post_link() : get_permalink();

            $draft_or_post_title = _draft_or_post_title();
            printf(
                '<li><a href="%1$s">%2$s</a> <span>%3$s</span></li>',
                $edit_post_link,
                $draft_or_post_title,
                sprintf( _x( '%1$s, %2$s', 'dashboard' ), $relative, get_the_time() )
            );
        }

        echo '</ul>';
        echo '</div>';

    } else {
        echo '<p>' . __( 'Nothing to show :)', 'gamipress' ) .'</p>';
    }

    wp_reset_postdata();
}

function gamipress_log_post_author_options( $field ) {
    global $post;

    $post_author =  get_post_field( 'post_author', $post->ID );
    $user = get_userdata( $post_author );

    return array( $post_author => $user->display_name . ' (' . $user->user_login . ')' );
}


// WordPress will handle the saving for us, so don't save post author to meta.
add_filter( 'cmb2_override_post_author_meta_save', '__return_true' );

// Show log title in edit log screen
function gamipress_admin_log_title_preview( $post ) {
    if( $post->post_type === 'gamipress-log' ) : ?>
        <div class="gamipress-log-title-preview">
            <h1><?php echo get_the_title( $post->ID ); ?></h1>
        </div>
    <?php endif;
}
add_action( 'edit_form_after_title', 'gamipress_admin_log_title_preview' );

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
function gamipress_get_user_log_count_old( $user_id = 0, $log_meta = array() ) {

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
function gamipress_insert_log_old( $user_id = 0, $access = 'public', $log_meta = array() ) {

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
function gamipress_parse_log_pattern_old( $log_pattern = '',  $log_data = array(), $log_meta = array()) {
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
function gamipress_parse_achievement_log_pattern_old( $log_data, $log_meta ) {

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
function gamipress_parse_points_log_pattern_old( $log_data, $log_meta ) {

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

/**
 * Update the post title of a log entry.
 *
 * @since  1.0.0
 *
 * @param  array $data      Post data.
 * @param  array $post_args Post args.
 * @return array            Updated post data.
 */
function gamipress_maybe_apply_log_pattern_old( $data = array(), $post_args = array() ) {

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $data;
    }

    if ( $post_args['ID'] !== 0 && $post_args['post_type'] === 'gamipress-log' ) {
        $data['post_title'] = gamipress_get_parsed_log_old( $post_args['ID'] );
    }

    return $data;

}
add_filter( 'wp_insert_post_data' , 'gamipress_maybe_apply_log_pattern_old' , 99, 2 );

/**
 * Get and apply the pattern of a log entry.
 *
 * @since  1.0.0
 *
 * @param  integer $log_id The log's ID.
 * @return string          Parsed log pattern.
 */
function gamipress_get_parsed_log_old( $log_id = null ) {

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

    $log_meta = apply_filters( 'gamipress_get_log_meta_data_old', $log_meta, $log_id );

    return gamipress_parse_log_pattern_old( $log_meta['pattern'], $log_data, $log_meta );

}

/**
 * Get log meta data defaults from a log's post ID
 *
 * @param $log_meta
 * @param $log_id
 *
 * @return mixed
 */
function gamipress_get_log_meta_data_defaults_old( $log_meta, $log_id ) {
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
add_filter( 'gamipress_get_log_meta_data_old', 'gamipress_get_log_meta_data_defaults', 10, 2 );

/**
 * Logs List Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function gamipress_logs_shortcode_old( $atts = array () ) {

    global $gamipress_template_args;

    $gamipress_template_args = array();

    $atts = shortcode_atts( array(
        'user_id'     => '0',
        'limit'       => '10',
        'orderby'     => 'menu_order',
        'order'       => 'ASC',
        'include'     => '',
        'exclude'     => '',
    ), $atts, 'gamipress_logs' );

    gamipress_enqueue_scripts();

    // GamiPress template args global
    $gamipress_template_args = $atts;

    // Query Achievements
    $args = array(
        'post_type'      =>	'gamipress-log',
        'orderby'        =>	$atts['orderby'],
        'order'          =>	$atts['order'],
        'posts_per_page' =>	$atts['limit'],
        'post_status'    => 'publish',
    );

    // User
    if( $atts['user_id'] !== '0' ) {
        $args['author'] = $atts['user_id'];
    }

    // Build $include array
    if ( ! is_array( $atts['include'] ) && ! empty( $atts['include'] ) ) {
        $include = explode( ',', $atts['include'] );
    }

    // Build $exclude array
    if ( ! is_array( $atts['exclude'] ) && ! empty( $atts['exclude'] ) ) {
        $exclude = explode( ',', $atts['exclude'] );
    }

    // Include certain achievements
    if ( isset( $include ) && ! empty( $include ) ) {
        $args[ 'post__in' ] = $include;
    }

    // Exclude certain achievements
    if ( isset( $exclude ) && ! empty( $exclude ) ) {
        $args[ 'post__not_in' ] = $exclude;
    }

    $gamipress_template_args['query'] = new WP_Query( $args );

    ob_start();
    gamipress_get_template_part( 'old/logs' );
    $output = ob_get_clean();

    return $output;

}

/**
 * Filters the post title.
 *
 * @param string $title The post title.
 * @param int    $id    The post ID.
 *
 * @return string 		The formatted title
 */
function gamipress_log_title_format_old( $title, $id = null ) {

    if( $id === null ) {
        $id = get_the_ID();
    }

    if( get_post_type( $id ) !== 'gamipress-log' ) {
        return $title;
    }

    return gamipress_get_parsed_log( $id );
}
add_filter( 'the_title', 'gamipress_log_title_format_old', 10, 2 );

/**
 * Get the count for the number of times is logged a user has triggered a particular trigger
 *
 * @since  1.0.0
 *
 * @param  integer $user_id The given user's ID
 * @param  string  $trigger The given trigger we're checking
 * @param  integer $since 	The since timestamp where retrieve the logs
 * @param  integer $site_id The desired Site ID to check
 * @param  array $args      The triggered args or requirement object
 *
 * @return integer          The total number of times a user has triggered the trigger
 */
function gamipress_get_user_trigger_count_old( $user_id, $trigger, $since = 0, $site_id = 0, $args = array() ) {

    global $wpdb;

    // Set to current site id
    if ( ! $site_id )
        $site_id = get_current_blog_id();

    $post_date = '';

    if( $since !== 0 ) {
        $now = date( 'Y-m-d' );
        $since = date( 'Y-m-d', $since );

        $post_date = "BETWEEN '$since' AND '$now'";

        if( $since === $now ) {
            $post_date = ">= '$now'";
        }
    }

    // If is specific trigger then try to get the attached id
    if( in_array( $trigger, array_keys( gamipress_get_specific_activity_triggers() ) ) ) {

        $specific_id = 0;

        // if isset this key it means $args is a requirement object
        if( isset( $args['achievement_post'] ) ) {
            $specific_id = absint( $args['achievement_post'] );
        } else if( ! empty( $args ) ) {
            $specific_id = gamipress_specific_trigger_get_id( $trigger, $args );
        }

        // If there is a specific id, then try to find the count
        if( $specific_id !== 0 ) {
            $user_triggers = $wpdb->get_var( $wpdb->prepare(
                "
				SELECT COUNT(*)
				FROM   $wpdb->posts AS p
				LEFT JOIN $wpdb->postmeta AS pm1
				ON ( p.ID = pm1.post_id )
				LEFT JOIN $wpdb->postmeta AS pm2
				ON ( p.ID = pm2.post_id )
				LEFT JOIN $wpdb->postmeta AS pm3
				ON ( p.ID = pm3.post_id )
				WHERE p.post_type = %s
					AND p.post_author = %s
					AND CAST( p.post_date AS DATE ) {$post_date}
					AND (
						( pm1.meta_key = %s AND pm1.meta_value = %s )
						AND ( pm2.meta_key = %s AND pm2.meta_value = %s )
						AND ( pm3.meta_key = %s AND pm3.meta_value = %s )
					)
				",
                'gamipress-log',
                $user_id,
                '_gamipress_type', 'event_trigger',
                '_gamipress_trigger_type', $trigger,
                '_gamipress_achievement_post', $specific_id
            ) );
        } else {
            return 0;
        }
    } else {
        // Single trigger count
        $user_triggers = $wpdb->get_var( $wpdb->prepare(
            "
			SELECT COUNT(*)
			FROM   $wpdb->posts AS p
			LEFT JOIN $wpdb->postmeta AS pm1
			ON ( p.ID = pm1.post_id )
			LEFT JOIN $wpdb->postmeta AS pm2
			ON ( p.ID = pm2.post_id )
			WHERE p.post_type = %s
				AND p.post_author = %s
				AND CAST( p.post_date AS DATE ) {$post_date}
				AND (
					( pm1.meta_key = %s AND pm1.meta_value = %s )
					AND ( pm2.meta_key = %s AND pm2.meta_value = %s )
				)
			",
            'gamipress-log',
            $user_id,
            '_gamipress_type', 'event_trigger',
            '_gamipress_trigger_type', $trigger
        ) );
    }

    // If we have any triggers, return the current count for the given trigger
    return absint( $user_triggers );

}

/**
 * Add log extra data meta box
 *
 * @since  1.0.0
 * @return void
 */
function gamipress_add_log_extra_data_ui_meta_box_old() {
    add_meta_box( 'gamipress_log_extra_data_ui', __( 'Extra Data', 'gamipress' ), 'gamipress_log_extra_data_ui_meta_box_old', 'gamipress-log', 'advanced', 'default' );
}
add_action( 'add_meta_boxes', 'gamipress_add_log_extra_data_ui_meta_box_old' );

/**
 * Renders the HTML for meta box, refreshes whenever a new step is added
 *
 * @since  1.0.0
 * @param  WP_Post $post The current post object
 * @return void
 */
function gamipress_log_extra_data_ui_meta_box_old( $post  = null ) {
    ?>
    <div id="log-extra-data-ui-old">
        <?php gamipress_log_extra_data_ui_html_old( $post->ID ); ?>
    </div>
    <?php
}

/**
 * Renders the HTML for meta box based on the log type given
 *
 * @since  1.0.0
 * @param  WP_Post $post The current post object
 * @param  string $type Type to render form
 * @return void
 */
function gamipress_log_extra_data_ui_html_old( $post_id, $type = null ) {
    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_';
    $fields = array();

    if( $type === null ) {
        $type = get_post_meta( $post_id, $prefix .'type', true );
    }

    if( $type === 'event_trigger' ) {

        $fields = array(
            array(
                'name' 	=> __( 'Trigger', 'gamipress' ),
                'desc' 	=> __( 'The event user has triggered.', 'gamipress' ),
                'id'   	=> $prefix . 'trigger_type',
                'type' 	=> 'advanced_select',
                'options' 	=> gamipress_get_activity_triggers(),
            ),
            array(
                'name' 	=> __( 'Count', 'gamipress' ),
                'desc' 	=> __( 'Number of times user triggered this event until this log.', 'gamipress' ),
                'id'   	=> $prefix . 'count',
                'type' 	=> 'text',
            ),
        );

        $trigger = get_post_meta( $post_id, $prefix . 'trigger_type', true );

        // If is a specific activity trigger, then add the achievement_post field
        if( in_array( $trigger, array_keys( gamipress_get_specific_activity_triggers() ) ) ) {

            $achievement_post_id = get_post_meta( $post_id, $prefix . 'achievement_post', true );

            $fields[] = array(
                'name' 	=> __( 'Assigned Post', 'gamipress' ),
                'desc' 	=> __( 'Attached post to this log.', 'gamipress' ),
                'id'   	=> $prefix . 'achievement_post',
                'type' 	=> 'select',
                'options' 	=> array(
                    $achievement_post_id => get_post_field( 'post_title', $achievement_post_id ),
                ),
            );
        }
    } else if( $type === 'achievement_earn' || $type === 'achievement_award' ) {
        $achievement_id = get_post_meta( $post_id, $prefix . 'achievement_id', true );

        $fields = array(
            array(
                'name' 	=> __( 'Achievement', 'gamipress' ),
                'desc' 	=> __( 'Achievement user has earned.', 'gamipress' ),
                'id'   	=> $prefix . 'achievement_id',
                'type' 	=> 'select',
                'options' 	=> array(
                    $achievement_id => get_post_field( 'post_title', $achievement_id ),
                ),
            ),
        );
    } else if( $type === 'points_award' || $type === 'points_earn' ) {
        // Grab our points types as an array
        $points_types_options = array(
            '' => 'Default'
        );

        foreach( gamipress_get_points_types() as $slug => $data ) {
            $points_types_options[$slug] = $data['plural_name'];
        }

        $fields = array(
            array(
                'name' 	=> __( 'Points', 'gamipress' ),
                'desc' 	=> __( 'Points user has earned.', 'gamipress' ),
                'id'   	=> $prefix . 'points',
                'type' 	=> 'text_small',
            ),
            array(
                'name' 	=> __( 'Points Type', 'gamipress' ),
                'desc' 	=> __( 'Points type user has earned.', 'gamipress' ),
                'id'   	=> $prefix . 'points_type',
                'type' 	=> 'select',
                'options' => $points_types_options
            ),
            array(
                'name' 	=> __( 'Total Points', 'gamipress' ),
                'desc' 	=> __( 'Total points user has earned until this log.', 'gamipress' ),
                'id'   	=> $prefix . 'total_points',
                'type' 	=> 'text_small',
            ),
        );

        if( $type === 'points_award' ) {
            $admin_id = get_post_meta( $post_id, $prefix . 'admin_id', true );
            $admin = get_userdata( $admin_id );

            $fields[] = array(
                'name' 	=> __( 'Administrator', 'gamipress' ),
                'desc' 	=> __( 'User has made the award.', 'gamipress' ),
                'id'   	=> $prefix . 'admin_id',
                'type' 	=> 'select',
                'options' 	=> array(
                    $admin_id => $admin->user_login,
                ),
            );
        }
    }

    $fields = apply_filters( 'gamipress_log_extra_data_fields', $fields, $post_id, $type );

    if( ! empty( $fields ) ) {
        // Create a new box to render the form
        $cmb2 = new CMB2( array(
            'id'      => 'log_extra_data_ui_box',
            'classes' => 'gamipress-form gamipress-box-form',
            'hookup'  => false,
            'show_on' => array(
                'key'   => 'gamipress-log',
                'value' => $post_id
            ),
            'fields' => $fields
        ) );

        $cmb2->object_id( $post_id );

        $cmb2->show_form();
    } else {
        _e( 'No extra data registered', 'gamipress' );
    }
}