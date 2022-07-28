<?php
/**
 * User Earnings Functions
 *
 * @package     GamiPress\User_Earnings_Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.4.3
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Create a user earning
 *
 * @since  1.4.3
 *
 * @param  int      $user_id  	The user ID
 * @param  array    $data       User earning data
 * @param  array    $meta       User earning meta data
 *
 * @return int             	    The user earning ID of the newly created user earning entry
 */
function gamipress_insert_user_earning( $user_id = 0, $data = array(), $meta = array() ) {

    global $wpdb;

    // Setup table
    $ct_table = ct_setup_table( 'gamipress_user_earnings' );

    // Post data
    $data = wp_parse_args( $data, array(
        'title'	        => '',
        'user_id'	    => $user_id === 0 ? get_current_user_id() : absint( $user_id ),
        'post_id'	    => 0,
        'post_type' 	=> '',
        'points'	    => 0,
        'points_type'	=> '',
        'date'	        => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
    ) );

    // If title is empty, try to get the title from post assigned
    if( empty( $data['title'] ) && absint( $data['post_id'] ) !== 0 ) {
        $data['title'] = gamipress_get_post_field( 'post_title', $data['post_id'] );
    }

    // Store user earning entry
    $user_earning_id = $ct_table->db->insert( $data );

    // parent_post_type and parent_id meta to meet on requirements the parent information
    if( $user_earning_id && in_array( $data['post_type'], gamipress_get_requirement_types_slugs() ) ) {
        $parent_id = absint( gamipress_get_post_field( 'post_parent', $data['post_id'] ) );

        $meta['parent_post_type'] = '';

        if( $parent_id !== 0 ) {
            $meta['parent_post_type'] = gamipress_get_post_field( 'post_type', $parent_id );

            if( $meta['parent_post_type'] === 'points-type' ) {
                $meta['parent_post_type'] = gamipress_get_post_field( 'post_name', $parent_id );
            }
        }

        if( empty( $meta['parent_post_type'] ) ) {
            $meta['parent_post_type'] = $data['post_type'];
        }
    }

    // Store user earning meta data
    if ( $user_earning_id && ! empty( $meta ) ) {

        $metas = array();

        foreach ( (array) $meta as $key => $value ) {
            // Sanitize vars
            $meta_key = '_gamipress_' . sanitize_key( $key );
            $meta_key = wp_unslash( $meta_key );
            $meta_value = wp_unslash( $value );
            $meta_value = esc_sql( $meta_value );
            $meta_value = sanitize_meta( $meta_key, $meta_value, $ct_table->name );
            $meta_value = maybe_serialize( $meta_value );

            // Setup the insert value
            $metas[] = $wpdb->prepare( '%d, %s, %s', array( $user_earning_id, $meta_key, $meta_value ) );
        }

        $user_earnings_meta = GamiPress()->db->user_earnings_meta;
        $metas = implode( '), (', $metas );

        // Since the user earning is recently inserted, is faster to run a single query to insert all metas instead of insert them one-by-one
        $wpdb->query( "INSERT INTO {$user_earnings_meta} (user_earning_id, meta_key, meta_value) VALUES ({$metas})" );

    }

    /**
     * Action triggered after insert a user earning
     *
     * @since  1.4.3
     *
     * @param  int      $user_earning_id  	The user earning ID
     * @param  array    $data               User earning data
     * @param  array    $meta               User earning meta data
     * @param  int      $user_id  	        The user ID
     */
    do_action( 'gamipress_insert_user_earning', $user_earning_id, $data, $meta, $user_id );

    ct_reset_setup_table();

    return $user_earning_id;

}

/**
 * Get a specific earning count
 *
 * @since  1.8.6
 *
 * @param  array $query User earning query parameters
 *
 * @return int          The number of user earnings found
 */
function gamipress_get_earnings_count( $query = array() ) {

    global $wpdb;

    $where = gamipress_get_earnings_where( $query );

    // Merge all wheres
    $where = implode( ' AND ', $where );

    $user_earnings = GamiPress()->db->user_earnings;

    return absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$user_earnings} AS ue WHERE {$where}" ) );

}

