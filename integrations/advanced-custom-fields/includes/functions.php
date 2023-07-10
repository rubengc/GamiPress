<?php
/**
 * Functions
 *
 * @package GamiPress\Advanced_Custom_Fields\Functions
 * @since 1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Overrides GamiPress AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function gamipress_acf_ajax_get_posts() {

    global $wpdb;

    $results = array();

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? sanitize_text_field( $_REQUEST['q'] ) : '';
    $search = $wpdb->esc_like( $search );

    if( isset( $_REQUEST['post_type'] ) && in_array( 'acf_user_fields', $_REQUEST['post_type'] ) ) {

        $all_user_groups = gamipress_acf_get_user_field_groups();
        
        foreach( $all_user_groups as $group ) {
        
            // Get fields from group
            $all_acf_fields = acf_get_fields( $group['ID'] );
    
            foreach ( $all_acf_fields as $acf_fields ){

                $results[] = array(
                    'ID' => $acf_fields['ID'],
                    'post_title' => $acf_fields['label'],
                );
    
            }
    
        }

        // Return our results
        wp_send_json_success( $results );
        die;

    } else if( isset( $_REQUEST['post_type'] ) && in_array( 'acf_post_fields', $_REQUEST['post_type'] ) ) {

        $args = array(
            'post_type' => 'post',
        );
        
        // Get groups related to users
        $all_user_groups = acf_get_field_groups( $args );
        
        foreach( $all_user_groups as $group ) {
        
            // Get fields from group
            $all_acf_fields = acf_get_fields( $group['ID'] );
    
            foreach ( $all_acf_fields as $acf_fields ){

                $results[] = array(
                    'ID' => $acf_fields['ID'],
                    'post_title' => $acf_fields['label'],
                );
    
            }
    
        }

        // Return our results
        wp_send_json_success( $results );
        die;
    }

}
add_action( 'wp_ajax_gamipress_get_posts', 'gamipress_acf_ajax_get_posts', 5 );


// Get the acf field title
function gamipress_acf_get_post_field_title( $acf_field_id ) {

    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare(
        "SELECT a.post_title FROM {$wpdb->prefix}posts AS a WHERE a.ID = %d",
        absint( $acf_field_id )
    ) );

}

// To check if the changed meta is an acf field
function gamipress_acf_check_acf_fields() {

    $args = array(
        'post_type' => 'post',
    );
    
    // Get groups related to posts
    $all_post_groups = acf_get_field_groups( $args );

    if ( empty ( $all_post_groups ) ) {
        return false;
    }
    
    foreach( $all_post_groups as $group ) {
    
        // Get fields from group
        $all_acf_fields = acf_get_fields( $group['ID'] );

        foreach ( $all_acf_fields as $acf_fields ){

            $results[$acf_fields['name']] =  $acf_fields['label'];

        }

    }

    return $results;
}

// To check if the changed meta is a user acf field
function gamipress_acf_check_acf_user_fields() {
    
    $all_user_groups = gamipress_acf_get_user_field_groups();

    if ( empty ( $all_user_groups ) ) {
        return false;
    }
    
    foreach( $all_user_groups as $group ) {
    
        // Get fields from group
        $all_acf_fields = acf_get_fields( $group['ID'] );

        foreach ( $all_acf_fields as $acf_fields ){

            $results[$acf_fields['name']] =  $acf_fields['label'];

        }

    }

    return $results;
}

/**
* Get all user field groups 
*
* @return array 
*/
function gamipress_acf_get_user_field_groups() {

	if ( ! function_exists( 'acf_get_field_groups' ) ) {
		return array();
	}

	$groups_user = array();

    // User location types
    $groups_user_types = array(
        'user_form',
        'current_user_role',
        'user_role',
        'current_user',
    );

	$groups = acf_get_field_groups();

	foreach ( $groups as $group ) {

		if ( ! empty( $group['location'] ) ) {

			foreach ( $group['location'] as $locations ) {

				foreach ( $locations as $location ) {

                    foreach ( $groups_user_types as $type ){

                        if ( $type === $location['param'] ) {

                            $groups_user[] = $group;
    
                        }

                    }

				}

			}

		}
        
	}

	return $groups_user;

}

