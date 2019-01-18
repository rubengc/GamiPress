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
 * Create an user earning
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

    // Setup table
    ct_setup_table( 'gamipress_user_earnings' );

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
    $user_earning_id = ct_insert_object( $data );

    // Store user earning meta data
    if ( $user_earning_id && ! empty( $meta ) ) {

        foreach ( (array) $meta as $key => $value ) {

            ct_update_object_meta( $user_earning_id, '_gamipress_' . sanitize_key( $key ), $value );

        }

    }

    // Hook to add custom data
    do_action( 'gamipress_insert_user_earning', $user_earning_id, $data, $meta, $user_id );

    ct_reset_setup_table();

    return $user_earning_id;

}