/**
 * Get the last earning
 *
 * @since  2.1.2
 *
 * @param  array $query     User earning query parameters
 *
 * @return stdClass|null    The last earning object
 */
function gamipress_get_last_earning( $query = array() ) {

    $query['limit'] = 1;

    return gamipress_get_last_earnings( $query );

}

/**
 * Get the last earnings
 *
 * @since  2.2.0
 *
 * @param  array $query     User earning query parameters
 *
 * @return array|stdClass|null       Array of the last earnings registered
 */
function gamipress_get_last_earnings( $query = array() ) {

    global $wpdb;

    $where = gamipress_get_earnings_where( $query );

    // Merge all wheres
    $where = implode( ' AND ', $where );

    $user_earnings = GamiPress()->db->user_earnings;

    $limit = ( isset( $query['limit'] ) ? absint( $query['limit'] ) : 1 );

    return $wpdb->get_results( "SELECT ue.* FROM {$user_earnings} AS ue WHERE {$where} ORDER BY ue.date DESC LIMIT {$limit}" );

}

/**
 * Get the last earning ID
 *
 * @since  2.1.2
 *
 * @param  array $query User earning query parameters
 *
 * @return int          The last earning ID
 */
function gamipress_get_last_earning_id( $query = array() ) {

    global $wpdb;

    $where = gamipress_get_earnings_where( $query );

    // Merge all wheres
    $where = implode( ' AND ', $where );

    $user_earnings = GamiPress()->db->user_earnings;

    return $wpdb->get_var( "SELECT ue.user_earning_id FROM {$user_earnings} AS ue WHERE {$where} ORDER BY ue.date DESC LIMIT 1" );

}

/**
 * Get the last earning post ID
 *
 * @since  2.1.2
 *
 * @param  array $query User earning query parameters
 *
 * @return int          The last earning post ID
 */
function gamipress_get_last_earning_post_id( $query = array() ) {

    global $wpdb;

    $where = gamipress_get_earnings_where( $query );

    // Merge all wheres
    $where = implode( ' AND ', $where );

    $user_earnings = GamiPress()->db->user_earnings;

    return $wpdb->get_var( "SELECT ue.post_id FROM {$user_earnings} AS ue WHERE {$where} ORDER BY ue.date DESC LIMIT 1" );

}

/**
 * Get the last earning date
 *
 * @since  1.8.7
 *
 * @param  array $query User earning query parameters
 *
 * @return string       The last earning date
 */
function gamipress_get_last_earning_date( $query = array() ) {

    global $wpdb;

    $where = gamipress_get_earnings_where( $query );

    // Merge all wheres
    $where = implode( ' AND ', $where );

    $user_earnings = GamiPress()->db->user_earnings;

    return $wpdb->get_var( "SELECT ue.date FROM {$user_earnings} AS ue WHERE {$where} ORDER BY ue.date DESC LIMIT 1" );

}

/**
 * Get the last earning datetime
 *
 * @since  1.8.7
 *
 * @param  array $query User earning query parameters
 *
 * @return int          The last earning datetime
 */
function gamipress_get_last_earning_datetime( $query = array() ) {

    $date = gamipress_get_last_earning_date( $query );

    return ! empty( $date ) ? strtotime( $date ) : 0;

}

/**
 * Setup a common where conditions for the user earnings queries
 *
 * @since  1.8.7
 *
 * @param  array $query User earning query parameters
 *
 * @return array        Array of where clauses
 */