/**
 * Get conditions
 *
 * @since 1.0.0
 *
 * @return array
 */
function gamipress_acf_get_value_conditions() {

    return array(
        // String
        'equal'             => __( 'is equal to', 'gamipress' ),
        'not_equal'         => __( 'is not equal to', 'gamipress' ),
        'contains'          => __( 'contains', 'gamipress' ),
        'not_contains'      => __( 'does not contains', 'gamipress' ),
        'start_with'        => __( 'starts with', 'gamipress' ),
        'not_start_with'    => __( 'does not starts with', 'gamipress' ),
        'ends_with'         => __( 'ends with', 'gamipress' ),
        'not_ends_with'     => __( 'does not ends with', 'gamipress' ),
        // Number
        'less_than'         => __( 'is less than', 'gamipress' ),
        'greater_than'      => __( 'is greater than', 'gamipress' ),
        'less_or_equal'     => __( 'is less or equal to', 'gamipress' ),
        'greater_or_equal'  => __( 'is greater or equal to', 'gamipress' ),
    );

}

/**
 * Utility function to get the condition option parameter
 *
 * @since 1.4.5
 *
 * @param mixed     $a          Element to match
 * @param mixed     $b          Element to compare
 * @param string    $condition  The condition to compare elements
 *
 * @return bool
 */
function gamipress_acf_condition_matches( $a, $b, $condition ) {

    if( empty( $condition ) ) {
        $condition = 'equal';
    }

    $matches = false;

    // Ensure that the element to compare is a string
    if( is_array( $b ) ) {
        $b = implode( ',', $b );
    }

    $a = strval( $a );
    $b = strval( $b );

    // If not is a string condition and elements to compare are numerics, turn them to float
    if( ! gamipress_acf_is_string_condition( $condition ) ) {
        if( is_numeric( $a ) ) {
            $a = (float) $a;
        }

        if( is_numeric( $b ) ) {
            $b = (float) $b;
        }
    }

    switch( $condition ) {
        case 'equal':
        case '=':
        case '==':
        case '===':
            $matches = ( $a == $b );
            break;
        case 'not_equal':
        case '!=':
        case '!==':
            $matches = ( $a != $b );
            break;
        case 'less_than':
        case '<':
            $matches = ( $a < $b );
            break;
        case 'greater_than':
        case '>':
            $matches = ( $a > $b );
            break;
        case 'less_or_equal':
        case '<=':
            $matches = ( $a <= $b );
            break;
        case 'greater_or_equal':
        case '>=':
            $matches = ( $a >= $b );
            break;
        case 'contains':
            $matches = ( strpos( $a, strval( $b ) ) !== false );
            break;
        case 'not_contains':
            $matches = ( strpos( $a, strval( $b ) ) === false );
            break;
        case 'start_with':
            $matches = ( gamipress_starts_with( $a, $b ) );
            break;
        case 'not_start_with':
            $matches = ( ! gamipress_starts_with( $a, $b ) );
            break;
        case 'ends_with':
            $matches = ( gamipress_ends_with( $a, $b ) );
            break;
        case 'not_ends_with':
            $matches = ( ! gamipress_ends_with( $a, $b ) );
            break;
    }

    return $matches;

}

/**
 * Utility function to meet if condition is related to string
 *
 * @since 1.7.6
 *
 * @param string    $condition  The condition to check
 *
 * @return bool
 */
function gamipress_acf_is_string_condition( $condition ) {

    $return = false;

    switch( $condition ) {
        case 'contains':
        case 'not_contains':
        case 'start_with':
        case 'not_start_with':
        case 'ends_with':
        case 'not_ends_with':
            $return = true;
            break;
    }

    return $return;

}