function gamipress_get_earnings_where( $query = array() ) {

    // Post data
    $query = wp_parse_args( $query, array(
        'user_id'	        => 0,
        'post_id'	        => 0,
        'achievement_id'	=> 0,
        'post_type' 	    => '',
        'points_type'	    => '',
        'since'	            => 0,
    ) );

    // Parse mapped keys
    if( $query['achievement_id'] !== 0 && $query['post_id'] === 0 ) {
        $query['post_id'] = $query['achievement_id'];
    }

    $where = array(
        '1 = 1'
    );

    // User ID
    if( is_array( $query['user_id'] ) ) {
        $where[] = 'ue.user_id IN ( ' . implode( ', ', $query['user_id'] ) . ' )';
    } else if ( absint( $query['user_id'] ) !== 0 ) {
        $where[] = 'ue.user_id = ' . absint( $query['user_id'] );
    }

    // Post ID
    if( is_array( $query['post_id'] ) ) {
        $where[] = 'ue.post_id IN ( ' . implode( ', ', $query['post_id'] ) . ' )';
    } else if ( absint( $query['post_id'] ) !== 0 ) {
        $where[] = 'ue.post_id = ' . absint( $query['post_id'] );
    }

    // Post type
    if( is_array( $query['post_type'] ) ) {
        $where[] = 'ue.post_type IN ( "' . implode( '", "', $query['post_type'] ) . '" )';
    } else if ( ! empty( $query['post_type'] ) ) {
        $where[] = 'ue.post_type = "' . $query['post_type'] . '"';
    }

    // Points type
    if( is_array( $query['points_type'] ) ) {
        $where[] = 'ue.points_type IN ( "' . implode( '", "', $query['points_type'] ) . '" )';
    } else if ( ! empty( $query['points_type'] ) ) {
        $where[] = 'ue.points_type = "' . $query['points_type'] . '"';
    }

    // Since
    if( ! empty( $query['since'] ) ) {

        $since = $query['since'];

        // Turn a string date into time
        if( gettype( $query['since'] ) === 'string' ) {
            $since = strtotime( $query['since'] );
        }

        if( $since > 0 ) {
            $since = date( 'Y-m-d H:i:s', $since );
            $where[] = " ue.date > '{$since}'";
        }
    }

    // Before
    if( isset( $query['before'] ) ) {

        $before = $query['before'];

        // Turn a string date into time
        if( gettype( $query['before'] ) === 'string' ) {
            $before = strtotime( $query['before'] );
        }

        if( $before > 0 ) {
            $before = date( 'Y-m-d H:i:s', $before );
            $where[] = " ue.date < '{$before}'";
        }

    }

    // After
    if( isset( $query['after'] ) ) {

        $after = $query['after'];

        // Turn a string date into time
        if( gettype( $query['after'] ) === 'string' ) {
            $after = strtotime( $query['after'] );
        }

        if( $after > 0 ) {
            $after = date( 'Y-m-d H:i:s', $after );
            $where[] = " ue.date > '{$after}'";
        }

    }

    return $where;

}

/**
 * Get the user earning object data
 *
 * @since 2.1.2
 *
 * @param int       $user_earning_id    The user earning ID
 * @param string    $meta_key           The meta key to retrieve. By default, returns
 *                                      data for all keys. Default empty.
 * @param bool      $single             Optional. Whether to return a single value. Default false.
 *
 * @return mixed                        Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function gamipress_get_user_earning_meta( $user_earning_id, $meta_key = '', $single = false ) {

    ct_setup_table( 'gamipress_user_earnings' );

    $meta_value = ct_get_object_meta( $user_earning_id, $meta_key, $single );

    ct_reset_setup_table();

    return $meta_value;

}

/**
 * Update the user earning object data
 *
 * @since 2.1.2
 *
 * @param int       $user_earning_id    The user earning ID
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed  $prev_value Optional. Previous value to check before removing.
 *                           Default empty.
 *
 * @return int|bool         Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function gamipress_update_user_earning_meta( $user_earning_id, $meta_key, $meta_value, $prev_value = '' ) {

    ct_setup_table( 'gamipress_user_earnings' );

    $meta_id = ct_update_object_meta( $user_earning_id, $meta_key, $meta_value, $prev_value );

    ct_reset_setup_table();

    return $meta_id;

}